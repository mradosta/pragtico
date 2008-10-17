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
  
//header("Content-type: application/vnd.ms-excel"); 
//header("Content-Disposition: attachment; filename=" . $session->read('nombreArchivo')); 
//header("Pragma: no-cache"); 
//header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
//header("Expires: 0"); 

echo $content_for_layout;
?>