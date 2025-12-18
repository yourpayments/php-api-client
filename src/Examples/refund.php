<?php

declare(strict_types=1);

use Ypmn\ApiRequest;
use Ypmn\Refund;

/**
 * Пример запроса на возврат
 * Либо с помощью отдельного запроса, описанного ниже.
 */

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Это файл с формой для тестирования
require_once 'refund_form.php';

if (!empty($_POST)) {
    // Создадим запрос на возврат средств
    $refund = (new Refund);
    // Установим номер платежа Ypmn
    $refund->setYpmnPaymentReference(@$_POST['payuPaymentReference']);
    // Cумма исходной операции
    $refund->setOriginalAmount(@$_POST['originalAmount']);
    // Cумма возврата
    $refund->setAmount(@$_POST['amount']);

    /**
     * Возврат с разделением по сабмерчантам
     *
     * // Добавим Сабмерчантов
     * $refund->addMarketPlaceSubmerchant('SUBMERCHANT_1', 3000);
     * $refund->addMarketPlaceSubmerchant('SUBMERCHANT_2', 700);
     */

    // Установим валюту
    $refund->setCurrency('RUB');
    // Создадим HTTP-запрос к API
    $apiRequest = new ApiRequest($merchant);
    // Режим отладки (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setDebugMode(isset($_REQUEST['debug']));
    // Режим тестового сервер (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setSandboxMode(isset($_REQUEST['sandbox']));
    // Отправим запрос к API
    $responseData = $apiRequest->sendRefundRequest($refund, $merchant);
}
