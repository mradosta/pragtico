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
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Ausencia.id'] = array();
$campos['Ausencia.relacion_id'] = array(	"lov"=>array("controller"	=>	"relaciones",
													"seleccionMultiple"	=> 	0,
													"camposRetorno"		=> 	array(	"Empleador.nombre",
																					"Trabajador.apellido")));
																					
$campos['Ausencia.ausencia_motivo_id'] = array(	"empty"			=> true,
												"options"		=> "listable",
												"order"			=> "AusenciasMotivo.motivo",
												"displayField"	=> "AusenciasMotivo.motivo",
												"groupField"	=> "AusenciasMotivo.tipo",
												"model"			=> "AusenciasMotivo",
												"label"			=> "Motivo");
$campos['Ausencia.desde'] = array();
$fieldsets[] = 	array('campos' => $campos);


$campos = null;
$campos['AusenciasSeguimiento.id'] = array();
$campos['AusenciasSeguimiento.dias'] = array();
$campos['AusenciasSeguimiento.comprobante'] = array("label"=>"Presento Comprobante");
$campos['AusenciasSeguimiento.archivo'] = array("label"=>"Comprobante", "type"=>"file", "descargar"=>true, "mostrar"=>true);
$campos['AusenciasSeguimiento.estado'] = array('type' => 'radio');
$campos['AusenciasSeguimiento.observacion'] = array();
$fieldsets[] = 	array('campos' => $campos, 'opciones' => array('fieldset' => array("class"=>"detail", 'legend' => "Seguimientos", 'imagen' => 'seguimientos.gif')));

$fieldset = $appForm->pintarFieldsets($fieldsets, array('div' => array('class' => 'unica'), 'fieldset' => array('legend' => "Ausencias", 'imagen' => 'ausencias.gif')));

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$miga = array('format' 	=> '%s %s (%s)', 
			  'content' => array('Relacion.Trabajador.apellido', 'Relacion.Trabajador.nombre', 'Relacion.Empleador.nombre'));
echo $this->element("add/add", array('fieldset' => $fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data"), 'miga' => $miga));

$extraJs = array();
if (!empty($this->data)) {
    foreach ($this->data as $ausencia) {
        foreach ($ausencia['AusenciasSeguimiento'] as $k => $seguimiento) {
            $extraJs[] = 'jQuery.detailAfterAdd(' . $k . ');';
        }
    }
}
$appForm->addScript('
        
    detalle();
    jQuery("a.link_boton").bind("click", agregar);
   
        
    jQuery.detailAfterAdd = function(id) {
        if (id == undefined) {
            id = "0";
        }
        jQuery("#AusenciasSeguimientoEstado" + id + "Liquidado").attr("disabled", true);
    }
    ' . implode('', $extraJs));
?>