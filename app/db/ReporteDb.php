<?php

require_once './models/reportes/LogUsuarios.php';
require_once './models/reportes/MesaComentario.php';
require_once './models/reportes/MesaMasMenosUsada.php';
require_once './models/reportes/MesaFacturacion.php';
require_once './models/reportes/OperacionPorEmpleado.php';
require_once './models/reportes/OperacionPorSector.php';
require_once './models/reportes/OperacionPorEmpleadoSector.php';
require_once './models/reportes/PedidoCancelados.php';
require_once './models/reportes/PedidoEntregaEnTiempo.php';
require_once './models/reportes/PedidoVendidos.php';

class ReporteDb
{
    public static function ObtenerConsultaIngresosAlSistema()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT lu.id, lu.usuarioId, u.nombre, u.apellido, lu.fecha, lu.perfil FROM logusuarios lu
                INNER JOIN usuarios u ON u.id = lu.usuarioId;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'LogUsuarios');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerOperacionesPorSector()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT u.sectorId, COUNT(u.sectorId) AS cantOperaciones FROM PedidosDetalle pd
                INNER JOIN Usuarios u ON u.id = pd.empleadoId
                GROUP BY u.sectorId;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'OperacionPorSector');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerOperacionesPorMesas()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT u.sectorId, COUNT(u.sectorId) AS cantOperaciones FROM Pedidos p
                INNER JOIN Usuarios u ON u.id = p.empleadoId
                GROUP BY u.sectorId;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'OperacionPorSector');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerOperacionesPorEmpleadoSector()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT pd.empleadoId, u.nombre, u.apellido, p.sectorId, COUNT(pd.empleadoId) AS cantOperaciones FROM PedidosDetalle pd
                INNER JOIN Usuarios u ON u.id = pd.empleadoId
                INNER JOIN Productos p ON p.id = pd.productoId
                GROUP BY p.sectorId, pd.empleadoId
                ORDER BY pd.empleadoId;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'OperacionesPorEmpleadoSector');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerOperacionesPorEmpleadoMesas()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.empleadoId, u.nombre, u.apellido, COUNT(p.empleadoId) AS cantOperaciones FROM Pedidos p
                INNER JOIN Usuarios u ON u.id = p.empleadoId
                GROUP BY p.empleadoId;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'OperacionesPorEmpleadoSector');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPedidosMasVendidos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT pd.productoId, p.descripcion, MAX(pd.cantidad) AS cantidadVendida FROM PedidosDetalle pd
                INNER JOIN Productos p on p.id = pd.productoId");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoVendidos');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPedidosMenosVendidos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT pd.productoId, p.descripcion, MIN(pd.cantidad) AS cantidadVendida FROM PedidosDetalle pd
                INNER JOIN Productos p on p.id = pd.productoId
                GROUP BY pd.productoId  
               
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoVendidos');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPedidosEntregaVencida()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.id AS pedidoId, pd.id AS pedidosDetalleId, pd.productoId, prod.descripcion, (pd.TiempoInicial + INTERVAL pd.TiempoEstimado MINUTE) AS entregaEstimada, pd.tiempoFinal FROM PedidosDetalle pd
                INNER JOIN Pedidos p ON p.id = pd.pedidoId
                INNER JOIN Productos prod on prod.id = pd.productoId
                WHERE pd.estadoId = 4 AND pd.tiempoFinal > (pd.tiempoInicial + INTERVAL pd.tiempoEstimado MINUTE);
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoEntregaEnTiempo');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPedidosCancelados()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT pd.pedidoId, p.codigo, pd.id AS pedidosDetalleId, pd.productoId, prod.descripcion, pd.estadoId FROM PedidosDetalle pd
                INNER JOIN Pedidos p ON p.id = pd.pedidoId
                INNER JOIN Productos prod on prod.id = pd.productoId
                WHERE pd.estadoId = 5;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'PedidoCancelados');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMasUsada()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, COUNT(p.mesaId) as CantUsos FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                GROUP BY mesaId
                ORDER BY CantUsos DESC;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaMasMenosUsada');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMenosUsada()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, COUNT(p.mesaId) as CantUsos FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                GROUP BY mesaId
                ORDER BY CantUsos ASC;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaMasMenosUsada');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMayorFacturacion()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, SUM(p.precio) as facturacion FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                GROUP BY p.mesaId
                ORDER BY facturacion DESC;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaFacturacion');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMenorFacturacion()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, SUM(p.precio) as facturacion FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                GROUP BY p.mesaId
                ORDER BY facturacion ASC;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaFacturacion');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMayorImporte()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, p.precio AS facturacion FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                ORDER BY p.precio DESC;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaFacturacion');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMenorImporte()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, p.precio AS facturacion FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                ORDER BY p.precio ASC;
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaFacturacion');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasEntreFechas($desde, $hasta)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.mesaId, m.codigo, SUM(p.precio) as facturacion FROM pedidos p
                INNER JOIN Mesas m ON m.id = p.mesaId
                WHERE p.fecha BETWEEN :desde AND :hasta
                GROUP BY p.mesaId
                ORDER BY facturacion DESC;
                ");
            $consulta->bindValue(':desde', $desde);
            $consulta->bindValue(':hasta', $hasta);
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaFacturacion');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasMejorComentario()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.id as pedidoId, p.mesaId, m.codigo, e.puntajeMesa, e.comentario FROM Pedidos p
                INNER JOIN Encuestas e on e.pedidoId = p.id
                INNER JOIN Mesas m ON m.id = p.mesaId
                ORDER BY e.puntajeMesa DESC;    
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaComentario');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerMesasPeorComentario()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("
                SELECT p.id, p.mesaId, m.codigo, e.puntajeMesa, e.comentario FROM Pedidos p
                INNER JOIN Encuestas e on e.pedidoId = p.id
                INNER JOIN Mesas m ON m.id = p.mesaId
                ORDER BY e.puntajeMesa ASC;    
                ");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'MesaComentario');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }
}
?>