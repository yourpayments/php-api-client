# Инструкции по интеграции библиотеки YourPayments PHP API Client

## Обзор библиотеки

Библиотека `yourpayments/php-api-client` предоставляет готовые PHP классы для интеграции с платежным шлюзом "Твои Платежи". Библиотека поддерживает полный цикл обработки платежей: авторизацию, захват средств, возвраты, выплаты, отчеты и работу с подписками.

### Ключевые возможности
- Оплата российскими и зарубежными картами
- Оплата с разделением платежа на нескольких получателей
- Рекуррентные платежи и подписки
- Токенизация карт
- Выплаты на банковские карты
- Работа с отчётами
- Обработка вебхуков

## Системные требования

- PHP 7.4+ (рекомендуется PHP 8.1+)
- Расширения PHP: `curl`, `json`, `mbstring`
- Composer для установки зависимостей

## Установка

### Через Composer (рекомендуется)
```bash
composer require yourpayments/php-api-client
```

## Базовая конфигурация

### 1. Создание мерчанта
```php
use Ypmn\Merchant;

// Получите код мерчанта и секретный ключ у менеджера YPMN
$merchant = new Merchant('your_merchant_code', 'your_secret_key');
```

### 2. Инициализация API-клиента
```php
use Ypmn\ApiRequest;

$apiRequest = new ApiRequest($merchant);

// Режим отладки (только для разработки)
$apiRequest->setDebugMode(true);

// Тестовый режим (sandbox)
$apiRequest->setSandboxMode(true);

// Ключ идемпотентности (опционально)
$apiRequest->setIdempotencyKey('unique-key-' . time());
```

## Основные классы и интерфейсы

### Главные компоненты
- `ApiRequest` - отправка HTTP-запросов к API
- `Merchant` - данные мерчанта
- `Payment` - представление платежа
- `Client` - информация о клиенте
- `Billing` - биллинговая информация
- `Product` - товарные позиции

### Константы API endpoints
```php
ApiRequest::AUTHORIZE_API = '/api/v4/payments/authorize';
ApiRequest::CAPTURE_API = '/api/v4/payments/capture';
ApiRequest::TOKEN_API = '/api/v4/token';
ApiRequest::REFUND_API = '/api/v4/payments/refund';
ApiRequest::STATUS_API = '/api/v4/payments/status';
ApiRequest::PAYOUT_CREATE_API = '/api/v4/payout';
ApiRequest::REPORTS_ORDERS_API = '/reports/orders';
ApiRequest::SESSION_API = '/api/v4/payments/sessions';
ApiRequest::REPORT_CHART_API = '/api/v4/reports/chart';
ApiRequest::REPORT_GENERAL_API = '/api/v4/reports/general';
ApiRequest::REPORT_ORDERS_API_V4 = '/api/v4/reports/orders';
ApiRequest::REPORT_ORDER_DETAILS_API = '/api/v4/reports/order-details';
ApiRequest::PODELI_MERCHANT_REGISTRATION_API = '/api/v4/registration/merchant/podeli';
ApiRequest::QST_CREATE_API = '/api/v4/qst/create';
ApiRequest::QST_STATUS_API = '/api/v4/qst/status';
ApiRequest::QST_PRINT_API = '/api/v4/qst/print';
ApiRequest::QST_LIST_API = '/api/v4/qst/list';
```
### Константы API endpoints
```php
ApiRequest::HOST = 'https://secure.ypmn.ru'; // боевой сервер
ApiRequest::SANDBOX_HOST = 'https://sandbox.ypmn.ru'; // тестовый сервер
```

## Примеры интеграции

