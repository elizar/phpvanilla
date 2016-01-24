<?php
require_once './app.php';

$path_info      = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';             // defaults to '/'
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'; // defaults to 'get'

// Init app like a bitch!
$app = new App();

// Overrides default not found handler
$app->notFoundHandler = function () {
    echo 'Not Found Bitches!';
};

// Adds routes
$app->get('/hello/:name', function ($params) {
        echo 'Hello, ' . ucfirst($params['name']);
    })
    ->get('/hello/world/asd', function () {
        echo 'hello world asd';
    })
    ->get('/', function () {
        header('content-type: application/json');
        echo json_encode($_SERVER);
    })
    ->run($request_method, $path_info);
