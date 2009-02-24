<?php
/**
 * Este archivo contiene toda la logica de acceso a datos asociada a los patrones de los documentos modelo del sistema.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de acceso a datos asociada a los patrones de los documentos modelo del sistema.
 *
 * @package     pragtico
 * @subpackage  app.models
 */
class DocumentosPatron extends AppModel {

    
    var $belongsTo = array('Documento');
    
}
?>