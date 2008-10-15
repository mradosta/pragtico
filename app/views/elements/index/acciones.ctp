<?php

/**
* Agrego los botones de las acciones.
* Nuevo, eliminar y modificar desde la seleccion multiple.
*
* Ejemplos:
* Solo el boton de accion nuevo y uno propio del formulario.
* $accionesExtra['opciones'] = array("acciones"=>array("nuevo", $formulario->bloque($formulario->link("Importar Planilla", "importarPlanillas", array("class"=>"link_boton", "title"=>"Importa las planillas de ingreso masivo de horas")))));
*
* Ninguna accion.
* $accionesExtra['opciones'] = array("acciones"=>array());
*/
$modificar = $formulario->bloque($formulario->link("Modificar", "", array("id"=>"modificar", "class"=>"link_boton", "title"=>"Modifica los registros seleccionados")));
$nuevo = $formulario->bloque($formulario->link("Nuevo", "add", array("class"=>"link_boton", "title"=>"Inserta un nuevo registro")));
$eliminar = $formulario->bloque($formulario->link("Eliminar", "", array("id"=>"eliminar", "class"=>"link_boton_rojo", "title"=>"Elimina los registros seleccionados")));

if((!empty($this->params['named']['layout']) && $this->params['named']['layout'] == "lov")) {
	echo $formulario->bloque("&nbsp;", array("div"=>array("class"=>"botones_acciones")));
}
else {
	if(isset($accionesExtra['opciones']['acciones'])) {
		foreach($accionesExtra['opciones']['acciones'] as $v) {
			switch ($v) {
				case "nuevo":
					$acciones[] = $nuevo;
					break;
				case "modificar":
					$acciones[] = $modificar;
					break;
				case "eliminar":
					$acciones[] = $eliminar;
					break;
				default:
					$acciones[] = $v;
					break;
			}
		}
	}
	else {
		$acciones[] = $nuevo;
		$acciones[] = $modificar;
		$acciones[] = $eliminar;
		$acciones = am($acciones, $accionesExtra);
	}
	if(!empty($acciones)) {
		echo $formulario->bloque($acciones, array("div"=>array("class"=>"botones_acciones")));
	}
}

?>