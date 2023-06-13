<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
//use Slim\Handlers\Strategies\RequestHandler;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

use function PHPSTORM_META\map;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/MesaController.php';
require_once './controllers/PedidoController.php';
require_once './controllers/LoginController.php';


require_once './middlewares/AuthTokenMW.php';
require_once './middlewares/ValidatorMW.php';





// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
$app->setBasePath('/La_comanda/app');
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// peticiones
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('[/]', \UsuarioController::class . ':TraerTodos');
  $group->post('[/]', \UsuarioController::class . ':CargarUno')->add(\ValidatorMW::class . ':CheckPerfilSocio')->add(\AuthTokenMW::class . ':AutenticarUsuario');
});

$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->get('[/]', \MesaController::class . ':TraerTodos');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos');

  $group->post('[/]', \PedidoController::class . ':CargarUno') ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');

});

$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \LoginController::class . ':Login');
});
$app->run();

?>
