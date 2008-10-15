<?php
/**
* Datos del empleador.
*/
$fila = null;
$fila[] = array("valor"=>"Datos del Empleador", "class"=>"imitar_th_izquierda", "colspan"=>11);
$cuerpo[] = $fila;

$fila = null;
$fila[] = array("valor"=>"<span class='label_liquidacion'>Nombre: </span>" . $this->data['Liquidacion']['empleador_nombre'], "class"=>"izquierda", "colspan"=>11);
$cuerpo[] = $fila;

$fila = null;
$fila[] = array("valor"=>"<span class='label_liquidacion'>Direccion: </span>" . $this->data['Liquidacion']['empleador_direccion'], "class"=>"izquierda", "colspan"=>11);
$cuerpo[] = $fila;

$fila = null;
$fila[] = array("valor"=>"<span class='label_liquidacion'>Cuit: </span>" . $this->data['Liquidacion']['empleador_cuit'], "class"=>"izquierda", "colspan"=>11);
$cuerpo[] = $fila;

/**
* Datos del trabajador.
*/
$fila = null;
$fila[] = array("valor"=>"Datos del Trabajador", "class"=>"imitar_th_izquierda", "colspan"=>11);
$cuerpo[] = $fila;

$fila = null;
$fila[] = array("valor"=>"<span class='label_liquidacion'>Nombre: </span>" . $this->data['Liquidacion']['trabajador_nombre'] . ", " . $this->data['Liquidacion']['trabajador_apellido'], "class"=>"izquierda", "colspan"=>4);
$fila[] = array("valor"=>"<span class='label_liquidacion'>Cuil: </span>" . $this->data['Liquidacion']['trabajador_cuil'], "class"=>"izquierda", "colspan"=>6);
$cuerpo[] = $fila;

$fila = null;
$fila[] = array("valor"=>"<span class='label_liquidacion'>Puesto/Categoria: </span>" . $this->data['Liquidacion']['convenio_categoria_nombre'], "class"=>"izquierda", "colspan"=>3);
$fila[] = array("valor"=>"<span class='label_liquidacion'>Jornada: </span>" . $this->data['Liquidacion']['convenio_categoria_jornada'], "class"=>"izquierda");
$fila[] = array("valor"=>"<span class='label_liquidacion'>Ingreso: </span>" . $formato->format($this->data['Liquidacion']['relacion_ingreso'], "db2helper"), "class"=>"izquierda", "colspan"=>6);
$cuerpo[] = $fila;


/**
* Conceptos.
*/
$fila = null;
$fila[] = array("valor"=>"Liquidacion de Haberes", "class"=>"imitar_th_izquierda", "colspan"=>11);
$cuerpo[] = $fila;
$fila = null;
$fila[] = array("valor"=>"Codigo", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Concepto", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Coeficiente", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Imprime", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Formula", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Resolucion", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Cantidad", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Remunarativo", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"Deduccion", "class"=>"imitar_th_izquierda");
$fila[] = array("valor"=>"No Remunarativo", "class"=>"imitar_th_izquierda");
$cuerpo[] = $fila;


foreach($this->data['LiquidacionesDetalle'] as $concepto) {
		$fila = null;
		$fila[] = array("valor"=>$concepto['concepto_codigo']);
		$fila[] = array("valor"=>$concepto['concepto_nombre']);
		$fila[] = array("valor"=>$concepto['coeficiente_nombre'] . "-" . $concepto['coeficiente_valor']);
		$fila[] = array("valor"=>$concepto['concepto_imprimir']);
		$fila[] = array("valor"=>$concepto['concepto_formula']);
		$fila[] = array("valor"=>$concepto['debug']);
		if($concepto['valor_cantidad'] > 0) {
			$fila[] = array("valor"=>$concepto['valor_cantidad'], "class"=>"derecha");
		}
		else {
			$fila[] = array("valor"=>"");
		}

		$valor = $formato->format($concepto['valor'], array("before"=>"$ ", "places"=>2));
		if($concepto['concepto_tipo'] == "Remunerativo") {
			$fila[] = array("valor"=>$valor, "class"=>"derecha");
			$fila[] = array("valor"=>"");
			$fila[] = array("valor"=>"");
		}
		elseif($concepto['concepto_tipo'] == "Deduccion") {
			$fila[] = array("valor"=>"");
			$fila[] = array("valor"=>$valor, "class"=>"derecha");
			$fila[] = array("valor"=>"");
		}
		elseif($concepto['concepto_tipo'] == "No Remunerativo") {
			$fila[] = array("valor"=>"");
			$fila[] = array("valor"=>"");
			$fila[] = array("valor"=>$valor, "class"=>"derecha");
		}
		$cuerpo[] = $fila;
}

/**
* Totales
*/
$fila = null;
$fila[] = array("valor"=>"Totales", "class"=>"imitar_th_izquierda", "colspan"=>7);
$fila[] = array("valor"=>$formato->format($this->data['Liquidacion']['remunerativo'], array("before"=>"$ ", "places"=>2)), "class"=>"derecha");
$fila[] = array("valor"=>$formato->format($this->data['Liquidacion']['deduccion'], array("before"=>"$ ", "places"=>2)), "class"=>"derecha");
$fila[] = array("valor"=>$formato->format($this->data['Liquidacion']['no_remunerativo'], array("before"=>"$ ", "places"=>2)), "class"=>"derecha");
$cuerpo[] = $fila;

$fila = null;
$fila[] = array("valor"=>"Son " . $formato->numeroALetras($this->data['Liquidacion']['total_pesos']) . " en Pesos", "class"=>"imitar_th_izquierda", "colspan"=>9);
$fila[] = array("valor"=>"Pesos " . $formato->format($this->data['Liquidacion']['total_pesos'], array("before"=>"$ ", "places"=>2)), "class"=>"imitar_th_derecha");
$cuerpo[] = $fila;
$fila = null;
$fila[] = array("valor"=>"Son " . $formato->numeroALetras($this->data['Liquidacion']['total_beneficios']) . " pesos en Beneficios", "class"=>"imitar_th_izquierda", "colspan"=>9);
$fila[] = array("valor"=>"Beneficios " . $formato->format($this->data['Liquidacion']['total_beneficios'], array("before"=>"$ ", "places"=>2)), "class"=>"imitar_th_derecha");
$cuerpo[] = $fila;
$fila = null;
$fila[] = array("valor"=>"&nbsp;", "class"=>"imitar_th_izquierda", "colspan"=>9);
$fila[] = array("valor"=>$formato->format($this->data['Liquidacion']['total'], array("before"=>"$ ", "places"=>2)), "class"=>"imitar_th_derecha");
$cuerpo[] = $fila;	
	

$opcionesTabla =  array("tabla"=>
							array(	"eliminar"			=>false,
									"ordenEnEncabezados"=>false,
									'permisos'			=>false,
									"modificar"			=>false,
									"seleccionMultiple"	=>false,
									"mostrarEncabezados"=>false,
									"zebra"				=>false,
									"mostrarIds"		=>false,
									"omitirMensajeVacio"=>true));


$tabla = $formulario->tabla(am(array("cuerpo"=>$cuerpo), $opcionesTabla));

/**
* Pongo todo dentro de un div (index) y muestro el resultado.
*/
echo $formulario->bloque($formulario->bloque($tabla), array("div"=>array("id"=>"index", "class"=>"index")));



?>