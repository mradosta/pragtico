<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 332 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-02-25 16:33:58 -0200 (Wed, 25 Feb 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
 

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['RelacionesHistorica'] as $v) {
    $fila = null;
    $fila[] = array('model' => 'RelacionesHistorica', 'field' => 'id', 'valor' => $v['id'], 'write' => $v['write'], 'delete' => $v['delete']);
    $fila[] = array('model' => 'RelacionesHistorica', 'field' => 'ingreso', 'valor' => $v['ingreso']);
    $fila[] = array('model' => 'RelacionesHistorica', 'field' => 'egreso', 'valor' => $v['egreso']);
    $fila[] = array('model' => 'EgresosMotivo', 'field' => 'motivo', 'valor' => $v['EgresosMotivo']['motivo']);
    $cuerpo[] = $fila;
}

echo $this->element('desgloses/agregar', array('titulo' => 'Historicas', 'cuerpo' => $cuerpo));

?>