<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.AusenciasSeguimiento-fecha'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Seguimiento de las Ausencias", "imagen"=>"buscar.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"id", "valor"=>$v['AusenciasSeguimiento']['id'], "write"=>$v['AusenciasSeguimiento']['write'], "delete"=>$v['AusenciasSeguimiento']['delete']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"fecha", "valor"=>$v['AusenciasSeguimiento']['motivo']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"dias", "valor"=>$v['AusenciasSeguimiento']['dias']);
	$fila[] = array("model"=>"AusenciasSeguimiento", "field"=>"observacion", "valor"=>$v['AusenciasSeguimiento']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>