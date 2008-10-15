<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Variable-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"variables.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Variable", "field"=>"id", "valor"=>$v['Variable']['id'], "write"=>$v['Variable']['write'], "delete"=>$v['Variable']['delete']);
	$fila[] = array("model"=>"Variable", "field"=>"nombre", "valor"=>$v['Variable']['nombre']);
	$fila[] = array("model"=>"Variable", "field"=>"descripcion", "valor"=>$v['Variable']['descripcion']);
	$fila[] = array("model"=>"Variable", "field"=>"ejemplo", "valor"=>$v['Variable']['ejemplo']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>