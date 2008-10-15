/**
 * Choose | jQuery's Plugin
 * @ Version:	1.0 
 * @ Author:	Marco Pegoraro
 *
 * Permette di generare un'interfaccia di selezione valore basata su pupUp con
 * il caricamento AJAX dei valori selezionabili.
 * Questa interfaccia permette di visualizzare un'ampio spettro di dati
 * (molti piÃ¹ di una select!) e di effettuare complesse operazioni di popolamento
 * alla selezione del risultato.
 *
 */


/**
 * Inizializza un pannello di selezione o una select tag.
 */
jQuery.fn.choose = function(cfg) {
	
	// Gestione del passaggio di un'unico parametro come url della sorgente dati per la           #
	// composizione della dialog di selezione.                                                    #
	// Passando ques'unico parametro senza definire alcuna callback si automatizza la generazione #
	// della dialog e la compilazione del campo "choose" di origine.                              #
	if ( typeof(cfg) == 'string' ) {
		source		= cfg;
		cfg			= {}
		cfg.source	= source;
	}
	
	// Inizializzazione della struttura di configurazione dell'oggetto.                           #
	cfg					= cfg				|| {}
	cfg.useDblClick		= cfg.useDblClick	|| false
	cfg.blockEditing	= cfg.blockEditing	|| true
	cfg.source			= cfg.source		|| null
	cfg.onChoose		= cfg.onChoose		|| function( obj ) { obj.chooseDialog({ source: cfg.source }); }
	cfg.o				= $(this);
	
	// ------------------------------------------------------------------------------------------ #
	// Controllo se si sta assegnando il tipo "choose" ad una select al fine di modificare
	// interativamente l'xHTML per generare il codice necessario al plugin.
	if ( $('option',$(this)).length ) {
		
		// Salvo l'id dell'oggetto SELECT da convertire in bottone "choose".                      #
		chooseId = $(this).attr('id');
		
		// Creazione dinamica del codice necessario all'implementazione di "choose".              #
		// E' stato necessario realizzare un workaround per safari in quanto non riusciva ad      #
		// identificare lo span creato mediante ID. Viene cosÃ¬ aggiunta una classe speciale che   #
		// serve semplicemente all'identificazione dell'elemento da convertire in campo "choose". #
		if ( $.browser.safari ) {
			code = '<span id="'+chooseId+'" class="choose_select_starter_'+chooseId+'">';
		} else {
			code = '<span id="'+chooseId+'">';
		}
		code+= '<input type="text" class="value" name="'+$(this).attr('name')+'" value="" />';
		code+= '<input type="text" class="label" value="" />';
		code+= '</span>';
		
		// Aggiunta del codice alla pagina nella posizione successiva alla select di origine.     #
		$(this).after(code);		
		$(this).remove();
		
		// Preimpostazione del campo "choose" con il valore predefinito della SELECT.             #
		$('option:selected',$(this)).each(function() {
			if( !($('option:selected',$(this)).html() == "&nbsp;" && $('option:selected',$(this)).attr('value') == undefined) ) {
				
				// Workaround per il corretto funzionamento su safari.                            #
				if ( $.browser.safari ) {
					$('.choose_select_starter_'+chooseId+' .value').attr('value', $(this).attr('value') );
					$('.choose_select_starter_'+chooseId+' .label').attr('value', $(this).html() );
				} else {
					$('#'+chooseId+' .value').attr('value', $(this).attr('value') );
					$('#'+chooseId+' .label').attr('value', $(this).html() );
				}
				
			}
		});
		
		// Ripresa del workaround per safari per identificare l'oggetto "choose" su cui applicare #
		// il plugin.                                                                             #
		if ( $.browser.safari ) {
			obj = $('span.choose_select_starter_'+chooseId);
		} else {
			obj = $('#'+chooseId);
		}
		
		// Richiamo la generazione dell'oggetto "choose" sull'oggetto medesimo che ora Ã¨          #
		// convertito in TAG SPAN con campi di INPUT.                                             #
		return $(obj).choose({
			useDblClick:	cfg.useDblClick,
			blockEditing:	cfg.blockEditing,
			source:			cfg.source,
			onChoose:		cfg.onChoose
		});
		
	} // Fine controllo e gestione di "choose" su di un tag "OPTION".                             #
	// ------------------------------------------------------------------------------------------ #
	
	
	
	$(this).addClass('choose');
	
	// Nascondo il campo valore per il campo selezionato.
	$('.value', $(this)).hide();
	$('.label', $(this)).after('<a href="">Choose</a>');
	
	// Attivo la gestione della richiesta di ricerca in pupUp.
	$('a', $(this)).click(function() {
		cfg.onChoose( cfg.o );
		return false;
	});
	
	// La gestione del soppio click sul campo di label Ã¨ attivabile unicamente come campo         #
	// opzionale di configurazione. Di default Ã¨ disattivato in quanto su FF genera errore.       #
	if ( cfg.useDblClick )
		$('.label', $(this)).dblclick(function() {
			cfg.onChoose( cfg.o );
			return false;
		});
	
	// Blocco della digitazione nel campo di input "label". Di default Ã¨ attivata ma Ã¨ possibile  #
	// disattivarla da campo opzionale di configurazione.                                         #
	// TODO: alla pressione di INVIO bisogna far aprire la dialog di selezione per questioni di   #
	// accessibilitÃ  del componente generato.                                                     #
	if ( cfg.blockEditing )
		$('.label', $(this)).keypress(function() {
			return false;
		});
	
	return $(this);
} // EndOf: "jQuery.fn.choose()" ##################################################################


