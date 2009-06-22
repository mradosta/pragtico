<?php
/** Crea el hidden que lleva la accion, esta accion puede ser buscar o limpiar.*/
$out[] = $appForm->input('Formulario.accion', array('type' => 'hidden', 'id' => 'accion', 'value' => 'buscar'));

/**
 * Si tengo el parametro de una seleccion multiple (cuando habro una lov), lo pongo en un hidden para no perderlo.
 if (!empty($this->params['named'])) {
 	foreach($this->params['named'] as $k=>$v) {
 		if($k != "accion" && $k != "layout") {
 			$out[] = $appForm->input("Formulario." . $k, array("type"=>"hidden", "value"=>$v));
 		}
 	}
 }
 */


if ($this->params['isAjax']) {

	/**
	* Creo los botones de los buscadores.
	* El boton de Buscar y el de Limpiar.
	*/
	$out[] = $appForm->button(__('Clear', true), array('class' => 'buscador_ajax', 'title' => 'Limpiar los criterios de busqueda'));
	$out[] = $appForm->button(__('Search', true), array('class' => 'buscador_ajax', 'title' => 'Realizar la busqueda'));
	
	$out[] = $appForm->codeBlock("
jQuery(document).ready(function($) {

        jQuery.bindMultipleCheckBoxManipulation('#lov');
    
        /** When #opened_lov_options not empty, because I'm on a lov */
        if (jQuery('#opened_lov_options').val() != '') {
            /**Hides everything but select option */
            jQuery('td.acciones > a', jQuery('#simplemodal-container')).hide();
            jQuery('td.acciones > img:not(\'.seleccionar\')', jQuery('#simplemodal-container')).hide();

            var params = jQuery.makeObject(jQuery('#opened_lov_options').val());
            if (params['seleccionMultiple'] == 0) {
                jQuery('input.selection_lov', jQuery('#simplemodal-container')).hide();
            }

            
            jQuery('.seleccionar').click(function() {

                var toReturn = params['camposRetorno'].split('|');
    
                /** Marks the checkbox associated as checked */
                jQuery('.selection_lov', jQuery(this).parent()).attr('checked', true);

                var selectedData = new Array();
                var selectedIds = new Array();
                jQuery('.selection_lov').filter(':checked').each(
                    function() {
                        var row = jQuery(this).parent().parent();
                        var returnRowData = new Array();
                        jQuery('td:not(\'.acciones\')', row).each(
                            function() {
                                if (jQuery.inArray(jQuery(this).attr('axis'), toReturn) >= 0) {
                                    returnRowData.push(jQuery(this).html());
                                }
                            }
                        );
                        if (returnRowData.length > 0) {
                            selectedData.push(jQuery.vsprintf(params['mask'], returnRowData));
                        }

                        selectedIds.push(jQuery(this).parent().parent().attr('charoff'));
                    }
                );
                jQuery('#' + params['retornarA'] + '__').val(selectedData.join('\\n'));
                jQuery('#' + params['retornarA']).val(selectedIds.join('**||**'));
                jQuery('a.modalCloseImg').trigger('click');
            });
            
        }
            

        /** Do ajax submit. */
        jQuery('.buscador_ajax', jQuery('#lov')).click(function(){
            /** Set action (clean or search) */
            var accion = jQuery(this).val().toLowerCase();
            jQuery('#accion', jQuery('#lov')).val(accion);
            /** Seteo las opciones para hacer el request ajax. */
            var url = jQuery('#form', jQuery('#lov')).attr('action');
            var options = {
                target:     '#lov',
                type:       'POST',
                url:        url
            };
            jQuery('#form', jQuery('#lov')).ajaxSubmit(options);
        });
    
    
        /** Finds wath is already selected and mark it */
        var data = jQuery('#' + params['retornarA']).val().split('**||**')
        jQuery('tr', jQuery('#lov')).each(
            function() {
                if (jQuery.inArray(jQuery(this).attr('charoff'), data) >= 0) {
                    jQuery('input.selection_lov', jQuery(this)).attr('checked', true);
                }
            }
        );
    
    });");
	
} else {
	/**
	* hidden para no perder el layout en el que estoy ni si es permitido seleccion Multiple o no.
	*/
	//$out[] = $appForm->input("Formulario.layout", array("type"=>"hidden", "value"=>$this->layout));
	
	$limpiar = $appForm->button(__("Clear", true), array("class"=>"limpiar", "onclick"=>"document.getElementById('accion').value='limpiar';form.action='" . Router::url(array("controller" => $this->params['controller'], "action" => $opcionesForm['action'])) . "';form.submit();"));
	$buscar = $appForm->submit(__("Search", true), array("onclick"=>"document.getElementById('accion').value='buscar'"));
	
	if(isset($botonesExtra['opciones']['botones'])) {
		foreach($botonesExtra['opciones']['botones'] as $v) {
			switch ($v) {
				case "limpiar":
					$out[] = $limpiar;
					break;
				case "buscar":
					$out[] = $buscar;
					break;
				default:
					$out[] = $v;
					break;
			}
		}
	} else {
		/**
		* Creo los botones de los buscadores.
		* El boton de Buscar y el de Limpiar.
		*/
		$out[] = $limpiar;
		$out[] = $buscar;
	}
}

/**
* Creo un bloque con los botones y agrego el div clear antes para cerrar la caja redondeada.
*/
$out[] = $appForm->tag('div', '', array('class' => 'clear'));


echo $appForm->tag('div', $out, array('class' => 'buscadores'));

?>