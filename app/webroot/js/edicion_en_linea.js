<!--
// script by yvoschaap.com
// freely useable
// optional link back would be very web 2.0 :)
// Modifica Martin Radosta 22/05/2007
// Ejemplo:
/*
<span	title='Presione aqui para modificar el valor' 
		onclick=\"editarEnLinea(this, 'urlQueRecibe.php', 'un_div');\" 
		style='cursor:pointer;' id='xx'>"
		zzzzzz
</span>
*/

var urlBase = "";
var divUpdate = "";
var changing = false;

//Prepara el posteo al server via ajax
function datosServidor() {
};
datosServidor.prototype.iniciar = function() {
	try {
		// Mozilla / Safari
		this._xh = new XMLHttpRequest();
	} catch (e) {
		// Explorer
		var _ieModelos = new Array(
		'MSXML2.XMLHTTP.5.0',
		'MSXML2.XMLHTTP.4.0',
		'MSXML2.XMLHTTP.3.0',
		'MSXML2.XMLHTTP',
		'Microsoft.XMLHTTP'
		);
		var success = false;
		for (var i=0;i < _ieModelos.length && !success; i++) {
			try {
				this._xh = new ActiveXObject(_ieModelos[i]);
				success = true;
			} catch (e) {
				// Implementar manejo de excepciones
			}
		}
		if ( !success ) {
			// Implementar manejo de excepciones, mientras alerta.
			return false;
		}
		return true;
	}
}

datosServidor.prototype.ocupado = function() {
	estadoActual = this._xh.readyState;
	return (estadoActual && (estadoActual < 4));
}

datosServidor.prototype.procesa = function() {
	if (this._xh.readyState == 4 && this._xh.status == 200) {
		this.procesado = true;
	}
}

datosServidor.prototype.enviar = function(urlget,datos) {
	if (!this._xh) {
		this.iniciar();
	}
	if (!this.ocupado()) {
		this._xh.open("GET",urlget,false);
		this._xh.send(datos);
		if (this._xh.readyState == 4 && this._xh.status == 200) {
			return this._xh.responseText;
		}
		
	}
	return false;
}


//Cuando presiono enter posteo
function fieldEnter(campo,evt,idfld) {
	evt = (evt) ? evt : window.event;
	if (evt.keyCode == 13 && campo.value!="") {
		elem = document.getElementById( idfld )
		if (divUpdate != "")
		{
			var paraUpdate = document.getElementById( divUpdate );
		}
		else
		{
			var paraUpdate = document.getElementById( idfld );
		}

		remotos = new datosServidor;
		nt = remotos.enviar(urlBase + "/" + escape(elem.id) + "/" + escape(campo.value));
		//remove glow
		noLight(elem);
		paraUpdate.innerHTML = nt;
		changing = false;
		return false;
	} else {
		return true;
	}


}

//Cuando pierde el foco posteo
function fieldBlur(campo,idfld) {
	if (campo.value!="") {
		elem = document.getElementById( idfld )
		if (divUpdate != "")
		{
			var paraUpdate = document.getElementById( divUpdate );
		}
		else
		{
			var paraUpdate = document.getElementById( idfld );
		}
		remotos = new datosServidor;
		nt = remotos.enviar(urlBase + "/" + escape(elem.id) + "/" + escape(campo.value));
		paraUpdate.innerHTML = nt;
		changing = false;
		return false;
	}
}

//Creo un input para que este pueda ser editado
function editarEnLinea(actual, url, update) {
	if(!changing){
		urlBase   = url;
		divUpdate = update;
		width = widthEl(actual.id) + 5;
		height =heightEl(actual.id);
		if(width < 40)
			width = 40;
		if(height < 40)
			actual.innerHTML = "<input id=\""+ actual.id +"_field\" style=\"width: "+width+"px; height: "+height+"px;\" maxlength=\"254\" type=\"text\" value=\"" + actual.innerHTML + "\" onkeypress=\"return fieldEnter(this,event,'" + actual.id + "')\" onfocus=\"highLight(this);\" onblur=\"noLight(this); return fieldBlur(this,'" + actual.id + "');\" />";
		else
			actual.innerHTML = "<textarea name=\"textarea\" id=\""+ actual.id +"_field\" style=\"width: "+width+"px; height: "+height+"px;\" onfocus=\"highLight(this);\" onblur=\"noLight(this); return fieldBlur(this,'" + actual.id + "');\">" + actual.innerHTML + "</textarea>";
	
		changing = true;
	}
	actual.firstChild.focus();
}

//Obtengo el width del elemento texto
function widthEl(span){

	if (document.layers){
	  w=document.layers[span].clip.width;
	} else if (document.all && !document.getElementById){
	  w=document.all[span].offsetWidth;
	} else if(document.getElementById){
	  w=document.getElementById(span).offsetWidth;
	}
return w;
}

//Obtengo el heightdel elemento texto
function heightEl(span){

	if (document.layers){
	  h=document.layers[span].clip.height;
	} else if (document.all && !document.getElementById){
	  h=document.all[span].offsetHeight;
	} else if(document.getElementById){
	  h=document.getElementById(span).offsetHeight;
	}
return h;
}

//le pongo un marco llamativo al elemento
function highLight(span){
            span.parentNode.style.border = "2px solid #D1FDCD";
            span.parentNode.style.padding = "0";
            span.style.border = "1px solid #54CE43";          
}

//le saca el marco llamativo al elemento
function noLight(span){
        span.parentNode.style.border = "0px";
        span.parentNode.style.padding = "0px";
        span.style.border = "0px";       
}