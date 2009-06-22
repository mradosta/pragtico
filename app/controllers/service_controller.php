<?php
/**
 * Este archivo contiene toda la logica de negocio asociada al manejo de WebServices.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.controllers
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * La clase encapsula la logica de negocio asociada a los rubros de las empresas.
 *
 * @package     pragtico
 * @subpackage  app.controllers
 */
 
/**
* 
*
* OJO que se cache el wsdl en el /tmp, si agrego una funcion nueva, para que la vea debo borrar el wsdl.
* Puedo tocar el php.ini para que no se comporte asi.
* Revisar esto: ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
*/
class MSSoapClient extends SoapClient {

    function __doRequest($request, $location, $action, $version) {
        $namespace = "http://localhost/servicioWeb";

        $request = preg_replace('/<ns1:(\w+)/', '<$1 xmlns="'.$namespace.'"', $request, 1);
        $request = preg_replace('/<ns1:(\w+)/', '<$1', $request);
        $request = str_replace(array('/ns1:', 'xmlns:ns1="'.$namespace.'"'), array('/', ''), $request);

        // parent call
        return parent::__doRequest($request, $location, $action, $version);
    }
}


class ServiceController extends AppController {

    var $uses = array('Manager2Service');
    var $components = array('Soap');



	function probar_consumir() {
        /*
		$client = new MSSoapClient("http://upsoft.dyndns.org/manager2Saldos/webservice.asmx?WSDL",
			array("trace"=>1,
					'uri'=>"http://localhost/servicioWeb"));
        */

        $client = new MSSoapClient("http://192.168.1.151/WsManager2/webService.asmx?wsdl",
            array("trace"=>1,
                    'uri'=>"http://localhost/servicioWeb"));        
		try {

			//strGrupo
			//strCodigo
			//strCUIT
			//$request = new SoapVar(array('strCUIT'=>"30641966645"), SOAP_ENC_OBJECT);
			//$request = new SoapVar(array('strCUIT' => "20131933992"), SOAP_ENC_OBJECT);
            $request = new SoapVar(array('strGrupo' => '3', 'strCodigo' => '2', 'strCUIT' => '30-80919358-5'), SOAP_ENC_OBJECT);
		
			echo print_r($result->limitesResult);
		} catch(SoapFault $soapFault) {
			//d($soapFault);
			//echo $client->__getLastRequest();
			echo "Request :<br>", $client->__getLastRequest(), "<br>";
			echo "Response :<br>", $client->__getLastResponse(), "<br>";
		}
		//echo print_r($result->limites);
		die;
	
	/*
		$soapClient = new
			SoapClient(
				"http://upsoft.dyndns.org/manager2Saldos/webservice.asmx?WSDL", array('trace'=>1, 'soap_version'=>SOAP_1_2)
			);
		try {
			$retorno['wsdl'] = "";
			$retorno['retorno'] = $soapClient->limites("30641966645");
		}
		catch (SoapFault $soapFault) {
			//var_dump($soapFault);
			echo "Request :<br>", $soapClient->__getLastRequest(), "<br>";
			echo "Response :<br>", $soapClient->__getLastResponse(), "<br>";
			die();
			//d("X");
		}
	*/
		$this->set("pruebas", $retorno);
		$this->render("probar_cliente");
	}

	
	function xml2array() {
		/**
		* CArgo desde un XML a un array.
		*/
		Uses('Xml');
		$myArray = Set::reverse(new Xml("<xxx><yxyx za='x' zax='x'>xxxx</yxyx><yxyx>zaza</yxyx></xxx>"));
		d($myArray);
	}

	function probar_cliente() {
		//d(utf8_decode("o'connor &#xE1;&#xE9;&#xED;&#xF3;&#xFA;"));
		//d($this->Manager2Service->facturacion(0));
		//d($this->Manager2Service->pagos(100));
		//d($this->Manager2Service->anulaciones_pagos(0));
		//d(inflector::variable("empleador_id"));
		//$this->autoRender = false;
		$soapClient = new
			SoapClient(
				router::url("/", true) . "service/wsdl/manager2", array('trace'=> 1)
			);
		try {
			$retorno['wsdl'] = $this->Soap->getWSDL("manager2", 'call');
			//$retorno['retorno'] = $soapClient->empleadores(0);
			$retorno['retorno'] = $soapClient->facturacion(0);
			//$retorno['retorno'] = $soapClient->pagos(1);
			//d($soapClient->facturacion(1) . ". Se ejecuto OK");
			//d($soapClient->hola("MARTIN") . ". Se ejecuto OK");
			//d($soapClient->divide(10, 2) . ". Se ejecuto OK");
		}
		catch (SoapFault $soapFault) {
			//var_dump($soapFault);
			echo "Request :<br>", $soapClient->__getLastRequest(), "<br>";
			echo "Response :<br>", $soapClient->__getLastResponse(), "<br>";
			d("X");
		}
		$this->set("pruebas", $retorno);
	}


/**
 * Se encarga de administrar cada llamada SOAP.
 */
    function call($model) {
        $this->autoRender = false;
        $this->Soap->handle($model, 'wsdl');
    }


    
    
/**
 * Genera un WSDL para el model en cuestion.
 */
    function wsdl($model) {
        $this->layout = "wsdl";
        $this->set("data", $this->Soap->getWSDL($model, 'call'));
    }
}
?>