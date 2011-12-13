<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las horas de una relacion laboral.
 * Las horas puedenser horas extras, horas de enfermedad, etc.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a las horas de una relacion laboral.
 * Las horas puedenser horas extras, horas de enfermedad, etc.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class HorasController extends AppController {

    var $paginate = array(
        'order' => array(
            'Hora.periodo' => 'desc'
        )
    );


	function save_reloj() {
		if (!empty($this->data['Form']['accion'])) {
			if ($this->data['Form']['accion'] === 'grabar') {


				foreach ($this->data['Hora'] as $k => $v) {

					$relationId = array_pop(explode('_', $k));

					if (!empty($this->data['Control']['relation_selected_' . $relationId])) {
						$saveAll[] = array(
							'relacion_id' 		=> $relationId,
							'tipo' 				=> $this->data['Form']['tipo'],
							'liquidacion_tipo' 	=> $this->data['Form']['liquidacion_tipo'],
							'periodo' 			=> $this->data['Form']['periodo'],
							'estado'			=> 'Confirmada',
							'cantidad' 			=> $v,
							'observacion' 		=> 'Ingresado desde archivo de reloj. Confirmado el ' . date('Y-m-d'),
						);
					}

				}


				if (!empty($saveAll)) {

					if ($this->Hora->appSave(array('Hora' => $saveAll))) {
						$this->Session->setFlash('Se ingresaron correctamente las horas desde el archivo del reloj', 'ok');
					} else {
						$this->Session->setFlash('No fue posible confirmar las horas desde archivo del reloj', 'error');
					}
				}
			}
		}
		$this->redirect('index');
	}


/**
 * Import data from access control watches
 *
 */
	function importar_reloj() {

		if (!empty($this->data['Formulario']['accion'])) {
			if ($this->data['Formulario']['accion'] === 'importar') {


				$errors = array();
				if (empty($this->data['Hora']['empleador_id'])) {
					$errors[] = 'Debe seleccionar el empleador.';
				}
				if (empty($this->data['Hora']['periodo'])) {
					$errors[] = 'Debe seleccionar el periodo.';
				}
				if (empty($this->data['Hora']['archivo']['tmp_name'])) {
					$errors[] = 'Debe seleccionar el archivo proveniente desde el reloj.';
				}

				if (!empty($errors)) {

					$this->Session->setFlash(implode('<br/>', $errors), 'error');

				} else {

					$tmpColumns = explode("\n", $this->data['Hora']['columnas']);
					foreach ($tmpColumns as $v) {
						$tmp = explode('=>', $v);
						$columns[trim($tmp[0])] = trim($tmp[1]) - 1;
					}



					$rows = file($this->data['Hora']['archivo']['tmp_name']);

					foreach ($rows as $k => $row) {

						$row = trim($row);

						if (!empty($row)) {

							$d = explode("\t", $row);

							$data[$d[$columns['legajo']]][] = $d[$columns['fecha/hora']];

							$legajos[] = $d[$columns['legajo']];
						}
					}


					$relations = $this->Hora->Relacion->find('all',
						array(
							'contain' => array('Trabajador'),
							'conditions' => array(
								'Relacion.legajo' => $legajos,
								'Relacion.empleador_id' => $this->data['Hora']['empleador_id'],
								'Relacion.estado' => 'Activa'
							)
						)
					);
					$relations = Set::combine($relations, '{n}.Relacion.legajo', '{n}');


					App::import('Vendor', 'dates', 'pragmatia');

					foreach($data as $k => $v) {

						$error = '';
						if ((count($v) & 1) == 1) {

							$error = 'Error: No existe un egreso para cada ingreso.';

						} else if (empty($relations[$k])) {

							$error = 'Error: El legajo no corresponde a una relación activa existente.';
						}
						
						if (empty($error)) {

							$computedData[$k] = $relations[$k];

							foreach ($v as $kk => $vv) {

								if (($kk & 1) == 1) {
									$diff = Dates::dateDiff($v[$kk-1], $vv, array('toInclusive' => false));
									$computedData[$k]['records'][] = array(
										'from' 			=> $v[$kk-1],
										'to'			=> $vv,
										'diff'			=> $diff
									);
								}
							}

						} else {
							$computedData[$k]['error'] = $error;
						}
					}
					$this->set('data', $computedData);
					$this->set('liquidacion_tipo', $this->data['Hora']['liquidacion_tipo']);
					$this->set('periodo', $this->data['Hora']['periodo']);
					$this->set('tipo', $this->data['Hora']['tipo']);
				}
			}
		}

	}


    function afterPaginate($results) {
        if (!empty($results)) {
            $this->set('cantidad', $this->Hora->getTotal($this->Paginador->getCondition()));
        } else {
            $this->set('cantidad', 0);
        }
    }

}

?>