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
foreach ($this->data['LiquidacionesError'] as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"tipo", "valor"=>$v['tipo']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"gravedad", "valor"=>$v['gravedad']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"concepto", "valor"=>$v['concepto']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"formula", "valor"=>$v['formula']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"descripcion", "valor"=>$v['descripcion']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"descripcion_adicional", "valor"=>$v['descripcion_adicional']);
	$fila[] = array("model"=>"LiquidacionesError", "field"=>"recomendacion", "valor"=>$v['recomendacion']);
	$cuerpo[] = $fila;
}


/**
* Creo la tabla.
*/
$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>false,
									"ordenEnEncabezados"=>false,
									"modificar"			=>false,
									"seleccionMultiple"	=>false,
									"permisos"			=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false));

echo $this->renderElement("desgloses/agregar", array("opcionesTabla"=>$opcionesTabla, "titulo"=>"Errores de la Liquidacion", "cuerpo"=>$cuerpo));

?>