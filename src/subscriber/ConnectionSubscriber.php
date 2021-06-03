<?php

declare(strict_types=1);

namespace chaser\stream\subscriber;

use chaser\stream\event\Established;
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
        return [Established::class => 'established'] + CommunicationConnectedSubscribable::events() + CommunicationSubscribable::events();
    }

    /**
     * 构造方法
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(protected ConnectionInterface $connection)
    {
    }

    /**
     * 连接稳定事件响应
     *
     * @param Established $event
     */
    public function established(Established $event): void
    {
    }
}
