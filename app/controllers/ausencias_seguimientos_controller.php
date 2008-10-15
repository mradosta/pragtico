<?php
/**
 * Este archivo contiene toda la logica de negocio asociada al seguimiento de una ausencia.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers
 * @since			Pragtico v 1.0.0
 * @version			1.0.0
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada al seguimiento de una ausencia.
 *
 *
 * @package		pragtico
 * @subpackage	app.controllers
 */
class AusenciasSeguimientosController extends AppController {


/**
 * Permite descargar y/o mostrar la foto del trabajador.
 */
	function descargar($id) {
		$ausenciasSeguimiento = $this->AusenciasSeguimiento->findById($id);
		$archivo['data'] = $ausenciasSeguimiento['AusenciasSeguimiento']['file_data'];
		$archivo['size'] = $ausenciasSeguimiento['AusenciasSeguimiento']['file_size'];
		$archivo['type'] = $ausenciasSeguimiento['AusenciasSeguimiento']['file_type'];
		$archivo['name'] = $this->Util->getFileName("conprobante_" . $ausenciasSeguimiento['AusenciasSeguimiento']['id'], $ausenciasSeguimiento['AusenciasSeguimiento']['file_type']);
		$this->set("archivo", $archivo);
		if(!empty($this->params['named']['mostrar']) && $this->params['named']['mostrar'] == true) {
			$this->set("mostrar", true);
		}
		$this->render("../elements/descargar", "descargar");
	}

}
?>