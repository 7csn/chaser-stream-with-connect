<?php

declare(strict_types=1);

namespace chaser\stream\traits;

use chaser\stream\exceptions\UnpackedException;
use chaser\stream\events\{
    RecvBufferFull,
    UnpackingFail,
    Message,
    SendBufferDrain,
    SendBufferFull,
    SendFail,
    SendInvalid
};
use chaser\stream\interfaces\parts\CommunicationConnectedInterface;

/**
 * 连接通信特征
 *
 * @package chaser\stream\traits
 *
 * @property int $readBufferSize
 * @property int $maxRecvBufferSize
 * @property int $maxSendBufferSize
 */
trait ConnectedCommunication
{
    /**
     * 常规配置
     *
     * @var array
     */
    protected array $configurations = [
        'readBufferSize' => self::READ_BUFFER_SIZE,
        'maxRecvBufferSize' => self::MAX_REQUEST_BUFFER_SIZE,
        'maxSendBufferSize' => self::MAX_RESPONSE_BUFFER_SIZE
    ];

    /**
     * 当前状态
     *
     * @var int
     */
    protected int $status = CommunicationConnectedInterface::STATUS_INITIAL;

    /**
     * 接收状态
     *
     * @var bool
     */
    protected bool $receiving = false;

    /**
     * 接收缓冲区内容
     *
     * @var string
     */
    protected string $recvBuffer = '';

    /**
     * 发送缓冲区内容
     *
     * @var string
     */
    protected string $sendBuffer = '';

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
     * @inheritDoc
     */
    public function receive()
    {
        $this->read();
    }

    /**
     * 套接字写回调
     */
    public function writeCallback()
    {
        $this->write();
    }

    /**
     * @inheritDoc
     */
    public function send(string $data)
    {
        // 关闭阶段不能发送
        if ($this->status === self::STATUS_CLOSING || $this->status === self::STATUS_CLOSED) {
            return;
        }

        if ($data === '') {
            return;
        }

        $length = strlen($data);

        if ($this->sendBuffer === '') {
            $wLen = $this->writeOnly($data);

            // 发送失败（客户端关闭）
            if (!$wLen && $this->invalid()) {
                $this->dispatch(SendInvalid::class, $data);
                $this->destroy();
                return;
            }

            // 记录写入数据量
            $this->writtenBytes += $wLen;

            // 发送完毕直接返回
            if ($wLen === $length) {
                return;
            }

            // 剩余数据计入发送缓冲区
            $this->sendBuffer = substr($data, $wLen);

            // 监听套接字写，发送缓冲区数据由套接字写回调处理
            $this->reactor->addWrite($this->socket, [$this, 'writeCallback']);
        } else {
            if ($this->isSendBufferFull()) {
                return;
            }
            $this->sendBuffer .= $data;
        }

        $this->isSendBufferFull();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        if ($this->status === self::STATUS_CONNECTING) {
            $this->destroy();
            return;
        }

        if ($this->status === self::STATUS_CLOSING || $this->status === self::STATUS_CLOSED) {
            return;
        }

        $this->status = self::STATUS_CLOSING;

        $this->sendBuffer === '' ? $this->destroy() : $this->pauseRecv();
    }

    /**
     * 套接字读
     *
     * @return false|string
     */
    protected function readOnly()
    {
        return @fread($this->socket, $this->readBufferSize);
    }

    /**
     * 套接字写
     *
     * @param string $data
     * @return false|int
     */
    protected function writeOnly(string $data)
    {
        return @fwrite($this->socket, $data);
    }

    /**
     * 检测连接是否完成
     *
     * @return bool
     */
    protected function checkConnection(): bool
    {
        return true;
    }

    /**
     * 读操作
     *
     * @param bool $checkEof
     */
    protected function read(bool $checkEof = true)
    {
        $read = $this->readOnly();

        if ($read) {
            $length = strlen($read);
            $this->readBytes += $length;
            $this->recvBuffer .= $read;
            try {
                $message = $this->getMessage();
                if ($message) {
                    $this->dispatch(Message::class, $message);
                } elseif (strlen($this->recvBuffer) >= $this->maxRecvBufferSize) {
                    $this->dispatch(RecvBufferFull::class);
                    $this->destroy();
                }
            } catch (UnpackedException $e) {
                $this->dispatch(UnpackingFail::class, $e);
                $this->destroy();
            }
        } elseif ($checkEof && ($read === false || $this->invalid())) {
            $this->destroy();
        }
    }

    /**
     * 尝试解包
     *
     * @return string|object|null
     * @throws UnpackedException
     */
    protected function getMessage()
    {
        $data = $this->recvBuffer;
        $this->recvBuffer = '';
        return $data;
    }

    /**
     * 写操作
     */
    protected function write()
    {
        $length = strlen($this->sendBuffer);

        if ($length === 0) {
            return;
        }

        $writeLength = $this->writeOnly($this->sendBuffer);

        if ($writeLength > 0) {
            // 写入计数
            $this->writtenBytes += $writeLength;

            // 发送缓冲全部写入成功
            if ($writeLength === $length) {
                // 清除写侦听
                $this->reactor->delWrite($this->socket);

                // 清空发送缓冲区
                $this->sendBuffer = '';

                // 清空缓冲区事件分发
                $this->dispatch(SendBufferDrain::class);

                // 处于关闭阶段，销毁连接
                if ($this->status === self::STATUS_CLOSING) {
                    $this->destroy();
                }
            } else {
                // 更新发送缓冲
                $this->sendBuffer = substr($this->sendBuffer, $writeLength);
            }
        } // 写入失败（分发事件），破坏连接
        else {
            $this->dispatch(SendFail::class);
            $this->destroy();
        }
    }

    /**
     * 检测是否发送缓冲区满
     *
     * @return bool
     */
    protected function isSendBufferFull(): bool
    {
        $full = strlen($this->sendBuffer) >= $this->maxSendBufferSize;
        if ($full) {
            $this->dispatch(SendBufferFull::class);
        }
        return $full;
    }

    /**
     * 破坏连接
     */
    protected function destroy()
    {
        if ($this->status === self::STATUS_CLOSED) {
            return;
        }

        $this->reactor->delRead($this->socket);
        $this->reactor->delWrite($this->socket);

        $this->server->removeConnection($this->hash());
        $this->closeSocket();

        $this->status = self::STATUS_CLOSED;

        $this->recvBuffer = $this->sendBuffer = '';
    }

    /**
     * 开始或继续接收数据
     *
     * @param bool $checkEof
     */
    protected function resumeRecv(bool $checkEof = false)
    {
        if ($this->receiving === false) {
            $this->receiving = true;
            $this->addReadReactor([$this, 'receive']);
            $this->read($checkEof);
        }
    }

    /**
     * 停止接收数据
     */
    protected function pauseRecv()
    {
        if ($this->receiving === true) {
            $this->reactor->delRead($this->socket);
            $this->receiving = false;
        }
    }
}
