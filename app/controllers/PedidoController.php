<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './models/DetallePedido.php';
require_once './Utilities/EstadoPedidoDetalleEnum.php';
require_once './Utilities/EstadoMesaEnum.php';
require_once './Utilities/PerfilUsuarioEnum.php';
require_once './Utilities/fileHelper.php';
require_once '/xampp/htdocs/La_Comanda/Views/pedidoDashboardView.php';
require_once '/xampp/htdocs/La_Comanda/Views/mozoPedidoView.php';



class PedidoController extends Pedido implements IApiUsable
{
  public  $path = "Utilities/archivos/";
    public function CargarUno($request, $response, $args)
    {
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $empleadoId = $request->getAttribute('usuarioId');
        $parametros = $request->getParsedBody();
        $mesaId = $parametros['mesaId'];
        $nombreCliente = $parametros['nombreCliente'];
        $productos = $parametros['productos'];
        $cantidad = $parametros['cantidad'];
        try
        {
          Mesa::ModificarEstado(EstadoMesaEnum::esperandoPedido, $mesaId);
        }
        catch(PDOException $e){
          $error = json_encode(array("mensaje" => "Error al modificar el estado de la mesa: ".$e->getMessage()));
          $response->getBody()->write($error);
        }

        $pedido = new Pedido();
        $fecha = date("Y/m/d h:i:sa");
        $pedido->ToPedido(intval($mesaId), intval($empleadoId), $nombreCliente, $fecha, intval($pedido->precio));
       
       
        $mensaje = "";
       
        foreach($productos as $producto)
        {
            $pedidoDetalle = new DetallePedido();

            $productoString = json_encode($producto);
            $decodedProducto = json_decode($productoString);

            
            $productoDb = Producto::ObtenerProducto($decodedProducto);
            
            if($productoDb)
            {
             
             
              $pedido->precio = $pedido->precio + $productoDb->precio * $cantidad;

              try
              {
                $ultimoIdFromPedido = $pedido->crearPedido();
              }
              catch(PDOException $e){
                $error = json_encode(array("mensaje" => "Error al crear el pedido: ".$e->getMessage()));
                $response->getBody()->write($error);
                return $response;
              }

              $pedidoDetalle->ToDetallePedido(intval($ultimoIdFromPedido), intval($empleadoId) , $fecha, EstadoPedidoDetalleEnum::pendiente, intval($productoDb->id) , intval($cantidad) );
              try{
                $pedidoDetalle->crearPedidoDetalle($pedidoDetalle);
                $mensaje = "Pedido generado con exito";
              }
              catch(PDOException $e){
                $error = json_encode(array("mensaje" => "Error al crear el detalle del pedido: ".$e->getMessage()));
                $response->getBody()->write($error);
                return $response;
              }
            }
            else{
             
              $mensaje = "No se pudo generar los pedidos para los productos";
            }
            
        }

        $payload = json_encode(array("mensaje" => $mensaje));
              $response->getBody()->write($payload);
        return $response
                  ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
       
      
    }

