<?php

use core\Html;

/** @var \core\View $this */
/** @var string $layout */
/** @var string $content */
/** @var \core\IdentityInterface $identity */
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/main.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title><?= Html::encode($this->title) ?></title>
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/index.php?r=post/index">Главная</a></li>
                <?php if ($identity) { ?>
                    <?php if ($identity->getIsAdmin()) { ?>
                        <li><a href="/index.php?r=admin/index">Админка</a></li>
                    <?php } ?>
                    <li><a href="/index.php?r=auth/logout">Выход</a></li>
                <?php } else { ?>
                    <li><a href="/index.php?r=auth/login">Вход</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<?= $content ?>

</body>
</html>
