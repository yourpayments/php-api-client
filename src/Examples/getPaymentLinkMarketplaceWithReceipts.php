<?php

/**
 * Этот файл аналогичен getPaymentLink.php за исключением того,
 * что здесь оплата разделяется на несколько мерчантов
 */

declare(strict_types=1);

use Ypmn\Authorization;
use Ypmn\Delivery;
use Ypmn\Details;
use Ypmn\IdentityDocument;
use Ypmn\Payment;
use Ypmn\Client;
use Ypmn\Billing;
use Ypmn\ApiRequest;
use Ypmn\PaymentException;
use Ypmn\Product;
use Ypmn\Std;
use Ypmn\SubmerchantReceipt;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Оплата по ссылке Ypmn
// Представим, что нам надо оплатить пару позиций: Синий Мяч и Жёлтый Круг

// Опишем первую позицию
$product1 = new Product;
// Установим Наименование (название товара или услуги)
$product1->setName('Синий Квадрат');
// Установим Артикул
$product1->setSku('ball-05');
// Установим Стоимость за единицу
$product1->setUnitPrice(500);
// Установим Количество
$product1->setQuantity(1);
// Установим НДС
$product1->setVat(20);
// Установим Код Мерчанта (для маркетплейса)
$product1->setMarketplaceSubmerchantByCode('SUBMERCHANT_1');

