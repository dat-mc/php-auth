<?php
require_once __DIR__ . '/vendor/autoload.php';

//получаем параметры url, формат /api/{имя ресурса}/{id}
//запуск сервера через php -S localhost:80 http.php
//например /api/posts
//         /api/post/2
$url_array = explode("/", $_SERVER['REQUEST_URI']);
//TODO попробовать убрать дублирование
//Проверка входных параметров api на соответствие заданному маршруту и вывод подсказки пока просто текстом
if ($url_array[1] != 'api' || empty($url_array[2])) {
    //если нет приставки api считаем это запросом из браузера
    //Определяем запрашиваемую страницу
    $page = !empty($url_array[1]) ? $url_array[1] : 'index';

    $routes = require "httpRoutes.php";

    if (isset($routes[$page])) {
        $functionName = $routes[$page];
    } else {
        echo "Нет такой страницы";
        die();
    }


    if (function_exists($functionName)) {
        $result = $functionName();
        echo $result;
    } else {
        echo 'Нет такой функции';
    }

    exit;
}

//Получаем имя ресурса и id если есть
$action = $url_array[2];
$id = $url_array[3] ?? null;

//Получаем список маршрутов и извлекаем имя нужной функции
$routes = require "apiRoutes.php";
if (isset($routes[$action])) {
    $functionName = $routes[$action];
} else {
    echo "Нет такого ресурса";
    die();
}


if (function_exists($functionName)) {
    $result = isset($id) ? $functionName($id) : $functionName();
    header('Content-Type: application/json; charset=utf-8');
    echo $result;
} else {
    echo 'Нет такой функции';
}

