<?php
//TODO * сделать CRUD на этом движке
function httpIndex(): string
{
  return render('index', [
      'name' => 'Админ'
  ]);
}

function httpPosts(): string
{

    $posts = getAllPosts();

    return render('posts', [
        'posts' => $posts
    ]);
}