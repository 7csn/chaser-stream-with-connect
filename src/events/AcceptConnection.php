<?php

declare(strict_types=1);

namespace chaser\stream\events;

use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\traits\PropertyReadable;

/**
 * 流服务器接收连接事件类
 *
 * @package chaser\stream\events
 *
 * @property-read ConnectionInterface $connection
 */
class AcceptConnection
{
    use PropertyReadable;

    /**
     * 初始化数据
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(private ConnectionInterface $connection)
    {
    }
}
