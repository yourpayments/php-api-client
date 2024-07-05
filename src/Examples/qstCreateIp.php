<?php

declare(strict_types=1);

use Ypmn\ApiRequest;
use Ypmn\Qst;
use Ypmn\QstSchema;
use Ypmn\QstSchemaActualAddress;
use Ypmn\QstSchemaBankAccount;
use Ypmn\QstSchemaIdentityDoc;
use Ypmn\QstSchemaLegalAddress;
use Ypmn\QstSchemaPostAddress;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/* Создание и отправка анкеты для подключения продавца */

/* Создаем и заполняем объект анкеты */
$qst = new Qst();

/* ИНН продавца */
$qst->setInn('773200328662');

/* Данные продавца */
$qstSchema = new QstSchema();
$qstSchema->addPhone('+7 495 1234567, доб. 123');
$qstSchema->addEmail('example@ypmn.com');

$qstLegalAddress = (new QstSchemaLegalAddress())
    ->setZip('123456')
    ->setRegion('Москва')
    ->setCity('Москва')
    ->setStreet('ул. Охотный ряд')
    ->setHouse('1');
$qstSchema->setLegalAddress($qstLegalAddress);

$qstActualAddress = (new QstSchemaActualAddress())->setChecked(true);
$qstSchema->setActualAddress($qstActualAddress);

$qstPostAddress = (new QstSchemaPostAddress())->setChecked(true);
$qstSchema->setPostAddress($qstPostAddress);

$qstIdentityDoc = (new QstSchemaIdentityDoc())
    ->setSeries('1234')
    ->setNumber('123456')
    ->setIssueDate('2000-01-30')
    ->setIssuedBy('МВД')
    ->setIssuedByKP('123-456');

$qstSchema
    ->setBirthDate('1969-02-23')
    ->setBirthPlace('Москва')
    ->setIdentityDoc($qstIdentityDoc);

$qstBankAccount = (new QstSchemaBankAccount())
    ->setBankBIK('044525700')
    ->setBankCorAccount('30101810200000000700')
    ->setBankAccount('40702810100002400756');

$qstSchema->addBankAccount($qstBankAccount);

$qstSchema->setAdditionalFieldByKey(1, 'Доп. поле');

$qst->setSchema($qstSchema);

/* Создадим HTTP-запрос к API */
$apiRequest = new ApiRequest($merchant);

// Включить режим отладки (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setDebugMode();
// Переключиться на тестовый сервер (закомментируйте или удалите в рабочей программе!) //
$apiRequest->setSandboxMode();

/* Запрос на отправку анкеты */
$responseData = $apiRequest->sendQstCreateRequest($qst);

/* Преобразуем ответ из JSON в массив */
try {
    $responseData = json_decode((string) $responseData["response"], true);
    if (isset($responseData['id'])) {
        echo "Анкета #{$responseData['id']} создана и отправлена на проверку";
    } else {
        echo "Анкета не создана, см. причину в ответа от сервера YPMN";
    }
} catch (Exception $exception) {
    echo "Ошибка запроса: {$exception->getMessage()}";
    throw new Exception($exception->getMessage());
}
