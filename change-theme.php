<?php
// стартуем сессию
session_start();

// получаем имя темы
$themeName = $_POST['theme'];

// если не существует сессия, создаем её
if(!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = '';
}

// заносим имя темы в сесси с маленькой буквы
$_SESSION['theme'] = lcfirst($themeName);

$data = [
    'themeName' => $_SESSION['theme']
];

// отправляем json
echo json_encode($data);
die;
