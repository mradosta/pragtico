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
$condiciones['Condicion.Relacion-trabajador_id'] = array(	"lov"=>array("controller"		=>	"trabajadores",
																		"separadorRetorno"	=>	" ",
																		"camposRetorno"		=>array("Trabajador.apellido",
																									"Trabajador.nombre")));

$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=> "empleadores",
																		"camposRetorno"	=> array("Empleador.nombre")));

$condiciones['Condicion.Vacacion-relacion_id'] = array(	"lov"=>array("controller"	=>	"relaciones",
																		"camposRetorno"	=>array("Empleador.nombre",
																								"Trabajador.apellido")));

$condiciones['Condicion.Vacacion-desde'] = array();
$condiciones['Condicion.Vacacion-periodo'] = array();
$condiciones['Condicion.Vacacion-estado'] = array('type' => 'select', 'multiple' => 'checkbox');
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => "Vacaciones", 'imagen' => 'vacaciones.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k => $v) {
	$fila = null;
	$fila[] = array('model' => 'Vacacion', 'field' => 'id', 'valor' => $v['Vacacion']['id'], 'write' => $v['Vacacion']['write'], 'delete' => $v['Vacacion']['delete']);
	$fila[] = array('model' => 'Empleador', 'field' => 'nombre', 'valor' => $v['Relacion']['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array('model' => 'Trabajador', 'field' => 'numero_documento', 'valor' => $v['Relacion']['Trabajador']['numero_documento'], "class"=>"derecha", "nombreEncabezado"=>"Documento");
	$fila[] = array('model' => 'Trabajador', 'field' => 'apellido', 'valor' => $v['Relacion']['Trabajador']['apellido'] . " " . $v['Relacion']['Trabajador']['nombre'], "nombreEncabezado"=>"Trabajador");
	$fila[] = array('model' => 'Vacacion', 'field' => 'desde', 'valor' => $v['Vacacion']['desde']);
	$fila[] = array('model' => 'Vacacion', 'field' => 'dias', 'valor' => $v['Vacacion']['dias']);
    $fila[] = array('model' => 'Vacacion', 'field' => 'periodo', 'valor' => $v['Vacacion']['periodo']);
	$fila[] = array('model' => 'Vacacion', 'field' => 'estado', 'valor' => $v['Vacacion']['estado']);
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo));

?>