<?php 
class ConceptoTestFixture extends CakeTestFixture {
    var $name = 'Concepto';
    
    var $fields = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'codigo' => array('type' => 'string', 'length' => 100, 'null' => false),
        'nombre' => array('type' => 'string', 'length' => 255, 'null' => false),
        'desde' => array('type' => 'date', 'null' => false),
        'hasta' => array('type' => 'date', 'null' => false)
    );
    var $records = array(
        array ('id' => 1, 'codigo' => 'concepto1', 'nombre' => 'Concepto 1', 'desde' => '2007-03-18', 'hasta' => '2007-03-18'),
        array ('id' => 2, 'codigo' => 'concepto2', 'nombre' => 'Concepto 2', 'desde' => '2007-03-18', 'hasta' => '2007-03-18'),
        array ('id' => 3, 'codigo' => 'concepto3', 'nombre' => 'Concepto 1', 'desde' => '2007-03-18', 'hasta' => '2007-03-18')
    );
}
?>