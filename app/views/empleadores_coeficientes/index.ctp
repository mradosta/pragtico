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
$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.cuit",
																								"Empleador.nombre")));
$condiciones['Condicion.Coeficiente-nombre'] = array();
$condiciones['Condicion.Coeficiente-tipo'] = array();
$fieldsets[] = array('campos' => $condiciones);
$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array("legend"=>"Coeficientes de los Empleadores", 'imagen' => 'coeficientes.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => "EmpleadoresCoeficiente", 'field' => "id", 'valor' => $v['EmpleadoresCoeficiente']['id'], "write"=>$v['EmpleadoresCoeficiente']['write'], "delete"=>$v['EmpleadoresCoeficiente']['delete']);
	$fila[] = array('model' => "Empleador", 'field' => "nombre", "nombreEncabezado"=>"Empleador", 'valor' => $v['Empleador']['cuit'] . " - " . $v['Empleador']['nombre']);
	$fila[] = array('model' => "Coeficiente", 'field' => "nombre", "nombreEncabezado"=>"Coeficiente", 'valor' => $v['Coeficiente']['nombre']);
	$fila[] = array('model' => "Coeficiente", 'field' => "tipo", 'valor' => $v['Coeficiente']['tipo']);
	$fila[] = array('model' => "EmpleadoresCoeficiente", 'field' => "valor", 'valor' => $v['EmpleadoresCoeficiente']['valor']);
	$fila[] = array('model' => "EmpleadoresCoeficiente", 'field' => "observacion", 'valor' => $v['EmpleadoresCoeficiente']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->element('index/index', array('condiciones' => $fieldset, 'cuerpo' => $cuerpo));

?>