<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

use App\Routing\Router;

$router = new Router();

$router->get('/', [App\Controllers\HomeController::class, 'index']);

$router->get('/groups/preview', [App\Controllers\GroupsController::class, 'preview']);
$router->post('/groups', [App\Controllers\GroupsController::class, 'store']);

$router->post('/careers', [App\Controllers\CareersController::class, 'store']);
$router->post('/careers/{id}/disable', [App\Controllers\CareersController::class, 'disable']);
$router->post('/careers/{id}/enable', [App\Controllers\CareersController::class, 'enable']);

$router->post('/students', [App\Controllers\StudentsController::class, 'store']);
$router->get('/students/{id}', [App\Controllers\StudentsController::class, 'show']);
$router->get('/students/{id}/edit', [App\Controllers\StudentsController::class, 'edit']);
$router->post('/students/{id}/update', [App\Controllers\StudentsController::class, 'update']);
$router->post('/students/{id}/delete', [App\Controllers\StudentsController::class, 'destroy']);
$router->post('/students/{id}/enable', [App\Controllers\StudentsController::class, 'enable']);

$response = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

http_response_code($response->status);
foreach ($response->headers as $name => $value) {
    header($name . ': ' . $value);
}
echo $response->body;
