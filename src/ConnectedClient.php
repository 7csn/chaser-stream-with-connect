<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\interfaces\ConnectedClientInterface;

/**
 * 有连接的客户端
 *
 * @package chaser\stream
 */
abstract class ConnectedClient extends Client implements ConnectedClientInterface
{
}
