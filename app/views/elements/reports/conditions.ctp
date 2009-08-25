<?php
    
if (!empty($groups)) {
    if (isset($groups[User::get('preferencias/grupos_seleccionados')])) {
        $conditions['Condicion.Bar-grupo_id'] = array('options' => $groups, 'empty' => false, 'value' => User::get('preferencias/grupos_seleccionados'));
    } else {
        $conditions['Condicion.Bar-grupo_id'] = array('options' => $groups, 'empty' => false);
    }
}
$conditions['Condicion.Bar-empleador_id'] = array( 'lov' => array(
        'controller'        => 'empleadores',
        'seleccionMultiple' => false,
        'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
        
if (!empty($aditionalConditions)) {
    $conditions = array_merge($conditions, $aditionalConditions);
}
$conditions['Condicion.Bar-formato'] = array('type' => 'radio', 'options' => array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'), 'value' => 'Excel2007');

$fieldsets[] = array('campos' => $conditions);

$fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => $options['title'], 'imagen' => 'reports.gif')));

$accionesExtra['opciones'] = array('acciones' => array());
$botonesExtra[] = 'limpiar';
$botonesExtra[] = $appForm->submit('Generar', array('title' => $options['title'], 'onclick' => 'document.getElementById("accion").value="generar"'));

echo $this->element('index/index', array(
                    'opcionesTabla' => array('tabla' => array('omitirMensajeVacio' => true)),
                    'botonesExtra'  => array('opciones' => array('botones' => $botonesExtra)),
                    'accionesExtra' => $accionesExtra,
                    'opcionesForm'  => array('action' => $this->action),
                    'condiciones'   => $fieldset,
                    'cuerpo'        => null));
?>