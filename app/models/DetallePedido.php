<?php
require_once '/xampp/htdocs/La_Comanda/Views/detallePedidoDashboardView.php';
class DetallePedido
{
    public $id;
    public $pedidoId;
    public $estadoId;
    public $empleadoId;
    public $productoId;
    public $cantidad;
    public $tiempoEstimado;
    public $tiempoInicial;
    public $tiempoFinal;

    public function crearPedidoDetalle()
    {
        try 
        {

            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidosdetalle 
                      (estadoId, pedidoId, empleadoId, tiempoEstimado, cantidad, productoId) 
            VALUES (:estadoId, :pedidoId, :empleadoId, :tiempoEstimado, :cantidad, :productoId)");
            $consulta->bindValue(':estadoId', $this->estadoId, PDO::PARAM_INT);
            $consulta->bindValue(':pedidoId', $this->pedidoId, PDO::PARAM_INT);
            $consulta->bindValue(':empleadoId', $this->empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoEstimado', $this->tiempoEstimado, PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
            $consulta->bindValue(':productoId', $this->productoId, PDO::PARAM_INT);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        
    }

    public static function obtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT p.* FROM pedidos p
                ");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Pedido');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerFullDataPedidosDetalle($pedidoId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();

            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT p.sectorId, p.descripcion, dp.cantidad, dp.empleadoId, dp.estadoId, dp.tiempoEstimado, dp.tiempoInicial, dp.tiempoFinal, p.precio FROM pedidosdetalle dp
                INNER JOIN productos p on p.id = dp.productoId
                WHERE dp.pedidoId =:pedidoId
                ");
            $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'DetallePedidoDashboardView');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    

    //ver si  ponerle el estado  o  no al  filtro
    public static function ObtenerPedidosDetallePorSector($sectorId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            //estado 1 = pendiente
            $consulta = $objAccesoDatos->prepararConsulta(
                "SELECT p.sectorId, p.descripcion, dp.cantidad, dp.estadoId, dp.tiempoEstimado, dp.tiempoInicial, dp.tiempoFinal FROM pedidosdetalle dp
                INNER JOIN productos p on p.id = dp.productoId
                WHERE p.sectorId =:sectorId 
                ");
            $consulta->bindValue(':sectorId', $sectorId, PDO::PARAM_INT);
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'DetallePedidoDashboardView');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    
    public static function IniciarPreparacion($estadoId, $detallePedidoId, $empleadoId, $tiempoEstimado)
    {
        try{
            $tiempoInicial = DateHelper::FullDateHMS();
           $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidosdetalle SET estadoId = :estadoId, empleadoId = :empleadoId, tiempoEstimado = :tiempoEstimado, tiempoInicial = :tiempoInicial  
            WHERE id = :detallePedidoId");
            $consulta->bindValue(':estadoId', $estadoId, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoEstimado', $tiempoEstimado, PDO::PARAM_INT);
            $consulta->bindValue(':empleadoId', $empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':detallePedidoId', $detallePedidoId, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoInicial', $tiempoInicial);
            $consulta->execute(); 
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    //listo  para entregar, cancelar
    public static function ModificarEstado($estadoId, $detallePedidoId)
    {
        try{
            $tiempoInicial = DateHelper::FullDateHMS();
           $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidosdetalle SET estadoId = :estadoId 
            WHERE id = :detallePedidoId");
            $consulta->bindValue(':estadoId', $estadoId, PDO::PARAM_INT);
            $consulta->bindValue(':detallePedidoId', $detallePedidoId, PDO::PARAM_INT);
            $consulta->execute(); 
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function EntregarPedido($estadoId, $detallePedidoId)
    {
        try{
            $tiempoFinal = DateHelper::FullDateHMS();
           $objAccesoDato = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDato->prepararConsulta(
            "UPDATE pedidosdetalle SET estadoId = :estadoId, tiempoFinal = :tiempoFinal
            WHERE id = :detallePedidoId");
            $consulta->bindValue(':estadoId', $estadoId, PDO::PARAM_INT);
            $consulta->bindValue(':detallePedidoId', $detallePedidoId, PDO::PARAM_INT);
            $consulta->bindValue(':tiempoFinal', $tiempoFinal);
            $consulta->execute(); 
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    public  function ToDetallePedido($pedidoId, $empleadoId, $tiempoInicial, $estado, $productoId, $cantidad)
    {
            $this->productoId = $productoId;
            $this->cantidad = $cantidad;
            $this->estadoId = $estado;
            $this->tiempoInicial = $tiempoInicial;
            $this->pedidoId = $pedidoId;
            $this->empleadoId = $empleadoId;
    }

    
}