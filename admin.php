<?php

// Подключение файла соединения с БД
require_once('db.php');
require_once('functions.php');

// старт сессии
session_start();

$title = 'Административная панель';

// осуществление доступа админке
if (!isset($_SESSION['user']['is_login']) && !isset($_COOKIE['user']['is_login'])) {
    header('Location: /'); // редирект на главную
    exit;
}
if (!isset($_COOKIE['user']['is_login']) && isset($_SESSION['user']['is_login']) && $_SESSION['user']['name'] != 'admin') {
    header('Location: /'); // редирект на главную
    exit;
}
if (!isset($_SESSION['user']['is_login']) && isset($_COOKIE['user']['is_login']) && $_COOKIE['user']['name'] != 'admin') {
    header('Location: /'); // редирект на главную
    exit;
}

// если сессия с флеш-сообщениями не существует
if (!isset($_SESSION['messages'])) {
    $_SESSION['messages'] = []; // создать сессию
} else {
    // если сессия существует, записать значения в соответствующие переменные
    $errors = $_SESSION['messages']['errors'] ? $_SESSION['messages']['errors'] : null;
    $success = $_SESSION['messages']['success'] ? $_SESSION['messages']['success'] : null;

    // уничтожить сессию
    unset($_SESSION['messages']);
}

// если сессия с данными пользователя не существует
if (!isset($_SESSION['user'])) {
    // если существуют куки с данными
    if (isset($_COOKIE['user'])) {
        $isLogin = $_COOKIE['user']['is_login'];
        $name = $_COOKIE['user']['name'];
        $email = $_COOKIE['user']['email'];
    }
} else {
    // если же сессия с данными пользователя существует
    $isLogin = $_SESSION['user']['is_login'];
    $name = $_SESSION['user']['name'];
    $email = $_SESSION['user']['email'];
}

/**
 * Получение всех комментариев из базы
 *
 * @param [object] $pdo
 * @return array
 */
function getAllComments($pdo) {
    // формируем sql-запрос
    $sql = "SELECT cs.*, us.name, us.image
            FROM comments AS cs 
            LEFT JOIN users AS us 
            ON cs.user_id = us.id 
            ORDER BY cs.id DESC";
    // выполняем sql-запрос
    $stmt = $pdo->query($sql);
    // формируем ассоциативный массив полученных данных
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // возвращаем массив
    return $row;
}


function getAllComsForPaginate($pdo, $offset, $limit) {
    
    $sql = "SELECT cs.*, us.name, us.image 
            FROM comments AS cs 
            LEFT JOIN users AS us 
            ON cs.user_id = us.id 
            ORDER BY cs.id DESC 
            LIMIT $offset,$limit";
    //dd($sql);
    // выполняем sql-запрос
    $stmt = $pdo->query($sql);
    // формируем ассоциативный массив полученных данных
    $row = $stmt->fetchAll();
    // возвращаем массив
    return $row;
}

/**
 * Формирование красивой даты для вывода
 *
 * @param [string] $date
 * @return string
 */
function prettyDate($date) {
    // формирование массива из строки
    $arr = explode('-', $date);
    // реверс массива
    $arr_rev = array_reverse($arr);
    // формирование строки с датой из массива
    $date = implode('/', $arr_rev);

    return $date;
}



$paginator = paginator($pdo);
$paginator['link'] = 'admin.php/?page=';
$paginator['comments'] = getAllComsForPaginate($pdo, $paginator['offset'], $paginator['perPage']);


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    require_once('templates/ajax_admin.php');
    echo '<script src="./markup/js/pagination.js" defer></script>';
    echo '<script src="./markup/js/admin.js" defer></script>';
    
} else {
    require_once('templates/default_admin.php');
    
    echo '<script src="./markup/js/jquery.min.js" defer></script>';
    echo '<script src="./markup/js/bootstrap.js" defer></script>';
    echo '<script src="./markup/js/main.js" defer></script>';
    echo '<script src="./markup/js/admin.js" defer></script>';
    if(!isset($_POST['store'])) {
        echo '<script src="./markup/js/pagination.js" defer></script>';
    }
    
}

?>


