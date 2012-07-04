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
 * @version			$Revision: 1063 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2009-10-08 13:53:17 -0300 (Thu, 08 Oct 2009) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['TrabajadoresDocumento.id'] = array();
$campos['TrabajadoresDocumento.trabajador_id'] = array(  'lov'=>array('controller'       =>  'trabajadores',
                                                        'seleccionMultiple' =>  0,
                                                            'camposRetorno' =>  array(  'Trabajador.cuil',
                                                                                        'Trabajador.nombre',
                                                                                        'Trabajador.apellido')));
$campos['TrabajadoresDocumento.nombre'] = array();
$campos['TrabajadoresDocumento.archivo'] = array('label' => 'Documento', 'type' => 'file', 'descargar' => true, 'mostrar' => true);
$campos['TrabajadoresDocumento.descripcion'] = array();
$fieldsets[] = array('campos' => $campos);

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('legend' => 'Documento', 'imagen' => 'documentos.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
echo $this->element('add/add', array('fieldset' => $fieldset, 'opcionesForm'=>array('enctype' => 'multipart/form-data')));
?>