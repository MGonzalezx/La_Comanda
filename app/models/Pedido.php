<?php
require_once './Utilities/DateHelper.php';
require_once 'C:\xampp\htdocs\La_Comanda\Views/pedidoDetalleCSV.php';
require_once '/xampp/htdocs/La_Comanda//Views/Recibo.php';



class Pedido
{
    public $id;
    public $codigo;
    public $mesaId;
    public $nombreCliente;
    public $foto;
    public $precio;
    public $fecha;
    public $empleadoId;


    public function crearPedido()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO pedidos (codigo, mesaId, nombreCliente, empleadoId, fecha, precio) VALUES (:codigo, :mesaId, :nombreCliente, :empleadoId, :fecha, :precio)");
            $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':mesaId', $this->mesaId, PDO::PARAM_INT);
            $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
            $consulta->bindValue(':empleadoId', $this->empleadoId, PDO::PARAM_INT);
            $consulta->bindValue(':fecha', DateHelper::DateAMD());
            $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);

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
        $hoy = DateHelper::DateAMD();
         var_dump($hoy);
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, u.nombre, u.apellido, m.estado, p.empleadoId as mozoId, p.fecha   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN mesas m on m.id = p.mesaId
            WHERE CAST(p.fecha AS DATE) =:hoy && m.estado != 4
            ORDER BY p.id, m.estado, p.fecha");

            $consulta->bindValue(':hoy', $hoy."%");
    
            $consulta->execute();
            return  $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoDashboardView');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPedido($pedidoId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM pedidos 
        
        WHERE id = :pedidoId");
        $consulta->bindValue(':pedidoId', $pedidoId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Pedido');
    }

    public static function AgregarFoto($foto, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE pedidos SET foto = :foto  WHERE id = :id");
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }

    public static function ObtenerTodosDetalle()
    {

        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, u.usuario as emailMozo, p.precio, p.fecha, p.nombreCliente   FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN mesas m on m.id = p.mesaId
            ORDER BY fecha");
            $consulta->execute();
            
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoDetalleCSV');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    
    
    public  function ToPedido($mesaId, $empleadoId, $nombreCliente, $fecha, $precio)
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $codigo = substr(str_shuffle($permitted_chars), 0, 5);
        $this->codigo = $codigo;
        $this->mesaId = $mesaId;
        $this->empleadoId = $empleadoId;
        $this->nombreCliente = $nombreCliente;
        $this->fecha = $fecha;
        $this->$precio = $precio;
    }

    public function PedidoCompare($pedidoA, $pedidoB)
    {
        return $pedidoA->codigo === $pedidoB->codigo;
    }

    public function SetCodigo(){
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $this->codigo = substr(str_shuffle($permitted_chars), 0, 5);
    }

    public static function CargarPedidosCSV()
    {
       $listaPedidos =  Pedido::obtenerTodosDetalle();
       return $listaPedidos;

    }

    public static function ObtenerParaRecidoPorPedidoId($pedidoId)
    {
        $hoy = DateHelper::DateAMD();
        // var_dump($hoy);
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, u.nombre, u.apellido, m.estado, p.empleadoId as mozoId, p.fecha , p.precio  FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN mesas m on m.id = p.mesaId
            WHERE   p.id =:pedidoId
            ORDER BY p.id, m.estado, p.fecha");

            $consulta->bindValue(':pedidoId', $pedidoId);

            $consulta->execute();
            return $consulta->fetchObject('Recibo');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerParaRecidoPorPedidoCliente($cliente)
    {
        $hoy = DateHelper::DateAMD();
        // var_dump($hoy);
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT p.id, p.codigo as codigoPedido, m.codigo as codigoMesa, u.nombre, u.apellido, m.estado, p.empleadoId as mozoId, p.fecha , p.precio  FROM pedidos p
            INNER JOIN usuarios u on u.id = p.empleadoId
            INNER JOIN mesas m on m.id = p.mesaId
            WHERE   p.nombreCliente =:cliente
            ORDER BY p.id, m.estado, p.fecha");

            $consulta->bindValue(':cliente', $cliente);

            $consulta->execute();
            return $consulta->fetchObject('Recibo');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
    

    
}