<?php

declare(strict_types=1);

namespace chaser\stream\traits;

use chaser\stream\interfaces\SubscriberInterface;
use chaser\stream\event\{SendBufferDrain, SendBufferFull, SendFail, SendInvalid};

/**
 * 通信连接部分订阅特征
 *
 * @package chaser\stream\traits
 *
 * @see SubscriberInterface
 */
trait CommunicationConnectedSubscribable
{
    /**
     * @inheritDoc
     */
    public static function events(): array
    {
        return [
            SendBufferDrain::class => 'sendBufferDrain',
            SendBufferFull::class => 'sendBufferFull',
            SendFail::class => 'sendFail',
            SendInvalid::class => 'sendInvalid'
        ];
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
