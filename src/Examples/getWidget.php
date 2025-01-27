<?php

declare(strict_types=1);

use Ypmn\Widget;

// Подключим файл, в котором заданы параметры мерчанта
include_once 'start.php';

// Многократно используемые параметры виджета
define("LABEL", "Купить в один клик");
define("CLASSES", "button pay_button");

// Создадим экземпляр класса виджета.
$widget = new Widget($merchant->getCode(), "RU", "RUB");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Виджет YPMN</title>
</head>
<body>
    <h1>Пример работы виджета</h1>

    <p>Полное описание товарной позиции номер один</p>
    <p><span>Товар номер один</span> - <?php
        // Выводим кнопку покупки на 199.99 рублей.
        echo $widget->makeBuyButton(199.99, "RUB", LABEL, CLASSES);
    ?></p>

    <p>Полное описание товарной позиции номер два</p>
    <p><span>Товар номер два</span> - <?php
        // Выводим кнопку покупки на 299.99 рублей.
        echo $widget->makeBuyButton(299.99, "RUB", LABEL, CLASSES);
    ?></p>

    <p>Полное описание товарной позиции номер три</p>
    <p><span>Товар номер три</span> - <?php
        // Выводим кнопку покупки на 399.99 рублей.
        echo $widget->makeBuyButton(399.99, "RUB", LABEL, CLASSES);
    ?></p>

    <?php echo $widget->makeBuyForm(); ?>
</body>
</html>
