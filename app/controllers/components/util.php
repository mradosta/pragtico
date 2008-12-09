<?php
/**
 * Util Component.
 * Tiene metodos genericos que uso en los controladores.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.controllers.components
 * @since			Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica generica.
 *
 * @package		pragtico
 * @subpackage	app.controllers.components
 */
class UtilComponent extends Object {

    var $controller;
    
    function startup(&$controller) {
        $this->controller = &$controller;
    }


/**
 * Dado un array proveniente desde la seleccion multiple desde una tabla index,
 * retorna un array con los ids seleccionados.
 *
 * @param array $data Array desde donde extraer los valores (se extraen desde las keys).
 * @return array Ids de los registros seleccionados, vacio en cualquier otro caso.
 */
	function extraerIds($data = array()) {
		if(!empty($data)) {
			$ids = array();
			foreach($data as $k=>$v) {
				if(($v === 1 || $v === "1" || $v === true || $v === "true")  && preg_match("/^id_([0-9]+$)/", $k, $matches)) {
					$ids[] = $matches[1];
				}
			}
			return $ids;
		}
		else {
			return array();
		}
	}


/**
* Dada una preferencia trae su valor
*/
	function traerPreferencia($preferencia) {
		if($this->controller->Session->check("__Usuario")) {
			$usuario = $this->controller->Session->read("__Usuario");
			return $usuario['Usuario']['preferencias'][$preferencia];
		}
	}
	

/**
* Dado un array (key => value), genera un string de la forma que lo necesita la funcion js autocomplete.
*/
	function generarAutocomplete($datos) {
		if(!empty($datos) && is_array($datos)) {
			foreach($datos as $k=>$v) {
				$data[] = $v . "|" . $k;
			}
			return implode("\n", $data);
		}
		else {
			return "";
		}
	}
	
	
/**
* Dado un array (key => value), genera un array para una tabla simple. Normalmente se usa para cargar una tabla
* FromTo via ajax, evitando generar un vista, que lo resuelva directamente el controlador y le pasa los datos al
* element.
*/
	function generarCuerpoTablaSimple($datos, $opciones = array()) {
		$opcionesDefault = array("encabezados"=>"Nombre", "class"=>"izquierda");
		$opciones = am($opcionesDefault, $opciones);
		$cuerpo = null;
		if(!empty($datos) && is_array($datos)) {
			foreach($datos as $k=>$v) {
				$fila = array();
				$fila[] = array("model"=>"Bar", "field"=>"id", "valor"=>$k);
				$fila[] = array("model"=>"Bar", "field"=>"foo", "valor"=>$v);
				$cuerpo[] = $fila;
			}
		}
		return array("cuerpo"=>$cuerpo, "encabezados"=>array($opciones['encabezados']), "class"=>$opciones['class']);
	}
	
	
/**
 * Creates an associative array using a $path1 as the path to build its keys, and optionally
 * $path2 as path to get the values. If $path2 is not specified, all values will be initialized
 * to null (useful for Set::merge). You can optionally group the values by what is obtained when
 * following the path specified in $groupPath.
 *
 * Si el array $data esta vacio, retorno un array vacio en lugar de un 
 *
 * @param array $data Array from where to extract keys and values
 * @param mixed $path1 As an array, or as a dot-separated string.
 * @param mixed $path2 As an array, or as a dot-separated string.
 * @param string $groupPath As an array, or as a dot-separated string.
 * @return array Combined array
 * @access public
 */

