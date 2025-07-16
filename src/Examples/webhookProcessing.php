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

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

use Ypmn\Webhook;

// Обрабатываем только POST запросы
if (empty($_POST)) {
    die();
}

// Создадим обработчик вебхука
$webhookHandler = new Webhook();

// Обработаем вебхук
try {
    $webhookHandler->catchJsonRequest();

    $paymentResult = $webhookHandler->getPaymentResult();
    $orderData = $webhookHandler->getOrderData();
    $authorization = $webhookHandler->getAuthorization();


    // Обозначим путь до папки и запишем вебхук в лог
    $folder = __DIR__ . "/../../webhooks_log/";
    $filename = $folder . "ypmn" . date("Y-m-d H:i:s") ."_" . uniqid() . ".log";

    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    file_put_contents($filename, json_encode([
        "orderData" => [
            "orderDate" => $orderData->getOrderDate(),
            "payuPaymentReference" => $orderData->getPayuPaymentReference(),
            "merchantPaymentReference" => $orderData->getMerchantPaymentReference(),
            "status" => $orderData->getStatus(),
            "currency" => $orderData->getCurrency(),
            "amount" => $orderData->getAmount() ?? "null",
            "commission" => $orderData->getCommission() ?? "null",
            "loyaltyPointsAmount" => $orderData->getLoyaltyPointsAmount() ?? "null",
            "loyaltyPointsDetails" => $orderData->getLoyaltyPointsDetails() ?? "null",
        ],

        "paymentResult" => [
            "cardDetails" => [
                "bin" => $paymentResult->getCardDetails()->getBin(),
                "owner" => $paymentResult->getCardDetails()->getOwner(),
                "pan" => $paymentResult->getCardDetails()->getPan(),
                "type" => $paymentResult->getCardDetails()->getType(),
                "cardIssuerBank" => $paymentResult->getCardDetails()->getCardIssuerBank(),
            ],
            "paymentMethod" => $paymentResult->getPaymentMethod(),
            "paymentDate" => $paymentResult->getPaymentDate(),
            "authCode" => $paymentResult->getAuthCode(),
            "merchantId" => $paymentResult->getMerchantId(),
        ],
    ]));


} catch (\Ypmn\PaymentException $e) {
    // YourLogger::log(...);
}

