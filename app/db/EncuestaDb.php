<?php

require_once './models/Encuesta.php';

class EncuestaDb extends Encuesta 
{
    public function Crear($encuesta)
    {
        try 
        {
            $objAccesoDatos = AccesoDatos::obtenerInstancia();
            $consulta = $objAccesoDatos->prepararConsulta(
                "INSERT INTO encuestas (pedidoId, puntajeMesa, puntajeResto, puntajeMozo, puntajeCocinero, comentario) 
                 VALUES (:pedidoId, :puntajeMesa, :puntajeResto, :puntajeMozo, :puntajeCocinero, :comentario)");
            $consulta->bindValue(':pedidoId', $encuesta->pedidoId, PDO::PARAM_INT);
            $consulta->bindValue(':puntajeMesa', $encuesta->puntajeMesa, PDO::PARAM_INT);
            $consulta->bindValue(':puntajeResto', $encuesta->puntajeResto, PDO::PARAM_INT);
            $consulta->bindValue(':puntajeMozo', $encuesta->puntajeMozo, PDO::PARAM_INT);
            $consulta->bindValue(':puntajeCocinero', $encuesta->puntajeCocinero, PDO::PARAM_INT);
            $consulta->bindValue(':comentario', $encuesta->comentario, PDO::PARAM_STR);
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
            $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas");
            $consulta->execute();

            return $consulta->fetchAll(PDO::FETCH_CLASS, 'Encuesta');
        }
        catch(PDOException $e)
        {
            throw $e;
        }
    }

    public static function ObtenerUno($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM encuestas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchObject('Encuesta');
    }
}
?>