	function combine($data, $path1 = null, $path2 = null, $groupPath = null) {
		if(!empty($data)) {
			return Set::combine($data, $path1, $path2, $groupPath);
		}
		else {
			return array();
		}
	}
 	

/**
 * Formatea un valor de acuerdo a un formato.
 *
 * @param string $valor Un valor a formatear.
 * @param array $options Array que contiene el tipo de formato y/o sus opciones.
 * @return string Un string con el valor formateado de acuerdo a lo especificado.
 * @see FormatoHelper->format(...
 * @access public
 */
	function format($valor, $options = array()) {
		App::import("Helper", array("Number", "Time", "Formato"));
		$formato = new FormatoHelper();
		$formato->Time = new TimeHelper();
		$formato->Number = new NumberHelper();
		return $formato->format($valor, $options);
	}


/**
 * Formatea un valor de acuerdo a un formato.
 *
 * @param string $valor Un valor a formatear.
 * @param array $options Array que contiene el tipo de formato y/o sus opciones.
 * @return string Un string con el formato especificado.
 */
	function format_deprecated($valor, $options = array()) {
		if(is_string($options)) {
			$tmp = $options;
			$options = array();
			$options['type'] = $tmp;
		}

		$return = $valor;
		if(!isset($options['type'])) {
			$return = $this->Number->format($valor, $options);
		}
		else {
			switch($options['type']) {
				case "helper2db":
					$tmpFecha = explode("/", substr($valor,0,10));
					$mes=$tmpFecha[1];
					$dia=$tmpFecha[0];
					$anio=$tmpFecha[2];
					$return = $anio . "-" . $mes . "-" . $dia . substr($valor,10);
					break;
				case "db2helper":
					if($valor == "0000-00-00" || $valor == "0000-00-00 00:00:00") {
						$return = "";
					}
					else {
						$tmpFecha = explode("-", substr($valor,0,10));
						$mes=$tmpFecha[1];
						$dia=$tmpFecha[2];
						$anio=$tmpFecha[0];
						$return = $dia . "/" . $mes . "/" . $anio . substr($valor,10);
					}
					break;
				case "ano":
					$return = $this->Time->format("Y", substr($valor,0,10));
					break;
				case "mes":
					$return = $this->Time->format("m", substr($valor,0,10));
					break;
				case "dia":
					$return = $this->Time->format("d", substr($valor,0,10));
					break;
				case "ultimoDiaDelMes":
					$return = idate('d', mktime(0, 0, 0, ($options['mes'] + 1), 0, $options['ano']));
					break;
				case "mesEnLetras":
					$meses = $this->getMeses();
					$mes = $this->Time->format("m", substr($valor,0,10));
					/**
					* Me aseguro de que sea un numero, y no un string.
					*/
					$mes = $mes * 1;
					if(is_numeric($mes) && $mes >=1 && $mes <= 12) {
						$return = $meses[$mes];
					}
					break;
			}
		}
		return $return;
	}


/**
 * Trae el dia.
 * $options['fecha']
 */
	function traerDia_deprecated($options = null) {
		return date("d", strtotime($options['fecha']));
	}
	

/**
 * Trae el mes.
 * $options['fecha']
 */
	function traerMes_deprecated($options = null) {
		return date("m", strtotime($options['fecha']));
	}

	
/**
 * Trae el ano.
 * $options['fecha']
 */
	function traerAno_deprecated($options = null) {
		return date("Y", strtotime($options['fecha']));
	}
	
	
/**
 * Trae el ultimo dia del mes en numero.
 * $options['mes']
 * $options['ano']
 */
	function traerUltimoDiaDelMes_deprecated($options = null) {
		return  idate('d', mktime(0, 0, 0, ($options['mes'] + 1), 0, $options['ano']));
	}

/**
 * Trae una fecha.
 * $options['dia']
 * $options['mes']
 * $options['ano']
 */
	function traerFecha_deprecated($options = null) {
		return $options['ano'] . "-" . str_pad($options['mes'], 2, "0", STR_PAD_LEFT) . "-" . str_pad($options['dia'], 2, "0", STR_PAD_LEFT);
	}

/**
 * A partir de un periodo expresado en formato string, retorna un array de ano, mes y periodo.
 * El string (case-insensitive) puede ser:
 * 			- 2008031Q	Ano:2008, Mes:03, Periodo: 1Q
 * 			- 2007112Q	Ano:2007, Mes:11, Periodo: 2Q
 * 			- 200712M	Ano:2007, Mes:12, Periodo: M
 * false en caso de que venga vacio o no cumpla con el formato.
 */
	function traerPeriodo_deprecated($periodo) {
		if(!empty($periodo) && preg_match(VALID_PERIODO, strtoupper($periodo), $matches)) {

			$return['periodoCompleto'] = $matches[0];
			$return['ano'] = $matches[1];
			$return['mes'] = $matches[2];
			$return['periodo'] = $matches[3];

			$opciones = array(	"mes"	=> $periodo['mes'],
								"ano"	=> $periodo['ano']);

			if ($matches[3] == "1Q") {
				$opciones = am($opciones, array("dia"=>"01"));
				$fechaDesde = $this->traerFecha($opciones);
				$opciones = am($opciones, array("dia"=>"15"));
				$fechaHasta = $this->traerFecha($opciones);
			}
			elseif ($matches[3] == "2Q") {
				$opciones = am($opciones, array("dia"=>"16"));
				$fechaDesde = $this->traerFecha($opciones);
				$opciones = am($opciones, array("dia"=>$this->traerUltimoDiaDelMes($opciones)));
				$fechaHasta = $this->traerFecha($opciones);
			}
			elseif ($matches[3] == "M") {
				$opciones = am($opciones, array("dia"=>"01"));
				$fechaDesde = $this->traerFecha($opciones);
				$opciones = am($opciones, array("dia"=>$this->traerUltimoDiaDelMes($opciones)));
				$fechaHasta = $this->traerFecha($opciones);
			}
			$return['desde'] = $fechaDesde;
			$return['hasta'] = $fechaHasta;
			
			return $return;
		}
		return false;
	}

	
	
/**
 * Calcula la diferencia entre dos fechas.
 *
 * Las fechas deben estar en formato mysql
 * 	Formatos Admitidos de entrada:
 *			yyyy-mm-dd hh:mm:ss
 *			yyyy-mm-dd hh:mm
 *			yyyy-mm-dd
 *
 * @param string $fechaDesde La fecha desde la cual se tomara la diferencia.
 * @param string $fechaHasta La fecha hasta la cual se tomara la diferencia. Si no se pasa la fecha hasta,
 * se tomara la fecha actual como segunda fecha.
 *
 * @return mixed 	array con dias, horas, minutos y segundos en caso de que las fechas sean validas.
 * 					False en caso de que las fechas sean invalidas.
 * @access public
 */
	function dateDiff($fechaDesde, $fechaHasta = null) {
		App::import("Vendor", "dates", "pragmatia");
		$Dates = new Dates();
		return $Dates->dateDiff($fechaDesde, $fechaHasta);
	}
	

/**
 * Suma una cantidad de intervalo a una fecha.
 *
 * El intervalor puede ser y,q,m,w,d,h,n,s
 */
	function dateAdd_deprecated ($options = array()) {
		$default = array("intervalo"=>"d", "cantidad"=>"1", "fecha"=>date("Y-m-d"));
		$options = am($default, $options);
		$fecha = strtotime($options['fecha']);
		
		$ds = getdate($fecha);
		$h = $ds["hours"];
		$n = $ds["minutes"];
		$s = $ds["seconds"];
		$m = $ds["mon"];
		$d = $ds["mday"];
		$y = $ds["year"];

		$n = $options['cantidad'];
		switch ($options['intervalo']) {
			case "y":
				$y += $n;
				break;
			case "q":
				$m +=($n * 3);
				break;
			case "m":
				$m += $n;
				break;
			case "w":
				$d +=($n * 7);
				break;
			case "d":
				$d += $n;
				break;
			case "h":
				$h += $n;
				break;
			case "n":
				$n += $n;
				break;
			case "s":
				$s += $n;
				break;
		}
		return date("Y-m-d", mktime($h ,$n, $s,$m ,$d, $y));
	}
 

/**
 * Calcula la diferencia entre dos fechas.
 *
 * Las fechas deben estar en formato mysql (yyyy-mm-dd)
 * Si no se pasa la segunda fecha, se tomara la fecha actual como segunda fecha.
 * @return mixed 	array con dias, horas, minutos y segundos en caso de que las fechas sean validas.
 * 					False en caso de que las fechas sean invalidas.
 */
function diferenciaEntreFechas_deprecated($options = null) {

	$fecha1 = strtotime($options['desde']);
	if(empty($options['hasta'])) {
		$fecha2 = time();
	}
	else {
		$fecha2 = strtotime($options['hasta']);
	}
	
	if($fecha1 && $fecha2) {
		$diff = abs($fecha1-$fecha2);
		$daysDiff = floor($diff/60/60/24);
		$diff -= $daysDiff*60*60*24;
		$hrsDiff = floor($diff/60/60);
		$diff -= $hrsDiff*60*60;
		$minsDiff = floor($diff/60);
		$diff -= $minsDiff*60;
		$secsDiff = $diff;

		$diferencia=false;
		$diferencia['dias']=$daysDiff;
		$diferencia['horas']=$hrsDiff;
		$diferencia['minutos']=$minsDiff;
		$diferencia['segundos']=$secsDiff;
		return $diferencia;
	}
	else {
		return false;
	}
}

