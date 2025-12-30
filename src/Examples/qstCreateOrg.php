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
use Ypmn\Std;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

/* Создание и отправка анкеты для подключения сабмерчанта (продавца) организации */

/* Создадим объект анкеты */
$qst = new Qst();

/* Укажем ИНН добавляемого сабмерчанта */
$qst->setInn('7704217370');

/* Создадим объект данных анкеты добавляемого сабмерчанта */
$qstSchema = new QstSchema();

/* Добавим в данные анкеты номер телефона сабмерчанта */
$qstSchema->addPhone('+7 495 1234567, доб. 123');
/* Добавим в данные анкеты еще один номер телефона сабмерчанта */
$qstSchema->addPhone('+7 499 7654321, доб. 321');

/* Добавим в данные анкеты email сабмерчанта */
$qstSchema->addEmail('example@ypmn.com');

/* Создадим и заполним объект юридического адреса сабмерчанта */
$qstLegalAddress = (new QstSchemaLegalAddress())
    ->setZip('123112') // индекс
    ->setRegion('Москва') // регион
    ->setCity('Москва') // город
    ->setStreet('Пресненская наб.') // улица
    ->setHouse('д. 10') // дом
    ->setFlat('эт. 41, Пом. I, комн. 6'); // офис
/* Установим объект юридического адреса сабмерчанта в данных анкеты */
$qstSchema->setLegalAddress($qstLegalAddress);

/*
 * Создадим объект фактического адреса сабмерчанта и отметим, что
 * фактический адрес сабмерчанта соответствует юридическому
 */
$qstActualAddress = (new QstSchemaActualAddress())->setChecked(true);
/* Установим объект фактического адреса сабмерчанта в данных анкеты */
$qstSchema->setActualAddress($qstActualAddress);


/* Создадим объект удостоверяющего документа и заполним его паспортными данными руководителя организации */
$qstCeoIdentityDoc = (new QstSchemaIdentityDoc())
    ->setSeries('1234') // номер паспорта
    ->setNumber('123456') // серия
    ->setIssueDate('2000-01-30') // дата выдачи
    ->setIssuedBy('МВД') // кем выдан
    ->setIssuedByKP('123-456'); // к/п

/*
 * Создадим объект руководителя организации.
 * Установим в него объект с паспортными данными руководителя.
 * Заполним место и дату рождения, адрес регистрации руководителя
 */
$qstCeo = (new QstSchemaCeo())
    ->setIdentityDoc($qstCeoIdentityDoc) // объект с паспортными данными руководителя
    ->setBirthDate('1980-01-30') // дата рождения руководителя
    ->setBirthPlace('Москва') // место рождения руководителя
    ->setRegistrationAddress('г. Москва, ул. Ленина, д. 1, кв. 1'); // адрес регистрации руководителя

/* Установим объект с данными руководителя организации в данных анкеты */
$qstSchema->setCeo($qstCeo);

/* Создадим объект собственника организации, заполним ФИО и долю собственника */
$qstOwner = (new QstSchemaOwner())->setOwner('Иванов Иван Иванович')->setShare('100');
/* Добавим объект с данными собственника организации в объект данных анкеты */
$qstSchema->addOwner($qstOwner);

/* Создадим и заполним объект с банковскими данными организации */
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
