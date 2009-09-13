//defino la variable para que este accesible desde todos lados (como una global)
//var timer;

jQuery(document).ready(function($) {

    /** Creates the menu */
    jQuery(".menu").accordion({
        header: "a.header",
        active: parseInt(jQuery.cookie("menu_cookie"))
    });
    
    /** Binds click event to select/deselect/invert classes for manipulating checkboxes */
    jQuery.bindMultipleCheckBoxManipulation();
    
    
    /** Binds click to expand textarea control */
    jQuery(".expand_text_area").click(function() {
        var textarea = "#" + jQuery("textarea", jQuery(this).parent()).attr("id");
        if (jQuery(this).hasClass("colapse_text_area")) {
            jQuery(textarea).parent().css("width", "365px");
            jQuery(textarea).css("width", "196px").css("background-image", "url(" + jQuery.url("css/img/textarea.gif") + ")");
            jQuery(this).removeClass("colapse_text_area");
            jQuery(this).addClass("expand_text_area");
        } else {
            jQuery(textarea).parent().css("width", "720px");
            jQuery(textarea).css("width", "565px").css("background-image", "url(" + jQuery.url("css/img/wide_textarea.gif") + ")");
            jQuery(this).addClass("colapse_text_area");
            jQuery(this).removeClass("expand_text_area");
        }
    });


    /** Checks cookie to decide to hide conditions frameset */
    if (jQuery.cookie("conditionsFrameCookie") == "false") {
        jQuery(".conditions_frame").hide();
        jQuery("#hideConditions > img").attr("src", jQuery.url("img/") + "sin_pinchar.gif");
    }
    
    
    /** Binds click to Show / Hide conditions */
    jQuery("#hideConditions").bind("click",
        function() {
            jQuery(".conditions_frame").toggle();
            if (jQuery(".conditions_frame").is(":visible")) {
                jQuery.cookie("conditionsFrameCookie", "true");
                jQuery("#hideConditions > img").attr("src", jQuery.url("img/") + "pinchado.gif");
            } else {
                jQuery.cookie("conditionsFrameCookie", "false");
                jQuery("#hideConditions > img").attr("src", jQuery.url("img/") + "sin_pinchar.gif");
            }
        }
    );


    /** Binds event to every lov caller */
    jQuery(".lupa_lov").click(
        function() {
    
            jQuery("#opened_lov_options").val(jQuery(this).attr("longdesc"));
            var params = jQuery.makeObject(jQuery("#opened_lov_options").val());

            jQuery("#lov").load(jQuery.url(params["controller"] + "/" + params["action"])).modal({
                containerCss: {
                    height: 450,
                    width: 850,
                    position: "absolute",
                    paddingLeft: 4
                }
            });
        }
    );
    

    /** Hides select img when not in lov */
    jQuery(".seleccionar").hide();
    
});


/** Binds click event to select/deselect/invert classes for manipulating checkboxes */
jQuery.extend({
    bindMultipleCheckBoxManipulation: function(scope) {

        if (scope == undefined) {
            scope = "#index";
        }

        jQuery(scope + " table .seleccionarTodos").click(
            function() {
                jQuery(".tabla :checkbox").checkbox("seleccionar");
                return false;
            }
        );

        jQuery(scope + " table .deseleccionarTodos").click(
            function() {
                jQuery(".tabla :checkbox").checkbox("deseleccionar");
                return false;
            }
        );

        jQuery(scope + " table .invertir").click(
            function() {
                jQuery(".tabla :checkbox").checkbox("invertir");
                return false;
            }
        );
    }
});

    
/** Cretes an object (key => value) from a string
 * The form of the string should be:
 * str = "paramNameA: aaaaa; paramNameB: cccc";
*/
jQuery.makeObject = function(str, separator) {
    if (separator == undefined) {
        separator = ";";
    }

    var items = {};
    jQuery.each(str.split(separator),
        function() {
            var tmp = this.split(":");
            //items[tmp[0]] = tmp[1].trim();
            items[tmp[0]] = tmp[1];
        }
    );
    return items;
}
    
/** Useful function to avoid using Router::url everywere */
jQuery.url = function(url) {
    return jQuery("#base_url").val() + url;
}

    
Array.prototype.clean = function(to_delete)
{
   var a;
   for (a = 0; a < this.length; a++)
   {
      if (this[a] == to_delete)
      {         
         this.splice(a, 1);
         a--;
      }
   }
   return this;
};

