<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\events\{Connect, ConnectFail};
use chaser\stream\interfaces\ConnectedClientInterface;
use chaser\stream\traits\ConnectedCommunication;

/**
 * 有连接的客户端
 *
 * @package chaser\stream
 *
 * @property int $readBufferSize
 * @property int $maxRecvBufferSize
 * @property int $maxRendBufferSize
 * @property string $subscriber
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
        'readBufferSize' => self::READ_BUFFER_SIZE,
        'maxRecvBufferSize' => self::MAX_SEND_BUFFER_SIZE,
        'maxSendBufferSize' => self::MAX_RECV_BUFFER_SIZE,
        'subscriber' => ''
    ];

    /**
     * @inheritDoc
     */
    public static function subscriber(): string
    {
        return ConnectedClientSubscriber::class;
    }

    /**
     * @inheritDoc
     */
    public function connect()
    {
        // 可连接状态：初始、关闭中、已关闭
        if ($this->status === self::STATUS_INITIAL || $this->status === self::STATUS_CLOSING || $this->status === self::STATUS_CLOSED) {
            // 关闭中套接字流尚在，不需另行创建
            if ($this->status === self::STATUS_CLOSING || $this->create()) {
                $this->status = self::STATUS_CONNECTING;
                $this->reactor->addWrite($this->stream, [$this, 'connecting']);
            }
        }
    }

    /**
     * 连接中
     */
    public function connecting()
    {
        if ($this->status === self::STATUS_CONNECTING) {

            $this->reactor->delWrite($this->stream);

            if ($remoteAddress = stream_socket_get_name($this->stream, true)) {

                stream_set_blocking($this->stream, false);
                stream_set_read_buffer($this->stream, 0);

                $this->remoteAddress = $remoteAddress;

                if ($this->checkConnection()) {

                    $this->connected();
                }

            } else {
                $this->dispatchCache(ConnectFail::class);
            }
        }
    }

    /**
     * 连接成功
     */
    protected function connected()
    {
        $this->addRecvReactor();
        $this->status = self::STATUS_ESTABLISHED;
        $this->dispatchCache(Connect::class);
    }
}
