<?php

declare(strict_types=1);

namespace chaser\stream\events;

use Throwable;

/**
 * 连接信息解包失败
 *
 * @package chaser\stream\events
 */
class ConnectionUnpackingFail
{
    /**
     * 解包异常
     *
     * @var Throwable
     */
    public Throwable $exception;

    /**
     * 初始化数据
     *
     * @param Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }
}
