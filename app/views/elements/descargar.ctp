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
	 * Realiza un download generico de cualquier archivo.
	 */
    //Configure::write('debug', 0);

    if(!empty($reemplazarTexto)) {
    	$archivo['data'] = $formato->reemplazarEnTexto($reemplazarTexto['patrones'], $reemplazarTexto['reemplazos'], $archivo['data']);
    	/**
    	* Si el texto esta en UTF-8, se debe usar latin1...
    	* http://ar2.php.net/mb_strlen (ver comentario de Peter Albertsson)
    	*/
    	$archivo['size'] = mb_strlen($archivo['data'], 'latin1');
    }
    
    header('Content-type: ' . $archivo['type']);
    if(!isset($mostrar)) {
    	header('Content-length: ' . $archivo['size']);
    	header('Content-Disposition: attachment; filename="' . $archivo['name'] . '"');
    }
    echo $archivo['data'];
    exit();
?>