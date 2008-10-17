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
 
$campos['Asignar.accion'] = array("value"=>$accion, "type"=>"hidden");
$campos['Asignar.concepto_id'] = array("value"=>$concepto['Concepto']['id'], "type"=>"hidden");
$campos['Asignar.convenio_id'] = array("options"=>$convenios, "type"=>"checkboxMultiple", "contenedorHtmlAttributes" => array("class" => "checkboxMultipleCeldaUnica checkboxMultiple"));
$campos['Asignar.empleador_comportamiento'] = array("value"=>"incluir", "after"=>"<br />Indica la accion que se tomara con los empleadores seleccionados. Si no seleeciona ningun Empleador, se aplicara a todos.", "label"=>"Comportamiento", "options"=>$comportamientos, "type"=>"radio");
$campos['Asignar.empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre")));


$fieldsets[] = array("campos"=>$campos);

$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>ucfirst($accion) . " el Concepto " . $concepto['Concepto']['nombre'] . " a todos los Trabajadores de:", "imagen"=>$accion . ".gif")));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->renderElement("add/add", array("opcionesForm"=>array("action"=>"manipular_concepto"), "fieldset"=>$fieldset));

if($accion == "agregar") {
	$msg = "Esta seguro que desea asigar el concepto " . $concepto['Concepto']['nombre'] . " a todos los Trabajadores que pertenezcan a los Convenios Colectivos seleccionados?	";
}
else {
	$msg = "Esta seguro que desea quitar el concepto " . $concepto['Concepto']['nombre'] . " de todos los Trabajadores que pertenezcan a los Convenios Colectivos seleccionados?	";
}

echo $formulario->codeBlock("
	/**
	* Quito el evento por defecto del onclick del submit grabar y lo manejo en esta funcion.
	*/
	jQuery('#boton_grabar').attr('onclick', 'false');
	
	jQuery('#form').submit(
		function () {
			var seleccionado = false;
			jQuery('.checkboxMultiple ul li input').each(
				function() {
					if(jQuery(this).attr('checked') == true) {
						seleccionado = true;
					}
				}
			);
			if(!seleccionado) {
				alert('Debe seleccionar al menos un Convenio Colectivo');
				return false;
			}
			if(confirm('" . $msg . "')) {
				jQuery('#accion').attr('value', 'grabar');
				return true;
			}
			else {
				return false;
			}
		}
	);
");
?>