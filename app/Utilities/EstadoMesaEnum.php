<?php
abstract class EstadoMesaEnum
{

    const esperandoPedido = 1;
    const comiendo = 2;
    const pagando = 3;
    const cerrada = 4;
    const disponible = 5;
    // etc.

       public static function GetDescription($intValue){
        switch($intValue)
        {
            case 1:
                return "Con cliente esperando pedido";
                break;
            case 2:
                return "Con cliente comiendo";
                break;
            case 3:
                return "Con cliente pagando";
                break;
            case 4:
                return "Cerrado";
                break;
            case 5:
                return "Disponible";
                break;
        }
    }
}

