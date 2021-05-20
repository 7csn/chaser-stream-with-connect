<?php

namespace chaser\stream\subscribers;

use chaser\stream\events\AcceptConnection;
use chaser\stream\interfaces\ConnectedServerInterface;

/**
 * 有连接的流服务器事件订阅类
 *
 * @package chaser\stream\subscribers
 *
 * @property ConnectedServerInterface $server
 */
class ConnectedServerSubscriber extends ServerSubscriber
{
    /**
     * @inheritDoc
     */
    public static function events(): array
    {
        return [AcceptConnection::class => 'accept'] + parent::events();
    }

    /**
     * 接收连接事件响应
     *
     * @param AcceptConnection $event
     */
    public function accept(AcceptConnection $event): void
    {
    }
}
