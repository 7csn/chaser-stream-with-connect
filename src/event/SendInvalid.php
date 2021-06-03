<?php

declare(strict_types=1);

namespace chaser\stream\event;

use chaser\stream\traits\PropertyReadable;

/**
 * 信息发送无效事件类
 *
 * @package chaser\stream\event
 *
 * @property-read string $data
 */
class SendInvalid
{
    use PropertyReadable;

    /**
     * 初始化数据
     *
     * @param string $data
     */
    public function __construct(private string $data)
    {
    }
}
