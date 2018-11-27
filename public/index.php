<?php

namespace Tochka;

require __DIR__ . '/../vendor/autoload.php';

$tasks = new Task(1000);

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new \Slim\App($configuration);

$container = $app->getContainer();
$container['renderer'] = new \Slim\Views\PhpRenderer(__DIR__ . '/../templates');

$app->get('/', function ($request, $response) use ($tasks) {
    $params = [];
    $tasksList = [];
    $offset = 0;
    $limit = 10;
    $search = '';    
    
    if ($request->getParam('search')) {
        $search = htmlspecialchars($request->getParam('search'));
        $url = '/?search=' . $search . '&page={page}';    
    } else {
        $url = '/?page={page}';
    }

    if (!$request->getParam('page')) {
        $page = 1;        
    } else {
        $page = $request->getParam('page');
    }
    $offset = $limit * ($page - 1);

    $tasksList = $tasks->getTasks($offset, $limit, $search);

    $params['tasks'] = $tasksList;
    $params['search'] = $search;

    $pagination = new Pagination();
    $pagination->total = count($tasks->getTasks(0, -1,$search));
    $pagination->page = $page;
    $pagination->limit = $limit;
    $pagination->num_links = 5;
    $pagination->url = $url;
    $params['pagination'] = $pagination->render();
    return $this->renderer->render($response, 'index.phtml', $params);
});

$app->get('/api/v1/task', function ($request, $response) use ($tasks) {
    $tasksList = $tasks->getTasks();
    return $response->withJson($tasksList);
});
$app->get('/api/v1/task/{id}', function ($request, $response, array $args) use ($tasks) {
	$id = $args['id'];
    $task = $tasks->getTask($id);
    return $response->withJson($task);
});
$app->run();