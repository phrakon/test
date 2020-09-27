<?php

use app\models\AdminPost;
use core\Html;
use core\View;

/** @var View $this */
/** @var AdminPost[] $posts */
/** @var bool $sort */

$this->title = 'Отзывы';
?>

<div class="container">

    <div class="row">

        <div class="col-xs-12">
            <?php if ($sort) { ?>
                <a href="/index.php?r=admin/index&sort=0">сначала новые</a>
            <?php } else { ?>
                <a href="/index.php?r=admin/index&sort=1">сначала старые</a>
            <?php } ?>
            <hr>
        </div>

        <div class="col-xs-12">

            <table class="table table-stripped">
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Дата</th>
                    <th>Текст</th>
                    <th></th>
                </tr>

                <?php foreach ($posts as $post) { ?>
                    <tr>
                        <td><?= $post->id ?></td>
                        <td>
                            <?php if ($post->image) { ?>
                                <img src="<?= $post->getImage() ?>" width="64" alt="">
                            <?php } ?>
                        </td>
                        <td><?= Html::encode($post->name) ?></td>
                        <td><?= Html::encode($post->email) ?></td>
                        <td><?= $post->getDate() ?></td>
                        <td><?= Html::encode($post->text) ?></td>
                        <td>
                            <a href="index.php?r=admin/toggle&id=<?= $post->id ?>">
                                <?= $post->is_hidden ? 'принять' : 'отклонить' ?>
                            </a>
                            <a href="index.php?r=admin/update&id=<?= $post->id ?>">изменить</a>
                        </td>
                    </tr>
                <?php } ?>
            </table>

        </div>

    </div>
</div>