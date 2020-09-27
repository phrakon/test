<?php

use app\models\Post;
use core\Html;
use core\View;

/** @var View $this */
/** @var Post[] $posts */
/** @var Post $model */
/** @var bool $sort */

$this->title = 'Отзывы';
?>

<div class="container">

    <div class="row">

        <?php if ($posts) { ?>
            <div class="col-xs-12">
                <?php if ($sort) { ?>
                    <a href="/index.php?r=post/index&sort=0">сначала новые</a>
                <?php } else { ?>
                    <a href="/index.php?r=post/index&sort=1">сначала старые</a>
                <?php } ?>
                <hr>
            </div>
        <?php } ?>

        <?php foreach ($posts as $post) { ?>
            <div class="col-xs-12">
                <strong><?= $post->name ?></strong> <?= Html::mailto($post->email) ?>
                <?php if ($post->is_updated) { ?>
                    <span class="label label-warning">изменен администратором</span>
                <?php } ?>
                <br>
                @<?= $post->getDate() ?><br>
                <?= nl2br(Html::encode($post->text)) ?>
                <?php if ($post->image) { ?>
                    <br><img src="<?= $post->getImage() ?>" alt="">
                <?php } ?>
                <hr>
            </div>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-sm-6">

            <?php if ($model->hasErrors()) { ?>
                <div class="alert alert-warning" role="alert">
                    <strong>Ошибка!</strong> <?= $model->getFirstError() ?>
                </div>
            <?php } ?>

            <form method="post" enctype="multipart/form-data">
                <h2 class="form-signin-heading">Добавить отзыв</h2>
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
                              required><?= Html::encode($model->text) ?></textarea>
                </div>

                <div class="form-group">
                    <input type="file" name="file" accept="image/jpeg,image/png">
                </div>

                <button class="btn btn-lg btn-primary btn-block" type="submit">Отправить</button>
            </form>
        </div>
    </div>
</div>
