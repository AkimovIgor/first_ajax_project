<?php

require_once('db.php');

session_start();

if (!isset($_SESSION['user'])) {
    // если существуют куки с данными
    if (isset($_COOKIE['user'])) {
        $email = $_COOKIE['user']['email'];
    }
} else {
    $email = $_SESSION['user']['email'];
}

/**
 * Изменение пользовательского пароля
 *
 * @param [object] $pdo
 * @param [string] $email
 * @return void
 */
function changeUserPassword($pdo, $email) {

    // почта текущего пользователя
    $currentEmail = $email;

    // получение данных с полей
    $currentPass = trim(htmlspecialchars($_POST['current']));
    $password = trim(htmlspecialchars($_POST['password']));
    $passwordConfirm = trim(htmlspecialchars($_POST['password_confirmation']));

    $password_hash = password_hash($password, PASSWORD_DEFAULT); // шифрование пароля

    $validation = true; // статус валидации
    $messages = [];     // массив для флеш-сообщений

    // валидация полей
    $checkPass = checkCurrentPassword($pdo, $currentPass, $currentEmail);
    if (!$checkPass) {
        $validation = false;
        $messages['errors']['current'] = 'Неверный текущий пароль!';
    }
    if (empty($currentPass)) {
        $validation = false;
        $messages['errors']['current'] = 'Введите текущий пароль!';
    }
    if (strlen($password) < 6 ) {
        $validation = false;
        $messages['errors']['password'] = 'Минимальная длина пароля 6 символов';
    }
    if (empty($password)) {
        $validation = false;
        $messages['errors']['password'] = 'Введите новый пароль!';
    }
    if (empty($passwordConfirm)) {
        $validation = false;
        $messages['errors']['password_confirmation'] = 'Подтвердите новый пароль!';
    }
    if (!empty($password) && !empty($passwordConfirm) && $password != $passwordConfirm) {
        $validation = false;
        $messages['errors']['password_equal'] = 'Пароли не совпадают!';
    }

    // если поля прошли валидацию
    if ($validation == true) {
        // формируем sql-запрос 
        $sql = "UPDATE users 
                SET password = :password 
                WHERE email = '$currentEmail'";

        // подготавливаем sql-запрос 
        $stmt = $pdo->prepare($sql);

        // связываение параметров
        $stmt->bindParam(':password', $password_hash);

        // выполнение запроса
        $stmt->execute();

        // добавление флеш-сообщения
        $messages['success'] = 'Пароль успешно изменен!';
    }

    
    
    $data = [
        'messages' => $messages,
        'SESSION' => $_SESSION,
        'POST' => $_POST,
        'validation' => $validation,
    ];
    
    echo json_encode($data);die;
}

/**
 * Проверка текущего пароля
 *
 * @param [object] $pdo
 * @param [string] $currentPass
 * @param [string] $currentEmail
 * @return boolean
 */
function checkCurrentPassword($pdo, $currentPass, $currentEmail) {

    // выбираем пароль текущего пользователя
    $sql = "SELECT password 
            FROM users 
            WHERE email = '$currentEmail' 
            LIMIT 1";
    
    $stmt = $pdo->query($sql);
    $user = $stmt->fetch();

    // если пароль был найден в базе
    if ($user['password']) {
        // сравнивание введенного пароля с хешом текущего пароля
        $passwordVerified = password_verify($currentPass, $user['password']);
        return (!$passwordVerified) ? false : true;
    }

    return false;
}

if (isset($_POST['edit'])) {
    changeUserPassword($pdo, $email);
}