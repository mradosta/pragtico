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
 

	if(empty($this->viewVars['archivo']['nombre'])) {
		$this->viewVars['archivo']['nombre'] = "archivo.txt";
	}
	//d($_SERVER['HTTP_USER_AGENT']);
	define('PMA_USR_BROWSER_AGENT', 'Gecko');
	$mime_type = (PMA_USR_BROWSER_AGENT == 'IE' || PMA_USR_BROWSER_AGENT == 'OPERA')
	? 'application/octetstream'
	: 'application/octet-stream';
	header('Content-Type: ' . $mime_type);
	header('Content-Disposition: inline; filename="' . $this->viewVars['archivo']['nombre'] . '"');
	//header("Content-Transfer-Encoding: binary");
	header('Expires: 0');
	if (PMA_USR_BROWSER_AGENT == 'IE')
	{
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
	}
	else {
		header('Pragma: no-cache');
	}
	//echo $this->viewVars[archivo['contenido'];
	echo $content_for_layout;	
?>