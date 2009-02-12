<?php
/**
 * Este archivo contiene un model generico (fake) para los casos de pruebas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2009, Pragmatia
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.tests.models
 * @since           Pragtico v 1.0.0
 * @version         $Revision: 54 $
 * @modifiedby      $LastChangedBy: mradosta $
 * @lastmodified    $Date: 2008-10-23 23:14:28 -0300 (Thu, 23 Oct 2008) $
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */

App::import('Component', array('Session', 'Util')); 
App::import('Model', array('Liquidacion', 'Relacion', 'Trabajador'));
 
/**
 * La clase para un para un caso de prueba generico (fake).
 *
 * @package app.tests
 * @subpackage app.tests.models
 */
class LiquidacionTest extends Liquidacion {

	
/**
 * Indico en nombre del model.
 *
 * @var string
 * @access public
 */
	var $name = 'Liquidacion';
	
	
/**
 * Indico que use la conexion de prueba (test).
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'test';

}



class LiquidacionTestCase extends CakeTestCase {

    var $fixturesx = array('trabajador', 'liquidacion', 'relacion', 'localidad', 'provincia', 'siniestrado', 'condicion', 'obras_social', 'empleador', 'actividad',
                         'area', 'suss', 'sucursal', 'banco', 'recibo', 'recibos_concepto', 'concepto', 'coeficiente', 'convenio', 'convenios_categoria', 
                         'convenios_categorias_historico', 'situacion', 'modalidad', 'ausencias_motivo', 'ausencia', 'ausencias_seguimiento',
                         'ropa', 'ropas_detalle', 'hora', 'relaciones_concepto','pago', 'pagos_forma', 'cuenta', 'descuento', 'descuentos_detalle',
                         'convenios_informacion', 'informacion', 'convenios_concepto', 'empleadores_concepto', 'rubro', 'empleadores_rubro', 'empleadores_coeficiente',
                         'factura', 'facturas_detalle', 'liquidaciones_detalle', 'liquidaciones_auxiliar', 'liquidaciones_error', 'variable',
                         'usuario', 'rol', 'grupo', 'roles_usuario', 'grupos_usuario', 'grupos_parametro', 'roles_accion', 'roles_menu', 'menu', 'accion', 'controlador',
                         'preferencia', 'preferencias_usuario', 'preferencias_valor');
    

/**
 * __detachBehaviors method
 *
 * @access private
 * @return void
 */
    function __detachBehaviors(&$model) {
        foreach ($model->Behaviors->attached() as $behavior) {
            $model->Behaviors->detach($behavior);
        }
    }


/**
 * startTest method
 *
 * @access public
 * @return void
 */
    function startTest() {
    }
    

    
    function testGetReceiptSac() {
        $this->Liquidacion = ClassRegistry::init('Liquidacion');
        $this->Liquidacion->Relacion = ClassRegistry::init('Relacion');
        $this->__detachBehaviors($this->Liquidacion->Relacion);
        $this->Liquidacion->Relacion->Behaviors->attach('Containable');

        $relationships = $this->Liquidacion->Relacion->find('all', array('contain' => array('Trabajador', 'Empleador'), 'limit' => 1));

        /** Complete Half*/
        $relationships[0]['Relacion']['ingreso'] = '2004-05-02';
        $relationships[0]['Relacion']['egreso'] = '2010-01-01';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2007;
        $options['january'] = 1538.50;
        $options['february'] = 2300.23;
        $options['march'] = 1450.85;
        $options['april'] = 900.55;
        $options['may'] = 2301.66;
        $options['june'] = 1256.25;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 1150.83);
        
        /** Complete Half (leap year)*/
        $relationships[0]['Relacion']['ingreso'] = '2004-05-02';
        $relationships[0]['Relacion']['egreso'] = '2010-01-01';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2008;
        $options['january'] = 1538.50;
        $options['february'] = 2300.23;
        $options['march'] = 1450.85;
        $options['april'] = 900.55;
        $options['may'] = 2301.66;
        $options['june'] = 1256.25;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 1150.83);


        /** Start 2007-07-04*/
        $relationships[0]['Relacion']['ingreso'] = '2007-04-07';
        $relationships[0]['Relacion']['egreso'] = '2010-01-01';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2007;
        $options['april'] = 900.55;
        $options['may'] = 2301.66;
        $options['june'] = 1256.25;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 540.45);
        
        
        /** Start 2008-07-04 (leap year)*/
        $relationships[0]['Relacion']['ingreso'] = '2008-04-07';
        $relationships[0]['Relacion']['egreso'] = '2010-01-01';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2008;
        $options['april'] = 900.55;
        $options['may'] = 2301.66;
        $options['june'] = 1256.25;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 537.48);
        
        
        /** End 2008-03-23 */
        $relationships[0]['Relacion']['ingreso'] = '2005-04-08';
        $relationships[0]['Relacion']['egreso'] = '2007-03-23';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2007;
        $options['january'] = 1538.50;
        $options['february'] = 2300.23;
        $options['march'] = 1450.85;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 521.05);
        

        /** End 2008-03-23 (leap year)*/
        $relationships[0]['Relacion']['ingreso'] = '2005-04-08';
        $relationships[0]['Relacion']['egreso'] = '2008-03-23';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2008;
        $options['january'] = 1538.50;
        $options['february'] = 2300.23;
        $options['march'] = 1450.85;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 524.50);
        
        
        /** Start 2007-02-16, End 2007-05-28 */
        $relationships[0]['Relacion']['ingreso'] = '2007-02-16';
        $relationships[0]['Relacion']['egreso'] = '2007-05-28';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2007;
        $options['february'] = 2300.23;
        $options['march'] = 1450.85;
        $options['april'] = 900.55;
        $options['may'] = 2301.66;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 648.53);
        

        /** Start 2008-02-16, End 2008-05-28 (leap year) */
        $relationships[0]['Relacion']['ingreso'] = '2008-02-16';
        $relationships[0]['Relacion']['egreso'] = '2008-05-28';
        $options = null;
        $options['period'] = '1';
        $options['year'] = 2008;
        $options['february'] = 2300.23;
        $options['march'] = 1450.85;
        $options['april'] = 900.55;
        $options['may'] = 2301.66;
        $result = $this->Liquidacion->getReceipt('sac', $relationships, $options);
        $this->assertEqual($result, 651.29);
    }

}

?>