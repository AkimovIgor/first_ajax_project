<?php

require_once('db.php');
require_once('functions.php');

session_start();

$title = 'Авторизация';

// если сессия с данными пользователя не существует
if (!isset($_SESSION['user'])) {
    // если существуют куки с данными
    if (isset($_COOKIE['user'])) {
        $isLogin = $_COOKIE['user']['is_login'];
        $name = $_COOKIE['user']['name'];
        $email = $_COOKIE['user']['email'];
    }
    
} else {
    $isLogin = $_SESSION['user']['is_login'];
    $name = $_SESSION['user']['name'];
    $email = $_SESSION['user']['email'];
}
// переменная для автозаполнения полей
    if (isset($_COOKIE['user'])) {
        $fieldData = $_COOKIE['user'];
    } else {
        $fieldData = $_SESSION['fieldData'];
    }
function userLogin($pdo) {
    
    if (empty($_POST)) {
        return false;
    }

    // получение данных из полей
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    // статус чекбокса "Запомнить меня"
    $rememberMe = $_POST['remember'] === 'true' ? 1 : 0;

    $validation = true; // статус валидации
    $messages = [];     // массив для флеш-сообщений
    
    // создаем сессию для автозаполнения полей
    $_SESSION['fieldData']['email'] = $email;
    $_SESSION['fieldData']['password'] = $password;

    // валидация для корректного ввода email
    if (!preg_match("/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/i", $email)) {
        $validation = false;
        $messages['errors']['email'] = 'Введенный вами email не соответствует формату!';
    }
    if (empty($email)) {
        $validation = false;
        $messages['errors']['email'] = 'Введите email!';
    }
    if (strlen($password) < 6 ) {
        $validation = false;
        $messages['errors']['password'] = 'Минимальная длина пароля 6 символов';
    }
    if (empty($password)) {
        $validation = false;
        $messages['errors']['password'] = 'Введите пароль!';
    }

    // если поля прошли валидацию
    if ($validation == true) {

        // формируем sql-запрос 
        $sql = "SELECT * 
                FROM users 
                WHERE email = '$email'
                LIMIT 1";

        // выполнение запроса
        $stmt = $pdo->query($sql);

        // получение данных
        $row = $stmt->fetch();

        $password_unhash = password_verify($password, $row['password']); // дешифрование пароля
        
        // если пользователь существует в базе данных
        if ($row['email'] && $password_unhash) {
            // добавление флеш-сообщения
            $messages['success'] = 'Вход успешно выполнен!';
            
            //dd($rememberMe);
            // стоит галочка "Запомнить меня"
            if ($rememberMe) {
                
                setcookie('user[name]', $row['name'], time() + 60 * 2);
                setcookie('user[email]', $email, time() + 60 * 2);
                setcookie('user[password]', $password, time() + 60 * 2);
                setcookie('user[is_login]', true, time() + 60 * 2);
            } else {
                // создаем массив данных пользователя
                $userData['name'] = $row['name'];
                $userData['email'] = $row['email'];
                $userData['password'] = $row['password'];
                $userData['is_login'] = true;
                // создаем сессию для хранения данных пользователя
                $_SESSION['user'] = $userData;
            }

            $messages['success'] = 'Вход в учетную запись успешно выполнен!';

        } else {
            if (!$row['email']) {
                $messages['errors']['email'] = 'Неверный email!';
            }
            if (!$password_unhash) {
                $messages['errors']['password'] = 'Неверный пароль!';
            }
        }
    }
    
    // заносим массив флеш-сообщений в сессию
    $_SESSION['messages'] = $messages;


    $data = [
        'messages' => $messages,
        'SESSION' => $_SESSION,
        'COOKIE' => $_COOKIE,
        'remember' => $rememberMe,
    ];
    // уничтожение сессий
    unset($_SESSION['messages']);
    unset($_SESSION['fieldData']);

    echo json_encode($data);
    die;
}

if (isset($_POST['submit'])) {
    

    // данные пользователя
    $user =  $_SESSION['user'];
    

    userLogin($pdo);
}



?>

<?php require_once('includes/header.php'); ?>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">Вход в учетную запись</div>

                            <div class="card-body">
                                <form method="POST" action="">

                                    <div class="form-group row">
                                        <label for="exampleFormControlInputemail" class="col-md-4 col-form-label text-md-right">E-Mail адрес</label>

                                        <div class="col-md-6">
                                            <input id="exampleFormControlInputemail" class="form-control" name="email"  autocomplete="email" autofocus value="<?= $fieldData['email']; ?>">
                                            
                                                <span class="invalid-feedback" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleFormControlInputpassword" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                        <div class="col-md-6">
                                            <input id="exampleFormControlInputpassword" type="password" class="form-control" name="password"  autocomplete="current-password" value="<?= $fieldData['password']; ?>">
                                            
                                                <span class="invalid-feedback" role="alert" style="display: none;">
                                                    <strong></strong>
                                                </span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-6 offset-md-4">



                                            <!-- <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" name="remember" id="exampleFormControlInputremember" >
                                                <label class="custom-control-label" for="exampleFormControlInputremember">Запомнить меня</label>
                                            </div> -->

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="exampleFormControlInputremember" >
                                                <label class="custom-control-label" for="exampleFormControlInputremember">Запомнить меня</label>
                                            </div>

                                            <!-- <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember" id="exampleFormControlInputremember" >

                                                <label class="form-check-label" for="exampleFormControlInputremember">
                                                    Запомнить меня
                                                </label>
                                            </div> -->
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-8 offset-md-4">
                                            <button type="submit" class="btn btn-primary" id="login">
                                               Войти
                                            </button>
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
</body>
</html>
