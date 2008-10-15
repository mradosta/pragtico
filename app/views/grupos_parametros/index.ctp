<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.GruposParametro-nombre'] = array();
$condiciones['Condicion.GruposParametro-valor'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Parametros del Grupo", "imagen"=>"parametros.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"GruposParametro", "field"=>"id", "valor"=>$v['GruposParametro']['id'], "write"=>$v['GruposParametro']['write'], "delete"=>$v['GruposParametro']['delete']);
	$fila[] = array("model"=>"Grupo", "field"=>"nombre", "valor"=>$v['Grupo']['nombre'], "nombreEncabezado"=>"Grupo");
	$fila[] = array("model"=>"GruposParametro", "field"=>"nombre", "valor"=>$v['GruposParametro']['nombre']);
	$fila[] = array("model"=>"GruposParametro", "field"=>"valor", "valor"=>$v['GruposParametro']['valor']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>