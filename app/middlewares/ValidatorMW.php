<?php

use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as Handler;
 use Slim\Psr7\Response as Response;

class ValidatorMW
{


    public static function CheckAdminEmpleadoCliente(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil === PerfilUsuarioEnum::cliente || $perfil === PerfilUsuarioEnum::socio || $perfil === PerfilUsuarioEnum::mozo)
      { 
         $response = $handler->handle($request);
    
        return $response;
      }else {

         $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }
    
    public static function CheckPerfilMozoYCliente(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil === PerfilUsuarioEnum::cliente || $perfil === PerfilUsuarioEnum::mozo)
      { 
         $response = $handler->handle($request);
    
        return $response;
      }else {
         
         $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckPerfilSocio(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::socio)
      { 
        $response->getBody()->write('ok');
         $response = $handler->handle($request);
    
        return $response;
      }else {
         
         $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckPerfilMozo(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      

      // var_dump($perfil);
      if($perfil == PerfilUsuarioEnum::mozo)
      { 
        $response->getBody()->write('ok');
         $response = $handler->handle($request);
         
    
        return $response;
      }else {
         $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckEmpleados(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil =! PerfilUsuarioEnum::cliente)
      {
          $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }else {
         return $handler->handle($request);
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckEmpleadosParaTomarPedido(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::cliente || $perfil == PerfilUsuarioEnum::socio || $perfil == PerfilUsuarioEnum::mozo)
      {
          $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }else {
         return $handler->handle($request);
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckCocinero(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::cocinero)
      {
          $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }else {
         return $handler->handle($request);
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckBartender(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::bartender)
      {
          $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }else {
         return $handler->handle($request);
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

    public static function CheckCervecero(Request $request,Handler $handler)
    {
      $response = new Response();
      $perfil = $request->getAttribute('perfil');
      if($perfil == PerfilUsuarioEnum::cervecero)
      {
          $response->getBody()->write(json_encode(array("message" => 'No tiene permisos')));
          return $response;
      }else {
         return $handler->handle($request);
      }
      
      $response->getBody()
      ->write(json_encode(array("message" => 'El usuario ingresado no esta registrado')));
      return $response;  
    }

}