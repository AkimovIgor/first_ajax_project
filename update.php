<?php
/* ОБРАБОТЧИК ОБНОВЛЕНИЯ СТАТУСА КОММЕНТАРИЯ */

// подключаемся к бд
require_once('db.php');

// стартуем сессию
session_start();

// если нет параметра id в запросе
if (!isset($_REQUEST['id'])) {
    header('Location: /admin.php'); // редирект в админку
    exit();
}

/**
 * Обновление статуса комментария
 *
 * @param [object] $pdo
 * @return void
 */
function updateStatus($pdo) {

    // получаем ID обновляемого коммента
    $comId =  (integer) trim(htmlspecialchars($_REQUEST['id']));

    // sql-запрос для получения текущего статуса
    $sql = "SELECT status 
            FROM comments 
            WHERE id = $comId";

    $stmt = $pdo->query($sql);
    $comment = $stmt->fetch();

    // получаем текущий статус
    $comment = $comment['status'];

    // если комментарий существует
    if ($comment !== null) {

        // подготавливаем данные
        $data = [
            'id' => $comId
        ];

        // проверяем и устанавливаем статус
        if ($comment) {
            $data['status' ] = 0;
        } else {
            $data['status' ] = 1;
        }

        // обновляем статус
        $sql = "UPDATE comments 
            SET status = :status 
            WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        
        $stmt->execute($data);
        
    } else {
        $messages['errors']['status'] = 'Такого комментария не существует!';
        
    }

    $data = [
        'messages' => $messages,
        'status' => $data['status']
    ];

    echo json_encode($data);
    die;
    // записываем флеш в сессию
    // $_SESSION['messages'] = $messages;

    // редиректим в админку
    // header('Location: /admin.php');
}

updateStatus($pdo);