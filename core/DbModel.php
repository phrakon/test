<?php

namespace core;

use PDO;

class DbModel extends Model
{
    /**
     * @var array
     */
    private $_attributes;
    /**
     * @var PDO
     */
    private static $_db;

    /**
     * @return string
     */
    public static function tableName()
    {
        $ref = new \ReflectionClass(static::class);
        return strtolower($ref->getShortName());
    }

    /**
     * @return PDO
     */
    protected static function getDB()
    {
        if (static::$_db === null) {
            throw new \Exception("You must config database");
        }
        return static::$_db;
    }

    /**
     * @param array $config
     */
    public static function setDb($config)
    {
        $dsn = "mysql:dbname=$config[dbname];host=$config[dbhost];charset=utf8";
        static::$_db = new PDO($dsn, $config['dbuser'], $config['dbpass']);
        static::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * SomeModel::find([
     *  'where' => ['id' => 10, ['>', 'created_at', 0],
     *  'orderBy' => 'name',
     *  'limit' => 10
     * ])
     * @param mixed $config
     * @return static[]
     */
    public static function find($config = [])
    {
        if (is_scalar($config)) {
            $config = ['where' => $config];
        }

        if (isset($config['where'])) {
            $where = $config['where'];
            $bindWhere = [];
            if (is_scalar($where)) {
                $pks = (new static())->primaryKey();
                if (count($pks) > 1) {
                    return [];
                }
                $where = [reset($pks) => $where];
            }
            foreach ($where as $k => &$item) {
                if (is_scalar($item)) {
                    $bindWhere[':' . $k] = $item;
                    $item = "$k = :$k";
                } elseif (is_array($item)) {
                    if (!isset($item[0], $item[1], $item[2])) {
                        unset($item);
                        continue;
                    }
                    [$operator, $key, $value] = $item;
                    $bindWhere[':' . $key] = $value;
                    $item = "$key $operator :$key";
                }
            }
            unset($item);
        }

        if (isset($config['orderBy'])) {
            $orderBy = $config['orderBy'];
            if (is_scalar($orderBy)) {
                $orderBy = [$orderBy];
            }
        }

        $db = static::getDB();
        $table = static::tableName();

        $query = "SELECT * FROM $table";

        if (isset($where)) {
            $query .= " WHERE " . implode(' AND ', $where);
        }

        if (isset($orderBy)) {
            $query .= " ORDER BY " . implode(',', $orderBy);
        }

        if (isset($config['limit'])) {
            $limit = (int)$config['limit'];
            $query .= " LIMIT $limit";
        }

        $statement = $db->prepare($query);

        if (isset($bindWhere)) {
            foreach ($bindWhere as $k => $v) {
                $statement->bindValue($k, $v);
            }
        }

        $statement->execute();

        $models = [];

        while ($model = $statement->fetchObject(static::class)) {
            $models[] = $model;
        }

        return $models;
    }

    /**
     * @param mixed $condition
     * @return static|null
     */
    public static function findOne($condition)
    {
        $models = static::find($condition);
        return $models ? reset($models) : null;
    }

    /**
     * поддерживается только простой, AI pk
     * @return array
     */
    final public function primaryKey()
    {
        return ['id'];
    }

    /**
     * названия колонок из таблицы
     * @return array
     */
    protected function getSchemaAttributes()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function attributes()
    {
        $attributes = $this->getSchemaAttributes();
        return array_merge(parent::attributes(), $attributes);
    }

    /**
     * новая, если первичный ключ пустой
     * @return bool
     */
    public function getIsNewRecord()
    {
        foreach ($this->primaryKey() as $pk) {
            if ($this->$pk === null) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param bool $runValidation
     * @return false
     */
    public function save($runValidation = true)
    {

        if ($runValidation && !$this->validate()) {
            return false;
        }

        $db = static::getDB();
        $table = static::tableName();

        foreach ($this->getSchemaAttributes() as $attribute) {
            if (in_array($attribute, $this->primaryKey())) {
                continue;
            }

            if ($this->$attribute === null) {
                continue;
            }

            $set[$attribute] = $attribute . ' = ' . ':' . $attribute;
        }

        if ($this->getIsNewRecord()) {
            $query = "INSERT INTO $table SET " . implode(',', $set);
        } else {
            $pk = implode(' AND ', array_map(fn($item) => $item . ' = ' . $this->$item, $this->primaryKey()));
            $query = "UPDATE $table SET " . implode(',', $set) . " WHERE " . $pk;
        }

        $statement = $db->prepare($query);

        foreach ($set as $k => $v) {
            $statement->bindValue(':' . $k, $this->$k);
        }

        $success = $statement->execute();

        if ($success) {
            foreach ($this->primaryKey() as $pk) {
                $this->$pk = static::getDB()->lastInsertId();
            }
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     */
    public function __get($name)
    {
        if (in_array($name, $this->getSchemaAttributes())) {
            return $this->_attributes[$name] ?? null;
        }
        return parent::__get($name);
    }

    /**
     * {@inheritDoc}
     */
    public function __set($name, $value)
    {
        if (in_array($name, $this->getSchemaAttributes())) {
            $this->_attributes[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }
}
