<?php declare(strict_types=1);

namespace Ypmn\Payout;

use Ypmn\PaymentException;

class PayoutWebhook implements PayoutWebhookInterface
{
    /** @var PayoutPayoutOrderData Информация о заказе */
    private PayoutPayoutOrderData $payoutOrderData;

    /** @var PayoutPaymentResult Результат выплаты */
    private PayoutPaymentResult $payoutPaymentResult;

    /** @var PayoutRecipientInterface Информация о получателе */
    private PayoutRecipientInterface $payoutRecipient;

    /** @inheritDoc */
    public function catchJsonRequest(): self
    {
        try {
            $request = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $exception) {
            throw new PaymentException('Не удалось преобразовать ответ от платёжной системы.
            Проверьте настройку веб-сервера.');
        }

        $this->payoutOrderData = new PayoutPayoutOrderData();
        $this->payoutOrderData
            ->setOrderDate($request['orderData']['orderDate'])
            ->setPayuPayoutReference($request['orderData']['payuPayoutReference'])
            ->setMerchantPayoutReference($request['orderData']['merchantPayoutReference'])
            ->setStatus($request['orderData']['status'])
            ->setCurrency($request['orderData']['currency'])
            ->setAmount((float)$request['orderData']['amount'])
            ->setMessage($request['orderData']['message']);

        $commission = new PayoutCommission();
        $commission
            ->setMerchant((float)$request['paymentResult']['commission']['merchant'])
            ->setPayee((float)$request['paymentResult']['commission']['payee']);

        $this->payoutPaymentResult = new PayoutPaymentResult();
        $this->payoutPaymentResult
            ->setPaymentMethod($request['paymentResult']['paymentMethod'])
            ->setPaymentDate($request['paymentResult']['paymentDate'])
            ->setAuthCode((string) $request['paymentResult']['authCode'])
            ->setRrn((string) $request['paymentResult']['rrn'])
            ->setCommission($commission);

        $this->payoutRecipient = new PayoutRecipient();
        $this->payoutRecipient
            ->setCity($request['recipient']['city'])
            ->setAddress($request['recipient']['address'])
            ->setPostalCode($request['recipient']['postalCode'])
            ->setCountryCode($request['recipient']['countryCode'])
            ->setClientName($request['recipient']['clientName']);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOrderData(): PayoutOrderDataInterface
    {
        return $this->payoutOrderData;
    }

    /**
     * @inheritDoc
     */
    public function setOrderData(PayoutOrderDataInterface $payoutOrderData): PayoutWebhookInterface
    {
        $this->payoutOrderData = $payoutOrderData;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPaymentResults(): PayoutPaymentResultInterface
    {
        return $this->payoutPaymentResult;
    }

    /**
     * @inheritDoc
     */
    public function setPaymentResults(PayoutPaymentResultInterface $payoutPaymentResult): PayoutWebhookInterface
    {
        $this->payoutPaymentResult = $payoutPaymentResult;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRecipient(): PayoutRecipientInterface
    {
        return $this->payoutRecipient;
    }

    /**
     * @inheritDoc
     */
    public function setRecipient(PayoutRecipientInterface $payoutRecipient): PayoutWebhookInterface
    {
        $this->payoutRecipient = $payoutRecipient;

        return $this;
    }
}