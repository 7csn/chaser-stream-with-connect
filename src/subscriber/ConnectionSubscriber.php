<?php

declare(strict_types=1);

namespace chaser\stream\subscriber;

use chaser\container\ContainerInterface;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\traits\{CommunicationConnectedSubscribable, CommunicationSubscribable};

/**
 * 通信连接事件订阅类
 *
 * @package chaser\stream\subscriber
 */
class ConnectionSubscriber extends Subscriber
{
    use CommunicationConnectedSubscribable, CommunicationSubscribable;

    /**
     * @inheritDoc
     */
    public static function events(): array
    {
        return CommunicationConnectedSubscribable::events() + CommunicationSubscribable::events();
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
