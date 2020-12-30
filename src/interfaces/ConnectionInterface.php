<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

use chaser\stream\interfaces\parts\{CommunicationInterface, ConnectedInterface, HelperInterface};

/**
 * 连接
 *
 * @package chaser\stream\interfaces
 */
interface ConnectionInterface extends CommunicationInterface, ConnectedInterface, HelperInterface
{
    /**
     * 获取对象标识
     *
     * @return string
     */
    public function hash(): string;
}
