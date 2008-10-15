<?php
/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Condicion.Documento-nombre'] = array();
$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("fieldset"=>array("imagen"=>"documentos.gif")));


/**
* Creo el cuerpo de la tabla.
*/
$cuerpo = null;
foreach ($registros as $k=>$v) {
	$fila = null;
	$fila[] = array("model"=>"Documento", "field"=>"id", "valor"=>$v['Documento']['id'], "write"=>$v['Documento']['write'], "delete"=>$v['Documento']['delete']);
	$fila[] = array("tipo"=>"accion", "valor"=>$formulario->link($formulario->image("archivo.gif", array("alt"=>"Descargar")), "descargar/" . $v['Documento']['id']));	
	$fila[] = array("model"=>"Documento", "field"=>"nombre", "valor"=>$v['Documento']['nombre']);
	$fila[] = array("model"=>"Documento", "field"=>"observacion", "valor"=>$v['Documento']['observacion']);
	$cuerpo[] = $fila;
}

echo $this->renderElement("index/index", array("accionesExtra"=>array("opciones"=>array("acciones"=>array("nuevo", "eliminar"))), "condiciones"=>$fieldset, "opcionesTabla"=>array("tabla"=>array("modificar"=>false)), "cuerpo"=>$cuerpo));

?>