# «Твои Платежи»: Интеграция на PHP
Готовая библиотека PHP API Client для YourPayments + примеры с комментариями

![](https://repository-images.githubusercontent.com/638835276/2067d028-b541-4355-b069-3c12c8a28042)

## Оглавление
- [Описание](#описание)
- [Требования](#требования)
- [Установка](#установка)
  - [Запуск встроенного сервера](#запуск-встроенного-сервера)
  - [Запуск в контейнере docker](#запуск-в-контейнере-docker)
- [Примеры использования](#примеры-использования)
  - [Начало работы: настройка интеграции](#1-начало-работы-настройка-интеграции)
  - [Приём платежей](#2-приём-платежей)
  - [Подписки](#3-подписки) (рекуррентные платежи)
  - [Токенизация](#4-токенизация) (запомнить данные плательщика, чтобы не запрашивать и не вводить их повторно)
  - [Отчёты и статусы платежей](#5-отчёты)
  - [Возврат средств плательщику](#6-возврат-средств-плательщику-refund) (refunds, рефанды)
  - [Выплаты](#7-выплаты) (отправка денег по номеру карты или телефона)
  - [Подключение продавцов](#8-подключение-продавцов)
  - [Обработка вебхуков](#9-обработка-вебхуков)
  - [Страница после оплаты](#10-страница-после-оплаты)
  - [Безопасные поля](#11-безопасные-поля-secure-fields) (отдельный вид интеграции карточной формы)
  - [Обработка ошибок](#12-обработка-ошибок)
- [Обновление библиотеки](#обновление)
- [Поддержка и контакты](#ссылки-поддержка-и-контакты)

## Описание
`yourpayments/php-api-client` — это PHP библиотека для быстрой и удобной интеграции с платежным шлюзом YourPayments. 
С её помощью можно принимать оплаты и создавать выплаты, получать  отчёты, делать возвраты и работать с подпискам.

Библиотека ориентирована на простое и надёжное использование, подходит как для опытных, так и для начинающих разработчиков.

Пример быстрого старта для приёма платежей:
```php
$merchant = new Merchant('MERCHANT_CODE', 'SECRET_KEY'); // Коды подключения API
$merchantPaymentReference = 123; // Номер заказа в вашей системе
$billing = (new Billing)
  ->setCountryCode('RU') // Страна Плательщика
  ->setFirstName('Иван') // Имя Плательщика
  ->setLastName('Петров') // Фамилия Плательщика
  ->setEmail('test1@ypmn.ru') // Почта Плательщика
  ->setPhone('+74996492009') // Телефон Плательщика
  ->setCity('Москва');  // Город Плательщика
  
$client = (new Client)->setBilling($billing);
$payment = (new Payment)
  ->addProduct(new Product([
    'name'  => 'Заказ №' . $merchantPaymentReference, // Наименование товарной позиции
    'sku'  => 'test_artikul', // Артикул
    'unitPrice'  => 20.42, // Стоимость единицы
    'quantity'  => 1, // Количество
]));
$payment_method = $_GET['method'] ?? PaymentMethods::CCVISAMC; // Определим платёжный метод
$authorization = new Authorization($payment_method, true);
$payment->setAuthorization($authorization);
$payment->setMerchantPaymentReference($merchantPaymentReference);
$payment->setSuccessUrl('https://' . $_SERVER['HTTP_HOST'] . '/?status=success'); // Редирект после успешной оплаты
$payment->setFailUrl('https://' . $_SERVER['HTTP_HOST'] . '/?status=success'); // Редирект в случае неоплаты
$payment->setClient($client);

$apiRequest = new ApiRequest($merchant);
$responseData = $apiRequest->sendAuthRequest($payment, $merchant);
$responseData = json_decode((string) $responseData["response"], true); // Отправка запроса и обработка ответа
if (isset($responseData["paymentResult"])) {
    if (!empty($responseData['paymentResult']['bankResponseDetails']['customBankNode']['qr'])) {
        $qr = $responseData['paymentResult']['bankResponseDetails']['customBankNode']['qr'];
    }

    // Выведем кнопку оплаты (рекомендуется)
    echo Std::drawYpmnButton([
        'qr' => ($qr ?? null),
        'url' => $responseData['paymentResult']['url'] ?? '',
        'sum' => $payment->sumProductsAmount() ?? 0,
        'payment_method' => $payment_method ?? null,
        'newpage' => true,
    ]);

    // .. или сделаем редирект на форму оплаты (опционально)
    // Std::redirect($responseData["paymentResult"]['url']);
}
```

Особенностями системы являются:
- мульти-эквайринг (работа сразу со многими банками, переключение в случае недоступности)
- поддержка сплитования (разделение одного платежа на несколько получателей платежа, в рамках одного чеке)
- безопасность и точность расчётов

Библиотека содержит:
- Клиент для работы с API платежей, выплат, отчётов
- Простой встроенный сервер с примерами
- Описание контейнера для запуска в Docker

---

## Требования

- PHP 7.4 и выше (рекомендуется PHP 8.1+)
- Расширения PHP: `curl`, `json`, `mbstring`
- Рекомендуется: Composer для управления зависимостями

---

## Установка
Установка с [пакета composer](https://packagist.org/packages/yourpayments/php-api-client) -- самый простой и рекомендуемый способ:

```shell
composer require yourpayments/php-api-client
```

Если на вашем проекте нет Composer, 
склонируйте или скачайте, а затем подключите файлы этого репозитория, 
([пример](src/Examples/autoload.php)) 

### Запуск встроенного сервера
```shell
php -S localhost:8080 index.php
```

После запуска по адресу http://localhost:8080 будут доступны интерактивные примеры в следующем виде:
![скриншот встроенного сервера с примерами](/assets/img/screenshot2.jpg)


### Запуск в контейнере docker
Создайте и запустите docker контейнер следующей командой:
```shell
docker compose up
```

Либо в фоновом режиме командой:
```shell
docker compose up --detach
```
После выполнения сервис с документацией и примерами будет доступен по адресу http://localhost:8080/

---
 
## Примеры использования:
##### 1. [Начало работы: настройка интеграции](src/Examples/start.php)

##### 2. Приём платежей
1. [Платёж, токенизация, чеки](src/Examples/authorize.php)
1. [Минимальная установка](src/Examples/minimal.php)
1. [Списание средств (только для двустадийной оплаты)](src/Examples/paymentCapture.php) 

##### 3. Подписки
Рекуррентные платежи
1. [Создание подписки СБП](src/Examples/getBindingFasterPayment.php)
1. [Оплата по подписке СБП](src/Examples/paymentByFasterBinding.php)
1. [Создание подписки SberPay, T-Pay, Картой не РФ](src/Examples/getBindingPays.php)
1. [Оплата по подписке SberPay, T-Pay, Картой не РФ](src/Examples/paymentByBindingPays.php)
   
##### 4. Токенизация
Запомнить данные клиента, чтобы не запрашивать и не вводить их повторно
1. [Создание платёжного токена ](src/Examples/getToken.php)
2. [Оплата токеном](src/Examples/paymentByToken.php)
  
##### 5. Отчёты
1. [Проверка статуса платежа](src/Examples/paymentGetStatus.php)
2. [Запрос детального отчета по заказу](src/Examples/getReportOrderDetails.php)
3. [Запрос быстрого отчёта по заказам для сверки](src/Examples/getReportOrder.php)
4. [Запрос отчёта по заказам](src/Examples/getReportGeneral.php)
5. [Запрос отчёта в виде графика](src/Examples/getReportChart.php)

##### 6. Возврат средств плательщику (Refund)
1. [Возврат средств](src/Examples/paymentRefund.php)
2. [Возврат средств со сплитом (разделением платежа)](src/Examples/paymentRefundMarketplace.php)

##### 7. Выплаты
1. [Выплаты на банковские карты](src/Examples/payout.php)
2. [Запрос баланса для выплаты](src/Examples/payoutGetBalance.php)

##### 8. Подключение продавцов
Добавление сабмерчантов маркетплейсов по API
1. [Подключение продавца-юридического лица (отправка анкеты)](src/Examples/qstCreateOrg.php)
2. [Подключение продавца-ИП (отправка анкеты)](src/Examples/qstCreateIp.php)
3. [Получение статуса анкеты](src/Examples/qstStatus.php)
4. [Печать анкеты](src/Examples/qstPrint.php)
5. [Список анкет](src/Examples/qstList.php)

##### 9. [Обработка вебхуков](src/Examples/webhookProcessing.php)
Вебхуки -- HTTP запросы, оповещающие ваш сервер о событиях (успешные и неуспешные оплаты, списания) 

##### 10. [Страница после оплаты](src/Examples/returnPage.php)

##### 11. [Безопасные поля (Secure fields)](src/Examples/secureFields.php)
2. [Создание сессии](src/Examples/getSession.php)
3. [Оплата одноразовым токеном](src/Examples/oneTimeTokenPayment.php)

##### 12. Обработка ошибок
Библиотека выбрасывает один вид исключений: [Ypmn\PaymentException](/src/PaymentException.php).

Пример перехвата исключения можно посмотреть в примере: [Cамый простой платёж](src/Examples/simpleGetPaymentLink.php)

---

## Обновление

Обновления библиотеки позволяют быстро исправлять ошибки и получать доступ к новым функциям

```shell
composer update yourpayments/php-api-client
```

---

## Ссылки, поддержка и контакты
- [НКО «Твои Платежи»](https://YPMN.ru/?utm_source=php-api-client)
- [Докуметация API](https://ypmn.ru/doc/?utm_source=php-api-client)
- [Тестовые банковские карты](https://ypmn.ru/doc/?utm_source=php-api-client#tag/testing)
- [FAQ, ответы на частые вопросы](https://ypmn.ru/ru/support/?utm_source=php-api-client)
- [Задать вопрос или сообщить о проблеме](https://github.com/yourpayments/php-api-client/issues/new)

---
🟢 [«Твои Платежи»](https://YPMN.ru/ "Платёжная система для сайтов, платформ и приложений") -- финтех для сайтов, платформ и приложений
