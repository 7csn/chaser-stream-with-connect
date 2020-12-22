<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\interfaces\ConnectedServerInterface;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\traits\Communication;
use chaser\stream\traits\Configuration;
use chaser\stream\traits\ConnectedCommunication;
use chaser\stream\traits\Stream;

/**
 * 连接
 *
 * @package chaser\stream
 *
 * @property int $readBufferSize
 */
abstract class Connection implements ConnectionInterface
{
    use Communication, Configuration, ConnectedCommunication, Stream {
        Stream::close as streamClose;
    }

    /**
     * 服务器对象
     *
     * @var ConnectedServerInterface
     */
    protected ConnectedServerInterface $server;

    /**
     * 对象标识
     *
     * @var string
     */
    protected string $hash;

    /**
     * 常规配置
     *
     * @var array
     */
    protected array $configurations = [
        'readBufferSize' => self::READ_BUFFER_SIZE
    ];

    /**
     * 构造
     *
     * @param ConnectedServerInterface $server
     * @param resource $stream
     * @param string $address
     */
    public function __construct(ConnectedServerInterface $server, $stream, string $address)
    {
        $this->server = $server;
        $this->stream = $stream;
        $this->remoteAddress = $address;
    }

    /**
     * @inheritDoc
     */
    public function hash(): string
    {
        return $this->hash ??= spl_object_hash($this);
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        $close = $this->streamClose();
        if ($close) {
            $this->server->removeConnection($this->hash());
        }
        return $close;
    }
}
