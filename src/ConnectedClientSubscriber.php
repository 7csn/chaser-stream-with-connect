<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\events\ConnectFail;

/**
 * 有连接的客户端事件订阅者
 *
 * @package chaser\stream
 *
 * @property ConnectedClient $client
 */
class ConnectedClientSubscriber extends ClientSubscriber
{
    /**
     * @inheritDoc
     */
    public function __construct(ConnectedClient $client)
    {
        parent::__construct($client);
        $this->setEvent(ConnectFail::class, 'connectFail');
    }

    /**
     * 连接失败事件响应
     *
     * @param ConnectFail $event
     */
    public function connectFail(ConnectFail $event):void{}
}
