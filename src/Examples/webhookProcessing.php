<?php

/**
 * webhookProcessing.php
 *
 * На адрес этого файла будет приходить вебхук IPN
 * Чтобы изменить адрес, отредактируйте параметр URL на странице
 * https://secure.ypmn.ru/cpanel/ipn_settings.php
 *
 */

declare(strict_types=1);

use Ypmn\Webhook;

// Создадим обработчик вебхука
$webhookHandler = new Webhook();


// Обработаем вебхук
try {
    $webhookHandler->catchJsonRequest();

    $paymentResult = $webhookHandler->getPaymentResult();
    $orderData = $webhookHandler->getOrderData();
    $authorization = $webhookHandler->getAuthorization();

} catch (\Ypmn\PaymentException $e) {
    // YourLogger::log(...);
}

