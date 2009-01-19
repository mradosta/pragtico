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
ob_start();
pr($this->data['Auditoria']['data']);
$out = ob_get_clean();

$codigoHtml = $appForm->tag("span", "Detalles (Datos del registro)", array("class"=>"titulo"));
$codigoHtml .= $appForm->tag("div", $out, array("class"=>"tabla")) ;
echo $appForm->tag("div", $codigoHtml, array("class"=>"unica"));

?>