<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Это файл класса для настройки платёжной страницы
 **/
class PaymentPageOptions implements PaymentPageOptionsInterface
{
    /** @var int минимальное время на оплату заказа */
    const MIN_ORDER_TIMEOUT_SECONDS = 60;

    /** @var int|null максимальное время оплаты товара (в сек) */
    protected ?int $timeoutSeconds = null;

    /** @throws PaymentException */
    public function __construct($timeoutSeconds)
    {
        $this->setOrderTimeout($timeoutSeconds);
    }

    /** @inheritDoc */
    public function setOrderTimeout(int $timeoutSeconds): self
    {
        if ($timeoutSeconds < self::MIN_ORDER_TIMEOUT_SECONDS) {
            throw new PaymentException($timeoutSeconds . ' -- слишком маленькое время для оплаты заказа (в секундах)');
        }
        $this->timeoutSeconds = $timeoutSeconds;

        return $this;
    }

    /** @inheritDoc */
    public function getOrderTimeout(): int
    {
        return $this->timeoutSeconds;
    }

    /** @inheritDoc */
    public function toArray(): array
    {
        return [
            'orderTimeout'  => $this->getOrderTimeout(),
        ];
    }
}
