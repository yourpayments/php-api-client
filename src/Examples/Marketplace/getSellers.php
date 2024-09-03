<?php

declare(strict_types=1);

use Ypmn\ApiRequest;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'startMarketplace.php';

// Отправим запрос
$apiRequest = new ApiRequest($merchant);
$apiRequest->setSandboxMode();
$apiRequest->setDebugMode();

try {
    $session = $apiRequest->sendMarketplaceGetSellersRequest();

} catch (\Ypmn\PaymentException $e) {
}
