<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.ObrasSocial-codigo'] = array();
$condiciones['Condicion.ObrasSocial-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Obra Social", "imagen"=>"obras_sociales.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"ObrasSocial", "field"=>"id", "valor"=>$v['ObrasSocial']['id'], "write"=>$v['ObrasSocial']['write'], "delete"=>$v['ObrasSocial']['delete']);
	$fila[] = array("model"=>"ObrasSocial", "field"=>"codigo", "valor"=>$v['ObrasSocial']['codigo']);
	$fila[] = array("model"=>"ObrasSocial", "field"=>"nombre", "valor"=>$v['ObrasSocial']['nombre']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>