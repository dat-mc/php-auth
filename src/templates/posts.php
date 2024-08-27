<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Посты</title>
    <style>
        body {
            background-color: aqua;
        }
        div {
            border: 2px solid black;
            padding: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<nav>
    <a href="/">Главная</a>
    <a href="/posts">Посты</a>
</nav>
Все посты
<?php foreach ($posts as $post): ?>
    <div>
        <h2><?=$post['title']?></h2>
        <p><?=$post['preview']?></p>
    </div>
<?php endforeach; ?>
</body>
</html>