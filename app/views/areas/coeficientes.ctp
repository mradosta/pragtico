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
 * @version			$Revision: 236 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2009-01-27 11:26:49 -0200 (mar, 27 ene 2009) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['Coeficiente'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => 'AreasCoeficiente', 'field' => 'id', 'valor' => $v['AreasCoeficiente']['id'], 'write' => $v['AreasCoeficiente']['write'], 'delete' => $v['AreasCoeficiente']['delete']);
	$fila[] = array('model' => 'AreasCoeficiente', 'field' => 'nombre', 'valor' => $v['nombre']);
	$fila[] = array('model' => 'AreasCoeficiente', 'field' => 'tipo', 'valor' => $v['tipo']);
	$fila[] = array('model' => 'AreasCoeficiente', 'field' => 'porcentaje', 'valor' => $v['AreasCoeficiente']['porcentaje'], 'tipoDato' => 'percentage');
	$cuerpo[] = $fila;
}

$url = array('controller' => 'areas_coeficientes', 'action' => 'add', 'AreasCoeficiente.area_id' => $this->data['Area']['id']);
echo $this->element('desgloses/agregar', array('url' => $url, 'titulo' => 'Coeficiente', 'cuerpo' => $cuerpo));

?>