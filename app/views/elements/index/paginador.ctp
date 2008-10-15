<?php


/**
* Creo un bloque con el paginador.
* Lo divido al paginador en navegacion (las flechitas) y la posicion, registro (4 de 10).
*/
$options = array();
foreach(array("named", "pass") as $nombre) {
	if(!empty($this->params[$nombre])) {
		unset($this->params[$nombre]['direction']);
		unset($this->params[$nombre]['sort']);
		unset($this->params[$nombre]['page']);
		$options = am($options, $this->params[$nombre]);
	}
}
$bloque_paginador = "";
$bloque_paginador .= $formulario->bloque($paginador->paginador("navegacion", array("url"=>$options)), array("div"=>array("class"=>"navegacion")));
$bloque_paginador .= $formulario->bloque($paginador->paginador("posicion"), array("div"=>array("class"=>"posicion")));


/**
* Si hay algun registro, muestro el "mostrar".
*/
if(isset($paginador->params['paging'][inflector::classify($paginador->params['controller'])]['count']) && $paginador->params['paging'][inflector::classify($paginador->params['controller'])]['count'] > 0) {
	$ver = null;
	$ver[15] = $formulario->link("15", am($options, array("filas_por_pagina"=>"15")), array("title"=>"Ver 15 registros"));
	$ver[25] = $formulario->link("25", am($options, array("filas_por_pagina"=>"25")), array("title"=>"Ver 25 registros"));
	$ver[50] = $formulario->link("50", am($options, array("filas_por_pagina"=>"50")), array("title"=>"Ver 50 registros"));
	$ver[1000] = $formulario->link("T", am($options, array("filas_por_pagina"=>"1000")), array("title"=>"Ver todos los registros"));
	$cantidadActual = $this->params['paging'][inflector::classify($this->name)]['options']['limit'];
	if($cantidadActual < 1000) {
		$ver[$cantidadActual] = $formulario->tag("span", $cantidadActual);
	}
	else {
		$ver[$cantidadActual] = $formulario->tag("span", "T");
	}
	$bloque_paginador .= $formulario->bloque("Ver: " . implode("/", $ver), array("div"=>array("class"=>"cantidad_a_mostrar")));
}
echo $bloque_paginador;
?>