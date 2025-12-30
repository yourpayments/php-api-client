<?php

declare(strict_types=1);

namespace Ypmn;

interface StoredCredentialsInterface
{
    /**
     * Получить
     * @return string
     */
    public function getUseType(): string;

    /**
     * Установить
     * @param string $useType
     * @return $this
     */
    public function setUseType(string $useType): self;

    /**
     * Получить id исходной операции
     * @return string id исходной операции
     */
    public function getUseId(): string;

    /**
     * Установить id исходной операции
     * @param string $useId id исходной операции
     * @return $this
     */
    public function setUseId(string $useId): self;

    /**
     * Получить тип использования привязки.
     *
     * @return string
     */
    public function getConsentType(): string;

    /**
     * Установить тип использования привязки.
     *
     * @param string $consentType
     *
     * @return $this
     */
    public function setConsentType(string $consentType): self;

    /**
     * Получить причину создания привязки.
     *
     * @return string
     */
    public function getSubscriptionPurpose(): string;

    /**
     * Установить причину создания привязки.
     *
     * @param string $subscriptionPurpose
     *
     * @return $this
     */
    public function setSubscriptionPurpose(string $subscriptionPurpose): self;

    /**
     * Вернуть массив всех имеющихся параметров
     *
     * @return array
     */
    public function arraySerialize(): array;
}
