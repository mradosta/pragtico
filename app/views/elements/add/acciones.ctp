<?php

/**
* Agrego descripcion adicional al form y los botones.
* El boton de Grabar y cancelar.
*/
$accion = $formulario->input("Form.accion", array("type"=>"hidden", "id"=>"accion", "value"=>"grabar"));
$cancelar = $formulario->button("Cancelar", array("class"=>"cancelar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$duplicar = $formulario->button("Duplicar", array("onclick"=>"document.getElementById('accion').value='duplicar';document.getElementById('form').action='" . Router::url("/") . $this->params['controller'] . "/add';form.submit();"));
$eliminar = $formulario->button("Eliminar", array("class"=>"boton_rojo", "onclick"=>"document.getElementById('accion').value='duplicar';document.getElementById('form').action='" . Router::url("/") . $this->params['controller'] . "/add';form.submit();"));
$grabar = $formulario->submit("Grabar", array("id"=>"boton_grabar", "onclick"=>"document.getElementById('accion').value='grabar';form.submit();"));

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
		$acciones[] = $formulario->tag("div", $formulario->input("Form.volverAInsertar", array("div"=>false, "label"=>"Insertar un nuevo registro despues de grabar", "type"=>"checkbox", "checked"=>"false")), array("class"=>"volver_a_insertar"));
	}
	if($this->params['action'] === "edit" && count($this->data) == 1) {
		$acciones[] = $eliminar;
		$acciones[] = $duplicar;
	}
	$acciones[] = $accion;
	$acciones[] = $cancelar;
	$acciones[] = $grabar;
	if(!empty($accionesExtra)) {
		$acciones = array_merge($acciones, $accionesExtra);
	}
}
if(!empty($acciones)) {
	echo $formulario->tag("div", $acciones, array("class"=>"botones"));
}
/*
if(!empty($variablesForm['isAjax']) && $variablesForm['isAjax'] == "1") {
	$botones[] = $formulario->input("Form.isAjax", array("type"=>"hidden", "id"=>"isAjax", "value"=>"1"));
}
else {
	$botones[] = $formulario->input("Form.isAjax", array("type"=>"hidden", "id"=>"isAjax", "value"=>"0"));
}
*/

/*
if($this->params['action'] == "add" && empty($variablesForm['isAjax'])) {
	
	// Si hay parametros, pueden ser de una lov, los mando de vuelta al controller para no perderlos.
	
	if(!empty($this->params['pass'])) {
		$params[] = $formulario->input("Form.params", array("type"=>"hidden", "value"=>serialize($this->params['pass'])));
	}
	$params[] = $formulario->input("Form.volverAInsertar", array("div"=>false, "label"=>"Insertar un nuevo registro despues de grabar", "type"=>"checkbox", "checked"=>"false"));
}
echo $formulario->tag("div", $botones, array("class"=>"botones"));
if(!empty($params)) {
	echo $formulario->tag("div", $params, array("class"=>"volver_a_insertar"));
}
*/

?>