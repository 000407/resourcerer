<?php

class Route {
    private $prefix = NULL;

    public static function group(array $settings, Closure $callback) {
        extract($settings);
        $route = new Route;

        if(isset($prefix)) {
            $prefix = trim($prefix);
            $prefix = ltrim(rtrim($prefix, '/'), '/');
            $route->prefix = $prefix;
        }

        $callback->call($route);
    }

    public static function get($path, $actionRef, Route $route = NULL) {
        $route = $route ?? new Route;
        $route->generateRoute($path, $actionRef, 'GET');
    }

    public static function post($path, $actionRef, Route $route = NULL) {
        $route = $route ?? new Route;
        $route->generateRoute($path, $actionRef, 'POST');
    }

    private function generateRoute($path, $actionRef, $method) {
        global $ROUTES;

        if(isset($this->prefix)) {
            $prefix = $this->prefix;
            $path = APPROOT . "/$prefix/$path";
        }
        else {
            $path = APPROOT . "/$path";
        }

        list($pathRegex, $params) = self::resolvePath($path);

        list($controller, $action) = self::resolveControllerAction($actionRef);

        $ROUTES[$pathRegex] = [
            'path' => $path,
            'method' => $method,
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    }

    private static function resolveControllerAction($actionRef) {
        $actionRefArr = explode('@', $actionRef);
        $controller = $actionRefArr[0];
        $action = $actionRefArr[1];

        return array($controller, $action);
    }

    private static function resolvePath($path)
    {
        global $ROUTES;
        $paramPattern = '(\{[a-zA-Z][a-zA-Z-d]+\})';

        $path = ltrim(rtrim($path, '/'), '/');

        $params = [];

        foreach(explode('/', $path) as $i => $pathSeg) {
            if(preg_match($paramPattern, $pathSeg)) {
                $params[] = [
                    'index' => $i,
                    'name' => ltrim(rtrim($pathSeg, '}'), '{')
                ];
            }
        }

        $pathRegex = preg_replace_callback($paramPattern, function ($m) {
            return '[a-zA-Z\d]+';
        }, $path);

        $pathRegex = '/' . str_replace('/', '\/', $pathRegex) . '/';

        if (array_key_exists($pathRegex, $ROUTES)) {
            die('Duplicate route detected.');
        }
        return array($pathRegex, $params);
    }
}