//Опишем вторую позицию с помощью сокращённого синтаксиса:
$product2 = new Product([
    'name'  => 'Оранжевый Круг',
    'sku'  => 'toy-15',
    'unitPrice'  => 160000,
    'quantity'  => 3,
    'vat'  => 0,
    'merchantCode'  => 'SUBMERCHANT_2',
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
$billing->setAddressLine1('Офис Ypmn');
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
// Установим Код страны
$delivery->setCountryCode('RU');
// Установим Город
$delivery->setCity('Москва');
// Установим Регион
$delivery->setState('Центральный регион');
// Установим Адрес Лица, принимающего заказ (первая строка)
$delivery->setAddressLine1('Улица Старый Арбат, дом 10');
// Установим Адрес Лица, принимающего заказ (вторая строка)
$delivery->setAddressLine1('Офис Ypmn');
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
// Установим позиции
$payment->addProduct($product1);
$payment->addProduct($product2);
// Установим валюту
$payment->setCurrency('RUB');
// Создадим и установим авторизацию по типу платежа
$payment->setAuthorization(new Authorization('CCVISAMC',true));
// Установим номер заказа (должен быть уникальным в вашей системе)
$payment->setMerchantPaymentReference('primer_nomer__' . time());
// Установим адрес перенаправления пользователя после оплаты
$payment->setReturnUrl('http://' . $_SERVER['SERVER_NAME'] . '/php-api-client/?function=returnPage');
// Установим клиентское подключение
$payment->setClient($client);


// Создадим объект расширенных данных по транзакции
$details = new Details();

// Ниже пример данных для регистрации чека в онлайн кассе АТОЛ
// Подробнее см. в документации АТОЛ
$receiptCommonArr = [
    'external_id' => $payment->getMerchantPaymentReference(),
    'receipt' => [
        'client' => ['email' => $billing->getEmail()],
        'company' => [
            'email' => 'chek@romashka.ru', // Электронная почта отправителя чека
            'sno' => 'osn', // Система налогообложения
            'inn' => '5544332219', // ИНН организации
            'payment_address' => 'https://v4.online.atol.ru' // место расчетов
        ],
        'items' => [],
        'payments' => [
            [
                'type' => 1,
                'sum' => 0,
            ]
        ],
        'vats' => [],
        'total' => 0,
    ],
    'timestamp' => (new DateTime())->format('d.m.Y H:i:s')
];

$receipts = [];

foreach ($payment->getProducts() as $i => $product) {
    $receiptArr = $receiptCommonArr;
    $receiptArr['external_id'] .= '_' . $i;
    $receiptArr['receipt']['items'][] = [
        'name' => $product->getName(),
        'price' => $product->getUnitPrice(),
        'quantity' => $product->getQuantity(),
        'sum' => $product->getUnitPrice() * $product->getQuantity(),
        'measurement_unit' => 'кг',
        'payment_method' => 'full_payment',
        'payment_object' => 'commodity',
        'vat' => ['type' => 'vat120'],
    ];
    $receiptArr['receipt']['vats'][] = ['type' => 'vat120'];
    $receiptArr['receipt']['payments'][0]['sum'] = $product->getUnitPrice() * $product->getQuantity();
    $receiptArr['receipt']['total'] = $product->getUnitPrice() * $product->getQuantity();
    // Закодируем данные для онлайн кассы АТОЛ в JSON строку
    $receipt = json_encode($receiptArr);
    $receipts[] = new SubmerchantReceipt(
        $product->getMarketplaceSubmerchant()->getMerchantCode(),
        $receipt
    );
}


// Установим данные для передачи в АТОЛ для регистрации чеков
//$receipt1 = new \Ypmn\DetailsReceiptsSubmerchantReceipt('Sharkov1', "{\"external_id\":\"qseeDE44d100043sdsea4da4dDs\",\"receipt\": {\"client\": {\"email\":\"cf0d9595-c517-4cb5-932c-7542b6738ebb@emailhook.site\" },\"company\": {\"email\":\"chek@romashka.ru\",\"sno\":\"osn\",\"inn\":\"5544332219\",\"payment_address\":\"https:\/\/v4.online.atol.ru\" },\"items\": [ {\"name\":\"\u043a\u043e\u043b\u0431\u0430\u0441\u0430 \u041a\u043b\u0438\u043d\u0441\u043a\u0438\u0439 \u0411\u0440\u0430\u0443\u043d\u0448\u0432\u0435\u0439\u0433\u0441\u043a\u0430\u044f \u0441\/\u043a \u0432\/\u0441\",\"price\": 1000.00,\"quantity\": 0.3,\"sum\": 300.00,\"measurement_unit\":\"\u043a\u0433\",\"payment_method\":\"full_payment\",\"payment_object\":\"commodity\",\"vat\": {\"type\":\"vat120\" } }, {\"name\":\"\u044f\u0439\u0446\u043e \u041e\u043a\u0441\u043a\u043e\u0435 \u043a\u0443\u0440\u0438\u043d\u043e\u0435 \u04210 \u0431\u0435\u043b\u043e\u0435\",\"price\": 100.00,\"quantity\": 1.0,\"sum\": 100.00,\"measurement_unit\":\"\u0423\u043f\u0430\u043a\u043e\u0432\u043a\u0430 10 \u0448\u0442.\",\"payment_method\":\"full_payment\",\"payment_object\":\"commodity\",\"vat\": {\"type\":\"vat120\" } } ],\"payments\": [ {\"type\": 1,\"sum\": 400.0 } ],\"vats\": [ {\"type\":\"vat120\" }, {\"type\":\"vat120\" } ],\"total\": 400.0 },\"service\": {\"callback_url\":\"https:\/\/webhook.site\/e0dbd724-dffd-4a14-9278-eebb2390ec3a\" },\"timestamp\":\"01.02.2017 13:45:00\" }");
//$receipt2 = new \Ypmn\DetailsReceiptsSubmerchantReceipt('Gubanov2', "{\"external_id\":\"qseeDE44d100044sdsea4da4dDs\",\"receipt\": {\"client\": {\"email\":\"cf0d9595-c517-4cb5-932c-7542b6738ebb@emailhook.site\" },\"company\": {\"email\":\"chek@romashka.ru\",\"sno\":\"osn\",\"inn\":\"5544332219\",\"payment_address\":\"https:\/\/v4.online.atol.ru\" },\"items\": [ {\"name\":\"\u043a\u043e\u043b\u0431\u0430\u0441\u0430 \u041a\u043b\u0438\u043d\u0441\u043a\u0438\u0439 \u0411\u0440\u0430\u0443\u043d\u0448\u0432\u0435\u0439\u0433\u0441\u043a\u0430\u044f \u0441\/\u043a \u0432\/\u0441\",\"price\": 1000.00,\"quantity\": 0.3,\"sum\": 300.00,\"measurement_unit\":\"\u043a\u0433\",\"payment_method\":\"full_payment\",\"payment_object\":\"commodity\",\"vat\": {\"type\":\"vat120\" } }, {\"name\":\"\u044f\u0439\u0446\u043e \u041e\u043a\u0441\u043a\u043e\u0435 \u043a\u0443\u0440\u0438\u043d\u043e\u0435 \u04210 \u0431\u0435\u043b\u043e\u0435\",\"price\": 100.00,\"quantity\": 1.0,\"sum\": 100.00,\"measurement_unit\":\"\u0423\u043f\u0430\u043a\u043e\u0432\u043a\u0430 10 \u0448\u0442.\",\"payment_method\":\"full_payment\",\"payment_object\":\"commodity\",\"vat\": {\"type\":\"vat120\" } } ],\"payments\": [ {\"type\": 1,\"sum\": 400.0 } ],\"vats\": [ {\"type\":\"vat120\" }, {\"type\":\"vat120\" } ],\"total\": 400.0 },\"service\": {\"callback_url\":\"https:\/\/webhook.site\/e0dbd724-dffd-4a14-9278-eebb2390ec3a\" },\"timestamp\":\"01.02.2017 13:45:00\" }");
$details->setReceipts($receipts);
// Добавим расширенные данные по транзакции в запрос на авторизацию платежа
$payment->setDetails($details);

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
