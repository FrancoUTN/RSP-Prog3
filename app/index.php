<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;
use Illuminate\Database\Capsule\Manager as Capsule;

require __DIR__ . '/../vendor/autoload.php';

require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/Verificadora.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/CriptomonedaController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Eloquent
$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();


// Routes
$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Recuperatorio del Segundo Parcial");
    return $response;
});

$app->group('/login', function (RouteCollectorProxy $group) {
    $group->post('[/]', \Verificadora::class . ':Verificar');
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
});


$app->get('/criptomonedas[/]', \CriptomonedaController::class . ':TraerTodos');
$app->get('/criptomonedas/{id}', \CriptomonedaController::class . ':TraerUno');

$app->get('/criptomonedas/tipo/{tipo}', \CriptomonedaController::class . ':TraerTipo');

$app->post('/criptomonedas[/]', \CriptomonedaController::class . ':CargarUno');
$app->put('/criptomonedas/{id}', \CriptomonedaController::class . ':ModificarUno');
$app->delete('/criptomonedas/{id}', \CriptomonedaController::class . ':BorrarUno');

// Traer validando
$app->get('/criptomoneda', \CriptomonedaController::class . ':TraerTodos')->add(\MiAutentificador::class . ":Validar");
// $app->get('/criptomoneda', \CriptomonedaController::class . ':TraerTodos')->add($validar);
// $app->get('/criptomoneda', \CriptomonedaController::class . ':TraerTodos')->add($validar)->add($admin);


// $app->group('/criptomonedas', function (RouteCollectorProxy $group) {
//     $group->get('[/]', \CriptomonedaController::class . ':TraerTodos');
//     $group->get('/{id}', \CriptomonedaController::class . ':TraerUno');

//     $group->get('/tipo/{tipo}', \CriptomonedaController::class . ':TraerTipo');

//     $group->post('[/]', \CriptomonedaController::class . ':CargarUno');
//     $group->put('/{id}', \CriptomonedaController::class . ':ModificarUno');
//     $group->delete('/{id}', \CriptomonedaController::class . ':BorrarUno');
// });
// // })->add($mwFotos);

$app->run();
