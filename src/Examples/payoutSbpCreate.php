<?php

declare(strict_types=1);

use Ypmn\Amount;
use Ypmn\ApiRequest;
use Ypmn\Billing;
use Ypmn\Merchant;
use Ypmn\Payout;
use Ypmn\PayoutMobileDestination;
use Ypmn\PayoutSource;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/**
 * Это файл с примером для создания выплат по номеру телефона через СБП физ. лицам
 **/

// Созданим выплату
$payout = new Payout();

// Назначим ей уникальный номер выплаты
// (повторно этот номер использовать нельзя,
// даже если выплата неудачная
$payout->setMerchantPayoutReference(
    'payout__'
    . str_replace('.', '', uniqid('' . time(), true))
);

// Назначим сумму (здесь пример передачи данных из формы + стандартное значение)
$payout->setAmount(
    new Amount((float) @$_POST['summ'] ?: 150.00, 'RUB')
);

// Назначим Описание
$payout->setDescription(@$_POST['description'] ?: 'Тестовое Описание Платежа');

// Опишем и назначим Направление и Получателя платежа
$destination = (new PayoutMobileDestination())
    ->setPhoneNumber(@$_POST['ph-number'] ?: "79001112233") // Назначим номер телефона (здесь пример передачи данных из формы + стандартное значение)
    ->setBankInformation((int)$_POST['bank'], $_POST['bankName']); // Установим id/имя банка из списка НСПК
// Имя получателя из GET-запроса
$postRecipientName = explode(' ', @$_POST['reciever-name'] ?: '');

// Опишем получателя
$recipient = (new Billing())
    ->setEmail('support@ypmn.ru') // E-mail получателя
    ->setCity('Москва') // Город получателя
    ->setAddressLine1('Арбат, 10') // Адрес получателя
    ->setZipCode('121000') // Почтовый индекс получателя
    ->setCountryCode('RU') // Код страны получателя (2 буквы, на английском)
    ->setFirstName(@$postRecipientName[0] ?: 'Иван') // Установим Имя получателя для платежа (здесь пример передачи данных из формы + стандартное значение)
    ->setLastName(@$postRecipientName[1] ?: @$postRecipientName[0] ?: 'Иванович'); // Фамилия получателя (здесь пример передачи данных из формы + стандартное значение)

$destination->setRecipient($recipient);
$payout->setDestination($destination);

// Опишем и назначим Источник платежа
$source = new PayoutSource();
// Опишем отправителя
$sender = (new Billing())
    ->setFirstName('Василий') // Имя отправителя
    ->setLastName('Петрович') // Фамилия отправителя
    ->setPhone('0764111111') // Телефон отправителя
    ->setEmail('test@example.ru'); // Email отправителя
$source->setSender($sender);
$payout->setSource($source);

// Создадим HTTP-запрос к API
$apiRequest = (new ApiRequest(new Merchant($_POST['merchantCode'], $_POST['merchantSecret'])))
    ->setDebugMode() // (Опционально) Включить режим отладки (закомментируйте или удалите в рабочей программе!)
    ->setSandboxMode(); // (Опционально) Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!)

// Отправим запрос
$responseData = $apiRequest->sendPayoutCreateRequest($payout);
