<?php

namespace core;

use ReflectionClass;
use ReflectionProperty;

class Model extends BaseObject
{
    /**
     * @var array
     */
    private $_errors;

    /**
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Массовое присвоение атрибутов
     * @param mixed $data
     * @return bool
     */
    public function load($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        $attributes = $this->safeAttributes();

        foreach ($data as $name => $value) {
            if (in_array($name, $attributes)) {
                $this->$name = $value;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        $attributes = [];
        foreach ((new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $attributes[] = $property->getName();
        }
        return $attributes;
    }

    /**
     * атрибуты, которые могут быть массово присвоены методом load()
     * @return array
     */
    public function safeAttributes()
    {
        $attributes = [];
        foreach ($this->rules() as $rule) {
            if (isset($rule[0])) {
                $attrs = is_array($rule[0]) ? $rule[0] : [$rule[0]];
                foreach ($attrs as $attr) {
                    if($attr[0] === '!') {
                        continue;
                    }
                    if (!in_array($attr, $attributes)) {
                        $attributes[] = $attr;
                    }
                }
            }
        }
        return $attributes;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        $this->_errors = [];

        $attributes = $this->attributes();

        foreach ($this->rules() as $rule) {
            if (is_array($rule)) {
                if (isset($rule[0], $rule[1])) {
                    $attrs = is_array($rule[0]) ? $rule[0] : [$rule[0]];
                    $handler = $rule[1];

                    $params = array_slice($rule, 2);

                    foreach ($attrs as &$attr) {
                        if($attr[0] === '!') {
                            $attr = substr($attr, 1);
                        }
                        if (in_array($attr, $attributes)) {
                            if (is_callable($handler)) {
                                $handler($attr, $params);
                            } elseif (method_exists($this, $handler)) {
                                $this->$handler($attr, $params);
                            }
                        }
                    }
                }
            }
        }

        return !$this->hasErrors();
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return string
     */
    public function getFirstError()
    {
        $errors = $this->getErrors();
        $firstErrorByFirstAttribute = reset($errors);
        return reset($firstErrorByFirstAttribute);
    }

    /**
     * @param string $attribute
     * @param string $message
     */
    public function addError($attribute, $message)
    {
        $this->_errors[$attribute][] = $message;
    }

    //встроенная валидация

    /**
     * @param string $attribute
     * @param array $params
     */
    public function checkRequired($attribute, $params = [])
    {
        if (!$this->$attribute) {
            $this->addError($attribute, "Необходимо заполнить $attribute");
        }
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function checkDefault($attribute, $params = [])
    {
        if (!$this->$attribute && isset($params['value'])) {
            $this->$attribute = $params['value'];
        }
    }

    /**
     * @param string $attribute
     * @param array $params
     */
    public function checkTrim($attribute, $params = [])
    {
        $this->$attribute = trim($this->$attribute);
    }
}
