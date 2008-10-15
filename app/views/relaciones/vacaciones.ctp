<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Vacacion'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Vacacion", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Vacacion", "field"=>"desde", "valor"=>$v['desde']);
	$fila[] = array("model"=>"Vacacion", "field"=>"hasta", "valor"=>$v['hasta']);
 	$fila[] = array("model"=>"Vacacion", "field"=>"observacion", "valor"=>$v['observacion']);
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

$url = array("controller"=>"vacaciones", "action"=>"add", "Vacacion.relacion_id"=>$this->data['Relacion']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "texto"=>"Vacaciones"));
echo $formulario->bloque($formulario->tabla(am(array("cuerpo"=>$cuerpo), $opcionesTabla)), array("div"=>array("class"=>"unica")));

?>