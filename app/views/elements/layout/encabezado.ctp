<?php
$grupos = '';
$usuario = $session->read('__Usuario');

foreach ($usuario['Grupo'] as $grupo) {
    
	if ((int)$grupo['id'] & (int)$usuario['Usuario']['preferencias']['grupos_seleccionados']) {
		$p = $appForm->link($appForm->image('ok.gif', array('alt' => 'Deseleccionar este Grupo')), array(
							   		'controller' 	=> 'grupos',
									'action'		=> 'cambiar_grupo_activo',
		 							'accion'		=> 'quitar',
		  							'grupo_id'		=>	$grupo['id']));
	} else {
		$p = $appForm->link($appForm->image('error.gif', array('alt' => 'Seleccionar este Grupo')), array(
							   		'controller' 	=> 'grupos',
									'action'		=> 'cambiar_grupo_activo',
		 							'accion'		=> 'agregar',
		  							'grupo_id'		=>	$grupo['id']));
	}

    if ($grupo['id'] === $usuario['Usuario']['preferencias']['grupo_default_id']) {
        $defaultIcon = $appForm->image('default.gif', array('alt' => 'Grupo a utilizar por defecto')) . ' ' . $grupo['nombre'];
        $defaultIcon = $appForm->tag('span', $defaultIcon, array('class' => 'default'));
        $liDefault = $appForm->tag('li', $defaultIcon . ' ' . $p);
    } else {
        $default = $appForm->link($grupo['nombre'], array(
                                  'controller'    => 'grupos',
                                  'action'        => 'setear_grupo_default',
                                  $grupo['id']), array('title' => 'Hacer de este grupo el grupo por defecto'));
        $lis[] = $appForm->tag('li', $default . ' ' . $p);
    }
}

foreach ($usuario['Rol'] as $rol) {
    $lis[] = $appForm->tag('li', $rol['nombre']);
}

if (!empty($lis)) {
    if (!empty($liDefault)) {
        array_unshift($lis, $liDefault);
    }
	$grupos = $appForm->tag('ul', implode('', $lis));
}

$grupos = $appForm->tag('div', $grupos, array('class' => 'grupos'));
echo $appForm->tag('div', $grupos, array('class' => 'encabezado'));

?>