var desglose_deprecated = function (tr, url) {
	var selector = "#" + tr;
	
	var tr = jQuery("<tr></tr>"); 
	var td = jQuery("<td></td>").attr("colspan", "10").html("aaaaaaaaaaaaaaa"); 
	tr.append(td);
	jQuery("table tr").next().append(tr);
	jQuery("table tr").next().append(tr);
	jQuery("table tr").next().append(tr);
	
	
	//table [x]
	if (!jQuery(selector).is(":visible")) {
		var td = jQuery("<td></td>").attr("colspan", "10"); 
		td.append(jQuery("<div></div>").attr("class", "desglose").load(url));
		jQuery(selector).append(td);
	}
	
	jQuery(selector).toggle();
}


var vOcultar = function() {
	jQuery('.session_flash').fadeOut('slow',
		function() {
			jQuery(this).remove();
		}
	);
}
	
var ajaxGet = function (url) {
	jQuery.ajax({
		type: 	'GET',
		async: 	false,
		url: 	url
	});
}

var ajaxPost = function(url) {
	jQuery.ajax({
		type: 	'POST',
		async: 	false,
		url: 	url
	});
}



/**
 * Esta funcion retorna el valor desde una lov (ya sea en div o en popup).
 * El valor lo retorna al input que se ve y a un hidden retorna el id.
 * Si el parametro padre viene un string vacio ("") implica que viene desde un div,
 * si es una popup este parametro tendra el valor "parent".
 */
function retornoLov_deprecated(retornarA, id, valor, padre)
{
	var idRetorno = 'div_' + retornarA;

	/**
	* Busco los elementos donde debo retornar, ya sean el parent (si es una popup)
	* o en la misma ventana.
	*/
	if(padre == 'opener') {
		var retornarAHidden = opener.document.getElementById(retornarA);
		var HiddenTmp = opener.document.getElementById(retornarA + 'Tmp');
		var retornarA = opener.document.getElementById(retornarA + '__');
	}
	else {
		var retornarAHidden = document.getElementById(retornarA);
		var HiddenTmp = document.getElementById(retornarA + 'Tmp');
		var retornarA = document.getElementById(retornarA + '__');
	}
	
	var ids = new Array();
	var valores = new Array();

	/**
	* Busco los valores y ids de estos de los checkbox seleccionados.
	*/
	jQuery(".tabla :checkbox").each(
		function() {
			var id;
			if(this.checked) {
				id = this.id.toString().substring(19);
				ids.push(id);
				jQuery(this).parent().find('a.seleccionar').each(
					function() {
						valores.push(jQuery(this).attr('title'));
					}
				);
			}
		}
	);

	/**
	* Incluyo en los valores a retornar tambien el valor actual (el clickeado) si no los tengo ya incluidos.
	*/
	if(jQuery.inArray(id, ids) == -1) {
		ids.push(id);
		valores.push(valor);
	}
	
	/**
	* Cuando se selecciona algo del control relaciondo hijo (el select),
	* genero un hidden que como value tiene el id del control relaciondo hijo + "|" + id seleccionado del padre.
	* Entonces, si existe este hidden, significa que ya antes se ha selecionado algo del select, y debo verificar
	* que haya cambiado o no la seleccion del padre.
	* En caso de que haya cambiado, le obligo que seleccione su opcion nuevamente en el select hijo.
	*/
	if(HiddenTmp != null) {
		var tmp = HiddenTmp.value.split('|');
		var idControlRelacionado = tmp[0];
		var idRelacionAnterior = tmp[1];
		if(id != idRelacionAnterior) {
			jQuery("#" + idControlRelacionado).html('<option value="0">Seleccione su opcion</option>');
		}
	}
	
	/**
	* Expando el alto del control de modo de poder mostrar todo.
	*/
	//retornarA.style.height=(valores.length * 18) + 'px';
	retornarAHidden.value = ids.join('**||**');
	retornarA.value = valores.join('\n');
	retornarA.title = valores.join('\n');

	/*
	* Debo cerrar si es una popup u ocultar si es un div.
	*/
	if(padre == 'opener') {
		/*
		* Evita la pregunta de seguridad que hace ie7 si desea cerrar la ventana...
		*/
		window.open('','_parent',''); 
		window.close();
	}
	//else {
		/*
		* Si es un div modal de jqmodal, simplemente lo oculto.
		*/
		//jqmWindow
		//jQuery('.jqmWindow').jqmHide();
		//alert(idRetorno);
		//jQuery('#' + idRetorno).jqmHide();
	//}
}



function abrirVentana_deprecated(winName, theURL, w, h) {

	if (typeof w=='undefined') {
		w=screen.availWidth-150;
	}
	if (typeof h=='undefined') {
		h=screen.availHeight-150;
	}
	
	var top=(screen.availHeight-h)/2;
	var left=(screen.availWidth-w)/2;

	var windowprops="top="+top+",left="+left+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height="+h;
	
	ventana=window.open(theURL, winName, windowprops);
	
	if (ventana.focus) {
		ventana.focus();
	}
} 