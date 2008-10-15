<?php
/**
* Especifico los campos de ingreso de datos.
*/
$campos = null;
$campos['Documento.id'] = array();
$campos['Documento.nombre'] = array();
$campos['Documento.patrones'] = array("type"=>"hidden");
$campos['Documento.model'] = array("type"=>"select", "options"=>$models, "empty"=>true);
$campos['Documento.archivo'] = array("aclaracion"=>"Debe cargar un archivo RTF con los patrones de la forma #*Model.campo*#.", "type"=>"file", "label"=>"Archivo Origen (.rtf)");
$campos['Documento.observacion'] = array();
$fieldsets[] = array("campos"=>$campos);

/**
* Pinto el element add con todos los fieldsets que he definido.
*/
$bloqueAdicional = "";
if(!empty($patrones)) {
	$bloque = "Se identificaron los siguientes patrones dentro del Documento:";
	$lis = array();
	foreach($patrones as $v) {
		$lis[] = $formulario->tag("li",  $v);
	}
	$bloque .= $formulario->tag("ul",  $lis);
	$bloque .= $formulario->tag("span",  "Presione sobre el boton grabar para confirmar si los patrones encontrados son correctos.<br />En caso de que no lo sean correctos, presione el boton cancelar, modifique el archivo de origen y reintentelo.<br /><br /><br /><br />");
	$bloque .= $formulario->input("Form.confirmar", array("type"=>"hidden", "value"=>"confirmado"));
	$bloqueAdicional = $formulario->tag("div", $bloque, array("class"=>"unica"));
}


$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("imagen"=>"documentos.gif")));
echo $this->renderElement("add/add", array("bloqueAdicional"=>$bloqueAdicional, "opcionesForm"=>array("enctype"=>"multipart/form-data"), "fieldset"=>$fieldset));
?>