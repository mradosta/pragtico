<?php

/**
 * Creo la miga de pan.
 */
$appForm->addCrumb($this->name, 
		array('controller' 	=> $this->params['controller'], 
			  'action' 		=> 'index'));

if ($this->action === 'add') {
	$appForm->addCrumb(__('New', true));
} else {
	$appForm->addCrumb(__('Edit', true));
	$count = count($this->data);
	if($count === 1 && isset($this->data[0])) {
		$appForm->addCrumb('<h5>' . $this->data[0][Inflector::classify($this->params['controller'])]['bread_crumb_text'] . '</h5>');
	} else {
		$appForm->addCrumb(sprintf(__('%s Records', true), $count));
	}
}


/**
 * Me aseguro de que este definida la varaible.
 */
if(!isset($accionesExtra)) {
	$accionesExtra = array();
}


/**
 * Creo los campos de ingreso de datos.
 */
$bloques[] = $fieldset;


/**
 * Si me pasaron unbloque adicional, lo agrego.
 */
if(!empty($bloqueAdicional)) {
	$bloques[] = $bloqueAdicional;
}


/**
 * Pongo las acciones.
 */
$bloques[] = $this->element('add/acciones', array('accionesExtra' => $accionesExtra));


/**
 * Pongo alguna variable especifica del Form que me llega desde el controller.
 */
if (!empty($variablesForm)) {
	foreach($variablesForm as $variable => $valor) {
		$bloques[] = $appForm->input('Form.' . $variable,
										array(	'type'	=> 'hidden',
												'value'	=> $valor));
	}
}


/**
 * Creo el formulario y pongo todo dentro.
 */
if (!isset($opcionesForm['action'])) {
	$opcionesForm['action'] = 'save';
}
$form = $appForm->form($bloques, $opcionesForm);


/**
* Pongo todo dentro de un div (add) y muestro el resultado.
*/
echo $appForm->bloque($form, array('div' => array('class' => 'add')));
?>