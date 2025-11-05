<?php declare(strict_types=1);

namespace Ypmn;

/**
 * Это класс-справочник платёжных методов
 **/
class PaymentMethods
{
    public const CCVISAMC = 'CCVISAMC'; // Карта
    public const FASTER_PAYMENTS = 'FASTER_PAYMENTS'; // СБП
    public const PAYOUT = 'PAYOUT'; // Выплата по номеру карты
    public const PAYOUT_FP = 'PAYOUT_FP'; // Выплата по номеру телефона
    public const BNPL = 'BNPL'; // Рассрочка
    public const INTCARD = 'INTCARD'; // Иностранная карта

    public const ALFAPAY = 'ALFAPAY';
    public const SBERPAY = 'SBERPAY';
    public const TPAY = 'TPAY'; // T-Pay
}
