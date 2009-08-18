<?php
    
if (!empty($groups)) {
    if (isset($groups[User::get('preferencias/grupos_seleccionados')])) {
        $condiciones['Condicion.Bar-grupo_id'] = array('options' => $groups, 'empty' => false, 'value' => User::get('preferencias/grupos_seleccionados'));
    } else {
        $condiciones['Condicion.Bar-grupo_id'] = array('options' => $groups, 'empty' => false);
    }
}
$condiciones['Condicion.Bar-empleador_id'] = array( 'lov' => array(
        'controller'        => 'empleadores',
        'seleccionMultiple' => false,
        'camposRetorno'     => array('Empleador.cuit', 'Empleador.nombre')));
if (!empty($options['periodo_largo'])) {
    $condiciones['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => $options['periodo_largo']);
} else {
    $condiciones['Condicion.Bar-periodo_largo'] = array('label' => 'Periodo', 'type' => 'periodo', 'periodo' => array('1Q', '2Q', 'M', '1S', '2S'));
}
$condiciones['Condicion.Bar-convenio_id'] = array( 'lov' => array(
        'controller'        => 'convenios',
        'seleccionMultiple' => true,
        'camposRetorno'     => array('Convenio.numero', 'Convenio.nombre')));

$condiciones['Condicion.Bar-formato'] = array('type' => 'radio', 'options' => array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'), 'value' => 'Excel2007');

$fieldsets[] = array('campos' => $condiciones);
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