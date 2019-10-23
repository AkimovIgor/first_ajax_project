<?php
session_start();

$themeName = $_POST['theme'];

if(!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = '';
}

$_SESSION['theme'] = lcfirst($themeName);

$data = [
    'themeName' => $_SESSION['theme']
];

echo json_encode($data);
die;
