<?php

namespace chaser\stream\subscribers;

use chaser\container\ContainerInterface;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\traits\CommunicationSubscribable;
use chaser\stream\traits\CommunicationConnectedSubscribable;

/**
 * 通信连接事件订阅类
 *
 * @package chaser\stream\subscribers
 */
class ConnectionSubscriber extends Subscriber
{
    use CommunicationSubscribable {
        events as CommunicationEvents;
    }

    use CommunicationConnectedSubscribable;

    /**
     * @inheritDoc
     */
    public static function events(): array
    {
        return self::moreEvents() + self::CommunicationEvents();
    }

    /**
     * 构造方法
     *
     * @param ContainerInterface $container
     * @param ConnectionInterface $connection
     */
    public function __construct(protected ContainerInterface $container, protected ConnectionInterface $connection)
    {
    }
}
