<?php
/**
 * Helper que me facilita el uso de ajax vis jQuery.
 *
 * Me permite colocar metodos js genericos que uso en las vistas.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views.helpers
 * @since           Pragtico v 1.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
/**
 * Clase que contiene el helper para el uso de ajax mediante jQuery.
 *
 * @package     pragtico
 * @subpackage  app.views.helpers
 */
class AjaxHelper extends AppHelper {

/**
 * Los helpers que utilizare.
 *
 * @var arraya
 * @access public.
 */
    var $helpers = array("Html", "Javascript", "AppForm");
    
    
/**
 * Crea un link Ajax.
 *
 */
	function link($title, $href, $options = array(), $confirm = null, $escapeTitle = true) {
        $href = $this->Html->url($href);
		//$update = $options['update'];

		$clickAjax = "jQuery.ajax({
  			type: 'GET',
  			async: false,
  			url: '".$href."',
			success: function(html){
				jQuery('#" . $options['update'] . "').html(html);
			}
		});";
			
        if (!isset($options['onclick'])) {
			$options['onclick'] = "";
		}
		
		$options['onclick'] = $clickAjax . $options['onclick'] . "return false;";
		
		unset($options['update']);
		$att = $this->_parseAttributes($options);
		
        $return = "<a href='" . $href . "' " . $att . ">" . $title . "</a>";
        return $return;
    }

    
    function observeField($element, $options){
        $event = $options["event"];
        $update = $options["update"];
        $url = $this->Html->url($options["url"]);


        $code = $options["loading"].'
        $.ajax({
   type: "POST",
   url: "'.$url.'",
   data: $("'.$element.'").serialize(),
   success: function(data){
        $("'.$update.'").html(data);
       '.$options["complete"].'
   }
 });
    ';

        return $this->_jsBlock($this->_addReady("\$(\"$element\").$event(function(){ $code })"));
    }


/**
 * Escribo los codigo js que debo repetir varias veces, solo para no repetir el codigo en las vistas.
 * tipo detalle: agrega un detalle para facilitar la carga de un master detail.
 */
