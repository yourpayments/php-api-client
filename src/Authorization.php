<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Авторизация платежа
 */
class Authorization implements AuthorizationInterface
{
    /**
     * включить страницу оплаты Ypmn
     * @var bool страница оплаты Ypmn включена?
     */
    private bool $usePaymentPage = true;
    private ?string $paymentMethod = PaymentMethods::CCVISAMC;

    /** @var CardDetailsInterface|null Данные карты */
    private ?CardDetailsInterface $cardDetails = null;

    /** @var MerchantTokenInterface|null Данные карты (в виде объекта токена) */
    private ?MerchantTokenInterface $merchantToken = null;

    /** @var OneTimeUseToken|null Одноразовый токен оплаты */
    private ?OneTimeUseToken $oneTimeUseToken = null;

    /** @var PaymentPageOptions|null */
    private ?PaymentPageOptions $paymentPageOptions = null;

    /**
     * Создать Платёжную Авторизацию
     * (можно указать метод из справочника PaymentMethods.php,
     * или передать null, чтобы плательщик выбрал метод сам)
     * @param string|null $paymentMethodType Метод оплаты (из справочника)
     * @param bool $isPaymentPageUsed страница оплаты Ypmn включена?
     * @throws PaymentException Ошибка оплаты
     */
    public function __construct(?string $paymentMethodType = null, bool $isPaymentPageUsed = true) {
        $this->setPaymentMethod($paymentMethodType);
        $this->setUsePaymentPage($isPaymentPageUsed);
    }

    /** @inheritDoc */
    public function setPaymentMethod(?string $paymentMethod = null) : self
    {
        if (is_string($paymentMethod)) {
            $paymentMethod = strtoupper($paymentMethod);
        }

        switch ($paymentMethod) {
            case PaymentMethods::CCVISAMC:
            case PaymentMethods::FASTER_PAYMENTS:
            case PaymentMethods::SOM:
            case PaymentMethods::MIRPAY:
            case PaymentMethods::ALFAPAY:
            case PaymentMethods::TPAY:
            case PaymentMethods::SBERPAY:
            case PaymentMethods::PAYOUT:
            case PaymentMethods::PAYOUT_FP:
            case null:
                $this->paymentMethod = $paymentMethod;
                break;
            case '':
                $this->paymentMethod = null;
                break;
            default:
                throw new PaymentException('Неверный тип оплаты в авторизации');
        }

        return $this;
    }

    /** @inheritDoc */
    public function setUsePaymentPage(bool $isUsed) : self
    {
        if ($isUsed === true) {
            if (is_null($this->merchantToken) && is_null($this->cardDetails)) {
                $this->usePaymentPage = $isUsed;
            } else {
                throw new PaymentException('For using PaymentPage need to make MerchantToken = NULL and CardDetails = NULL');
            }
        } else {
            $this->usePaymentPage = $isUsed;
        }

        return $this;
    }

    /** @inheritDoc */
    public function getUsePaymentPage(): bool
    {
        return $this->usePaymentPage;
    }

    /** @inheritDoc */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /** @inheritDoc */
    public function getCardDetails(): ?CardDetailsInterface
    {
        return $this->cardDetails;
    }

    /** @inheritDoc */
    public function setCardDetails(?CardDetailsInterface $cardDetails): self
    {
        if (is_null($this->merchantToken) && $this->usePaymentPage === false) {
            $this->cardDetails = $cardDetails;

            return $this;
        } else {
            throw new PaymentException('For using CardDetails need to make MerchantToken = NULL and usePaymentPage = false');
        }
    }

    /** @inheritDoc */
    public function getMerchantToken(): ?MerchantTokenInterface
    {
        return $this->merchantToken;
    }

    /** @inheritDoc */
    public function setOneTimeUseToken(?OneTimeUseToken $oneTimeUseToken): self
    {
        $this->setCardDetails(null);
        $this->setUsePaymentPage(false);
        $this->oneTimeUseToken = $oneTimeUseToken;

        return $this;
    }

    /** @inheritDoc */
    public function getOneTimeUseToken(): ?OneTimeUseToken
    {
        return $this->oneTimeUseToken;
    }

    /** @inheritDoc */
    public function setMerchantToken(?MerchantTokenInterface $merchantToken): self
    {
        if (is_null($this->getCardDetails()) && $this->getUsePaymentPage() === false) {
            $this->merchantToken = $merchantToken;

            return $this;
        } else {
            throw new PaymentException('For using MerchantToken need to make CardDetails = NULL and usePaymentPage = false');
        }
    }

    /** @inheritDoc */
    public function setPaymentPageOptions(PaymentPageOptionsInterface $paymentPageOptions): self
    {
        $this->paymentPageOptions = $paymentPageOptions;

        return $this;
    }

    /** @inheritDoc */
    public function getPaymentPageOptions(): PaymentPageOptionsInterface
    {
        return $this->paymentPageOptions;
    }

    /**
     * @return array
     */
    public function arraySerialize(): array
    {
        $resultArray = [
            'usePaymentPage' => ($this->usePaymentPage ? 'YES' : 'NO'),
            'paymentMethod'  => $this->paymentMethod,
        ];

        if (!is_null($this->cardDetails)) {
            $resultArray['cardDetails'] = $this->cardDetails->toArray();
        }

        if (!is_null($this->oneTimeUseToken)) {
            $resultArray['oneTimeUseToken'] = $this->oneTimeUseToken->toArray();
        }

        if (!is_null($this->merchantToken)) {
            $resultArray['merchantToken'] = $this->merchantToken->toArray();
        }

        if (!is_null($this->paymentPageOptions) && $this->paymentPageOptions->getOrderTimeout() > 0) {
            $resultArray['paymentPageOptions'] = $this->paymentPageOptions->toArray();
        }

        return $resultArray;
    }
}
