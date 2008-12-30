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
$campos['Trabajador.id'] = array();
$campos['Trabajador.cuil'] = array();
$campos['Trabajador.nombre'] = array();
$campos['Trabajador.apellido'] = array();
$campos['Trabajador.tipo_documento'] = array();
$campos['Trabajador.numero_documento'] = array("aclaracion"=>"Si lo deja en blanco, se lo extraera desde el cuil.");
$campos['Trabajador.estado_civil'] = array();
$campos['Trabajador.sexo'] = array();
$campos['Trabajador.nacimiento'] = array();
$campos['Trabajador.archivo'] = array("type"=>"file", "label"=>"Foto", "mostrar"=>true);
$campos['Trabajador.nacionalidad'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Identificacion", "imagen"=>"identificacion.gif")));

$campos = null;
$campos['Trabajador.direccion'] = array();
$campos['Trabajador.codigo_postal'] = array();
$campos['Trabajador.barrio'] = array();
$campos['Trabajador.ciudad'] = array();
$campos['Trabajador.localidad_id'] = array("lov"=>array("controller"		=>	"localidades",
														"seleccionMultiple"	=> 	0,
														"separadorRetorno"	=> 	", ",
														"camposRetorno"		=>	array(	"Provincia.nombre",
																						"Localidad.nombre")));
$campos['Trabajador.pais'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Ubicacion", "imagen"=>"ubicacion.gif")));

$campos = null;
$campos['Trabajador.telefono'] = array();
$campos['Trabajador.celular'] = array();
$campos['Trabajador.email'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Contacto", "imagen"=>"contacto.gif")));

$campos = null;
$campos['Trabajador.tipo_cuenta'] = array("label"=>"Tipo");
$campos['Trabajador.cbu'] = array("aclaracion"=>"Ingrese sin guiones ni barras.");
if($this->action === "edit") {
	$campos['Trabajador.banco'] = array("type"=>"soloLectura");
	$campos['Trabajador.sucursal'] = array("type"=>"soloLectura");
	$campos['Trabajador.cuenta'] = array("type"=>"soloLectura");
}
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Informacion Bancaria", "imagen"=>"pagos.gif")));

$campos = null;
$campos['Trabajador.jubilacion'] = array();
$campos['Trabajador.condicion_id'] = array(	"lov"=>array("controller"		=>	"condiciones",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=>	array(	"Condicion.codigo",
																						"Condicion.nombre")));
$campos['Trabajador.obra_social_id'] = array(	"lov"=>array("controller"	=>	"obras_sociales",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=>	array(	"ObrasSocial.codigo",
																						"ObrasSocial.nombre")));
$campos['Trabajador.adicional_os'] = array("aclaracion"=>"Importe adicional en la Obra Social (SIAP).");
$campos['Trabajador.excedentes_os'] = array("aclaracion"=>"Importe de los excedentes en la Obra Social (SIAP).");
$campos['Trabajador.adherentes_os'] = array();
$campos['Trabajador.aporte_adicional_os'] = array("aclaracion"=>"Aporte adicional a la Obra Social (SIAP).");
$campos['Trabajador.siniestrado_id'] = array(	"aclaracion"=>"Indica algun tipo de imposibilidad (SIAP).",
											 	"lov"=>array("controller"	=>	"siniestrados",
														"seleccionMultiple"	=> 	0,
														"camposRetorno"		=>	array(	"Siniestrado.codigo",
																						"Siniestrado.nombre")));
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Afip", "imagen"=>"afip.gif")));

$campos = null;
$campos['Trabajador.observacion'] = array();
$fieldsets[] = array("campos"=>$campos, "opciones"=>array("div"=>array("class"=>"subset"), "fieldset"=>array("legend"=>"Observaciones", "imagen"=>"observaciones.gif")));



/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$miga = array('format' 	=> '%s %s', 
			  'content' => array('Trabajador.apellido', 'Trabajador.nombre'));
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"trabajadores.gif")));
echo $this->renderElement("add/add", array("fieldset"=>$fieldset, "opcionesForm"=>array("enctype"=>"multipart/form-data"), "miga" => $miga));

?>