<?php 
// старт сессии
session_start();

// подключение базы данных и функций
require_once('db.php');
require_once('functions.php');


// если пользователь не авторизован
if (!isset($_SESSION['user']['is_login']) && !isset($_COOKIE['user']['is_login'])) {
    header('Location: /'); // редирект на главную
    exit;
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
    $isLogin = $_SESSION['user'];
    $name = $_SESSION['user']['name'];
    $email = $_SESSION['user']['email'];
}

// заголовок страницы
$title = "Профиль";

// получение картинки пользователя из базы
$image = getUserImage($pdo,$email)['image'];


// если картинка заглушка, подключать из другого места
if ($image == 'no-user.jpg') {
    $image = 'markup/img/' . $image;
} else {
    // иначе, загружать с другой папки
    $image = 'uploads/' . $image;
}  


/**
 * Редактирование профиля
 *
 * @param [object] $pdo
 * @param [string] $email
 * @param [array] $user
 * @return void
 */
function changeProfile($pdo, $email, $user, $image, $name) {

    if (empty($_POST)) {
        return false;
    }

    // почта текущего пользователя
    $currentUser = $email;
    // имя текущего пользователя
    $currentName = $name;

    // получение данных с полей
    $name = trim(htmlentities($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $file = trim(htmlspecialchars($_POST['file']));

    $path_parts = mb_pathinfo($file);


    // статус валидации
    $validation = true;

    // доступные форматы загружаемых файлов
    $availableFormats = ['jpg', 'jpeg', 'png', 'gif'];

    // получение формата загружаемого файла
    $format = $path_parts['extension'];

    // валидация формата
    for($i = 0; $i < count($availableFormats); $i++) {
        if ($format === $availableFormats[$i]) {
            $isAvailable = true;
            break;
        } 
    }

    
    // валидация полей

    if (!empty($name) && empty($email)) {
        $email = $currentUser;
        $validation = true;
        $messages = [];
    }
    if (mb_strlen($name) < 4) {
        $validation = false;
        $messages['errors']['name'] = 'Длина имени должна содержать больше 4-х символов!';
    }
    if ((empty($name) && !empty($email)) || (!empty($name) && !empty($email))) {

        $name = empty($name) ? $currentName : $name;
        $validation = true;
        $messages = [];

        if (!empty($file)) {
            $validation = true;
            $messages = [];
        }
        // валидация для корректного ввода email
        if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $email)) {
            $validation = false;
            $messages = [];
            $messages['errors']['email'] = 'Введенный вами email не соответствует формату!';
        }
        // валидация на уже существующий email
        $email_exist = checkEmail($pdo, $email, $currentUser);
        if ($email_exist) {
            $validation = false;
            $messages['errors']['email'] = 'Введенный вами email уже занят!';
        }
    }
    if (empty($name) && empty($email)) {
        $email = $currentUser;
        $name = $currentName;
        $validation = false;
        $messages = [];
        $messages['errors']['name'] = 'Введите имя!';

        if (!empty($file)) {
            $validation = true;
            $messages = [];
        }
    }
    if (!isset($isAvailable) && !empty($file)) {
        $validation = false;
        $messages['errors']['file'] = 'Недопустимый формат файла! Допустимые форматы: ' . implode(', ', $availableFormats);
    }
    

    
    // формирование SET конструкции для выполнения запроса
    $set = '';
    $data = [];

    // данные для запроса
    $data['name'] = $name;
    $data['email'] = $email;

    
    // если поля прошли валидацию
    if ($validation == true) {

        // формируем SET конструкцию
        foreach ($data as $key => $val) {
            $set .= $key . ' = :' . $key . ', ';
        }
        $set = rtrim($set, ', ');

        // sql-запрос на обновление данных пользователя
        $sql = "UPDATE users 
                SET $set 
                WHERE email = '$currentUser'";


        // подготовка запроса
        $stmt = $pdo->prepare($sql);
        // выполнение запроса
        $stmt->execute($data);

        // освежить данные в сессиях и куки, удаляя их, а затем устанавливая заново
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            $userData['name'] = $name;
            $userData['email'] = $email;
            $userData['is_login'] = true;
            $_SESSION['user'] = $userData;
        }
        if(isset($_COOKIE['user'])) {
            setcookie('user[is_login]', '', time()-5);
            setcookie('user[name]', $name, time() + 60 * 2);
            setcookie('user[email]', $email, time() + 60 * 2);
            setcookie('user[is_login]', true, time() + 60 * 2);
        }

        // добавление флеш-сообщения
        $messages['success'] = 'Изменения сохранены!';
    }

    
    $image = getUserImage($pdo,$email)['image'];

    if ($image == 'no-user.jpg') {
        $image = 'markup/img/' . $image;
    } else {
        // иначе, загружать с другой папки
        $image = 'uploads/' . $image;
    } 
    
    // записываем в сессию статус валидации
    $_SESSION['validation'] = $validation;

    // данные для json (много лишнего для дебага)
    $data = [
        'messages' => $messages,
        'userImage' => $image,
        'baseImage' => getUserImage($pdo,$email)['image'],
        'DATA' => $_SESSION['user'] ? $_SESSION : $_COOKIE['user'] ? $_COOKIE : null,
        'SESSION' => $_SESSION,
        'COOKIE' => $_COOKIE,
        'POST' => $_POST,
        'validation' => $_SESSION['validation'],
        'fileName' => $_SESSION['fileName'],
        'availableFormats' => $_SESSION['availableFormats'],
        'email' => $email,
        'name' => $name,
        'currUser' => $currentUser,
        'SQL' => $sql
    ];
    
    // отправляем данные в json формате
    echo json_encode($data);
    die;
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

