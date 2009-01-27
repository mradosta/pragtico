<?php

/**
* Agrego descripcion adicional al form y los botones.
* El boton de Grabar y cancelar.
*/
$accion = $appForm->input("Form.accion", array("type"=>"hidden", "id"=>"accion", "value"=>"grabar"));
$cancelar = $appForm->button(__('Cancel', true), array("class"=>"cancelar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$duplicar = $appForm->button(__('Duplicate', true), array("onclick"=>"document.getElementById('accion').value='duplicar';document.getElementById('form').action='../save';form.submit();"));
$eliminar = $appForm->button(__('Delete', true), array("class"=>"boton_rojo", "onclick"=>"document.getElementById('accion').value='delete';document.getElementById('form').action='../delete/#*ID*#/2';form.submit();"));
$grabar = $appForm->submit(__('Save', true), array("id"=>"boton_grabar", "onclick"=>"document.getElementById('accion').value='grabar';form.submit();"));

if(isset($accionesExtra['opciones']['acciones'])) {
	foreach($accionesExtra['opciones']['acciones'] as $v) {
		switch ($v) {
			case "cancelar":
				$acciones[] = $cancelar;
				break;
			case "duplicar":
				$acciones[] = $duplicar;
				break;
			case "eliminar":
				$acciones[] = $eliminar;
				break;
			case "grabar":
				$acciones[] = $grabar;
				break;
			default:
				$acciones[] = $v;
				break;
		}
	}
}
else {
	if($this->params['action'] === "add") {
		$acciones[] = $appForm->tag("div", $appForm->input("Form.volverAInsertar", array('div' => false, "label"=>"Insertar un nuevo registro despues de grabar", "type"=>"checkbox", "checked"=>"false")), array("class"=>"volver_a_insertar"));
	}
	$acciones[] = $cancelar;
	if($this->params['action'] === "edit" && count($this->data) === 1) {
		$acciones[] = str_replace('#*ID*#', $this->data[0][Inflector::classify($this->params['controller'])]['id'], $eliminar);
		$acciones[] = $duplicar;
	}
	$acciones[] = $accion;
	$acciones[] = $grabar;
	if(!empty($accionesExtra)) {
		$acciones = array_merge($acciones, $accionesExtra);
	}
}
if(!empty($acciones)) {
	echo $appForm->tag("div", $acciones, array("class"=>"botones"));
}
/*
if(!empty($variablesForm['isAjax']) && $variablesForm['isAjax'] == "1") {
	$botones[] = $appForm->input("Form.isAjax", array("type"=>"hidden", "id"=>"isAjax", "value"=>"1"));
}
else {
	$botones[] = $appForm->input("Form.isAjax", array("type"=>"hidden", "id"=>"isAjax", "value"=>"0"));
}
*/

/*
if($this->params['action'] == "add" && empty($variablesForm['isAjax'])) {
	
	// Si hay parametros, pueden ser de una lov, los mando de vuelta al controller para no perderlos.
	
	if(!empty($this->params['pass'])) {
		$params[] = $appForm->input("Form.params", array("type"=>"hidden", "value"=>serialize($this->params['pass'])));
	}
	$params[] = $appForm->input("Form.volverAInsertar", array('div' => false, "label"=>"Insertar un nuevo registro despues de grabar", "type"=>"checkbox", "checked"=>"false"));
}
echo $appForm->tag("div", $botones, array("class"=>"botones"));
if(!empty($params)) {
	echo $appForm->tag("div", $params, array("class"=>"volver_a_insertar"));
}
*/

?>