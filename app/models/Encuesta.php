<?php

class Encuesta
{
    public $id;
    public $pedidoId;
    public $puntajeMesa;
    public $puntajeResto;
    public $puntajeMozo;
    public $puntajeCocinero;
    public $comentario;
   
    public  function Mapper($pedidoId, $puntajeMesa, $puntajeResto, $puntajeMozo, $puntajeCocinero, $comentario)
    {
        $this->pedidoId = $pedidoId;
        $this->puntajeMesa = $puntajeMesa;
        $this->puntajeResto = $puntajeResto;
        $this->puntajeMozo = $puntajeMozo;
        $this->puntajeCocinero = $puntajeCocinero;
        $this->comentario = $comentario;
    }

    public function Compare($encuestaA, $encuestaB)
    {
        return $encuestaA->id === $encuestaB->id;
    }
}
?>