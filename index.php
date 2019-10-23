<?php
// Подключение файла соединения с БД
require_once('db.php');
require_once('functions.php');

// старт сессии
session_start();

$title = 'Главная';

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
            WHERE status = 1 
            ORDER BY cs.id DESC";
    // выполняем sql-запрос
    $stmt = $pdo->query($sql);
    // формируем ассоциативный массив полученных данных
    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // возвращаем массив
    return $row;
}


/**
 * Получить все комментарии для пагинации
 *
 * @param [object] $pdo
 * @param [integer] $offset
 * @param [integer] $limit
 * @return array
 */
function getAllComsForPaginate($pdo, $offset, $limit) {
    
    $sql = "SELECT cs.*, us.name, us.image 
            FROM comments AS cs 
            LEFT JOIN users AS us 
            ON cs.user_id = us.id 
            WHERE status = 1 
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

/**
 * Получение ID текущего пользователя
 *
 * @param [object] $pdo
 * @param [string] $email
 * @return integer
 */
function checkUser($pdo, $email) {
    // выбираем ID пользователя с текущим email
    $sql = "SELECT id 
            FROM users 
            WHERE email = '$email' 
            LIMIT 1";

    $stmt = $pdo->query($sql);
    
    $row = $stmt->fetch();
    // возвращаем ID
    return $row['id'];
}

$paginator = paginator($pdo);

// получение ID текущего пользователя
$userId = checkUser($pdo, $email);



if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    require_once('templates/ajax_index.php');
    echo '<script src="./markup/js/pagination.js" defer></script>';
    
} else {
    require_once('templates/default_index.php');
    
    echo '<script src="./markup/js/jquery.min.js" defer></script>';
    echo '<script src="./markup/js/bootstrap.js" defer></script>';
    echo '<script src="./markup/js/main.js" defer></script>';
    if(!isset($_POST['store'])) {
        echo '<script src="./markup/js/pagination.js" defer></script>';
    }
    
}
?>