### 1. Платёж
```php
use Ypmn\Merchant;
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

// Создадим объект мерчанта с его кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

// Представим, что нам надо оплатить товары: 1 Синий Мяч и 2 Жёлтых Круга
// Опишем первую позицию
$product1 = new Product;
// Установим Наименование (название товара или услуги)
$product1->setName('Синий Мяч');
// Установим Артикул, например
$product1->setSku('toy-05');
// Установим Стоимость за единицу
$product1->setUnitPrice(10);
// Установим Количество
$product1->setQuantity(1);
// Установим НДС для этого продукта, например:
$product1->setVat(20);

// Опишем вторую позицию
$product2 = new Product;
// Установим Наименование (название товара или услуги)
$product1->setName('Жёлтый Круг');
// Установим Артикул, например
$product1->setSku('toy-15');
// Установим Стоимость за единицу
$product1->setUnitPrice(10);
// Установим Количество
$product1->setQuantity(2);
// Установим НДС для этого продукта, например:
$product1->setVat(20);

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
$authorization->setPaymentPageOptions(new PaymentPageOptions(6000));
// Назначим авторизацию для нашего платежа
$payment->setAuthorization($authorization);

// Установим номер заказа (должен быть уникальным в вашей системе)
$payment->setMerchantPaymentReference('primer_nomer__' . time());
// Установим адрес перенаправления пользователя после оплаты, например (замените на свой адрес)
$payment->setReturnUrl('http://' . $_SERVER['SERVER_NAME'] . '/php-api-client/?function=returnPage');
// Установим клиентское подключение
$payment->setClient($client);

// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);

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
```

### 2. Проверка статуса платежа
```php
use Ypmn\Merchant;
use Ypmn\ApiRequest;

// Создадим объект мерчанта с его кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

try {
    $response = $apiRequest->sendStatusRequest('order_reference');
    $statusData = json_decode($response['response'], true);
    
    $status = $statusData['status']; // PENDING, AUTHORIZED, CAPTURED, etc.
} catch (PaymentException $e) {
    echo $e->getMessage();
}
```

### 3. Списание платежа (для двустадийной оплаты)
```php
use Ypmn\Merchant;
use Ypmn\Capture;
use Ypmn\ApiRequest;
use Ypmn\Capture;

// Создадим объект мерчанта с кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

// В зависимости от настройки мерчанта, Ypmn может списывать денежные средства автоматически,
// Либо с помощью дополнительного запроса, описанного ниже.

// Создадим такой запрос:
$capture = (new Capture);

// Номер платежа Ypmn (возвращается в ответ на запрос на авторизацию в JSON Response)
$capture->setYpmnPaymentReference('2297597');

// Cумма исходной операции на авторизацию
$capture->setOriginalAmount(5300);
// Cумма фактического списания
$capture->setAmount(3700);
// Валюта
$capture->setCurrency('RUB');

// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();
// Отправим запрос к API
$responseData = $apiRequest->sendCaptureRequest($capture, $merchant);
```

### 5. Возврат средств
```php
use Ypmn\Merchant;
use Ypmn\ApiRequest;
use Ypmn\Refund;

// Создадим объект мерчанта с кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

// Создадим запрос на возврат средств
$refund = (new Refund);
// Установим номер платежа Ypmn
$refund->setYpmnPaymentReference("2297597");
// Cумма исходной операции на авторизацию
$refund->setOriginalAmount(3700);
// Cумма фактического списания
$refund->setAmount(3700);
// Установим валюту
$refund->setCurrency('RUB');
// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Отправим запрос к API
$responseData = $apiRequest->sendRefundRequest($refund, $merchant);
```


### 6. Выплаты на карты
```php
use Ypmn\Merchant;
use Ypmn\Amount;
use Ypmn\ApiRequest;
use Ypmn\Billing;
use Ypmn\Payout;
use Ypmn\PayoutDestination;
use Ypmn\PayoutSource;

// Создадим объект мерчанта с кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

// Созданим выплату
$payout = new Payout();

// Назначим ей уникальный номер выплаты
// (повторно этот номер использовать нельзя,
// даже если выплата неудачная
$payout->setMerchantPayoutReference('payout__' . time());

// Назначим сумму (здесь пример передачи данных из формы + стандартное значение)
$payout->setAmount(
    new Amount((float) @$_POST['summ'] ?: 150.00, 'RUB')
);

// Назначим Описание
$payout->setDescription(@$_POST['description'] ?: 'Тестовое Описание Платежа');

// Опишем и назначим Направление и Получателя платежа
$destination = new PayoutDestination();
// Назначим номер карты (здесь пример передачи данных из формы + стандартное значение)
$destination->setCardNumber(@$_POST['cc-number'] ?: "4149605380309302");
// Опишем получателя
$recipient = new Billing();
// E-mail получателя
$recipient->setEmail('support@ypmn.ru');
// Город получателя
$recipient->setCity('Москва');
// Адрес получателя
$recipient->setAddressLine1('Арбат, 10');
// Почтовый индекс получателя
$recipient->setZipCode('121000');
// Код страны получателя (2 буквы, на английском)
$recipient->setCountryCode('RU');

// Имя получателя из POST-запроса
$postRecipientName = explode(' ', @$_POST['reciever-name'] ?: '');
// Установим Имя получателя для платежа (здесь пример передачи данных из формы + стандартное значение)
$recipient->setFirstName(@$postRecipientName[0] ?: 'Иван');
// Фамилия получателя (здесь пример передачи данных из формы + стандартное значение)
$recipient->setLastName(@$postRecipientName[1] ?: @$postRecipientName[0] ?: 'Иванович');
$destination->setRecipient($recipient);
$payout->setDestination($destination);

// Опишем и назначим Источник платежа
$source = new PayoutSource();
// Опишем отправителя
$sender = new Billing();
// Имя отправителя
$sender->setFirstName('Василий');
// Фамилия отправителя
$sender->setLastName('Петрович');
// Телефон отправителя
$sender->setPhone('0764111111');
// Email отправителя
$sender->setEmail('test@example.ru');;
$source->setSender($sender);
$payout->setSource($source);

// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();
// Отправим запрос
$responseData = $apiRequest->sendPayoutCreateRequest($payout);
```

