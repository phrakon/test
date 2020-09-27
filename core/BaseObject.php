<?php

namespace core;

class BaseObject
{
    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $k => $v) {
                $this->$k = $v;
            }
        }
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        throw new \Exception();
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $method = 'set' . ucfirst($name);

        if (!method_exists($this, $method)) {
            throw new \Exception();
        }

        $this->$method($value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        $method = 'get' . ucfirst($name);

        if (method_exists($this, $method)) {
            return $this->$method() !== null;
        }

        return false;
    }
}
