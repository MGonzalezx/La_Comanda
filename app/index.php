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
  $group->post('/carga-csv', \ProductoController::class . ':LoadCSV');
});

$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->post('/actualizar-estado', \MesaController::class . ':ModificarUno')->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');
});

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\ValidatorMW::class . ':CheckEmpleados')->add(\AuthTokenMW::class . ':AutenticarUsuario');;

  $group->post('/fotos', \PedidoController::class . ':CargarFoto')->add(\ValidatorMW::class . ':CheckPerfilMozoYCliente')->add(\AuthTokenMW::class . ':AutenticarUsuario');

  $group->post('[/]', \PedidoController::class . ':CargarUno') ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');
  
  $group->get('/descarga-csv', \PedidoController::class . ':DownloadCSV');

  $group->post('/tomar-pedido', \PedidoController::class . ':TomarPedidoDetalle')
    ->add(\ValidatorMW::class . ':CheckEmpleadosParaTomarPedido')->add(\AuthTokenMW::class . ':AutenticarUsuario');
  $group->post('/modificar-pedido', \PedidoController::class . ':ModificarEstadoPedidoDetalle')
      ->add(\ValidatorMW::class . ':CheckEmpleadosParaTomarPedido')->add(\AuthTokenMW::class . ':AutenticarUsuario');

  //entregar y  cancelar solo lo puede hacer el  mozo
  $group->post('/entregar-pedido', \PedidoController::class . ':EntregarPedidoDetalle')
    ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');
  $group->post('/cancelar-pedido', \PedidoController::class . ':CancelarPedidoDetalle')
    ->add(\ValidatorMW::class . ':CheckPerfilMozo')->add(\AuthTokenMW::class . ':AutenticarUsuario');
    $group->get('/a', \PedidoController::class . ':pedidosDetallesPorPedidoId');

});

$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \LoginController::class . ':Login');
});
$app->run();

?>
