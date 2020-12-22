<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

/**
 * 有连接的流服务器
 *
 * @package chaser\stream\interfaces
 */
interface ConnectedServerInterface extends ServerInterface
{
    /**
     * 移除指定连接
     *
     * @param string $hash
     */
    public function removeConnection(string $hash);
}
