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
$condiciones['Condicion.Siap-version'] = array("options"=>"listable", "model"=>"Siap", "displayField"=>array("Siap.version"));
$condiciones['Condicion.Siap-empleador_id'] = array("options"=>$empleadores);

$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Generar archivo para SIAP","imagen"=>"archivo.gif")));

$accionesExtra['opciones'] = array("acciones"=>array());
$botonesExtra[] = $formulario->button("Cancelar", array("title"=>"Cancelar", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$botonesExtra[] = $formulario->submit("Generar", array("title"=>"Importar la PLanilla", "onclick"=>"document.getElementById('accion').value='generar'"));

echo $this->renderElement("index/index", array("opcionesTabla"=>array("tabla"=>array("omitirMensajeVacio"=>true)), "botonesExtra"=>array("opciones"=>array("botones"=>$botonesExtra)), "accionesExtra"=>$accionesExtra, "opcionesForm"=>array("action"=>"generar_archivo_siap"), "condiciones"=>$fieldset, "cuerpo"=>null));
?>