/**
 * Imposta il valore di un campo "choose" da un oggetto "{value|label}".
 * I valori di "value" e "label" di default sono vuoti.
 */
jQuery.fn.chooseSet = function( cfg ) {
	cfg			= cfg			|| {}
	cfg.value	= cfg.value		|| '';
	cfg.label	= cfg.label		|| '';
	
	$('.value', $(this)).attr('value',cfg.value);
	$('.label', $(this)).attr('value',cfg.label);
	
	return $(this);
} // EndOf: "jQuery.fn.chooseSet()" ###############################################################


/**
 * Legge il valore di un campo "choose" in un oggetto "{value|label}"
 */
jQuery.fn.chooseGet = function() {
	cfg			= {};
	cfg.value	= $('.value', $(this)).attr('value');
	cfg.label	= $('.label', $(this)).attr('value');
	return cfg;
} // EndOf: "jQuery.fn.chooseGet()" ###############################################################


/**
 * Resetta il valore di un campo "choose".
 */
jQuery.fn.chooseReset = function() {
	$('.value', $(this)).attr('value','');
	$('.label', $(this)).attr('value','');
	
	return $(this);
} // EndOf: "jQuery.fn.chooseReset()" #############################################################


/**
 * Produce una dialog di selezione valori caricando la lista da uno script remoto via
 * AJAX e fornendo l'interfaccia di selezione del valore ed aggiornamento di un campo "choose".
 */
 jQuery.fn.chooseDialog = function(cfg) {
	cfg				= cfg				|| {}
	cfg.title		= cfg.title			|| 'Selezionare un valore:'
	cfg.onChoose	= cfg.onChoose		|| function() {}
	cfg.field		= $(this);
	
	// Definizione dell'Id della dialog in base all'id del campo "choose" di origine.             #
	if ( cfg.field != null )
		dlgId = cfg.field.attr('id')+"_dialog";
	else
		dlgId = "chooseButtonModalDialog";

	// Creazione dinamica del codice della dialog con caricamento della sorgente dati per la      #
	// selezione del valore.                                                                      #
	$('body').append( jqmGetModal(dlgId, cfg.title, 'Caricamento dati in corso...' ) );
	dlg = $('#'+dlgId);
	
	// Attivazione della finestra modale e visualizzazione.                                       #
	dlg.jqm().jqmShow();
	// Gestione rollover sul bottone di chiusura finestra.                                        #
	$('input.jqmdX',dlg).hover(function(){ this.blur(); $(this).addClass('jqmdXFocus'); },function(){ this.blur(); $(this).removeClass('jqmdXFocus'); }).focus(function(){ this.hideFocus=true; $(this).addClass('jqmdXFocus'); }).blur( function(){ $(this).removeClass('jqmdXFocus'); });
	$('input.jqmdX',dlg).each(function(){this.blur();});
	
	// Attivazione della richiesta ajax dei dati relativi al popolamento della dialog.            #
	if ( cfg.source != null )
		// ModalitÃ  sincrona prima dell'introduzione delle funzioni esterne... Obsoleta.          #
		//$('.jqmdMSG',dlg).html( $.ajax({url: cfg.source,async: false}).responseText );
		$('.jqmdMSG',dlg).load(cfg.source,function(){ chooseDialog_initData( dlg, cfg ); });
	else chooseDialog_initData( dlg, cfg );
	
	return $(this);
} // EndOf: "jQuery.fn.chooseDialog()" ############################################################


/**
 * Inizializza la gestione dei dati caricati via AJAX come tabella.
 */
