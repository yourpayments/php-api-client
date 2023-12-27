<?php

declare(strict_types=1);

use Ypmn\ApiRequest;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Получение графика отчета

// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();

// Подготовим диапазон дат для отчета
$endDate = (new DateTime('now'))->format("Y-m-d");

$startDate = (new DateTime($endDate))
    ->modify('-14 day')
    ->format("Y-m-d");

// Отправим запрос
$responseData = $apiRequest->sendReportChartRequest([
    'startDate' => $startDate,
    'endDate' => $endDate,
    'periodLength' => 'day'
]);