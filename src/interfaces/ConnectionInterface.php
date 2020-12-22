<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

use chaser\stream\interfaces\parts\CommunicationInterface;
use chaser\stream\interfaces\parts\ConfigurationInterface;
use chaser\stream\interfaces\parts\ConnectedInterface;
use chaser\stream\interfaces\parts\StreamInterface;

/**
 * 连接
 *
 * @package chaser\stream\interfaces
 */
interface ConnectionInterface extends CommunicationInterface, ConfigurationInterface, ConnectedInterface, StreamInterface
{
    /**
     * 获取对象标识
     *
     * @return string
     */
    public function hash(): string;
}
