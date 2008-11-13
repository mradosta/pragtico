<?php
/**
 * Este archivo contiene un caso de prueba para el proceso de liquidaciones.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link			http://www.pragmatia.com
 * @package			pragtico
 * @subpackage		app.tests.controllers
 * @since			Pragtico v 1.0.0
 * @version			$Revision: 54 $
 * @modifiedby		$LastChangedBy: mradosta $
 * @lastmodified	$Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author      	Martin Radosta <mradosta@pragmatia.com>
 */
 
App::import('Component', 'Session');
App::import("Model", array("Liquidacion", "Usuario", "Variable"));
App::import("Controller", "Liquidaciones");
 
/**
 * La clase para un para un controlador de prueba generico (fake).
 *
 * @package app.tests
 * @subpackage app.tests.controllers
 */
class LiquidacionesTestController extends CakeTestCase {
   
/**
 * Fixtures asociados a este caso de prueba.
 *
 * @var array
 * @access public
 */	
	var $fixtures = array('trabajador', 'liquidacion', 'relacion', 'localidad', 'provincia', 'siniestrado', 'condicion', 'obras_social', 'empleador', 'actividad',
						 'area', 'suss', 'sucursal', 'banco', 'recibo', 'recibos_concepto', 'concepto', 'coeficiente', 'convenio', 'convenios_categoria', 
						 'convenios_categorias_historico', 'situacion', 'modalidad', 'ausencias_motivo', 'ausencia', 'ausencias_seguimiento',
						 'ropa', 'ropas_detalle', 'hora', 'relaciones_concepto','pago', 'pagos_forma', 'cuenta', 'descuento', 'descuentos_detalle',
						 'convenios_informacion', 'informacion', 'convenios_concepto', 'empleadores_concepto', 'rubro', 'empleadores_rubro', 'empleadores_coeficiente',
						 'factura', 'facturas_detalle', 'liquidaciones_detalle', 'liquidaciones_auxiliar', 'liquidaciones_error', 'variable',
						 'usuario', 'rol', 'grupo', 'roles_usuario', 'grupos_usuario', 'grupos_parametro', 'roles_accion', 'roles_menu', 'menu', 'accion', 'controlador',
						 'preferencia', 'preferencias_usuario', 'preferencias_valor');
	
	
/**
 * Sobreescribo el metodo endController. Se ejecuta inmediatamente despues de testAction.
 * Cuando finaliza un caso de prueba, elimina (drop) las tablas creadas por los fixtures. Antes de que esto ocurra, 
 * realizo las queries necesarias y guardo los resultados obtenidos que utilizare para comparar contra los resultados esperados.
 *
 * @var array
 * @access public
 */	
	function endController(&$controller, $params = array()) {
		$this->__LiquidacionesDetalle = $controller->Liquidacion->LiquidacionesDetalle->find("all");
		
		/**
		* A Liquidacion (el primer elemento del vector), 
		* lo he creado mediante un fixture, si no lo saco, lo intentara dropear nuevamente y dara error.
		*/
		unset($this->_actionFixtures[0]);
		return parent::endController($controller, $params);
	}
	
	
/**
 * El caso de prueba para la preliquidacion.
 *
 * - Normal
 * - Mensual
 * - Empleado de Comercio (Auxiliar Especializado A)
 * - Conceptos:
 * 		- sueldo_basico
 * 		- antiguedad
 * 		- presentismo
 * 		- jubilacion
 * 		- ley_19032
 * 		- agec
 * 		- obra_social
*/	
	function testPreliquidacionNormal() { 
		
		$this->__login();
		
		/**
		* Preparo la data.
		*/
		$data['Formulario']['accion'] = "buscar";
		$data['Extras']['Liquidacion-periodo'] = "200808M";
		$data['Extras']['Liquidacion-tipo'] = "normal";
		$data['Condicion']['Relacion-id'] = "1";
		
		$result = $this->testAction('/liquidaciones/preliquidar', 
								array('connection'	=> 'test', 
										'method' 	=> 'post',
		  								'fixturize' => true, 
		  								'return'	=> 'vars',
		  								'data' 		=> $data));
		d($this->__LiquidacionesDetalle);
		d($result);
		$this->assertEqual(0, count($result['registros'][0]['LiquidacionesError']));
		$this->assertEqual(22, $this->cantidadDetalles);
	}
	
	
	
/**
 * El caso de prueba para la preliquidacion Normal por Hora.
 */	
	function xtestPreliquidacionNormalPorHora() { 
		
		$this->__login();
		
		/**
		* Preparo la data.
		*/
		$data['Formulario']['accion'] = "buscar";
		$data['Extras']['Liquidacion-periodo'] = "2008092Q";
		$data['Condicion']['Relacion-id'] = "2";
		$data['Extras']['Liquidacion-tipo'] = "normal";
		
		$result = $this->testAction('/liquidaciones/preliquidar', 
								array('connection'	=> 'test', 
										'method' 	=> 'post',
		  								'fixturize' => true, 
		  								'return'	=> 'vars',
		  								'data' 		=> $data));
		
		$this->assertEqual(0, count($result['registros'][0]['LiquidacionesError']));
		$this->assertEqual(23, $this->cantidadDetalles);
	}
	
	
	
/**
* Simula un login como administrador.
*/
	function __login() { 
		$Usuario = new Usuario();
		$condiciones['nombre'] = "root";
		$condiciones['clave'] = "x";	
		$usuario = $Usuario->verificarLogin($condiciones);
		$session = &new SessionComponent();
		$session->write('__Usuario', $usuario);
	}
	
} 

?>