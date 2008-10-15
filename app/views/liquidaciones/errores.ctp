<?php

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['LiquidacionesError'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"tipo", "valor"=>$v['tipo']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"gravedad", "valor"=>$v['gravedad']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"concepto", "valor"=>$v['concepto']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"formula", "valor"=>$v['formula']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"descripcion", "valor"=>$v['descripcion']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"descripcion_adicional", "valor"=>$v['descripcion_adicional']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"recomendacion", "valor"=>$v['recomendacion']);
	$cuerpo[] = $fila;
}


/**
* Creo la tabla.
*/
$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>false,
									"ordenEnEncabezados"=>false,
									"modificar"			=>false,
									"seleccionMultiple"	=>false,
									"permisos"			=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false));

echo $this->renderElement("desgloses/agregar", array("texto"=>"Errores de la Liquidacion"));
echo $formulario->bloque($formulario->tabla(am(array("cuerpo"=>$cuerpo), $opcionesTabla)), array("div"=>array("class"=>"unica")));

?>