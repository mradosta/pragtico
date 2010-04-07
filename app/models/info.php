<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a informaciones varias.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 761 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-07-27 16:28:23 -0300 (Mon, 27 Jul 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a informaciones varias.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class Info extends AppModel {


    var $useTable = false;

	function findRelationErrors() {
		$sql = "
			SELECT 		`Trabajador`.`id`,
						`Trabajador`.`cuil`,
						`Trabajador`.`nombre`,
						`Trabajador`.`apellido`,
						`Trabajador`.`nombre`,
						`Trabajador`.`obra_social_id`,
						`Trabajador`.`localidad_id`,
						`Empleador`.`cuit`,
						`Empleador`.`nombre`
			FROM		`relaciones` AS Relacion
			INNER JOIN	`trabajadores` AS Trabajador
				ON		(`Relacion`.`trabajador_id` = `Trabajador`.`id`)
			INNER JOIN	`empleadores` AS Empleador
				ON		(`Relacion`.`empleador_id` = `Empleador`.`id`)
			WHERE		`Relacion`.`estado` = 'Activa'
			AND
				(`Trabajador`.`obra_social_id` IS NULL OR `Trabajador`.`localidad_id` IS NULL)
			ORDER BY	`Trabajador`.`apellido`, `Trabajador`.`nombre`
			";

		$Relacion = ClassRegistry::init('Relacion');
		return $Relacion->query($sql);
	}


	function findInvoiceErrors() {

		$Liquidacion = ClassRegistry::init('Liquidacion');
		return $Liquidacion->find('all', array(
				'checkSecurity'	=> false,
				'contain'		=> 'Factura',
				'order'			=> array('Liquidacion.empleador_nombre'),
				'fields'		=> array(
					'Liquidacion.empleador_cuit',
					'Liquidacion.empleador_nombre',
					'SUM(Liquidacion.total) AS total'),
				'group'			=> array('Liquidacion.empleador_cuit', 'Liquidacion.empleador_nombre'),
				'conditions' 	=> array(
					array('OR' => array(
						'Liquidacion.factura_id' 	=> null,
						array('AND' => array(
							'Liquidacion.factura_id !=' => null,
							'Factura.estado !=' 		=> 'Confirmada')))),
					'Liquidacion.estado' 		=> 'Confirmada')));
	}

}	
?>