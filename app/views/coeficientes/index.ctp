<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Coeficiente-nombre'] = array();
$condiciones['Condicion.Coeficiente-tipo'] = array();
$condiciones['Condicion.Coeficiente-descripcion'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"coeficientes.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Coeficiente", "field"=>"id", "valor"=>$v['Coeficiente']['id'], "write"=>$v['Coeficiente']['write'], "delete"=>$v['Coeficiente']['delete']);
	$fila[] = array("model"=>"Coeficiente", "field"=>"nombre", "valor"=>$v['Coeficiente']['nombre']);
	$fila[] = array("model"=>"Coeficiente", "field"=>"tipo", "valor"=>$v['Coeficiente']['tipo']);
	$fila[] = array("model"=>"Coeficiente", "field"=>"valor", "valor"=>$v['Coeficiente']['valor']);
	$fila[] = array("model"=>"Coeficiente", "field"=>"descripcion", "valor"=>$v['Coeficiente']['descripcion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>