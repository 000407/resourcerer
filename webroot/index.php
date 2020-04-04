<?php
/**
 * Created by IntelliJ IDEA.
 * User: LocalAdmin
 * Date: 2/23/2019
 * Time: 4:31 PM
 */
session_start();

$ROUTES = [];

require_once("./autoloaders.php");

require_once("const.php");

require_once(ROOT . "config/core.php");

require_once(ROOT . "config/routes/web.php");

require_once(ROOT . "Router.php");

$request = Router::parse($_SERVER["REQUEST_URI"]);

$controllerName = $request->controller;
$methodName = $request->action;
$params = $request->params;

$controller = new $controllerName();

try {
    call_user_func_array(array($controller, $methodName), $params);
} catch (Exception $e) {
    //TODO: Handle errors
}