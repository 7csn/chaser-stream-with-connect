<?php

declare(strict_types=1);

namespace chaser\stream\interfaces\part;

/**
 * 通信连接部分接口
 *
 * @package chaser\stream\interfaces\part
 */
interface CommunicationConnectInterface
{
    /**
     * 状态：初始
     *
     * @var int
     */
    public const STATUS_INITIAL = 1;

    /**
     * 状态：连接中
     *
     * @var int
     */
    public const STATUS_CONNECTING = 2;

    /**
     * 状态：已连接
     *
     * @var int
     */
    public const STATUS_ESTABLISHED = 3;

    /**
     * 状态：关闭中
     *
     * @var int
     */
    public const STATUS_CLOSING = 4;

    /**
     * 状态：已关闭
     *
     * @var int
     */
    public const STATUS_CLOSED = 5;

    /**
     * 读缓冲区限制 64K-1
     *
     * @var int
     */
    public const READ_BUFFER_SIZE = (64 << 10) - 1;

    /**
     * 发送缓冲区默认上限 10M
     */
    public const MAX_SEND_BUFFER_SIZE = 10 << 10 << 10;
}
