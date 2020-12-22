<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\interfaces\ConnectedServerInterface;
use chaser\stream\interfaces\ConnectionInterface;

/**
 * 有连接的流服务器
 *
 * @package chaser\stream
 *
 * @property array $connection
 */
abstract class ConnectedServer extends Server implements ConnectedServerInterface
{
    /**
     * @inheritDoc
     */
    protected array $configurations = [
        'connection' => []
    ];

    /**
     * 有连接的通信对象库
     *
     * @var ConnectionInterface[]
     */
    protected array $connections = [];

    /**
     * @inheritDoc
     */
    public function accept()
    {
        $stream = stream_socket_accept($this->stream, 0, $remoteAddress);

        if ($stream) {

            // 非阻塞模式、兼容 hhvm 无缓冲
            stream_set_blocking($stream, false);
            stream_set_read_buffer($stream, 0);

            // 获取通信对象、配置并保存
            $connection = $this->connection($stream, $remoteAddress);
            $connection->set($this->connection);
            $this->saveConnection($connection);

            // 返回通信对象
            return $connection;
        }

        return false;
    }

    /**
     * 获取通信对象
     *
     * @param resource $stream
     * @param string $remoteAddress
     * @return ConnectionInterface|null
     */
    protected function connection($stream, string $remoteAddress): ?ConnectionInterface
    {
        return new Connection($this, $stream, $remoteAddress);
    }

    /**
     * 保存通信对象
     *
     * @param ConnectionInterface $connection
     */
    protected function saveConnection(ConnectionInterface $connection)
    {
        $this->connections[$connection->hash()] = $connection;
    }

    /**
     * @inheritDoc
     */
    public function removeConnection(string $hash)
    {
        unset($this->connections[$hash]);
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        foreach ($this->connections as $connection) {
            $connection->close();
        }
        return parent::close();
    }
}
