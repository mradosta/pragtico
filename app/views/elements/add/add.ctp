<?php

/**
 * Creo la miga de pan.
 */
$appForm->addCrumb($this->name, 
		array('controller' 	=> $this->params['controller'], 
			  'action' 		=> 'index'));

if ($this->action === 'add') {
	$appForm->addCrumb(__('New', true));
}
else {
	$appForm->addCrumb(__('Edit', true));
	if (!empty($miga)) {
		
		if (is_string($miga)) {
			$tmp = $miga;
			$miga = null;
			$miga['content'][] = $tmp;
		}
		
		if (isset($this->data[0])) {
			$data = $this->data[0];
		} elseif (isset($this->data)) {
			$data = $this->data;
		}
		
		if (!empty($data)) {
			foreach ($miga['content'] as $contents) {
				
				$c = explode('.', $contents);
				if (count($c) === 3) {
					$text = $data[$c[0]][$c[1]][$c[2]];
				}
				elseif (count($c) === 2) {
					$text = $data[$c[0]][$c[1]];
				}
				elseif (count($c) === 1) {
					$text = $data[$c[0]];
				}
				$texts[] = $text;
			}
			if (!empty($miga['format'])) {
				$breadCrumbText = vsprintf($miga['format'], $texts);
			} else {
				$breadCrumbText = implode(' ', $texts);
			}
			
			$count = count($this->data);
			if($count === 1) {
				$appForm->addCrumb("<h5>" . $breadCrumbText . "</h5>");
			}
			else {
				$appForm->addCrumb(sprintf(__('%s Records', true), $count));
			}
		}
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
$bloques[] = $this->element("add/acciones", array("accionesExtra"=>$accionesExtra));


/**
 * Pongo alguna variable especifica del Form que me llega desde el controller.
 */
if(!empty($variablesForm)) {
	foreach($variablesForm as $variable=>$valor) {
		$bloques[] = $appForm->input("Form." . $variable, 
										array(	"type"	=> "hidden", 
												"value"	=> $valor));
	}
}


/**
 * Creo el formulario y pongo todo dentro.
 */
if(!isset($opcionesForm['action'])) {
	$opcionesForm['action'] = "save";
}
$opcionesForm['action'] = "save";
$form = $appForm->form($bloques, $opcionesForm);


/**
* Pongo todo dentro de un div (add) y muestro el resultado.
*/
echo $appForm->bloque($form, array('div' => array("class"=>"add")));
?>