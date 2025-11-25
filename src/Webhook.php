<?php

declare(strict_types=1);

namespace Ypmn;

/**
 * Принятие информации о запросе на стороне мерчанта
 * https://secure.ypmn.ru/docs/#tag/Webhooks/paths/~1merchant-ipn-url/post
 */
class Webhook implements WebhookInterface
{
    /** @var PaymentResultInterface Результат Платежа */
    private PaymentResultInterface $paymentResult;

    /** @var OrderDataInterface Информация о Заказе */
    private OrderDataInterface $orderData;

    /** @var WebhookAuthorizationInterface Информация об авторизации */
    private WebhookAuthorizationInterface $authorization;

    /** @inheritDoc */
    public function catchJsonRequest(): self
    {
        try {
            $request = json_decode(file_get_contents('php://input'), true);
        } catch (\Exception $exception) {
            throw new PaymentException('Не удалось преобразовать ответ от платёжной системы.
            Проверьте настройку веб-сервера.');
        }

        $this->orderData = new OrderData;

        if (!empty($request['orderData']['orderDate'])) {
            $this->orderData->setOrderDate($request['orderData']['orderDate']);
        }

        if (!empty($request['orderData']['payuPaymentReference'])) {
            $this->orderData->setPayUPaymentReference($request['orderData']['payuPaymentReference']);
        }

        if (!empty($request['orderData']['merchantPaymentReference'])) {
            $this->orderData->setMerchantPaymentReference($request['orderData']['merchantPaymentReference']);
        }

        if (!empty($request['orderData']['status'])) {
            $this->orderData->setStatus($request['orderData']['status']);
        }

        if (!empty($request['orderData']['currency'])) {
            $this->orderData->setCurrency($request['orderData']['currency']);
        }

        if (!empty($request['orderData']['amount'])) {
            $this->orderData->setAmount((float) $request['orderData']['amount']);
        }

        if (isset($request['orderData']['commission'])) {
            $this->orderData->setCommission((float) $request['orderData']['commission']);
        }

        if (isset($request['orderData']['loyaltyPointsAmount'])) {
            $this->orderData->setLoyaltyPointsAmount((int) $request['orderData']['loyaltyPointsAmount']);
        }

        if (!empty($request['orderData']['loyaltyPointsDetails'])) {
            $this->orderData->setLoyaltyPointsDetails((array) $request['orderData']['loyaltyPointsDetails']);
        }

        if (!empty($request['paymentResult']['cardDetails'])) {
            $cardDetails = new CardDetails;

            if (!empty($request['paymentResult']['cardDetails']['bin'])) {
                $cardDetails->setBin((int) $request['paymentResult']['cardDetails']['bin']);
            }

            if (!empty($request['paymentResult']['cardDetails']['owner'])) {
                $cardDetails->setOwner($request['paymentResult']['cardDetails']['owner']);
            }

            if (!empty($request['paymentResult']['cardDetails']['pan'])) {
                $cardDetails->setPan($request['paymentResult']['cardDetails']['pan']);
            }

            if (!empty($request['paymentResult']['cardDetails']['type'])) {
                $cardDetails->setType($request['paymentResult']['cardDetails']['type']);
            }

            if (!empty($request['paymentResult']['cardDetails']['cardIssuerBank'])) {
                $cardDetails->setCardIssuerBank($request['paymentResult']['cardDetails']['cardIssuerBank']);
            }

            $this->paymentResult = new PaymentResult;
            $this->paymentResult->setCardDetails($cardDetails);

            if (!empty($request['paymentResult']['paymentMethod'])) {
                $this->paymentResult->setPaymentMethod($request['paymentResult']['paymentMethod']);
            }

            if (!empty($request['paymentResult']['paymentDate'])) {
                $this->paymentResult->setPaymentDate($request['paymentResult']['paymentDate']);
            }

            if (isset($request['paymentResult']['authCode'])) {
                $this->paymentResult->setAuthCode((string) $request['paymentResult']['authCode']);
            }

            if (!empty($request['paymentResult']['merchantId'])) {
                $this->paymentResult->setMerchantId($request['paymentResult']['merchantId']);
            }

            if (!empty($request['paymentResult']['captureDate'])) {
                $this->paymentResult->setCaptureDate($request['paymentResult']['captureDate']);
            }

            if (isset($request['paymentResult']['rrn'])) {
                $this->paymentResult->setRrn((int) $request['paymentResult']['rrn']);
            }

            if (!empty($request['paymentResult']['cardProgramName'])) {
                $this->paymentResult->setCardProgramName($request['paymentResult']['cardProgramName']);
            }

            if (isset($request['paymentResult']['installmentsNumber'])) {
                $this->paymentResult->setInstallmentsNumber($request['paymentResult']['installmentsNumber']);
            }
        }

        if (!empty($request['client']) && count($request['client']) > 0) {
            $billing = new Billing;

            if (!empty($request['client']['billing']['firstName'])) {
                $billing->setFirstName($request['client']['billing']['firstName']);
            }

            if (!empty($request['client']['billing']['lastName'])) {
                $billing->setLastName($request['client']['billing']['lastName']);
            }

            if (!empty($request['client']['billing']['email'])) {
                $billing->setEmail($request['client']['billing']['email']);
            }

            if (!empty($request['client']['billing']['phone'])) {
                $billing->setPhone($request['client']['billing']['phone']);
            }

            if (!empty($request['client']['billing']['countryCode'])) {
                $billing->setCountryCode($request['client']['billing']['countryCode']);
            }

            if (!empty($request['client']['billing']['city'])) {
                $billing->setCity($request['client']['billing']['city']);
            }

            if (!empty($request['client']['billing']['state'])) {
                $billing->setState($request['client']['billing']['state']);
            }

            if (!empty($request['client']['billing']['companyName'])) {
                $billing->setCompanyName($request['client']['billing']['companyName']);
            }

            if (!empty($request['client']['billing']['taxId'])) {
                $billing->setTaxId($request['client']['billing']['taxId']);
            }

            if (!empty($request['client']['billing']['addressLine1'])) {
                $billing->setAddressLine1($request['client']['billing']['addressLine1']);
            }

            if (!empty($request['client']['billing']['addressLine2'])) {
                $billing->setAddressLine2($request['client']['billing']['addressLine2']);
            }

            if (!empty($request['client']['billing']['zipCode'])) {
                $billing->setZipCode($request['client']['billing']['zipCode']);
            }

            if (!empty($request['client']['billing']['identityDocument']) && count($request['client']['billing']['identityDocument']) > 0) {
                $identityDocument = new IdentityDocument(
                    (int) $request['client']['billing']['identityDocument']['number'],
                    $request['client']['billing']['identityDocument']['type']
                );
                $billing->setIdentityDocument($identityDocument);
            }

            $delivery = new Delivery;

            $client = new Client;
            $client->setBilling($billing);
            $client->setDelivery($delivery);
        }

        if (!empty($request['authorization']['storedCredentials'])) {
            $storedCredentialsArray = $request['authorization']['storedCredentials'];

            $storedCredentials = new WebhookStoredCredentials;

            if (!empty($storedCredentialsArray['ypmnBindingId'])) {
                $storedCredentials->setYpmnBindingId($storedCredentialsArray['ypmnBindingId']);
            }

            if (!empty($storedCredentialsArray['useId'])) {
                $storedCredentials->setUseId($storedCredentialsArray['useId']);
            }

            $this->authorization = new WebhookAuthorization;
            $this->authorization->setStoredCredentials($storedCredentials);
        }

        return $this;
    }

    /** @inheritDoc */
    public function getPaymentResult(): PaymentResultInterface
    {
        return $this->paymentResult ?? new PaymentResult();
    }

    /** @inheritDoc */
    public function setPaymentResult(PaymentResultInterface $paymentResult): self
    {
        $this->paymentResult = $paymentResult;
        return $this;
    }

    /** @inheritDoc */
    public function getOrderData(): OrderDataInterface
    {
        return $this->orderData ?? new OrderData();
    }

    /** @inheritDoc */
    public function setOrderData(OrderDataInterface $orderData): self
    {
        $this->orderData = $orderData;
        return $this;
    }

    /** @inheritDoc */
    public function setAuthorization(WebhookAuthorizationInterface $authorization): self
    {
        $this->authorization = $authorization;
        return $this;
    }

    /** @inheritDoc */
    public function getAuthorization(): WebhookAuthorizationInterface
    {
        return $this->authorization ?? new WebhookAuthorization();
    }
}
