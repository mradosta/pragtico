<?php

$groups = User::getUserGroups();
$defaultGroup = User::get('/Usuario/preferencias/grupo_default_id');
if (count($groups) > 1 && isset($groups[$defaultGroup])) {
    $conditions['Condicion.Bar-grupo_id'] = array(
        'options'   => $groups,
        'empty'     => false,
        'value'     => $defaultGroup);
} else {
    $conditions['Condicion.Bar-grupo_id'] = array('options' => $groups, 'empty' => false);
}

if (!empty($aditionalConditions)) {
    $conditions = array_merge($conditions, $aditionalConditions);
}
$conditions['Condicion.Bar-file_format'] = array('type' => 'radio', 'options' => array('Excel5' => 'Excel', 'Excel2007' => 'Excel 2007'), 'value' => 'Excel2007');

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