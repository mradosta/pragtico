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
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Novedad.periodo'] = array("type"=>"periodo", "aclaracion"=>"Tomara este periodo para los datos ingresados desde la planilla.", "verificarRequerido"=>false);
$campos['Novedad.planilla'] = array("type"=>"file");

$fieldset = $appForm->pintarFieldsets(array(array('campos' => $campos)), array('fieldset' => array("legend"=>"Importar novedades desde planilla", 'imagen' => 'excel.gif')));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = $pie = null;
if(!empty($registros)) {
	foreach ($registros as $k=>$v) {
		$fila = null;
		$id = $v['Hora']['id'];
		$fila[] = array('model' => "Hora", 'field' => "id", 'valor' => $v['Hora']['id'], "write"=>$v['Hora']['write'], "delete"=>$v['Hora']['delete']);
		$fila[] = array('model' => "Empleador", 'field' => "cuit", 'valor' => $v['Relacion']['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
		$fila[] = array('model' => "Trabajador", 'field' => "cuil", 'valor' => $v['Relacion']['Trabajador']['nombre'] . " " . $v['Relacion']['Trabajador']['apellido'], "nombreEncabezado"=>"Trabajador");
		$fila[] = array('model' => "Hora", 'field' => "periodo", 'valor' => $v['Hora']['periodo']);
		$fila[] = array('model' => "Hora", 'field' => "cantidad", 'valor' => $v['Hora']['cantidad']);
		$fila[] = array('model' => "Hora", 'field' => "tipo", 'valor' => $v['Hora']['tipo']);
		$fila[] = array('model' => "Hora", 'field' => "estado", 'valor' => $v['Hora']['estado']);
		if($v['Hora']['confirmadas'] > 0) {
			$cuerpo[] = array("contenido"=>$fila, "opciones"=>array("title"=>"Existen " . $v['Hora']['confirmadas'] . " horas confirmadas para el mismo periodo", "class"=>"fila_resaltada"));
		}
		else {
			$cuerpo[] = $fila;
		}
	}

	$fila = null;
	$fila[] = array('model' => "Hora", 'field' => "id", "valor"=>"");
	$fila[] = array('model' => "Empleador", 'field' => "cuit", "valor"=>"");
	$fila[] = array('model' => "Trabajador", 'field' => "cuil", "valor"=>"");
	$fila[] = array('model' => "Hora", 'field' => "periodo", "valor"=>"");
	$fila[] = array('model' => "Hora", 'field' => "cantidad", "valor"=>$totales['cantidad']);
	$fila[] = array('model' => "Hora", 'field' => "tipo", "valor"=>"");
	$fila[] = array('model' => "Hora", 'field' => "estado", "valor"=>"");
	$pie[] = $fila;
}

$botonesExtra[] = $appForm->button("Cancelar", array("title"=>"Cancelar", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$botonesExtra[] = $appForm->submit("Importar", array("title"=>"Importar la PLanilla", "onclick"=>"document.getElementById('accion').value='importar'"));
$opcionesTabla['tabla']['omitirMensajeVacio'] = true;
echo $this->element('index/index', array("botonesExtra"=>array("opciones"=>array("botones"=>$botonesExtra)), "condiciones"=>$fieldset, "opcionesTabla"=>$opcionesTabla, "opcionesForm"=>array("enctype"=>"multipart/form-data", 'action' => "importar_planilla")));

?>