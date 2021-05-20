<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\exceptions\ClientConnectedException;
use chaser\stream\subscribers\ConnectedClientSubscriber;
use chaser\stream\events\{Connect, ConnectFail};
use chaser\stream\interfaces\ConnectedClientInterface;
use chaser\stream\traits\ConnectedCommunication;

/**
 * 有通信连接的客户端类
 *
 * @package chaser\stream
 */
abstract class ConnectedClient extends Client implements ConnectedClientInterface
{
    use ConnectedCommunication;

    /**
     * @inheritDoc
     */
    public static function subscriber(): string
    {
        return ConnectedClientSubscriber::class;
    }

    /**
     * @throws ClientConnectedException
     */
    public function connect(): void
    {
        // 可连接状态：初始、关闭中、已关闭
        if ($this->status === self::STATUS_INITIAL || $this->status === self::STATUS_CLOSING || $this->status === self::STATUS_CLOSED) {
            // 关闭中套接字流尚在，不需另行创建
            if ($this->status === self::STATUS_CLOSING) {
                $this->status = self::STATUS_CONNECTING;
                $this->reactor->addWrite($this->socket, [$this, 'connecting']);
            } else {
                $this->create();
                $this->status = self::STATUS_CONNECTING;
                $this->reactor->addWrite($this->socket, [$this, 'connecting']);
            }
        }
    }

    /**
     * 连接中
     */
    public function connecting()
    {
        if ($this->status === self::STATUS_CONNECTING) {

            $this->reactor->delWrite($this->socket);

            if ($remoteAddress = stream_socket_get_name($this->socket, true)) {

                stream_set_blocking($this->socket, false);
                stream_set_read_buffer($this->socket, 0);

                $this->remoteAddress = $remoteAddress;

                if ($this->checkConnection()) {
                    $this->connected();
                }

            } else {
                $this->dispatch(ConnectFail::class);
            }
        }
    }

    /**
     * 连接成功
     */
    protected function connected()
    {
        $this->addReadReactor([$this, 'receive']);
        $this->status = self::STATUS_ESTABLISHED;
        $this->dispatch(Connect::class);
    }
}
