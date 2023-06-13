<?php
require_once './Utilities/PerfilUsuarioEnum.php';

use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as Handler;
 use Slim\Psr7\Response as Response;

class AuthTokenMW
{
      private static $claveSecreta = 'T3sT$JWT';
    private static $tipoEncriptacion = ['HS256'];

    public static function CrearToken($datos)
    {
        $ahora = time();

        $payload = array(
        	'iat'=>$ahora,
            'exp' => $ahora + (60 * 30),
            'aud' => self::Aud(),
            'data' => $datos,
            'app'=> "API REST La Comanda"
        );
        return JWT::encode($payload, self::$claveSecreta);
    }

    public static function VerificarToken($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        try {
            $decodificado = JWT::decode(
                $token,
                self::$claveSecreta,
                self::$tipoEncriptacion
            );
        } catch (Exception $e) {
            throw $e;
        }
        if ($decodificado->aud !== self::Aud()) {
            throw new Exception("No es el usuario valido");
        }
    }


    public static function ObtenerPayLoad($token)
    {
        if (empty($token)) {
            throw new Exception("El token esta vacio.");
        }
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }

    public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }

    public static function AutenticarUsuario(Request $request,Handler $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        $esValido = false;
        $response = new Response();

        try {
            AuthTokenMW::VerificarToken($token);
            $esValido = true;
        }
        catch(Exception $e) {
            
                $response->getBody()
                ->write(json_encode(array("message" => 'Algo no esta bien..'.$e->getMessage())));
                return $response;  
        }

        if(!$esValido) {
            $response->getBody()->write(json_encode(array("message" => 'Algo no esta bien..')));
            return $response;
            
                
        }

        $tokenData = AuthTokenMW::ObtenerData($token);
        $request = $request->withAttribute('perfil', $tokenData->perfil);
        $request = $request->withAttribute('usuario', $tokenData->usuario);
        $request = $request->withAttribute('usuarioId', $tokenData->usuarioId);


        return $handler->handle($request);
        
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

}