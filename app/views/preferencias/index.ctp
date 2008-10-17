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
$condiciones['Condicion.Preferencia-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"preferencias.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	foreach($v['PreferenciasValor'] as $v1) {
		if($v1['predeterminado'] == "Si") {
			$valorPreedterminado = $v1['valor'];
		}
	}
	$fila = null;
	$id = $v['Preferencia']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"preferencias.gif", "alt"=>"Valores"), "url"=>'valores');
	$fila[] = array("model"=>"Preferencia", "field"=>"id", "valor"=>$id, "write"=>$v['Preferencia']['write'], "delete"=>$v['Preferencia']['delete']);
	$fila[] = array("model"=>"Preferencia", "field"=>"nombre", "valor"=>$v['Preferencia']['nombre']);
	$fila[] = array("tipo"=>"celda", "valor"=>$valorPreedterminado, "nombreEncabezado"=>"Predeterminado", "orden"=>false);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));

?>