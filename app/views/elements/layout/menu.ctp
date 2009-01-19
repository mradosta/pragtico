<?php
/**
 TODO
 Ver que hacer con los params. --usar siempre key para array
 */
$baseUrl = Router::url("/");
if($baseUrl != "/") {
	$menuActual = Router::parse(str_replace($baseUrl, "", $this->here));
}
else {
	$menuActual = Router::parse($this->here);
}

$MenuItems = $session->read('__MenuItems');
$navegacion = $appForm->traerPreferencia("navegacion");

$actual = 0;
$c = 0;
$menu = "";
foreach($MenuItems as $k=>$padre) {
	
	if(empty($padre['Menu']['ayuda'])) {
		$padre['Menu']['ayuda'] = $padre['Menu']['etiqueta'];
	}

	if(empty($padre['Menu']['imagen'])) {
		$padre['Menu']['imagen'] = $padre['Menu']['nombre'] . '.gif';
	}

	$menu .=  $appForm->tag("dt", $appForm->image($padre['Menu']['imagen']) . $padre['Menu']['etiqueta'], array("title" => $padre['Menu']['ayuda']));

	$hijos = "";
	foreach($padre['children'] as $k1=>$hijo) {
		if(empty($hijo['Menu']['ayuda'])) {
			$hijo['Menu']['ayuda'] = $hijo['Menu']['etiqueta'];
		}

		if(empty($hijo['Menu']['imagen'])) {
			$hijo['Menu']['imagen'] = $hijo['Menu']['nombre'] . '.gif';
		}
		
		$url = array(	"controller"	=> $hijo['Menu']['controller'],
						"action"		=> $hijo['Menu']['action']);

		if($navegacion === "ajax") {
			$hijos .= $ajax->link($appForm->image($hijo['Menu']['imagen']) . $appForm->tag("span", $hijo['Menu']['etiqueta']), $url, array("update"=>"index", "title"=>$hijo['Menu']['ayuda']));
		}
		else {
			$hijos .= $appForm->link($appForm->image($hijo['Menu']['imagen']) . $appForm->tag("span", $hijo['Menu']['etiqueta']), $url, array("title"=>$hijo['Menu']['ayuda']));
		}
		
		if($menuActual['controller'] === $hijo['Menu']['controller'] && $menuActual['action'] == $hijo['Menu']['action']) {
			$actual = $c;
		}
		else if($menuActual['controller'] === $hijo['Menu']['controller'] && $actual == 0) {
			$actual = $c;
		}
	}
	$menu .=  $appForm->tag("dd", $hijos);
	$c++;
}
$menu =  $appForm->tag("dl", $menu);
echo $appForm->tag("div", $menu, array("class"=>"menu"));

$js = "
	jQuery('.menu').Accordion( {
			headerSelector	: 'dt',
			panelSelector	: 'dd',
			activeClass 	: 'menuActive',
			hoverClass   	: 'menuHover',
			panelHeight 	: 305,
			speed         	: 300,
			currentPanel	: {$actual}
		}
	);
";
$appForm->addScript($js, "ready");
?>