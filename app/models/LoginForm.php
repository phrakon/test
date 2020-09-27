<?php

namespace app\models;

class LoginForm extends \core\Model
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $password;

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['name', 'password'], 'checkRequired'],
            ['password', 'checkPassword'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function checkPassword($attribute)
    {
        if ($user = $this->getUser()) {
            if (password_verify($this->$attribute, $user->password_hash)) {
                return;
            }
        }

        $this->addError($attribute, 'Неверное Имя или Пароль');
    }

    /**
     * @var User
     */
    private $_user;

    /**
     * @return User
     */
    public function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByName($this->name);
        }
        return $this->_user;
    }
}
