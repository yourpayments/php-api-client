<?php

declare(strict_types=1);

use Ypmn\ApiRequest;
use Ypmn\Std;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/*
 * Запрос баланса для выплат
 */

/* Создадим HTTP-запрос к API */
$apiRequest = new ApiRequest($merchant);

// Включить режим отладки (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setSandboxMode();

// Отправим запрос
$responseData = $apiRequest->sendPayoutGetBalanceRequest([
    'merchantCodes' => 'gitttest', // Массив кодов отправителя (если у Вас их несколько и необходимо получить отчет только по некоторым из них)
//    'merchantCodes' => 'test1,test2', // Массив кодов отправителя (если у Вас их несколько и необходимо получить отчет только по некоторым из них)
//    'minValue' => 0, // Минимальная сумма баланса.
//    'maxValue' => 100, // Максимальная сумма баланса.
//    'currency' => 'RUB', // Код валюты, в котороxй выражены цены. Согласно ISO 4217
])['response'];
$responseData = json_decode($responseData, true);

if ($responseData['code'] === 200) {
    foreach($responseData['items'] as $item) {
        echo Std::alert([
            'type'  =>  'info',
            'text'  =>  'Баланс выплат ' . $item['merchantCode'] . ': ' . $item['value'] . ' ' . $item['currency'],
        ]);
    }
} else {
    echo Std::alert([
        'type'  =>  'warning',
        'text'  =>  'Баланс недоступен. Убедитесь в правильности ключа и секрета, а также уточните, включены ли у вас выплаты',
    ]);
}
