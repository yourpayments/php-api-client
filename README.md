# Твои Платежи, интеграция на PHP
PHP SDK, готовый клиент для нашего API + примеры использования платёжной системы

![](https://repository-images.githubusercontent.com/638835276/ff494b04-d65b-4843-8759-e85c689a7e80)
 
Эта библиотека содержит подробные [примеры](src/Examples/) с комментариями на русском языке 
и предназначена для быстрой интеграции. Подходит для сайтов, платформ и приложений.

Репозиторий опубликован в виде [пакета Composer](https://packagist.org/packages/yourpayments/php-api-client) и может 
использоваться с любыми фреймворками и CMS.
 
Требования: [PHP 7.4 и выше](https://github.com/yourpayments/php-api-client/blob/main/composer.json)

## Установка
### Composer
```shell
$ composer require yourpayments/php-api-client
```

```php
<?php

require vendor/autoload.php;
```

### PHP без фреймворков
Клонируйте или скачайте, а затем подключите ([require](https://www.php.net/manual/ru/function.require.php)) файлы этого репозитория.

## Документация: Примеры + комментарии
1. [Начало работы (настройка интеграции)](src/Examples/start.php)
2. [Cамый простой платёж](src/Examples/simpleGetPaymentLink.php)
3. [Подробный платёж](src/Examples/getPaymentLink.php)
4. [Оплата через СБП](src/Examples/getFasterPayment.php)
5. [Создание подписки СБП](src/Examples/getBindingFasterPayment.php)
6. [Оплата по подписке СБП](src/Examples/paymentByFasterBinding.php)
7. [Платёж со сплитом](src/Examples/getPaymentLinkMarketplace.php)
8. [Токенизация карты (чтобы запомнить карту клиента и не вводить повторно)](src/Examples/getToken.php)
9. [Оплата токеном](src/Examples/paymentByToken.php)
10. [Списание средств](src/Examples/paymentCapture.php)
11. [Возврат средств](src/Examples/paymentRefund.php)
12. [Возврат средств со сплитом](src/Examples/paymentRefundMarketplace.php)
13. [Проверка статуса платежа](src/Examples/paymentGetStatus.php)
14. [Выплаты на банковские карты](src/Examples/payoutCreate.php)
15. [Создание сессии](src/Examples/getSession.php)
16. [Оплата одноразовым токеном](src/Examples/oneTimeTokenPayment.php)
17. [Страница после оплаты](src/Examples/returnPage.php)
18. [Безопасные поля (Secure fields)](src/Examples/secureFields.php)
19. [Запрос отчёта в формате Json](src/Examples/getReportGeneral.php)
20. [Запрос отчёта в виде графика](src/Examples/getReportChart.php)

## Ссылки
- [Основной сайт НКО "Твои Платежи"](https://YPMN.ru/)
- [Докуметация по API](https://ypmn.ru/ru/documentation/)
- [Реквизиты тестовых банковских карт](https://dev.payu.ru/ru/documents/rest-api/testing/#menu-2)
- [Задать вопрос или сообщить о проблеме](https://github.com/yourpayments/php-api-client/issues/new)

-------------
[НКО «Твои Платежи»](https://YPMN.ru/ "Платёжная система для сайтов, платформ и приложений") - платёжная система для сайтов, платформ, игр и приложений.
