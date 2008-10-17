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
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Siap-periodo'] = array("type"=>"periodo", "periodo"=>array("soloAAAAMM"), "aclaracion"=>"De la forma AAAAMM");
$condiciones['Condicion.Siap-version'] = array("options"=>$siaps);
$condiciones['Condicion.Siap-modo'] = array("options"=>$modos, "type"=>"radio");
$condiciones['Condicion.Siap-grupo_id'] = array("options"=>$grupos, "empty"=>true);
$condiciones['Condicion.Siap-empleador_id'] = array(	"aclaracion"=> "Solo se tendra en cuenta este campo si el tipo es 'Por Empleador'.",
														"lov"		=> array(	"controller"		=> "empleadores",
																				"seleccionMultiple"	=> 	0,
																				"camposRetorno"		=> 	array(	"Empleador.cuit",
																												"Empleador.nombre")));
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Generar archivo para SIAP","imagen"=>"archivo.gif")));

$accionesExtra['opciones'] = array("acciones"=>array());
$botonesExtra[] = $formulario->button("Cancelar", array("title"=>"Cancelar", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$botonesExtra[] = $formulario->submit("Generar", array("title"=>"Importar la PLanilla", "onclick"=>"document.getElementById('accion').value='generar'"));

echo $this->renderElement("index/index", array("opcionesTabla"=>array("tabla"=>array("omitirMensajeVacio"=>true)), "botonesExtra"=>array("opciones"=>array("botones"=>$botonesExtra)), "accionesExtra"=>$accionesExtra, "opcionesForm"=>array("action"=>"generar_archivo_siap"), "condiciones"=>$fieldset, "cuerpo"=>null));
?>