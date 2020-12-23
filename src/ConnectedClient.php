<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\interfaces\ConnectedClientInterface;
use chaser\stream\traits\ConnectedCommunication;

/**
 * 有连接的客户端
 *
 * @package chaser\stream
 *
 * @property int $readBufferSize
 */
abstract class ConnectedClient extends Client implements ConnectedClientInterface
{
    use ConnectedCommunication;

    /**
     * 常规配置
     *
     * @var array
     */
    protected array $configurations = [
        'readBufferSize' => self::READ_BUFFER_SIZE
    ];
}
