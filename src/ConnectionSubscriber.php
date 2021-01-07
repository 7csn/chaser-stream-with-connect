<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\event\SubscriberInterface;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\traits\CommunicationSubscribable;
use chaser\stream\traits\ConnectedCommunicationSubscribable;

/**
 * 连接事件订阅者
 *
 * @package chaser\stream
 */
class ConnectionSubscriber implements SubscriberInterface
{
    use CommunicationSubscribable, ConnectedCommunicationSubscribable;

    /**
     * 客户端
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $connection;

    /**
     * 初始化连接
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->addConnectedEvents();
    }
}
