<?php

namespace chaser\stream\subscribers;

use chaser\stream\events\ConnectFail;
use chaser\stream\interfaces\ConnectedClientInterface;
use chaser\stream\traits\CommunicationConnectedSubscribable;

/**
 * 有通信连接的客户端事件订阅类
 *
 * @package chaser\stream\subscribers
 *
 * @property  ConnectedClientInterface $client
 */
class ConnectedClientSubscriber extends ClientSubscriber
{
    use CommunicationConnectedSubscribable;

    /**
     * @inheritDoc
     */
    public static function events(): array
    {
        return self::moreEvents() + parent::events();
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
