<?php

declare(strict_types=1);

namespace chaser\stream\subscriber;

use chaser\stream\event\Establish;
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
        return [Establish::class => 'establish'] + CommunicationConnectedSubscribable::events() + CommunicationSubscribable::events();
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
     * @param Establish $event
     */
    public function establish(Establish $event): void
    {
    }
}
