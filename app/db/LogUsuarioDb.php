<?php

require_once './models/reportes/LogUsuarios.php';

class LogUsuarioDb extends LogUsuarios 
{
    public function CrearLog($logUsuario)
    {
        try 
        {
            // var_dump($logUsuario->nombre);
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logUsuarios (usuarioId, nombre, apellido,fecha, perfil) VALUES (:usuarioId, :nombre, :apellido, :fecha, :perfil)");
            $consulta->bindValue(':apellido', $logUsuario->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $logUsuario->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':fecha', $logUsuario->fecha);
            $consulta->bindValue(':perfil', $logUsuario->perfil, PDO::PARAM_INT);
            $consulta->bindValue(':usuarioId', $logUsuario->usuarioId, PDO::PARAM_INT);
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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logUsuarios");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'LogUsuarios');
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

        return $consulta->fetchObject('LogUsuarios');
    }

}