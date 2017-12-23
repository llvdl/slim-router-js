<?php

require_once __DIR__ . '/../vendor/autoload.php';


//var_dump($_SERVER["REQUEST_URI"]);

$app = new \Slim\App();

// set a base path for absolute URLs
$app->add(function ($request, $response, $next) use ($app) {
    $app->getContainer()->get('router')->setBasePath('https://example.com');

    return $next($request, $response);
});

// Add router javascript
$app->get('/router.js', function ($req, $res, $args) {
    $routerJs = new \Llvdl\Slim\RouterJs($this->router);
    return $routerJs->getRouterJavascriptResponse();
});

// Add a named route
$app->get('/hello/{name}', function ($req, $res, $args) {
    return 'hello ' . htmlspecialchars($args['name']);
})->setName('hello');

// Add a named routes within a group
$app->group('/users/{id:[0-9]+}', function () {
    $this->map(['GET', 'DELETE', 'PATCH', 'PUT'], '', function ($request, $response, $args) {
        // Find, delete, patch or replace user identified by $args['id']
    })->setName('user');
    $this->get('/reset-password', function ($request, $response, $args) {
        // Route for /users/{id:[0-9]+}/reset-password
        // Reset the password for user identified by $args['id']
    })->setName('user-password-reset');
});

// Add a route to a HTML that calls the Slim.Router object in Javascript
$app->get('/', function ($req, $res) {
    return file_get_contents(__DIR__ . '/home.html');
});

$app->run();
