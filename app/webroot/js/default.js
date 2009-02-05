//defino la variable para que este accesible desde todos lados (como una global)
var timer;

var desglose = function (tr, url) {
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


//id del div a ocultar
//si forzar es true, no importa el estado anterior, siempre lo muestra, false siempre lo oculta
function mostrarOcultarDiv_deprecated( id_div, forzar ) {

	var elDiv = document.getElementById(id_div);
	if(forzar == true)
	{
		elDiv.style.visibility = 'visible';
		elDiv.style.display = 'inline';
	}
	else if(forzar == false)
	{
		elDiv.style.visibility = 'hidden';
		elDiv.style.display = 'none';
	}
	else
	{
		if (elDiv.style.visibility == 'visible')
		{
			elDiv.style.visibility = 'hidden';
			elDiv.style.display = 'none';
		}
		else if(elDiv.style.visibility == 'hidden' || elDiv.style.visibility == '')
		{
			elDiv.style.visibility = 'visible';
			elDiv.style.display = 'inline';
		}
	}
}


function mostrarOcultarDivYTr_deprecated(refElement, id_div, id_tr, base_url) {
	
	var elDiv = document.getElementById(id_div);
	var elTr = document.getElementById(id_tr);
	if (elTr.style.visibility == 'visible') {
		elDiv.style.visibility = 'hidden';
		elDiv.style.display = 'none';
		elTr.style.visibility = 'hidden';
		elTr.style.display = 'none';
		/**
		* Ejecuto esta accion para que me elimine de la session este desglose ya que esta cerrado.
		*/
		jQuery.get(base_url + '/quitarDesglose/' + refElement.className);
		return false;
	}
	else
	{
		elDiv.style.visibility = 'visible';
		elDiv.style.display = 'inline';
		elTr.style.visibility = 'visible';
		var isMSIE = /*@cc_on!@*/false; //detecto si es MSIE
		if(isMSIE)
		{ 
			elTr.style.display = 'block'; //solo para explorer
		}
		else
		{
			elTr.style.display = 'table-row'; //gecko
		}
	}
}

/**
 * Esta funcion retorna el valor desde una lov (ya sea en div o en popup).
 * El valor lo retorna al input que se ve y a un hidden retorna el id.
 * Si el parametro padre viene un string vacio ("") implica que viene desde un div,
 * si es una popup este parametro tendra el valor "parent".
 */
function retornoLov(retornarA, id, valor, padre)
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



function abrirVentana(winName, theURL, w, h) {

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