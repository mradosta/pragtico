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
 * @version         $Revision: 201 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2008-12-30 16:36:44 -0200 (mar, 30 dic 2008) $
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