<?php
$grupos = "";
$usuario = $session->read("__Usuario");

foreach($usuario['Grupo'] as $grupo) {
	$default = "";
	if($grupo['id'] === $usuario['Usuario']['preferencias']['grupo_default_id']) {
		$default = $formulario->image('default.gif', array('alt' => "Grupo a utilizar por defecto")) . ' ' . $grupo['nombre'];
	}
	else {
		$default = $formulario->link($grupo['nombre'], array(
									'controller' 	=> 'grupos',
		  							'action'		=> 'setear_grupo_default',
		    						$grupo['id']), array("title"=>"Hacer de este grupo el grupo por defecto"));
	}

	if((int)$grupo['id'] & (int)$usuario['Usuario']['preferencias']['grupos_seleccionados']) {
		$p = $formulario->link($formulario->image('ok.gif', array('alt' => "Deseleccionar este Grupo")) . null, array(
							   		'controller' 	=> 'grupos',
									'action'		=> 'cambiar_grupo_activo',
		 							'accion'		=> 'quitar',
		  							'grupo_id'		=>	$grupo['id']));
	}
	else {
		$p = $formulario->link($formulario->image('error.gif', array('alt' => "Seleccionar este Grupo")) . null, array(
							   		'controller' 	=> 'grupos',
									'action'		=> 'cambiar_grupo_activo',
		 							'accion'		=> 'agregar',
		  							'grupo_id'		=>	$grupo['id']));
	}

	$p = $default . ' ' . $p;
	$lis[] = $formulario->tag("li", $p);
}

foreach($usuario['Rol'] as $rol) {
	$p = $rol['nombre'];
	$lis[] = $formulario->tag("li", $p);
}

if(!empty($lis)) {
	$grupos = $formulario->tag("ul", implode("", $lis));
}

$codigo_html = "";
$grupos = $formulario->tag("div", $grupos, array("class"=>"grupos"));
$codigo_html .= $formulario->tag("div", $grupos, array("class"=>"encabezado"));
echo $codigo_html;

?>