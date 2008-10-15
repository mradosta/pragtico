<?
/*
********************************************************************************************************
defino la estructura a nivel formato, margenes de celdas, margenes de titulos, tamaño de letras, etc.
********************************************************************************************************
*/
/*
 * Indica el desplazamiento en y desde el y inicial hasta el encabezado.
 */
define("ENCABEZADO", 520);
/**
 * Indica el desplazamiento en x de un recibo al otro en la hoja apaizada.
 */
define("DESPLAZAMIENTO_X",  414);

/*
* defino constantes de formatos
*/

define("SIZE_LETRA_TITULOS", 7);
define("SIZE_LETRA_ENCABEZADOS", 15);
define("SIZE_LETRA_DATOS", 10);

define("LETRA_TITULOS", "tahoma");
define("LETRA_ENCABEZADOS", "tahoma");
define("LETRA_DATOS", "tahoma");


define("ALTURA_CELDA_ENCABEZADO", 15);
define("ALTURA_CELDA_TITULOS", 8);
define("ALTURA_CELDA_DATOS", 10);

/*
define("MARGEN_IZQUIERDA_CELDA_TITULOS", 5);
define("MARGEN_DERECHA_CELDA_TITULOS", 5);
define("MARGEN_ABAJO_CELDA_TITULOS", 5);
define("MARGEN_ARRIBA_CELDA_TITULOS", 5);
*/

define("INTERLINEADO", 10);
define("ANCHO_RECIBO", 408);
define("ALTO_RECIBO", 600);


/*
* indica si se se va a imprimir un recibo preimpreso o no 
* 0 se imprimen todas los titulos
* 1 no se imprimen los titulos y solo se imprime el texto
*/


$f = 0;
$c = 0;
$celda[$f][$c]['preimpreso'] = true;
$celda[$f][$c]['ancho'] = 60;
$celda[$f][$c]['alto'] = 12;
//$celda[$f][$c]['contenido']['tipo'] = "imagen";
//$celda[$f][$c]['contenido']['valor'] = "logo.jpg";
$celda[$f][$c]['contenido']['tipo'] = "texto";
$celda[$f][$c]['contenido']['valor'] = "logo.jpg";
$celda[$f][$c]['contenido']['size_letra'] = SIZE_LETRA_ENCABEZADOS;

$c++;
$celda[$f][$c]['preimpreso'] = true;
$celda[$f][$c]['ancho'] = 40;
$celda[$f][$c]['alto'] = 12;
//$celda[$f][$c]['contenido']['tipo'] = "texto_multilinea";
//$celda[$f][$c]['contenido']['valor'] = array("25 de mayo", "5000-cordoba");
$celda[$f][$c]['contenido']['tipo'] = "texto";
$celda[$f][$c]['contenido']['valor'] = "25 de mayo";
$celda[$f][$c]['contenido']['size_letra'] = SIZE_LETRA_ENCABEZADOS;

$f++;
$c = 0;
$celda[$f][$c]['preimpreso'] = false;
$celda[$f][$c]['ancho'] = 100;
$celda[$f][$c]['alto'] = 5;
$celda[$f][$c]['contenido']['tipo'] = "texto";
$celda[$f][$c]['contenido']['valor'] = "USUARIO";
$celda[$f][$c]['contenido']['size_letra'] = SIZE_LETRA_DATOS;
$celda[$f][$c]['borde']['superior'] = true;
$celda[$f][$c]['margen']['superior'] = "2";
$celda[$f][$c]['borde']['inferior'] = true;
$celda[$f][$c]['margen']['inferior'] = "2";

$f++;
$c = 0;
$celda[$f][$c]['preimpreso'] = false;
$celda[$f][$c]['ancho'] = 30;
$celda[$f][$c]['alto'] = 2;
$celda[$f][$c]['contenido']['tipo'] = "texto";
$celda[$f][$c]['contenido']['valor'] = "Periodo Abonadox";
$celda[$f][$c]['contenido']['size_letra'] = SIZE_LETRA_TITULOS;

$c++;
$celda[$f][$c]['preimpreso'] = false;
$celda[$f][$c]['ancho'] = 40;
$celda[$f][$c]['alto'] = 2;
$celda[$f][$c]['contenido']['tipo'] = "texto";
$celda[$f][$c]['contenido']['valor'] = "Fecha de Pago";
$celda[$f][$c]['contenido']['size_letra'] = SIZE_LETRA_TITULOS;

escribirPdf(normalizar($celda), $pdf);

function escribirPdf($filas = array(), &$pdf) {

	$pdf->Cezpdf("a4", "landscape");
	$pdf->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");

	$yA = ALTO_RECIBO-10; // Me lleva el acumulado en Y.
	foreach($filas as $f=>$celdas) {
		$xA = 50; // Me lleva el acumulado en X.
		foreach($celdas as $c=>$celda) {
			$y = ALTO_RECIBO * $celda['alto'] / 100;
			$x = ANCHO_RECIBO * $celda['ancho'] / 100;


			/*
			* veo los bordes si tiene
			*/ 
			foreach($celda['borde'] as $ubicacion=>$valor) {
				if($valor) {
					switch($ubicacion) {
						case "superior":
							$pdf->line($xA, $yA + $celda['alto'] + $celda['margen'][$ubicacion], $x, $yA + $celda['alto'] + $celda['margen'][$ubicacion]);
						break;
						case "inferior":
							$pdf->line($xA, $yA - $celda['margen'][$ubicacion], $x, $yA - $celda['margen'][$ubicacion]);
						break;
					}
				}
			}

			switch($celda['contenido']['tipo']) {
				case "texto":
					$pdf->addText($xA, $yA, $celda['contenido']['size_letra'], "y: ".$y." yA: ".$yA. " - ".$celda['contenido']['valor']);
					break;
			}
			$xA += $x;
		}
		$yA -= $y; // El acumuador de Y decrementa porque el pdf empieza desde abajo
	}
	/**
	Muestra el documento pdf.
	*/
	$pdf->ezStream();

}

/**
* TODO: 
* Verificar porcentuales
*/
function normalizar($filas = array()) {


	$default['ancho'] = 100; 

	$defaultMargen['izquierdo'] = 0;
	$defaultMargen['derecho'] = 0;
	$defaultMargen['superior'] = 0;
	$defaultMargen['inferior'] = 0; 

	$defaultBorde['izquierdo'] = false;
	$defaultBorde['derecho'] = false;
	$defaultBorde['superior'] = false;
	$defaultBorde['inferior'] = false; 

	$defaultContenido['tipo'] = "texto"; 
	$defaultContenido['valor'] = ""; 
	$defaultContenido['size_letra'] = "10"; 

	foreach($filas as $f=>$celdas) {
		foreach($celdas as $c=>$celda) {
			/**
			* Valores por defecto del borde.
			*/
			if(empty($celda['borde'])) {
				$celda['borde'] = array();
			}
			$celda['borde'] = am($defaultBorde, $celda['borde']);

			/**
			* Valores por defecto del contenido.
			*/
			if(empty($celda['contenido'])) {
				$celda['contenido'] = array();
			}
			$celda['contenido'] = am($defaultContenido, $celda['contenido']);

			/**
			* Valores por defecto del margen.
			*/
			if(empty($celda['margen'])) {
				$celda['margen'] = array();
			}
			$celda['margen'] = am($defaultMargen, $celda['margen']);

			$celda = am($default, $celda);

			$return[$f][$c] = $celda;
		}
	}
	//d($return);
	return $return;
}
?>