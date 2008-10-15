<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['PreferenciasValor.id'] = array();
$campos['PreferenciasValor.preferencia_id'] = array(	"lov"=>array("controller"	=>	"preferencias",
																"seleccionMultiple"	=> 	0,
																	"camposRetorno"	=>	array("Preferencia.nombre")));
$campos['PreferenciasValor.valor'] = array();
$campos['PreferenciasValor.predeterminado'] = array();

$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"registro de valor de la preferencia", "imagen"=>"preferencias_valores.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>