<?php

require_once './models/Producto.php';

class ProductoDb extends Producto
{
    public function Crear($producto)
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO productos (descripcion, tipoProductoId, sectorId, precio) VALUES (:descripcion, :tipoProductoId, :sectorId, :precio)");
            $consulta->bindValue(':descripcion', $producto->descripcion, PDO::PARAM_STR);
            $consulta->bindValue(':tipoProductoId', $producto->tipoProductoId, PDO::PARAM_INT);
            $consulta->bindValue(':sectorId', $producto->sectorId, PDO::PARAM_INT);
            $consulta->bindValue(':precio', $producto->precio, PDO::PARAM_INT);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
            // echo ''Error: '' .$e->getMessage() . ''<br/> '';
        }
        
    }

    public static function ObtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Producto');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerPorId($productoId)
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM productos WHERE id = :productoId");
            $consulta->bindValue(':productoId', $productoId, PDO::PARAM_INT);
            $consulta->execute();

            return $consulta->fetchObject('Producto');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

}