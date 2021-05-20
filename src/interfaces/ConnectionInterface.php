<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

use chaser\stream\interfaces\parts\{CommonInterface, CommunicationInterface, CommunicationConnectedInterface};

/**
 * 流服务器接收的连接接口
 *
 * @package chaser\stream\interfaces
 */
interface ConnectionInterface extends CommonInterface, CommunicationInterface, CommunicationConnectedInterface
{
    /**
     * 获取对象标识
     *
     * @return string
     */
    public function hash(): string;
}
