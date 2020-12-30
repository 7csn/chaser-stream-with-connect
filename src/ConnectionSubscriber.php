<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\event\SubscriberInterface;
use chaser\stream\traits\Subscribable;
use chaser\stream\events\{
    Close,
    Connect,
    ConnectionRecvBufferFull,
    ConnectionUnpackingFail,
    SendBufferDrain,
    SendBufferFull,
    SendFail,
    SendInvalid,
    Message
};

/**
 * 连接事件订阅者
 *
 * @package chaser\stream
 */
class ConnectionSubscriber implements SubscriberInterface
{
    use Subscribable;

    /**
     * 客户端
     *
     * @var Connection
     */
    protected Connection $connection;

    /**
     * 订阅事件库
     *
     * @var string[]
     */
    protected array $events = [
        Connect::class => 'connect',
        Message::class => 'message',
        Close::class => 'close',
        SendInvalid::class => 'sendInvalid',
        SendFail::class => 'sendFail',
        SendBufferFull::class => 'sendBufferFull',
        SendBufferDrain::class => 'sendBufferDrain',
        ConnectionUnpackingFail::class => 'unpackingFail',
        ConnectionRecvBufferFull::class => 'recvBufferFull'
    ];

    /**
     * 初始化连接
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * 连接事件响应
     *
     * @param Connect $event
     */
    public function connect(Connect $event): void
    {
    }

    /**
     * 接收事件响应
     *
     * @param Message $event
     */
    public function message(Message $event): void
    {
    }

    /**
     * 关闭事件响应
     *
     * @param Close $event
     */
    public function close(Close $event): void
    {
    }

    /**
     * 发送失败（客户端关闭）事件响应
     *
     * @param SendInvalid $event
     */
    public function sendInvalid(SendInvalid $event): void
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
     * 发送缓冲区满事件响应
     *
     * @param SendBufferFull $event
     */
    public function sendBufferFull(SendBufferFull $event): void
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
     * 解包失败事件响应
     *
     * @param ConnectionUnpackingFail $event
     */
    public function unpackingFail(ConnectionUnpackingFail $event): void
    {
    }

    /**
     * 接收缓冲区满事件响应
     *
     * @param ConnectionRecvBufferFull $event
     */
    public function recvBufferFull(ConnectionRecvBufferFull $event): void
    {
    }
}
