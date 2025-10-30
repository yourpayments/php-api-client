<?php

declare(strict_types=1);

use Ypmn\Authorization;
use Ypmn\Delivery;
use Ypmn\IdentityDocument;
use Ypmn\Payment;
use Ypmn\Client;
use Ypmn\Billing;
use Ypmn\ApiRequest;
use Ypmn\PaymentException;
use Ypmn\PaymentMethods;
use Ypmn\PaymentPageOptions;
use Ypmn\Product;
use Ypmn\Std;
use Ypmn\Details;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Оплата по ссылке Ypmn
// Представим, что нам надо оплатить пару позиций: Синий Мяч и Жёлтый Круг

// Опишем первую позицию
$product1 = new Product;
// Установим Наименование (название товара или услуги)
$product1->setName('Синий Мяч');
// Установим Артикул
$product1->setSku('toy-05');
// Установим Стоимость за единицу
$product1->setUnitPrice(10);
// Установим Количество
$product1->setQuantity(1);
// Установим НДС
$product1->setVat(20);

//Опишем вторую позицию с помощью сокращённого синтаксиса:
$product2 = new Product([
    'name'  => 'Жёлтый Круг',
    'sku'  => 'toy-15',
    'unitPrice'  => 2,
    'quantity'  => 3,
    'vat'  => 0,
]);

// Опишем Биллинговую (платёжную) информацию
$billing = new Billing;
// Установим Код страны
$billing->setCountryCode('RU');
// Установим Город
$billing->setCity('Москва');
// Установим Регион
$billing->setState('Центральный регион');
// Установим Адрес Плательщика (первая строка)
$billing->setAddressLine1('Улица Старый Арбат, дом 10');
// Установим Адрес Плательщика (вторая строка)
$billing->setAddressLine2('Офис Ypmn');
// Установим Почтовый Индекс Плательщика
$billing->setZipCode('121000');
// Установим Имя Плательщика
$billing->setFirstName('Иван');
// Установим Фамилия Плательщика
$billing->setLastName('Петров');
// Установим Телефон Плательщика
$billing->setPhone('+79670660742');
// Установим Email Плательщика
$billing->setEmail('test1@ypmn.ru');

// (необязательно) Опишем Доствку и принимающее лицо
$delivery = new Delivery;
// Установим документ, подтверждающий право приёма доставки
$delivery->setIdentityDocument(
    new IdentityDocument(123456, 'PERSONALID')
);

// (необязательно) Опишем поля для чеков
$details = new Details;
$details->setReceipts(<<<DETAILS
[
    {
        "merchantCode": "{$merchant->getCode()}",
        "receipt": {
            "client": {
                "email": "sales@romashka.ru"
            },
            "company": {
                "email": "chek@romashka.ru",
                "sno": "osn",
                "inn": "1234567890",
                "payment_address": "https://v4.online.atol.ru"
            },
            "items": [
                {
                    "name": "колбаса Клинский Брауншвейгская с/к в/с ",
                    "price": 1000.00,
                    "quantity": 0.3,
                    "sum": 300.00,
                    "measurement_unit": "кг",
                    "payment_method": "full_payment",
                    "payment_object": "commodity",
                    "vat": {
                        "type": "vat120"
                    }
                },
                {
                    "name": "яйцо Окское куриное С0 белое",
                    "price": 100.00,
                    "quantity": 1.0,
                    "sum": 100.00,
                    "measurement_unit": "Упаковка 10 шт.",
                    "payment_method": "full_payment",
                    "payment_object": "commodity",
                    "vat": {
                        "type": "vat120"
                    }
                }
            ],
            "payments": [
                {
                    "type": 1,
                    "sum": 400.0
                }
            ],
            "vats": [
                {
                    "type": "vat120"
                },
                {
                    "type": "vat120"
                }
            ],
            "total": 400.0
        }
    }
]
DETAILS);

// Установим Код страны
$delivery->setCountryCode('RU');
// Установим Город
$delivery->setCity('Москва');
// Установим Регион
$delivery->setState('Центральный регион');
// Установим Адрес Лица, принимающего заказ (первая строка)
$delivery->setAddressLine1('Улица Старый Арбат, дом 10');
// Установим Адрес Лица, принимающего заказ (вторая строка)
$delivery->setAddressLine2('Офис Ypmn');
// Установим Почтовый Индекс Лица, принимающего заказ
$delivery->setZipCode('121000');
// Установим Имя Лица, принимающего заказ
$delivery->setFirstName('Мария');
// Установим Фамилия Лица, принимающего заказ
$delivery->setLastName('Петрова');
// Установим Телефон Лица, принимающего заказ
$delivery->setPhone('+79670660743');
// Установим Email Лица, принимающего заказ
$delivery->setEmail('test2@ypmn.ru');
// Установим Название Компании, в которой можно оставить заказ
$delivery->setCompanyName('ООО "Вектор"');

// Создадим клиентское подключение
$client = new Client;
// Установим биллинг
$client->setBilling($billing);
// Установим доставку
$client->setDelivery($delivery);
// Установим IP (автоматически)
$client->setCurrentClientIp();
// И Установим время (автоматически)
$client->setCurrentClientTime();

// Создадим платёж
$payment = new Payment;
// Присвоим товарные позиции
$payment->addProduct($product1);
$payment->addProduct($product2);
// Установим валюту
$payment->setCurrency('RUB');
// Установим дополнительные поля
$payment->setDetails($details);

// Создадим запрос на  авторизацию платежа
// Здесь первым параметром можно передать конкретный способ оплаты из справочника
// PaymentMethods.php
$authorization = new Authorization(PaymentMethods::CCVISAMC);
// Можно установить лимит времени для оплаты заказа (в секундах)
$authorization->setPaymentPageOptions(new PaymentPageOptions(600));
// Назначим авторизацию для нашего платежа
$payment->setAuthorization($authorization);

// Установим номер заказа (должен быть уникальным в вашей системе)
$payment->setMerchantPaymentReference('primer_nomer__' . time());
// Установим адрес перенаправления пользователя после оплаты
$payment->setReturnUrl('https://' . @$_SERVER['HTTP_HOST'] . '/php-api-client/?function=returnPage');
// Установим клиентское подключение
$payment->setClient($client);

// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();
// Отправим запрос
$responseData = $apiRequest->sendAuthRequest($payment);
// Преобразуем ответ из JSON в массив
try {
    $responseData = json_decode((string) $responseData["response"], true);

    // Нарисуем кнопку оплаты
    echo Std::drawYpmnButton([
        'url' => $responseData["paymentResult"]['url'],
        'sum' => $payment->sumProductsAmount(),
    ]);

    // Либо сделаем редирект (перенаправление) браузера по адресу оплаты:
    // echo Std::redirect($responseData["paymentResult"]['url']);
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
