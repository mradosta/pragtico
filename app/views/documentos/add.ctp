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
$campos['Documento.id'] = array();
$campos['Documento.nombre'] = array();
$campos['Documento.patrones'] = array('type'=>'hidden');
$campos['Documento.file_name'] = array('type'=>'hidden');
$campos['Documento.file_type'] = array('type'=>'hidden');
$campos['Documento.file_size'] = array('type'=>'hidden');
$campos['Documento.model'] = array('type'=>'select', 'options'=>$models, 'empty'=>true);
$campos['Documento.archivo'] = array('aclaracion' => 'Debe cargar un archivo RTF con los patrones de la forma #*Model.campo*#.', 'type'=>'file', 'label'=>'Archivo Origen (.rtf)');
$campos['Documento.observacion'] = array();
$fieldsets[] = array('campos' => $campos);

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$bloqueAdicional = '';
if (!empty($this->data['Documento']['patrones'])) {
	$bloque[] = 'Se identificaron los siguientes patrones dentro del Documento:';
	$lis = array();
	foreach ($this->data['Documento']['patrones'] as $v) {
		$lis[] = $appForm->tag('li',  $v);
	}
	$bloque[] = $appForm->tag('ul',  $lis);
	$bloque[] = $appForm->tag('span',  'Presione sobre el boton grabar para confirmar si los patrones encontrados son correctos.<br />En caso de que no lo sean correctos, presione el boton cancelar, modifique el archivo de origen y reintentelo.<br /><br /><br /><br />');
	$bloque[] = $appForm->input('Form.confirmar', array('type'=>'hidden', 'value'=>'confirmado'));
	$bloqueAdicional = $appForm->tag('div', $bloque, array('class' => 'unica'));
}

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('imagen' => 'documentos.gif')));
echo $this->element('add/add', array('bloqueAdicional'=>$bloqueAdicional, 'opcionesForm'=>array('enctype'=>'multipart/form-data'), 'fieldset'=>$fieldset, 'miga' => 'Documento.nombre'));
?>