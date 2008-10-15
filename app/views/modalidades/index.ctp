<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Modalidad-codigo'] = array();
$condiciones['Condicion.Modalidad-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"modalidades.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Modalidad", "field"=>"id", "valor"=>$v['Modalidad']['id'], "write"=>$v['Modalidad']['write'], "delete"=>$v['Modalidad']['delete']);
	$fila[] = array("model"=>"Modalidad", "field"=>"codigo", "valor"=>$v['Modalidad']['codigo']);
	$fila[] = array("model"=>"Modalidad", "field"=>"nombre", "valor"=>$v['Modalidad']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>