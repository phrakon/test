<?php

namespace app\controllers;

use app\models\LoginForm;
use app\models\User;

class AuthController extends \core\Controller
{
    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load($_POST)) {
            if ($model->validate()) {
                $this->app->setIdentity($model->getUser());
                return $this->goHome();
            }
        }
        return $this->render('login', ['model' => $model]);
    }

    public function actionLogout()
    {
        $this->app->setIdentity(null);
        return $this->goHome();
    }

    /**
     * Добавить пользователя
     * /index.php?r=auth/create&name=admin&password=123
     * @param string $name
     * @param string $password
     */
    public function actionCreate($name, $password)
    {
        $user = new User(['name' => $name, 'password' => $password]);
        $user->save();
    }
}
