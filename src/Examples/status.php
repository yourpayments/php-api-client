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
    // Включить режим отладки (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setDebugMode();
    // Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setSandboxMode();
    // Отправим запрос к API
    $responseData = $apiRequest->sendStatusRequest($merchantPaymentReference);
}
