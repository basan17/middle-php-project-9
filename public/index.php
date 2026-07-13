<?php

// Подключение автозагрузки через composer
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Valitron\Validator;

// Старт PHP сессии
session_start();

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    return new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    $messages = $this->get('flash')->getMessages();
    $params = ['flash' => $messages];
    return $this->get('renderer')->render($response, 'base.phtml', $params);
})->setName('homepage');

$app->post('/', function ($request, $response) {
    $url = $request->getParsedBodyParam('url');

    $v = new Validator(array('url' => $url));
    $v->rules([
        'lengthMax' => [
            ['url', 255]
        ],
        'required' => [
            ['url']
        ],
        'url' => [
            ['url']
        ]
    ]);
    if($v->validate()) {
        $this->get('flash')->addMessage('success', 'Url was successfully added!');
    } else {
        $this->get('flash')->addMessage('error', 'Url is not valid!');
    }
    $errors = $v->errors() || [];
    $params = [
        'url' => $url,
        'errors' => $errors
    ];
    // Redirect
    return $response->withStatus(302)->withRedirect('/');
    //return $this->get('renderer')->render($response, "urls.phtml", $params);
});

$app->run();
