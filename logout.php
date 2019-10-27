<?php
/* Выход из учетной записи пользователя */

// старт сессии
session_start();

// если пользователь не авторизован
if (!isset($_SESSION['user']['is_login']) && !isset($_COOKIE['user']['is_login'])) {
    header('Location: /'); // редирект на главную
    exit;
}

// удаляем сессии и куки, если они есть
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    header('Location: /');
}
if (isset($_COOKIE['user'])) {
    setcookie('user[is_login]', '', time()-5);
    header('Location: /');
}