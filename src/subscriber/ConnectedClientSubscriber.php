<?php

declare(strict_types=1);

namespace chaser\stream\subscriber;

use chaser\stream\ConnectClient;
use chaser\stream\event\ConnectFail;
use chaser\stream\traits\CommunicationConnectedSubscribable;

/**
 * 有通信连接的客户端事件订阅类
 *
 * @package chaser\stream\subscriber
 *
 * @property ConnectClient $client
 */
class ConnectedClientSubscriber extends ClientSubscriber
{
    use CommunicationConnectedSubscribable;

    /**
     * @inheritDoc
     */
    public static function events(): array
    {
        return [ConnectFail::class => 'connectFail'] + CommunicationConnectedSubscribable::events() + parent::events();
    }

    /**
     * 连接失败事件响应
     *
     * @param ConnectFail $event
     */
    public function connectFail(ConnectFail $event): void
    {
    }
}
