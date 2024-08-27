<?php
function apiPosts(): bool|string
{
    $posts = getAllPosts();

    if (!$posts) {
        return json_encode([
            'status' => 'error',
            'message' => 'Ошибка получения запроса ' . pg_last_error(@dbConnect(getConfig()))
        ]);
    }

    return json_encode([
        'status' => 'success',
        'message' => 'Посты успешно получены',
        'data' => $posts
    ]);
}

function apiPost(int $id)
{
    $db = @dbConnect(getConfig());

    @pg_prepare($db, "select", "select id, title, preview from public.\"Posts\" where id = $1;");

    $result = @pg_execute($db, "select", [$id]);

    //TODO проверка на ошибки и вывод json c текстом ошибки

    return json_encode(pg_fetch_assoc($result), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}