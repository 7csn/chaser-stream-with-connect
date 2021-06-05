<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\event\AcceptConnection;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\interfaces\part\ServerConnectInterface;
use chaser\stream\subscriber\ConnectedServerSubscriber;

/**
 * 有连接的流服务器类
 *
 * @package chaser\stream
 *
 * @property-read ConnectionInterface[] $connection
 */
abstract class ConnectedServer extends Server implements ServerConnectInterface
{
    /**
     * 通信连接库
     *
     * @var ConnectionInterface[]
     */
    protected array $connections = [];

    /**
     * @inheritDoc
     */
    public static function subscriber(): string
    {
        return ConnectedServerSubscriber::class;
    }

    /**
     * @inheritDoc
     */
    public static function configurations(): array
    {
        return ['connection' => []] + parent::configurations();
    }

    /**
     * @inheritDoc
     */
    public function removeConnection(int $hash): void
    {
        unset($this->connections[$hash]);
    }

    /**
     * @inheritDoc
     */
    public function accept(): void
    {
        if ($this->socket === null) {
            return;
        }

        $socket = stream_socket_accept($this->socket, 0);

        if ($socket) {
            // 获取连接并保存
            $connection = $this->connection($socket);
            $this->connections[$connection->hash()] = $connection;

            // 连接配置、确立（附加协议）
            $connection->configure($this->connection);
            $connection->establish();

            // 接收连接事件
            $this->dispatch(AcceptConnection::class, $connection);
        }
    }

    /**
     * @inheritDoc
     */
    protected function close(): void
    {
        $this->closeConnections();
        parent::close();
    }

    /**
     * 获取连接对象
     *
     * @param resource $socket
     * @return ConnectionInterface
     */
    protected function connection($socket): ConnectionInterface
    {
        return new Connection($this->container, $this, $this->reactor, $socket);
    }

    /**
     * 关闭连接的套接字流资源
     */
    protected function closeConnections(): void
    {
        foreach ($this->connections as $connection) {
            $connection->close();
        }
    }
}
