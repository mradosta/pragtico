<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Usuario'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"RolesUsuario", "field"=>"id", "valor"=>$v['RolesUsuario']['id'], "write"=>$v['RolesUsuario']['write'], "delete"=>$v['RolesUsuario']['delete']);
	$fila[] = array("model"=>"Usuario", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Usuario", "field"=>"nombre_completo", "valor"=>$v['nombre_completo']);
 	$fila[] = array("model"=>"Usuario", "field"=>"ultimo_ingreso", "valor"=>$v['ultimo_ingreso']);
 	$fila[] = array("model"=>"Usuario", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"roles_usuarios", "action"=>"add", "RolesUsuario.rol_id"=>$this->data['Rol']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Usuarios", "cuerpo"=>$cuerpo));

?>