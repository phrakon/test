<?php

namespace app\controllers;

use app\models\Post;

class ApiController extends \core\Controller
{
    /**
     * @var string
     */
    public $tokenKey = 'access_token';
    /**
     * @var string
     */
    public $token = 'aaabbbccc';

    /**
     * {@inheritDoc}
     */
    public function __construct($app, $config = [])
    {
        parent::__construct($app, $config);

        $this->app->addHeader("Content-Type: application/json; charset=UTF-8");
        /**
         * здесь так же можно проверять корректность пришедшего json, валидность токена и т.д.,
         * если другие экшены будут иметь подобный функционал
         */
    }

    /**
     * Добавить отзыв
     * http://test.local/index.php?r=api/create
     * @return false|string
     */
    public function actionCreate()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if ($data === null) {
            $out = ['message' => json_last_error_msg()];
        } else {
            if (!isset($data[$this->tokenKey]) || $data[$this->tokenKey] != $this->token) {
                $this->app->setStatusCode(403);
                $out = ['message' => 'Forbidden'];
                return json_encode($out);
            } else {
                unset($data[$this->tokenKey]);
                $post = new Post();
                if ($post->load($data)) {
                    if ($post->save()) {
                        $post = Post::findOne($post->id);
                        $attributes = [];
                        foreach ($post->attributes() as $attribute) {
                            $attributes[$attribute] = $post->$attribute;
                        }
                        $out = ['success' => 1, 'post' => $attributes];
                    } else {
                        $out = ['message' => 'An error occurred while processing your request'];
                    }
                } else {
                    $out = ['message' => 'No data provided'];
                }
            }
        }

        if (isset($out['message'])) {
            $this->app->setStatusCode(500);
        }

        return json_encode($out);
    }
}