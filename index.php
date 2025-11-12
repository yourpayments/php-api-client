<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Эта функция подключает классы
spl_autoload_register(function ($className) {
    $className = explode('\\', $className);
    $className =  end($className);

    $filename = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $className . '.php';

    if (is_readable($filename)) {
        require_once($filename);
    }
});
require_once __DIR__ . DIRECTORY_SEPARATOR . 'src' .DIRECTORY_SEPARATOR . 'Traits/ProtobufSerializable.php';

// обработка json запросов
$jsonMode = $_REQUEST['json'] ?? false;

if ($jsonMode) {
    require 'example.php';
} else {
    include 'example_list.php';
    include 'example_header.php';
    require 'example.php';
    include 'example_footer.html';
}
