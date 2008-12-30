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
$condiciones['Condicion.Suss-fecha'] = array("type"=>"date", "label"=>"Fecha de Pago");
$condiciones['Condicion.Suss-periodo'] = array("aclaracion"=>"De la forma AAAAMM");
$condiciones['Condicion.Suss-banco_id'] = array("options"=>$bancos);
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Realiza la confirmacion del pago de SUSS", 'imagen' => '../legends/bancos.gif')));

$accionesExtra['opciones'] = array("acciones"=>array());
$botonesExtra[] = $formulario->button("Cancelar", array("title"=>"Cancelar", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$botonesExtra[] = $formulario->submit("Generar", array("title"=>"Importar la PLanilla", "onclick"=>"document.getElementById('accion').value='asignar'"));

echo $this->element('index/index', array("opcionesTabla"=>array("tabla"=>array("omitirMensajeVacio"=>true)), "botonesExtra"=>array("opciones"=>array("botones"=>$botonesExtra)), "accionesExtra"=>$accionesExtra, "opcionesForm"=>array("action"=>"asignar_suss"), "condiciones"=>$fieldset, "cuerpo"=>null));
?>