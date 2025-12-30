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
use Ypmn\Std;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/* Создание и отправка анкеты для подключения сабмерчанта (продавца) ИП */

/* Создадим объект анкеты */
$qst = new Qst();

/* Укажем ИНН добавляемого сабмерчанта */
$qst->setInn('773200328662');

/* Создадим объект данных анкеты добавляемого сабмерчанта */
$qstSchema = new QstSchema();

/* Добавим в данные анкеты номер телефона сабмерчанта */
$qstSchema->addPhone('+7 495 1234567, доб. 123');

/* Добавим в данные анкеты email сабмерчанта */
$qstSchema->addEmail('example@ypmn.com');

/* Создадим и заполним объект юридического адреса сабмерчанта */
$qstLegalAddress = (new QstSchemaLegalAddress())
    ->setZip('123456') // индекс
    ->setRegion('Москва') // регион
    ->setCity('Москва') // город
    ->setStreet('ул. Охотный ряд') // улица
    ->setHouse('1'); // дом
/* Укажем объект юридического адреса сабмерчанта в данных анкеты */
$qstSchema->setLegalAddress($qstLegalAddress);

/*
 * Создадим объект фактического адреса сабмерчанта и отметим, что
 * фактический адрес сабмерчанта соответствует юридическому
 */
$qstActualAddress = (new QstSchemaActualAddress())->setChecked(true);
/* Укажем объект фактического адреса сабмерчанта в данных анкеты */
$qstSchema->setActualAddress($qstActualAddress);

/*
 * Создадим объект почтового адреса сабмерчанта и отметим, что
 * почтовый адрес сабмерчанта соответствует юридическому
 */
$qstPostAddress = (new QstSchemaPostAddress())->setChecked(true);
/* Укажем объект почтового адреса сабмерчанта в данных анкеты */
$qstSchema->setPostAddress($qstPostAddress);

/* Создадим объект удостоверяющего документа и заполним его паспортными данными ИП */
$qstIdentityDoc = (new QstSchemaIdentityDoc())
    ->setSeries('1234') // номер паспорта
    ->setNumber('123456') // серия
    ->setIssueDate('2000-01-30') // дата выдачи
    ->setIssuedBy('МВД') // кем выдан
    ->setIssuedByKP('123-456'); // к/п

/*
 * Заполним дату и место рождения ИП в данных анкеты.
 * Укажем объект с паспортными данным ИП в данных анкеты.
 */
$qstSchema
    ->setBirthDate('1969-02-23') // дата рождения ИП
    ->setBirthPlace('Москва') // место рождения ИП
    ->setIdentityDoc($qstIdentityDoc); // объект с паспортными данным ИП

/* Создадим и заполним объект с банковскими данными ИП */
$qstBankAccount = (new QstSchemaBankAccount())
    ->setBankBIK('044525700') // БИК
    ->setBankCorAccount('30101810200000000700') // кор. счет
    ->setBankAccount('40702810100002400756'); // расч. счет
/* Добавим объект с банковскими данными сабмерчанта в данные анкеты */
$qstSchema->addBankAccount($qstBankAccount);

/* Заполним дополнительное поле #1 (при наличии) */
$qstSchema->setAdditionalFieldByKey(1, 'Доп. поле');

/* Установим объект с данными анкеты в объект анкеты */
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
        echo Std::alert([
            'type' => 'success',
            'text' => 'Анкета #' . (int) $responseData['id'] . ' создана и отправлена на проверку',
        ]);
    } else {
        echo Std::alert([
            'type' => 'danger',
            'text' => 'Анкета не создана! ' . htmlspecialchars(strip_tags(
                $responseData['code'] . ', '
                . $responseData['status'] . ': '
                . $responseData['message']
            ))
        ]);

        if (strpos($responseData['message'], 'package is not allowed')) {
            echo Std::alert([
                'type' => 'warning',
                'text' => 'Обратитесь к менеджеру для настройки маркетплейса',
            ]);
        }
    }
} catch (Exception $exception) {
    echo "Ошибка запроса: {$exception->getMessage()}";
    throw new Exception($exception->getMessage());
}
