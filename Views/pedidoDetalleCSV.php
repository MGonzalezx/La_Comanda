<?php

class PedidoDetalleCSV
{
    // $fields = array('ID', 'COD PEDIDO', 'COD MESA', 'CLIENTE', 'EMAIL', 'MOZO', 'PRECIO TOTAL', 'FECHA'); 

    public $id;
    public $codigoPedido;
    public $codigoMesa;
    public $cliente;
    public $emailCliente;
    public $emailMozo;
    public $precioTotal;
    public $fecha;
    public $nombre;
    public $apellido;
    
    public  function ToPedidoView($codigoPedido, $tiempoInicial, $estado, $productoId, $cantidad)
    {
            $this->productoId = $productoId;
            $this->cantidad = $cantidad;
            $this->estadoId = $estado;
            $this->tiempoInicial = $tiempoInicial;
            $this->$codigoPedido = $codigoPedido;
    }


}