<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Relacion-empleador_id'] = array(	"lov"=>array("controller"	=> "empleadores",
																		"camposRetorno"	=> array("Empleador.nombre")));


$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("legend"=>"Novedades", "imagen"=>"novedades.gif")));


$documento->create();
$documento->setCellValue("A1", "XXXXX");
$documento->setCellValue("A2", "MARTIN");
//$documento->save("/tmp/x1.xlsx");
$documento->save("/tmp/x1.html", "HTML");
exit;
d($documento);
d($registros);

/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$id = $v['Hora']['id'];
	$fila[] = array("model"=>"Hora", "field"=>"id", "valor"=>$v['Hora']['id'], "write"=>$v['Hora']['write'], "delete"=>$v['Hora']['delete']);
	$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>$v['Relacion']['Empleador']['nombre'], "nombreEncabezado"=>"Empleador");
	$fila[] = array("model"=>"Trabajador", "field"=>"numero_documento", "valor"=>$v['Relacion']['Trabajador']['numero_documento'], "class"=>"derecha", "nombreEncabezado"=>"Documento");
	$fila[] = array("model"=>"Trabajador", "field"=>"apellido", "valor"=>$v['Relacion']['Trabajador']['apellido'] . " " . $v['Relacion']['Trabajador']['nombre'], "nombreEncabezado"=>"Trabajador");
	$fila[] = array("model"=>"Hora", "field"=>"periodo", "valor"=>$v['Hora']['periodo']);
	$fila[] = array("model"=>"Hora", "field"=>"cantidad", "valor"=>$v['Hora']['cantidad']);
	$fila[] = array("model"=>"Hora", "field"=>"tipo", "valor"=>$v['Hora']['tipo']);
	$fila[] = array("model"=>"Hora", "field"=>"estado", "valor"=>$v['Hora']['estado']);
	if($v['Hora']['estado'] == "Liquidada") {
		$cuerpo[] = array("contenido"=>$fila, "opciones"=>array("seleccionMultiple"=>false, "eliminar"=>false, "modificar"=>false));
	}
	else {
		$cuerpo[] = $fila;
	}
}
$fila = null;
$fila[] = array("model"=>"Hora", "field"=>"id", "valor"=>"");
$fila[] = array("model"=>"Empleador", "field"=>"nombre", "valor"=>"");
$fila[] = array("model"=>"Trabajador", "field"=>"numero_documento", "valor"=>"");
$fila[] = array("model"=>"Trabajador", "field"=>"apellido", "valor"=>"");
$fila[] = array("model"=>"Hora", "field"=>"periodo", "valor"=>"");
$fila[] = array("model"=>"Hora", "field"=>"cantidad", "valor"=>$totales['cantidad']);
$fila[] = array("model"=>"Hora", "field"=>"tipo", "valor"=>"");
$fila[] = array("model"=>"Hora", "field"=>"estado", "valor"=>"");

$pie[] = $fila;
$accionesExtra[] = $formulario->link("Generar Planilla", null, array("title"=>"Genera las planillas para el ingreso masivo de horas", "class"=>"link_boton", "id"=>"botonGenerarPlanilla"));
$accionesExtra[] = $formulario->link("Importar Planilla", "importar_planilla", array("class"=>"link_boton", "title"=>"Importa las planillas de ingreso masivo de horas"));

$accionesExtra['opciones'] = array("acciones"=>array());
$botonesExtra[] = $formulario->button("Cancelar", array("title"=>"Cancelar", "class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='cancelar';form.submit();"));
$botonesExtra[] = $formulario->submit("Generar", array("title"=>"Generar la PLanilla para el Ingreso de Novedades", "onclick"=>"document.getElementById('accion').value='generar'"));
echo $this->renderElement("index/index", array("opcionesForm"=>array("action"=>"novedades"), "condiciones"=>$fieldset, "cuerpo"=>$cuerpo, "pie"=>$pie, "botonesExtra"=>array("opciones"=>array("botones"=>$botonesExtra)), "accionesExtra"=>$accionesExtra));

$js = "
	jQuery('#botonGenerarPlanilla').bind('click', function() {
		jQuery('#form').attr('action', '" . router::url("/") . $this->params['controller'] . "/generar_planilla');
		jQuery('#accion').attr('value', 'generar_planilla');
		jQuery('#form').submit();
	});
";
$formulario->addScript($js);
?>