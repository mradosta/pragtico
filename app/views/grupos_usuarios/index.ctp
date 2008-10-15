<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Grupo-nombre'] = array();
$condiciones['Condicion.Grupo-estado'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"buscar.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['GruposUsuario']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"usuarios.gif", "alt"=>"Usuarios"), "url"=>'usuarios');
	$fila[] = array("model"=>"GruposUsuario", "field"=>"id", "valor"=>$id, "write"=>$v['GruposUsuario']['write'], "delete"=>$v['GruposUsuario']['delete']);
	$fila[] = array("model"=>"Grupo", "field"=>"nombre", "valor"=>$v['Grupo']['nombre']);
	$fila[] = array("model"=>"Grupo", "field"=>"estado", "valor"=>$v['Grupo']['estado']);
	$fila[] = array("model"=>"Grupo", "field"=>"observacion", "valor"=>$v['Grupo']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>