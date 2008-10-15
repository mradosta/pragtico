<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Cuenta'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Cuenta", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Banco", "field"=>"nombre", "nombreEncabezado"=>"Banco", "valor"=>$v['Sucursal']['Banco']['nombre']);
	$fila[] = array("model"=>"Sucursal", "field"=>"direccion", "nombreEncabezado"=>"Sucursal", "valor"=>$v['Sucursal']['direccion']);
	$fila[] = array("model"=>"Cuenta", "field"=>"tipo", "valor"=>$v['tipo']);
	$fila[] = array("model"=>"Cuenta", "field"=>"numero", "valor"=>$v['numero']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"Cuentas", "action"=>"add", "Cuenta.empleador_id"=>$this->data['Empleador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Cuentas", "cuerpo"=>$cuerpo));

?>