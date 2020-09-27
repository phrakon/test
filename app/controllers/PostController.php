<?php

namespace app\controllers;

use app\models\Post;

class PostController extends \core\Controller
{
    /**
     * Список отзывов
     * @param int $sort
     * @return string|void
     */
    public function actionIndex($sort = 0)
    {
        $model = new Post();

        if ($model->load($_POST)) {
            $model->file = $_FILES['file'] ?? null;
            if ($model->save()) {
                return $this->goHome();
            }
        }

        $sortDirection = $sort == 1 ? 'ASC' : 'DESC';

        return $this->render('index', [
            'model' => $model,
            'posts' => Post::find([
                'where' => [
                    'is_hidden' => 0,
                ],
                'orderBy' => "created_at {$sortDirection}",
            ]),
            'sort' => (bool)$sort,
        ]);
    }
}
