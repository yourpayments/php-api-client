<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Класс для хранения номера транзакции на стороне YPMN
 */
class PaymentReference implements \JsonSerializable
{
    private ?string $paymentReference = null;
    private bool $autoGenerate = true;

    /** Protobuf generation Trait */
    use ProtobufSerializable;

    /**
     * @param null $paymentReference номер транзакции
     * @param bool $autoGenerate генерировать номер автоматически
     * @throws PaymentException
     */
    public function __construct($paymentReference = null, bool $autoGenerate = true)
    {
        $this->autoGenerate = $autoGenerate;
        $this->setPaymentReference($paymentReference);
    }

    /**
     * Установить уникальный номер заказа
     * @param $paymentReference
     * @return self
     * @throws PaymentException
     */
    private function setPaymentReference($paymentReference): self
    {
        if (empty($paymentReference)) {
            if($this->autoGenerate) {
                $paymentReference = 'Заказ__' . uniqid() . '__' . time();
            } else {
                throw new PaymentException('YPMN-003 Передайтие корректный уникальный номер заказа в вашей системе');
            }
        }

        $this->paymentReference = (string) $paymentReference;

        return $this;
    }

    /**
     * @throws PaymentException
     */
    public function jsonSerialize(): string
    {
        if(is_null($this->paymentReference)){
            throw new PaymentException("Не хватает номера оплаты для токенизации");
        }

        $resultArray = [
            'payuPaymentReference'  => $this->paymentReference,
        ];

        return json_encode($resultArray, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_LINE_TERMINATORS);
    }
}
