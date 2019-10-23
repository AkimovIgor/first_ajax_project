<?php
/* ОБРАБОТЧИК ДОБАВЛЕНИЯ КОММЕНТАРИЕВ */

// Подключение файла соединения с БД
require_once 'db.php';

// старт сессии
session_start();

if (!isset($_SESSION['user']['is_login']) && !isset($_COOKIE['user']['is_login'])) {
    header('Location: /'); // редирект на главную
    exit;
}

if (!isset($_SESSION['user'])) {
    // если существуют куки с данными
    if (isset($_COOKIE['user'])) {
        $email = $_COOKIE['user']['email'];
    }
} else {
    $email = $_SESSION['user']['email'];
}

$image = getUserImage($pdo,$email)['image'];

if ($image == 'no-user.jpg') {
    $image = 'markup/img/' . $image;
} else {
    // иначе, загружать с другой папки
    $image = 'uploads/' . $image;
}  

/**
 * Получение данных из запроса (получение данных из формы и дальнейшая работа с ними)
 *
 * @param [object] $pdo
 * @return void
 */
function getRequestData($pdo, $image) {
    $userId = $_POST['id'] ? $_POST['id'] : null;                           // получение ID комментатора
    $text = trim(htmlspecialchars($_POST['text'])); // получение текста комментария
    $date = date('Y-m-d');
    $page = $_POST['page'];                                   // устаковка даты добавления нового комментария

    $messages = []; // массив для хранения флеш-сообщений

    // если все поля были успешно заполнены
    if ($userId && ($text != null || $text == '0')) {
        // формируем sql-запрос в базу данных
        $sql = "INSERT INTO comments 
                (text, date, image, user_id) 
                VALUES (:text, '$date', '$image', $userId)";

        // подготавливаем запрос перед выполнением (для защиты от sql-инъекций)
        $stmt = $pdo->prepare($sql);

        // связываем подготовленные данные
        $stmt->bindParam(':text', nl2br($text));

        // выполнение запроса
        $stmt->execute();

        // запись в массив флеш-сообщения об успехе
        $messages['success'] = 'Комментарий успешно добавлен';

        
    } else {
        if (empty($text)) {
            $messages['errors']['text'] = 'Введите Ваш комментарий!';
        }
    }
    $data = [
        'messages' => $messages,
        'POST' => $_POST,
        'GET' => $page,
        'userId' => $userId,
        'text' => $text,
        'date' => prettyDate($date),
        'image' => $image,
        'author' => isset($_SESSION['user']['name']) ?  $_SESSION['user']['name'] : $_COOKIE['user']['name'],
        'COOKIE' => $_COOKIE,
    ];
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    die;
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
 * Получение аватара текущего пользователя
 *
 * @param [object] $pdo
 * @param [string] $currentUser
 * @return void
 */
function getUserImage($pdo, $currentUser) {
    // выбираем аватар текущего пользователя
    $sql = "SELECT name, image 
            FROM users 
            WHERE email = '$currentUser' 
            LIMIT 1";

    $stmt = $pdo->query($sql);
    $user = $stmt->fetch();
    return $user;
}

if (isset($_POST['store'])) {
    // вызов функции
    getRequestData($pdo, $image);
}