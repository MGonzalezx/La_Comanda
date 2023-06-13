<?php
class DateHelper
{   

    static function FullDateHMS()
    {
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $fecha = date("Y/m/d h:i:sa");
        return $fecha;
    }

    static function DateAMD()
    {
        date_default_timezone_set("America/Argentina/Buenos_Aires");
        $fecha = date("Y/m/d");
        return $fecha;
    }

  

}


?>
