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
require_once './controllers/VentaController.php';

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

$app->post('/login[/]', \UsuarioController::class . ':VerificarUno')
    ->add(\Verificadora::class . ':CrearJWT');

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');
    $group->put('/{id}', \UsuarioController::class . ':ModificarUno');
});

$app->group('/criptomonedas', function (RouteCollectorProxy $group) {

    $group->get('/{id}', \CriptomonedaController::class . ':TraerUno')
            ->add(\Verificadora::class . ':VerificarRegistro');

    $group->get('[/]', \CriptomonedaController::class . ':TraerTodos');
    
    $group->get('/tipo/{tipo}', \CriptomonedaController::class . ':TraerTipo');

    $group->get('/nacionalidad/{nacionalidad}', \CriptomonedaController::class . ':TraerNac');

    $group->post('[/]', \CriptomonedaController::class . ':CargarUno')
            ->add(\Verificadora::class . ':VerificarAdmin');

    $group->delete('/{id}', \CriptomonedaController::class . ':BorrarUno')
            ->add(\Verificadora::class . ':VerificarAdmin');

    $group->put('/{id}', \CriptomonedaController::class . ':ModificarUno')
            ->add(\Verificadora::class . ':VerificarAdmin');
});

$app->group('/ventas', function (RouteCollectorProxy $group) {

    $group->get('/{id}', \VentaController::class . ':TraerUno')
            ->add(\Verificadora::class . ':VerificarRegistro');

    $group->get('[/]', \VentaController::class . ':TraerTodos');    

    $group->post('[/]', \VentaController::class . ':CargarUno')
            ->add(\Verificadora::class . ':VerificarRegistro');

    $group->delete('/{id}', \VentaController::class . ':BorrarUno')
            ->add(\Verificadora::class . ':VerificarAdmin');

    $group->put('/{id}', \VentaController::class . ':ModificarUno')
            ->add(\Verificadora::class . ':VerificarAdmin');
});


$app->run();
