<?php

declare(strict_types=1);

use Ypmn\ApiRequest;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Это файл с формой для тестирования
require_once 'status_form.php';

if (!empty($_POST)) {
    // Номер заказа
    $merchantPaymentReference = $_POST['merchantPaymentReference'];
    // Создадим HTTP-запрос к API
    $apiRequest = new ApiRequest($merchant);
    // Режим отладки (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setDebugMode(@$_POST['debug'] === 'yes');
    // Режим тестового сервер (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setSandboxMode(@$_POST['sandbox'] === 'yes');
    // Отправим запрос к API
    $responseData = $apiRequest->sendStatusRequest($merchantPaymentReference);
}
