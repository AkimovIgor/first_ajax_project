<?php session_start();

error_reporting(E_ALL);
ini_set('display_errors', 'on');
setlocale(LC_ALL, 'ru_RU.UTF-8');
#-------------------------------------------------
require_once 'ext/connect.php';

if(isset($_POST['name']) && isset($_POST['text'])) {
	$name = htmlentities(mysqli_real_escape_string($conn, $_POST['name']));
	$text = htmlentities(mysqli_real_escape_string($conn, $_POST['text']));

	$query = "INSERT INTO messages (name, text) VALUES ('$name', '$text')";
	$result = mysqli_query($conn, $query);

#	var_dump($result);
	$_SESSION['mess'] = $result;
}

$res = mysqli_query($conn, "SELECT * FROM messages ORDER BY date DESC");
$data = mysqli_fetch_all($res, MYSQLI_ASSOC );

?>

<!----== РАЗДЕЛ HTML ==---->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Comments</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="css/app.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="index.html">
                    Project
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                            <li class="nav-item">
                                <a class="nav-link" href="login.html">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.html">Register</a>
                            </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header"><h3>Комментарии</h3></div>

<!--=======================  ЗАБИРАЕМ ДАННЫЕ ИЗ БАЗЫ  =======================-->

                            <div class="card-body">
	                            <?php var_dump($_SESSION['mess']); ?>
                                <?php
								if ($_SESSION['mess']){
									$message = "Комментарий успешно добавлен";
									$_SESSION['info'] = $message;
									#setcookie('message', $message, time() + 2, '/');
								}
                                ?>
                              <div class="alert alert-success" role="alert">
                                  <?php echo $_SESSION['info'];
	                              session_destroy();
                                  header('Location: index.php');?>
                              </div>

<!--=======================  РАЗДЕЛ ВЫВОДА СООБЩЕНИЯ =======================-->
	                         <?php foreach ($data as $user): ?>
			                    <div class="media">
				                <img src="img/no-user.jpg" class="mr-3" alt="..." width="64" height="64">
		                            <div class="media-body">
			                            <h5 class="mt-0"><?php echo $user['name']; ?> </h5>
			                            <span><small><?php echo date("d/m/Y", strtotime($user['date'])); ?> </small></span>
			                            <p>
				                            <?php echo $user['text']; ?>
			                            </p>
		                            </div>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

<!--=======================  КОНЕЦ РАЗДЕЛА ВЫВОДА =======================-->
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="card">
                            <div class="card-header"><h3>Оставить комментарий</h3></div>

                                <div class="card-body">
                                    <form action="index.php" method="post">
                                    <div class="form-group">
                                        <label for="exampleFormControlTextarea1">Имя</label>
                                        <input name="name" class="form-control" id="exampleFormControlTextarea1" pattern="[A-Za-zА-Яа-яЁё]+(\s+[A-Za-zА-Яа-яЁё]+)?" placeholder="Имя Фамилия" required />
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleFormControlTextarea1">Сообщение</label>
                                        <textarea name="text" class="form-control" id="exampleFormControlTextarea1" rows="3" pattern="[\S]+" placeholder="Текст" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success">Отправить</button>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
            </div>
        </main>
    </div>
</body>
</html>