function jsPredefinido($options = array()) {

	$js['tablasFromTo']['view'] = "

		/**
		* Pasa todos los elementos desde la tabla izquierda a la tabla derecha.
		*/
		var agregar_todos = function() {

			jQuery('table.izquierda tbody').find('tr').each(
				function() {
					jQuery(this).find('td').each(
						function() {
							if (jQuery(this).attr('class') == 'agregar' || jQuery(this).attr('class') == 'oculto') {
								jQuery(this).hide();
							}
							if (jQuery(this).attr('class') == 'acciones') {
								jQuery(this).show();
							}
						}
					)
					jQuery(this).createPrepend('td', {class : 'quitar'}, '');
					jQuery('table.derecha tbody').append(this);
				}
			);
			jQuery('td.quitar').bind('click', quitar);
		};

		/**
		* Pasa todos los elementos desde la tabla derecha a la tabla izquierda.
		*/
		var quitar_todos = function() {

			jQuery('table.derecha tbody').find('tr').each(
				function() {
					jQuery(this).find('td').each(
						function() {
							if (jQuery(this).attr('class') == 'quitar' || jQuery(this).attr('class') == 'acciones') {
								jQuery(this).hide();
							}
						}
					)
					jQuery(this).createAppend('td', {class : 'agregar'}, '');
					jQuery('table.izquierda tbody').append(this);
				}
			);
			jQuery('td.agregar').bind('click', agregar);
		};

		/**
		* Pasa la fila seleccionada desde la tabla izquierda a la tabla derecha.
		*/
		var agregar = function() {
			jQuery(this).parent().find('td').each (
				function() {
					if (jQuery(this).attr('class') == 'agregar' || jQuery(this).attr('class') == 'acciones' || jQuery(this).attr('class') == 'oculto') {
						jQuery(this).hide();
					}
					if (jQuery(this).attr('class') == 'acciones') {
						jQuery(this).show();
					}
				}
			);
			jQuery(this).parent().createPrepend('td', {class : 'quitar'}, '');
			jQuery('table.derecha tbody').append(jQuery(this).parent());
			jQuery('td.quitar').bind('click', quitar);
		};

		/**
		* Pasa la fila seleccionada desde la tabla derecha a la tabla izquierda.
		*/
		var quitar = function() {
			jQuery(this).parent().find('td').each (
				function() {
					if (jQuery(this).attr('class') == 'acciones' || jQuery(this).attr('class') == 'quitar') {
						jQuery(this).hide();
					}
				}
			);
			jQuery(this).parent().createAppend('td', {class : 'agregar'}, '');
			jQuery('table.izquierda tbody').append(jQuery(this).parent());
			jQuery('td.agregar').bind('click', agregar);
		};


		/**
		* Serializa de la forma campo1|campo2*||*campo1|campo2 el contenido de la tabla derecha.
		*/
		var serializar = function() {
			var tds = Array();
			var trs = Array();
			var tmp = '';
			jQuery('table.derecha tr').each(
				function(index, domEl) {
					jQuery(domEl).find('td').each(
						function() {
							if (!jQuery(this).is('td.quitar_todos') && !jQuery(this).is('td.quitar') && !jQuery(this).is('td.acciones')) {
								tds.push(jQuery(this).html());
							}
						}
					);
					tmp = tds.join('|');
					if (tmp.length > 0) {
						trs.push(tmp);
					}
					tds = Array();
				}
			);
			var valores = trs.join('*||*');
			jQuery('#form').createAppend('input', {type : 'hidden', name : 'data[Form][valores_derecha]', value : valores}, '');
		}

		/**
		* Transforma dos tablas comunes en tablas FromTo.
		*/
		var transformarTablaIzquierda = function () {
			jQuery('table.izquierda tbody').find('tr').each(function() {
				jQuery(this).createAppend('td', {class : 'agregar'}, '');
			});

			var encabezados = jQuery('table.izquierda thead tr').clone();
			jQuery('table.izquierda thead').createPrepend('tr', {class : 'fila_datos'},
					['td', {class : 'agregar_todos', colspan : '10'}, 'No Asignados']);

			jQuery('table.izquierda thead').find('tr').each(function() {
					jQuery(this).find('th').each(function() {
						if (jQuery(this).html() == 'Acciones') {
							jQuery(this).hide();
						}
					});
					jQuery(this).find('th:last').attr('colspan', '10');
			});
		}

		var transformarTablaDerecha = function () {

			//jQuery('table.derecha thead').find('tr').each(function() {jQuery(this).createPrepend('td', {}, 'x');});
			jQuery('table.derecha thead').find('tr th:first').attr('colspan', '10');
			jQuery('table.derecha tbody').find('tr').each(function() {jQuery(this).createPrepend('td', {class : 'quitar'}, '');});

			var encabezados = jQuery('table.izquierda thead').clone();

			/*
			if (jQuery('table.derecha thead').find('tr').each(function(){}).size() == 0) {
				encabezados.createPrepend('td', {}, 'x');
				jQuery('table.derecha thead').append(encabezados);
			}
			*/
			
			jQuery('table.derecha thead').createPrepend('tr', {class : 'fila_datos'},
					['td', {class : 'quitar_todos', colspan : '10'}, 'Asignados']);

			//jQuery(table.derecha thead tr).find('th:first').hide();
		}


		/**
		* Hago el bind de los eventos click.
		*/
		var bindearTablaIzquierda = function () {
			jQuery('td.agregar').bind('click', agregar);
			jQuery('td.agregar_todos').bind('click', agregar_todos);
		}

		var bindearTablaDerecha = function () {
			jQuery('td.quitar').bind('click', quitar);
			jQuery('td.quitar_todos').bind('click', quitar_todos);
		}

		var inicializar = function () {
			transformarTablaIzquierda();
			transformarTablaDerecha();
			bindearTablaIzquierda();
			bindearTablaDerecha();
			jQuery('input:submit').bind('click', serializar);
		}
	";
	$js['tablasFromTo']['ready'] = "inicializar();";



	$quitar = "";
	if (!empty($options['quitar']) && $options['quitar'] === true) {
		$quitar = "botonQuitar(this, id);";
	}

	$js['detalle']['view'] = "
		var botonQuitar = function (fieldSet, id) {
			var imagenQuitar = '<div class=\"quitarDetalle\"><a class=\"link_boton_rojo\" href=\"javascript:void(0);\" alt\"Quitar\" id=\"linkQuitar_' + id + '\">Quitar</a></div>';
			jQuery(fieldSet).find('legend > span').append(imagenQuitar);
			
			jQuery('#linkQuitar_' + id).bind('click', function(){
				/**
				* Solo puedo quitar mientras tenga por lo menos un detail.
				*/
				if (jQuery('fieldSet.detail').length > 1) {
					jQuery('#' + jQuery(this).attr('id').replace('linkQuitar_', 'fieldset_')).remove();
					jQuery(this).remove();
				}
				else {
					alert('Debe dejar por lo menos un registro de detalle.');
				}
			});
		}
		";

	$js['detalle']['view'] .= "
		var detalle = function() {
			var id = 0;
			jQuery('.fieldset_multiple fieldSet.detail').each(
				function() {
					jQuery(this).attr('id', 'fieldset_' + id);
					" . $quitar . "
					id++;
				}
			)";

	if (!empty($options['agregar']) && $options['agregar'] === true) {
		$js['detalle']['view'] .= "
			var img = \"<img alt='Agregar' title='Agregar' src='".Router::url("/") . "img/add.gif' />\";
			var i = 0;
			jQuery('.fieldset_multiple').each(
				function() {
					jQuery(this).attr('id', 'fieldsetMultiple_' + i);
					//jQuery(this).createAppend('div', {class : 'agregarDetalle'}, ['a', {class: 'link_boton', id : 'agregarDetalle_' + i, href : '#agregarDetalle_' + i}, 'Agregar']);
					jQuery(this).append('<div class=\"agregarDetalle\"><a class=\"link_boton\" id=\"agregarDetalle_' + i + '\" href=\"#agregarDetalle_' + i + '\">Agregar</a></div>');
					i++;
				}
			);
		}";
	}

	$js['detalle']['view'] .= "
		var agregar = function() {

			var fsM = jQuery(this).attr('id').replace('agregarDetalle_', 'fieldsetMultiple_');
			var id = jQuery('#' + fsM + ' fieldSet.detail').length;

			/**
			* Clono el fieldset y saco el campo hidden del id (para que cake haga un insert, sino hara un update
			* y al estar agregando, estoy seguro de que es uno nuevo.
			*/
			var fieldset = jQuery('#' + fsM + ' fieldset.detail:last').clone(true);
			fieldset.attr('id', 'fieldset_' + id);

			fieldset.find('a:first').attr('id', 'linkQuitar_' + id);
			fieldset.find('input:hidden').attr('value', '');

			/**
			* Si hay un control de tipo fecha, el campo al que retorna debo alterarle el id, sino no lo encontrara al correcto.
			*/
			fieldset.find('a').each(function(){
				this.href = this.href.replace(/(javascript:NewCal\(\')([a-zA-Z]+)(\',\'dd\/mm\/yyyy\'\))/, '$1$2_' + id + '$3');
			});

			/**
			* Si hay un control de tipo radio, la label de este hara referencia al cual fue clonado, debo cambiarla por el
			* nuevo id que tendra el control.
			*/
			fieldset.find('div.radio label.radio_label').each(function(){
				jQuery(this).attr('for', jQuery(this).attr('for') + '_' + id);
			});

			fieldset.find('input, select, textarea').each(function(){
				
				/**
				* Debo cambiar el subindice del nombre (name) de cada control,
				* para que no se me repita y se me pisen.
				* puede ser un solo registro o un edit multiple.
				*/
				this.id = this.id + '_' + id;
				var indice = parseInt(this.name.replace(/(^data\[[a-zA-Z]+\])\[([0-9]+)\](\[[a-zA-Z_]+\])/, '$2')) + 1;
				if (isNaN(indice)) {
					indice = parseInt(this.name.replace(/(^data\[[0-9]+\]\[[a-zA-Z]+\])\[([0-9]+)\](\[[a-zA-Z_]+\])/, '$2')) + 1;
					this.name = this.name.replace(/(^data\[[0-9]+\]\[[a-zA-Z]+\])\[([0-9]+)\](\[[a-zA-Z_]+\])/, '$1\[' + indice + '\]$3')
				}
				else {
					this.name = this.name.replace(/(^data\[[a-zA-Z]+\])\[([0-9]+)\](\[[a-zA-Z_]+\])/, '$1\[' + indice + '\]$3')
				}

			});

			/**
			* Agrego el nuevo fieldset y al boton agregar lo corro al final de este.
			*/
			jQuery('#' + fsM).append(fieldset);
			jQuery('#' + fsM).append(jQuery(this).parent());
		}";
		
	$js['detalle']['ready'] = "detalle();";
	$js['detalle']['ready'] .= "jQuery('a.link_boton').bind('click', agregar);";
	
	$js['busqueda']['view'] = "

		var busqueda = function() {
			/**
			* Hace el toggle entre autoincremental.
			*/
			jQuery('.busqueda_autoincremental').bind('click',
				function() {
					jQuery(this).hide();
					var tmp = this.id.split('_');
					if (tmp[1] == 'on') {
						jQuery('#autoincremental_off_' + tmp[2]).show();
					}
					else {
						jQuery('#autoincremental_on_' + tmp[2]).show();
					}
					jQuery('#' + tmp[2]).focus();
				}
			);

			/**
			* Hace el toggle entre que empiece/contenga.
			*/
			jQuery('.busqueda_tipo').bind('click',
				function() {
					jQuery(this).hide();
					var tmp = this.id.split('_');
					if (tmp[0] == 'empiece') {
						jQuery('#contenga_' + tmp[1]).show();
					}
					else {
						jQuery('#empiece_' + tmp[1]).show();
					}
					jQuery('#' + tmp[1]).focus();
				}
			);
		";

		if ($this->traerPreferencia("busqueda_tipo") == "empiece") {
			$js['busqueda']['view'] .= "jQuery('.busqueda_tipo').each(
				function() {
					var tmp = this.id.split('_');
					if (tmp[0] == 'contenga') {
						jQuery(this).hide();
					}
				});";
		}

		if ($this->traerPreferencia("busqueda_autoincremental") == "activado") {
			$js['busqueda']['view'] .= "jQuery('.busqueda_autoincremental').each(
				function() {
					var tmp = this.id.split('_');
					if (tmp[1] == 'off') {
						jQuery(this).hide();
					}
				});";
		}

	/**
	* Evito que el browser haga autocomplete (en molesto). No lo hago como un atributo html porque no valida xhtml.
	*/
	$js['busqueda']['view'] .= "jQuery('.autocomplete').attr('autocomplete', 'off');";
	$js['busqueda']['view'] .= "}";
	$js['busqueda']['ready'] = "busqueda();";
		
		if (!empty($js[$options['tipo']]['ready'])) {
			$this->AppForm->addScript($js[$options['tipo']]['ready'], "ready");
		}
		if (!empty($js[$options['tipo']]['view'])) {
			$this->AppForm->addScript($js[$options['tipo']]['view'], "view");
		}
	}

    function observeForm($element, $options){
        $event = $options["event"];
        $update = $options["update"];
        $url = $this->Html->url($options["url"]);


        $code = '$("'.$element.'").ajaxSubmit({
            target:        \''.$update.'\',
            beforeSubmit:  function(){'.$options["loading"].'},
            success:       function(){'.$options["complete"].'}
            }
        );';

        return $this->_jsBlock($this->_addReady("\$(\"$element\").$event(function(){ $code return false; })"));
    }

    function _addReady($content){
        return "\$(function(){ $content } );";
    }

    function test(){
        echo $this->_jsBlock($this->_addReady("alert(\"Jax Helper has been installed and ready to use!\");"));
    }

    
}
?>