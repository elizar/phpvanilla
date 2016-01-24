<?php
require_once './app.php';

$path_info      = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';             // defaults to '/'
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'; // defaults to 'get'

$app = new App();

$app->get('/hello/:name', function ($params) {
        echo 'Hello, ';
    })
    ->get('/hello/world/asd', function () {
        echo 'hello world asd';
    })
    ->get('/', function () {
        echo 'Home';
    })
    ->run($request_method, $path_info);
