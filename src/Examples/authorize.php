<?php

declare(strict_types=1);

use Ypmn\CardDetails;
use Ypmn\Details;
use Ypmn\MerchantToken;
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
use Ypmn\SubmerchantReceipt;
use Ypmn\UtmDto;

// Подключим файл, в котором заданы параметры мерчанта
require_once 'start.php';

// Это файл с формой для тестирования
require_once 'authorize_form.php';

if (!empty($_POST)) {
    // Сумма заказа вычисляется путём сложения стоимости "продуктов" (товаров или услуг в заказе)
    // Опишем первую позицию
    $product1 = new Product();
    $product1->setName('Первый тестовый товар'); // Установим Наименование товара или услуги
    $product1->setSku('toy-01'); // Установим Артикул
    $product1->setVat(22); // Установим НДС
    $product1->setUnitPrice(1); // Установим Стоимость за единицу
    $product1->setQuantity(2); // Установим Количество
    $product1->setMeasurementUnit('шт'); // Установим единицы измерения (для чеков)

    // Опишем вторую позицию
    $product2 = new Product();
    $product2->setName('Второй тестовый товар'); // Установим Наименование товара или услуги
    $product2->setSku('toy-02'); // Установим Артикул
    $product2->setVat(22); // Установим НДС
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
    $billing->setPhone('+79670660742'); // Установим Телефон Плательщика
    $billing->setEmail('example1@ypmn.ru'); // Установим Email Плательщика

    // Опишем Доставку и принимающее лицо (необязательно)
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
    $delivery->setPhone('+79670660742'); // Установим Телефон Лица, принимающего заказ
    $delivery->setEmail('example2@ypmn.ru'); // Установим Email Лица, принимающего заказ
    $delivery->setCompanyName('ООО НКО "Твои Платежи"'); // Установим Название Компании, в которой можно оставить заказ

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
    $authorization = new Authorization($payment_method);

    /**
     * // Пример для h2h оплаты картой
     * // (для ТСП, сертифицированных по PCI-DSS)
     *
     * // Пример включает в себя тестовую карту из
     * // https://ypmn.ru/doc/#tag/testing
     *     $authorization->setUsePaymentPage(false)
     *     $authorization->setCardDetails(
     *         (new CardDetails())
     *         ->setNumber('4652035440667037')
     *         ->setExpiryYear((int) date('Y', strtotime('+1 year')))
     *         ->setExpiryMonth(8)
     *         ->setCvv('971')
     *         ->setOwner('CARD OWNER')
     *     );
     *
     * // Также для ускорения платежа
     * // см. метод $authorization->setThreeDSecure()
     */

    // Назначим авторизацию для нашего платежа
    $payment->setAuthorization($authorization);
    // Установим номер заказа (должен быть уникальным в вашей системе)
    $merchantPaymentReference = 'primer_nomer__' . time();
    $payment->setMerchantPaymentReference($merchantPaymentReference);

    // Установим адреса перенаправления пользователя после удачной и неудачной оплаты
    $payment->setSuccessUrl('http://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . '/php-api-client/?function=returnPage&status=success');
    $payment->setFailUrl('http://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . '/php-api-client/?function=returnPage&status=fail');
    /*
     * @deprecated старый вариант с одним URL
     * $payment->setReturnUrl('http://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . '/php-api-client/?function=returnPage');
     */

    // Подготовим клиентское подключение
    $payment->setClient($client);

    // Создадим объект расширенных данных по транзакции
    $details = new Details();
    //Пример хранения маркетинговых меток
    if (!empty(@$_REQUEST['utm_source'])) {
        $details->set('utm', (new UtmDto())->fromArray([
            'utm_source'   => @$_REQUEST['utm_source'],   //'Источник трафика'
            'utm_medium'   => @$_REQUEST['utm_medium'],   //'Тип трафика или рекламный канал'
            'utm_campaign' => @$_REQUEST['utm_campaign'], //'Название рекламной кампании'
            'utm_term'     => @$_REQUEST['utm_term'],     //'Ключевое слово или дополнительный сегментный признак'
            'utm_content'  => @$_REQUEST['utm_content'],  //'Идентификатор конкретного рекламного экземпляра'
        ]));
    }

    // Новая оплата с токенизацией (необязательно), или оплата токеном
    if (@$_REQUEST['section'] === 'new_payment_and_tokenization') {
        // Оплата, токенизация
        // Токенизация (сохранение платёжной информации для повторных оплат)
        if (isset($_REQUEST['tokenization'])) {
            $storedCredentials = new StoredCredentials();
            $storedCredentials->setConsentType(
                @$_REQUEST['consentType']
                    ?? ($payment_method == PaymentMethods::FASTER_PAYMENTS ? 'recurring' : '')
            );
            $storedCredentials->setSubscriptionPurpose(@$_REQUEST['subscriptionPurpose']);
            $payment->setStoredCredentials($storedCredentials);
        }
    } elseif (@$_REQUEST['section'] === 'pay_with_token') {
        // Оплата токеном
        switch ($payment_method) {
            case PaymentMethods::CCVISAMC:
                $storedCredentials = new StoredCredentials();
                $storedCredentials->setUseType('cardholder');
                $authorization->setMerchantToken(new MerchantToken(@$_REQUEST['token_string']));
                $payment->setStoredCredentials($storedCredentials);
                break;
            case PaymentMethods::FASTER_PAYMENTS:
            case PaymentMethods::INTCARD:
            case PaymentMethods::SBERPAY:
            case PaymentMethods::TPAY:
                $storedCredentials = new StoredCredentials();
                $storedCredentials->setUseType('merchant');
                $authorization->setMerchantToken(
                    (new MerchantToken())
                        ->setYpmnBindingId(@$_REQUEST['token_string'])
                );
                $payment->setStoredCredentials($storedCredentials);
                break;
            default:
                throw new PaymentException('Метод пока не поддерживает оплату токеном');
        }
    }

    // Сплитование (разделение платежа между получателями)
    if (isset($_REQUEST['split'])) {
        // Коды сабмерчантов можно получить у менеджера после их подключения
        $product1->setMarketplaceSubmerchantByCode('SUBMERCHANT_1_CODE');
        $product2->setMarketplaceSubmerchantByCode('SUBMERCHANT_2_CODE');
    }

    // Чек внешней кассы
    if (isset($_REQUEST['receipt'])) {
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
    }

    // Добавим расширенные данные по транзакции в запрос на авторизацию платежа
    $payment->setDetails($details);

    // Создадим HTTP-запрос к API
    $apiRequest = new ApiRequest($merchant);
    // Режим отладки (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setDebugMode(@$_POST['debug'] === 'yes');
    // Режим тестового сервер (закомментируйте или удалите в рабочей программе!)
    $apiRequest->setSandboxMode(@$_POST['sandbox'] === 'yes');

    // Отправим запрос
    $responseData = $apiRequest->sendAuthRequest($payment);

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
