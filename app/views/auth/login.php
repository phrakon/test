<?php

use core\Html;

/** @var \core\View $this */
/** @var \app\models\LoginForm $model */

$this->title = 'Аутентификация';
?>

<div class="container">

    <?php if ($model->hasErrors()) { ?>
        <div class="alert alert-warning" role="alert">
            <strong>Ошибка!</strong> <?= $model->getFirstError() ?>
        </div>
    <?php } ?>

    <form class="form-signin" method="post">
        <h2 class="form-signin-heading"><?= Html::encode($this->title) ?></h2>
        <label for="inputLogin" class="sr-only">Логин</label>
        <input type="text" name="name" id="inputLogin" class="form-control" value="<?= Html::encode($model->name) ?>"
               placeholder="Имя" required autofocus> <br>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Пароль" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Вход</button>
    </form>
</div>

