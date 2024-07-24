<?php

declare(strict_types=1);

use Ypmn\ApiRequest;
use Ypmn\PaymentException;
use Ypmn\Std;
use Ypmn\PaymentReference;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Хотим получить токен
// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();
// Отправим запрос
$reference = (isset($_GET['reference']) ? $_GET['reference'] : '2450767');
$ypmnPaymentReference = new PaymentReference($reference);
$responseData = $apiRequest->sendTokenCreationRequest($ypmnPaymentReference);
// Преобразуем ответ из JSON в массив
try {
    $responseData = json_decode((string) $responseData["response"], true);
    
    if (isset($responseData['token'])) {
        echo Std::alert([
            'type' => 'success',
            'text' => '
                Карта успешно токенизирована (токен получен).
                <br>
                <br>Вот он: <code>' . $responseData['token'] . '</code>
                <br>
                <br>Теперь его <a href="./?function=paymentByToken&token=' . $responseData['token'] . '">можно использовать</a> в платежах вместо данных карты
            ',
        ]);
    }

} catch (Exception $exception) {
    //TODO: обработка исключения
    echo Std::alert([
        'text' => '
            Извините, платёжный метод временно недоступен.<br>
            Вы можете попробовать другой способ оплаты, либо свяжитесь с продавцом.<br>
            <br>
            <pre>' . $exception->getMessage() . '</pre>',
        'type' => 'danger',
    ]);

    throw new PaymentException('Платёжный метод временно недоступен');
}
