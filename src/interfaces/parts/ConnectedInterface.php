<?php

declare(strict_types=1);

namespace chaser\stream\interfaces\parts;

/**
 * 连接部分
 *
 * @package chaser\stream\interfaces\parts
 */
interface ConnectedInterface
{
    /**
     * 读缓冲区限制 64K-1
     *
     * @var int
     */
    public const READ_BUFFER_SIZE = (64 << 10) - 1;

    /**
     * 建立连接
     * 
     * @return bool
     */
    public function connect(): bool;
}
