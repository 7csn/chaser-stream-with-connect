<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\reactor\Reactor;
use chaser\stream\events\Connect;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\traits\{Communication, ConnectedCommunication, Helper};

/**
 * 连接
 *
 * @package chaser\stream
 *
 * @property int $readBufferSize
 * @property int $maxRecvBufferSize
 * @property int $maxRendBufferSize
 * @property string $subscriber
 */
class Connection implements ConnectionInterface
{
    use Communication, Helper, ConnectedCommunication;

    /**
     * 服务器对象
     *
     * @var ConnectedServer
     */
    protected ConnectedServer $server;

    /**
     * 事件反应器
     *
     * @var Reactor
     */
    protected Reactor $reactor;

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
        'readBufferSize' => self::READ_BUFFER_SIZE,
        'maxRecvBufferSize' => self::MAX_RECV_BUFFER_SIZE,
        'maxSendBufferSize' => self::MAX_SEND_BUFFER_SIZE,
        'subscriber' => ''
    ];

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
     * @param Reactor $reactor
     * @param resource $stream
     */
    public function __construct(ConnectedServer $server, Reactor $reactor, $stream)
    {
        // 非阻塞模式、兼容 hhvm 无缓冲
        stream_set_blocking($stream, false);
        stream_set_read_buffer($stream, 0);

        $this->server = $server;
        $this->reactor = $reactor;
        $this->stream = $stream;

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
            $this->reactor->addRead($this->stream, [$this, 'connecting']);
        }
    }

    /**
     * 连接中
     */
    public function connecting()
    {
        if ($this->status === self::STATUS_CONNECTING && $this->checkConnection()) {
            $this->reactor->delRead($this->stream);
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
        $this->dispatchCache(Connect::class);
        $this->resumeRecv($checkEof);
    }
}
