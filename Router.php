<?php
/**
 * Created by IntelliJ IDEA.
 * User: LocalAdmin
 * Date: 2/23/2019
 * Time: 4:36 PM
 */
class Router
{
    public static function parse($url) {
        global $ROUTES;

        $request = new stdClass;
        $url = ltrim(rtrim($url, '/'), '/');

        $pattern = false;

        foreach ($ROUTES as $p => $metadata) {
            if(preg_match($p, $url, $matches)) {
                $pattern = $p;
            }
        }

        if(!$pattern) {
            die('NOT FOUND'); //TODO: Handle 404
        }

        $pathMetadata = $ROUTES[$pattern];

        if($_SERVER['REQUEST_METHOD'] !== $pathMetadata['method']) {
            die("URL does not support the request method"); //TODO: Handle 405
        }

        $segments = explode('/', $url);
        $paramValues = [];

        foreach ($pathMetadata['params'] as $v) {
            $val = $segments[$v['index']];
            $paramValues[] = ctype_digit($val) ? intval($val) : $val;
        }

        $request->controller = $pathMetadata['controller'];
        $request->action = $pathMetadata['action'];
        $request->params = $paramValues;

        return $request;
    }
}