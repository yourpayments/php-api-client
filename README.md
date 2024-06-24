# «Твои платежи»: Интеграция на PHP
Готовая библиотека + подробные примеры с комментариями. Требования: [PHP 7.4 и выше](https://github.com/yourpayments/php-api-client/blob/main/composer.json)

![](https://repository-images.githubusercontent.com/638835276/2067d028-b541-4355-b069-3c12c8a28042)

[Пакет Composer](https://packagist.org/packages/yourpayments/php-api-client) может 
использоваться с любыми фреймворками, платформами и CMS, включая, но не ограничиваясь: Laravel, Bitrix, Wordpress, Symfony, и др.

## Установка за 1 минуту
```shell
composer require yourpayments/php-api-client
```
(если на вашем проекте нет composer, слонируйте или скачайте, а затем подключите ([require](https://www.php.net/manual/ru/function.require.php)) файлы этого репозитория)

## Примеры
1. [Начало работы (настройка интеграции)](src/Examples/start.php)
2. Платежи
2.1. [Cамый простой платёж](src/Examples/simpleGetPaymentLink.php)
2.2. [Подробный платёж](src/Examples/getPaymentLink.php)
2.3. [Платёж через СБП](src/Examples/getFasterPayment.php)
2.4. [Платёж со сплитом (разделением платежа)](src/Examples/getPaymentLinkMarketplace.php)
2.5. [Списание средств](src/Examples/paymentCapture.php)
2.6. Подписки СБП
2.6.1. [Создание подписки СБП](src/Examples/getBindingFasterPayment.php)
2.6.2. [Оплата по подписке СБП](src/Examples/paymentByFasterBinding.php)
2.7. Токенизация карты (чтобы запомнить карту клиента и не вводить повторно)
2.7.1. [Создание платёжного токена ](src/Examples/getToken.php)
2.7.2. [Оплата токеном](src/Examples/paymentByToken.php)
3. Отчёты
3.1. [Проверка статуса платежа](src/Examples/paymentGetStatus.php)
3.2. [Запрос детального отчета по заказу](src/Examples/getReportOrderDetails.php)
3.3. [Запрос быстрого отчёта по заказам для сверки](src/Examples/getReportOrder.php)
3.4 .[Запрос отчёта по заказам](src/Examples/getReportGeneral.php)
3.3. [Запрос отчёта в виде графика](src/Examples/getReportChart.php)
4. Возврат средств плательщику (Refund)
4.1. [Возврат средств](src/Examples/paymentRefund.php)
4.2. [Возврат средств со сплитом (разделением платежа)](src/Examples/paymentRefundMarketplace.php)
5. [Выплаты на банковские карты](src/Examples/payoutCreate.php)
6. [Безопасные поля (Secure fields)](src/Examples/secureFields.php)
6.1. [Создание сессии](src/Examples/getSession.php)
6.2. [Оплата одноразовым токеном](src/Examples/oneTimeTokenPayment.php)
7. [Страница после оплаты](src/Examples/returnPage.php)

## Ссылки
- [НКО «Твои платежи»](https://YPMN.ru/)
- [Докуметация API](https://ypmn.ru/ru/documentation/)
- [Тестовые банковские карты](https://ypmn.ru/ru/documentation/#tag/testing)
- [Задать вопрос или сообщить о проблеме](https://github.com/yourpayments/php-api-client/issues/new)

-------------
🟢 [«Твои платежи»](https://YPMN.ru/ "Платёжная система для сайтов, платформ и приложений") -- финтех-составляющая для сайтов, платформ и приложений
