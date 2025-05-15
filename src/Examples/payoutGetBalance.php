<?php

declare(strict_types=1);

use Ypmn\ApiRequest;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/** @var \Ypmn\Merchant $merchant */

/*
 * Запрос баланса для выплаты
 */

/* Создадим HTTP-запрос к API */
$apiRequest = new ApiRequest($merchant);

// Включить режим отладки (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setSandboxMode();

// Отправим запрос
$responseData = $apiRequest->sendPayoutGetBalanceRequest([
    'merchantCodes' => 'test1,test2', // Массив кодов отправителя (если у Вас их несколько и необходимо получить отчет только по некоторым из них)
    'minValue' => 0, // Минимальная сумма баланса.
    'maxValue' => 100, // Максимальная сумма баланса.
    'currency' => 'RUB', // Код валюты, в которой выражены цены. Согласно ISO 4217
]);
