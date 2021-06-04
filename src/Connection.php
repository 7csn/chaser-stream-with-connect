<?php

declare(strict_types=1);

namespace chaser\stream;

use chaser\container\ContainerInterface;
use chaser\reactor\Driver;
use chaser\stream\event\Establish;
use chaser\stream\interfaces\ConnectionInterface;
use chaser\stream\interfaces\part\ServerConnectInterface;
use chaser\stream\subscriber\ConnectionSubscriber;
use chaser\stream\traits\{Common, Communication, CommunicationConnected};

/**
 * 服务器接收的连接类
 *
 * @package chaser\stream
 */
class Connection implements ConnectionInterface
{
    use Common, Communication, CommunicationConnected;

    /**
     * 服务器对象
     *
     * @var ServerConnectInterface
     */
    protected ServerConnectInterface $server;

    /**
     * 对象标识
     *
     * @var int
     */
    protected int $hash;

    /**
     * @inheritDoc
     */
    public static function configurations(): array
    {
        return CommunicationConnected::configurations() + Common::configurations();
    }

    /**
     * @inheritDoc
     */
    public static function subscriber(): string
    {
        return ConnectionSubscriber::class;
    }

    /**
     * 构造方法
     *
     * @param ContainerInterface $container
     * @param ServerConnectInterface $server
     * @param Driver $reactor
     * @param resource $socket
     */
    public function __construct(ContainerInterface $container, ServerConnectInterface $server, Driver $reactor, $socket)
    {
        $this->container = $container;
        $this->server = $server;
        $this->reactor = $reactor;
        $this->socket = $socket;

        $this->configureSocket();

        $this->initCommon();
    }

    /**
     * @inheritDoc
     */
    public function hash(): int
    {
        return $this->hash ??= (int)$this->socket;
    }

    /**
     * @inheritDoc
     */
    public function establish(): void
    {
        if ($this->status === self::STATUS_INITIAL) {
            $this->status = self::STATUS_CONNECTING;
            $this->addReadReact(function () {
                if ($this->status === self::STATUS_CONNECTING && $this->isEstablished()) {
                    $this->delReadReact();
                    $this->status = self::STATUS_ESTABLISHED;
                    $this->dispatchEstablish();
                    $this->addReadReact([$this, 'receive']);
                }
            });
        }
    }

    /**
     * 连接稳定事件分发
     */
    protected function dispatchEstablish(): void
    {
        $this->dispatch(Establish::class);
    }

    /**
     * 关闭连接处理
     */
    protected function closeHandle(): void
    {
        $this->sendBuffer === '' ? $this->destroy() : $this->delReadReact();
    }

    /**
     * 破坏连接处理
     */
    protected function destroyHandle(): void
    {
        $this->server->removeConnection($this->hash());
    }

    /**
     * 写入完成
     */
    protected function writeCompleteHandle(): void
    {
        $this->destroy();
    }
}
