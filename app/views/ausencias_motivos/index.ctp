<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Banco-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Motivos de las Ausencias", "imagen"=>"ausencias_motivos.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"AusenciasMotivo", "field"=>"id", "valor"=>$v['AusenciasMotivo']['id'], "write"=>$v['AusenciasMotivo']['write'], "delete"=>$v['AusenciasMotivo']['delete']);
	$fila[] = array("model"=>"AusenciasMotivo", "field"=>"motivo", "valor"=>$v['AusenciasMotivo']['motivo']);
	$fila[] = array("model"=>"AusenciasMotivo", "field"=>"tipo", "valor"=>$v['AusenciasMotivo']['tipo']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>