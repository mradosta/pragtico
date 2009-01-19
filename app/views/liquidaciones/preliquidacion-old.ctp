<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.views
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
foreach($relaciones as $relacion) {
	/**
	* Datos del empleador.
	*/
	$fila = null;
	$fila[] = array("valor"=>"Datos del Empleador", "class"=>"imitar_th_izquierda", "colspan"=>10);
	$cuerpo[] = $fila;

	$fila = null;
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Nombre: </span>" . $relacion['Empleador']['nombre'], "class"=>"izquierda", "colspan"=>10);
	$cuerpo[] = $fila;
	
	$fila = null;
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Direccion: </span>" . $relacion['Empleador']['direccion'], "class"=>"izquierda", "colspan"=>10);
	$cuerpo[] = $fila;

	$fila = null;
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Cuit: </span>" . $relacion['Empleador']['cuit'], "class"=>"izquierda", "colspan"=>10);
	$cuerpo[] = $fila;

	/**
	* Datos del trabajador.
	*/
	$fila = null;
	$fila[] = array("valor"=>"Datos del Trabajador", "class"=>"imitar_th_izquierda", "colspan"=>10);
	$cuerpo[] = $fila;

	$fila = null;
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Nombre: </span>" . $relacion['Trabajador']['apellido'] . ", " . $relacion['Trabajador']['nombre'], "class"=>"izquierda", "colspan"=>2);
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Cuil: </span>" . $relacion['Trabajador']['cuil'], "class"=>"izquierda", "colspan"=>3);
	$cuerpo[] = $fila;

	$fila = null;
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Puesto/Categoria: </span>" . $relacion['ConveniosCategoria']['nombre'], "class"=>"izquierda", "colspan"=>2);
	$fila[] = array("valor"=>"<span class='label_liquidacion'>Ingreso: </span>" . $formato->format($relacion['Relacion']['ingreso'], "db2helper"), "class"=>"izquierda", "colspan"=>3);
	$cuerpo[] = $fila;


	/**
	* Conceptos.
	*/
	$fila = null;
	$fila[] = array("valor"=>"Liquidacion de Haberes", "class"=>"imitar_th_izquierda", "colspan"=>10);
	$cuerpo[] = $fila;
	$fila = null;
	$fila[] = array("valor"=>"Concepto", "class"=>"imitar_th_izquierda");
	$fila[] = array("valor"=>"Cantidad", "class"=>"imitar_th_izquierda");
	$fila[] = array("valor"=>"Remunarativo", "class"=>"imitar_th_izquierda");
	$fila[] = array("valor"=>"Remunarativo", "class"=>"imitar_th_izquierda");
	$fila[] = array("valor"=>"No Remunarativo", "class"=>"imitar_th_izquierda");
	$cuerpo[] = $fila;


	$totales['Remunerativo'] = 0;
	$totales['Deduccion'] = 0;
	$totales['No Remunerativo'] = 0;
	
	foreach($relacion['liquidacion'] as $concepto) {
		if($concepto['imprimir'] == "Si") {
			$fila = null;
			$fila[] = array("valor"=>$concepto['nombre']);
			$fila[] = array("valor"=>"");
			
			$valor = $formato->format($concepto['valor'], array("before"=>"$ ", "places"=>2));
			if($concepto['tipo'] == "Remunerativo") {
				$fila[] = array("valor"=>$valor, "class"=>"derecha");
				$fila[] = array("valor"=>"");
				$fila[] = array("valor"=>"");
			}
			elseif($concepto['tipo'] == "Deduccion") {
				$fila[] = array("valor"=>"");
				$fila[] = array("valor"=>$valor, "class"=>"derecha");
				$fila[] = array("valor"=>"");
			}
			elseif($concepto['tipo'] == "No Remunerativo") {
				$fila[] = array("valor"=>"");
				$fila[] = array("valor"=>"");
				$fila[] = array("valor"=>$valor, "class"=>"derecha");
			}
			$totales[$concepto['tipo']] += $concepto['valor'];
			$cuerpo[] = $fila;
		}
	}
	
	/**
	* Totales
	*/
	$fila = null;
	$fila[] = array("valor"=>"Totales", "class"=>"imitar_th_izquierda", "colspan"=>2);
	$fila[] = array("valor"=>$formato->format($totales['Remunerativo'], array("before"=>"$ ", "places"=>2)), "class"=>"derecha");
	$fila[] = array("valor"=>$formato->format($totales['Deduccion'], array("before"=>"$ ", "places"=>2)), "class"=>"derecha");
	$fila[] = array("valor"=>$formato->format($totales['No Remunerativo'], array("before"=>"$ ", "places"=>2)), "class"=>"derecha");
	$cuerpo[] = $fila;
	
	$fila = null;
	$totalAPagar = $totales['Remunerativo'] + $totales['No Remunerativo'] - $totales['Deduccion'];
	$fila[] = array("valor"=>"Son pesos " . $formato->numeroALetras($formato->format($totalAPagar, array("thousands"=>"", "decimals"=>".", "before"=>null, "places"=>2))), "class"=>"imitar_th_izquierda", "colspan"=>4);
	$fila[] = array("valor"=>"A pagar " . $formato->format($totalAPagar, array("before"=>"$ ", "places"=>2)), "class"=>"imitar_th_derecha");
	$cuerpo[] = $fila;
	
}

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


$tabla = $appForm->tabla(am(array('cuerpo' => $cuerpo), $opcionesTabla));

/**
* Pongo todo dentro de un div (index) y muestro el resultado.
*/
echo $appForm->bloque($appForm->bloque($tabla), array("div"=>array("id"=>"index", "class"=>"index")));



?>