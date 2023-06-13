<?php
abstract class ProductoTipoEnum
{
    const bebidasAlcoholicas = 1;
    const bebidasSinAlcohol = 2;
    const snacks = 3;
    const dulces = 4;
    const comida = 5;
    // etc.

       public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "Bebidas alcoholicas";
                break;
            case 2:
                return "Bebidas sin alcohol";
                break;
            case 3:
                return "Snacks";
                break;
            case 4:
                return "Dulces";
                break;
            default:
            return "Comida";
            
            break;
        }
    }

    public static function GetEnumerator($descripcion){
        $descSinEspacios = str_replace( ' ', '', strtolower($descripcion));
        echo $descSinEspacios;
        switch($descSinEspacios)
        {
            case "bebidasalcoholicas":
                return 1;
                break;
            case "bebidassinalcohol":
                return 2;
                break;
            case "snacks":
                return 3;
                break;
            case "dulces":
                return 4;
                break;
            case "comida":
                return 5;
                break;
            default:
            return "-";
            
            break;
        }
    }
}