// вызов функции регистрации
if (isset($_POST['edit'])) {
    changeProfile($pdo, $email, $user, $image, $name);
}
?>

<?php require_once('includes/header.php'); ?>

        <main class="py-4">
          <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <!-- Вывод флеш-сообщения в случае успеха -->
                    <div class="alert alert-success" style="display: none;" role="alert">
                        <span class="text"></span>
                        <button type="button" class="close" aria-label="Close">
                            <span aria-hidden="true" <?php if ($_SESSION['theme'] == 'sketchy') echo "style='opacity: 0;'" ?>>&times;</span>
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-header" id="profile"><h3>Профиль пользователя</h3></div>

                        <div class="card-body">
                            

                            <form id="user_form" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="exampleFormControlInputname">Имя</label>
                                            <input type="text" class="form-control" name="name" id="exampleFormControlInputname" placeholder="<?= $name ?>" maxlength="20">
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleFormControlInputemail">Email</label>
                                            <input type="email" class="form-control" name="email" id="exampleFormControlInputemail" placeholder="<?= $email ?>" maxlength="40">
                                            
                                                <span class="invalid-feedback" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                            
                                        </div>

                                        <div class="form-group">
                                            
                                                <label for="exampleFormControlInputimage">Аватар</label>
                                                
                                                <div class="custom-file" style="overflow: hidden;">
                                                    <input type="file" class="custom-file-input" name="file" id="exampleFormControlInputfile">
                                                    <label class="custom-file-label" id="exampleFormControlInputfile">Выберите изображение</label>
                                                </div>

                                                <!-- <label for="exampleFormControlInputimage">Аватар</label>
                                                <input type="file" class="form-control" name="file" id="exampleFormControlInputfile"> -->
                                            
                                            
                                                <span class="invalid-feedback" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <img id="user-image" src="<?= $image ?>" alt="" class="img-fluid">
                                    </div>

                                    <div class="col-md-12">
                                        <button  id="edit" name="edit" class="btn btn-warning">Редактировать</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="card">
                        <div class="card-header" id="password"><h3>Безопасность</h3></div>

                        <div class="card-body">
                            <form action="password.php#password" method="post">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="exampleFormControlInputcurrent">Текущий пароль</label>
                                            <input type="password" name="current" class="form-control" id="exampleFormControlInputcurrent">
                                            
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong>></strong>
                                            </span>
                                            
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleFormControlInputpassword">Новый пароль</label>
                                            <input type="password" name="password" class="form-control" id="exampleFormControlInputpassword">
                                            
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>

                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>
                                        </div>

                                        <div class="form-group">
                                            <label for="exampleFormControlInputpassword_confirmation">Новый пароль еще раз</label>
                                            <input type="password" name="password_confirmation" class="form-control" id="exampleFormControlInputpassword_confirmation">
                                            
                                            <span class="invalid-feedback" role="alert">
                                                <strong></strong>
                                            </span>
                                        </div>

                                        <button type="submit" id="edit-passw" name="edit-passw" class="btn btn-success">Сохранить</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </main>
    </div>

    <?php require_once('includes/footer.php'); ?>

    <?php require_once('includes/scripts.php'); ?>

    <div class="loader">
        <div class="loader-image"><img src="markup/img/35.gif" alt=""></div>
    </div>
</body>
</html>
