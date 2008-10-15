<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Rubro-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"rubros.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Rubro']['id'], "update"=>"desglose1", "imagen"=>array("nombre"=>"empleadores.gif", "alt"=>"Empleadores"), "url"=>'empleadores');
	$fila[] = array("model"=>"Rubro", "field"=>"id", "valor"=>$v['Rubro']['id'], "write"=>$v['Rubro']['write'], "delete"=>$v['Rubro']['delete']);
	$fila[] = array("model"=>"Rubro", "field"=>"nombre", "valor"=>$v['Rubro']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>