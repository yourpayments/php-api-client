<?php declare(strict_types=1);

namespace Ypmn\Payout;

interface PayoutPaymentResultInterface
{
    /**
     * Получить Метод Оплаты
     * @return string Метод Оплаты
     */
    public function getPaymentMethod(): string;

    /**
     * Установить Метод Оплаты
     * @param string $paymentMethod Метод Оплаты
     * @return self
     */
    public function setPaymentMethod(string $paymentMethod): self;

    /**
     * Получить Дату Авторизации платежа
     * @return string Дата Авторизации платежа
     */
    public function getPaymentDate(): string;

    /**
     * Установить Дату Авторизации платежа
     * @param string $paymentDate Дата Авторизации платежа
     * @return self
     */
    public function setPaymentDate(string $paymentDate): self;

    /**
     * Получить Код Авторизации
     * @return string|null Код Авторизации
     */
    public function getAuthCode(): ?string;

    /**
     * Установить Код Авторизации
     * @param string|null $authCode Код Авторизации
     * @return self
     */
    public function setAuthCode(?string $authCode): self;

    /**
     * Получить уникальный идентификатор банковской транзакции
     * @return string
     */
    public function getRrn(): ?string;

    /**
     * Установить уникальный идентификатор банковской транзакции
     * @param string|null $rrn Уникальный идентификатор банковской транзакции
     * @return self
     */
    public function setRrn(?string $rrn): self;

    /**
     * Получить блок комиссии выплаты
     * @return PayoutCommissionInterface
     */
    public function getCommission(): PayoutCommissionInterface;

    /**
     * Установить блок комиссии выплаты
     * @param PayoutCommissionInterface $commission блок комиссии выплаты
     * @return self
     */
    public function setCommission(PayoutCommissionInterface $commission): self;
}
