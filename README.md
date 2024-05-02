# «Твои платежи»: Интеграция для PHP
Готовая библиотека + подробные примеры с комментариями. Требования: [PHP 7.4 и выше](https://github.com/yourpayments/php-api-client/blob/main/composer.json)

![](https://repository-images.githubusercontent.com/638835276/ff494b04-d65b-4843-8759-e85c689a7e80)

[Пакет Composer](https://packagist.org/packages/yourpayments/php-api-client) может 
использоваться с любыми фреймворками, платформами и CMS.
 
## Установка за 1 минуту
```shell
composer require yourpayments/php-api-client
```
(если на вашем проекте нет composer, слонируйте или скачайте, а затем подключите ([require](https://www.php.net/manual/ru/function.require.php)) файлы этого репозитория)

## Примеры
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
21. [Платёж зарубежными картами](src/Examples/SOMGetPaymentLink.php)

## Ссылки
- [НКО «Твои платежи»](https://YPMN.ru/)
- [Докуметация API](https://ypmn.ru/ru/documentation/)
- [Тестовые банковские карты](https://ypmn.ru/ru/documentation/#tag/testing)
- [Задать вопрос или сообщить о проблеме](https://github.com/yourpayments/php-api-client/issues/new)

-------------
[«Твои платежи»](https://YPMN.ru/ "Платёжная система для сайтов, платформ и приложений") -- финтех-составляющая для сайтов, платформ и приложений
