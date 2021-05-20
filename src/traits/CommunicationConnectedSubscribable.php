<?php

declare(strict_types=1);

namespace chaser\stream\traits;

use chaser\stream\events\{
    RecvBufferFull,
    UnpackingFail,
    SendBufferDrain,
    SendBufferFull,
    SendFail,
    SendInvalid
};

/**
 * 通信连接部分订阅特征
 *
 * @package chaser\stream\traits
 */
trait CommunicationConnectedSubscribable
{
    /**
     * 返回事件响应对照表补充
     *
     * @return string[]
     */
    protected static function moreEvents(): array
    {
        return [
            RecvBufferFull::class => 'recvBufferFull',
            UnpackingFail::class => 'unpackingFail',
            SendBufferDrain::class => 'sendBufferDrain',
            SendBufferFull::class => 'sendBufferFull',
            SendFail::class => 'sendFail',
            SendInvalid::class => 'sendInvalid'
        ];
    }

    /**
     * 接收缓冲区满事件响应
     *
     * @param RecvBufferFull $event
     */
    public function recvBufferFull(RecvBufferFull $event): void
    {
    }

    /**
     * 解包失败事件响应
     *
     * @param UnpackingFail $event
     */
    public function unpackingFail(UnpackingFail $event): void
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
