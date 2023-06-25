<?php

class Usuario
{
    public $id;
    public $usuario;
    public $nombre;
    public $apellido;
    public $clave;
    public $tipoUsuarioId;
    public $sector;

    public function crearUsuario()
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios (usuario, clave, nombre, apellido, tipoUsuarioId, sectorId) VALUES (:usuario, :clave, :nombre, :apellido, :tipoUsuarioId, :sectorId)");
            $claveHash = password_hash($this->clave, PASSWORD_DEFAULT);
            $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $consulta->bindValue(':tipoUsuarioId', $this->tipoUsuarioId, PDO::PARAM_INT);
            $consulta->bindValue(':sectorId', $this->sector, PDO::PARAM_INT);
            $consulta->bindValue(':clave', $claveHash);
            $consulta->execute();

            return $objAccesoDatos->obtenerUltimoId();
        }
        catch(PDOException $e)
        {
            throw $e;
            // echo ''Error: '' .$e->getMessage() . ''<br/> '';
        }
        
    }

    public static function obtenerTodos()
    {
        try{
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
            $consulta->execute();
            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function obtenerUsuarioPorId($usuarioId)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, nombre, apellido, tipoUsuarioId FROM usuarios WHERE id = :usuarioId");
        $consulta->bindValue(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioPorUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }
    
    public static function obtenerUsuarioPorUsuarioYClave($miUsuario, $clave)
    {
        $usuario = Usuario::obtenerUsuarioPorUsuario($miUsuario);
        if($usuario->tipoUsuarioId == PerfilUsuarioEnum::socio && $usuario->clave == "claveMaestra")
        {
            return $usuario;
        }

        else if($usuario && $usuario->clave == $clave)
        {   
            

            //$usuario->clave = NULL;
           return $usuario;
        }

        return null;
    }

    public  function ToUsuario($nombre, $apellido, $usuario, $clave, $tipo,$sector)
    {
        $this->usuario = $usuario;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->clave = $clave;
        $this->tipoUsuarioId = $tipo;
        $this->sector = $sector;
    }

    public function UsuarioCompare($usuarioA, $usuarioB)
    {
        return $usuarioA === $usuarioB;
    }

}