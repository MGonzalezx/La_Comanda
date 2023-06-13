<?php
abstract class EstadoPedidoDetalleEnum
{
    const pendiente = 1;
    const enPreparacion = 2;
    const listoParaServir = 3;
    const entregado = 4;
    const cancelada = 5;

       public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "pendiente";
                break;
            case 2:
                return "En preparacion";
                break;
            case 3:
                return "Listo para servir";
                break;
            case 4:
                return "Entregado";
                break;
            case 5:
                return "Cancelada";
                break;
        }
    }
}
