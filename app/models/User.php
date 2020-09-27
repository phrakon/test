<?php

namespace app\models;

use core\DbModel;
use core\IdentityInterface;

/**
 * @property-read int $id
 * @property string $name
 * @property string $password_hash
 * @property bool $is_admin
 *
 * @property string $password
 */
class User extends DbModel implements IdentityInterface
{
    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            [['name', 'password'], 'checkRequired'],
            [['name'], 'checkName'],
        ];
    }

    /**
     * @param string $attribute
     */
    public function checkName($attribute)
    {
        $user = static::findByName($this->$attribute);

        if ($user) {
            $this->addError($attribute, 'Имя уже используется');
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getSchemaAttributes()
    {
        return ['id', 'name', 'password_hash', 'is_admin'];
    }

    /**
     * @param string $name
     * @return User
     */
    public static function findByName($name)
    {
        return static::findOne(['where' => ['name' => $name]]);
    }

    private $_password;

    /**
     * @param string $value
     */
    public function setPassword($value)
    {
        $this->_password = $value;
        $this->password_hash = password_hash($value, PASSWORD_DEFAULT, ['cost' => 13]);
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public static function findById($id)
    {
        return static::findOne($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getIsAdmin()
    {
        return $this->is_admin == 1;
    }
}
