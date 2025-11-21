<?php
/**
 * getFasterPayment.php
 */
declare(strict_types=1);

use Ypmn\Product;
use Ypmn\ApiRequest;
use Ypmn\Billing;
use Ypmn\Delivery;
use Ypmn\IdentityDocument;
use Ypmn\Client;
use Ypmn\Payment;
use Ypmn\Authorization;
use Ypmn\PaymentException;
use Ypmn\Std;
use Ypmn\StoredCredentials;
use Ypmn\MerchantToken;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/*
 * Оплата через СБП
 */

/* Опишем первую позицию */
$product1 = new Product();
$product1->setName('Круассаны Цезарь'); // Установим Наименование товара или услуги
$product1->setSku('toy-01'); // Установим Артикул
$product1->setVat(20); // Установим НДС
$product1->setUnitPrice(10); // Установим Стоимость за единицу
$product1->setQuantity(1); // Установим Количество

/* Опишем вторую позицию с помощью сокращённого синтаксиса */
$product2 = new Product([
    'name'  => 'Ассорти рулетиков Нежность',
    'sku'  => 'toy-02',
    'unitPrice'  => 8,
    'quantity'  => 3,
    'vat'  => 10,
]);

/* Опишем третью позицию с помощью JSON */
$product3 = new Product(
    json_decode(
        '{
            "name": "Ассорти мини-десертов",
            "sku": "toy-03",
            "unitPrice": 12,
            "quantity": 2,
            "vat": 0
        }',
        true
    )
);

/* Опишем Биллинговую (платёжную) информацию */
$billing = new Billing;
$billing->setCountryCode('RU'); // Установим Код страны
$billing->setCity('Москва'); // Установим Город
$billing->setState('Центральный регион'); // Установим Регион
$billing->setAddressLine1('Улица Старый Арбат, дом 10'); // Установим Адрес Плательщика (первая строка)
$billing->setAddressLine2('Офис Ypmn'); // Установим Адрес Плательщика (вторая строка)
$billing->setZipCode('121000'); // Установим Почтовый Индекс Плательщика
$billing->setFirstName('Иван'); // Установим Имя Плательщика
$billing->setLastName('Петров'); // Установим Фамилия Плательщика
$billing->setPhone('9670660742'); // Установим Телефон Плательщика
$billing->setEmail('develop@ypmn.ru'); // Установим Email Плательщика

/* Опишем Доствку и принимающее лицо (необязательно) */
$delivery = new Delivery;
// Установим документ, подтверждающий право приёма доставки
$delivery->setIdentityDocument(
    new IdentityDocument(123456, 'PERSONALID')
);
$delivery->setCountryCode('RU'); // Установим Код страны
$delivery->setCity('Москва'); // Установим Город
$delivery->setState('Центральный регион'); // Установим Регион
$delivery->setAddressLine1('Улица Старый Арбат, дом 10'); // Установим Адрес Лица, принимающего заказ (первая строка)
$delivery->setAddressLine2('Офис Ypmn'); // Установим Адрес Лица, принимающего заказ (вторая строка)
$delivery->setZipCode('121000'); // Установим Почтовый Индекс Лица, принимающего заказ
$delivery->setFirstName('Мария'); // Установим Имя Лица, принимающего заказ
$delivery->setLastName('Петрова'); // Установим Фамилия Лица, принимающего заказ
$delivery->setPhone('89670660743'); // Установим Телефон Лица, принимающего заказ
$delivery->setEmail('develop@ypmn.ru'); // Установим Email Лица, принимающего заказ
$delivery->setCompanyName('ООО "Вектор"'); // Установим Название Компании, в которой можно оставить заказ

/* Создадим клиентское подключение */
$client = new Client;
$client->setBilling($billing); // Установим биллинг
$client->setDelivery($delivery); // Установим доставку
$client->setCurrentClientIp(); // Установим IP (автоматически)
$client->setCurrentClientTime(); // И Установим время (автоматически)

/* Создадим платёж */
$payment = new Payment;
$payment->addProduct($product1); // Установим товарную позицию 1
$payment->addProduct($product2); // Установим товарную позицию 2
$payment->addProduct($product3); // Установим товарную позицию 3
$payment->setCurrency('RUB'); // Установим валюту

/* Создадим запись с токеном привязки */
$merchantToken = new MerchantToken();
$merchantToken->setBindingId("ab2bb91a-0012-4129-a2bf-cd18418bc726");

/* Создадим авторизацию по типу платежа */
$authorization = new Authorization('FASTER_PAYMENTS',false);
$authorization->setMerchantToken($merchantToken);

/* Запрашиваем подписку */
$storedCredentials = new StoredCredentials();
$storedCredentials->setUseType("merchant");

// Назначим авторизацию для нашего платежа //
$payment->setAuthorization($authorization);
// Запросим подписку
$payment->setStoredCredentials($storedCredentials);
// Установим номер заказа (должен быть уникальным в вашей системе) //
$payment->setMerchantPaymentReference('primer_nomer__' . time());
// Установим адрес перенаправления пользователя после оплаты //
$payment->setReturnUrl('http://' . $_SERVER['SERVER_NAME'] . '/php-api-client/?function=returnPage');
$payment->setClient($client); // Установим клиентское подключение

/* Создадим HTTP-запрос к API */
$apiRequest = new ApiRequest($merchant);

// Включить режим отладки (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setSandboxMode();

// Отправим запрос //
$responseData = $apiRequest->sendAuthRequest($payment, $merchant);

/* Преобразуем ответ из JSON в массив */
try {
    $responseData = json_decode((string) $responseData["response"], true);

    // Нарисуем кнопку оплаты
    echo Std::drawYpmnButton([
        'url' => $responseData["paymentResult"]['url'] ?? "",
        'sum' => $payment->sumProductsAmount() ?? 0,
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
