<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';

class UsuarioController extends Usuario implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $nombre = $parametros['nombre'];
        $apellido = $parametros['apellido'];
        $tipo = $parametros['tipo'];
        $usr = new Usuario();
        
        $usr->ToUsuario($nombre, $apellido, $usuario, $clave, $tipo);

        //Checkeamos su usuario(es un email) si ya está registrado
        try
        {
          $listaUsuarios = $usr->obtenerTodos();
          if($listaUsuarios != null && count($listaUsuarios) > 0)
          {
             
              foreach($listaUsuarios as $usrDb)
              {
                 $userComparison = $usr->UsuarioCompare($usr->usuario, $usrDb->usuario);
                  if($userComparison)
                  {
                    $payload = json_encode(array("mensaje" => "Usuario ya registrado"));
                    $response->getBody()->write($payload);
                    return $response->withHeader('Content-Type', 'application/json');
                  }
              }
          }

          
        }
        catch(PDOException $e){
          $error = json_encode(array("mensaje" => "Error al crear el usuario: ".$e->getMessage()));
          $response->getBody()->write($error);
          return $response->withHeader('Content-Type', 'application/json');

        }
        
        // Intentamos crearlo.
        try{
          $usr->crearUsuario();
        }
        catch(PDOException $e)
        {
          $error = json_encode(array("mensaje" => "Error al crear el usuario: ".$e->getMessage()));
          $response->getBody()->write($error);
          return $response->withHeader('Content-Type', 'application/json');

        }
        
        $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
       
    }

    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = Usuario::obtenerTodos();
        $sinclave = [];
        foreach($lista as $usuario)
        {
          $usuarioNew= new Usuario();
          $usuarioNew->ToUsuario( $usuario->nombre,  $usuario->apellido,  $usuario->usuario, null,  $usuario->tipoUsuarioId);
          $usuarioNew->id = $usuario->id;
          array_push($sinclave, $usuarioNew);
        }

        $payload = json_encode(array("listaUsuario" => $sinclave));

       

        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer los usuarios: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        // $id = $parametros['id'];
        
        $nombre = $parametros['nombre'];
        Usuario::obtenerUsuario($nombre);
        // Usuario::modificarUsuario($nombre);
        $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuarioId = $parametros['usuarioId'];
        Usuario::borrarUsuario($usuarioId);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
}
