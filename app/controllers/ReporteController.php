<?php
require_once './db/ReporteDb.php';
require_once './models/encuesta.php';
require_once './interfaces/IApiUsable.php';


class ReporteController extends ReporteDb
{
    /*** Empleados a- Los días y horarios que se ingresaron al sistema. ***/
    public function ReporteEmpleadosLogin($request, $response, $args)
    {
      try{
        $lista = ReporteDb::ObtenerConsultaIngresosAlSistema();

        $payload = json_encode(array("Reporte_1:" => $lista));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer los Logins: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    /*** Empleados b- Cantidad de operaciones de todos por sector. ***/
    public function ReportePorSector($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerOperacionesPorSector();
            if(count($lista) > 0)
            {
                foreach($lista as $reporte)
                {
                    $reporte->nombreSector = sectorEnum::GetDescription($reporte->sectorId);
                }
            }

            $sectorMesa = ReporteDb::ObtenerOperacionesPorMesas();
            $sectorMesa[0]->nombreSector = sectorEnum::GetDescription($sectorMesa[0]->sectorId);
            array_push($lista, $sectorMesa[0]);
    
            $payload = json_encode(array("Reporte_2:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Empleados c- Cantidad de operaciones de todos por sector, listada por cada empleado. ***/
    public function ReportePorEmpleadoSector($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerOperacionesPorEmpleadoSector();
            if(count($lista) > 0)
            {
                foreach($lista as $reporte)
                {
                    $reporte->nombreSector = sectorEnum::GetDescription($reporte->sectorId);
                    
                }
            }

            $sectorMesa = ReporteDb::ObtenerOperacionesPorEmpleadoMesas();
            $sectorMesa[0]->nombreSector = sectorEnum::GetDescription($sectorMesa[0]->sectorId);
            array_push($lista, $sectorMesa[0]);
    
            $payload = json_encode(array("Reporte_3:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Empleados d- Cantidad de operaciones de cada uno por separado. ***/
    public function ReportePorEmpleados($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerOperacionesPorEmpleadoSector();
            $sectorMesa = ReporteDb::ObtenerOperacionesPorEmpleadoMesas();
            array_push($lista, $sectorMesa[0]);
    
            $payload = json_encode(array("Reporte_4:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }


    /*** Pedidos a- Lo que más se vendió. ***/
    public function ReportePedidosMasVendido($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerPedidosMasVendidos();
    
            $payload = json_encode(array("Reporte_5:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer los productos más vendidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Pedidos b- Lo que menos se vendió. ***/
    public function ReportePedidosMenosVendido($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerPedidosMenosVendidos();
            
            $payload = json_encode(array("Reporte_6:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer los productos menos vendidos: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Pedidos c- Los que no se entregaron en el tiempo estipulado. ***/
    public function ReportePedidosEntregaVencida($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerPedidosEntregaVencida();

            $payload = json_encode(array("Reporte_7:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer los pedidos entregados a tiempo: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Pedidos d- Los cancelados. ***/
    public function ReportePedidosCancelados($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerPedidosCancelados();

            if(count($lista) > 0)
            {
                foreach($lista as $reporte)
                {
                    $reporte->nombreEstado = EstadoPedidoDetalleEnum::GetDescription($reporte->estadoId);
                }
            }
    
            $payload = json_encode(array("Reporte_8:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer los pedidos cancelados: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }


    /*** Mesas a- La más usada. ***/
    public function ReporteMesasPorMayorUso($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMasUsada();
    
            $payload = json_encode(array("Reporte_9:" => $lista));
            $response->getBody()->write($payload);
            }
            catch(PDOException $e)
            {
                $error = json_encode(array("mensaje" => "Error al traer las mesas mas usadas: ".$e->getMessage()));
                $response->getBody()->write($error);
            }  
            
            return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas b- La menos usada. ***/
    public function ReporteMesasPorMenorUso($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMenosUsada();
    
            $payload = json_encode(array("Reporte_10:" => $lista));
            $response->getBody()->write($payload);
            }
            catch(PDOException $e)
            {
                $error = json_encode(array("mensaje" => "Error al traer las mesas menos usadas: ".$e->getMessage()));
                $response->getBody()->write($error);
            }  
            
            return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas c- La que más facturo. ***/
    public function ReporteMesasPorMayorFacturacion($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMayorFacturacion();
    
            $payload = json_encode(array("Reporte_11:" => $lista));
            $response->getBody()->write($payload);
            }
            catch(PDOException $e)
            {
                $error = json_encode(array("mensaje" => "Error al traer las mesas con mayor facturacion: ".$e->getMessage()));
                $response->getBody()->write($error);
            }  
            
            return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas d- La que menos facturo. ***/
    public function ReporteMesasPorMenorFacturacion($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMenorFacturacion();
    
            $payload = json_encode(array("Reporte_12:" => $lista));
            $response->getBody()->write($payload);
            }
            catch(PDOException $e)
            {
                $error = json_encode(array("mensaje" => "Error al traer las mesas con menor facturacion: ".$e->getMessage()));
                $response->getBody()->write($error);
            }  
            
            return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas e- La mesa con mayor importe. ***/
    public function ReporteMesasPorMayorImporte($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMayorImporte();
    
            $payload = json_encode(array("Reporte_13:" => $lista));
            $response->getBody()->write($payload);
            }
            catch(PDOException $e)
            {
                $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
                $response->getBody()->write($error);
            }  
            
            return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas f- La mesa con menor importe. ***/
    public function ReporteMesasPorMenorImporte($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMenorImporte();

            $payload = json_encode(array("Reporte_14:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas g- Facturación entre dos fechas. ***/
    public function ReporteMesasPorFacturaEntreFechas($request, $response, $args)
    {
        $parametros = $request->getQueryParams();
        $desde = $parametros['desde'];
        $hasta = $parametros['hasta'];

        try{
            $lista = ReporteDb::ObtenerMesasEntreFechas($desde, $hasta);

            $payload = json_encode(array("Reporte_15:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas h- Mejores comentarios. ***/
    public function ReporteMesasPorMejorComentario($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasMejorComentario();
            
            $payload = json_encode(array("Reporte_16:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }

    /*** Mesas i- Peores comentarios. ***/
    public function ReporteMesasPorPeorComentario($request, $response, $args)
    {
        try{
            $lista = ReporteDb::ObtenerMesasPeorComentario();
           
            $payload = json_encode(array("Reporte_17:" => $lista));
            $response->getBody()->write($payload);
          }
          catch(PDOException $e)
          {
              $error = json_encode(array("mensaje" => "Error al traer las operaciones por empleado-sector: ".$e->getMessage()));
              $response->getBody()->write($error);
          }  
            
          return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
?>