<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\events\AcceptConnection;

/**
 * 有连接的服务器事件订阅者
 *
 * @package chaser\stream
 *
 * @property ConnectedServer $server
 */
class ConnectedServerSubscriber extends ServerSubscriber
{
    /**
     * @inheritDoc
     */
    public function __construct(ConnectedServer $server)
    {
        parent::__construct($server);
        $this->setEvent(AcceptConnection::class, 'accept');
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