    public function TomarPedidoDetalle($request, $response, $args)
    {
        $empleadoId = $request->getAttribute('usuarioId');
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        $estadoId = $parametros['estadoId'];
        $tiempoEstimado = $parametros['tiempoEstimado'];
        try{
          DetallePedido::IniciarPreparacion($estadoId, $detallePedidoId, $empleadoId, $tiempoEstimado);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido tomado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarEstadoPedidoDetalle($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        $estadoId = $parametros['estadoId'];
        try{
          DetallePedido::ModificarEstado(EstadoPedidoDetalleEnum::listoParaServir, $detallePedidoId);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido actualizado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function CancelarPedidoDetalle($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        try{
          DetallePedido::ModificarEstado(EstadoPedidoDetalleEnum::cancelada, $detallePedidoId);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido cancelado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function CargarFoto($request, $response, $args)
    {
      date_default_timezone_set("America/Argentina/Buenos_Aires");
      $parametros = $request->getParsedBody();
       $foto = $_FILES['foto'];
      //  $nombreCliente = $parametros['nombreCliente'];
       $pedidoId = $parametros['pedidoId'];
       $fecha = $fecha = date("Y/m/d");
       $pedido = Pedido::obtenerPedido($pedidoId);
       $pathFoto = "";
        try{
         $pathFoto = File::GuardarImagen($foto, $pedido->nombreCliente,  $pedidoId, $fecha);
        }
        catch(Exception $e){
          $error = json_encode(array("mensaje" => "Error al guardar la imagen en el folder: ".$e->getMessage()));
          $response->getBody()->write($error);
        }

        try
        {
            Pedido::AgregarFoto($pathFoto, $pedidoId);
        }
        catch(Exception $e){
          $error = json_encode(array("mensaje" => "Error al guardar la imagen en la DB: ".$e->getMessage()));
          $response->getBody()->write($error);
          return $response;
        }
    }

    public function EntregarPedidoDetalle($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $detallePedidoId = $parametros['detallePedidoId'];
        $estadoId = $parametros['estadoId'];
        try{
          DetallePedido::EntregarPedido($estadoId, $detallePedidoId);
         }
         catch(Exception $e){
           $error = json_encode(array("mensaje" => "Error al Actualizar el pedido: ".$e->getMessage()));
           $response->getBody()->write($error);
         }

         $payload = json_encode(array("mensaje" => "Pedido entregado."));
         $response->getBody()->write($payload);
         return $response
           ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $empleadoId = $request->getAttribute('usuarioId');
      $perfil = $request->getAttribute('perfil');
      $lista = [];
     
      switch($perfil)
      {
         
        case PerfilUsuarioEnum::socio:
           
        case PerfilUsuarioEnum::mozo:
          try{
            $lista = Pedido::obtenerTodos();
            var_dump($lista);
            if($lista)
            {
              
              foreach($lista as $pedido)
              {
                $listaDetalle = DetallePedido::ObtenerFullDataPedidosDetalle($pedido->id);

                if($listaDetalle)
                {
                  foreach($listaDetalle as $detalle)
                  {
                    if($detalle->empleadoId != null)
                    {
                      $usuario = Usuario::obtenerUsuarioPorId($detalle->empleadoId);
                      $listaDetalle->empleadoNombre = $usuario->apellido.", ".$usuario->nombre;
                    }

                    $detalle->sector = SectorEnum::GetDescription($detalle->sectorId);
                    $detalle->estadoPedido = EstadoPedidoDetalleEnum::GetDescription($detalle->estadoId);
                  }
                  $pedido->productos= $listaDetalle;
                }
              } 
              
            }
           
            $payload = json_encode(array("lista Pedidos socio" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer Los pedidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }

          break;
     
        case PerfilUsuarioEnum::cocinero:
          $lista = $this->DevolverListaDetallesPedidosPorSector(SectorEnum::cocina);

          break;
        case PerfilUsuarioEnum::bartender:
          $lista = $this->DevolverListaDetallesPedidosPorSector(SectorEnum::tragosYvinosEntrada);

          break;

        case PerfilUsuarioEnum::cervecero:
          $lista = $this->DevolverListaDetallesPedidosPorSector(SectorEnum::cervezasPatioTrasero);
          break;

      }

      $payload = json_encode(array("lista Pedidos" => $lista));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {

    }

    public function TraerUno($request, $response, $args)
    {

    }

    private function DevolverListaDetallesPedidosPorSector($sectorId)
    {
      $lista = DetallePedido::ObtenerPedidosDetallePorSector($sectorId);
      if($lista)
      {
        foreach($lista as $detalle)
        {
          $detalle->estadoDetalle = EstadoPedidoDetalleEnum::GetDescription($detalle->estadoId);
        }
      }

      return $lista;
    }

    public function DownloadCSV($request, $response, $args)
    {
      $filename = "pedidos-lista_".date('Y-m-d');
      $extension = ".csv"; 
      $listaPedidos = Pedido::CargarPedidosCSV();
      $arrayPedidos = [];
    
      $header = array('ID', 'COD PEDIDO', 'COD MESA', 'CLIENTE', 'MOZO', 'PRECIO TOTAL', 'FECHA'); 
      foreach($listaPedidos as $pedido)
      {   
          $lineData = array($pedido->id, $pedido->codigoPedido,  $pedido->codigoMesa, 
           $pedido->nombreCliente, $pedido->emailMozo, $pedido->precio, strval($pedido->fecha)); 
          array_push($arrayPedidos, $lineData);
      }

      File::ArchivarSVC($header, $arrayPedidos, $this->path, $filename, $extension);
      $mensaje = json_encode(array("mensaje" => "Descarga exitosa."));
          $response->getBody()->write($mensaje);
          return $response;
    }

    public function pedidosDetallesPorPedidoId($request, $response, $args)
    {
      $parametros = $request->getQueryParams();
      //  $nombreCliente = $parametros['nombreCliente'];
       $pedidoId = $parametros['pedidoId'];
      $lista = DetallePedido::ObtenerFullDataPedidosDetalle($pedidoId);
      foreach($lista as $detalle)
      {
        $detalle->estadoDetalle = EstadoPedidoDetalleEnum::GetDescription($detalle->estadoId);
      }
      $payload = json_encode(array("lista Pedidos" => $lista));
      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

}
