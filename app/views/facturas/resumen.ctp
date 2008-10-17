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
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Liquidacion-empleador_id'] = array(	"lov"=>array("controller"	=>	"empleadores",
																		"camposRetorno"	=>array("Empleador.nombre")));
$condiciones['Condicion.Liquidacion-periodo'] = array("type"=>"periodo");
$condiciones['Resumen.tipo'] = array("type"=>"radio", "options"=>$tipos);

$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"buscar.gif", "legend"=>"Resumen")));


$botonesExtra = $formulario->submit("Generar", array("title"=>"Imprime el Resumen de Facturacion"));
$accionesExtra['opciones'] = array("acciones"=>array());
$opcionesTabla =  array("tabla"=>array(	"omitirMensajeVacio"=>true));

echo $this->renderElement("index/index", array("opcionesForm"=>array("action"=>"resumen"), "opcionesTabla"=>$opcionesTabla, "accionesExtra"=>$accionesExtra, "botonesExtra"=>array("opciones"=>array("botones"=>array("limpiar", $botonesExtra))), "condiciones"=>$fieldset));

?>