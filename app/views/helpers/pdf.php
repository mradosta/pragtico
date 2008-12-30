<?php
/**
 * Este es un helper CakePHP que sirve para crear archivos pdf.
 *
 * @author MRadosta <mradosta AT pragmatia.com>
 * @from 11/07/2006
 */

 /** pdf-php */
 App::import('Vendor', "class", true, array(APP . "vendors" . DS . "pdf-php"), "class.ezpdf.php");

class PdfHelper extends Cezpdf {

	var $helpers = array();
	
	function PdfHelper(){
		$this->Cezpdf();
	}

	function ezInicio($opciones = array()) {
		$default = array("encabezado"=>true, "font"=>true, "usuario" => "");
		$opciones = am($default, $opciones);
		if ($opciones['font'] === true) {
			$this->selectFont(APP . "vendors" . DS . "pdf-php" . DS . "fonts" . DS . "Helvetica.afm");
		}
		if ($opciones['encabezado'] === true) {
			$this->ezEncabezado($opciones);
		}
	}
	
	// ------------------------------------------------------------------------------
	// 2006-11-20: Radosta Martin (mradosta@pragmatia.com)
	// Setea el encabezado
	function ezEncabezado($opciones=array()) {
		$default = array("usuario" => "");
		$opciones = am($default, $opciones);
	
		$this->ezStartPageNumbers(550,28,8,'','pagina {PAGENUM} de {TOTALPAGENUM}',1);
		$this->ezSetMargins(80,70,20,20);

		/**
		* Pongo el encabezado
		*/
		$all = $this->openObject();
		$this->saveState();
		$img = ImageCreatefromjpeg(APP . '/webroot/img/logo.jpg');
		$this->addImage($img,20,780,135,45);
		$this->addText(500, 800, 7, $opciones['usuario']);
		$this->addText(500, 780, 7, date('d/m/Y'));
		$this->addText(500, 790, 7, date('H:i'));
		$this->setLineStyle(1);
		$this->line(20,775,578,775);//linea arriba
		$this->line(20,40,578,40);//linea abajo
		$this->restoreState();
		$this->closeObject();
		$this->addObject($all,'all');
	}
}