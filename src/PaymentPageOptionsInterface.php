<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Это файл интерфейса для настройки платёжной страницы
 **/
interface PaymentPageOptionsInterface
{
    /**
     * Установить максимальное время оплаты заказа
     * В секундах, со времени создания
     * @param int $timeoutSeconds
     * @return $this
     * @throws PaymentException
     */
    public function setOrderTimeout(int $timeoutSeconds): self;

    /**
     * Получить максимальное время оплаты заказа
     * В секундах, со времени создания
     * @return int
     */
    public function getOrderTimeout(): int;

    /**
     * @return array
     */
    public function toArray(): array;
}
