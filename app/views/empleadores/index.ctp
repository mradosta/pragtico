<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Empleador-nombre'] = array();
$condiciones['Condicion.Empleador-cuit'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"empleadores.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Empleador']['id'];
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose1", "imagen"=>array("nombre"=>"trabajadores.gif", "alt"=>"Trabajadores"), "url"=>'relaciones');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose2", "imagen"=>array("nombre"=>"conceptos.gif", "alt"=>"Conceptos"), "url"=>'conceptos');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose3", "imagen"=>array("nombre"=>"coeficientes.gif", "alt"=>"Coeficientes"), "url"=>'coeficientes');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose4", "imagen"=>array("nombre"=>"areas.gif", "alt"=>"Areas"), "url"=>'areas');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose5", "imagen"=>array("nombre"=>"recibos.gif", "alt"=>"Recibos"), "url"=>'recibos');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose6", "imagen"=>array("nombre"=>"rubros.gif", "alt"=>"Rubros"), "url"=>'rubros');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose7", "imagen"=>array("nombre"=>"cuentas.gif", "alt"=>"Cuentas"), "url"=>'cuentas');
	$fila[] = array("tipo"=>"desglose", "id"=>$id, "update"=>"desglose8", "imagen"=>array("nombre"=>"suss.gif", "alt"=>"Suss"), "url"=>'suss');
	$fila[] = array("model"=>"Empleador", "field"=>"id", "valor"=>$v['Empleador']['id'], "write"=>$v['Empleador']['write'], "delete"=>$v['Empleador']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"cuit", "valor"=>$v['Empleador']['cuit'], "class"=>"centro");
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['Empleador']['nombre']);
	$fila[] = array("model"=>"Empleador", "field"=>"telefono", "valor"=>$v['Empleador']['telefono']);
	$fila[] = array("model"=>"Empleador", "field"=>"email", "valor"=>$v['Empleador']['email']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("condiciones"=>$fieldset, "cuerpo"=>$cuerpo));
/*
echo $formulario->codeBlock('
	jQuery("#CondicionEmpleador-cuit").mask("99-99999999-9");
');
*/
?>