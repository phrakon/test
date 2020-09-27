<?php

namespace core;

use app\models\User;
use Exception;
use ReflectionMethod;
use ReflectionParameter;

/**
 * Class Application
 * @package core
 */
class Application extends BaseObject
{
    /**
     * mydomain.com/index.php?r=post/update&id=100
     */
    public function run()
    {
        session_start();

        $route = !empty($_GET['r']) ? $_GET['r'] : 'post/index';

        if (strpos($route, '/') !== false) {
            [$controllerName, $actionName] = explode('/', $route);
        } else {
            $controllerName = $route;
        }

        if (empty($controllerName)) {
            throw new Exception('No controller');
        }

        if (!isset($actionName)) {
            $actionName = 'index';
        }

        $controllerClass = 'app\\controllers\\' . ucfirst($controllerName) . 'Controller';

        if (!class_exists($controllerClass)) {
            throw new Exception("Class {$controllerClass} does not exist");
        }

        $controller = new $controllerClass($this);

        $actionMethod = 'action' . ucfirst($actionName);

        if (!method_exists($controller, $actionMethod)) {
            throw new Exception("{$controllerClass} has no method {$actionMethod}");
        }

        $args = [];

        foreach ((new ReflectionMethod($controllerClass, $actionMethod))->getParameters() as $parameter) {

            $value = $_GET[$parameter->name] ?? null;

            if ($value === null) {
                $refParameter = new ReflectionParameter([$controllerClass, $actionMethod], $parameter->name);
                if ($refParameter->isOptional()) {
                    $value = $refParameter->getDefaultValue();
                } else {
                    throw new Exception("No required parameter {$parameter->name}");
                }
            }

            $args[$parameter->name] = $value;
        }

        ob_start();

        $result = call_user_func_array([$controller, $actionMethod], $args);

        $buffer = ob_get_clean();

        header_remove();

        foreach ($this->_headers as $name => $value) {
            if ($value === null) {
                header($name);
            } else {
                header("{$name}: {$value}");
            }
        }

        http_response_code($this->_status);

        echo $result;
    }

    public $identityParam = '__identity';

    /**
     * @var IdentityInterface
     */
    private $_identity;

    /**
     * @param IdentityInterface|null $user
     */
    public function setIdentity($user = null)
    {
        $this->_identity = $user;
        if ($user instanceof IdentityInterface) {
            $_SESSION[$this->identityParam] = $user->getId();
        } else {
            unset($_SESSION[$this->identityParam]);
        }
    }

    /**
     * @return IdentityInterface|null
     */
    public function getIdentity()
    {
        if ($this->_identity === null) {
            if (isset($_SESSION[$this->identityParam])) {
                $this->_identity = User::findOne($_SESSION[$this->identityParam]);
            }
        }
        return $this->_identity;
    }

    /**
     * @var array
     */
    private $_headers = [];

    /**
     * @param string $name
     * @param string|null $value
     */
    public function addHeader($name, $value = null)
    {
        $this->_headers[$name] = $value;
    }

    /**
     * @var int
     */
    private $_status = 200;

    public function setStatusCode($code)
    {
        $this->_status = $code;
    }
}
