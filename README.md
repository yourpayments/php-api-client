# «Твои платежи»: Интеграция для PHP
Готовая библиотека + подробные примеры с комментариями. Требования: [PHP 7.4 и выше](https://github.com/yourpayments/php-api-client/blob/main/composer.json)

![](https://repository-images.githubusercontent.com/638835276/ff494b04-d65b-4843-8759-e85c689a7e80)

[Пакет Composer](https://packagist.org/packages/yourpayments/php-api-client) может 
использоваться с любыми фреймворками и CMS.
 
## Установка за 1 минуту
```shell
$ composer require yourpayments/php-api-client
```
(если на вашем проекте нет composer, слонируйте или скачайте, а затем подключите ([require](https://www.php.net/manual/ru/function.require.php)) файлы этого репозитория)

## Примеры
1. [Начало работы (настройка интеграции)](src/Examples/start.php)
2. [Cамый простой платёж](src/Examples/simpleGetPaymentLink.php)
3. [Подробный платёж](src/Examples/getPaymentLink.php)
4. [Платёж со сплитом](src/Examples/getPaymentLinkMarketplace.php)
5. [Токенизация карты (чтобы запомнить карту клиента и не вводить повторно)](src/Examples/getToken.php)
6. [Оплата токеном](src/Examples/paymentByToken.php)
7. [Списание средств](src/Examples/paymentCapture.php)
8. [Возврат средств](src/Examples/paymentRefund.php)
9. [Возврат средств со сплитом](src/Examples/paymentRefundMarketplace.php)
10. [Проверка статуса платежа](src/Examples/paymentGetStatus.php)
11. [Выплаты на банковские карты](src/Examples/payoutCreate.php)
12. [Создание сессии](src/Examples/getSession.php)
13. [Оплата одноразовым токеном](src/Examples/oneTimeTokenPayment.php)
14. [Страница после оплаты](src/Examples/returnPage.php)
15. [Безопасные поля (Secure fields)](src/Examples/secureFields.php)
16. [Запрос отчёта в формате Json](src/Examples/getReportGeneral.php)
17. [Запрос отчёта в виде графика](src/Examples/getReportChart.php)

## Ссылки
- [НКО «Твои платежи»](https://YPMN.ru/)
- [Докуметация API](https://ypmn.ru/ru/documentation/)
- [Тестовые банковские карты](https://ypmn.ru/ru/documentation/#tag/testing)
- [Задать вопрос или сообщить о проблеме](https://github.com/yourpayments/php-api-client/issues/new)

-------------
[«Твои платежи»](https://YPMN.ru/ "Платёжная система для сайтов, платформ и приложений") -- финтех-составляющая для сайтов, платформ и приложений
