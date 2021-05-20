<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\reactor\Driver;
use chaser\stream\events\Connect;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\subscribers\ConnectionSubscriber;
use chaser\stream\traits\{Common, Communication, ConnectedCommunication};

/**
 * 服务器接收的连接类
 *
 * @package chaser\stream
 */
class Connection implements ConnectionInterface
{
    use Common, Communication, ConnectedCommunication;

    /**
     * 服务器对象
     *
     * @var ConnectedServer
     */
    protected ConnectedServer $server;

    /**
     * 对象标识
     *
     * @var string
     */
    protected string $hash;

    /**
     * @inheritDoc
     */
    public static function subscriber(): string
    {
        return ConnectionSubscriber::class;
    }

    /**
     * 构造函数
     *
     * @param ConnectedServer $server
     * @param Driver $reactor
     * @param resource $stream
     */
    public function __construct(ConnectedServer $server, Driver $reactor, $stream)
    {
        // 非阻塞模式、兼容 hhvm 无缓冲
        stream_set_blocking($stream, false);
        stream_set_read_buffer($stream, 0);

        $this->server = $server;
        $this->reactor = $reactor;
        $this->socket = $stream;

        $this->initEventDispatcher();
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
    public function connect()
    {
        if ($this->status === self::STATUS_INITIAL) {
            $this->status = self::STATUS_CONNECTING;
            $this->addReadReactor([$this, 'connecting']);
        }
    }

    /**
     * 连接中
     */
    public function connecting()
    {
        if ($this->status === self::STATUS_CONNECTING && $this->checkConnection()) {
            $this->reactor->delRead($this->socket);
            $this->connected(true);
        }
    }

    /**
     * 连接成功
     *
     * @param bool $checkEof
     */
    protected function connected(bool $checkEof = false)
    {
        $this->status = self::STATUS_ESTABLISHED;
        $this->dispatch(Connect::class);
        $this->resumeRecv($checkEof);
    }
}
