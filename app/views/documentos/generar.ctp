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
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Documento.id'] = array("label"=>"Documento", "options"=>$documentos, "verificarRequerido"=>"forzado");
if(!empty($model) && !empty($id)) {
	$campos['Extra.model'] = array("type"=>"hidden", "value"=>$model);
	$campos['Extra.id'] = array("type"=>"hidden", "value"=>$id);
}
if(!empty($contain)) {
	$campos['Extra.contain'] = array("type"=>"hidden", "value"=>$contain);
}
$fieldsets[] = array('campos' => $campos);

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('legend' => "Generar Documento", 'imagen' => 'documentos.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/

$bloqueAdicional = $appForm->image('detalles.gif', array("id"=>"mostrar_data", "style"=>"cursor:pointer;", 'alt' => "Mostrar los posibles campos que se pueden utilizar"));
$bloqueAdicional .= $appForm->tag("span", " Mostrar los posibles campos que se pueden utilizar");
$bloqueAdicional .= $appForm->tag("div", $data, array('class' => 'unica', "id"=>"data", "style"=>"display:none;"));

$accionesExtra['opciones'] = array("acciones"=>array("cancelar", $appForm->button("Generar", array("class"=>"boton", "onclick"=>"form.submit();"))));
echo $this->element('add/add', array("accionesExtra"=>$accionesExtra, "bloqueAdicional"=>$bloqueAdicional, "fieldset"=>$fieldset, "opcionesForm"=>array("action"=>"generar")));

/**
* Agrego el evento click asociado al boton confirmar.
*/
$js = '
	jQuery("#mostrar_data").click(
		function() {
			jQuery("#data").toggle();
		}
	);';
$appForm->addScript($js);
?>