<?php

use app\models\AdminPost;
use core\Html;
use core\View;

/** @var View $this */
/** @var AdminPost $model */

$this->title = 'Редактировать отзыв';
?>

<div class="container">

    <div class="row">
        <div class="col-sm-6">

            <?php if ($model->hasErrors()) { ?>
                <div class="alert alert-warning" role="alert">
                    <strong>Ошибка!</strong> <?= $model->getFirstError() ?>
                </div>
            <?php } ?>

            <form method="post" enctype="multipart/form-data">
                <h2 class="form-signin-heading">Редактировать отзыв</h2>
                <div class="form-group">
                    <label for="inputName" class="sr-only">Имя</label>
                    <input type="text" name="name" id="inputName" class="form-control"
                           value="<?= Html::encode($model->name) ?>" placeholder="Имя" required autofocus>
                </div>

                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Email</label>
                    <input type="text" name="email" id="inputEmail" class="form-control"
                           value="<?= Html::encode($model->email) ?>" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <label for="inputText" class="sr-only">Сообщение</label>
                    <textarea name="text" class="form-control" id="inputText" rows="10" placeholder="Сообщение"
                              required><?= $model->text ?></textarea>
                </div>

                <button class="btn btn-lg btn-primary btn-block" type="submit">Сохранить</button>
            </form>
        </div>
    </div>
</div>
