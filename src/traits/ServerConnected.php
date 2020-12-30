<?php

declare(strict_types=1);

namespace chaser\stream\traits;

use chaser\stream\interfaces\ConnectionInterface;

/**
 * 服务器连接相关
 *
 * @package chaser\stream\traits
 */
trait ServerConnected
{
    /**
     * 有连接的通信对象库
     *
     * @var ConnectionInterface[]
     */
    protected array $connections = [];

    /**
     * @inheritDoc
     */
    public function removeConnection(string $hash)
    {
        unset($this->connections[$hash]);
    }

    /**
     * 关闭连接的套接字流资源
     */
    protected function closeConnections()
    {
        foreach ($this->connections as $connection) {
            $connection->close();
        }
    }
}
