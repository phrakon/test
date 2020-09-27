<?php

namespace core;

class View
{
    public $title;

    /**
     * @param string $view
     * @param array $params
     * @param Controller $controller
     * @return string
     */
    public function render($view, $params, $controller)
    {
        extract($params, EXTR_SKIP);
        $identity = $controller->app->getIdentity();

        $ref = new \ReflectionClass($controller);
        $controllerPath = substr(strtolower($ref->getShortName()), 0, -10,);

        $viewFile = dirname(__DIR__) . "/app/views/{$controllerPath}/{$view}.php";

        if (is_readable($viewFile)) {
            ob_start();
            require $viewFile;
            return $this->renderWrappedContent($controller->layout, ob_get_clean(), $controller);
        }
        throw new \Exception("View '{$view}' not found");
    }

    /**
     * @param string $layout
     * @param string $content
     * @param Controller $controller
     * @return string
     * @throws \Exception
     */
    public function renderWrappedContent($layout, $content, $controller)
    {
        if (!$layout) {
            return $content;
        }

        $layoutFile = dirname(__DIR__) . "/app/views/layouts/{$layout}.php";

        if (is_readable($layoutFile)) {
            $identity = $controller->app->getIdentity();
            ob_start();
            require $layoutFile;
            return ob_get_clean();
        }

        throw new \Exception("Layout '{$layout}' not found");
    }
}
