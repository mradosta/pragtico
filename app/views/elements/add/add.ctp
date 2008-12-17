<?php

/**
 * Creo la miga de pan.
 */
$formulario->addCrumb($this->name, array("controller" => $this->params['controller'], "action" => "index"));
if($this->action === "add") {
	$formulario->addCrumb("Nuevo");
}
else {
	$formulario->addCrumb("Modificar");
	if(!empty($migaEdit)) {
		$count = count($this->data);
		if($count === 1) {
			$formulario->addCrumb("<h5>" . $migaEdit . "</h5>");
		}
		else {
			$formulario->addCrumb($count . " Registros");
		}
	}	
}


/**
 * Me aseguro de que este definida la varaible.
 */
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
$bloques[] = $this->element("add/acciones", array("accionesExtra"=>$accionesExtra));


/**
 * Pongo alguna variable especifica del Form que me llega desde el controller.
 */
if(!empty($variablesForm)) {
	foreach($variablesForm as $variable=>$valor) {
		$bloques[] = $formulario->input("Form." . $variable, 
										array(	"type"	=> "hidden", 
												"value"	=> $valor));
	}
}


/**
 * Creo el formulario y pongo todo dentro.
 */
if(!isset($opcionesForm['action'])) {
	$opcionesForm['action'] = "save_multiple";
}
$opcionesForm['action'] = "save_multiple";
$form = $formulario->form($bloques, $opcionesForm);


/**
* Pongo todo dentro de un div (add) y muestro el resultado.
*/
echo $formulario->bloque($form, array("div"=>array("class"=>"add")));
?>