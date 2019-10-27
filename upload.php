<?php
// подключение соединения с БД и функций
require_once('db.php');
require_once('functions.php');

// стартуем сессию
session_start();

// если пользователь не авторизован
if (!isset($_SESSION['user']['is_login']) && !isset($_COOKIE['user']['is_login'])) {
    header('Location: /'); // редирект на главную
    exit;
}

// если сессия с данными пользователя не существует
if (!isset($_SESSION['user'])) {
    // если существуют куки с данными
    if (isset($_COOKIE['user'])) {
        $name = $_COOKIE['user']['name'];
        $email = $_COOKIE['user']['email'];
    }
} else {
    $name = $_SESSION['user']['name'];
    $email = $_SESSION['user']['email'];
}

// email текущего пользователя
$currentUser = $email;

// получение статуса валидации из сессии
$validation = $_SESSION['validation'];

// если существует загружаемый файл
if (isset($_FILES['file'])) {

    // каталог для загружаемых файлов
    $uploadDir = __DIR__ . '/uploads/';

    // доступные форматы загружаемых файлов
    $availableFormats = ['jpg', 'jpeg', 'png', 'gif'];

    // получение формата загружаемого файла
    $format = mb_substr($_FILES['file']['name'], mb_strripos($_FILES['file']['name'], '.') + 1);

    // формируем имя файла
    $fileName = uniqid() . '.' . $format;

    // полный путь к месту назначения (папка uploads)
    $uploadFile = $uploadDir . $fileName;

    // валидация формата
    for($i = 0; $i < count($availableFormats); $i++) {
        if ($format === $availableFormats[$i]) {
            $isAvailable = true;
            break;
        } 
    }

    // если формат файла допустим и валидация данных прошла успешно
    if (isset($isAvailable) && $validation) {

        // формируем запрос
        $sql = "SELECT image 
            FROM users 
            WHERE email = :email 
            LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $currentUser]);
        
        // получаем картинку из базы
        $dbFileName = $stmt->fetch();
        
        // если такая картинка уже существует
        if (file_exists($uploadDir . $dbFileName['image'])) {
            // удалить старую картинку
            unlink($uploadDir . $dbFileName['image']);
            // загрузить новую картинку
            move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile);
        } else {
            // просто загрузить картинку
            move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile);
        }
    }

    // записываем данные в сессию
    $_SESSION['fileName'] = $fileName;
    $_SESSION['isAvailable'] = $isAvailable;
    $_SESSION['availableFormats'] = $availableFormats;

    
    // если валидация данных прошла успешно и формат файла допустим
    if ($validation && $_SESSION['isAvailable']) {
        // получаем картинку из сессии
        $image = $_SESSION['fileName'];

        // если картинка - заглушка, подключаем её с одной папки
        if ($image == 'no-user.jpg') {
            $image = 'markup/img/' . $image;
        } else {
            // иначе, загружать с другой папки
            $image = 'uploads/' . $image;
        } 

        $data['image'] = $_SESSION['fileName'];

        $sql = "UPDATE users 
                SET image = :image
                WHERE email = '$currentUser'";

        // подготовка запроса
        $stmt = $pdo->prepare($sql);
        // выполнение запроса
        $stmt->execute($data);
    }

    
    // данные для JSON (много лишнего для дебага)
    $rsp = array(
        'messages' => $messages,
        'userImage' => $image,
        'baseImage' => getUserImage($pdo,$email)['image'],
        'DATA' => $_SESSION['user'] ? $_SESSION : $_COOKIE['user'] ? $_COOKIE : null,
        'SESSION' => $_SESSION,
        'COOKIE' => $_COOKIE,
        'POST' => $_POST,
        'validation' => $validation,
        'fileName' => $_SESSION['fileName'],
        'email' => $email,
        'name' => $name,
        'currUser' => $currentUser,
        'SQL' => $sql
    );
    
    // удаляем ненужные сессии
    unset($_SESSION['validation']);
    unset($_SESSION['fileName']);
    unset($_SESSION['isAvailable']);
    unset($_SESSION['uploadDir']);
    unset($_SESSION['availableFormats']);
    unset($_SESSION['uploadFile']);

    // отправляем данные
    echo json_encode($rsp);
    exit;
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

/**
 * Проверка уже существующего email в базе
 *
 * @param [object] $pdo
 * @param [string] $email
 * @return boolean
 */
function checkEmail($pdo, $email, $currentUser) {
    // Выбор всех полей email в базе
    $sql = "SELECT email FROM users";
    // выполнение запроса
    $stmt = $pdo->query($sql);

    // проверка соответствия введенного email с другими
    while ($row = $stmt->fetch()) {
        if ($row['email'] == $email && $row['email'] != $currentUser) return true;
    }
    return false;
}