<?php

class OperacionPorSector
{
    public $sectorId;
    public $nombreSector;
    public $cantOperaciones;
   
    public  function Mapper($sectorId, $nombreSector, $cantOperaciones)
    {
        $this->sectorId = $sectorId;
        $this->nombreSector = $nombreSector;
        $this->cantOperaciones = $cantOperaciones;
    }

    public function Compare($objetoA, $objetoB)
    {
        return $objetoA->id === $objetoB->id;
    }
}
