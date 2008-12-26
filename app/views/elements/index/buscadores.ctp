<?php
/**
 * Crea el hidden que lleva la accion, esta accion puede ser buscar o limpiar.
 */
$out[] = $formulario->input("Formulario.accion", array("type"=>"hidden", "id"=>"accion", "value"=>"buscar"));

/**
 * Si tengo el parametro de una seleccion multiple (cuando habro una lov), lo pongo en un hidden para no perderlo.
 */
 if(!empty($this->params['named'])) {
 	foreach($this->params['named'] as $k=>$v) {
 		if($k != "accion" && $k != "layout") {
 			$out[] = $formulario->input("Formulario." . $k, array("type"=>"hidden", "value"=>$v));
 		}
 	}
 }


/**
* Decido en base a la preferencia si hacer un request ajax o comun.
*/
//if($formulario->traerPreferencia("buscadores_posteo") == "ajax") {
if(false) {

	/**
	* Creo los botones de los buscadores.
	* El boton de Buscar y el de Limpiar.
	*/
	$out[] = $formulario->button(__("Clear", true), array("class"=>"buscador_ajax", "title"=>"Limpiar los criterios de busqueda"));
	$out[] = $formulario->button(__("Search", true), array("class"=>"buscador_ajax", "title"=>"Realizar la busqueda"));
	
	/**
	* Si esta seteado el valor retornarA y es un request AJAX, significa que es una lov (div).
	* Si es una lov (div), debo actualizar el div #target, sino, el div #index ya que es una busqueda comun.
	*/
	if(!empty($this->params['isAjax']) && !empty($layout) && $layout == "lov") {
		//array_pop($botones);
		//$out[] = $formulario->button("Buscar", array("class"=>"buscador_ajax", "title"=>"Realizar la busqueda"));
		$out[] = $formulario->input("Formulario.layout", array("type"=>"hidden", "id"=>"layout", "value"=>"lov"));

		/**
		 * Si tengo el parametro de un targetId (cuando habro una lov en un div), lo pongo en un hidden para no perderlo.
		 */
		if(isset($this->params['named']['targetId'])) {
			//$out[] = $formulario->input("Formulario.targetId", array("type"=>"hidden", "value"=>$this->params['named']['targetId']));
			$target = $this->params['named']['targetId'];
		}
		//$out[] = $formulario->input("Formulario.targetId", array("type"=>"hidden", "id"=>"targetId", "value"=>$this->viewVars['targetId']));
		//$target = $this->viewVars['targetId'];
	}
	else {
		$target = "index";
	}
	
	$out[] = $formulario->codeBlock("
		jQuery('#{$target} .buscador_ajax').click(function(){

			/**
			* Seteo la accion (buscar o limpiar).
			*/
			var accion = jQuery(this).val().toLowerCase();
			jQuery('#{$target} #form #accion').val(accion);
			/**
			* Seteo las opciones para hacer el request ajax.
			*/
			var url = jQuery('#{$target} #form').attr('action');
			var options = {
				target: 	'#{$target}',
				type: 		'POST',
				url:		url
			};
			/**
			* Hago el submit ajax.
			*/
			jQuery('#" . $target . " #form').ajaxSubmit(options);

		});
	");
	
}
else {
	/**
	* hidden para no perder el layout en el que estoy ni si es permitido seleccion Multiple o no.
	*/
	$out[] = $formulario->input("Formulario.layout", array("type"=>"hidden", "value"=>$this->layout));
	
	$limpiar = $formulario->button(__("Clear", true), array("class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='limpiar';form.action='" . Router::url(array("controller" => $this->params['controller'], "action" => $opcionesForm['action'])) . "';form.submit();"));
	$buscar = $formulario->submit(__("Search", true), array("onclick"=>"document.getElementById('accion').value='buscar'"));
	
	if(isset($botonesExtra['opciones']['botones'])) {
		foreach($botonesExtra['opciones']['botones'] as $v) {
			switch ($v) {
				case "limpiar":
					$out[] = $limpiar;
					break;
				case "buscar":
					$out[] = $buscar;
					break;
				default:
					$out[] = $v;
					break;
			}
		}
	}
	else {
		/**
		* Creo los botones de los buscadores.
		* El boton de Buscar y el de Limpiar.
		*/
		$out[] = $limpiar;
		$out[] = $buscar;
	}
}

/**
* Creo un bloque con los botones y agrego el div clear antes para cerrar la caja redondeada.
*/
$out[] = $formulario->tag("div", "", array("class"=>"clear"));


echo $formulario->tag("div", $out, array("class"=>"buscadores"));

?>