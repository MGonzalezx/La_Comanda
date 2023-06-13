<?php

require_once './models/Mesa.php';

class MesaDb extends Mesa 
{
    public function Crear($mesa)
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO mesas (codigo, numeroMesa, estado) VALUES (:codigo, :numeroMesa, :estado)");
            $consulta->bindValue(':codigo', $$mesa->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':numeroMesa', $$mesa->numeroMesa, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $$mesa->estado, PDO::PARAM_INT);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
        }
        
    }

    public static function ObtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Mesa');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerUno($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM mesas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Mesa');
    }

    public static function ModificarEstado($estado, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE mesas SET estado = :estado  WHERE id = :id");
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
}