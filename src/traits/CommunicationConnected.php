<?php

declare(strict_types=1);

namespace chaser\stream\traits;

use chaser\stream\event\{Message, SendBufferDrain, SendBufferFull, SendFail, SendInvalid};
use chaser\stream\interfaces\part\CommunicationConnectInterface;

/**
 * 通信连接部分特征
 *
 * @package chaser\stream\traits
 *
 * @property-read int $status
 * @property-read int $readBytes
 * @property-read int $writtenBytes
 * @property-read string $sendBuffer
 *
 * @property-read int $readBufferSize
 * @property-read int $maxSendBufferSize
 *
 * @see CommunicationConnectInterface
 */
trait CommunicationConnected
{
    /**
     * 当前状态
     *
     * @var int
     */
    protected int $status = CommunicationConnectInterface::STATUS_INITIAL;

    /**
     * 读的字节数
     *
     * @var int
     */
    protected int $readBytes = 0;

    /**
     * 写的字节数
     *
     * @var int
     */
    protected int $writtenBytes = 0;

    /**
     * 发送缓冲区
     *
     * @var string
     */
    protected string $sendBuffer = '';

    /**
     * @inheritDoc
     */
    public static function configurations(): array
    {
        return ['readBufferSize' => self::READ_BUFFER_SIZE, 'maxSendBufferSize' => self::MAX_SEND_BUFFER_SIZE];
    }

    /**
     * @inheritDoc
     */
    public function receive(): void
    {
        if ($this->status === self::STATUS_ESTABLISHED) {
            $this->read();
        }
    }

    /**
     * @inheritDoc
     */
    public function send(string $data): bool
    {
        if ($data === '' || $this->isSendAble() === false) {
            return false;
        }

        if ($this->sendBuffer === '') {

            $writtenLength = $this->writeToSocket($data);

            if ($writtenLength > 0) {

                $this->writtenBytes += $writtenLength;
                if ($writtenLength === strlen($data)) {
                    return true;
                }

                $this->sendBuffer = substr($data, $writtenLength);
            } else {

                if ($this->invalid()) {
                    $this->dispatch(SendInvalid::class, $data);
                    $this->destroy(true);
                    return false;
                }

                $this->sendBuffer = $data;
            }

            // 有发送缓冲，添加可写反应
            $this->addWriteReact(fn(): void => $this->write());
        } else {
            $this->sendBuffer .= $data;
        }

        if (strlen($this->sendBuffer) >= $this->maxSendBufferSize) {
            $this->dispatch(SendBufferFull::class);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->status === self::STATUS_CONNECTING || $this->status === self::STATUS_ESTABLISHED) {
            $this->status = self::STATUS_CLOSING;
            $this->closeHandle();
        }
    }

    /**
     * 配置套接字流
     */
    protected function configureSocket(): void
    {
        // 非阻塞模式、兼容 hhvm 无缓冲
        stream_set_blocking($this->socket, false);
        stream_set_read_buffer($this->socket, 0);
    }

    /**
     * 检测连接是否完成
     *
     * @return bool
     */
    protected function isEstablished(): bool
    {
        return true;
    }

    /**
     * 返回是否可发送数据
     *
     * @return bool
     */
    protected function isSendAble(): bool
    {
        return $this->status === self::STATUS_ESTABLISHED || $this->status === self::STATUS_CONNECTING;
    }

    /**
     * 读操作
     *
     * @param bool $checkEof
     */
    protected function read(bool $checkEof = true): void
    {
        $read = $this->readFromSocket();
        if ($read) {
            $length = strlen($read);
            $this->readBytes += $length;
            $this->readHandle($read);
        } elseif ($checkEof && $this->invalid()) {
            $this->destroy(true);
        }
    }

    /**
     * 写操作
     */
    protected function write(): void
    {
        if ($this->sendBuffer) {
            if (0 < $writtenLength = $this->writeToSocket($this->sendBuffer)) {
                $this->writtenBytes += $writtenLength;
                $this->writeHandle($writtenLength);
            } else {
                $this->dispatch(SendFail::class);
                $this->destroy(true);
            }
        }
    }

    /**
     * 破坏连接操作
     *
     * @param bool $force
     */
    protected function destroy(bool $force = false): void
    {
        if ($force || $this->status === self::STATUS_CLOSED) {
            $this->delReadReact();

            if ($this->sendBuffer) {
                $this->sendBuffer = '';
                $this->delWriteReact();
            }

            $this->closeSocket();
            $this->status = self::STATUS_CLOSED;
            $this->dispatcher->clear();

            $this->destroyHandle();
        }
    }

    /**
     * 读数据处理
     *
     * @param string $data
     */
    protected function readHandle(string $data): void
    {
        $this->dispatchMessage($data);
    }

    /**
     * 写数据处理
     *
     * @param int $length
     */
    protected function writeHandle(int $length): void
    {
        // 发送缓冲全部写入成功
        if ($length === strlen($this->sendBuffer)) {
            // 清除写侦听
            $this->delWriteReact();

            // 清空发送缓冲区、事件分发
            $this->sendBuffer = '';
            $this->dispatch(SendBufferDrain::class);

            // 写入完成处理
            $this->writeCompleteHandle();
        } else {
            // 更新发送缓冲区
            $this->sendBuffer = substr($this->sendBuffer, $length);
        }
    }

    /**
     * 消息事件分发
     *
     * @param mixed $message
     */
    protected function dispatchMessage(mixed $message): void
    {
        $this->dispatch(Message::class, $message);
    }

    /**
     * 套接字读
     *
     * @return string
     */
    protected function readFromSocket(): string
    {
        return (string)@fread($this->socket, $this->readBufferSize);
    }

    /**
     * 套接字写
     *
     * @param string $data
     * @return int
     */
    protected function writeToSocket(string $data): int
    {
        return (int)@fwrite($this->socket, $data);
    }

    /**
     * 添加写事件侦听到事件循环
     *
     * @param callable $callback
     * @return bool
     */
    protected function addWriteReact(callable $callback): bool
    {
        return $this->reactor->addWrite($this->socket, $callback);
    }

    /**
     * 从事件循环中移除写事件侦听
     *
     * @return bool
     */
    protected function delWriteReact(): bool
    {
        return $this->reactor->delWrite($this->socket);
    }
}
