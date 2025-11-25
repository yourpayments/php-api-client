<?php

declare(strict_types=1);

use Ypmn\PaymentMethods;
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

// Подключим файл, в котором заданы параметры мерчанта
require_once 'start.php';

// Это файл с формой для тестирования
require_once 'payment_form.php';

if (!empty($_POST)) {
    // Опишем первую товарную позицию
    $product1 = new Product();
    $product1->setName('Первый тестовый товар'); // Установим Наименование товара или услуги
    $product1->setSku('toy-01'); // Установим Артикул
    $product1->setVat(20); // Установим НДС
    $product1->setUnitPrice(1); // Установим Стоимость за единицу
    $product1->setQuantity(2); // Установим Количество
    $product1->setMeasurementUnit('шт'); // Установим единицы измерения (для чеков)

    // Опишем вторую товарную позицию
    $product2 = new Product();
    $product2->setName('Второй тестовый товар'); // Установим Наименование товара или услуги
    $product2->setSku('toy-02'); // Установим Артикул
    $product2->setVat(15); // Установим НДС
    $product2->setUnitPrice(3); // Установим Стоимость за единицу
    $product2->setQuantity(1); // Установим Количество
    $product1->setMeasurementUnit('кг'); // Установим единицы измерения (для чеков)

    // Опишем Биллинговую (платёжную) информацию
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

    // Опишем Доствку и принимающее лицо (необязательно)
    $delivery = new Delivery;

    // Установим документ, подтверждающий право приёма доставки (необязательно)
    $delivery->setIdentityDocument(
        new IdentityDocument(123456, 'Паспорт РФ')
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

    // Создадим клиентское подключение
    $client = new Client;
    $client->setBilling($billing); // Установим биллинг
    $client->setDelivery($delivery); // Установим доставку
    $client->setCurrentClientIp(); // Установим IP (автоматически)
    $client->setCurrentClientTime(); // И Установим время (автоматически)

    // Создадим платёж
    $payment = new Payment;
    $payment->addProduct($product1); // Установим товарную позицию 1
    $payment->addProduct($product2); // Установим товарную позицию 2
    $payment->setCurrency('RUB'); // Установим валюту

    // Создадим авторизацию по типу платежа
    $payment_method = @$_REQUEST['payment_method'] ?? PaymentMethods::CCVISAMC;
    $authorization = new Authorization($_REQUEST['payment_method']);
    // Назначим авторизацию для нашего платежа
    $payment->setAuthorization($authorization);
    // Установим номер заказа (должен быть уникальным в вашей системе)
    $payment->setMerchantPaymentReference('primer_nomer__' . time());

    // Установим адреса перенаправления пользователя после удачной и неудачной оплаты
    $payment->setSuccessUrl('http://' . $_SERVER['SERVER_NAME'] . '/php-api-client/?function=returnPage&status=success');
    $payment->setFailUrl('http://' . $_SERVER['SERVER_NAME'] . '/php-api-client/?function=returnPage&status=fail');
    /* @deprecated старый вариант с одним URL */
    // $payment->setReturnUrl('http://' . $_SERVER['SERVER_NAME'] . '/php-api-client/?function=returnPage');

    // Подготовим клиентское подключение
    $payment->setClient($client);

    // Токенизация (сохранение платёжной информации для повторных оплат)
    if (isset($_REQUEST['tokenization'])) {
        $storedCredentials = new StoredCredentials();
        $storedCredentials->setConsentType('recurring');
        $storedCredentials->setSubscriptionPurpose('Обоснование или тема подписки');
        $payment->setStoredCredentials($storedCredentials);
    }

    // Сплитование (разделение платежа между получателями)
    if (isset($_REQUEST['split'])) {
        $product1->setMarketplaceSubmerchantByCode('SUBMERCHANT_1_CODE');
        $product2->setMarketplaceSubmerchantByCode('SUBMERCHANT_1_CODE');
    }

    // Чек внешней кассы
    if (isset($_REQUEST['receipt'])) {
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

        // Чек для одного юрлица (без разделения платежа между получателями)
        if (!isset($_REQUEST['split'])) {
            foreach ($payment->getProducts() as $product) {
                $receiptsArr['receipt']['items'][] = [
                    'name' => $product->getName(),
                    'price' => $product->getUnitPrice(),
                    'quantity' => $product->getQuantity(),
                    'sum' => $product->getUnitPrice() * $product->getQuantity(),
                    'measurement_unit' => 'кг',
                    'payment_method' => 'full_payment',
                    'payment_object' => 'commodity',
                    'vat' => ['type' => 'vat120'],
                ];
                $receiptsArr['receipt']['vats'][] = ['type' => 'vat120'];
                $receiptsArr['receipt']['payments'][0]['sum'] += $product->getUnitPrice() * $product->getQuantity();
                $receiptsArr['receipt']['total'] += $product->getUnitPrice() * $product->getQuantity();
            }

            // Закодируем данные для онлайн кассы АТОЛ в JSON строку
            $receipts = json_encode($receiptsArr);
        } else {
            // Чек для нескольких юрлиц (с разделением платежа между получателями)
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
        }

        // Установим данные для передачи в АТОЛ для регистрации чеков
        $details->setReceipts($receipts);
        // Добавим расширенные данные по транзакции в запрос на авторизацию платежа
        $payment->setDetails($details);
    }

    // Создадим HTTP-запрос к API
    $apiRequest = new ApiRequest($merchant);
    // Режим отладки (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setDebugMode(isset($_REQUEST['debug']));
    // Режим тестового сервер (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setSandboxMode(isset($_REQUEST['sandbox']));
    // Отправим запрос
    $responseData = $apiRequest->sendAuthRequest($payment, $merchant);

    // Преобразуем ответ из JSON в массив
    try {
        $responseData = json_decode(
            (string)$responseData['response'],
            true,
            16,
            JSON_THROW_ON_ERROR
        );

        if (!empty($responseData['paymentResult']['bankResponseDetails']['customBankNode']['qr'])) {
            $qr = $responseData['paymentResult']['bankResponseDetails']['customBankNode']['qr'];
        }

        // Выведем кнопку оплаты
        echo Std::drawYpmnButton([
            'url' => $responseData['paymentResult']['url'] ?? '',
            'sum' => $payment->sumProductsAmount(),
            'qr' => $qr ?? null,
            'payment_method' => $payment_method,
        ]);

        // Либо сделаем ссылку или редирект по адресу оплаты:
        // echo Std::redirect($responseData["paymentResult"]['url']);
    } catch (Exception $exception) {
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
}
