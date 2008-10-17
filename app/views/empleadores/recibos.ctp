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
foreach ($this->data['Recibo'] as $k=>$v) {
	$fila = null;
	$fila[] = array("tipo"=>"desglose", "id"=>$v['id'], "update"=>"desglose_1", "imagen"=>array("nombre"=>"detalles.gif", "alt"=>"Conceptos (Detalle del Recibo)"), "url"=>'../recibos/conceptos');
	$fila[] = array("tipo"=>"accion", "valor"=>$formulario->link($formulario->image("asignar.gif", array("alt"=>"Asignar este recibo a todos los Trabajadores", "title"=>"Asignar este recibo a todos los Trabajadores")), array("action"=>"asignar_recibo", "empleador_id"=>$this->data['Empleador']['id'], "recibo_id"=>$v['id']), array(), "Asignara los conceptos de este recibo a todos los trabajadores del empleador '" . $this->data['Empleador']['nombre'] . "'. Desea continuar?"));
	$fila[] = array("model"=>"Recibo", "field"=>"id", "valor"=>$v['id'], "write"=>$v['write'], "delete"=>$v['delete']);
	$fila[] = array("model"=>"Recibo", "field"=>"nombre", "valor"=>$v['nombre']);
	$fila[] = array("model"=>"Recibo", "field"=>"descripcion", "valor"=>$v['descripcion']);
	$cuerpo[] = $fila;
}

$url = array("controller"=>"recibos", "action"=>"add", "Recibo.empleador_id"=>$this->data['Empleador']['id']);
echo $this->renderElement("desgloses/agregar", array("url"=>$url, "titulo"=>"Recibos", "cuerpo"=>$cuerpo));

?>