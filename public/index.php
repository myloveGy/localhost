<?php

include '../vendor/autoload/autoload.php';

$config = include '../config/main.php';

// 解析请求路径
$request_uri = getValue($_SERVER, 'REQUEST_URI', '/');
$uri         = getValue(explode('?', $request_uri), 0, '/');
$uri         = $uri == '/index.php' ? '/' : $uri;
$uri         = $uri == '/' ? getValue($config, 'defaultUri') : $uri;
$params      = explode('/', trim($uri, '/'));
if (count($params) == 1) {
    array_push($params, 'index');
}

$action    = studlyCase(array_pop($params));
$class     = studlyCase(array_pop($params));
$class     .= 'Controller';
$action    = getValue($config, 'actionPrefix', '') . $action;
$namespace = '\\app\\controllers\\';
if ($params) {
    $namespace .= implode('\\', $params) . '\\';
}

$class = $namespace . $class;
if (class_exists($class) && method_exists($class, $action)) {
    (new $class)->$action();
}