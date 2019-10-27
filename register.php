<?php
// Подключение файла соединения с БД
require_once('db.php');

// старт сессии
session_start();

// заголовок страницы
$title = 'Регистрация';

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

/**
 * Регистрация нового пользователя
 *
 * @param [object] $pdo
 * @return void
 */
function userRegister($pdo) {

    if (empty($_POST)) {
        return false;
    }

    // получение данных с полей
    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));
    $password_confirmation = trim(htmlspecialchars($_POST['password_confirmation']));

    $password_hash = password_hash($password, PASSWORD_DEFAULT); // шифрование пароля

    $validation = true; // статус валидации
    $messages = [];     // массив для флеш-сообщений

    // записываем данные полей в сессионную переменную для автозаполнения
    $_SESSION['fieldData']['name'] = $name;
    $_SESSION['fieldData']['email'] = $email;
    $_SESSION['fieldData']['password'] = $password;

    // правила валидации полей формы и добавление сообщений для вывода под полями
    if (empty($name)) {
        $validation = false;
        $messages['errors']['name'] = 'Введите имя!';
    }

    // валидация полей
    $email_exist = checkEmail($pdo, $email);
    if ($email_exist) {
        $validation = false;
        $messages['errors']['email'] = 'Введенный вами email уже существует!';
    }
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
    if (empty($password_confirmation)) {
        $validation = false;
        $messages['errors']['password_confirmation'] = 'Подтвердите пароль!';
    }
    if (!empty($password) && !empty($password_confirmation) && $password != $password_confirmation) {
        $validation = false;
        $messages['errors']['password'] = 'Пароли не совпадают!';
    }
    
    // если поля прошли валидацию
    if ($validation == true) {
        // формируем sql-запрос 
        $sql = "INSERT INTO users 
                (name, email, password) 
                VALUES (:name, :email, :password)";

        // подготавливаем sql-запрос 
        $stmt = $pdo->prepare($sql);

        // связываение параметров
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);

        // выполнение запроса
        $stmt->execute();

        // добавление флеш-сообщения
        $messages['success'] = 'Вы были успешно зарегистрированы на сайте!';

        // создаем массив данных пользователя
        $userData['name'] = $name;
        $userData['email'] = $email;
        $userData['password'] = $password;
        $userData['is_login'] = true;

        // создаем сессию для хранения данных пользователя
        $_SESSION['user'] = $userData;

        // заносим массив флеш-сообщений в сессию
        $_SESSION['messages'] = $messages;
    }

    $data = [
        'messages' => $messages,
    ];
    // отправляем данные
    echo json_encode($data);
    die;
}

/**
 * Проверка уже существующего email в базе
 *
 * @param [object] $pdo
 * @param [string] $email
 * @return boolean
 */
function checkEmail($pdo, $email) {
    // Выбор всех полей email в базе
    $sql = "SELECT email FROM users";
    // выполнение запроса
    $stmt = $pdo->query($sql);

    // проверка соответствия введенного email с другими
    while ($row = $stmt->fetch()) {
        if ($row['email'] == $email) return true;
    }

    return false;
}

// если пользователь нажал кнопку зарегистрироваться
if (isset($_POST['submit'])) {
    // вызов функции регистрации
    userRegister($pdo);
}
?>

<?php require_once('includes/header.php'); ?>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="card border-secondary">
                            <div class="card-header">Регистрация</div>

                            <div class="card-body">
                                <form method="POST" action="">

                                    <div class="form-group row">
                                        <label for="exampleFormControlInputname" class="col-md-4 col-form-label text-md-right">Имя</label>

                                        <div class="col-md-6">
                                            <input id="exampleFormControlInputname" type="text" class="form-control" name="name" autofocus value="<?= $fieldData['name'] ?>" maxlength="15">
                                                
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleFormControlInputemail" class="col-md-4 col-form-label text-md-right">E-Mail адрес</label>

                                        <div class="col-md-6">
                                            <input id="exampleFormControlInputemail" type="text" class="form-control" name="email" value="<?= $fieldData['email'] ?>" maxlength="40">
                                            
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleFormControlInputpassword" class="col-md-4 col-form-label text-md-right">Пароль</label>

                                        <div class="col-md-6">
                                            <input id="exampleFormControlInputpassword" type="password" class="form-control" name="password"  autocomplete="new-password" value="<?= $fieldData['password'] ?>" maxlength="30">
                                            
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="exampleFormControlInputpassword_confirmation" class="col-md-4 col-form-label text-md-right">Пароль еще раз</label>

                                        <div class="col-md-6">
                                            <input id="exampleFormControlInputpassword_confirmation" type="password" class="form-control" name="password_confirmation"  autocomplete="new-password" maxlength="30">
                                            
                                            <span class="invalid-feedback" role="alert" style="display: none;">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary" id="register">
                                                Зарегистрироваться
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
