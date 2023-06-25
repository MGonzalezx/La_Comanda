<?php
class File
{   
    static function ReadFileTXT($root, $modo)
    {
        $file = fopen($root, $modo);
         $lineas = [];

        if(!$file)
        {
            echo "Error, no se encuentra el archivo";
        }
        else
        {
            while(!feof($file))
            {
                $retorno = fgets($file);

                $lineas = json_decode($retorno);
            }
          
        }

        fclose($file);
        return $lineas;
    }

    static function ArchivarSVC($header, $lista, $root, $filename, $extension)
    {
        if(count($lista) > 0)
        {
            $delimiter = ","; 
            $filename = "pedidos-lista_" . date('Y-m-d') . ".csv"; 
            // Create a file pointer 
            $f = fopen($root.$filename, 'w');  
             
            // Set column headers 
            fputcsv($f, $header, $delimiter); 
            for($i = 0; $i < count($lista); $i++)
            {
                fputcsv($f, $lista[$i], $delimiter); 
            }
          
            fseek($f, 0); 
             
            // Set headers to download file rather than displayed 
            header('Content-Type: text/csv'); 
            header('Content-Disposition: attachment; filename="' . $filename . '";'); 
             
            //output all remaining data on a file pointer 
            // fpassthru($f); 
        }
       
    }

    static function ReadFileCSV($root)
    {
        $file = fopen($root, 'r');
        $lineas = [];

        if(!$file)
        {
            echo "Error";
        }
        else
        {

            while (($row = fgetcsv($file)) !== false) {
                $lineas[] = $row;
            }
        }

        fclose($file);
        return $lineas;
    }


    static function ArchivarTXT($arrayObj, $obj, $root, $modo)
    {
        // $arrayObj = [$obj];
         $file = fopen($root, $modo);

        if(!$file)
        {
            echo "error";
        }
        else
        {
            file_put_contents($root, json_encode($arrayObj));
           // echo "se guardo el elemento: <br/>". $obj->ToJson($obj);
           echo "modificar esto al  guardar";
           return true;
        }

        fclose($file);
    }

    public static function GuardarImagen($imagen, $nombreCliente, $pedido, $fecha)
    {
        $userName = strtok($nombreCliente, "@");
       
        if(strlen($imagen["name"] > 0))
        {
            $extension = pathinfo($imagen["name"], PATHINFO_EXTENSION);

            if(File::VerificarExtension($extension))
            {
                $fechaStr = str_replace('/', '-', $fecha);
               
                $imagen["name"] = $userName."-".$pedido."-".$fechaStr.".".$extension;
                $moveTo = $_SERVER['DOCUMENT_ROOT']."/La_Comanda/app/Utilities/imagenesDeLaVenta/";
                move_uploaded_file($imagen["tmp_name"], $moveTo.$imagen["name"]);
                $fullPath =  $moveTo.$imagen["name"].$imagen["tmp_name"];
                return  $fullPath;
            }
            else { echo "No es un una imagen"; }
        }  
        else { echo "No se ingresó una imagen"; }           
    }

    public static function GuardarImagenBetter($imagen, $nombre, $nombreOtro, $fecha, $oldPath)
    {
        $extension = pathinfo($imagen["name"], PATHINFO_EXTENSION);
        $fechaStr = str_replace('/', '-', $fecha);
        $imagen["name"] = $nombre."-".$nombreOtro."-".$fechaStr.".".$extension;
        $moveTo = $_SERVER['DOCUMENT_ROOT']."\Utilities\imagenes\\";
        $fullPath =  $moveTo.$imagen["name"];

       if($oldPath == null)
       {
            if(strlen($imagen["name"] > 0))
            {
                if(File::VerificarExtension($extension))
                {
                    move_uploaded_file($imagen["tmp_name"], $fullPath);
                    return  $fullPath;
                }
                else { echo "No es un una imagen"; }
            }  
            else { echo "No se ingresó una imagen"; }  
       }
       else
       {


            $moveOldImageTo = $_SERVER['DOCUMENT_ROOT']."\Utilities\backup\\";
            $bkFullPath =  $moveOldImageTo."bk_".$imagen["name"];
            rename($oldPath, $bkFullPath);
            move_uploaded_file($imagen["tmp_name"], $fullPath);
            return $fullPath;
       }
                 
    }

    private static function VerificarExtension($name)
    {
        switch($name)
        {
            case "jfif":
            case "jpg":
            case "gif":
                return true;
                break;
            default:
                return false;
            break;
        }
    }


}


?>