### 7. Получение статуса заказа
```php
use Ypmn\Merchant;
use Ypmn\ApiRequest;

// Создадим объект мерчанта с кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

// Уникальный номер заказа
$merchantPaymentReference = 'primer_nomer__184';
// Создадим HTTP-запрос к API
$apiRequest = new ApiRequest($merchant);
// Включить режим отладки (закомментируйте или удалите в рабочей программе!)
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)
$apiRequest->setSandboxMode();
// Отправим запрос к API
$responseData = $apiRequest->sendStatusRequest($merchantPaymentReference);
```

### 8. Обработка вебхуков
```php
use Ypmn\Merchant;
use Ypmn\Webhook;

// Создадим объект мерчанта с кодом и секретным ключом
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');

// Получение данных вебхука
$webhookData = file_get_contents('php://input');
$webhook = new Webhook();

// Валидация подписи
if ($webhook->validateSignature($webhookData, $merchant->getSecret())) {
    $data = json_decode($webhookData, true);
    
    switch ($data['eventType']) {
        case 'PAYMENT_CAPTURED':
            // Обработка успешного платежа
            break;
        case 'PAYMENT_FAILED':
            // Обработка неуспешного платежа
            break;
    }
}
```

## Методы оплаты

### Поддерживаемые способы оплаты
```php
use Ypmn\PaymentMethods;

// Банковские карты РФ
PaymentMethods::CCVISAMC

// Банковские карты вне РФ
PaymentMethods::INTCARD

// СБП (Система быстрых платежей)  
PaymentMethods::FASTER_PAYMENTS

// BNPL (рассрочка)
PaymentMethods::BNPL

// ALFA PAY (Альфа Пей)
PaymentMethods::ALFAPAY

// SberPay
PaymentMethods::SBER_PAY

// MirPpay
PaymentMethods::MIRPAY

// T-PAY
PaymentMethods::TPAY

// Выплата по номеру карты
PaymentMethods::PAYOUT


// Выплата по номеру телефона
PaymentMethods::PAYOUT_FP
```


## Обработка ошибок

### Исключения
Библиотека выбрасывает исключения типа `Ypmn\PaymentException`:

```php
try {
    $response = $apiRequest->sendAuthRequest($payment);
} catch (PaymentException $e) {
    // Логирование ошибки
    error_log($e->getMessage());
    
    // Отображение пользователю
    echo "Произошла ошибка при обработке платежа";
}
```

