<?php

declare(strict_types=1);

use Ypmn\Authorization;
use Ypmn\Payment;
use Ypmn\Client;
use Ypmn\Billing;
use Ypmn\ApiRequest;
use Ypmn\PaymentException;
use Ypmn\Product;
use Ypmn\Std;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Оплата по ссылке Ypmn
// Минимальный набор полей

// Представим, что мы не хотим передавать товары, только номер заказа и сумму
// Установим номер (ID) заказа (номер заказа в вашем магазине, должен быть уникален в вашей системе)
$merchantPaymentReference = "order_id_" . time();

$orderAsProduct = new Product([
    'name'  => 'Заказ №' . $merchantPaymentReference,
    'sku'  => $merchantPaymentReference,
    'unitPrice'  => 20.42,
    'quantity'  => 1,
]);

// Опишем Биллинговую (платёжную) информацию
$billing = new Billing;
// Установим Код страны
$billing->setCountryCode('RU');
// Установим Имя Плательщика
$billing->setFirstName('Иван');
// Установим Фамилия Плательщика
$billing->setLastName('Петров');
// Установим Email Плательщика (необязательно)
$billing->setEmail('test1@ypmn.ru');
// Установим Телефон Плательщика
$billing->setPhone('+7-800-555-35-35');
// Установим Город
$billing->setCity('Москва');

// Создадим клиентское подключение
$client = new Client;
// Установим биллинг
$client->setBilling($billing);

// Создадим платёж
$payment = new Payment;
// Присвоим товарные позиции
$payment->addProduct($orderAsProduct);
// Создадим запрос на  авторизацию платежа
// здесь первым параметром можно передать конкретный способ оплаты из справочника
// PaymentMethods.php
$payment->setAuthorization(new Authorization());
// Установим номер заказа (должен быть уникальным в вашей системе)
$payment->setMerchantPaymentReference($merchantPaymentReference);
// Установим адрес перенаправления пользователя после оплаты
$payment->setReturnUrl('https://test.u2go.ru/php-api-client/?function=returnPage');
// Установим клиентское подключение
$payment->setClient($client);

// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();
// Отправим запрос
$responseData = $apiRequest->sendAuthRequest($payment, $merchant);
// Преобразуем ответ из JSON в массив
// TODO: перенести валидацию в функцию ApiClient
try {
    $responseData = json_decode((string) $responseData["response"], true);

    if (isset($responseData['code'])
        && $responseData['code'] === 429
        && $responseData['status'] === 'LIMIT_CALLS_EXCEEDED'
    ) {
        throw new PaymentException('YPMN-002: LIMIT_CALLS_EXCEEDED (превышена частота запросов к серверу)');
    }

    if (isset($responseData["paymentResult"])) {
        // Выведем кнопку оплаты
        echo Std::drawYpmnButton([
            'url' => $responseData["paymentResult"]['url'],
            'sum' => $payment->sumProductsAmount(),
            'newpage' => true,
        ]);

        // .. или сделаем редирект на форму оплаты (опционально)
        // Std::redirect($responseData["paymentResult"]['url']);
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
