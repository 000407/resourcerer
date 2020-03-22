<?php

class Route {
    public static function get($path, $actionRef) {
        $path = APPROOT . "/$path";
        global $ROUTES;

        list($pathRegex, $params) = self::resolvePath($path);

        list($controller, $action) = self::resolveControllerAction($actionRef);

        $ROUTES[$pathRegex] = [
            'path' => $path,
            'method' => 'GET',
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    }

    public static function post($path, $actionRef) {
        $path = APPROOT . "/$path";
        global $ROUTES;

        list($pathRegex, $params) = self::resolvePath($path);

        list($controller, $action) = self::resolveControllerAction($actionRef);

        $ROUTES[$pathRegex] = [
            'path' => $path,
            'method' => 'POST',
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