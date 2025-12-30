<?php

declare(strict_types=1);

use Ypmn\Merchant;

/**
 * Создадим объект Мерчанта
 * (получите Интеграционный Код Мерчанта и Секретный Ключ у вашего менеджера YPMN)
 *
 * Теперь включайте этот файл везде, где работаете с платежами
 *
 * Запросы от вашего приложения будут отправляться на:
 *      https://secure.ypmn.ru/
 *      https://sandbox.ypmn.ru/
 * Убедитесь, что эти адреса разрешены в Firewall вашего приложения
 */
$merchant = new Merchant('gitttest', 'vk0!K4(~9)1d69@0p4&N');
//$merchant = new Merchant('CLD_FUL', 'SECRET_KEY');
