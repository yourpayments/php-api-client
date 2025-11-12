<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Это класс для одноразовых токенов
 **/
class OneTimeUseToken
{
    /** @var string Сам токен */
    private string $token;

    /** @var string Id сеси */
    private string $sessionId;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /**
     * @param string $token
     * @param string $sessionId
     */
    public function __construct(string $token, string $sessionId)
    {
        $this->setToken($token);
        $this->setSessionId($sessionId);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return OneTimeUseToken
     */
    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @param string $sessionId
     * @return OneTimeUseToken
     */
    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    public function toArray()
    {
        return [
            'token' => $this->getToken(),
            'sessionId' => $this->getSessionId(),
        ];
    }
}
