<?php
$fila = null;
$valor = $formulario->input("yy.xx", array("id"=>"dueno", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false));
$fila[] = array("valor"=>$valor . " Dueño", "class"=>"imitar_th_centro");
$link = $formulario->input("Permisos.dl", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$link = $formulario->input("Permisos.de", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$link = $formulario->input("Permisos.dd", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$cuerpo[] = $fila;

$fila = null;
$valor = $formulario->input("yy.yy", array("id"=>"grupo", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false));
$fila[] = array("valor"=>$valor . " Grupo", "class"=>"imitar_th_centro");
$link = $formulario->input("Permisos.gl", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$link = $formulario->input("Permisos.ge", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$link = $formulario->input("Permisos.gd", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$cuerpo[] = $fila;

$fila = null;
$valor = $formulario->input("yy.zz", array("id"=>"otros", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false));
$fila[] = array("valor"=>$valor . " Otros", "class"=>"imitar_th_centro");
$link = $formulario->input("Permisos.ol", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$link = $formulario->input("Permisos.oe", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$link = $formulario->input("Permisos.od", array("type"=>"checkbox", "label"=>false, "div"=>false, "class"=>"checkbox"));
$fila[] = array("valor"=>$link, "class"=>"centro");
$cuerpo[] = $fila;

$encabezado[] = $formulario->input("xx.vv", array("id"=>"todos", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false));
$encabezado[] = $formulario->input("xx.xx", array("id"=>"leer", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false)) . " Leer";
$encabezado[] = $formulario->input("xx.yy", array("id"=>"escribir", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false)) . " Escribir";
$encabezado[] = $formulario->input("xx.zz", array("id"=>"eliminar", "type"=>"checkbox", "class"=>"checkbox", "label"=>false, "div"=>false)) . " Eliminar";

$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>false,
									"ordenEnEncabezados"=>false,
									'permisos'			=>false,
									"modificar"			=>false,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>true,
									"zebra"				=>false,
									"mostrarIds"		=>false,
									"omitirMensajeVacio"=>true));


$tabla = $formulario->tabla(am(array("cuerpo"=>$cuerpo, "encabezado"=>$encabezado), $opcionesTabla));

/**
* Pongo todo dentro de un div (index) y muestro el resultado.
*/
$permisos[] = $formulario->bloque("&nbsp;", array("div"=>array("class"=>"clear")));
$permisos[] = $formulario->bloque($tabla);


/**
* Especifico los campos para ingresar las condiciones.
*/
$condiciones['Permisos.usuario_id'] = array("options"=>$usuarios, "empty"=>true);
$condiciones['Permisos.grupo_id'] = array("options"=>$grupos, "empty"=>true);
$condiciones['Permisos.model_id'] = array("label"=>"Modelo", "options"=>$models);


$fieldsets[] = array("campos"=>$condiciones);
$fieldset = $formulario->pintarFieldsets($fieldsets, array("div"=>array("class"=>"unica"), "fieldset"=>array("legend"=>"Asignar Permisos / Cambiar Dueño y/o Grupo","imagen"=>"buscar.gif")));
$fieldset .= $formulario->bloque($permisos);

/**
* Creo los inputs para ingresar las condiciones.
*/
$bloque_condiciones = $formulario->bloque($fieldset, array("div"=>array("id"=>"condiciones")));


/**
* Creo un bloque con caja redondeada entre las condiciones, los botones y las opciones lov (si las hubiese).
*/
if(empty($accion)) {
	$accion = "falta_confirmacion";
	$advertencia = "";
	$labelBoton = "Asignar";
}
elseif($accion == "falta_confirmacion") {
	$accion = "confirmado";
	if(!empty($usuario))
		$mensaje[] = "El nuevo dueño de los registros sera el usuario " . $usuario;
	if(!empty($grupo))
		$mensaje[] = "El nuevo grupo primario de los registros sera " . $grupo;
	$advertencia = implode("<br />", $mensaje);
	if($model == "Todos") {
		$texto = "Todos los modelos";
	}
	else {
		$texto = $model;
	}
	$bloque_botones[] = "<span class='color_rojo'><h1>Atencion, los cambios se aplicaran sobre TODOS los registros de " . $texto . "</h1>" . $advertencia . "</span>";
	$labelBoton = "Confirmar";
}
$bloque_botones[] = $formulario->input("Formulario.accion", array("type"=>"hidden", "id"=>"accion"));
$bloque_botones[] = $formulario->submit($labelBoton, array("title"=>"Realiza la Asignacion", "onclick"=>"document.getElementById('accion').value='" . $accion . "'"));
$bloque_botones[] = $formulario->bloque("", array("div"=>array("class"=>"clear")));
$botones = $formulario->bloque($bloque_botones, array("div"=>array("id"=>"botones", "class"=>"botones")));

$bloques[] = $formulario->bloque(am($bloque_condiciones, $botones), array("caja_redondeada"=>true));


/**
* Creo el formulario y pongo todo dentro.
*/
$form = $formulario->form($bloques, array("action"=>"asignar"));


/**
* Pongo todo dentro de un div (index) y muestro el resultado.
*/
echo $formulario->bloque($form, array("div"=>array("id"=>"index", "class"=>"index")));

echo $formulario->codeBlock('
	var valor;
	
	jQuery("#todos").click(
		function() {
			if(jQuery(this).attr("checked")) {
				jQuery("input[@type=\'checkbox\']").checkbox("seleccionar");
			}
			else {
				jQuery("input[@type=\'checkbox\']").checkbox("deseleccionar");
			}
		}
	);
	
	jQuery("#leer").click(
		function() {
			if(jQuery(this).attr("checked")) {
				valor = true;
			}
			else {
				valor = false;
			}
			jQuery("#PermisosDl").attr("checked", valor);
			jQuery("#PermisosGl").attr("checked", valor);
			jQuery("#PermisosOl").attr("checked", valor);
		}
	);

	jQuery("#escribir").click(
		function() {
			if(jQuery(this).attr("checked")) {
				valor = true;
			}
			else {
				valor = false;
			}
			jQuery("#PermisosDe").attr("checked", valor);
			jQuery("#PermisosGe").attr("checked", valor);
			jQuery("#PermisosOe").attr("checked", valor);
		}
	);

	jQuery("#eliminar").click(
		function() {
			if(jQuery(this).attr("checked")) {
				valor = true;
			}
			else {
				valor = false;
			}
			jQuery("#PermisosDd").attr("checked", valor);
			jQuery("#PermisosGd").attr("checked", valor);
			jQuery("#PermisosOd").attr("checked", valor);
		}
	);

	jQuery("#dueno").click(
		function() {
			if(jQuery(this).attr("checked")) {
				valor = true;
			}
			else {
				valor = false;
			}
			jQuery("#PermisosDl").attr("checked", valor);
			jQuery("#PermisosDe").attr("checked", valor);
			jQuery("#PermisosDd").attr("checked", valor);
		}
	);
	
	jQuery("#grupo").click(
		function() {
			if(jQuery(this).attr("checked")) {
				valor = true;
			}
			else {
				valor = false;
			}
			jQuery("#PermisosGl").attr("checked", valor);
			jQuery("#PermisosGe").attr("checked", valor);
			jQuery("#PermisosGd").attr("checked", valor);
		}
	);
		
	jQuery("#otros").click(
		function() {
			if(jQuery(this).attr("checked")) {
				valor = true;
			}
			else {
				valor = false;
			}
			jQuery("#PermisosOl").attr("checked", valor);
			jQuery("#PermisosOe").attr("checked", valor);
			jQuery("#PermisosOd").attr("checked", valor);
		}
	);
	');


?>