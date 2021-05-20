<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

/**
 * 有连接的流服务器接口
 *
 * @package chaser\stream\interfaces
 */
interface ConnectedServerInterface extends ServerInterface
{
    /**
     * 接受客户端连接
     */
    public function acceptConnection(): void;

    /**
     * 移除指定连接
     *
     * @param string $hash
     */
    public function removeConnection(string $hash): void;
}
