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
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($this->data['ConveniosCategoriasHistorico'] as $k=>$v) {
	$fila = null;
	$fila[] = array('model' => 'ConveniosCategoriasHistorico', 'field' => 'id', 'valor' => $v['id'], 'write' => $v['write'], 'delete' => $v['delete']);
	$fila[] = array('model' => 'Informacion', 'field' => 'nombre', 'valor' => $v['Informacion']['nombre']);
	$fila[] = array('model' => 'ConveniosInformacion', 'field' => 'valor', 'valor' => $v['valor']);
	$cuerpo[] = $fila;
}

$url = array('controller' => "convenios_categorias_historicos", 'action' => 'add', "ConveniosCategoriasHistorico.categoria_id"=>$this->data['ConveniosCategoria']['id']);
echo $this->element('desgloses/agregar', array('url' => $url, 'titulo' => "Informacion Adicional", 'cuerpo' => $cuerpo));

?>