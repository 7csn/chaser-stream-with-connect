<?php

declare(strict_types=1);

namespace chaser\stream\subscriber;

use chaser\stream\ConnectedServer;
use chaser\stream\event\AcceptConnection;

/**
 * 有连接的流服务器事件订阅类
 *
 * @package chaser\stream\subscriber
 *
 * @property ConnectedServer $server
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
