<?php
//TODO добавить категории (и юзера)
//CRUD

$db = pg_connect("host=localhost port=5432 dbname=blog user=postgres password=password");

$buttonText = "Добавить";
$action = "add";
$raw = [];

session_start();

//CREATE USER
if (isset($_GET['action']) && $_GET['action'] === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $_SESSION['old']['registration']['nickname'] = $nickname;
    $_SESSION['old']['registration']['email'] = $email;

    if (!$nickname) {
        $_SESSION['errors'][] = 'Введите никнейм для регистрации';
    }

    if (!$email) {
        $_SESSION['errors'][] = 'Введите E-mail для регистрации';
    }

    if (!$password) {
        $_SESSION['errors'][] = 'Введите пароль для регистрации';
    }

    if ($_SESSION['errors']) {
        header('Location: posts.php');
        exit;
    }

    $password = password_hash($password, PASSWORD_DEFAULT);

    pg_prepare($db, "insert", "insert into \"users\" (nickname, email, role_id, password) values ($1, $2, 2, $3) RETURNING id, nickname;");

    $result = pg_execute($db, "insert", [$nickname, $email, $password]);

    if (!$result) {
        $_SESSION['errors'][] = 'Не удалось зарегистрироваться (возможно ваш никнейм уже занят)';

        header('Location: posts.php');
        exit;
    }

    $row = pg_fetch_assoc($result);

    $_SESSION['messages'][] = 'Вы успешно зарегистрировались';

    $_SESSION['nickname'] = $row['nickname'];
    $_SESSION['user_id'] = $row['id'];

    header('Location: posts.php');
    exit;
}

//LOGIN USER
if (isset($_GET['action']) && $_GET['action'] === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['nickname']);
    $password = trim($_POST['password']);

    $_SESSION['old']['login']['nickname'] = $nickname;

    if (!$nickname) {
        $_SESSION['errors'][] = 'Введите никнейм для входа';
    }

    if (!$password) {
        $_SESSION['errors'][] = 'Введите пароль для входа';
    }

    if ($_SESSION['errors']) {
        header('Location: posts.php');
        exit;
    }

    $result = pg_prepare($db, "select", "
        select id, nickname, password from \"users\"
        where nickname = $1;
    ");

    $result = pg_execute($db, "select", [$nickname]);

    $row = pg_fetch_assoc($result);

    if (password_verify($password, $row['password'])) {
        $_SESSION['nickname'] = $row['nickname'];
        $_SESSION['user_id'] = $row['id'];
    } else {
        $_SESSION['errors'][] = 'Не удалось войти (неверный логин или пароль)';
    }

    header('Location: posts.php');
    exit;
}

//LOGOUT USER
if (isset($_GET['action']) && $_GET['action'] === 'logout' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    session_destroy();
    header('Location: posts.php');
    exit;
}

//UPDATE
if (isset($_GET['action']) && $_GET['action'] === 'edit') {
    $id = (int)$_GET['id'];
    $result = pg_prepare($db, "select", "
        select
            \"Posts\".id,
            title,
            preview,
            user_id,
            category_id,
            categories.name as \"category_name\"
        from \"Posts\"
        inner join categories on category_id = categories.id
        where \"Posts\".id = $1;
    ");

    $result = pg_execute($db, "select", [$id]);

    $raw = pg_fetch_assoc($result);

    if ($raw['user_id'] != $_SESSION['user_id']) {
        header('Location: posts.php');
        exit;
    }

    $buttonText = "Править";
    $action = "save";
}

if (isset($_GET['action']) && $_GET['action'] === 'save') {
    $title = $_POST['title'];
    $text = $_POST['text'];
    $categoryId = $_POST['category'];
    $id = (int)$_POST['id'];

    if (!$_SESSION['user_id']) {
        header('Location: posts.php');
        exit;
    }

    $userId = $_SESSION['user_id'];

    pg_prepare($db, "update", "update \"Posts\" set title = $1, preview = $2, category_id = $3 where id = $4 AND user_id = $5;");
    pg_send_execute($db, "update", [$title, $text, $categoryId, $id, $userId]);

    header('Location: posts.php');
    exit;
}

//DELETE
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $id = (int)$_GET['id'];

    if (!$_SESSION['user_id']) {
        header('Location: posts.php');
        exit;
    }

    $userId = $_SESSION['user_id'];

    $query = "DELETE FROM \"Posts\" WHERE id = $1 AND user_id = $2";
    pg_prepare($db, "delete", $query);
    pg_send_execute($db, "delete", [$id, $userId]);

    header('Location: posts.php');
    exit;
}

