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
        $soapClient = new MSSoapClient("http://192.168.1.151/WsManager2/webService.asmx?wsdl",
            array("trace"=>1,
                    'uri'=>"http://localhost/servicioWeb"));        
        try {

            $request = new SoapVar(array('strGrupo' => '30-59083010-7', 'strCodigo' => '792001', 'strCUIT' => '30-54285183-6'), SOAP_ENC_OBJECT);
        
            d($soapClient->limites($request));
        } catch(SoapFault $soapFault) {
            echo "Request :<br>", $client->__getLastRequest(), "<br>";
            echo "Response :<br>", $client->__getLastResponse(), "<br>";
        }
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
        $soapClient = new
            SoapClient(
                router::url("/", true) . "service/wsdl/manager2", array('trace'=> 1)
            );
        try {
            $retorno['wsdl'] = $this->Soap->getWSDL("manager2", 'call');
            $retorno['retorno'] = $soapClient->empleadores(0);
            //$retorno['retorno'] = $soapClient->facturacion(71);
            //$retorno['retorno'] = $soapClient->pagos(347);
            //$retorno['retorno'] = $soapClient->anulaciones_pagos(0);
        }
        catch (SoapFault $soapFault) {
            echo "Request :<br>", $soapClient->__getLastRequest(), "<br>";
            echo "Response :<br>", $soapClient->__getLastResponse(), "<br>";
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