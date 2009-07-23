<?php
/**
 * Este archivo contiene toda la logica de negocio asociada a los feriados.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 524 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2009-05-19 16:41:08 -0300 (Tue, 19 May 2009) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

/**
 * La clase encapsula la logica de negocio asociada a los feriados.
 *
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
class FeriadosController extends AppController {


        function probar_consumir_min() {
        $soapClient = new SoapClient("http://webservices.mininterior.gov.ar/feriados/Service.svc?wsdl",
            array("trace"=>1,
                    'uri'=>"http://tempuri.org/"));        
        try {

            $anio = 2009;
$d1 = mktime(0, 0, 0, 1, 1, $anio);
$d2 = mktime(0, 0, 0, 12, 31, $anio);
$feriados = $soapClient->FeriadosEntreFechasAsXml(array('d1'=>$d1, 'd2'=>$d2));
d($feriados);
            
        } catch(SoapFault $soapFault) {
            echo "Request :<br>", $soapClient->__getLastRequest(), "<br>";
            echo "Response :<br>", $soapClient->__getLastResponse(), "<br>";
        }
    }

}
?>