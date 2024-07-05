<?php

declare(strict_types=1);

use Ypmn\ApiRequest;
use Ypmn\Qst;
use Ypmn\QstSchema;
use Ypmn\QstSchemaActualAddress;
use Ypmn\QstSchemaBankAccount;
use Ypmn\QstSchemaCeo;
use Ypmn\QstSchemaIdentityDoc;
use Ypmn\QstSchemaLegalAddress;
use Ypmn\QstSchemaOwner;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/* Создание и отправка анкеты для подключения продавца */

/* Создаем и заполняем объект анкеты */
$qst = new Qst();

/* ИНН продавца */
$qst->setInn('7750005806');

/* Данные продавца */
$qstSchema = new QstSchema();
$qstSchema->addPhone('+7 495 1234567, доб. 123');
$qstSchema->addPhone('+7 499 7654321, доб. 321');
$qstSchema->addEmail('example@ypmn.com');

$qstLegalAddress = (new QstSchemaLegalAddress())
    ->setZip('123456')
    ->setRegion('Москва')
    ->setCity('Москва')
    ->setStreet('ул. Арбат')
    ->setHouse('10');
$qstSchema->setLegalAddress($qstLegalAddress);

$qstActualAddress = (new QstSchemaActualAddress())->setChecked(true);
$qstSchema->setActualAddress($qstActualAddress);

$qstCeoIdentityDoc = (new QstSchemaIdentityDoc())
    ->setSeries('1234')
    ->setNumber('123456')
    ->setIssueDate('2000-01-30')
    ->setIssuedBy('МВД')
    ->setIssuedByKP('123-456');

$qstCeo = (new QstSchemaCeo())
    ->setIdentityDoc($qstCeoIdentityDoc)
    ->setBirthDate('1990-01-30')
    ->setBirthPlace('Москва')
    ->setRegistrationAddress('г. Москва, ул. Ленина, д. 1, кв. 1');
$qstSchema->setCeo($qstCeo);

$qstOwner = (new QstSchemaOwner())->setOwner('Иванов Иван Иванович')->setShare('100');
$qstSchema->addOwner($qstOwner);

$qstBankAccount = (new QstSchemaBankAccount())
    ->setBankBIK('044525974')
    ->setBankCorAccount('30101810145250000974')
    ->setBankAccount('40817810400002911811');

$qstSchema->addBankAccount($qstBankAccount);

$qstSchema->setAdditionalFieldByKey(1, 'Доп. поле');

$qst->setSchema($qstSchema);

/* Создадим HTTP-запрос к API */
$apiRequest = new ApiRequest($merchant);

// Включить режим отладки (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setLocalMode();

// Отправим запрос //
$responseData = $apiRequest->sendQstCreateRequest($qst);

/* Преобразуем ответ из JSON в массив */
try {
    $responseData = json_decode((string) $responseData["response"], true);
    echo "Анкета #{$responseData['id']} создана и отправлена на проверка";
} catch (Exception $exception) {
    echo "Ошибка запроса: {$exception->getMessage()}";
    throw new Exception($exception->getMessage());
}