	function getFileName($name, $type) {
		return $this->__getName($name) . "." . $this->__getType($type);
	}
	

	function __getName($name) {
		$name = str_replace(" ", "_", $name);
		$name = strtolower($name);
		return Inflector::classify($name);
	}
	
	function __getType($tipo) {
		$tipos = array(
			"ai"=>"application/postscript",
			"aif"=>"audio/x-aiff",
			"aifc"=>"audio/x-aiff",
			"aiff"=>"audio/x-aiff",
			"asc"=>"text/plain",
			"atom"=>"application/atom+xml",
			"au"=>"audio/basic",
			"avi"=>"video/x-msvideo",
			"bcpio"=>"application/x-bcpio",
			"bin"=>"application/octet-stream",
			"bmp"=>"image/bmp",
			"cdf"=>"application/x-netcdf",
			"cgm"=>"image/cgm",
			"class"=>"application/octet-stream",
			"cpio"=>"application/x-cpio",
			"cpt"=>"application/mac-compactpro",
			"csh"=>"application/x-csh",
			"css"=>"text/css",
			"dcr"=>"application/x-director",
			"dif"=>"video/x-dv",
			"dir"=>"application/x-director",
			"djv"=>"image/vnd.djvu",
			"djvu"=>"image/vnd.djvu",
			"dll"=>"application/octet-stream",
			"dmg"=>"application/octet-stream",
			"dms"=>"application/octet-stream",
			"doc"=>"application/msword",
			"dtd"=>"application/xml-dtd",
			"dv"=>"video/x-dv",
			"dvi"=>"application/x-dvi",
			"dxr"=>"application/x-director",
			"eps"=>"application/postscript",
			"etx"=>"text/x-setext",
			"exe"=>"application/octet-stream",
			"ez"=>"application/andrew-inset",
			"gif"=>"image/gif",
			"gram"=>"application/srgs",
			"grxml"=>"application/srgs+xml",
			"gtar"=>"application/x-gtar",
			"hdf"=>"application/x-hdf",
			"hqx"=>"application/mac-binhex40",
			"htm"=>"text/html",
			"html"=>"text/html",
			"ice"=>"x-conference/x-cooltalk",
			"ico"=>"image/x-icon",
			"ics"=>"text/calendar",
			"ief"=>"image/ief",
			"ifb"=>"text/calendar",
			"iges"=>"model/iges",
			"igs"=>"model/iges",
			"jnlp"=>"application/x-java-jnlp-file",
			"jp2"=>"image/jp2",
			"jpe"=>"image/jpeg",
			"jpeg"=>"image/jpeg",
			"jpg"=>"image/jpeg",
			"js"=>"application/x-javascript",
			"kar"=>"audio/midi",
			"latex"=>"application/x-latex",
			"lha"=>"application/octet-stream",
			"lzh"=>"application/octet-stream",
			"m3u"=>"audio/x-mpegurl",
			"m4a"=>"audio/mp4a-latm",
			"m4b"=>"audio/mp4a-latm",
			"m4p"=>"audio/mp4a-latm",
			"m4u"=>"video/vnd.mpegurl",
			"m4v"=>"video/x-m4v",
			"mac"=>"image/x-macpaint",
			"man"=>"application/x-troff-man",
			"mathml"=>"application/mathml+xml",
			"me"=>"application/x-troff-me",
			"mesh"=>"model/mesh",
			"mid"=>"audio/midi",
			"midi"=>"audio/midi",
			"mif"=>"application/vnd.mif",
			"mov"=>"video/quicktime",
			"movie"=>"video/x-sgi-movie",
			"mp2"=>"audio/mpeg",
			"mp3"=>"audio/mpeg",
			"mp4"=>"video/mp4",
			"mpe"=>"video/mpeg",
			"mpeg"=>"video/mpeg",
			"mpg"=>"video/mpeg",
			"mpga"=>"audio/mpeg",
			"ms"=>"application/x-troff-ms",
			"msh"=>"model/mesh",
			"mxu"=>"video/vnd.mpegurl",
			"nc"=>"application/x-netcdf",
			"oda"=>"application/oda",
			"ogg"=>"application/ogg",
			"pbm"=>"image/x-portable-bitmap",
			"pct"=>"image/pict",
			"pdb"=>"chemical/x-pdb",
			"pdf"=>"application/pdf",
			"pgm"=>"image/x-portable-graymap",
			"pgn"=>"application/x-chess-pgn",
			"pic"=>"image/pict",
			"pict"=>"image/pict",
			"png"=>"image/png",
			"pnm"=>"image/x-portable-anymap",
			"pnt"=>"image/x-macpaint",
			"pntg"=>"image/x-macpaint",
			"ppm"=>"image/x-portable-pixmap",
			"ppt"=>"application/vnd.ms-powerpoint",
			"ps"=>"application/postscript",
			"qt"=>"video/quicktime",
			"qti"=>"image/x-quicktime",
			"qtif"=>"image/x-quicktime",
			"ra"=>"audio/x-pn-realaudio",
			"ram"=>"audio/x-pn-realaudio",
			"ras"=>"image/x-cmu-raster",
			"rdf"=>"application/rdf+xml",
			"rgb"=>"image/x-rgb",
			"rm"=>"application/vnd.rn-realmedia",
			"roff"=>"application/x-troff",
			"rtf"=>"application/rtf",
			"rtx"=>"text/richtext",
			"sgm"=>"text/sgml",
			"sgml"=>"text/sgml",
			"sh"=>"application/x-sh",
			"shar"=>"application/x-shar",
			"silo"=>"model/mesh",
			"sit"=>"application/x-stuffit",
			"skd"=>"application/x-koan",
			"skm"=>"application/x-koan",
			"skp"=>"application/x-koan",
			"skt"=>"application/x-koan",
			"smi"=>"application/smil",
			"smil"=>"application/smil",
			"snd"=>"audio/basic",
			"so"=>"application/octet-stream",
			"spl"=>"application/x-futuresplash",
			"src"=>"application/x-wais-source",
			"sv4cpio"=>"application/x-sv4cpio",
			"sv4crc"=>"application/x-sv4crc",
			"svg"=>"image/svg+xml",
			"swf"=>"application/x-shockwave-flash",
			"t"=>"application/x-troff",
			"tar"=>"application/x-tar",
			"tcl"=>"application/x-tcl",
			"tex"=>"application/x-tex",
			"texi"=>"application/x-texinfo",
			"texinfo"=>"application/x-texinfo",
			"tif"=>"image/tiff",
			"tiff"=>"image/tiff",
			"tr"=>"application/x-troff",
			"tsv"=>"text/tab-separated-values",
			"txt"=>"text/plain",
			"ustar"=>"application/x-ustar",
			"vcd"=>"application/x-cdlink",
			"vrml"=>"model/vrml",
			"vxml"=>"application/voicexml+xml",
			"wav"=>"audio/x-wav",
			"wbmp"=>"image/vnd.wap.wbmp",
			"wbmxl"=>"application/vnd.wap.wbxml",
			"wml"=>"text/vnd.wap.wml",
			"wmlc"=>"application/vnd.wap.wmlc",
			"wmls"=>"text/vnd.wap.wmlscript",
			"wmlsc"=>"application/vnd.wap.wmlscriptc",
			"wrl"=>"model/vrml",
			"xbm"=>"image/x-xbitmap",
			"xhtml"=>"application/xhtml+xml",
			"xls"=>"application/vnd.ms-excel",
			"xml"=>"application/xml",
			"xpm"=>"image/x-xpixmap",
			"xsl"=>"application/xml",
			"xslt"=>"application/xslt+xml",
			"xul"=>"application/vnd.mozilla.xul+xml",
			"xwd"=>"image/x-xwindowdump",
			"xyz"=>"chemical/x-xyz",
			"zip"=>"application/zip");

		if(preg_match("/.+\/.+/", $tipo)) {
			$tipos = array_flip($tipos);
			if(!empty($tipos[$tipo])) {
				return $tipos[$tipo];
			}
		}
	}
}
?>