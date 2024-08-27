<?php

require_once __DIR__ . '/vendor/autoload.php';



// вызов корневой функции
//TODO  сделать абсолютный путь
$result = main('config.ini');

// вывод результата
echo $result;
