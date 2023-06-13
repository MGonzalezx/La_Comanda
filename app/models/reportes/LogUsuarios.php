<?php

class LogUsuarios
{
    public $id;
    public $usuarioId;
    public $nombre;
    public $apellido;
    public $fecha;
    public $perfil;
   
    public  function Mapper($usuarioId, $nombre, $apellido, $fecha, $perfil)
    {
        $this->usuarioId = $usuarioId;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->fecha = $fecha;
        $this->perfil = $perfil;
    }

    public function Compare($logA, $logB)
    {
        return $logA->id === $logB->id;
    }


    
}