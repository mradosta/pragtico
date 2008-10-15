<?php
/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['RopasDetalle'] as $k=>$v) {
	$fila = null;
	//$urls = array("delete"=>"../ropas_detalles/delete/" . $v['id'], "edit"=>"../ropas/edit/" . $this->data['Ropa']['id']);
	//$fila[] = array("tipo"=>"idDetail", "urls"=>$urls);
	$fila[] = array("model"=>"RopasDetalle", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"RopasDetalle", "field"=>"prenda", "valor"=>$v['prenda']);
	$fila[] = array("model"=>"RopasDetalle", "field"=>"tipo", "valor"=>$v['tipo']);
	$fila[] = array("model"=>"RopasDetalle", "field"=>"color", "valor"=>$v['color']);
	$fila[] = array("model"=>"RopasDetalle", "field"=>"modelo", "valor"=>$v['modelo']);
	$fila[] = array("model"=>"RopasDetalle", "field"=>"tamano", "valor"=>$v['tamano']);
	$cuerpo[] = $fila;
}


/**
* Creo la tabla.
*/
/*
$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>true,
									"ordenEnEncabezados"=>false,
									"modificar"			=>true,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false));


echo $formulario->bloque($formulario->tabla(am(array("cuerpo"=>$cuerpo), $opcionesTabla)), array("div"=>array("class"=>"unica")));
*/
$url = array("controller"=>"ropas_detalles", "action"=>"add", "RopasDetalle.ropa_id"=>$this->data['Ropa']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Prendas", "cuerpo"=>$cuerpo));

?>