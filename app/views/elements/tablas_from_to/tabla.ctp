<?php

/**
* Creo la tabla.
*/
if(empty($opciones['class'])) {
	$opciones['class'] = "izquierda";
}
$tablaUbicacion = ucfirst($opciones['class']);
$divId = "tablaFromTo" . $tablaUbicacion;
$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>false,
									"ordenEnEncabezados"=>false,
									'permisos'			=>false,
									"modificar"			=>false,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false,
									"omitirMensajeVacio"=>true,
									"class"				=>$opciones['class']));

$tabla = $formulario->tabla(am(array('cuerpo' => $cuerpo, "encabezado"=>$encabezados), $opcionesTabla));
//echo $formulario->codeBlock("transformarTabla" . $tablaUbicacion . "();bindearTabla" . $tablaUbicacion . "()");
echo $formulario->bloque($tabla, array("div"=>array("id"=>$divId)));

/**
* Si me llega el argumento selectedId, asumo que viene desde un request ajax que hizo un autocomplete, entonces,
* asumo que debo rebindear el codigo JS.
*/
if($this->params['isAjax'] == 1) {
	echo $formulario->codeBlock("transformarTabla" . $tablaUbicacion . "();bindearTabla" . $tablaUbicacion . "();");
	//$this->addScript("transformarTabla" . $tablaUbicacion . "();bindearTabla" . $tablaUbicacion . "()");
}



	
?>