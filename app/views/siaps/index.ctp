<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Siap-version'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Siap", "imagen"=>"afip.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Siap", "field"=>"id", "valor"=>$v['Siap']['id'], "write"=>$v['Siap']['write'], "delete"=>$v['Siap']['delete']);
	$fila[] = array("tipo"=>"desglose", "id"=>$v['Siap']['id'], "update"=>"desglose1", "imagen"=>array("nombre"=>"siap_detalle.gif", "alt"=>"Detalles"), "url"=>'detalles');
	$fila[] = array("model"=>"Siap", "field"=>"version", "valor"=>$v['Siap']['version']);
	$fila[] = array("model"=>"Siap", "field"=>"observacion", "valor"=>$v['Siap']['observacion']);
	$cuerpo[] = $fila;
}
echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>