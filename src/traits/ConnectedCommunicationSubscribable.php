<?php

declare(strict_types=1);

namespace chaser\stream\traits;

use chaser\stream\events\{
    ConnectionRecvBufferFull,
    ConnectionUnpackingFail,
    SendBufferDrain,
    SendBufferFull,
    SendFail,
    SendInvalid
};

/**
 * 连接通信订阅者相关
 *
 * @package chaser\stream\traits
 */
trait ConnectedCommunicationSubscribable
{
    use Subscribable;

    /**
     * 添加连接通信相关事件订阅
     */
    protected function addConnectedEvents()
    {
        $this->setEvents([
            ConnectionRecvBufferFull::class => 'recvBufferFull',
            ConnectionUnpackingFail::class => 'unpackingFail',
            SendBufferDrain::class => 'sendBufferDrain',
            SendBufferFull::class => 'sendBufferFull',
            SendFail::class => 'sendFail',
            SendInvalid::class => 'sendInvalid'
        ]);
    }

    /**
     * 接收缓冲区满事件响应
     *
     * @param ConnectionRecvBufferFull $event
     */
    public function recvBufferFull(ConnectionRecvBufferFull $event): void
    {
    }

    /**
     * 解包失败事件响应
     *
     * @param ConnectionUnpackingFail $event
     */
    public function unpackingFail(ConnectionUnpackingFail $event): void
    {
    }

    /**
     * 发送缓冲区空事件响应
     *
     * @param SendBufferDrain $event
     */
    public function sendBufferDrain(SendBufferDrain $event): void
    {
    }

    /**
     * 发送缓冲区满事件响应
     *
     * @param SendBufferFull $event
     */
    public function sendBufferFull(SendBufferFull $event): void
    {
    }

    /**
     * 发送失败事件响应
     *
     * @param SendFail $event
     */
    public function sendFail(SendFail $event): void
    {
    }

    /**
     * 发送失败（流无效）事件响应
     *
     * @param SendInvalid $event
     */
    public function sendInvalid(SendInvalid $event): void
    {
    }
}
