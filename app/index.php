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
require_once './controllers/EncuestaController.php';
require_once './controllers/ReporteController.php';

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

/* ENCUESTAS */
$app->group('/encuestas', function (RouteCollectorProxy $group) {
  $group->post('[/]', \EncuestaController::class . ':CargarUno');
  $group->get('/{id}', \EncuestaController::class . ':TraerUno');
  $group->get('[/]', \EncuestaController::class . ':TraerTodos');
});

/* REPORTES */
$app->group('/reportes', function (RouteCollectorProxy $group) {
  /* Reportes - Empleados */
  $group->get('/empleados/login', \ReporteController::class . ':ReporteEmpleadosLogin');
  $group->get('/empleados/sectores', \ReporteController::class . ':ReportePorSector');
  $group->get('/empleados/empleados_sectores', \ReporteController::class . ':ReportePorEmpleadoSector');
  $group->get('/empleados/empleados', \ReporteController::class . ':ReportePorEmpleados');
  /* Reportes - Pedidos */
  //por si  quieren uno solo  hacemos un  top one
  $group->get('/pedidos/mas_vendido', \ReporteController::class . ':ReportePedidosMasVendido');
  $group->get('/pedidos/menos_vendido', \ReporteController::class . ':ReportePedidosMenosVendido');
  //pedidos no entregados en  tiempo  y  forma
  $group->get('/pedidos/tiempo_estipulado', \ReporteController::class . ':ReportePedidosEntregaVencida');
  $group->get('/pedidos/cancelados', \ReporteController::class . ':ReportePedidosCancelados');
  /* Reportes - Mesas */
  $group->get('/mesas/mas_usada', \ReporteController::class . ':ReporteMesasPorMayorUso');
  $group->get('/mesas/menos_usada', \ReporteController::class . ':ReporteMesasPorMenorUso');
  $group->get('/mesas/mas_facturo', \ReporteController::class . ':ReporteMesasPorMayorFacturacion');
  $group->get('/mesas/menos_facturo', \ReporteController::class . ':ReporteMesasPorMenorFacturacion');
  $group->get('/mesas/mayor_importe', \ReporteController::class . ':ReporteMesasPorMayorImporte');
  $group->get('/mesas/menor_importe', \ReporteController::class . ':ReporteMesasPorMenorImporte');
  $group->get('/mesas/entre_fechas', \ReporteController::class . ':ReporteMesasPorFacturaEntreFechas');
  $group->get('/mesas/mejor_comentario', \ReporteController::class . ':ReporteMesasPorMejorComentario');
  $group->get('/mesas/peor_comentario', \ReporteController::class . ':ReporteMesasPorPeorComentario');
})->add(\ValidatorMW::class . ':CheckPerfilSocio')->add(\AuthTokenMW::class . ':AutenticarUsuario');
$app->run();

?>
