<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Grupo-nombre'] = array();
$condiciones['Condicion.Grupo-estado'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"grupos.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Grupo']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"usuarios.gif", "alt"=>"Usuarios"), "url"=>'usuarios');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose2", "imagen"=>array("nombre"=>"acciones.gif", "alt"=>"Acciones"), "url"=>'acciones');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose3", "imagen"=>array("nombre"=>"parametros.gif", "alt"=>"Parametros"), "url"=>'parametros');
	$fila[] = array("model"=>"Grupo", "field"=>"id", "valor"=>$id, "write"=>$v['Grupo']['write'], "delete"=>$v['Grupo']['delete']);
	$fila[] = array("model"=>"Grupo", "field"=>"nombre", "valor"=>$v['Grupo']['nombre']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array("model"=>"Grupo", "field"=>"estado", "valor"=>$v['Grupo']['estado']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>