### Коды ошибок
| Код ответа | Значение | Описание |

      | ---------- | -------- | -------- |

      | GW_ERROR_GENERIC | An error occurred during processing. Please retry the
      operation | При обработке произошла ошибка. Пожалуйста, повторите операцию
      |

      | GW_ERROR_GENERIC_3D | An error occurred during 3DS processing |
      Произошла ошибка при обработке 3DS |

      | GWERROR_-19 | Authentication failed | Ошибка аутентификации |

      | GWERROR_-18 | Error in CVC2 or CVC2 Description fields | Ошибка в CVC2
      или его описании |

      | GWERROR_-10 | Error in amount field | Ошибка в сумме операции
      (недопустимо значение 0, может быть отказ со стороны эмитента по
      транзакциям меньше 1 руб) |

      | GWERROR_-9 | Error in card expiration date field | Ошибка в поле срока
      действия карты |

      | GWERROR_-8 | Invalid card number | Номер карты не корректен |

      | GWERROR_-3 | Call acquirer support call number | Позвоните в службу
      поддержки эквайера (отказ без указания причины, комментарий может дать
      только банк, обслуживающий операцию |

      | GWERROR_-2 | An error occurred during processing. Please retry the
      operation | Ошибка обработки. Пожалуйста, повторите операцию |

      | GWERROR_01 | Card type not active or incorrect PIN | Карта не
      поддерживается или неверный PIN-код |

      | GWERROR_02 | Refer to card issuer, special condition | Обратитесь к
      эмитенту карты, отказ с по особым условиям |

      | GWERROR_03 | Invalid merchant | Неверно указан продавец |

      | GWERROR_04 | Restricted card | Функционал карты ограничен эмитентом |

      | GWERROR_05 | Authorization declined | В авторизации отказано |

      | GWERROR_06 | Error - retry | Ошибка. Повторите попытку |

      | GWERROR_07 | Password incorrect or card disabled | Неверный пароль или
      карта отключена |

      | GWERROR_08 | Invalid amount | Недопустимая сумма |

      | GWERROR_12 | Amount exceeds card ceiling | Данная сумма превышает
      допустимую и не может быть проведена с данной карты |

      | GWERROR_13 | Invalid amount | Недопустимая сумма |

      | GWERROR_14 | No such card | Нет такой карты |

      | GWERROR_15 | No such card/issuer | Нет такой карты/эмитента |

      | GWERROR_17 | Customer cancellation | Отменено плательщиком |

      | GWERROR_19 | Re-enter transaction | Повторно ввести транзакцию |

      | GWERROR_20 | Invalid response | Неверный ответ банка |

      | GWERROR_22 | Suspected Malfunction | Подозрение на неисправность |

      | GWERROR_30 | Format error | Ошибка формата |

      | GWERROR_34 | Credit card number failed the fraud | Система защиты от
      мошенничества блокировала операцию по данной карте |

      | GWERROR_36 | Credit restricted | Кредит ограничен |

      | GWERROR_41 | Lost card | Карта утеряна |

      | GWERROR_43 | Stolen card, pick up | Украдена карта |

      | GWERROR_51 | Insufficient funds | Недостаточно средств |

      | GWERROR_54 | Expired card | Срок действия карты истек |

      | GWERROR_55 | Incorrect PIN | Неверный PIN-код |

      | GWERROR_57 | Transaction not permitted on card | Транзакция по карте не
      разрешена |

      | GWERROR_58 | Not permitted to merchant | Не разрешено торговцу |

      | GWERROR_59 | Suspected fraud | Подозрение на мошенничество |

      | GWERROR_61 | Exceeds amount limit | Превышает лимит суммы |

      | GWERROR_62 | Restricted card | Ограничение по карте |

      | GWERROR_63 | Security violation | Нарушение правил безопасности |

      | GWERROR_65 | Exceeds frequency limit | Превышен предел частоты платежей
      с данной карты |

      | GWERROR_68 | Response received too late | Ответ получен слишком поздно,
      время ожидания ответа истекло |

      | GWERROR_75 | PIN tries exceeded | Количество попыток ввести ПИН-код
      превышено |

      | GWERROR_76 | Wrong pin, tries exceeded | Неверный пин-код, количество
      попыток превышено |

      | GWERROR_78 | Reserved | В случае, когда контракт эмитента и держателя
      карты расторгнут или приостановлен. |

      | GWERROR_82 | Time-out at issuer | Тайм-аут со стороны эмитента карты |

      | GWERROR_83 | Unable to verify PIN | Не удалось подтвердить PIN-код |

      | GWERROR_84 | Invalid cvv | Неверный CVV |

      | GWERROR_89 | Authentication failure | Ошибка аутентификации |

      | GWERROR_91 | A technical problem occurred. Issuer cannot process |
      Техническая ошибка. Эмитент не может обработать операцию |

      | GWERROR_93 | Violation of law | Нарушение законодательства (в некоторых
      странах законодательно запрещено платить локальными картами в зарубежных
      магазинах) |

      | GWERROR_94 | Duplicate transmission | Отказ в проведении операции по
      причине срабатывания контроля дубликатов |

      | GWERROR_95 | Reconcile error | Ошибка реконсиляции (сверки) на стороне
      банка-эквайера или банка-эмитента |

      | GWERROR_96 | System malfunction | Неисправность системы |

      | GWERROR_98 | Error during canceling transaction | Ошибка при отмене
      транзакции |

      | GWERROR_99 | Incorrect card brand | Неверный бренд карты |

      | GWERROR_102 | Acquirer timeout | Тайм-аут на стороне банка-эквайера |

      | GWERROR_105 | 3DS authentication error | Ошибка аутентификации 3DS |

      | GWERROR_107 | Sorry, at the moment the transaction cannot be processed
      due to excessive retries with this card. Please try using another card | К
      сожалению, в настоящее время транзакция не может быть обработана из-за
      большого количества повторных попыток оплаты этой картой. Попробуйте
      использовать другую карту |

      | GWERROR_108 | Sorry, at the moment the transaction cannot be processed.
      Please try using another card | К сожалению, в данный момент транзакция не
      может быть обработана. Попробуйте использовать другую карту |

      | GWERROR_109 | Inactive card, please activate the card first | Неактивная
      карта, сначала активируйте карту |

      | GWERROR_2304 | There is an ongoing process your order | Ваш заказ
      находится в обработке (блокирует повторную операцию с теми же данными) |

      | GWERROR_5007 | Debit cards only supports 3DS operations | Данная
      дебетовая карта поддерживает только 3DS-операции |

      | ALREADY_AUTHORIZED | Re-enter transaction | Операция уже авторизована, в
      повторной попытке нет необходимости |

      | -9999 | Banned operation | Отказ системы противодействия мошенничеству.
      Операция заблокирована |

      | YPMN-001 |  | Отсутствует код мерчанта |

      | YPMN-002 |  | Превышена частота запросов (429) |

      | LIMIT_CALLS_EXCEEDED |  | Превышен лимит вызовов API |

## Настройки безопасности

### Валидация вебхуков
```php
$signature = $_SERVER['HTTP_X_HEADER_SIGNATURE'] ?? '';
$isValid = hash_hmac('sha256', $webhookData, $merchant->getSecret()) === $signature;
```

### IP-адреса серверов
Убедитесь, что в файрволе разрешены соединения с:
- `secure.ypmn.ru` (продакшн)
- `sandbox.ypmn.ru` (тестовый режим)

## Настройка окружений

### Продакшн
```php
$apiRequest = new ApiRequest($merchant);
// Не используйте setDebugMode() и setSandboxMode() в продакшне
```

### Тестирование
```php
$apiRequest = new ApiRequest($merchant);
$apiRequest->setDebugMode(true);      // Отладочная информация
$apiRequest->setSandboxMode(true);    // Тестовый сервер
```

## Полезные утилиты

### Класс Std
```php
use Ypmn\Std;

// Удаление null значений
$cleanArray = Std::removeNullValues($array);

// Создание кнопки оплаты
echo Std::drawYpmnButton([
    'url' => $paymentUrl,
    'sum' => 1000.00,
    'newpage' => true
]);

// Уведомления
echo Std::alert([
    'text' => 'Платеж успешно обработан',
    'type' => 'success'
]);
```

## Рекомендации

1. **Всегда используйте HTTPS** для обработки платежных данных
2. **Логируйте все операции** для отладки и аудита
3. **Проверяйте подписи вебхуков** для безопасности
4. **Используйте идемпотентность** для критических операций
5. **Тестируйте интеграцию** в sandbox перед продакшном
6. **Обновляйте библиотеку** регулярно для получения исправлений

## Поддержка

- Email: itsupport@ypmn.ru
- Документация API: https://ypmn.ru/ru/documentation/
- GitHub Issues: https://github.com/yourpayments/php-api-client/issues
- Тестовые карты: https://ypmn.ru/ru/test-cards/

## Лицензия

MIT License - см. файл LICENSE в репозитории.

# Полное описание API
https://secure.ypmn.ru/docs/docs.yml
