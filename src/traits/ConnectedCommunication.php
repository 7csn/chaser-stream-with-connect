<?php

declare(strict_types=1);

namespace chaser\stream\traits;

/**
 * 连接通信
 *
 * @package chaser\stream\traits
 * 
 * @property resource $stream
 * @property int $readBufferSize
 */
trait ConnectedCommunication
{
    /**
     * @inheritDoc
     */
    public function connect(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function receive()
    {
        return fread($this->stream, $this->readBufferSize);
    }

    /**
     * @inheritDoc
     */
    public function send(string $data)
    {
        return fwrite($this->stream, $data);
    }
}
