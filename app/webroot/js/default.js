//defino la variable para que este accesible desde todos lados (como una global)
var timer;

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
 * Permite manipular una serie de checks dentro de un contenedor
 * @param string accion indica la acciona realizar: seleccionar (selecicona todos los checks)
 *													deseleccionar (deseleccionar todos los checks)
 *													invertir (al check que esta seleccionado lo deselecciona y biceversa)
 * @param string contenedor indica el id del contenedor donde se encuentran los checks
 * @return boolean true si encuentra el contenedor, false si no lo encuentra
 */
function accionesChecks_deprecated( accion, contenedor ) {
	var divContenedor;
	var inputs;

	divContenedor = document.getElementById(contenedor);

	if(divContenedor != null)
	{
		inputs = divContenedor.getElementsByTagName('input');

		for ( var i = 0; i < inputs.length; i++ ) {
			if ( inputs[i].type == 'checkbox' ) {
				if (accion == 'seleccionar')
				{
					inputs[i].checked = true;
				}
				else if (accion == 'deseleccionar')
				{
					inputs[i].checked = false;
				}
				else if (accion == 'invertir')
				{
					inputs[i].checked = !inputs[i].checked;
				}		
			}
		}
    return true;
	}
	else
	{
		return false;
	}
}


function mostrarOcultarDivx_deprecated( id ) {
    var elDiv = document.getElementById(id);
    var elTr = document.getElementById('tr_' + id);
	if (elDiv.style.visibility == 'visible')
    {
        elDiv.style.visibility = 'hidden';
		elDiv.style.display = 'none';
        elTr.style.visibility = 'hidden';
		elTr.style.display = 'none';
    }
	else
	{
        elDiv.style.visibility = 'visible';
		elDiv.style.display = 'block';
        elTr.style.visibility = 'visible';
		elTr.style.display = 'block';
	}
}

//id del div a ocultar
//si forzar es true, no importa el estado anterior, siempre lo muestra, false siempre lo oculta
function mostrarOcultarDiv( id_div, forzar ) {

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


function mostrarOcultarDivYTr(refElement, id_div, id_tr, base_url) {
	
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

/*
Muestra el div de los errores. Establece un tiempo para que se oculte solo.
*/
function mostrarErrores_deprecated( mensaje )
{
	if (mensaje == null)
	{
		mensaje = 'Error';
	}
    var divErrores = document.getElementById('errores');
	divErrores.innerHTML = '<img src=\'../img/12x12/error.gif\' />' + mensaje;
	divErrores.style.visibility = 'visible';
	timer = setInterval('ocultarErrores()',5000);
}

/*
Oculta el div de los errores. Resetea tiempo para que se oculte solo.
*/
function ocultarErrores_deprecated()
{
    var divErrores = document.getElementById('errores');
	divErrores.style.visibility = 'hidden';
	clearInterval(timer);
}

/**
 * Permite el ingreso solo de numeros (y las teclas de control) a los inputs
 * @param object campo que deseo se controle su ingreso
 * @param string evento el evento que disparo el llamado de la funcion
 * @param boolean indica si permite el ingreso de valores decimal. Por defecto solo enteros
 * @return boolean true si el caracter ingresado es numero o false en caso de no serlo
 * @example onKeyPress = 'return soloNumeros(this, event, true);'
 */

function soloNumeros_deprecated(campo, evento, decimal)
{
	if(decimal == null)
		decimal = false;

	var key;
	var keychar;

	if (window.event)
	   key = window.event.keyCode;
	else if (evento)
	   key = evento.which;
	else
	   return true;
	keychar = String.fromCharCode(key);

	// teclas de control 
	if ((key==null) || (key==0) || (key==8) || 
		(key==9) || (key==13) || (key==27) )
	   return true;

	// numeros
	else if ((("0123456789").indexOf(keychar) > -1))
	   return true;

	// decimal
	else if (decimal && (keychar == "."))
	   {
	   return true;
	   }
	else
	{
	   return false;
	}
}

/**
 * Limpia el texto que hubiere dentro de los imput text (cajas de texto) 
 * y selecciona el primer elemento de los selects (combos)
 * @param string contenedor id del div que actua como contenedor de los elementos
 * @return boolean true si encuentra el contenedor, false si no lo encuentra
 */

function limpiarBusqueda_deprecated( contenedor ) {
	var divContenedor;
	var objetos;
	var i;

    divContenedor = document.getElementById( contenedor );
	if (divContenedor != null)
	{
		//busco los inputs
		objetos = divContenedor.getElementsByTagName('input');
		if (objetos != null)
		{
			for ( i = 0; i < objetos.length; i++ ) 
			{
				if (objetos[i].type=='text')
				{
					objetos[i].value = "";
				}
			}
		}

		//busco los selects
		objetos = divContenedor.getElementsByTagName('select');
		if (objetos != null)
		{
			for ( i = 0; i < objetos.length; i++ ) 
			{
				objetos[i].options[0].selected = true;
			}
		}
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Al hacer click sobre el boton aceptar oculta el div del session flash
 * @return boolean true si encuentra el contenedor (session_flash), false si no lo encuentra
 */
function ocultarSessionFlash_deprecated(){
	var ElDivDelFlash = document.getElementById('session_flash');
	if (ElDivDelFlash)
	{
		ElDivDelFlash.style.display='none';
		ElDivDelFlash.parentNode.removeChild(ElDivDelFlash);
		return true;
	}
	else
		return false;
}


	
/**
 * Cambia la clase css de un elemento
 * @param string id id del elemento due�o de la clase
 * @param string clase nombre de la nueva clase a asignar al elemento
 * @return boolean true si encuentra el contenedor, false si no lo encuentra
 */
function cambiarClase_deprecated(id, clase){
	var elemento = document.getElementById(id);
	if (elemento)
	{
		elemento.className = clase;
		return true;
	}
	else
		return false;
}

/**
 * Intercambia entre dos clases css, dada una clase cambia por la otra y viceversa 
 * @param string id id del elemento due�o de la clase
 * @param string claseA nombre de la clase de origen
 * @param string claseB nombre de la clase de destino
 * @return boolean true si encuentra el contenedor, false si no lo encuentra
 */
function intercambiaClase_deprecated(id, claseA, claseB){
	var elemento = document.getElementById(id);
	if (elemento)
	{
		if(elemento.className == claseA)
			elemento.className = claseB;
		else if(elemento.className == claseB)
			elemento.className = claseA;

		return true;
	}
	else
		return false;
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
	jQuery(".tabla input[@type=\'checkbox\']").each(
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