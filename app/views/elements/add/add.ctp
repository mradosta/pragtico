<?php
/**
* Creo el element que se mostrar en caso de errores al guardar (por ejmplo, clave duplicada).
*/
//$erroresDb = $this->renderElement('error_base_datos');
$erroresDb = "";

if(!isset($accionesExtra)) {
	$accionesExtra = array();
}


/**
* Creo los campos de ingreso de datos.
*/
$bloques[] = $fieldset;


/**
* Si me pasaron unbloque adicional, lo agrego.
*/
if(!empty($bloqueAdicional)) {
	$bloques[] = $bloqueAdicional;
}

/**
* Pongo las acciones.
*/
$bloques[] = $this->renderElement("add/acciones", array("accionesExtra"=>$accionesExtra));

/**
* Pongo alguna variable especifica del Form que me llega desde el controller.
*/
if(!empty($variablesForm)) {
	foreach($variablesForm as $variable=>$valor) {
		$bloques[] = $formulario->input("Form.{$variable}", array("type"=>"hidden", "value"=>$valor));
	}
}


/**
* Creo el formulario y pongo todo dentro.
*/
if(!isset($opcionesForm)) {
	$opcionesForm = array();
}
$form = $formulario->form($bloques, $opcionesForm);


/**
* Pongo todo dentro de un div (add) y muestro el resultado.
*/
//$form .= $formulario->bloque("Atención, los datos marcados con <span class='color_rojo'>(*)</span> son obligatorios.");
echo $formulario->bloque($erroresDb . $form, array("div"=>array("class"=>"add")));
//echo $form;
/*
echo $formulario->codeBlock('
	jQuery("input[@type=text], textarea").focus(
		function() {
 			this.select();
		});
	');
*/
?>