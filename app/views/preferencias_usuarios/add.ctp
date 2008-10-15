<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['PreferenciasUsuario.id'] = array();
$campos['PreferenciasUsuario.preferencia_id'] = array(	"empty"			=> true,
														"options"		=> "listable",
														"recursive"		=> -1,
														"order"			=> "Preferencia.nombre",
														"displayField"	=> "Preferencia.nombre",
														"model"			=> "Preferencia");
$campos['PreferenciasUsuario.preferencias_valor_id'] = array("label"=>"Valor", "valor"=>"PreferenciasValor.valor", "type"=>"relacionado", "relacion"=>"PreferenciasUsuario.preferencia_id", "url"=>"preferencias_usuarios/valores_relacionado");

$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"registro de Preferencias del Usuario", "imagen"=>"preferencias.gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("fieldset"=>$fieldset));
?>