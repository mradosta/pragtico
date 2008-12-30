<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los parametros de los grupos.
 *
 * Los parametro son datos relacionados a los grupos. Se refiere a cualquier dato adicinal de un grupo
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
 * La clase encapsula la logica de negocio asociada a los parametros de los grupos.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class GruposParametrosController extends AppController {


/**
 * Realiza los seteos especificos (valores por defecto) al agregar y/o editar.
 */
	function __seteos() {
        $this->set("grupos", $this->GruposParametro->Grupo->find("list", array("recursive"=>-1, "fields"=>array("Grupo.nombre"))));
	}

}	
?>