<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\events\AcceptConnection;
use chaser\stream\traits\ServerConnected;
use chaser\stream\interfaces\{ConnectedServerInterface, ConnectionInterface};

/**
 * 有连接的流服务器
 *
 * @package chaser\stream
 *
 * @property array $connection
 */
abstract class ConnectedServer extends Server implements ConnectedServerInterface
{
    use ServerConnected;

    /**
     * @inheritDoc
     */
    protected array $configurations = [
        'connection' => [],
    ];

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
    public function accept()
    {
        $stream = stream_socket_accept($this->stream, 0);

        if ($stream) {
            // 获取连接
            $connection = $this->connection($stream);

            // 连接配置、订阅者、连接
            $connection->set($this->connection);
            if ($connection->subscriber) {
                $connection->addSubscriber($connection->subscriber);
            }
            $connection->connect();

            // 保存连接
            $this->connections[$connection->hash()] = $connection;

            // 接收连接事件
            $this->dispatch(AcceptConnection::class, $connection);
        }
    }

    /**
     * 获取连接对象
     *
     * @param resource $stream
     * @return ConnectionInterface
     */
    public function connection($stream): ConnectionInterface
    {
        return new Connection($this, $this->reactor, $stream);
    }

    /**
     * @inheritDoc
     */
    protected function close()
    {
        if ($this->stream) {
            $this->closeSocket();
            $this->closeConnections();
        }
    }
}
