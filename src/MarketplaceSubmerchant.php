<?php declare(strict_types=1);

namespace Ypmn;

use JsonSerializable;
use stdClass;

/**
 * Это объект Сабмерчанта
 * (для маркетплейса/сплита/разделения платежа)
 */
class MarketplaceSubmerchant implements MarketplaceSubmerchantInterface
{
    /** @var string Код Сабмерчанта (можно получить в личном кабинете) */
    private string $merchantCode;

    /** @var float|null Сумма этого Сабмерчанта в заказе */
    private ?float $amount = null;

    /** @throws PaymentException */
    public function __construct(string $merchantCode, float $amount = null)
    {
        $this->setMerchantCode($merchantCode);
        if (null !== $amount) {
            $this->setAmount($amount);
        }
    }

    /** @inheritDoc */
    public function setMerchantCode(string $merchantCode): self
    {
        $this->merchantCode = $merchantCode;

        return $this;
    }

    /** @inheritDoc */
    public function getMerchantCode(): string
    {
        return $this->merchantCode;
    }

    /** @inheritDoc
     * @throws PaymentException
     */
    public function setAmount(float $amount): self
    {
        if ($amount <= 0) {
            throw new PaymentException('Отрицательные суммы не принимаются');
        }

        $this->amount = $amount;

        return $this;
    }

    /** @inheritDoc */
    public function getAmount(): float
    {
        return $this->amount;
    }
}
