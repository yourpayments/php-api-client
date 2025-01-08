<?php

declare(strict_types=1);

use Ypmn\ApiRequest;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'startMarketplace.php';

// Уникальный UUID селлера, информацио о котором хотим получить
$marketplaceSellerId = '30b0db49-5ba3-4358-8cae-9cd29de8b037';

// Отправим запрос
$apiRequest = new ApiRequest($merchant);
$apiRequest->setSandboxMode();
$apiRequest->setDebugMode();

try {
    $session = $apiRequest->sendMarketplaceGetSellerRequest($marketplaceSellerId);

} catch (\Ypmn\PaymentException $e) {
}
