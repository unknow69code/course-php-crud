<?php

$request = Zend\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$loader = new Twig_Loader_Filesystem('.');
$twig = new \Twig_Environment($loader, array(
    'debug' => true,
    'cache' => false,
));

$router = new Aura\Router\RouterContainer();
$map = $router->getMap();
$map->get('todo.list', '/', function ($request) use ($twig) {
    /*$tasks = [
        [
            'id' => 1,
            'description' => 'Aprender inglés',
            'done' => false
        ],
        [
            'id' => 1,
            'description' => 'Hacer la tarea',
            'done' => true
        ],
        [
            'id' => 1,
            'description' => 'Pasear al perro',
            'done' => false
        ],
        [
            'id' => 1,
            'description' => 'Ver el curso de introducción a PHP',
            'done' => false
        ]
    ];*/
    $tasks = Task::all();
    $response = new Zend\Diactoros\Response\HtmlResponse($twig->render('template.twig', [
        'tasks' => $tasks
    ]));
    return $response;
});

$map->post('todo.add', '/add', function ($request) {
    $data = $request->getParsedBody();
    $task = new Task();
    $task->description = $data['description'];
    $task->save(); 
    $response = new Zend\Diactoros\Response\RedirectResponse('/');
    return $response;
});

$map->get('todo.check', '/check/{id}', function ($request) {
    $id = $request->getAttribute('id');
    $task = Task::find($id);
    $task->done = true;
    $task->save(); 
    $response = new Zend\Diactoros\Response\RedirectResponse('/');
    return $response;
});

$map->get('todo.uncheck', '/uncheck/{id}', function ($request) {
    $id = $request->getAttribute('id');
    $task = Task::find($id);
    $task->done = false;
    $task->save(); 
    $response = new Zend\Diactoros\Response\RedirectResponse('/');
    return $response;
});

$map->get('todo.delete', '/delete/{id}', function ($request) {
    $id = $request->getAttribute('id');
    $task = Task::find($id);
    $task->delete(); 
    $response = new Zend\Diactoros\Response\RedirectResponse('/');
    return $response;
});