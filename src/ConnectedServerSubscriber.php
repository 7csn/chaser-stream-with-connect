<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\events\AcceptConnection;
use chaser\stream\interfaces\ConnectedServerInterface;

/**
 * 有连接的服务器事件订阅者
 *
 * @package chaser\stream
 *
 * @property ConnectedServerInterface $server
 */
class ConnectedServerSubscriber extends ServerSubscriber
{
    /**
     * @inheritDoc
     */
    public function __construct(ConnectedServerInterface $server)
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