function chooseDialog_initData( dlg, cfg ) {
	
	// Sistemazioni grafiche varie.                                                               #
	$('.src',dlg).wrap('<div class="choose_dialog_tbc"></div>');
	$('.src',dlg).attr('cellspacing','0');
	
	// Mappatura delle colonne dati nascoste.                                                     #
	// Per definire una colonna dati nascosta dichiarare l'attributo "hide=true".                 #
	// Il dato viene nascosto all'interfaccia ma viene utilizzato per la composizione dell'oggetto#
	// dati per la compilazione del campo "choose".                                               #
	i = 0;
	$('.src thead tr:first',dlg).find('th').each(function() {
		if ( $(this).attr('hide') == "true" ) {
			$(this).hide();
			$('.src tbody tr',dlg).each(function(){
				$($(this).children().get(i)).hide();
			});
		}
		i++;
	});
	
	// Gestione degli eventi di selezione dei valori.                                             #
	$('.src tbody tr',dlg)
		.dblclick(function() { 
			chooseDialog_choose(cfg,dlg,$(this)); 
		})
		.hover(
			function(){ $(this).addClass('hover'); },
			function(){ $(this).removeClass('hover'); }
		)
		.click(function() {
			$('.src tbody tr',dlg).each(function(){ $(this).removeClass('selected'); });
			$(this).addClass('selected');
		})
	;
} // EndOf: "chooseDialog_initData()" #############################################################


/**
 * Gestisce l'avvenuta selezione di un valore come espressione di riga.
 * Viene utilizzata per gestire la callback in doppio click sulla riga o alla
 * pressione del tasto "seleziona".
 */
function chooseDialog_choose( cfg, dlg, row ) {
	// Costruzione dell'oggetto dati da fornire in output alla dialog.                            #
	obj = chooseDialog_getObject( row, $('table.src thead',dlg) );
		
	// Imposto il valore nel campo "choose" che ha generato la dialgo.                            #
	if ( cfg.field != null ) cfg.field.chooseSet(obj);
	
	// Autopopolamento dei vari campi con i valori addizionali della dialog.                      #
	for ( i=0; i<obj.property_names.length; i++ ) {
		if ( obj.property_names[i] != undefined ) {
			ffId = cfg.field.attr('id') + '_'+ obj.property_names[i];
			if ( $('#'+ffId).length > 0 )
				switch ( $('#'+ffId).get(0).tagName.toUpperCase() ) {
					case 'INPUT':
							$('#'+ffId).val(obj.fields[i]); break;
					case 'SELECT':
							break;
					default:
							$('#'+ffId).text(obj.fields[i]); break;
				}
		}
	}
	
	// Lancio la callback personalizzata riferita alla dialog.                                    #
	cfg.onChoose(obj);
	
	// Chiusura e distruzione della finestra modale.                                              #
	dlg.jqmHide().remove();
} // EndOf: "chooseDialog_choose()" ###############################################################


/**
 * Esegue la composizione dell'oggetto dati di output in base alla riga selezionata ed
 * ad un'eventuale composizione di tabella.
 * La peculiaritÃ  di questa funzione Ã¨ quella di creare un oggetto avente le chiavi
 * predefinite "value|label" per l'utilizzo in campi "choose", l'array di tutti campi
 * definiti nella tabella ed anche le proprietÃ  di tali campi nominate secondo l'attributo
 * "class" della corrispondente colonna in "thead".
 * Restituisce l'oggetto dati corrispondente alla riga selezionata.
 */
function chooseDialog_getObject(row,thead) {
	// Compongo un oggetto con i dati forniti nella riga selezionata.                             #
	obj = {}
	obj.value = $('th',row).html();
	obj.label = $('td:first',row).html();
	
	// Analizzo la riga di intestazione per costruire l'array dei nomi delle proprietÃ .           #
	obj.property_names = Array();
	$('th',thead).each(function(){
		obj.property_names[obj.property_names.length] = $(this).attr('scope');
	});
	
	
	// Compongo i restanti campi della sorgente dati nell'oggetto di callback.                    #
	obj.count = 1;
	obj.fields = Array();
	obj.fields[0] = obj.value;
	
	$('td',row).each(function() {
		obj.fields[obj.count] = $(this).html();
		// Costruisco la proprietÃ  nominata se Ã¨ stata esposta a questa funzionalitÃ .             #
		if ( obj.property_names[obj.count] != undefined )
			eval ( "obj."+obj.property_names[obj.count]+"='"+$(this).html()+"';" );
		
		obj.count++;
	});
	
	return obj;
} // EndOf: "chooseDialog_getObject()" ############################################################