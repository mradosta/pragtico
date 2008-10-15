<?php
/**
 * Crea el hidden que lleva la accion, esta accion puede ser buscar o limpiar.
 */
$botones[] = $formulario->input("Formulario.accion", array("type"=>"hidden", "id"=>"accion", "value"=>"buscar"));

/**
 * Si tengo el parametro de una seleccion multiple (cuando habro una lov), lo pongo en un hidden para no perderlo.
 */
 if(!empty($this->params['named'])) {
 	foreach($this->params['named'] as $k=>$v) {
 		if($k != "accion" && $k != "layout") {
 			$botones[] = $formulario->input("Formulario." . $k, array("type"=>"hidden", "value"=>$v));
 		}
 	}
 }


/**
* Decido en base a la preferencia si hacer un request ajax o comun.
*/
if($formulario->traerPreferencia("buscadores_posteo") == "ajax") {

	/**
	* Creo los botones de los buscadores.
	* El boton de Buscar y el de Limpiar.
	*/
	$botones[] = $formulario->button("Limpiar", array("class"=>"buscador_ajax", "title"=>"Limpiar los criterios de busqueda"));
	$botones[] = $formulario->button("Buscar", array("class"=>"buscador_ajax", "title"=>"Realizar la busqueda"));
	
	/**
	* Si esta seteado el valor retornarA y es un request AJAX, significa que es una lov (div).
	* Si es una lov (div), debo actualizar el div #target, sino, el div #index ya que es una busqueda comun.
	*/
	if(!empty($this->params['isAjax']) && !empty($layout) && $layout == "lov") {
		//array_pop($botones);
		//$botones[] = $formulario->button("Buscar", array("class"=>"buscador_ajax", "title"=>"Realizar la busqueda"));
		$botones[] = $formulario->input("Formulario.layout", array("type"=>"hidden", "id"=>"layout", "value"=>"lov"));

		/**
		 * Si tengo el parametro de un targetId (cuando habro una lov en un div), lo pongo en un hidden para no perderlo.
		 */
		if(isset($this->params['named']['targetId'])) {
			//$botones[] = $formulario->input("Formulario.targetId", array("type"=>"hidden", "value"=>$this->params['named']['targetId']));
			$target = $this->params['named']['targetId'];
		}
		//$botones[] = $formulario->input("Formulario.targetId", array("type"=>"hidden", "id"=>"targetId", "value"=>$this->viewVars['targetId']));
		//$target = $this->viewVars['targetId'];
	}
	else {
		$target = "index";
	}
	
	$botones[] = $formulario->codeBlock("
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
	$botones[] = $formulario->input("Formulario.layout", array("type"=>"hidden", "value"=>$this->layout));
	
	if(isset($botonesExtra['opciones']['botones'])) {
		foreach($botonesExtra['opciones']['botones'] as $v) {
			switch ($v) {
				case "limpiar":
					$botones[] = $formulario->button("Limpiar", array("title"=>"Limpiar los criterios de busqueda", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='limpiar';form.action='" . router::url("/") . $this->params['controller'] . "/" . $opcionesForm['action'] . "';form.submit();"));
					break;
				case "buscar":
					$botones[] = $formulario->submit("Buscar", array("title"=>"Realizar la busqueda", "onclick"=>"document.getElementById('accion').value='buscar'"));
					break;
				default:
					$botones[] = $v;
					break;
			}
		}
	}
	else {
		/**
		* Creo los botones de los buscadores.
		* El boton de Buscar y el de Limpiar.
		*/
		$botones[] = $formulario->button("Limpiar", array("title"=>"Limpiar los criterios de busqueda", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='limpiar';form.action='" . router::url("/") . $this->params['controller'] . "/" . $opcionesForm['action'] . "';form.submit();"));
		//$botones[] = $formulario->bloque($formulario->link("Limpiar", "", array("class"=>"link_boton", "title"=>"Limpiar los criterios de busqueda", "onclick"=>"document.getElementById('accion').value='limpiar';alert('xxx');form.submit();")));
		//$botones[] = $formulario->bloque($formulario->link("Limpiar", "", array("class"=>"link_boton", "title"=>"Limpiar los criterios de busqueda", "onclick"=>"document.getElementById('accion').value='limpiar';alert(document.getElementById('accion').value);jQuery('#form').submit()")));
		
		//$botones[] = $formulario->bloque($formulario->link("Buscar", "", array("class"=>"link_boton", "title"=>"Realizar la busqueda", "onclick"=>"document.getElementById('accion').value='buscar';form.submit();")));
		$botones[] = $formulario->submit("Buscar", array("title"=>"Realizar la busqueda", "onclick"=>"document.getElementById('accion').value='buscar'"));
	}
}


/**
* Puede que deba pintar mas botones.
* Estos deben venir en un array y ser codigo html.
if(!isset($botonesExtra)) {
	$botonesExtra = array();
}
$botones = am($botones, $botonesExtra);
*/


/**
* Creo un bloque con los botones y agrego el div clear antes para cerrar la caja redondeada.
*/
$botones[] = $formulario->bloque("", array("div"=>array("class"=>"clear")));
//$botones[] = $formulario->tag("div", null, array("class"=>"clear"));


echo $formulario->bloque($botones, array("div"=>array("class"=>"buscadores")));

?>