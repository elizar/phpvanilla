<?php

require_once './app.php';

$app = new App();

$app->get('/beep/boop', function () {
    echo 'beep boop';
});

$app->get('/hello/garci', function () {
    echo 'hello garci';
});

$app->get('/hello/world/asd', function () {
    echo 'hello world asd';
});

$app->get('/hello/:name/age/:age', function ($params) {
    echo 'Hello ' . $params['name'] . ', you are ' . $params['age'] . ' years old.';
});

$app->get('/', function () use ($app) {
    header('content-type: application/json');
    echo json_encode($app->getRoutes());
});

$path_info      = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';             // defaults to '/'
$request_method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'get'; // defaults to 'get'

$app->run($request_method, $path_info);