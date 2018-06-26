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
foreach ($this->data['Concepto'] as $k=>$v) {
	$fila = null;
	$fila[] = array(
		'tipo' => 'accion',
		'valor' => $appForm->link(
			$appForm->image('asignar.gif', array(
				'alt' 	=> 'Asignar este concepto a las relaciones que pertenezcan a este Convenio Colectivo',
				'title' => 'Asignar este concepto a las relaciones que pertenezcan a este Convenio Colectivo')
			), array(
				'action' 		=> 'manipular_concepto/agregar',
				'convenio_id' 	=> $this->data['Convenio']['id'],
				'concepto_id' 	=> $v['id']
			), array(),
			'Asignara este concepto a las relaciones que pertenezcan a este Convenio Colectivo' . $this->data['Convenio']['nombre'] . '. Desea continuar?'
		)
	);
	$fila[] = array(
		'tipo' 	=> 'accion',
		'valor' => $appForm->link(
			$appForm->image('quitar.gif',
			array(
				'alt' 	=> 'Quitara este concepto de todas las relaciones que pertenezcan a este Convenio Colectivo', 'title' => 'Quitara este concepto de todas las relaciones que pertenezcan a este Convenio Colectivo')
			),
			array(
				'action' 		=> 'manipular_concepto/quitar',
				'convenio_id' 	=> $this->data['Convenio']['id'],
				'concepto_id' 	=> $v['id']
			), array(),
			'Quitara este concepto de todos los trabajadores de todos los empleadores que tengan el convenio colectivo ' . $this->data['Convenio']['nombre'] . '. Desea continuar?'
		)
	);
	$fila[] = array(
		'model' 	=> 'ConveniosConcepto',
		'field' 	=> 'id',
		'valor' 	=> $v['ConveniosConcepto']['id'],
		'write' 	=> $v['ConveniosConcepto']['write'],
		'delete' 	=> $v['ConveniosConcepto']['delete']
	);
	$fila[] = array(
		'model' => 'ConveniosConcepto',
		'field' => 'codigo',
		'valor' => $v['codigo']
	);
	$fila[] = array(
		'model' => 'ConveniosConcepto',
		'field' => 'nombre',
		'valor' => $v['nombre']
	);

	if (!empty($v['ConveniosConcepto']['formula'])) {
		$fila[] = array(
			'model' 			=> 'Bar',
			'field' 			=> 'foo',
			'valor' 			=> 'Convenio',
			'nombreEncabezado' 	=> 'Jerarquia',
			'ordenEncabezado' 	=> false
		);
		$fila[] = array(
			'model' => 'ConveniosConcepto',
			'field' => 'formula',
			'valor' => $v['ConveniosConcepto']['formula']
		);
	} else {
		$fila[] = array(
			'model' 			=> 'Bar',
			'field' 			=> 'foo',
			'valor' 			=> 'Concepto',
			'nombreEncabezado' 	=> 'Jerarquia',
			'ordenEncabezado' 	=> false);
		$fila[] = array(
			'model' => 'Concepto',
			'field' => 'formula',
			'valor' => $v['formula']
		);
	}

	if (!empty($v['ConveniosConcepto']['nombre_formula'])) {
		$fila[] = array(
			'model' 			=> 'Bar',
			'field' 			=> 'foo',
			'valor' 			=> 'Convenio',
			'nombreEncabezado' 	=> 'Jerarquia',
			'ordenEncabezado' 	=> false
		);
		$fila[] = array(
			'model' => 'ConveniosConcepto',
			'field' => 'nombre_formula',
			'valor' => $v['ConveniosConcepto']['nombre_formula']
		);
	} else {
		$fila[] = array(
			'model' 			=> 'Bar',
			'field' 			=> 'foo',
			'valor' 			=> 'Concepto',
			'nombreEncabezado' 	=> 'Jerarquia',
			'ordenEncabezado' 	=> false);
		$fila[] = array(
			'model' => 'Concepto',
			'field' => 'nombre_formula',
			'valor' => $v['nombre_formula']
		);
	}

	if (!empty($v['ConveniosConcepto']['cantidad'])) {
		$fila[] = array(
			'model' 			=> 'Bar',
			'field' 			=> 'foo',
			'valor' 			=> 'Convenio',
			'nombreEncabezado' 	=> 'Jerarquia',
			'ordenEncabezado' 	=> false
		);
		$fila[] = array(
			'model' => 'ConveniosConcepto',
			'field' => 'cantidad',
			'valor' => $v['ConveniosConcepto']['cantidad']
		);
	} else {
		$fila[] = array(
			'model' 			=> 'Bar',
			'field' 			=> 'foo',
			'valor' 			=> 'Concepto',
			'nombreEncabezado' 	=> 'Jerarquia',
			'ordenEncabezado' 	=> false);
		$fila[] = array(
			'model' => 'Concepto',
			'field' => 'cantidad',
			'valor' => $v['cantidad']
		);
	}	
	$cuerpo[] = $fila;
}

$url[] = array(
	'controller' 					=> 'convenios_conceptos',
	'action' 						=> 'add',
	'ConveniosConcepto.convenio_id' => $this->data['Convenio']['id']
);
$url[] = array(
	'controller' 					=> 'convenios_conceptos',
	'action' 						=> 'add_rapido',
	'ConveniosConcepto.convenio_id' => $this->data['Convenio']['id'],
	'texto' 						=> 'Carga Rapida'
);
echo $this->element('desgloses/agregar',
	array(
		'url' 		=> $url,
		'titulo' 	=> 'Conceptos',
		'cuerpo' 	=> $cuerpo
	)
);

?>