<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

use chaser\stream\interfaces\part\{CommonInterface, CommunicationConnectInterface, CommunicationInterface};

/**
 * 流服务器接收的连接接口
 *
 * @package chaser\stream\interfaces
 */
interface ConnectionInterface extends CommonInterface, CommunicationConnectInterface, CommunicationInterface
{
    /**
     * 获取对象标识
     *
     * @return int
     */
    public function hash(): int;

    /**
     * 稳固连接
     */
    public function establish(): void;
}
