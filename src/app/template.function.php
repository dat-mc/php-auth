<?php

//вывод сообщения об ошибке красным
function handleError(string $errorText): string
{
    return "\033[31m" . $errorText . " \r\n \033[97m";
}

function handleHelp(): string
{
    $help = "Программа работы с файловым хранилищем \r\n";
    $help .= "Порядок вызова\r\n";
    $help .= "php cli.php [COMMAND] \r\n";
    $help .= "Доступные команды: \r\n";
    $help .= "rand - игра, \"Угадай число\"" . PHP_EOL;
    $help .= "help - помощь \r\n";

    return $help;
}