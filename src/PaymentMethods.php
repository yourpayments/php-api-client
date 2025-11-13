<?php

declare(strict_types=1);

namespace Ypmn;

use Ypmn\Traits\ProtobufSerializable;

/**
 * Это класс-справочник платёжных методов
 **/
class PaymentMethods
{
    public const CCVISAMC = 'CCVISAMC';
    public const FASTER_PAYMENTS = 'FASTER_PAYMENTS';
    public const PAYOUT = 'PAYOUT';
    public const PAYOUT_FP = 'PAYOUT_FP';
    public const BNPL = 'BNPL';
    public const INTCARD = 'INTCARD';
    public const ALFAPAY = 'ALFAPAY';
    public const SBERPAY = 'SBERPAY';
    public const TPAY = 'TPAY';

    public const NAMES = [
        'CCVISAMC' => 'Оплата картой (РФ)',
        'INTCARD' => 'Оплата иностранной картой',
        'FASTER_PAYMENTS' => 'СБП',
        'BNPL' => 'BNPL «ПОДЕЛИ»',
        'ALFAPAY' => 'Alfa Pay',
        'SBERPAY' => 'SberPay',
        'TPAY' => 'T-Pay',

        'PAYOUT' => 'Выплата по номеру карты',
        'PAYOUT_FP' => 'Выплата по номеру телефона',
    ];
}
