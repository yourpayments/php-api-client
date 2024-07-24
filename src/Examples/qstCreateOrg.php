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
$qst->setInn('7704217370');

/* Данные продавца */
$qstSchema = new QstSchema();
$qstSchema->addPhone('+7 495 1234567, доб. 123');
$qstSchema->addPhone('+7 499 7654321, доб. 321');
$qstSchema->addEmail('example@ypmn.com');

$qstLegalAddress = (new QstSchemaLegalAddress())
    ->setZip('123112')
    ->setRegion('Москва')
    ->setCity('Москва')
    ->setStreet('Пресненская наб.')
    ->setHouse('д. 10')
    ->setFlat('эт. 41, Пом. I, комн. 6');
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
    ->setBirthDate('1980-01-30')
    ->setBirthPlace('Москва')
    ->setRegistrationAddress('г. Москва, ул. Ленина, д. 1, кв. 1');
$qstSchema->setCeo($qstCeo);

$qstOwner = (new QstSchemaOwner())->setOwner('Иванов Иван Иванович')->setShare('100');
$qstSchema->addOwner($qstOwner);

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
