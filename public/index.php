<?php

// Подключение автозагрузки через composer
require_once __DIR__ . '/../vendor/autoload.php';

use App\Url;
use App\UrlRepository;
use App\UrlValidator;
use Slim\Factory\AppFactory;
use DI\Container;
use Carbon\Carbon;
use Valitron\Validator;

// Старт PHP сессии
session_start();

$container = new Container();
$container->set('renderer', function () {
    // Параметром передается базовая директория, в которой будут храниться шаблоны
    $renderer = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates', ['title' => 'Анализатор cтраниц']);
    $renderer->setLayout('layout.php');
    return $renderer; //new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');
});

$container->set(\PDO::class, function () {
    $conn = new \PDO('sqlite:database.sqlite');
    $conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    return $conn;
});

$initFilePath = implode('/', [dirname(__DIR__), 'database.sql']);
$initSql = file_get_contents($initFilePath);
$container->get(\PDO::class)->exec($initSql);

$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});
$app = AppFactory::createFromContainer($container);
$app->addErrorMiddleware(true, true, true);
$router = $app->getRouteCollector()->getRouteParser();

$app->get('/', function ($request, $response) {
    $messages = $this->get('flash')->getMessages();
    $oldUrl = $messages['old_url'][0] ?? '';

    $viewData = [
        'title' => 'Анализатор страниц - Главная',
        'flash' => $messages,
        'old_url' => $oldUrl
    ];
    return $this->get('renderer')->render($response, 'home.phtml', $viewData);
})->setName('homepage');

$app->post('/', function ($request, $response) use ($router) {
    $urlRepository = $this->get(UrlRepository::class);
    $url = $request->getParsedBodyParam('url');

    $validator = new UrlValidator();
    $errors = $validator->validate($url);
    
    if (empty($errors)) {
        $this->get('flash')->addMessage('success', 'Url was successfully added.');

        $url = Url::fromArray([$url, Carbon::now()]);
        $urlRepository->save($url);

        return $response->withRedirect($router->urlFor('urls.index'));
    } else {
        $this->get('flash')->addMessage('error', 'There was mistake in url.');
        $this->get('flash')->addMessage('old_url', $url);

        return $response->withRedirect($router->urlFor('homepage'));
    }
})->setName('urls.store');

$app->get('/urls', function ($request, $response) {
    $urlRepository = $this->get(UrlRepository::class);
    $urls = $urlRepository->getEntities();

    $messages = $this->get('flash')->getMessages();

    $viewData = [
        'title' => 'Анализатор страниц - Сайты',
        'urls' => $urls,
        'flash' => $messages
    ];

    return $this->get('renderer')->render($response, 'urls/index.phtml', $viewData);
})->setName('urls.index');

$app->get('/urls/new', function ($request, $response) {
    $params = [
        'url' => new Url(),
        'errors' => []
    ];

    return $this->get('renderer')->render($response, 'urls/new.phtml', $params);
})->setName('urls.create');

$app->get('/urls/{id}', function ($request, $response, $args) {
    $urlRepository = $this->get(UrlRepository::class);
    $id = $args['id'];
    $url = $urlRepository->find($id);

    if (is_null($url)) {
        return $response->write('Page not found')->withStatus(404);
    }

    $messages = $this->get('flash')->getMessages();

    $params = [
        'url' => $url,
        'flash' => $messages
    ];

    return $this->get('renderer')->render($response, 'urls/show.phtml', $params);
})->setName('urls.show');

$app->run();
