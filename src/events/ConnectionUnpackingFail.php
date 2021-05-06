<?php

declare(strict_types=1);

namespace chaser\stream\events;

use chaser\stream\traits\PropertyReadable;
use Throwable;

/**
 * 连接信息解包失败
 *
 * @package chaser\stream\events
 */
class ConnectionUnpackingFail
{
    use PropertyReadable;

    /**
     * 解包异常
     *
     * @property-read Throwable
     */
    protected Throwable $exception;

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