//CREATE
if (isset($_GET['action']) && $_GET['action'] === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $text = $_POST['text'];
    $category = $_POST['category'];

    if (!$_SESSION['user_id']) {
        header('Location: posts.php');
        exit;
    }

    $userId = $_SESSION['user_id'];

    pg_prepare($db, "insert", "insert into \"Posts\" (title, preview, text, category_id, user_id) values ($1, $2, '', $3, $4);");

    pg_send_execute($db, "insert", [$title, $text, $category, $userId]);

    header('Location: posts.php');
    exit;
}

// READ Posts
$result = pg_query($db, "
    select
        \"Posts\".id,
        title,
        preview,
        category_id,
        user_id,
        categories.name as \"category_name\",
        users.nickname as \"author\"
    from \"Posts\"
    inner join categories on category_id = categories.id
    inner join users on user_id = users.id
    ORDER BY id DESC;
");

$posts = pg_fetch_all($result);

// READ Categories
$result = pg_query($db, "select id, name from \"categories\" ORDER BY id ASC ;");

$categories = pg_fetch_all($result);

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>CRUD</title>
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
    <?php if ($_SESSION['messages'] ?? false): ?>
        <?php foreach ($_SESSION['messages'] as $message): ?>
            <p style="color: green;"><?= $message ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php if ($_SESSION['errors'] ?? false): ?>
        <?php foreach ($_SESSION['errors'] as $error): ?>
            <p style="color: red;"><?= $error ?></p>
        <?php endforeach; ?>
    <?php endif; ?>
    <h1>Блог</h1>
    <?php if (!$_SESSION['nickname'] ?? false): ?>
        <form method="post" action="?action=register">
            <input type="text" name="nickname" placeholder="Никнейм" value="<?= $_SESSION['old']['registration']['nickname'] ?? '' ?>">
            <input type="email" name="email" placeholder="E-mail" value="<?= $_SESSION['old']['registration']['email'] ?? '' ?>">
            <input type="password" name="password" placeholder="Пароль">
            <input type="submit" value="Зарегистрироваться"><br>
        </form>
        <br>
        <p>или</p>
        <br>
        <form method="post" action="?action=login">
            <input type="text" name="nickname" placeholder="Никнейм" value="<?= $_SESSION['old']['login']['nickname'] ?? '' ?>">
            <input type="password" name="password" placeholder="Пароль">
            <input type="submit" value="Войти"><br>
        </form>
        <br>
    <?php endif; ?>
    <?php if ($_SESSION['nickname'] ?? false): ?>
        <p>Привет, <?= $_SESSION['nickname'] ?></p>
        <a href="?action=logout">Выйти</a>
        <h2>Посты</h2>
        <form method="post" action="?action=<?= $action ?>">
            <input type="text" name="id" hidden value="<?= $raw['id'] ?? '' ?>">
            <input type="text" name="title" placeholder="Заголовок поста" value="<?= $raw['title'] ?? '' ?>"><br>
            <textarea name="text" cols="30" rows="3" placeholder="Текст поста"><?= $raw['preview'] ?? '' ?></textarea><br>
            <label for="category">Категория</label>
            <select name="category" id="category">
                <?php foreach ($categories as $category): ?>
                    <option <?php if ($category['id'] == ($raw['category_id'] ?? 0)) echo 'selected' ?> value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="<?= $buttonText ?>"><br>
        </form>
        <br>
        <?php foreach ($posts as $post): ?>
            <div>
                <h2><?= $post['title'] ?></h2>
                <p> Категория: <?= $post['category_name'] ?></p>
                <p><?= $post['preview'] ?></p>
                <p> Автор: <?= $post['author'] ?></p>
                <?php if ($_SESSION['user_id'] == $post['user_id']): ?>
                    <a href="?action=edit&id=<?= $post['id'] ?>">[правка]</a>
                    <a href="?action=delete&id=<?= $post['id'] ?>">[X]</a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php
    unset($_SESSION['messages']);
    unset($_SESSION['errors']);
    unset($_SESSION['old']);
    ?>
</body>

</html>