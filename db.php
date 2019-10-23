<?php

// Данные подключения к базе данных
$hostName = 'localhost';    // имя хоста
$dbName = 'mybase';         // имя базы данных
$dbUser = 'root';           // имя пользователя базы данных
$dbPassword = null;         // пароль пользователя базы данных

// Указываем данные для соединения с mysql
$dsn = "mysql:host=$hostName;dbname=$dbName";

// Опции для настройки PDO подключения (не обязательно)
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

// Подключение к базе данных с помощью объекта PDO
//$pdo = new PDO($dsn, $dbUser, $dbPassword, $options);

try {
    $pdo = new PDO($dsn, $dbUser, $dbPassword, $options);
} catch (PDOException $e) {
    die('Ошибка соединения с БД: ' . $e->getMessage());
}

?>