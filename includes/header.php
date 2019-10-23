<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= $title; ?></title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="./markup/css/app.css" rel="stylesheet">
    <link href="./markup/css/main.css" rel="stylesheet">
    
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/cerulean/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/cyborg/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/darkly/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/flatly/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/journal/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/litera/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/lumen/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/lux/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/materia/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/pulse/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/sandstone/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/simplex/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/sketchy/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/slate/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/solar/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/spacelab/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/superhero/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/united/bootstrap.min.css" rel="stylesheet"> -->
    <!-- <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.3.1/yeti/bootstrap.min.css" rel="stylesheet"> -->

<?php require_once('themes.php'); ?>
    
    
</head>
<body data-theme="<?= isset($_SESSION['theme']) ? $_SESSION['theme'] : 'spacelab'; ?>">
    <div id="app" data-page="/?page=1">
        <nav class="navbar sticky-top navbar-expand-md navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="markup/img/LRmNhq_N_400x400.png" width="30" height="30" class="d-inline-block align-top" alt="">
                    JavaScript
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
                        <?php if (isset($isLogin)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?= $name ?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink" style="right: 0px;left: -82px;">
                                <a class="dropdown-item" href="profile.php">Профиль</a>
                                
                                <a class="dropdown-item admin-control" href="admin.php" style="<?php if ($_SESSION['user']['name'] == 'admin' || $_COOKIE['user']['name'] == 'admin'): ?> display: block; <?php else: ?> display: none; <?php endif; ?>">Админ панель</a>
                                
                                <a class="dropdown-item" href="logout.php">Выход</a>
                                <div class="dropdown-divider"></div>
                                <div class="dropleft dropdown-submenu" data-toggle="dropdown" >
                                    <span class="dropdown-item" id="th">
                                        Темы
                                    </span>
                                    
                                    <div class="dropdown-menu dropdown-list">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?php foreach ($themeList as $theme): ?>
                                                    <a class="dropdown-item theme-item" href="change-theme.php/?theme=<?= $theme ?>"><?= ucfirst($theme) ?></a>
                                                    <?php if ($theme == 'sandstone'): ?>
                                                        </div><div class="col-md-6">
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php">Вход</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="register.php">Регистрация</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </nav>