<?php

/**
 * Creo la miga de pan.
 */
$formulario->addCrumb($this->name);
$formulario->addCrumb("Grilla");


/**
 * Me aseguro que todas las variables queden inicializadas.
 */
if(!isset($botonesExtra)) {
	$botonesExtra = array();
}
if(!isset($accionesExtra)) {
	$accionesExtra = array();
}
if(!isset($separadorRetorno)) {
	$separadorRetorno = "";
}
if(empty($opcionesForm)) {
	$opcionesForm['action'] = "index";
}
if(!isset($pie)) {
	$pie = array();
}

/**
 * Creo un bloque con caja redondeada entre las condiciones, los botones y las opciones lov (si las hubiese).
 */
$lov = $this->renderElement("index/lov");

/**
 * Pongo el nombre del controller como un parametro, aunque no lo use, de modo de que el cache cree un archivo 
 * por cada controlador.
 */
//$botones = $this->element("index/buscadores", array("cache"=>"+30 day", $this->name=>"name", "botonesExtra"=>$botonesExtra, "opcionesForm"=>$opcionesForm));
$botones = $this->element("index/buscadores", array("botonesExtra"=>$botonesExtra, "opcionesForm"=>$opcionesForm));

$bloques[] = $formulario->bloque(am($condiciones, $botones, $lov), array("div"=>array("class"=>"unica")));


/**
 * Agrego los botones de las acciones.
 * Nuevo y eliminar desde la seleccion multiple.
 *
 * Pongo el nombre del controller como un parametro, aunque no lo use, de modo de que el cache cree un archivo 
 * por cada controlador.
 */
//$acciones = $this->element("index/acciones", array("cache"=>"+30 day", $this->name=>"name", "accionesExtra"=>$accionesExtra));
$acciones = $this->element("index/acciones", array("accionesExtra"=>$accionesExtra));


if(!isset($opcionesTabla)) {
	//$opcionesTabla = array("tabla"=>array("permisos"=>false));
	$opcionesTabla = array();
}

/**
* Seteo las opcion para el caso que se comporte como una lov.
*/
if(!empty($this->params['named']['retornarA']) && !empty($this->params['named']['camposRetorno'])) {

	$opcionesTabla =  array("tabla"=>
								array(	"seleccionLov"		=> array("retornarA"		=> $this->params['named']['retornarA'],
																	"separadorRetorno"	=> $this->params['named']['separadorRetorno'],
																	"camposRetorno"		=> $this->params['named']['camposRetorno']),
										"eliminar"			=> false,
										"modificar"			=> false,
										"seleccionMultiple"	=> true,
										"mostrarEncabezados"=> true,
										"zebra"				=> true,
										"mostrarIds"		=> false));
}
//else {
//	$opcionesTabla =  array("tabla"=> array("permisos" => true));
//}

/**
 * Creo un bloque con el paginador superior e inferior.
 */
//$paginador = $this->element("index/paginador", array("cache"=>"+30 day", $this->name=>"name"));
$paginador = $this->element("index/paginador");
$bloque_paginador_superior = $formulario->bloque($paginador, array("div"=>array("class"=>"paginador_superior")));
$bloque_paginador_inferior = $formulario->bloque($paginador, array("div"=>array("class"=>"paginador_inferior")));

/**
 * Creo la tabla.
 */
if(!isset($cuerpo)) {
	$cuerpo = null;
}


/**
 * Agrego el cuerpo de la tabla, siempre y cuendo este no este vacio y no tenga seteado el valor de omitir mensaje vacio.
 */
if(!empty($opcionesTabla['tabla']['contenido'])) {
	$bloques[] = $opcionesTabla['tabla']['contenido'];
}
else if(!((isset($opcionesTabla['tabla']['omitirMensajeVacio']) && $opcionesTabla['tabla']['omitirMensajeVacio'] === true) && empty($cuerpo))) {
	$tabla = $formulario->tag("div", $formulario->tabla(am(array("cuerpo"=>$cuerpo, "pie"=>$pie), $opcionesTabla)), array("class"=>"tabla"));
	$bloque_superior = $formulario->bloque($acciones . $bloque_paginador_superior, array("div"=>array("class"=>"bloque_superior_index")));
	$bloque_inferior = $formulario->bloque($bloque_paginador_inferior, array("div"=>array("class"=>"bloque_inferior_index")));
	$bloques[] = $formulario->bloque($bloque_superior . $tabla . $bloque_inferior, array("div"=>array("class"=>"unica")));
}


/**
 * Creo el formulario y pongo todo dentro.
 */
$form = $formulario->form($bloques, $opcionesForm);


/**
* Pongo todo dentro de un div (index) y muestro el resultado, siempre y cuando no sea un request ajax.
* Si es ajax y creo el div, voy a meter un div, dentro de otro, dentro de otro, dentro de otro....
*/
if(!empty($this->params['isAjax']) && $this->params['isAjax'] == "1") {
	echo $formulario->bloque($form);
}
else {
	echo $formulario->bloque($form, array("div"=>array("id"=>"index", "class"=>"index")));
}	


?>