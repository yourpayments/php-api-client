<?php

declare(strict_types=1);

$examples = [
    'start' => [
        'name'  => 'Начало работы',
        'about'  => '
                Первый шаг интеграции с YPMN API &ndash; это получение кода мерчанта и секретного ключа после <a href="https://ypmn.ru/ru/connect/?utm_source=header_btn_1">подключения</a> (спросите у Вашего менеджера). 
                <br>
                <br>Они нужны для отправки всех запросов к API.
                <br>
                <br>На стороне клиента они используются для создания объекта Merchant (смотрите <a href="https://github.com/yourpayments/php-api-client/blob/main/src/Examples/start.php">файл с примером</a>).
                
        ',
        'docLink'  => '',
        'link'  => '',
    ],
    'simpleGetPaymentLink' => [
        'name'  => 'Самая простая кнопка оплаты',
        'about'  => 'В этом примере показана самая простая реализация. С минимальным набором полей без детализации, просто оплата заказа c определённой суммой.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1authorize/post',
        'link'  => '',
    ],
    'getPaymentLink' => [
        'name'  => 'Подробный платёж',
        'about'  => 'Это пример платежа с максимальным набором полей.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1authorize/post',
        'link'  => '',
    ],
    'getFasterPayment' => [
        'name'  => 'Оплата через СБП',
        'about'  => 'Пример платежа через систему быстрых платежей',
        'docLink'  => 'https://ypmn.ru/ru/documentation/',
        'link'  => '',
    ],
    'getFasterPaymentWithReceipts' => [
        'name'  => 'Оплата через СБП с регистрацией чека',
        'about'  => 'Пример платежа через систему быстрых платежей с регистрацией чека',
        'docLink'  => 'https://ypmn.ru/ru/documentation/',
        'link'  => '',
    ],
    'getBindingFasterPayment' => [
        'name'  => 'Создание подписки СБП',
        'about'  => 'В этом примере отправляется запрос на создание подписки СБП с одновременной оплатой',
        'docLink'  => 'https://ypmn.ru/ru/documentation/',
        'link'  => '',
    ],
    'paymentByFasterBinding' => [
        'name'  => 'Оплата по подписке СБП',
        'about'  => 'Это пример демонстрирует оплату через СБП по средством ранее созщданной подписки',
        'docLink'  => 'https://ypmn.ru/ru/documentation/',
        'link'  => '',
    ],
    'getBindingPays' => [
        'name'  => 'Создание токена SberPay',
        'about'  => 'В этом примере отправляется запрос на создание токена SberPay с одновременной оплатой',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1authorize/post',
        'link'  => '',
    ],
    'paymentByBindingPays' => [
        'name'  => 'Оплата по токену SberPay',
        'about'  => 'Это пример демонстрирует оплату через SberPay по средством ранее созданного токена',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1authorize/post',
        'link'  => '',
    ],
    'getPaymentLinkMarketplace' => [
        'name'  => 'Платёж со сплитом',
        'about'  => 'Это пример платежа со сплитом (разделением оплаты на несколько плательщиков).',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-split-api',
        'link'  => '',
    ],
    'getPaymentLinkMarketplaceWithReceipts' => [
        'name'  => 'Платёж со сплитом и регистрацией чеков',
        'about'  => 'Это пример платежа со сплитом (разделением оплаты на несколько плательщиков) и регистрацией чеков.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-split-api',
        'link'  => '',
    ],
    'getToken' => [
        'name'  => 'Создание токена',
        'about'  => 'Приложение передаёт номер успешно оплаченного заказа в YPMN API, и получает в ответ платёжный токен.<br><br>Это называется "Токенизация карты" (чтобы запомнить карту клиента и не вводить повторно.<br><br>Очень полезная функция для подписок и регулярных платежей.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/token-api/paths/~1v4~1token/post',
        'link'  => '',
    ],
    'paymentByToken' => [
        'name'  => 'Оплата токеном',
        'about'  => 'Оплата с помощью токена (теперь не нужно повторно вводить данные банковской карты)',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1authorize/post',
        'link'  => '',
    ],
    'paymentCapture' => [
        'name'  => 'Списание средств',
        'about'  => 'Списание ранее заблокированной на счету суммы. Не обязательно, если у Вас настроена оплата в 1 шаг.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1capture/post',
        'link'  => '',
    ],
    'paymentRefund' => [
        'name'  => 'Возврат средств',
        'about'  => 'Запрос на полный или частичный возврат средств.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1refund/post',
        'link'  => '',
    ],
    'paymentRefundMarketplace' => [
        'name'  => 'Возврат средств со сплитом',
        'about'  => 'Запрос на полный или частичный возврат средств с разделением на несколько получателей.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1refund/post',
        'link'  => '',
    ],
    'paymentGetStatus' => [
        'name'  => 'Проверка статуса платежа',
        'about'  => 'Запрос к YPMN API о состоянии платежа.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1status~1%7BmerchantPaymentReference%7D/get',
        'link'  => '',
    ],
    'payoutCreate' => [
        'name'  => 'Создание выплаты',
        'about'  => 'Запрос к YPMN для совершения выплаты на карту (для компаний, сертифицированных по PCI-DSS). У вас должно быть достаточно средств на специальном счету для выплат.<br><br>Тестовая карта (для выплат на тестовом контуре): 4149605380309302',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payouts-api',
        'link'  => '',
    ],
    'payoutGetBalance' => [
        'name'  => 'Получение баланса для выплаты',
        'about'  => 'Запрос к YPMN для проверки баланса на вылпату',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payouts-api/paths/~1v4~1payout~1balance/get',
        'link'  => '',
    ],
    'getSession' => [
        'name'  => 'Создание сессии',
        'about'  => 'Создание уникальной сессии YPMN',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/sessions/paths/~1v4~1payments~1sessions/post',
        'link'  => '',
    ],
    'oneTimeTokenPayment' => [
        'name'  => 'Оплата одноразовым токеном',
        'about'  => 'Оплата одноразовым токеном',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api/paths/~1v4~1payments~1authorize/post',
        'link'  => '',
    ],
    'returnPage' => [
        'name'  => 'Страница после оплаты',
        'about'  => 'Это пример страницы, на которую плательщик возвращается после совершения платежа.',
        'docLink'  => '',
        'link'  => '',
    ],
    'secureFields' => [
        'name'  => 'Безопасные поля (Secure fields)',
        'about'  => 'Это пример формы оплаты с использованием Secure Fields.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/secure-fields',
        'link'  => '',
    ],
    'getReportGeneral' => [
        'name'  => 'Запрос отчёта в формате JSON',
        'about'  => 'Это пример получения отчета в формате JSON.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/reports/paths/~1v4~1reports~1general/get',
        'link'  => '',
    ],
    'getReportChart' => [
        'name'  => 'Запрос отчёта в виде графика',
        'about'  => 'Это пример получения отчета в виде графика.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/reports/paths/~1v4~1reports~1chart/get',
        'link'  => '',
    ],
    'SOMGetPaymentLink' => [
        'name'  => 'Оплата зарубежными картами',
        'about'  => 'В этом примере показана простая реализация оплаты заказа зарубежной картой.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/payment-api',
        'link'  => '',
    ],
    'qstCreateOrg' => [
        'name'  => 'Подключение продавца-организации (отправка анкеты)',
        'about'  => 'В этом примере показана реализация отправки анкеты подключаемого продавца-организации на проверку в YPMN.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/qst-api/paths/~1v4~1qst~1create/post',
        'link'  => '',
    ],
    'qstCreateIp' => [
        'name'  => 'Подключение продавца-ИП (отправка анкеты)',
        'about'  => 'В этом примере показана реализация отправки анкеты подключаемого продавца-ИП на проверку в YPMN.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/qst-api/paths/~1v4~1qst~1create/post',
        'link'  => '',
    ],
    'qstStatus' => [
        'name'  => 'Получение статуса анкеты',
        'about'  => 'В этом примере показано получение статуса анкеты по её ID.<br/><br/>ID анкеты возвращается при отправке анкеты на проверку.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/qst-api/paths/~1v4~1qst~1status~1%7Bid%7D/get',
        'link'  => '',
    ],
    'qstPrint' => [
        'name'  => 'Печать анкеты',
        'about'  => 'В этом примере показано получение заполненной pdf версии анкеты по её ID.<br/><br/>ID анкеты возвращается при отправке анкеты на проверку.<br/><br/>Распечатать можно только одобренную анкету - в статусе approved.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/qst-api/paths/~1v4~1qst~1print~1%7Bid%7D/get',
        'link'  => '',
    ],
    'qstList' => [
        'name'  => 'Список анкет',
        'about'  => 'В этом примере показано получение списка анкет.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/qst-api/paths/~1v4~1qst~1list/get',
        'link'  => '',
    ],
    'getWidget' => [
        'name'  => 'Получение виджета',
        'about'  => 'В этом примере показано получение виджета.',
        'docLink'  => 'https://ypmn.ru/ru/documentation/#tag/widget-integration',
        'link'  => '',
    ],
    'webhookProcessing' => [
        'name' => 'Обработка вебхука',
        'about' => 'В этом примере показана обработка вебхука IPN',
        'docLink' => 'https://ypmn.ru/ru/documentation/#tag/webhooks',
        'link' => '',
    ]
];
