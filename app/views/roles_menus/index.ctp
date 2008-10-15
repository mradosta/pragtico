<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Rol-nombre'] = array();
$condiciones['Condicion.RolesMenu-estado'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"roles.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['RolesMenu']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"usuarios.gif", "alt"=>"Usuarios"), "url"=>'usuarios');
	$fila[] = array("model"=>"Rol", "field"=>"id", "valor"=>$id, "write"=>$v['RolesMenu']['write'], "delete"=>$v['RolesMenu']['delete']);
	$fila[] = array("model"=>"Rol", "field"=>"nombre", "valor"=>$v['Rol']['nombre']);
	$fila[] = array("model"=>"RolesMenu", "field"=>"estado", "valor"=>$v['RolesMenu']['estado']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>