<?php

declare(strict_types=1);

namespace chaser\stream\events;

use chaser\stream\traits\PropertyReadable;

/**
 * 信息发送无效（客户端关闭）事件类
 *
 * @package chaser\stream\events
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
