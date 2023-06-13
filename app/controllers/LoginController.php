<?php
require_once './models/reportes/LogUsuarios.php';
require_once './models/Mesa.php';
require_once './db/LogUsuarioDb.php';
require_once './interfaces/IApiUsable.php';
require_once './Utilities/EstadoMesaEnum.php';
require_once './middlewares/AuthTokenMW.php';

class LoginController
{
    public function Login($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $fecha = DateHelper::DateAMD();
      
      $usuariofromDb =  Usuario::obtenerUsuarioPorUsuarioYClave($usuario, $clave);
       //var_dump($usuariofromDb);
      if($usuariofromDb)
      {
        $datos = array('usuario' => $usuario, 'perfil' => $usuariofromDb->tipoUsuarioId, 'usuarioId' => $usuariofromDb->id);
        $request = $request->withAttribute('usuarioId', $usuariofromDb->id);

        $logUsuario = new LogUsuarios();
        $logUsuario->Mapper($usuariofromDb->id, $usuariofromDb->nombre, $usuariofromDb->apellido, $fecha, $usuariofromDb->tipoUsuarioId);
        $logUsuarioDb = new LogUsuarioDb();
        try
        {
            $logUsuarioDb->CrearLog($logUsuario);
        }
        catch(PDOException $e)
        {
          $response->getBody()->write(json_encode(array("message" => 'Algo  ocurrio  al intentar guardar el log: '.$e)));
          return $response;
        }
    
        $token = AuthTokenMW::CrearToken($datos);

        $payload = json_encode(array('jwt' => $token));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
      }
     
      
      $response->getBody()->write("No tiene permisos para ingresar.");
      return $response
        ->withHeader('Content-Type', 'application/json');
    }
}
