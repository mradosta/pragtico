<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.PreferenciasUsuario-usuario_id'] = array(	"lov"=>array("controller"	=>	"usuarios",
																			"camposRetorno"	=>array("Usuario.nombre",
																									"Usuario.nombre_completo")));

$condiciones['Condicion.PreferenciasUsuario-valor'] = array();
$condiciones['Condicion.Preferencia-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Preferencias de Usuario", "imagen"=>"preferencias.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['PreferenciasUsuario']['id'];
	//$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"usuarios.gif", "alt"=>"Usuarios"), "url"=>'usuarios');
	$fila[] = array("model"=>"PreferenciasUsuario", "field"=>"id", "valor"=>$id, "write"=>$v['PreferenciasUsuario']['write'], "delete"=>$v['PreferenciasUsuario']['delete']);
	$fila[] = array("model"=>"Usuario", "field"=>"nombre", "valor"=>$v['Usuario']['nombre'], "nombreEncabezado"=>"Usuario");
	$fila[] = array("model"=>"Preferencia", "field"=>"nombre", "valor"=>$v['Preferencia']['nombre']);
	$fila[] = array("model"=>"PreferenciasValor", "field"=>"valor", "valor"=>$v['PreferenciasValor']['valor']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>