<?php
require_once './app.php';


// Init app like a bitch!
$app = new App();

// Overrides default not found handler
// $app->notFoundHandler = function () {
//     echo 'Not Found Bitches!';
// };


$app
    ->get('/sayhi/:name', function ($params) {
        echo 'Hi, ' . $params['name'];
    })
    ->run();
