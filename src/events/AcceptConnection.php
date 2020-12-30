<?php

declare(strict_types=1);

namespace chaser\stream\events;

use chaser\stream\Connection;
use chaser\stream\traits\PropertyReadable;

/**
 * 服务器接收连接事件
 *
 * @package chaser\stream\events
 */
class AcceptConnection
{
    use PropertyReadable;

    /**
     * 连接
     *
     * @property-read Connection
     */
    protected Connection $connection;

    /**
     * 初始化数据
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
