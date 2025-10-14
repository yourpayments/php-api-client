<?php declare(strict_types=1);

use Ypmn\Amount;
use Ypmn\ApiRequest;
use Ypmn\Billing;
use Ypmn\Payout;
use Ypmn\PayoutDestination;
use Ypmn\PayoutSource;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/**
 * Это файл с примером для создания выплат на карту физ. лицам
 **/

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
