<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['AusenciasMotivo.id'] = array();
$campos['AusenciasMotivo.motivo'] = array();
$campos['AusenciasMotivo.tipo'] = array();
$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Motivos de Ausencia", "imagen"=>"motivos.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>