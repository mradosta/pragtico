<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Accion'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Accion", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Accion", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Accion", "field"=>"etiqueta", "valor"=>$v['etiqueta']);
	$fila[] = array("model"=>"Accion", "field"=>"estado", "valor"=>$v['estado']);
	$cuerpo[] = $fila;
}


/**
* Creo la tabla.
*/
$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>true,
									"ordenEnEncabezados"=>false,
									"modificar"			=>true,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false));
									
$url = array("controller"=>"acciones", "action"=>"add", "Accion.controlador_id"=>$this->data['Controlador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Acciones", "cuerpo"=>$cuerpo));
//echo $formulario->bloque($formulario->tabla(am(array("cuerpo"=>$cuerpo), $opcionesTabla)), array("div"=>array("class"=>"unica")));


?>