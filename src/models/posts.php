<?php
function getAllPosts(): array | bool
{
    $db = @dbConnect(getConfig());

    $result = @pg_query($db, "select id, title, preview from public.\"Posts\";");

    if (!$result) {
        return false;
    }

    return pg_fetch_all($result);
}