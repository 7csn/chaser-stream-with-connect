<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\stream\interfaces\part\CommunicationConnectInterface;
use chaser\stream\event\ConnectFail;
use chaser\stream\subscriber\ConnectedClientSubscriber;
use chaser\stream\traits\CommunicationConnected;

/**
 * 有通信连接的客户端类
 *
 * @package chaser\stream
 */
abstract class ConnectClient extends Client implements CommunicationConnectInterface
{
    use CommunicationConnected;

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
    public static function configurations(): array
    {
        return CommunicationConnected::configurations() + parent::configurations();
    }

    /**
     * @inheritDoc
     */
    public function ready(): void
    {
        // 可连接状态：初始、已关闭
        if ($this->status === self::STATUS_INITIAL || $this->status === self::STATUS_CLOSED) {
            $this->create();
            $this->status = self::STATUS_CONNECTING;
            $this->setWriteReact(function () {
                if ($this->status === self::STATUS_CONNECTING) {
                    if ($remoteAddress = stream_socket_get_name($this->socket, true)) {
                        $this->target = $remoteAddress;
                        $this->configureSocket();
                        if ($this->isEstablished()) {
                            $this->delWriteReact();
                            $this->status = self::STATUS_ESTABLISHED;
                            $this->readyHandle();
                        }
                    } else {
                        $this->delWriteReact();
                        $this->dispatch(ConnectFail::class);
                    }
                }
            });
        }
    }

    /**
     * 关闭连接处理
     */
    protected function closeHandle(): void
    {
        $this->destroy();
    }

    /**
     * 破坏连接处理
     */
    protected function destroyHandle(): void
    {

    }

    /**
     * 写入完成
     */
    protected function writeCompleteHandle(): void
    {
    }
}
