# «Твои Платежи»: Интеграция на PHP
Готовая библиотека + подробные примеры с комментариями. Требования: [PHP 7.4 и выше](https://github.com/yourpayments/php-api-client/blob/main/composer.json)

![](https://repository-images.githubusercontent.com/638835276/2067d028-b541-4355-b069-3c12c8a28042)

[Пакет Composer](https://packagist.org/packages/yourpayments/php-api-client) может 
использоваться с любыми фреймворками, платформами и CMS, включая, но не ограничиваясь: Laravel, Bitrix, Wordpress, Yii, Symfony, и др.

## Установка и обновление за 1 минуту
```shell
composer require yourpayments/php-api-client
```
```shell
composer update yourpayments/php-api-client
```
(если на вашем проекте нет composer, слонируйте или скачайте, а затем подключите ([require](https://www.php.net/manual/ru/function.require.php)) файлы этого репозитория)

## Запуск в контейнере docker
Создайте и запустите docker контейнер следующей командой:
```shell
docker compose up
```
либо в фоновом режиме командой:
```shell
docker compose up --detach
```
После выполнения сервис с документацией и примерами будет доступен по адресу http://localhost:8080/
 
## Примеры с комментариями на русском языке:
##### 1. [Начало работы: настройка интеграции](src/Examples/start.php)

##### 2. Платежи
1. [Cамый простой платёж](src/Examples/simpleGetPaymentLink.php)
2. [Подробный платёж](src/Examples/getPaymentLink.php)
3. [Платёж через СБП (Систему Быстрых Платежей)](src/Examples/getFasterPayment.php)
4. [Платёж со сплитом (разделением платежа)](src/Examples/getPaymentLinkMarketplace.php)
5. [Списание средств](src/Examples/paymentCapture.php)

##### 3. Подписки СБП  
1. [Создание подписки СБП](src/Examples/getBindingFasterPayment.php)
2. [Оплата по подписке СБП](src/Examples/paymentByFasterBinding.php)

##### 4. Подписки SberPay, T-Pay, Картой не РФ
1. [Создание подписки](src/Examples/getBindingPays.php)
2. [Оплата по подписке](src/Examples/paymentByBindingPays.php)
   
##### 5. Токенизация карты (чтобы запомнить карту клиента и не вводить повторно)
1. [Создание платёжного токена ](src/Examples/getToken.php)
2. [Оплата токеном](src/Examples/paymentByToken.php)
  
##### 6. Отчёты
1. [Проверка статуса платежа](src/Examples/paymentGetStatus.php)
2. [Запрос детального отчета по заказу](src/Examples/getReportOrderDetails.php)
3. [Запрос быстрого отчёта по заказам для сверки](src/Examples/getReportOrder.php)
4. [Запрос отчёта по заказам](src/Examples/getReportGeneral.php)
5. [Запрос отчёта в виде графика](src/Examples/getReportChart.php)

##### 7. Возврат средств плательщику (Refund)
1. [Возврат средств](src/Examples/paymentRefund.php)
2. [Возврат средств со сплитом (разделением платежа)](src/Examples/paymentRefundMarketplace.php)

##### 8. Выплаты
1. [Выплаты на банковские карты](src/Examples/payoutCreate.php)
2. [Запрос баланса для выплаты](src/Examples/payoutGetBalance.php)

##### 9. [Безопасные поля (Secure fields)](src/Examples/secureFields.php)
2. [Создание сессии](src/Examples/getSession.php)
3. [Оплата одноразовым токеном](src/Examples/oneTimeTokenPayment.php)

##### 10. [Страница после оплаты](src/Examples/returnPage.php)

##### 11. Подключение продавцов (сабмерчантов маркетплейсов)
1. [Подключение продавца-юридического лица (отправка анкеты)](src/Examples/qstCreateOrg.php)
2. [Подключение продавца-ИП (отправка анкеты)](src/Examples/qstCreateIp.php)
3. [Получение статуса анкеты](src/Examples/qstStatus.php)
4. [Печать анкеты](src/Examples/qstPrint.php)
5. [Список анкет](src/Examples/qstList.php)

##### 12. Виджет
- [Подключение виджета](src/Examples/getWidget.php)

## Ссылки
- [НКО «Твои Платежи»](https://YPMN.ru/)
- [Докуметация API](https://ypmn.ru/ru/documentation/)
- [Тестовые банковские карты](https://ypmn.ru/ru/documentation/#tag/testing)
- [Задать вопрос или сообщить о проблеме](https://github.com/yourpayments/php-api-client/issues/new)

-------------
🟢 [«Твои Платежи»](https://YPMN.ru/ "Платёжная система для сайтов, платформ и приложений") -- финтех-составляющая для сайтов, платформ и приложений
