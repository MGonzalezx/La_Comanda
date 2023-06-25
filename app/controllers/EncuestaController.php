<?php
require_once './db/EncuestaDb.php';
require_once './models/encuesta.php';
require_once './interfaces/IApiUsable.php';

class EncuestaController extends EncuestaDb implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $pedidoId = $parametros['pedidoId'];
        $puntajeMesa = $parametros['puntajeMesa'];
        $puntajeResto = $parametros['puntajeResto'];
        $puntajeMozo = $parametros['puntajeMozo'];
        $puntajeCocinero = $parametros['puntajeCocinero'];
        $comentario = $parametros['comentario'];

        $encuesta = new Encuesta();
        $encuesta->Mapper(intval($pedidoId), intval($puntajeMesa), intval($puntajeResto), intval($puntajeMozo), intval($puntajeCocinero), $comentario);

        EncuestaDb::Crear($encuesta);

        $payload = json_encode(array("mensaje" => "Encuesta creada con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = EncuestaDb::ObtenerTodos();

        if(count($lista) > 0)
        {
            foreach($lista as $encuesta)
            {
                echo $encuesta->comentario;
            }
        }

        $payload = json_encode(array("lista Encuestas" => $lista));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer Las encuestas: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args) {}
    
    public function BorrarUno($request, $response, $args) {}

    public function TraerUno($request, $response, $args) {
      try{
        $encuesta = EncuestaDb::ObtenerUno($args["id"]);

        $payload = json_encode($encuesta);
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer La Encuesta: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
?>