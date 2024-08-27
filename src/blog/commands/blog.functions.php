<?php

function addRandomPost()
{
    $faker = Faker\Factory::create();

    echo $faker->realtext(100);


    return "";
}

function getPost(array $config): string
{
    //TODO сделать так
    //$config = getConfig();

    $db = @dbConnect($config);

    //TODO сделайте валидацию параметра, что если параметр не передан или это не число вывести ошибку
    $id = (int)$_SERVER['argv'][2];

    $result = @pg_prepare($db, "select", "select id, title, preview from public.\"Posts\" where id = $1;");

    $result = @pg_execute($db, "select", [$id]);

    if (!$result) {
        return handleError("Ошибка запроса "  . pg_last_error($db));
    }

    //TODO вывести запись одной строкой
    return print_r(pg_fetch_assoc($result), true);
}

function getPosts(array $config): string
{



    $result = getAllPosts();

    if (!$result) {
        return handleError("Ошибка запроса "  . pg_last_error($db));
    }

    //TODO сделать чтобы каждый пост был в одной строке
    //Собрать строку красиво через цикл
    //можно как вариант использовать array_map и implode
    //print_r(implode(" | ", pg_fetch_all($result)[1]));
/*    while ($row = pg_fetch_assoc($result)) {
        $posts[] = $row;
    }*/

    return print_r($result, true);
}