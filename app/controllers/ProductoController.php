<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';
require_once './Utilities/SectorEnum.php';
require_once './Utilities/TipoProductoEnum.php';


class ProductoController extends Producto implements IApiUsable
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $descripcion = $parametros['descripcion'];
        $precio = $parametros['precio'];
        $sectorId = $parametros['sectorId'];
        $tipoProductoId = $parametros['tipoProductoId'];

        $prod = new Producto();
        $prod->ToProducto($descripcion, $precio, intval($sectorId), intval($tipoProductoId));
        try
        {
          $listaProductos = $prod->ObtenerTodos();
          if($listaProductos != null && count($listaProductos) > 0)
          {
             
              foreach($listaProductos as $prodDb)
              {
                $prodComparison = $prod->ProductoCompare($prod, $prodDb);
                if($prodComparison)
                {
                  $payload = json_encode(array("mensaje" => "El producto ya existe"));
                  $response->getBody()->write($payload);
                  return $response->withHeader('Content-Type', 'application/json');
                }
              }
          }
        }
        catch(PDOException $e){
          $error = json_encode(array("mensaje" => "Error al crear el usuario: ".$e->getMessage()));
          $response->getBody()->write($error);
        }

        $prod->crearProducto();
        $payload = json_encode(array("mensaje" => "Producto creado con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
      try{
        $lista = Producto::obtenerTodos();
        if(count($lista) > 0)
        {
            foreach($lista as $producto)
            {
                $producto->sectorDescripion = SectorEnum::GetDescription($producto->sectorId);
                $producto->tipoProductoDescripion = ProductoTipoEnum::GetDescription($producto->tipoProductoId);
            }
        }

        $payload = json_encode(array("listaUsuario" => $lista));
        $response->getBody()->write($payload);
      }
      catch(PDOException $e)
      {
          $error = json_encode(array("mensaje" => "Error al traer los productos: ".$e->getMessage()));
          $response->getBody()->write($error);
      }  
        
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
      
        
      
    }

    public function BorrarUno($request, $response, $args)
    {

    }

    public function TraerUno($request, $response, $args)
    {
      
    }

    public function LoadCSV($request, $response, $args)
    {
      $parametros = $request->getParsedBody();
      $archivo = $_FILES['archivoCSV'];

      try{
        $listaproductos = File::ReadFileCSV($archivo['tmp_name']);
      }
      catch(Exception $e)
      {
        $error = json_encode(array("error" => "Ocurrió un problema al  intentar leer el archivo, por favor contacte al  administrador."));
          $response->getBody()->write($error);
          return $response;
      }

      for($i = 1; $i < count($listaproductos); $i++)
      {

        $producto = new Producto();
        $producto->descripcion = $listaproductos[$i][0];
        $producto->tipoProductoId = ProductoTipoEnum::GetEnumerator($listaproductos[$i][1]);
        $producto->sectorId = SectorEnum::GetEnumerator($listaproductos[$i][2]);
        $producto->precio = $listaproductos[$i][3];

         if(!$this->VerificarSiExisteProducto($producto))
        {
            try
            {
              
              $producto->crearProducto($producto);
            }
            catch(PDOException $e)
            {
              $error = json_encode(array("mensaje" => "Ocurrió un problema al  intentar guardar el producto, por favor contacte al  administrador.". $e->getMessage()));
              $response->getBody()->write($error);
              return $response;
            }
        }
        else
        {
          $error = json_encode(array("mensaje" => "El producto de la linea ".$i."- ".$producto->descripcion. " ya existe."));
              $response->getBody()->write($error);
              return $response;
        }
        
        
      }

      $payload = json_encode(array("mensaje" => (count($listaproductos) - 1)." Productos creados con exito"));
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
      
      
    }

    private function VerificarSiExisteProducto($producto)
    {
        $retorno = 0;
        $listaProductos = Producto::ObtenerTodos();
          if($listaProductos != null && count($listaProductos) > 0)
          {
             
              foreach($listaProductos as $prodDb)
              {
                $prodComparison = $producto->ProductoCompare($producto, $prodDb);
                if($prodComparison)
                {
                  $retorno = true;
                }
              }
          }

          return $retorno;

    }


}
