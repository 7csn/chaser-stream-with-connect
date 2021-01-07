<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\events\ConnectFail;
use chaser\stream\interfaces\ConnectedClientInterface;
use chaser\stream\traits\ConnectedCommunicationSubscribable;

/**
 * 有连接的客户端事件订阅者
 *
 * @package chaser\stream
 *
 * @property ConnectedClientInterface $client
 */
class ConnectedClientSubscriber extends ClientSubscriber
{
    use ConnectedCommunicationSubscribable;

    /**
     * @inheritDoc
     */
    public function __construct(ConnectedClientInterface $client)
    {
        parent::__construct($client);
        $this->setEvent(ConnectFail::class, 'connectFail');
        $this->addConnectedEvents();
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
