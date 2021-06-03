<?php

declare(strict_types=1);

namespace chaser\stream\interfaces\part;

/**
 * 服务器连接部分接口
 *
 * @package chaser\stream\interfaces\part
 */
interface ServerConnectInterface
{
    /**
     * 移除指定连接
     *
     * @param int $hash
     */
    public function removeConnection(int $hash): void;
}
