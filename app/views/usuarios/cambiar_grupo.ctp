<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Usuario.id'] = array("value"=>$usuario['Usuario']['id']);
$campos['Usuario.grupos'] = array("type"=>"checkboxMultiple", "options"=>$grupos);

$fieldsets[] = array("campos"=>$campos);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Cambio de grupo para " . $usuario['Usuario']['nombre_completo'] . " (" . $usuario['Usuario']['nombre'] . ")", "imagen"=>"usuarios.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("action"=>"cambiar_grupo")));
?>