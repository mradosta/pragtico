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
 * @version			$Revision: 24 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-17 15:49:35 -0300 (vie, 17 oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['AusenciasMotivo'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => "AusenciasMotivo", 'field' => "id", 'valor' => $v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array('model' => "AusenciasMotivo", 'field' => "motivo", 'valor' => $v['motivo']);
	$fila[] = array('model' => "AusenciasMotivo", 'field' => "tipo", 'valor' => $v['tipo']);
	$cuerpo[] = $fila;
}

$url = array('controller' => "ausencias_motivos", 'action' => 'add', "AusenciasMotivo.situacion_id"=>$this->data['Situacion']['id']);
echo $this->element('desgloses/agregar', array('url' => $url, 'titulo' => "Motivos de Ausencia", 'cuerpo' => $cuerpo));

?>