<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a las informacion digitalizada de los trabajadores.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 1345 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2010-06-04 16:17:50 -0300 (Fri, 04 Jun 2010) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a la informacion digitalizada de los trabajadores.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class TrabajadoresDocumentosController extends AppController {

/**
 * Permite descargar y/o mostrar la foto del trabajador.
 */
	function descargar($id) {
		$documento = $this->TrabajadoresDocumento->findById($id);
		$archivo['data'] = $documento['TrabajadoresDocumento']['file_data'];
		$archivo['size'] = $documento['TrabajadoresDocumento']['file_size'];
		$archivo['type'] = $documento['TrabajadoresDocumento']['file_type'];
		$archivo['name'] = $this->Util->getFileName("conprobante_" . $documento['TrabajadoresDocumento']['id'], $documento['TrabajadoresDocumento']['file_type']);
		$this->set("archivo", $archivo);
		if (!empty($this->params['named']['mostrar']) && $this->params['named']['mostrar'] == true) {
			$this->set("mostrar", true);
		}
		$this->render("../elements/descargar", "descargar");
	}

 
}
?>