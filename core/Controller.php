<?php

namespace core;

/**
 * @property View $view
 */
class Controller extends BaseObject
{
    /**
     * @var string
     */
    public $layout = 'bootstrap3';
    /**
     * @var Application
     */
    public $app;

    /**
     * Controller constructor.
     * @param Application $app
     * @param array $config
     */
    public function __construct($app, $config = [])
    {
        $this->app = $app;
        parent::__construct($config);
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     */
//    public function __call($name, $args)
//    {
//        $actionHandler = 'action' . ucfirst($name);
//
//        if (!method_exists($this, $actionHandler)) {
//            throw new \Exception();
//        }
//
//        return call_user_func_array([$this, $actionHandler], $args);
//    }

    /**
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render($view, $params = [])
    {
        return $this->view->render($view, $params, $this);
    }

    private $_view;

    /**
     * @return View
     */
    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = new View();
        }

        return $this->_view;
    }

    public function goHome()
    {
        $this->redirect('index.php');
    }

    /**
     * @param string $url
     */
    public function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    /**
     * @param int $code
     * @param string $message
     */
    public function exit($code = 200, $message = '')
    {
        http_response_code($code);
        echo $message;
        exit;
    }
}
