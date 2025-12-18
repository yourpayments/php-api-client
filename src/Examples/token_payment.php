<?php

declare(strict_types=1);

use Ypmn\ApiRequest;
use Ypmn\Capture;

/**
 * В зависимости от настройки мерчанта,
 * Система Your Payments может списывать денежные средства автоматически
 * после или во время авторизации платежа (пример authorize.php),
 *
 * Либо с помощью отдельного запроса, описанного ниже.
 */

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Это файл с формой для тестирования
require_once 'token_payment_form.php';

if (!empty($_POST)) {
    // Создадим такой запрос:
    $capture = (new Capture);

    // Номер транзакции в системе Your Payments
    // (возвращается в ответ на запрос на авторизацию в JSON Response)
    $capture->setYpmnPaymentReference(@$_REQUEST['payuPaymentReference']);

    // Cумма исходной операции на авторизацию
    $capture->setOriginalAmount((float) @$_REQUEST['originalAmount']);
    // Cумма фактического списания (не все методы и банки поддерживают)
    $capture->setAmount((float) @$_REQUEST['amount']);
    // Валюта
    $capture->setCurrency('RUB');

    // Создадим HTTP-запрос к API
    $apiRequest = new ApiRequest($merchant);
    // Режим отладки (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setDebugMode(isset($_REQUEST['debug']));
    // Режим тестового сервер (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setSandboxMode(isset($_REQUEST['sandbox']));

    // Отправим запрос к API
    $responseData = $apiRequest->sendCaptureRequest($capture);
}
