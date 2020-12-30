<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

use chaser\stream\interfaces\parts\ConnectedInterface;

/**
 * 有连接的流客户端
 *
 * @package chaser\stream\interfaces
 */
interface ConnectedClientInterface extends ClientInterface, ConnectedInterface
{
}
