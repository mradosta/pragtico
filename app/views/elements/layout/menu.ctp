<?php

$MenuItems = $session->read('__itemsMenu');
//$navegacion = $appForm->traerPreferencia('navegacion');
$navegacion = null;

$menu = '';
foreach ($MenuItems as $k => $padre) {
	
	if (empty($padre['Menu']['ayuda'])) {
		$padre['Menu']['ayuda'] = $padre['Menu']['etiqueta'];
	}

	if (empty($padre['Menu']['imagen'])) {
		$padre['Menu']['imagen'] = $padre['Menu']['nombre'] . '.gif';
	}

	$menu .=  $appForm->tag('dt', $appForm->image($padre['Menu']['imagen']) . $padre['Menu']['etiqueta'], array('title' => $padre['Menu']['ayuda']));

	$hijos = '';
	foreach ($padre['children'] as $k1 => $hijo) {
		if(empty($hijo['Menu']['ayuda'])) {
			$hijo['Menu']['ayuda'] = $hijo['Menu']['etiqueta'];
		}

		if(empty($hijo['Menu']['imagen'])) {
			$hijo['Menu']['imagen'] = $hijo['Menu']['nombre'] . '.gif';
		}
		
		$url = array(	'controller'	=> $hijo['Menu']['controller'],
						'action'		=> $hijo['Menu']['action'],
						'am'			=> $k);

		if ($navegacion === 'ajax') {
			$hijos .= $ajax->link($appForm->image($hijo['Menu']['imagen']) . $appForm->tag('span', $hijo['Menu']['etiqueta']), $url, array('update'=>'index', 'title' => $hijo['Menu']['ayuda']));
		} else {
			$hijos .= $appForm->link($appForm->image($hijo['Menu']['imagen']) . $appForm->tag('span', $hijo['Menu']['etiqueta']), $url, array('title' => $hijo['Menu']['ayuda']));
		}
	}
	$menu .=  $appForm->tag('dd', $hijos);
}
$menu =  $appForm->tag('dl', $menu);
echo $appForm->tag('div', $menu, array('class'=>'menu'));

/**
 * Get actualMenu passed by argument or from session.
 */
if (isset($this->params['named']['am'])) {
	$actualMenu = $this->params['named']['am'];
} else {
	$actualMenu = $session->read('__actualMenu');
}

$js = '
	jQuery(".menu").Accordion( {
			headerSelector	: "dt",
			panelSelector	: "dd",
			activeClass 	: "menuActive",
			hoverClass   	: "menuHover",
			panelHeight 	: 305,
			speed         	: 300,
			currentPanel	: ' . $actualMenu . '
		}
	);
';
//console.log(jQuery(".menu")[0].accordionCfg.currentPanel);
$appForm->addScript($js, 'ready');
?>