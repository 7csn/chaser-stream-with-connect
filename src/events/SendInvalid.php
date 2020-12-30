<?php

declare(strict_types=1);

namespace chaser\stream\events;

use chaser\stream\traits\PropertyReadable;

/**
 * 连接通信发送无效（客户端关闭）
 *
 * @package chaser\stream\events
 */
class SendInvalid
{
    use PropertyReadable;

    /**
     * 发送信息
     *
     * @property-read string
     */
    protected string $data;

    /**
     * 初始化数据
     *
     * @param string $data
     */
    public function __construct(string $data)
    {
        $this->data = $data;
    }
}
