<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Авторизация платежа
 */
class Authorization implements AuthorizationInterface
{
    /**
     * Включить страницу оплаты Ypmn
     * @var bool страница оплаты Ypmn включена?
     */
    private bool $usePaymentPage = true;

    /**
     * Платёжный метод
     * @var string|null
     */
    private ?string $paymentMethod = PaymentMethods::CCVISAMC;

    /**
     * @var array|null
     */
    private ?array $threeDSecure = null;

    /**
     * @var CardDetailsInterface|null Данные карты
     */
    private ?CardDetailsInterface $cardDetails = null;

    /** @var MerchantTokenInterface|null Данные карты (в виде объекта токена) */
    private ?MerchantTokenInterface $merchantToken = null;

    /** @var OneTimeUseToken|null Одноразовый токен оплаты */
    private ?OneTimeUseToken $oneTimeUseToken = null;

    /** @var PaymentPageOptions|null */
    private ?PaymentPageOptions $paymentPageOptions = null;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /**
     * Создать Авторизацию платежа
     * (можно указать метод из справочника PaymentMethods.php,
     * или передать null, чтобы плательщик выбрал метод сам)
     * @param string|null $paymentMethodType Метод оплаты (из справочника)
     * @param bool $isPaymentPageUsed страница оплаты Ypmn включена?
     * @throws PaymentException Ошибка оплаты
     */
    public function __construct(
        ?string $paymentMethodType = null,
        bool $isPaymentPageUsed = true,
        ?array $threeDSecure = null
    ) {
        $this->setPaymentMethod($paymentMethodType);
        $this->setUsePaymentPage($isPaymentPageUsed);
    }

    use ProtobufSerializable;

    /** @inheritDoc */
    public function setPaymentMethod(?string $paymentMethod = null) : self
    {
        if (is_string($paymentMethod)) {
            $paymentMethod = strtoupper($paymentMethod);
        }

        switch ($paymentMethod) {
            case PaymentMethods::CCVISAMC:
            case PaymentMethods::FASTER_PAYMENTS:
            case PaymentMethods::INTCARD:
            case PaymentMethods::ALFAPAY:
            case PaymentMethods::TPAY:
            case PaymentMethods::SBERPAY:
            case PaymentMethods::PAYOUT:
            case PaymentMethods::PAYOUT_FP:
            case PaymentMethods::BNPL:
            case null:
                $this->paymentMethod = $paymentMethod;
                break;
            case '':
                $this->paymentMethod = PaymentMethods::CCVISAMC;
                break;
            default:
                throw new PaymentException('Неверный тип оплаты в авторизации');
        }

        return $this;
    }

    /** @inheritDoc */
    public function setUsePaymentPage(?bool $isUsed) : self
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
        if (is_null($this->getCardDetails())) {
            $this->merchantToken = $merchantToken;

            return $this;
        } else {
            throw new PaymentException('For using MerchantToken, make sure CardDetails = NULL');
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

    /** @inheritDoc */
    public function setThreeDSecure(
        int $screenHeight = null,
        int $screenWidth = null,
        int $timezone = null,
        string $userAgent = null,
        string $colorDepth = null,
        string $language = null,
        string $requestIp = null
    ): self
    {
        if (empty($userAgent)) {
            if (!empty($_SERVER['HTTP_USER_AGENT'])) {
                $userAgent = $_SERVER['HTTP_USER_AGENT'];
            } else {
                $userAgent = 'Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/140.0.0.0 Safari\/537.36';
            }
        }

        $this->threeDSecure = [
            'strongCustomerAuthentication' => [
                'clientEnvironment' => [
                    'browser' => [
                        'screenHeight' => (!empty($screenHeight) ? $screenHeight : '1440'),
                        'screenWidth' => (!empty($screenWidth) ? $screenWidth : '2561'),
                        'timezone' => (!empty($timezone) ? $timezone : '-180'),
                        'userAgent' => (!empty($userAgent) ? $userAgent : 'Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/140.0.0.0 Safari\/537.36'),
                        'colorDepth' => (!empty($colorDepth) ? $colorDepth : '24'),
                        'language' => (!empty($language) ? $language : 'ru-RU'),
                        'requestIp' => (filter_var($requestIp, FILTER_VALIDATE_IP) ? $requestIp : Std::get_client_ip()),
                        'acceptHeader' => '*\/*',
                        'javaEnabled' => 'NO',
                    ]
                ]
            ]
        ];

        return $this;
    }

    /** @return array */
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

        if (!is_null($this->threeDSecure)) {
            $resultArray['threeDSecure'] = $this->threeDSecure;
        }

        if (!is_null($this->paymentPageOptions) && $this->paymentPageOptions->getOrderTimeout() > 0) {
            $resultArray['paymentPageOptions'] = $this->paymentPageOptions->toArray();
        }

        return $resultArray;
    }
}
