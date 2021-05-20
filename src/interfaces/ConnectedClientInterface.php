<?php

declare(strict_types=1);

namespace chaser\stream\interfaces;

use chaser\stream\interfaces\parts\CommunicationConnectedInterface;

/**
 * 有通信连接的流客户端接口
 *
 * @package chaser\stream\interfaces
 */
interface ConnectedClientInterface extends ClientInterface, CommunicationConnectedInterface
{
}
