<?php

namespace app\controllers;

use app\models\AdminPost;
use app\models\Post;
use core\Application;

class AdminController extends \core\Controller
{
    /**
     * AdminController constructor.
     * @param Application $app
     * @param array $config
     */
    public function __construct($app, $config = [])
    {
        parent::__construct($app, $config);

        if (!$this->app->getIdentity() || !$this->app->getIdentity()->getIsAdmin()) {
            $this->exit(403, 'Access Denied');
        }
    }

    /**
     * Список отзывов
     * @param int $sort
     * @return string
     */
    public function actionIndex($sort = 0)
    {
        $sortDirection = $sort == 1 ? 'ASC' : 'DESC';

        return $this->render('index', [
            'posts' => Post::find(['orderBy' => "created_at {$sortDirection}"]),
            'sort' => (bool)$sort,
        ]);
    }

    /**
     * Обновить отзыв
     * @param int $id
     * @return string|void
     */
    public function actionUpdate($id)
    {
        $model = AdminPost::findOne($id);

        if (!$model) {
            $this->exit(404, 'Not found');
        }

        if ($model->load($_POST)) {
            if ($model->save()) {
                return $this->redirect('/index.php?r=admin/index');
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Переключить состояние отзыва: видимый/невидимый
     * @param int $id
     */
    public function actionToggle($id)
    {
        $post = AdminPost::findOne($id);

        if (!$post) {
            $this->exit(404, 'Not found');
        }

        $post->toggle();

        return $this->redirect('/index.php?r=admin/index');
    }
}