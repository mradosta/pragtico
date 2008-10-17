<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Creo los inputs para ingresar las condiciones.
*/
$condiciones[] = $formulario->input("Condicion.Actividad-codigo");
$condiciones[] = $formulario->input("Condicion.Actividad-nombre");
$condiciones[] = $formulario->input("Condicion.Actividad-tipo");
$bloque_condiciones = $formulario->bloque($condiciones, array("div"=>array("id"=>"condiciones")));


/**
* Creo un bloque (fieldset) con las condiciones.
*/
$bloque_condiciones = $formulario->bloque($bloque_condiciones, array("fieldset"=>array("legend"=>"Buscar actividad por:", "imagen"=>"buscar.gif")));



/**
* Creo un bloque con caja redondeada entre las condiciones, los botones y las opciones lov (si las hubiese).
*/
$lov = $this->renderElement("index/lov");
$botones = $this->renderElement("index/buscadores");

$bloques[] = $formulario->bloque(am($bloque_condiciones, $botones, $lov), array("caja_redondeada"=>true));


/**
* Agrego los botones de las acciones.
* Nuevo y eliminar desde la seleccion multiple.
*/
$bloques[] = $this->renderElement("index/acciones");


/**
* Creo un bloque con las opciones para seleccion multiple de registros.
* Seleccionar Todo / Nada / Invertir
*/
$bloque_seleccion = $this->renderElement("index/seleccion");


/**
* Seteo las opcion para el caso que se comporte como una lov.
*/
$opcionesTabla = array();
$url = array();
if($this->layout == "lov" && isset($retornarA)
	&& !empty($retornarA) && isset($camposRetorno)
		&& !empty($camposRetorno)) {

	if(!isset($separadorRetorno)) {
		$separadorRetorn = "";
	}
	
	$url['retornarA'] = $retornarA;
	$url['separadorRetorno'] = $separadorRetorno;
	$url['camposRetorno'] = $camposRetorno;
	$url['layout'] = $this->layout;

	$opcionesTabla =  array("tabla"=>
								array(	"seleccionLov"		=>array("retornarA"			=> $retornarA,
																	"separadorRetorno"	=> $separadorRetorno,
																	"camposRetorno"		=> $camposRetorno),
										"eliminar"			=>false,
										"modificar"			=>false,
										"seleccionMultiple"	=>false,
										"mostrarEncabezados"=>true,
										"zebra"				=>true,
										"mostrarIds"		=>false));
}


/**
* Creo un bloque con el paginador.
*/
$bloque_paginador[] = $this->renderElement("index/paginador", array("url"=>$url));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Actividad", "field"=>"id", "valor"=>$v['Actividad']['id']);
	$fila[] = array("model"=>"Actividad", "field"=>"codigo", "valor"=>$v['Actividad']['codigo'], "class"=>"derecha");
	$fila[] = array("model"=>"Actividad", "field"=>"nombre", "valor"=>$v['Actividad']['nombre']);
	$fila[] = array("model"=>"Actividad", "field"=>"tipo", "valor"=>$v['Actividad']['tipo']);
	$fila[] = array("model"=>"Actividad", "field"=>"observacion", "valor"=>$v['Actividad']['observacion']);
	$cuerpo[] = $fila;
}


/**
* Creo la tabla.
*/
$tabla = $formulario->tabla(am(array("cuerpo"=>$cuerpo), $opcionesTabla));


/**
* Pongo la tabla, junto al paginador y el bloque de seleccion en un bloque.
*/
$bloques[] = $formulario->bloque(am($bloque_paginador, $bloque_seleccion, $tabla), array("div"=>array("id"=>"tabla")));


/**
* Creo el formulario y pongo todo dentro.
*/
$form = $formulario->form($bloques, array("action"=>"index"));


/**
* Pongo todo dentro de un div (index) y muestro el resultado.
*/
echo $formulario->bloque($form, array("div"=>array("id"=>"index", "class"=>"index")));

?>