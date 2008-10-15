/* default.js */
var timer;var vOcultar=function(){jQuery('.session_flash').fadeOut('slow',function(){jQuery(this).remove();});}
var ajaxGet=function(url){jQuery.ajax({type:'GET',async:false,url:url});}
var ajaxPost=function(url){jQuery.ajax({type:'POST',async:false,url:url});}
function accionesChecks_deprecated(accion,contenedor){var divContenedor;var inputs;divContenedor=document.getElementById(contenedor);if(divContenedor!=null)
{inputs=divContenedor.getElementsByTagName('input');for(var i=0;i<inputs.length;i++){if(inputs[i].type=='checkbox'){if(accion=='seleccionar')
{inputs[i].checked=true;}
else if(accion=='deseleccionar')
{inputs[i].checked=false;}
else if(accion=='invertir')
{inputs[i].checked=!inputs[i].checked;}}}
return true;}
else
{return false;}}
function mostrarOcultarDivx_deprecated(id){var elDiv=document.getElementById(id);var elTr=document.getElementById('tr_'+id);if(elDiv.style.visibility=='visible')
{elDiv.style.visibility='hidden';elDiv.style.display='none';elTr.style.visibility='hidden';elTr.style.display='none';}
else
{elDiv.style.visibility='visible';elDiv.style.display='block';elTr.style.visibility='visible';elTr.style.display='block';}}
function mostrarOcultarDiv(id_div,forzar){var elDiv=document.getElementById(id_div);if(forzar==true)
{elDiv.style.visibility='visible';elDiv.style.display='inline';}
else if(forzar==false)
{elDiv.style.visibility='hidden';elDiv.style.display='none';}
else
{if(elDiv.style.visibility=='visible')
{elDiv.style.visibility='hidden';elDiv.style.display='none';}
else if(elDiv.style.visibility=='hidden'||elDiv.style.visibility=='')
{elDiv.style.visibility='visible';elDiv.style.display='inline';}}}
function mostrarOcultarDivYTr(refElement,id_div,id_tr,base_url){var elDiv=document.getElementById(id_div);var elTr=document.getElementById(id_tr);if(elTr.style.visibility=='visible'){elDiv.style.visibility='hidden';elDiv.style.display='none';elTr.style.visibility='hidden';elTr.style.display='none';jQuery.get(base_url+'/quitarDesglose/'+refElement.className);return false;}
else
{elDiv.style.visibility='visible';elDiv.style.display='inline';elTr.style.visibility='visible';var isMSIE=false;if(isMSIE)
{elTr.style.display='block';}
else
{elTr.style.display='table-row';}}}
function mostrarErrores_deprecated(mensaje)
{if(mensaje==null)
{mensaje='Error';}
var divErrores=document.getElementById('errores');divErrores.innerHTML='<img src=\'../img/12x12/error.gif\' />'+mensaje;divErrores.style.visibility='visible';timer=setInterval('ocultarErrores()',5000);}
function ocultarErrores_deprecated()
{var divErrores=document.getElementById('errores');divErrores.style.visibility='hidden';clearInterval(timer);}
function soloNumeros_deprecated(campo,evento,decimal)
{if(decimal==null)
decimal=false;var key;var keychar;if(window.event)
key=window.event.keyCode;else if(evento)
key=evento.which;else
return true;keychar=String.fromCharCode(key);if((key==null)||(key==0)||(key==8)||(key==9)||(key==13)||(key==27))
return true;else if((("0123456789").indexOf(keychar)>-1))
return true;else if(decimal&&(keychar=="."))
{return true;}
else
{return false;}}
function limpiarBusqueda_deprecated(contenedor){var divContenedor;var objetos;var i;divContenedor=document.getElementById(contenedor);if(divContenedor!=null)
{objetos=divContenedor.getElementsByTagName('input');if(objetos!=null)
{for(i=0;i<objetos.length;i++)
{if(objetos[i].type=='text')
{objetos[i].value="";}}}
objetos=divContenedor.getElementsByTagName('select');if(objetos!=null)
{for(i=0;i<objetos.length;i++)
{objetos[i].options[0].selected=true;}}
return true;}
else
{return false;}}
function ocultarSessionFlash_deprecated(){var ElDivDelFlash=document.getElementById('session_flash');if(ElDivDelFlash)
{ElDivDelFlash.style.display='none';ElDivDelFlash.parentNode.removeChild(ElDivDelFlash);return true;}
else
return false;}
function cambiarClase_deprecated(id,clase){var elemento=document.getElementById(id);if(elemento)
{elemento.className=clase;return true;}
else
return false;}
function intercambiaClase_deprecated(id,claseA,claseB){var elemento=document.getElementById(id);if(elemento)
{if(elemento.className==claseA)
elemento.className=claseB;else if(elemento.className==claseB)
elemento.className=claseA;return true;}
else
return false;}
function retornoLov(retornarA,id,valor,padre)
{var idRetorno='div_'+retornarA;if(padre=='opener'){var retornarAHidden=opener.document.getElementById(retornarA);var HiddenTmp=opener.document.getElementById(retornarA+'Tmp');var retornarA=opener.document.getElementById(retornarA+'__');}
else{var retornarAHidden=document.getElementById(retornarA);var HiddenTmp=document.getElementById(retornarA+'Tmp');var retornarA=document.getElementById(retornarA+'__');}
var ids=new Array();var valores=new Array();jQuery("#tabla input[@type=\'checkbox\']").each(function(){var id;if(this.checked){id=this.id.toString().substring(19);ids.push(id);jQuery(this).parent().find('a.seleccionar').each(function(){valores.push(jQuery(this).attr('title'));});}});if(jQuery.inArray(id,ids)==-1){ids.push(id);valores.push(valor);}
if(HiddenTmp!=null){var tmp=HiddenTmp.value.split('|');var idControlRelacionado=tmp[0];var idRelacionAnterior=tmp[1];if(id!=idRelacionAnterior){jQuery("#"+idControlRelacionado).html('<option value="0">Seleccione su opcion</option>');}}
retornarAHidden.value=ids.join('**||**');retornarA.value=valores.join('\n');retornarA.title=valores.join('\n');if(padre=='opener'){window.open('','_parent','');window.close();}}
function abrirVentana(winName,theURL,w,h){if(typeof w=='undefined'){w=screen.availWidth-150;}
if(typeof h=='undefined'){h=screen.availHeight-150;}
var top=(screen.availHeight-h)/2;var left=(screen.availWidth-w)/2;var windowprops="top="+top+",left="+left+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height="+h;ventana=window.open(theURL,winName,windowprops);if(ventana.focus){ventana.focus();}}
/* datetimepicker.js */
var winCal;var dtToday=new Date();var Cal;var MonthName=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];var WeekDayName1=["Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado"];var WeekDayName2=["Lunes","Martes","Miercoles","Jueves","Viernes","Sabado","Domingo"];var exDateTime;var selDate;var cnTop="200";var cnLeft="500";var WindowTitle="DateTime Picker";var WeekChar=2;var CellWidth=30;var DateSeparator="/";var TimeMode=24;var ShowLongMonth=true;var ShowMonthYear=true;var MonthYearColor="#cc0033";var WeekHeadColor="#0099CC";var SundayColor="#6699FF";var SaturdayColor="#CCCCFF";var WeekDayColor="white";var FontColor="blue";var TodayColor="#FFFF33";var SelDateColor="FFFF99";var YrSelColor="#cc0033";var MthSelColor="#cc0033";var ThemeBg="";var PrecedeZero=true;var MondayFirstDay=true;function NewCal(pCtrl,pFormat,pShowTime,pTimeMode,pScroller,pHideSeconds)
{Cal=new Calendar(dtToday);if((pShowTime!=null)&&(pShowTime))
{Cal.ShowTime=true;if((pTimeMode!=null)&&((pTimeMode=='12')||(pTimeMode=='24')))
{TimeMode=pTimeMode;}
if((pHideSeconds!=null)&&(pHideSeconds))
{Cal.ShowSeconds=false;}}
if(pCtrl!=null)
Cal.Ctrl=pCtrl;if(pFormat!=null)
Cal.Format=pFormat.toUpperCase();if(pScroller!=null)
{if(pScroller.toUpperCase()=="ARROW")
Cal.Scroller="ARROW";else
Cal.Scroller="DROPDOWN";}
exDateTime=document.getElementById(pCtrl).value;if(exDateTime!="")
{var Sp1;var Sp2;var tSp1;var tSp1;var strMonth;var strDate;var strYear;var intMonth;var YearPattern;var strHour;var strMinute;var strSecond;var winHeight;Sp1=exDateTime.indexOf(DateSeparator,0)
Sp2=exDateTime.indexOf(DateSeparator,(parseInt(Sp1)+1));var offset=parseInt(Cal.Format.toUpperCase().lastIndexOf("M"))-parseInt(Cal.Format.toUpperCase().indexOf("M"))-1;if((Cal.Format.toUpperCase()=="DDMMYYYY")||(Cal.Format.toUpperCase()=="DDMMMYYYY"))
{if(DateSeparator=="")
{strMonth=exDateTime.substring(2,4+offset);strDate=exDateTime.substring(0,2);strYear=exDateTime.substring(4+offset,8+offset);}
else
{strMonth=exDateTime.substring(Sp1+1,Sp2);strDate=exDateTime.substring(0,Sp1);strYear=exDateTime.substring(Sp2+1,Sp2+5);}}
else if((Cal.Format.toUpperCase()=="MMDDYYYY")||(Cal.Format.toUpperCase()=="MMMDDYYYY"))
{if(DateSeparator=="")
{strMonth=exDateTime.substring(0,2+offset);strDate=exDateTime.substring(2+offset,4+offset);strYear=exDateTime.substring(4+offset,8+offset);}
else
{strMonth=exDateTime.substring(0,Sp1);strDate=exDateTime.substring(Sp1+1,Sp2);strYear=exDateTime.substring(Sp2+1,Sp2+5);}}
else if((Cal.Format.toUpperCase()=="YYYYMMDD")||(Cal.Format.toUpperCase()=="YYYYMMMDD"))
{if(DateSeparator=="")
{strMonth=exDateTime.substring(4,6+offset);strDate=exDateTime.substring(6+offset,8+offset);strYear=exDateTime.substring(0,4);}
else
{strMonth=exDateTime.substring(Sp1+1,Sp2);strDate=exDateTime.substring(Sp2+1,Sp2+3);strYear=exDateTime.substring(0,Sp1);}}
else if((Cal.Format.toUpperCase()=="DD/MM/YYYY"))
{if(Cal.ShowTime==true){var tmp1=exDateTime.substring(0,10);var tmp=tmp1.split(DateSeparator);}
else{var tmp=exDateTime.split(DateSeparator);}
strDate=tmp[0];strMonth=tmp[1];strYear=tmp[2];}
if(isNaN(strMonth))
intMonth=Cal.GetMonthIndex(strMonth);else
intMonth=parseInt(strMonth,10)-1;if((parseInt(intMonth,10)>=0)&&(parseInt(intMonth,10)<12))
Cal.Month=intMonth;if((parseInt(strDate,10)<=Cal.GetMonDays())&&(parseInt(strDate,10)>=1))
Cal.Date=strDate;YearPattern=/^\d{4}$/;if(YearPattern.test(strYear))
Cal.Year=parseInt(strYear,10);if(Cal.ShowTime==true)
{if(TimeMode==12)
{strAMPM=exDateTime.substring(exDateTime.length-2,exDateTime.length)
Cal.AMorPM=strAMPM;}
tSp1=exDateTime.indexOf(":",0)
tSp2=exDateTime.indexOf(":",(parseInt(tSp1)+1));if(tSp1>0)
{strHour=exDateTime.substring(tSp1,(tSp1)-2);Cal.SetHour(strHour);strMinute=exDateTime.substring(tSp1+1,tSp1+3);Cal.SetMinute(strMinute);strSecond=exDateTime.substring(tSp2+1,tSp2+3);Cal.SetSecond(strSecond);}}}
selDate=new Date(Cal.Year,Cal.Month,Cal.Date);winCal=window.open("","DateTimePicker","toolbar=0,status=0,menubar=0,fullscreen=no,width=230,height=245,resizable=0,top="+cnTop+",left="+cnLeft);RenderCal();winCal.focus();}
function RenderCal()
{var vCalHeader;var vCalData;var vCalTime;var i;var j;var SelectStr;var vDayCount=0;var vFirstDay;winCal.document.open();winCal.document.writeln("<html><head><title>"+WindowTitle+"</title>");winCal.document.writeln("<script>var winMain=window.opener;</script>");winCal.document.writeln("</head><body background='"+ThemeBg+"' link="+FontColor+" vlink="+FontColor+"><form name='Calendar'>");vCalHeader="<table border=1 cellpadding=1 cellspacing=1 width=200 valign=\"top\">\n";vCalHeader+="<tr>\n<td colspan='7'><table border=0 width=200 cellpadding=0 cellspacing=0><tr>\n";if(Cal.Scroller=="DROPDOWN")
{vCalHeader+="<td align='left'><select name=\"MonthSelector\" onChange=\"javascript:winMain.Cal.SwitchMth(this.selectedIndex);winMain.RenderCal();\">\n";for(i=0;i<12;i++)
{if(i==Cal.Month)
SelectStr="Selected";else
SelectStr="";vCalHeader+="<option "+SelectStr+" value >"+MonthName[i]+"\n";}
vCalHeader+="</select></td>";vCalHeader+="\n<td align='right'><a href=\"javascript:winMain.Cal.DecYear();winMain.RenderCal()\"><b><font color=\""+YrSelColor+"\"><</font></b></a><font face=\"Verdana\" color=\""+YrSelColor+"\" size=2><b> "+Cal.Year+" </b></font><a href=\"javascript:winMain.Cal.IncYear();winMain.RenderCal()\"><b><font color=\""+YrSelColor+"\">></font></b></a></td></tr></table></td>\n";vCalHeader+="</tr>";}
else if(Cal.Scroller=="ARROW")
{vCalHeader+="<td align='center'><a href='javascript:winMain.Cal.DecYear();winMain.RenderCal();'>- </a></td>";vCalHeader+="<td align='center'><a href='javascript:winMain.Cal.DecMonth();winMain.RenderCal();'>&lt;</a></td>";vCalHeader+="<td align='center' width='70%'><font face='Verdana' size='2' color='"+YrSelColor+"'><b>"+Cal.GetMonthName(ShowLongMonth)+" "+Cal.Year+"</b></font></td>"
vCalHeader+="<td align='center'><a href='javascript:winMain.Cal.IncMonth();winMain.RenderCal();'>&gt;</a></td>";vCalHeader+="<td align='center'><a href='javascript:winMain.Cal.IncYear();winMain.RenderCal();'>+</a></td>";vCalHeader+="</tr></table></td></tr>"}
if((ShowMonthYear)&&(Cal.Scroller=="DROPDOWN"))
vCalHeader+="<tr><td colspan='7'><font face='Verdana' size='2' align='center' color='"+MonthYearColor+"'><b>"+Cal.GetMonthName(ShowLongMonth)+" "+Cal.Year+"</b></font></td></tr>\n";vCalHeader+="<tr bgcolor="+WeekHeadColor+">";var WeekDayName=new Array();if(MondayFirstDay==true)
WeekDayName=WeekDayName2;else
WeekDayName=WeekDayName1;for(i=0;i<7;i++)
{vCalHeader+="<td align='center'><font face='Verdana' size='2'>"+WeekDayName[i].substr(0,WeekChar)+"</font></td>";}
vCalHeader+="</tr>";winCal.document.write(vCalHeader);CalDate=new Date(Cal.Year,Cal.Month);CalDate.setDate(1);vFirstDay=CalDate.getDay();if(MondayFirstDay==true)
{vFirstDay-=1;if(vFirstDay==-1)
vFirstDay=6;}
vCalData="<tr>";for(i=0;i<vFirstDay;i++)
{vCalData=vCalData+GenCell();vDayCount=vDayCount+1;}
for(j=1;j<=Cal.GetMonDays();j++)
{var strCell;vDayCount=vDayCount+1;if((j==dtToday.getDate())&&(Cal.Month==dtToday.getMonth())&&(Cal.Year==dtToday.getFullYear()))
strCell=GenCell(j,true,TodayColor);else
{if((j==selDate.getDate())&&(Cal.Month==selDate.getMonth())&&(Cal.Year==selDate.getFullYear()))
{strCell=GenCell(j,true,SelDateColor);}
else
{if(MondayFirstDay==true)
{if(vDayCount%7==0)
strCell=GenCell(j,false,SundayColor);else if((vDayCount+1)%7==0)
strCell=GenCell(j,false,SaturdayColor);else
strCell=GenCell(j,null,WeekDayColor);}
else
{if(vDayCount%7==0)
strCell=GenCell(j,false,SaturdayColor);else if((vDayCount+6)%7==0)
strCell=GenCell(j,false,SundayColor);else
strCell=GenCell(j,null,WeekDayColor);}}}
vCalData=vCalData+strCell;if((vDayCount%7==0)&&(j<Cal.GetMonDays()))
{vCalData=vCalData+"</tr>\n<tr>";}}
winCal.document.writeln(vCalData);if(Cal.ShowTime)
{var showHour;showHour=Cal.getShowHour();vCalTime="<tr>\n<td colspan='7' align='center'>";vCalTime+="<input type='text' name='hour' maxlength=2 size=1 style=\"WIDTH: 22px\" value="+showHour+" onchange=\"javascript:winMain.Cal.SetHour(this.value)\">";vCalTime+=" : ";vCalTime+="<input type='text' name='minute' maxlength=2 size=1 style=\"WIDTH: 22px\" value="+Cal.Minutes+" onchange=\"javascript:winMain.Cal.SetMinute(this.value)\">";if(Cal.ShowSeconds)
{vCalTime+=" : ";vCalTime+="<input type='text' name='second' maxlength=2 size=1 style=\"WIDTH: 22px\" value="+Cal.Seconds+" onchange=\"javascript:winMain.Cal.SetSecond(this.value)\">";}
if(TimeMode==12)
{var SelectAm=(Cal.AMorPM=="AM")?"Selected":"";var SelectPm=(Cal.AMorPM=="PM")?"Selected":"";vCalTime+="<select name=\"ampm\" onchange=\"javascript:winMain.Cal.SetAmPm(this.options[this.selectedIndex].value);\">";vCalTime+="<option "+SelectAm+" value=\"AM\">AM</option>";vCalTime+="<option "+SelectPm+" value=\"PM\">PM<option>";vCalTime+="</select>";}
vCalTime+="\n</td>\n</tr>";winCal.document.write(vCalTime);}
winCal.document.writeln("\n</table>");winCal.document.writeln("</form></body></html>");winCal.document.close();}
function GenCell(pValue,pHighLight,pColor)
{var PValue;var PCellStr;var vColor;var vHLstr1;var vHlstr2;var vTimeStr;if(pValue==null)
PValue="";else
PValue=pValue;if(pColor!=null)
vColor="bgcolor=\""+pColor+"\"";else
vColor="";if((pHighLight!=null)&&(pHighLight))
{vHLstr1="color='red'><b>";vHLstr2="</b>";}
else
{vHLstr1=">";vHLstr2="";}
if(Cal.ShowTime)
{vTimeStr="winMain.document.getElementById('"+Cal.Ctrl+"').value+=' '+"+"winMain.Cal.getShowHour()"+"+':'+"+"winMain.Cal.Minutes";if(Cal.ShowSeconds)
vTimeStr+="+':'+"+"winMain.Cal.Seconds";if(TimeMode==12)
vTimeStr+="+' '+winMain.Cal.AMorPM";}
else
vTimeStr="";PCellStr="<td "+vColor+" width="+CellWidth+" align='center'><font face='verdana' size='2'"+vHLstr1+"<a href=\"javascript:winMain.document.getElementById('"+Cal.Ctrl+"').value='"+Cal.FormatDate(PValue)+"';"+vTimeStr+";window.close();\">"+PValue+"</a>"+vHLstr2+"</font></td>";return PCellStr;}
function Calendar(pDate,pCtrl)
{this.Date=pDate.getDate();this.Month=pDate.getMonth();this.Year=pDate.getFullYear();this.Hours=pDate.getHours();if(pDate.getMinutes()<10)
this.Minutes="0"+pDate.getMinutes();else
this.Minutes=pDate.getMinutes();if(pDate.getSeconds()<10)
this.Seconds="0"+pDate.getSeconds();else
this.Seconds=pDate.getSeconds();this.MyWindow=winCal;this.Ctrl=pCtrl;this.Format="ddMMyyyy";this.Separator=DateSeparator;this.ShowTime=false;this.Scroller="DROPDOWN";if(pDate.getHours()<12)
this.AMorPM="AM";else
this.AMorPM="PM";this.ShowSeconds=true;}
function GetMonthIndex(shortMonthName)
{for(i=0;i<12;i++)
{if(MonthName[i].substring(0,3).toUpperCase()==shortMonthName.toUpperCase())
{return i;}}}
Calendar.prototype.GetMonthIndex=GetMonthIndex;function IncYear()
{Cal.Year++;}
Calendar.prototype.IncYear=IncYear;function DecYear()
{Cal.Year--;}
Calendar.prototype.DecYear=DecYear;function IncMonth()
{Cal.Month++;if(Cal.Month>=12)
{Cal.Month=0;Cal.IncYear();}}
Calendar.prototype.IncMonth=IncMonth;function DecMonth()
{Cal.Month--;if(Cal.Month<0)
{Cal.Month=11;Cal.DecYear();}}
Calendar.prototype.DecMonth=DecMonth;function SwitchMth(intMth)
{Cal.Month=intMth;}
Calendar.prototype.SwitchMth=SwitchMth;function SetHour(intHour)
{var MaxHour;var MinHour;if(TimeMode==24)
{MaxHour=23;MinHour=0}
else if(TimeMode==12)
{MaxHour=12;MinHour=1}
else
alert("TimeMode can only be 12 or 24");var HourExp=new RegExp("^\\d\\d");var SingleDigit=new RegExp("\\d");if(SingleDigit.test(intHour))
{intHour="0"+intHour+"";}
if(HourExp.test(intHour)&&(parseInt(intHour,10)<=MaxHour)&&(parseInt(intHour,10)>=MinHour))
{if((TimeMode==12)&&(Cal.AMorPM=="PM"))
{if(parseInt(intHour,10)==12)
Cal.Hours=12;else
Cal.Hours=parseInt(intHour,10)+12;}
else if((TimeMode==12)&&(Cal.AMorPM=="AM"))
{if(intHour==12)
intHour-=12;Cal.Hours=parseInt(intHour,10);}
else if(TimeMode==24)
Cal.Hours=parseInt(intHour,10);}}
Calendar.prototype.SetHour=SetHour;function SetMinute(intMin)
{var MinExp=new RegExp("^\\d\\d$");if(MinExp.test(intMin)&&(intMin<60))
Cal.Minutes=intMin;}
Calendar.prototype.SetMinute=SetMinute;function SetSecond(intSec)
{var SecExp=new RegExp("^\\d\\d$");if(SecExp.test(intSec)&&(intSec<60))
Cal.Seconds=intSec;}
Calendar.prototype.SetSecond=SetSecond;function SetAmPm(pvalue)
{this.AMorPM=pvalue;if(pvalue=="PM")
{this.Hours=(parseInt(this.Hours,10))+12;if(this.Hours==24)
this.Hours=12;}
else if(pvalue=="AM")
this.Hours-=12;}
Calendar.prototype.SetAmPm=SetAmPm;function getShowHour()
{var finalHour;if(TimeMode==12)
{if(parseInt(this.Hours,10)==0)
{this.AMorPM="AM";finalHour=parseInt(this.Hours,10)+12;}
else if(parseInt(this.Hours,10)==12)
{this.AMorPM="PM";finalHour=12;}
else if(this.Hours>12)
{this.AMorPM="PM";if((this.Hours-12)<10)
finalHour="0"+((parseInt(this.Hours,10))-12);else
finalHour=parseInt(this.Hours,10)-12;}
else
{this.AMorPM="AM";if(this.Hours<10)
finalHour="0"+parseInt(this.Hours,10);else
finalHour=this.Hours;}}
else if(TimeMode==24)
{if(this.Hours<10)
finalHour="0"+parseInt(this.Hours,10);else
finalHour=this.Hours;}
return finalHour;}
Calendar.prototype.getShowHour=getShowHour;function GetMonthName(IsLong)
{var Month=MonthName[this.Month];if(IsLong)
return Month;else
return Month.substr(0,3);}
Calendar.prototype.GetMonthName=GetMonthName;function GetMonDays()
{var DaysInMonth=[31,28,31,30,31,30,31,31,30,31,30,31];if(this.IsLeapYear())
{DaysInMonth[1]=29;}
return DaysInMonth[this.Month];}
Calendar.prototype.GetMonDays=GetMonDays;function IsLeapYear()
{if((this.Year%4)==0)
{if((this.Year%100==0)&&(this.Year%400)!=0)
{return false;}
else
{return true;}}
else
{return false;}}
Calendar.prototype.IsLeapYear=IsLeapYear;function FormatDate(pDate)
{var MonthDigit=this.Month+1;if(PrecedeZero==true)
{if(pDate<10)
pDate="0"+pDate;if(MonthDigit<10)
MonthDigit="0"+MonthDigit;}
if(this.Format.toUpperCase()=="DDMMYYYY"){return(pDate+DateSeparator+MonthDigit+DateSeparator+this.Year);}
else if(this.Format.toUpperCase()=="DDMMMYYYY")
return(pDate+DateSeparator+this.GetMonthName(false)+DateSeparator+this.Year);else if(this.Format.toUpperCase()=="MMDDYYYY")
return(MonthDigit+DateSeparator+pDate+DateSeparator+this.Year);else if(this.Format.toUpperCase()=="MMMDDYYYY")
return(this.GetMonthName(false)+DateSeparator+pDate+DateSeparator+this.Year);else if(this.Format.toUpperCase()=="YYYYMMDD")
return(this.Year+DateSeparator+MonthDigit+DateSeparator+pDate);else if(this.Format.toUpperCase()=="YYYYMMMDD")
return(this.Year+DateSeparator+this.GetMonthName(false)+DateSeparator+pDate);else
return(pDate+DateSeparator+MonthDigit+DateSeparator+this.Year);}
Calendar.prototype.FormatDate=FormatDate;
/* jquery.js */
(function(){if(window.jQuery)
var _jQuery=window.jQuery;var jQuery=window.jQuery=function(selector,context){return new jQuery.prototype.init(selector,context);};if(window.$)
var _$=window.$;window.$=jQuery;var quickExpr=/^[^<]*(<(.|\s)+>)[^>]*$|^#(\w+)$/;var isSimple=/^.[^:#\[\.]*$/;jQuery.fn=jQuery.prototype={init:function(selector,context){selector=selector||document;if(selector.nodeType){this[0]=selector;this.length=1;return this;}else if(typeof selector=="string"){var match=quickExpr.exec(selector);if(match&&(match[1]||!context)){if(match[1])
selector=jQuery.clean([match[1]],context);else{var elem=document.getElementById(match[3]);if(elem)
if(elem.id!=match[3])
return jQuery().find(selector);else{this[0]=elem;this.length=1;return this;}
else
selector=[];}}else
return new jQuery(context).find(selector);}else if(jQuery.isFunction(selector))
return new jQuery(document)[jQuery.fn.ready?"ready":"load"](selector);return this.setArray(selector.constructor==Array&&selector||(selector.jquery||selector.length&&selector!=window&&!selector.nodeType&&selector[0]!=undefined&&selector[0].nodeType)&&jQuery.makeArray(selector)||[selector]);},jquery:"1.2.2",size:function(){return this.length;},length:0,get:function(num){return num==undefined?jQuery.makeArray(this):this[num];},pushStack:function(elems){var ret=jQuery(elems);ret.prevObject=this;return ret;},setArray:function(elems){this.length=0;Array.prototype.push.apply(this,elems);return this;},each:function(callback,args){return jQuery.each(this,callback,args);},index:function(elem){var ret=-1;this.each(function(i){if(this==elem)
ret=i;});return ret;},attr:function(name,value,type){var options=name;if(name.constructor==String)
if(value==undefined)
return this.length&&jQuery[type||"attr"](this[0],name)||undefined;else{options={};options[name]=value;}
return this.each(function(i){for(name in options)
jQuery.attr(type?this.style:this,name,jQuery.prop(this,options[name],type,i,name));});},css:function(key,value){if((key=='width'||key=='height')&&parseFloat(value)<0)
value=undefined;return this.attr(key,value,"curCSS");},text:function(text){if(typeof text!="object"&&text!=null)
return this.empty().append((this[0]&&this[0].ownerDocument||document).createTextNode(text));var ret="";jQuery.each(text||this,function(){jQuery.each(this.childNodes,function(){if(this.nodeType!=8)
ret+=this.nodeType!=1?this.nodeValue:jQuery.fn.text([this]);});});return ret;},wrapAll:function(html){if(this[0])
jQuery(html,this[0].ownerDocument).clone().insertBefore(this[0]).map(function(){var elem=this;while(elem.firstChild)
elem=elem.firstChild;return elem;}).append(this);return this;},wrapInner:function(html){return this.each(function(){jQuery(this).contents().wrapAll(html);});},wrap:function(html){return this.each(function(){jQuery(this).wrapAll(html);});},append:function(){return this.domManip(arguments,true,false,function(elem){if(this.nodeType==1)
this.appendChild(elem);});},prepend:function(){return this.domManip(arguments,true,true,function(elem){if(this.nodeType==1)
this.insertBefore(elem,this.firstChild);});},before:function(){return this.domManip(arguments,false,false,function(elem){this.parentNode.insertBefore(elem,this);});},after:function(){return this.domManip(arguments,false,true,function(elem){this.parentNode.insertBefore(elem,this.nextSibling);});},end:function(){return this.prevObject||jQuery([]);},find:function(selector){var elems=jQuery.map(this,function(elem){return jQuery.find(selector,elem);});return this.pushStack(/[^+>] [^+>]/.test(selector)||selector.indexOf("..")>-1?jQuery.unique(elems):elems);},clone:function(events){var ret=this.map(function(){if(jQuery.browser.msie&&!jQuery.isXMLDoc(this)){var clone=this.cloneNode(true),container=document.createElement("div"),container2=document.createElement("div");container.appendChild(clone);container2.innerHTML=container.innerHTML;return container2.firstChild;}else
return this.cloneNode(true);});var clone=ret.find("*").andSelf().each(function(){if(this[expando]!=undefined)
this[expando]=null;});if(events===true)
this.find("*").andSelf().each(function(i){if(this.nodeType==3)
return;var events=jQuery.data(this,"events");for(var type in events)
for(var handler in events[type])
jQuery.event.add(clone[i],type,events[type][handler],events[type][handler].data);});return ret;},filter:function(selector){return this.pushStack(jQuery.isFunction(selector)&&jQuery.grep(this,function(elem,i){return selector.call(elem,i);})||jQuery.multiFilter(selector,this));},not:function(selector){if(selector.constructor==String)
if(isSimple.test(selector))
return this.pushStack(jQuery.multiFilter(selector,this,true));else
selector=jQuery.multiFilter(selector,this);var isArrayLike=selector.length&&selector[selector.length-1]!==undefined&&!selector.nodeType;return this.filter(function(){return isArrayLike?jQuery.inArray(this,selector)<0:this!=selector;});},add:function(selector){return!selector?this:this.pushStack(jQuery.merge(this.get(),selector.constructor==String?jQuery(selector).get():selector.length!=undefined&&(!selector.nodeName||jQuery.nodeName(selector,"form"))?selector:[selector]));},is:function(selector){return selector?jQuery.multiFilter(selector,this).length>0:false;},hasClass:function(selector){return this.is("."+selector);},val:function(value){if(value==undefined){if(this.length){var elem=this[0];if(jQuery.nodeName(elem,"select")){var index=elem.selectedIndex,values=[],options=elem.options,one=elem.type=="select-one";if(index<0)
return null;for(var i=one?index:0,max=one?index+1:options.length;i<max;i++){var option=options[i];if(option.selected){value=jQuery.browser.msie&&!option.attributes.value.specified?option.text:option.value;if(one)
return value;values.push(value);}}
return values;}else
return(this[0].value||"").replace(/\r/g,"");}
return undefined;}
return this.each(function(){if(this.nodeType!=1)
return;if(value.constructor==Array&&/radio|checkbox/.test(this.type))
this.checked=(jQuery.inArray(this.value,value)>=0||jQuery.inArray(this.name,value)>=0);else if(jQuery.nodeName(this,"select")){var values=value.constructor==Array?value:[value];jQuery("option",this).each(function(){this.selected=(jQuery.inArray(this.value,values)>=0||jQuery.inArray(this.text,values)>=0);});if(!values.length)
this.selectedIndex=-1;}else
this.value=value;});},html:function(value){return value==undefined?(this.length?this[0].innerHTML:null):this.empty().append(value);},replaceWith:function(value){return this.after(value).remove();},eq:function(i){return this.slice(i,i+1);},slice:function(){return this.pushStack(Array.prototype.slice.apply(this,arguments));},map:function(callback){return this.pushStack(jQuery.map(this,function(elem,i){return callback.call(elem,i,elem);}));},andSelf:function(){return this.add(this.prevObject);},domManip:function(args,table,reverse,callback){var clone=this.length>1,elems;return this.each(function(){if(!elems){elems=jQuery.clean(args,this.ownerDocument);if(reverse)
elems.reverse();}
var obj=this;if(table&&jQuery.nodeName(this,"table")&&jQuery.nodeName(elems[0],"tr"))
obj=this.getElementsByTagName("tbody")[0]||this.appendChild(this.ownerDocument.createElement("tbody"));var scripts=jQuery([]);jQuery.each(elems,function(){var elem=clone?jQuery(this).clone(true)[0]:this;if(jQuery.nodeName(elem,"script")){scripts=scripts.add(elem);}else{if(elem.nodeType==1)
scripts=scripts.add(jQuery("script",elem).remove());callback.call(obj,elem);}});scripts.each(evalScript);});}};jQuery.prototype.init.prototype=jQuery.prototype;function evalScript(i,elem){if(elem.src)
jQuery.ajax({url:elem.src,async:false,dataType:"script"});else
jQuery.globalEval(elem.text||elem.textContent||elem.innerHTML||"");if(elem.parentNode)
elem.parentNode.removeChild(elem);}
jQuery.extend=jQuery.fn.extend=function(){var target=arguments[0]||{},i=1,length=arguments.length,deep=false,options;if(target.constructor==Boolean){deep=target;target=arguments[1]||{};i=2;}
if(typeof target!="object"&&typeof target!="function")
target={};if(length==1){target=this;i=0;}
for(;i<length;i++)
if((options=arguments[i])!=null)
for(var name in options){if(target===options[name])
continue;if(deep&&options[name]&&typeof options[name]=="object"&&target[name]&&!options[name].nodeType)
target[name]=jQuery.extend(target[name],options[name]);else if(options[name]!=undefined)
target[name]=options[name];}
return target;};var expando="jQuery"+(new Date()).getTime(),uuid=0,windowData={};var exclude=/z-?index|font-?weight|opacity|zoom|line-?height/i;jQuery.extend({noConflict:function(deep){window.$=_$;if(deep)
window.jQuery=_jQuery;return jQuery;},isFunction:function(fn){return!!fn&&typeof fn!="string"&&!fn.nodeName&&fn.constructor!=Array&&/function/i.test(fn+"");},isXMLDoc:function(elem){return elem.documentElement&&!elem.body||elem.tagName&&elem.ownerDocument&&!elem.ownerDocument.body;},globalEval:function(data){data=jQuery.trim(data);if(data){var head=document.getElementsByTagName("head")[0]||document.documentElement,script=document.createElement("script");script.type="text/javascript";if(jQuery.browser.msie)
script.text=data;else
script.appendChild(document.createTextNode(data));head.appendChild(script);head.removeChild(script);}},nodeName:function(elem,name){return elem.nodeName&&elem.nodeName.toUpperCase()==name.toUpperCase();},cache:{},data:function(elem,name,data){elem=elem==window?windowData:elem;var id=elem[expando];if(!id)
id=elem[expando]=++uuid;if(name&&!jQuery.cache[id])
jQuery.cache[id]={};if(data!=undefined)
jQuery.cache[id][name]=data;return name?jQuery.cache[id][name]:id;},removeData:function(elem,name){elem=elem==window?windowData:elem;var id=elem[expando];if(name){if(jQuery.cache[id]){delete jQuery.cache[id][name];name="";for(name in jQuery.cache[id])
break;if(!name)
jQuery.removeData(elem);}}else{try{delete elem[expando];}catch(e){if(elem.removeAttribute)
elem.removeAttribute(expando);}
delete jQuery.cache[id];}},each:function(object,callback,args){if(args){if(object.length==undefined){for(var name in object)
if(callback.apply(object[name],args)===false)
break;}else
for(var i=0,length=object.length;i<length;i++)
if(callback.apply(object[i],args)===false)
break;}else{if(object.length==undefined){for(var name in object)
if(callback.call(object[name],name,object[name])===false)
break;}else
for(var i=0,length=object.length,value=object[0];i<length&&callback.call(value,i,value)!==false;value=object[++i]){}}
return object;},prop:function(elem,value,type,i,name){if(jQuery.isFunction(value))
value=value.call(elem,i);return value&&value.constructor==Number&&type=="curCSS"&&!exclude.test(name)?value+"px":value;},className:{add:function(elem,classNames){jQuery.each((classNames||"").split(/\s+/),function(i,className){if(elem.nodeType==1&&!jQuery.className.has(elem.className,className))
elem.className+=(elem.className?" ":"")+className;});},remove:function(elem,classNames){if(elem.nodeType==1)
elem.className=classNames!=undefined?jQuery.grep(elem.className.split(/\s+/),function(className){return!jQuery.className.has(classNames,className);}).join(" "):"";},has:function(elem,className){return jQuery.inArray(className,(elem.className||elem).toString().split(/\s+/))>-1;}},swap:function(elem,options,callback){var old={};for(var name in options){old[name]=elem.style[name];elem.style[name]=options[name];}
callback.call(elem);for(var name in options)
elem.style[name]=old[name];},css:function(elem,name,force){if(name=="width"||name=="height"){var val,props={position:"absolute",visibility:"hidden",display:"block"},which=name=="width"?["Left","Right"]:["Top","Bottom"];function getWH(){val=name=="width"?elem.offsetWidth:elem.offsetHeight;var padding=0,border=0;jQuery.each(which,function(){padding+=parseFloat(jQuery.curCSS(elem,"padding"+this,true))||0;border+=parseFloat(jQuery.curCSS(elem,"border"+this+"Width",true))||0;});val-=Math.round(padding+border);}
if(jQuery(elem).is(":visible"))
getWH();else
jQuery.swap(elem,props,getWH);return Math.max(0,val);}
return jQuery.curCSS(elem,name,force);},curCSS:function(elem,name,force){var ret;function color(elem){if(!jQuery.browser.safari)
return false;var ret=document.defaultView.getComputedStyle(elem,null);return!ret||ret.getPropertyValue("color")=="";}
if(name=="opacity"&&jQuery.browser.msie){ret=jQuery.attr(elem.style,"opacity");return ret==""?"1":ret;}
if(jQuery.browser.opera&&name=="display"){var save=elem.style.display;elem.style.display="block";elem.style.display=save;}
if(name.match(/float/i))
name=styleFloat;if(!force&&elem.style&&elem.style[name])
ret=elem.style[name];else if(document.defaultView&&document.defaultView.getComputedStyle){if(name.match(/float/i))
name="float";name=name.replace(/([A-Z])/g,"-$1").toLowerCase();var getComputedStyle=document.defaultView.getComputedStyle(elem,null);if(getComputedStyle&&!color(elem))
ret=getComputedStyle.getPropertyValue(name);else{var swap=[],stack=[];for(var a=elem;a&&color(a);a=a.parentNode)
stack.unshift(a);for(var i=0;i<stack.length;i++)
if(color(stack[i])){swap[i]=stack[i].style.display;stack[i].style.display="block";}
ret=name=="display"&&swap[stack.length-1]!=null?"none":(getComputedStyle&&getComputedStyle.getPropertyValue(name))||"";for(var i=0;i<swap.length;i++)
if(swap[i]!=null)
stack[i].style.display=swap[i];}
if(name=="opacity"&&ret=="")
ret="1";}else if(elem.currentStyle){var camelCase=name.replace(/\-(\w)/g,function(all,letter){return letter.toUpperCase();});ret=elem.currentStyle[name]||elem.currentStyle[camelCase];if(!/^\d+(px)?$/i.test(ret)&&/^\d/.test(ret)){var style=elem.style.left,runtimeStyle=elem.runtimeStyle.left;elem.runtimeStyle.left=elem.currentStyle.left;elem.style.left=ret||0;ret=elem.style.pixelLeft+"px";elem.style.left=style;elem.runtimeStyle.left=runtimeStyle;}}
return ret;},clean:function(elems,context){var ret=[];context=context||document;if(typeof context.createElement=='undefined')
context=context.ownerDocument||context[0]&&context[0].ownerDocument||document;jQuery.each(elems,function(i,elem){if(!elem)
return;if(elem.constructor==Number)
elem=elem.toString();if(typeof elem=="string"){elem=elem.replace(/(<(\w+)[^>]*?)\/>/g,function(all,front,tag){return tag.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i)?all:front+"></"+tag+">";});var tags=jQuery.trim(elem).toLowerCase(),div=context.createElement("div");var wrap=!tags.indexOf("<opt")&&[1,"<select multiple='multiple'>","</select>"]||!tags.indexOf("<leg")&&[1,"<fieldset>","</fieldset>"]||tags.match(/^<(thead|tbody|tfoot|colg|cap)/)&&[1,"<table>","</table>"]||!tags.indexOf("<tr")&&[2,"<table><tbody>","</tbody></table>"]||(!tags.indexOf("<td")||!tags.indexOf("<th"))&&[3,"<table><tbody><tr>","</tr></tbody></table>"]||!tags.indexOf("<col")&&[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"]||jQuery.browser.msie&&[1,"div<div>","</div>"]||[0,"",""];div.innerHTML=wrap[1]+elem+wrap[2];while(wrap[0]--)
div=div.lastChild;if(jQuery.browser.msie){var tbody=!tags.indexOf("<table")&&tags.indexOf("<tbody")<0?div.firstChild&&div.firstChild.childNodes:wrap[1]=="<table>"&&tags.indexOf("<tbody")<0?div.childNodes:[];for(var j=tbody.length-1;j>=0;--j)
if(jQuery.nodeName(tbody[j],"tbody")&&!tbody[j].childNodes.length)
tbody[j].parentNode.removeChild(tbody[j]);if(/^\s/.test(elem))
div.insertBefore(context.createTextNode(elem.match(/^\s*/)[0]),div.firstChild);}
elem=jQuery.makeArray(div.childNodes);}
if(elem.length===0&&(!jQuery.nodeName(elem,"form")&&!jQuery.nodeName(elem,"select")))
return;if(elem[0]==undefined||jQuery.nodeName(elem,"form")||elem.options)
ret.push(elem);else
ret=jQuery.merge(ret,elem);});return ret;},attr:function(elem,name,value){if(!elem||elem.nodeType==3||elem.nodeType==8)
return undefined;var fix=jQuery.isXMLDoc(elem)?{}:jQuery.props;if(name=="selected"&&jQuery.browser.safari)
elem.parentNode.selectedIndex;if(fix[name]){if(value!=undefined)
elem[fix[name]]=value;return elem[fix[name]];}else if(jQuery.browser.msie&&name=="style")
return jQuery.attr(elem.style,"cssText",value);else if(value==undefined&&jQuery.browser.msie&&jQuery.nodeName(elem,"form")&&(name=="action"||name=="method"))
return elem.getAttributeNode(name).nodeValue;else if(elem.tagName){if(value!=undefined){if(name=="type"&&jQuery.nodeName(elem,"input")&&elem.parentNode)
throw"type property can't be changed";elem.setAttribute(name,""+value);}
if(jQuery.browser.msie&&/href|src/.test(name)&&!jQuery.isXMLDoc(elem))
return elem.getAttribute(name,2);return elem.getAttribute(name);}else{if(name=="opacity"&&jQuery.browser.msie){if(value!=undefined){elem.zoom=1;elem.filter=(elem.filter||"").replace(/alpha\([^)]*\)/,"")+
(parseFloat(value).toString()=="NaN"?"":"alpha(opacity="+value*100+")");}
return elem.filter&&elem.filter.indexOf("opacity=")>=0?(parseFloat(elem.filter.match(/opacity=([^)]*)/)[1])/100).toString():"";}
name=name.replace(/-([a-z])/ig,function(all,letter){return letter.toUpperCase();});if(value!=undefined)
elem[name]=value;return elem[name];}},trim:function(text){return(text||"").replace(/^\s+|\s+$/g,"");},makeArray:function(array){var ret=[];if(typeof array!="array")
for(var i=0,length=array.length;i<length;i++)
ret.push(array[i]);else
ret=array.slice(0);return ret;},inArray:function(elem,array){for(var i=0,length=array.length;i<length;i++)
if(array[i]==elem)
return i;return-1;},merge:function(first,second){if(jQuery.browser.msie){for(var i=0;second[i];i++)
if(second[i].nodeType!=8)
first.push(second[i]);}else
for(var i=0;second[i];i++)
first.push(second[i]);return first;},unique:function(array){var ret=[],done={};try{for(var i=0,length=array.length;i<length;i++){var id=jQuery.data(array[i]);if(!done[id]){done[id]=true;ret.push(array[i]);}}}catch(e){ret=array;}
return ret;},grep:function(elems,callback,inv){if(typeof callback=="string")
callback=eval("false||function(a,i){return "+callback+"}");var ret=[];for(var i=0,length=elems.length;i<length;i++)
if(!inv&&callback(elems[i],i)||inv&&!callback(elems[i],i))
ret.push(elems[i]);return ret;},map:function(elems,callback){var ret=[];for(var i=0,length=elems.length;i<length;i++){var value=callback(elems[i],i);if(value!==null&&value!=undefined){if(value.constructor!=Array)
value=[value];ret=ret.concat(value);}}
return ret;}});var userAgent=navigator.userAgent.toLowerCase();jQuery.browser={version:(userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[])[1],safari:/webkit/.test(userAgent),opera:/opera/.test(userAgent),msie:/msie/.test(userAgent)&&!/opera/.test(userAgent),mozilla:/mozilla/.test(userAgent)&&!/(compatible|webkit)/.test(userAgent)};var styleFloat=jQuery.browser.msie?"styleFloat":"cssFloat";jQuery.extend({boxModel:!jQuery.browser.msie||document.compatMode=="CSS1Compat",props:{"for":"htmlFor","class":"className","float":styleFloat,cssFloat:styleFloat,styleFloat:styleFloat,innerHTML:"innerHTML",className:"className",value:"value",disabled:"disabled",checked:"checked",readonly:"readOnly",selected:"selected",maxlength:"maxLength",selectedIndex:"selectedIndex",defaultValue:"defaultValue",tagName:"tagName",nodeName:"nodeName"}});jQuery.each({parent:"elem.parentNode",parents:"jQuery.dir(elem,'parentNode')",next:"jQuery.nth(elem,2,'nextSibling')",prev:"jQuery.nth(elem,2,'previousSibling')",nextAll:"jQuery.dir(elem,'nextSibling')",prevAll:"jQuery.dir(elem,'previousSibling')",siblings:"jQuery.sibling(elem.parentNode.firstChild,elem)",children:"jQuery.sibling(elem.firstChild)",contents:"jQuery.nodeName(elem,'iframe')?elem.contentDocument||elem.contentWindow.document:jQuery.makeArray(elem.childNodes)"},function(name,fn){fn=eval("false||function(elem){return "+fn+"}");jQuery.fn[name]=function(selector){var ret=jQuery.map(this,fn);if(selector&&typeof selector=="string")
ret=jQuery.multiFilter(selector,ret);return this.pushStack(jQuery.unique(ret));};});jQuery.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(name,original){jQuery.fn[name]=function(){var args=arguments;return this.each(function(){for(var i=0,length=args.length;i<length;i++)
jQuery(args[i])[original](this);});};});jQuery.each({removeAttr:function(name){jQuery.attr(this,name,"");if(this.nodeType==1)
this.removeAttribute(name);},addClass:function(classNames){jQuery.className.add(this,classNames);},removeClass:function(classNames){jQuery.className.remove(this,classNames);},toggleClass:function(classNames){jQuery.className[jQuery.className.has(this,classNames)?"remove":"add"](this,classNames);},remove:function(selector){if(!selector||jQuery.filter(selector,[this]).r.length){jQuery("*",this).add(this).each(function(){jQuery.event.remove(this);jQuery.removeData(this);});if(this.parentNode)
this.parentNode.removeChild(this);}},empty:function(){jQuery(">*",this).remove();while(this.firstChild)
this.removeChild(this.firstChild);}},function(name,fn){jQuery.fn[name]=function(){return this.each(fn,arguments);};});jQuery.each(["Height","Width"],function(i,name){var type=name.toLowerCase();jQuery.fn[type]=function(size){return this[0]==window?jQuery.browser.opera&&document.body["client"+name]||jQuery.browser.safari&&window["inner"+name]||document.compatMode=="CSS1Compat"&&document.documentElement["client"+name]||document.body["client"+name]:this[0]==document?Math.max(Math.max(document.body["scroll"+name],document.documentElement["scroll"+name]),Math.max(document.body["offset"+name],document.documentElement["offset"+name])):size==undefined?(this.length?jQuery.css(this[0],type):null):this.css(type,size.constructor==String?size:size+"px");};});var chars=jQuery.browser.safari&&parseInt(jQuery.browser.version)<417?"(?:[\\w*_-]|\\\\.)":"(?:[\\w\u0128-\uFFFF*_-]|\\\\.)",quickChild=new RegExp("^>\\s*("+chars+"+)"),quickID=new RegExp("^("+chars+"+)(#)("+chars+"+)"),quickClass=new RegExp("^([#.]?)("+chars+"*)");jQuery.extend({expr:{"":"m[2]=='*'||jQuery.nodeName(a,m[2])","#":"a.getAttribute('id')==m[2]",":":{lt:"i<m[3]-0",gt:"i>m[3]-0",nth:"m[3]-0==i",eq:"m[3]-0==i",first:"i==0",last:"i==r.length-1",even:"i%2==0",odd:"i%2","first-child":"a.parentNode.getElementsByTagName('*')[0]==a","last-child":"jQuery.nth(a.parentNode.lastChild,1,'previousSibling')==a","only-child":"!jQuery.nth(a.parentNode.lastChild,2,'previousSibling')",parent:"a.firstChild",empty:"!a.firstChild",contains:"(a.textContent||a.innerText||jQuery(a).text()||'').indexOf(m[3])>=0",visible:'"hidden"!=a.type&&jQuery.css(a,"display")!="none"&&jQuery.css(a,"visibility")!="hidden"',hidden:'"hidden"==a.type||jQuery.css(a,"display")=="none"||jQuery.css(a,"visibility")=="hidden"',enabled:"!a.disabled",disabled:"a.disabled",checked:"a.checked",selected:"a.selected||jQuery.attr(a,'selected')",text:"'text'==a.type",radio:"'radio'==a.type",checkbox:"'checkbox'==a.type",file:"'file'==a.type",password:"'password'==a.type",submit:"'submit'==a.type",image:"'image'==a.type",reset:"'reset'==a.type",button:'"button"==a.type||jQuery.nodeName(a,"button")',input:"/input|select|textarea|button/i.test(a.nodeName)",has:"jQuery.find(m[3],a).length",header:"/h\\d/i.test(a.nodeName)",animated:"jQuery.grep(jQuery.timers,function(fn){return a==fn.elem;}).length"}},parse:[/^(\[) *@?([\w-]+) *([!*$^~=]*) *('?"?)(.*?)\4 *\]/,/^(:)([\w-]+)\("?'?(.*?(\(.*?\))?[^(]*?)"?'?\)/,new RegExp("^([:.#]*)("+chars+"+)")],multiFilter:function(expr,elems,not){var old,cur=[];while(expr&&expr!=old){old=expr;var f=jQuery.filter(expr,elems,not);expr=f.t.replace(/^\s*,\s*/,"");cur=not?elems=f.r:jQuery.merge(cur,f.r);}
return cur;},find:function(t,context){if(typeof t!="string")
return[t];if(context&&context.nodeType!=1&&context.nodeType!=9)
return[];context=context||document;var ret=[context],done=[],last,nodeName;while(t&&last!=t){var r=[];last=t;t=jQuery.trim(t);var foundToken=false;var re=quickChild;var m=re.exec(t);if(m){nodeName=m[1].toUpperCase();for(var i=0;ret[i];i++)
for(var c=ret[i].firstChild;c;c=c.nextSibling)
if(c.nodeType==1&&(nodeName=="*"||c.nodeName.toUpperCase()==nodeName))
r.push(c);ret=r;t=t.replace(re,"");if(t.indexOf(" ")==0)continue;foundToken=true;}else{re=/^([>+~])\s*(\w*)/i;if((m=re.exec(t))!=null){r=[];var merge={};nodeName=m[2].toUpperCase();m=m[1];for(var j=0,rl=ret.length;j<rl;j++){var n=m=="~"||m=="+"?ret[j].nextSibling:ret[j].firstChild;for(;n;n=n.nextSibling)
if(n.nodeType==1){var id=jQuery.data(n);if(m=="~"&&merge[id])break;if(!nodeName||n.nodeName.toUpperCase()==nodeName){if(m=="~")merge[id]=true;r.push(n);}
if(m=="+")break;}}
ret=r;t=jQuery.trim(t.replace(re,""));foundToken=true;}}
if(t&&!foundToken){if(!t.indexOf(",")){if(context==ret[0])ret.shift();done=jQuery.merge(done,ret);r=ret=[context];t=" "+t.substr(1,t.length);}else{var re2=quickID;var m=re2.exec(t);if(m){m=[0,m[2],m[3],m[1]];}else{re2=quickClass;m=re2.exec(t);}
m[2]=m[2].replace(/\\/g,"");var elem=ret[ret.length-1];if(m[1]=="#"&&elem&&elem.getElementById&&!jQuery.isXMLDoc(elem)){var oid=elem.getElementById(m[2]);if((jQuery.browser.msie||jQuery.browser.opera)&&oid&&typeof oid.id=="string"&&oid.id!=m[2])
oid=jQuery('[@id="'+m[2]+'"]',elem)[0];ret=r=oid&&(!m[3]||jQuery.nodeName(oid,m[3]))?[oid]:[];}else{for(var i=0;ret[i];i++){var tag=m[1]=="#"&&m[3]?m[3]:m[1]!=""||m[0]==""?"*":m[2];if(tag=="*"&&ret[i].nodeName.toLowerCase()=="object")
tag="param";r=jQuery.merge(r,ret[i].getElementsByTagName(tag));}
if(m[1]==".")
r=jQuery.classFilter(r,m[2]);if(m[1]=="#"){var tmp=[];for(var i=0;r[i];i++)
if(r[i].getAttribute("id")==m[2]){tmp=[r[i]];break;}
r=tmp;}
ret=r;}
t=t.replace(re2,"");}}
if(t){var val=jQuery.filter(t,r);ret=r=val.r;t=jQuery.trim(val.t);}}
if(t)
ret=[];if(ret&&context==ret[0])
ret.shift();done=jQuery.merge(done,ret);return done;},classFilter:function(r,m,not){m=" "+m+" ";var tmp=[];for(var i=0;r[i];i++){var pass=(" "+r[i].className+" ").indexOf(m)>=0;if(!not&&pass||not&&!pass)
tmp.push(r[i]);}
return tmp;},filter:function(t,r,not){var last;while(t&&t!=last){last=t;var p=jQuery.parse,m;for(var i=0;p[i];i++){m=p[i].exec(t);if(m){t=t.substring(m[0].length);m[2]=m[2].replace(/\\/g,"");break;}}
if(!m)
break;if(m[1]==":"&&m[2]=="not")
r=isSimple.test(m[3])?jQuery.filter(m[3],r,true).r:jQuery(r).not(m[3]);else if(m[1]==".")
r=jQuery.classFilter(r,m[2],not);else if(m[1]=="["){var tmp=[],type=m[3];for(var i=0,rl=r.length;i<rl;i++){var a=r[i],z=a[jQuery.props[m[2]]||m[2]];if(z==null||/href|src|selected/.test(m[2]))
z=jQuery.attr(a,m[2])||'';if((type==""&&!!z||type=="="&&z==m[5]||type=="!="&&z!=m[5]||type=="^="&&z&&!z.indexOf(m[5])||type=="$="&&z.substr(z.length-m[5].length)==m[5]||(type=="*="||type=="~=")&&z.indexOf(m[5])>=0)^not)
tmp.push(a);}
r=tmp;}else if(m[1]==":"&&m[2]=="nth-child"){var merge={},tmp=[],test=/(-?)(\d*)n((?:\+|-)?\d*)/.exec(m[3]=="even"&&"2n"||m[3]=="odd"&&"2n+1"||!/\D/.test(m[3])&&"0n+"+m[3]||m[3]),first=(test[1]+(test[2]||1))-0,last=test[3]-0;for(var i=0,rl=r.length;i<rl;i++){var node=r[i],parentNode=node.parentNode,id=jQuery.data(parentNode);if(!merge[id]){var c=1;for(var n=parentNode.firstChild;n;n=n.nextSibling)
if(n.nodeType==1)
n.nodeIndex=c++;merge[id]=true;}
var add=false;if(first==0){if(node.nodeIndex==last)
add=true;}else if((node.nodeIndex-last)%first==0&&(node.nodeIndex-last)/first>=0)
add=true;if(add^not)
tmp.push(node);}
r=tmp;}else{var f=jQuery.expr[m[1]];if(typeof f!="string")
f=jQuery.expr[m[1]][m[2]];f=eval("false||function(a,i){return "+f+"}");r=jQuery.grep(r,f,not);}}
return{r:r,t:t};},dir:function(elem,dir){var matched=[];var cur=elem[dir];while(cur&&cur!=document){if(cur.nodeType==1)
matched.push(cur);cur=cur[dir];}
return matched;},nth:function(cur,result,dir,elem){result=result||1;var num=0;for(;cur;cur=cur[dir])
if(cur.nodeType==1&&++num==result)
break;return cur;},sibling:function(n,elem){var r=[];for(;n;n=n.nextSibling){if(n.nodeType==1&&(!elem||n!=elem))
r.push(n);}
return r;}});jQuery.event={add:function(elem,types,handler,data){if(elem.nodeType==3||elem.nodeType==8)
return;if(jQuery.browser.msie&&elem.setInterval!=undefined)
elem=window;if(!handler.guid)
handler.guid=this.guid++;if(data!=undefined){var fn=handler;handler=function(){return fn.apply(this,arguments);};handler.data=data;handler.guid=fn.guid;}
var events=jQuery.data(elem,"events")||jQuery.data(elem,"events",{}),handle=jQuery.data(elem,"handle")||jQuery.data(elem,"handle",function(){var val;if(typeof jQuery=="undefined"||jQuery.event.triggered)
return val;val=jQuery.event.handle.apply(arguments.callee.elem,arguments);return val;});handle.elem=elem;jQuery.each(types.split(/\s+/),function(index,type){var parts=type.split(".");type=parts[0];handler.type=parts[1];var handlers=events[type];if(!handlers){handlers=events[type]={};if(!jQuery.event.special[type]||jQuery.event.special[type].setup.call(elem)===false){if(elem.addEventListener)
elem.addEventListener(type,handle,false);else if(elem.attachEvent)
elem.attachEvent("on"+type,handle);}}
handlers[handler.guid]=handler;jQuery.event.global[type]=true;});elem=null;},guid:1,global:{},remove:function(elem,types,handler){if(elem.nodeType==3||elem.nodeType==8)
return;var events=jQuery.data(elem,"events"),ret,index;if(events){if(types==undefined)
for(var type in events)
this.remove(elem,type);else{if(types.type){handler=types.handler;types=types.type;}
jQuery.each(types.split(/\s+/),function(index,type){var parts=type.split(".");type=parts[0];if(events[type]){if(handler)
delete events[type][handler.guid];else
for(handler in events[type])
if(!parts[1]||events[type][handler].type==parts[1])
delete events[type][handler];for(ret in events[type])break;if(!ret){if(!jQuery.event.special[type]||jQuery.event.special[type].teardown.call(elem)===false){if(elem.removeEventListener)
elem.removeEventListener(type,jQuery.data(elem,"handle"),false);else if(elem.detachEvent)
elem.detachEvent("on"+type,jQuery.data(elem,"handle"));}
ret=null;delete events[type];}}});}
for(ret in events)break;if(!ret){var handle=jQuery.data(elem,"handle");if(handle)handle.elem=null;jQuery.removeData(elem,"events");jQuery.removeData(elem,"handle");}}},trigger:function(type,data,elem,donative,extra){data=jQuery.makeArray(data||[]);if(!elem){if(this.global[type])
jQuery("*").add([window,document]).trigger(type,data);}else{if(elem.nodeType==3||elem.nodeType==8)
return undefined;var val,ret,fn=jQuery.isFunction(elem[type]||null),event=!data[0]||!data[0].preventDefault;if(event)
data.unshift(this.fix({type:type,target:elem}));data[0].type=type;if(jQuery.isFunction(jQuery.data(elem,"handle")))
val=jQuery.data(elem,"handle").apply(elem,data);if(!fn&&elem["on"+type]&&elem["on"+type].apply(elem,data)===false)
val=false;if(event)
data.shift();if(extra&&jQuery.isFunction(extra)){ret=extra.apply(elem,val==null?data:data.concat(val));if(ret!==undefined)
val=ret;}
if(fn&&donative!==false&&val!==false&&!(jQuery.nodeName(elem,'a')&&type=="click")){this.triggered=true;try{elem[type]();}catch(e){}}
this.triggered=false;}
return val;},handle:function(event){var val;event=jQuery.event.fix(event||window.event||{});var parts=event.type.split(".");event.type=parts[0];var handlers=jQuery.data(this,"events")&&jQuery.data(this,"events")[event.type],args=Array.prototype.slice.call(arguments,1);args.unshift(event);for(var j in handlers){var handler=handlers[j];args[0].handler=handler;args[0].data=handler.data;if(!parts[1]||handler.type==parts[1]){var ret=handler.apply(this,args);if(val!==false)
val=ret;if(ret===false){event.preventDefault();event.stopPropagation();}}}
if(jQuery.browser.msie)
event.target=event.preventDefault=event.stopPropagation=event.handler=event.data=null;return val;},fix:function(event){var originalEvent=event;event=jQuery.extend({},originalEvent);event.preventDefault=function(){if(originalEvent.preventDefault)
originalEvent.preventDefault();originalEvent.returnValue=false;};event.stopPropagation=function(){if(originalEvent.stopPropagation)
originalEvent.stopPropagation();originalEvent.cancelBubble=true;};if(!event.target)
event.target=event.srcElement||document;if(event.target.nodeType==3)
event.target=originalEvent.target.parentNode;if(!event.relatedTarget&&event.fromElement)
event.relatedTarget=event.fromElement==event.target?event.toElement:event.fromElement;if(event.pageX==null&&event.clientX!=null){var doc=document.documentElement,body=document.body;event.pageX=event.clientX+(doc&&doc.scrollLeft||body&&body.scrollLeft||0)-(doc.clientLeft||0);event.pageY=event.clientY+(doc&&doc.scrollTop||body&&body.scrollTop||0)-(doc.clientTop||0);}
if(!event.which&&((event.charCode||event.charCode===0)?event.charCode:event.keyCode))
event.which=event.charCode||event.keyCode;if(!event.metaKey&&event.ctrlKey)
event.metaKey=event.ctrlKey;if(!event.which&&event.button)
event.which=(event.button&1?1:(event.button&2?3:(event.button&4?2:0)));return event;},special:{ready:{setup:function(){bindReady();return;},teardown:function(){return;}},mouseenter:{setup:function(){if(jQuery.browser.msie)return false;jQuery(this).bind("mouseover",jQuery.event.special.mouseenter.handler);return true;},teardown:function(){if(jQuery.browser.msie)return false;jQuery(this).unbind("mouseover",jQuery.event.special.mouseenter.handler);return true;},handler:function(event){if(withinElement(event,this))return true;arguments[0].type="mouseenter";return jQuery.event.handle.apply(this,arguments);}},mouseleave:{setup:function(){if(jQuery.browser.msie)return false;jQuery(this).bind("mouseout",jQuery.event.special.mouseleave.handler);return true;},teardown:function(){if(jQuery.browser.msie)return false;jQuery(this).unbind("mouseout",jQuery.event.special.mouseleave.handler);return true;},handler:function(event){if(withinElement(event,this))return true;arguments[0].type="mouseleave";return jQuery.event.handle.apply(this,arguments);}}}};jQuery.fn.extend({bind:function(type,data,fn){return type=="unload"?this.one(type,data,fn):this.each(function(){jQuery.event.add(this,type,fn||data,fn&&data);});},one:function(type,data,fn){return this.each(function(){jQuery.event.add(this,type,function(event){jQuery(this).unbind(event);return(fn||data).apply(this,arguments);},fn&&data);});},unbind:function(type,fn){return this.each(function(){jQuery.event.remove(this,type,fn);});},trigger:function(type,data,fn){return this.each(function(){jQuery.event.trigger(type,data,this,true,fn);});},triggerHandler:function(type,data,fn){if(this[0])
return jQuery.event.trigger(type,data,this[0],false,fn);return undefined;},toggle:function(){var args=arguments;return this.click(function(event){this.lastToggle=0==this.lastToggle?1:0;event.preventDefault();return args[this.lastToggle].apply(this,arguments)||false;});},hover:function(fnOver,fnOut){return this.bind('mouseenter',fnOver).bind('mouseleave',fnOut);},ready:function(fn){bindReady();if(jQuery.isReady)
fn.call(document,jQuery);else
jQuery.readyList.push(function(){return fn.call(this,jQuery);});return this;}});jQuery.extend({isReady:false,readyList:[],ready:function(){if(!jQuery.isReady){jQuery.isReady=true;if(jQuery.readyList){jQuery.each(jQuery.readyList,function(){this.apply(document);});jQuery.readyList=null;}
jQuery(document).triggerHandler("ready");}}});var readyBound=false;function bindReady(){if(readyBound)return;readyBound=true;if(document.addEventListener&&!jQuery.browser.opera)
document.addEventListener("DOMContentLoaded",jQuery.ready,false);if(jQuery.browser.msie&&window==top)(function(){if(jQuery.isReady)return;try{document.documentElement.doScroll("left");}catch(error){setTimeout(arguments.callee,0);return;}
jQuery.ready();})();if(jQuery.browser.opera)
document.addEventListener("DOMContentLoaded",function(){if(jQuery.isReady)return;for(var i=0;i<document.styleSheets.length;i++)
if(document.styleSheets[i].disabled){setTimeout(arguments.callee,0);return;}
jQuery.ready();},false);if(jQuery.browser.safari){var numStyles;(function(){if(jQuery.isReady)return;if(document.readyState!="loaded"&&document.readyState!="complete"){setTimeout(arguments.callee,0);return;}
if(numStyles===undefined)
numStyles=jQuery("style, link[rel=stylesheet]").length;if(document.styleSheets.length!=numStyles){setTimeout(arguments.callee,0);return;}
jQuery.ready();})();}
jQuery.event.add(window,"load",jQuery.ready);}
jQuery.each(("blur,focus,load,resize,scroll,unload,click,dblclick,"+"mousedown,mouseup,mousemove,mouseover,mouseout,change,select,"+"submit,keydown,keypress,keyup,error").split(","),function(i,name){jQuery.fn[name]=function(fn){return fn?this.bind(name,fn):this.trigger(name);};});var withinElement=function(event,elem){var parent=event.relatedTarget;while(parent&&parent!=elem)try{parent=parent.parentNode;}catch(error){parent=elem;}
return parent==elem;};jQuery(window).bind("unload",function(){jQuery("*").add(document).unbind();});jQuery.fn.extend({load:function(url,params,callback){if(jQuery.isFunction(url))
return this.bind("load",url);var off=url.indexOf(" ");if(off>=0){var selector=url.slice(off,url.length);url=url.slice(0,off);}
callback=callback||function(){};var type="GET";if(params)
if(jQuery.isFunction(params)){callback=params;params=null;}else{params=jQuery.param(params);type="POST";}
var self=this;jQuery.ajax({url:url,type:type,dataType:"html",data:params,complete:function(res,status){if(status=="success"||status=="notmodified")
self.html(selector?jQuery("<div/>").append(res.responseText.replace(/<script(.|\s)*?\/script>/g,"")).find(selector):res.responseText);self.each(callback,[res.responseText,status,res]);}});return this;},serialize:function(){return jQuery.param(this.serializeArray());},serializeArray:function(){return this.map(function(){return jQuery.nodeName(this,"form")?jQuery.makeArray(this.elements):this;}).filter(function(){return this.name&&!this.disabled&&(this.checked||/select|textarea/i.test(this.nodeName)||/text|hidden|password/i.test(this.type));}).map(function(i,elem){var val=jQuery(this).val();return val==null?null:val.constructor==Array?jQuery.map(val,function(val,i){return{name:elem.name,value:val};}):{name:elem.name,value:val};}).get();}});jQuery.each("ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","),function(i,o){jQuery.fn[o]=function(f){return this.bind(o,f);};});var jsc=(new Date).getTime();jQuery.extend({get:function(url,data,callback,type){if(jQuery.isFunction(data)){callback=data;data=null;}
return jQuery.ajax({type:"GET",url:url,data:data,success:callback,dataType:type});},getScript:function(url,callback){return jQuery.get(url,null,callback,"script");},getJSON:function(url,data,callback){return jQuery.get(url,data,callback,"json");},post:function(url,data,callback,type){if(jQuery.isFunction(data)){callback=data;data={};}
return jQuery.ajax({type:"POST",url:url,data:data,success:callback,dataType:type});},ajaxSetup:function(settings){jQuery.extend(jQuery.ajaxSettings,settings);},ajaxSettings:{global:true,type:"GET",timeout:0,contentType:"application/x-www-form-urlencoded",processData:true,async:true,data:null,username:null,password:null,accepts:{xml:"application/xml, text/xml",html:"text/html",script:"text/javascript, application/javascript",json:"application/json, text/javascript",text:"text/plain",_default:"*/*"}},lastModified:{},ajax:function(s){var jsonp,jsre=/=\?(&|$)/g,status,data;s=jQuery.extend(true,s,jQuery.extend(true,{},jQuery.ajaxSettings,s));if(s.data&&s.processData&&typeof s.data!="string")
s.data=jQuery.param(s.data);if(s.dataType=="jsonp"){if(s.type.toLowerCase()=="get"){if(!s.url.match(jsre))
s.url+=(s.url.match(/\?/)?"&":"?")+(s.jsonp||"callback")+"=?";}else if(!s.data||!s.data.match(jsre))
s.data=(s.data?s.data+"&":"")+(s.jsonp||"callback")+"=?";s.dataType="json";}
if(s.dataType=="json"&&(s.data&&s.data.match(jsre)||s.url.match(jsre))){jsonp="jsonp"+jsc++;if(s.data)
s.data=(s.data+"").replace(jsre,"="+jsonp+"$1");s.url=s.url.replace(jsre,"="+jsonp+"$1");s.dataType="script";window[jsonp]=function(tmp){data=tmp;success();complete();window[jsonp]=undefined;try{delete window[jsonp];}catch(e){}
if(head)
head.removeChild(script);};}
if(s.dataType=="script"&&s.cache==null)
s.cache=false;if(s.cache===false&&s.type.toLowerCase()=="get"){var ts=(new Date()).getTime();var ret=s.url.replace(/(\?|&)_=.*?(&|$)/,"$1_="+ts+"$2");s.url=ret+((ret==s.url)?(s.url.match(/\?/)?"&":"?")+"_="+ts:"");}
if(s.data&&s.type.toLowerCase()=="get"){s.url+=(s.url.match(/\?/)?"&":"?")+s.data;s.data=null;}
if(s.global&&!jQuery.active++)
jQuery.event.trigger("ajaxStart");if((!s.url.indexOf("http")||!s.url.indexOf("//"))&&(s.dataType=="script"||s.dataType=="json")&&s.type.toLowerCase()=="get"){var head=document.getElementsByTagName("head")[0];var script=document.createElement("script");script.src=s.url;if(s.scriptCharset)
script.charset=s.scriptCharset;if(!jsonp){var done=false;script.onload=script.onreadystatechange=function(){if(!done&&(!this.readyState||this.readyState=="loaded"||this.readyState=="complete")){done=true;success();complete();head.removeChild(script);}};}
head.appendChild(script);return undefined;}
var requestDone=false;var xml=window.ActiveXObject?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();xml.open(s.type,s.url,s.async,s.username,s.password);try{if(s.data)
xml.setRequestHeader("Content-Type",s.contentType);if(s.ifModified)
xml.setRequestHeader("If-Modified-Since",jQuery.lastModified[s.url]||"Thu, 01 Jan 1970 00:00:00 GMT");xml.setRequestHeader("X-Requested-With","XMLHttpRequest");xml.setRequestHeader("Accept",s.dataType&&s.accepts[s.dataType]?s.accepts[s.dataType]+", */*":s.accepts._default);}catch(e){}
if(s.beforeSend)
s.beforeSend(xml);if(s.global)
jQuery.event.trigger("ajaxSend",[xml,s]);var onreadystatechange=function(isTimeout){if(!requestDone&&xml&&(xml.readyState==4||isTimeout=="timeout")){requestDone=true;if(ival){clearInterval(ival);ival=null;}
status=isTimeout=="timeout"&&"timeout"||!jQuery.httpSuccess(xml)&&"error"||s.ifModified&&jQuery.httpNotModified(xml,s.url)&&"notmodified"||"success";if(status=="success"){try{data=jQuery.httpData(xml,s.dataType);}catch(e){status="parsererror";}}
if(status=="success"){var modRes;try{modRes=xml.getResponseHeader("Last-Modified");}catch(e){}
if(s.ifModified&&modRes)
jQuery.lastModified[s.url]=modRes;if(!jsonp)
success();}else
jQuery.handleError(s,xml,status);complete();if(s.async)
xml=null;}};if(s.async){var ival=setInterval(onreadystatechange,13);if(s.timeout>0)
setTimeout(function(){if(xml){xml.abort();if(!requestDone)
onreadystatechange("timeout");}},s.timeout);}
try{xml.send(s.data);}catch(e){jQuery.handleError(s,xml,null,e);}
if(!s.async)
onreadystatechange();function success(){if(s.success)
s.success(data,status);if(s.global)
jQuery.event.trigger("ajaxSuccess",[xml,s]);}
function complete(){if(s.complete)
s.complete(xml,status);if(s.global)
jQuery.event.trigger("ajaxComplete",[xml,s]);if(s.global&&!--jQuery.active)
jQuery.event.trigger("ajaxStop");}
return xml;},handleError:function(s,xml,status,e){if(s.error)s.error(xml,status,e);if(s.global)
jQuery.event.trigger("ajaxError",[xml,s,e]);},active:0,httpSuccess:function(r){try{return!r.status&&location.protocol=="file:"||(r.status>=200&&r.status<300)||r.status==304||r.status==1223||jQuery.browser.safari&&r.status==undefined;}catch(e){}
return false;},httpNotModified:function(xml,url){try{var xmlRes=xml.getResponseHeader("Last-Modified");return xml.status==304||xmlRes==jQuery.lastModified[url]||jQuery.browser.safari&&xml.status==undefined;}catch(e){}
return false;},httpData:function(r,type){var ct=r.getResponseHeader("content-type");var xml=type=="xml"||!type&&ct&&ct.indexOf("xml")>=0;var data=xml?r.responseXML:r.responseText;if(xml&&data.documentElement.tagName=="parsererror")
throw"parsererror";if(type=="script")
jQuery.globalEval(data);if(type=="json")
data=eval("("+data+")");return data;},param:function(a){var s=[];if(a.constructor==Array||a.jquery)
jQuery.each(a,function(){s.push(encodeURIComponent(this.name)+"="+encodeURIComponent(this.value));});else
for(var j in a)
if(a[j]&&a[j].constructor==Array)
jQuery.each(a[j],function(){s.push(encodeURIComponent(j)+"="+encodeURIComponent(this));});else
s.push(encodeURIComponent(j)+"="+encodeURIComponent(a[j]));return s.join("&").replace(/%20/g,"+");}});jQuery.fn.extend({show:function(speed,callback){return speed?this.animate({height:"show",width:"show",opacity:"show"},speed,callback):this.filter(":hidden").each(function(){this.style.display=this.oldblock||"";if(jQuery.css(this,"display")=="none"){var elem=jQuery("<"+this.tagName+" />").appendTo("body");this.style.display=elem.css("display");if(this.style.display=="none")
this.style.display="block";elem.remove();}}).end();},hide:function(speed,callback){return speed?this.animate({height:"hide",width:"hide",opacity:"hide"},speed,callback):this.filter(":visible").each(function(){this.oldblock=this.oldblock||jQuery.css(this,"display");this.style.display="none";}).end();},_toggle:jQuery.fn.toggle,toggle:function(fn,fn2){return jQuery.isFunction(fn)&&jQuery.isFunction(fn2)?this._toggle(fn,fn2):fn?this.animate({height:"toggle",width:"toggle",opacity:"toggle"},fn,fn2):this.each(function(){jQuery(this)[jQuery(this).is(":hidden")?"show":"hide"]();});},slideDown:function(speed,callback){return this.animate({height:"show"},speed,callback);},slideUp:function(speed,callback){return this.animate({height:"hide"},speed,callback);},slideToggle:function(speed,callback){return this.animate({height:"toggle"},speed,callback);},fadeIn:function(speed,callback){return this.animate({opacity:"show"},speed,callback);},fadeOut:function(speed,callback){return this.animate({opacity:"hide"},speed,callback);},fadeTo:function(speed,to,callback){return this.animate({opacity:to},speed,callback);},animate:function(prop,speed,easing,callback){var optall=jQuery.speed(speed,easing,callback);return this[optall.queue===false?"each":"queue"](function(){if(this.nodeType!=1)
return false;var opt=jQuery.extend({},optall);var hidden=jQuery(this).is(":hidden"),self=this;for(var p in prop){if(prop[p]=="hide"&&hidden||prop[p]=="show"&&!hidden)
return jQuery.isFunction(opt.complete)&&opt.complete.apply(this);if(p=="height"||p=="width"){opt.display=jQuery.css(this,"display");opt.overflow=this.style.overflow;}}
if(opt.overflow!=null)
this.style.overflow="hidden";opt.curAnim=jQuery.extend({},prop);jQuery.each(prop,function(name,val){var e=new jQuery.fx(self,opt,name);if(/toggle|show|hide/.test(val))
e[val=="toggle"?hidden?"show":"hide":val](prop);else{var parts=val.toString().match(/^([+-]=)?([\d+-.]+)(.*)$/),start=e.cur(true)||0;if(parts){var end=parseFloat(parts[2]),unit=parts[3]||"px";if(unit!="px"){self.style[name]=(end||1)+unit;start=((end||1)/e.cur(true))*start;self.style[name]=start+unit;}
if(parts[1])
end=((parts[1]=="-="?-1:1)*end)+start;e.custom(start,end,unit);}else
e.custom(start,val,"");}});return true;});},queue:function(type,fn){if(jQuery.isFunction(type)||(type&&type.constructor==Array)){fn=type;type="fx";}
if(!type||(typeof type=="string"&&!fn))
return queue(this[0],type);return this.each(function(){if(fn.constructor==Array)
queue(this,type,fn);else{queue(this,type).push(fn);if(queue(this,type).length==1)
fn.apply(this);}});},stop:function(clearQueue,gotoEnd){var timers=jQuery.timers;if(clearQueue)
this.queue([]);this.each(function(){for(var i=timers.length-1;i>=0;i--)
if(timers[i].elem==this){if(gotoEnd)
timers[i](true);timers.splice(i,1);}});if(!gotoEnd)
this.dequeue();return this;}});var queue=function(elem,type,array){if(!elem)
return undefined;type=type||"fx";var q=jQuery.data(elem,type+"queue");if(!q||array)
q=jQuery.data(elem,type+"queue",array?jQuery.makeArray(array):[]);return q;};jQuery.fn.dequeue=function(type){type=type||"fx";return this.each(function(){var q=queue(this,type);q.shift();if(q.length)
q[0].apply(this);});};jQuery.extend({speed:function(speed,easing,fn){var opt=speed&&speed.constructor==Object?speed:{complete:fn||!fn&&easing||jQuery.isFunction(speed)&&speed,duration:speed,easing:fn&&easing||easing&&easing.constructor!=Function&&easing};opt.duration=(opt.duration&&opt.duration.constructor==Number?opt.duration:{slow:600,fast:200}[opt.duration])||400;opt.old=opt.complete;opt.complete=function(){if(opt.queue!==false)
jQuery(this).dequeue();if(jQuery.isFunction(opt.old))
opt.old.apply(this);};return opt;},easing:{linear:function(p,n,firstNum,diff){return firstNum+diff*p;},swing:function(p,n,firstNum,diff){return((-Math.cos(p*Math.PI)/2)+0.5)*diff+firstNum;}},timers:[],timerId:null,fx:function(elem,options,prop){this.options=options;this.elem=elem;this.prop=prop;if(!options.orig)
options.orig={};}});jQuery.fx.prototype={update:function(){if(this.options.step)
this.options.step.apply(this.elem,[this.now,this]);(jQuery.fx.step[this.prop]||jQuery.fx.step._default)(this);if(this.prop=="height"||this.prop=="width")
this.elem.style.display="block";},cur:function(force){if(this.elem[this.prop]!=null&&this.elem.style[this.prop]==null)
return this.elem[this.prop];var r=parseFloat(jQuery.css(this.elem,this.prop,force));return r&&r>-10000?r:parseFloat(jQuery.curCSS(this.elem,this.prop))||0;},custom:function(from,to,unit){this.startTime=(new Date()).getTime();this.start=from;this.end=to;this.unit=unit||this.unit||"px";this.now=this.start;this.pos=this.state=0;this.update();var self=this;function t(gotoEnd){return self.step(gotoEnd);}
t.elem=this.elem;jQuery.timers.push(t);if(jQuery.timerId==null){jQuery.timerId=setInterval(function(){var timers=jQuery.timers;for(var i=0;i<timers.length;i++)
if(!timers[i]())
timers.splice(i--,1);if(!timers.length){clearInterval(jQuery.timerId);jQuery.timerId=null;}},13);}},show:function(){this.options.orig[this.prop]=jQuery.attr(this.elem.style,this.prop);this.options.show=true;this.custom(0,this.cur());if(this.prop=="width"||this.prop=="height")
this.elem.style[this.prop]="1px";jQuery(this.elem).show();},hide:function(){this.options.orig[this.prop]=jQuery.attr(this.elem.style,this.prop);this.options.hide=true;this.custom(this.cur(),0);},step:function(gotoEnd){var t=(new Date()).getTime();if(gotoEnd||t>this.options.duration+this.startTime){this.now=this.end;this.pos=this.state=1;this.update();this.options.curAnim[this.prop]=true;var done=true;for(var i in this.options.curAnim)
if(this.options.curAnim[i]!==true)
done=false;if(done){if(this.options.display!=null){this.elem.style.overflow=this.options.overflow;this.elem.style.display=this.options.display;if(jQuery.css(this.elem,"display")=="none")
this.elem.style.display="block";}
if(this.options.hide)
this.elem.style.display="none";if(this.options.hide||this.options.show)
for(var p in this.options.curAnim)
jQuery.attr(this.elem.style,p,this.options.orig[p]);}
if(done&&jQuery.isFunction(this.options.complete))
this.options.complete.apply(this.elem);return false;}else{var n=t-this.startTime;this.state=n/this.options.duration;this.pos=jQuery.easing[this.options.easing||(jQuery.easing.swing?"swing":"linear")](this.state,n,0,1,this.options.duration);this.now=this.start+((this.end-this.start)*this.pos);this.update();}
return true;}};jQuery.fx.step={scrollLeft:function(fx){fx.elem.scrollLeft=fx.now;},scrollTop:function(fx){fx.elem.scrollTop=fx.now;},opacity:function(fx){jQuery.attr(fx.elem.style,"opacity",fx.now);},_default:function(fx){fx.elem.style[fx.prop]=fx.now+fx.unit;}};jQuery.fn.offset=function(){var left=0,top=0,elem=this[0],results;if(elem)with(jQuery.browser){var parent=elem.parentNode,offsetChild=elem,offsetParent=elem.offsetParent,doc=elem.ownerDocument,safari2=safari&&parseInt(version)<522,fixed=jQuery.css(elem,"position")=="fixed";if(elem.getBoundingClientRect){var box=elem.getBoundingClientRect();add(box.left+Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft),box.top+Math.max(doc.documentElement.scrollTop,doc.body.scrollTop));add(-doc.documentElement.clientLeft,-doc.documentElement.clientTop);}else{add(elem.offsetLeft,elem.offsetTop);while(offsetParent){add(offsetParent.offsetLeft,offsetParent.offsetTop);if(mozilla&&!/^t(able|d|h)$/i.test(offsetParent.tagName)||safari&&!safari2)
border(offsetParent);if(!fixed&&jQuery.css(offsetParent,"position")=="fixed")
fixed=true;offsetChild=/^body$/i.test(offsetParent.tagName)?offsetChild:offsetParent;offsetParent=offsetParent.offsetParent;}
while(parent&&parent.tagName&&!/^body|html$/i.test(parent.tagName)){if(!/^inline|table.*$/i.test(jQuery.css(parent,"display")))
add(-parent.scrollLeft,-parent.scrollTop);if(mozilla&&jQuery.css(parent,"overflow")!="visible")
border(parent);parent=parent.parentNode;}
if((safari2&&(fixed||jQuery.css(offsetChild,"position")=="absolute"))||(mozilla&&jQuery.css(offsetChild,"position")!="absolute"))
add(-doc.body.offsetLeft,-doc.body.offsetTop);if(fixed)
add(Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft),Math.max(doc.documentElement.scrollTop,doc.body.scrollTop));}
results={top:top,left:left};}
function border(elem){add(jQuery.curCSS(elem,"borderLeftWidth",true),jQuery.curCSS(elem,"borderTopWidth",true));}
function add(l,t){left+=parseInt(l)||0;top+=parseInt(t)||0;}
return results;};})();
/* jquery.autocomplete.js */
jQuery.autocomplete=function(input,options){var me=this;var $input=$(input).attr("autocomplete","off");if(options.inputClass)$input.addClass(options.inputClass);var results=document.createElement("div");var $results=$(results);$results.hide().addClass(options.resultsClass).css("position","absolute");if(options.width>0)$results.css("width",options.width);$("body").append(results);input.autocompleter=me;var timeout=null;var prev="";var active=-1;var cache={};var keyb=false;var hasFocus=false;var lastKeyPressCode=null;function flushCache(){cache={};cache.data={};cache.length=0;};flushCache();if(options.data!=null){var sFirstChar="",stMatchSets={},row=[];if(typeof options.url!="string")options.cacheLength=1;for(var i=0;i<options.data.length;i++){row=((typeof options.data[i]=="string")?[options.data[i]]:options.data[i]);if(row[0].length>0){sFirstChar=row[0].substring(0,1).toLowerCase();if(!stMatchSets[sFirstChar])stMatchSets[sFirstChar]=[];stMatchSets[sFirstChar].push(row);}}
for(var k in stMatchSets){options.cacheLength++;addToCache(k,stMatchSets[k]);}}
$input.keydown(function(e){lastKeyPressCode=e.keyCode;switch(e.keyCode){case 38:e.preventDefault();moveSelect(-1);break;case 40:e.preventDefault();moveSelect(1);break;case 9:case 13:if(selectCurrent()){$input.get(0).blur();e.preventDefault();}
break;default:active=-1;if(timeout)clearTimeout(timeout);timeout=setTimeout(function(){onChange();},options.delay);break;}}).focus(function(){hasFocus=true;}).blur(function(){hasFocus=false;hideResults();});hideResultsNow();function onChange(){if(lastKeyPressCode==46||(lastKeyPressCode>8&&lastKeyPressCode<32))return $results.hide();var v=$input.val();if(v==prev)return;prev=v;if(v.length>=options.minChars){$input.addClass(options.loadingClass);requestData(v);}else{$input.removeClass(options.loadingClass);$results.hide();}};function moveSelect(step){var lis=$("li",results);if(!lis)return;active+=step;if(active<0){active=0;}else if(active>=lis.size()){active=lis.size()-1;}
lis.removeClass("ac_over");$(lis[active]).addClass("ac_over");};function selectCurrent(){var li=$("li.ac_over",results)[0];if(!li){var $li=$("li",results);if(options.selectOnly){if($li.length==1)li=$li[0];}else if(options.selectFirst){li=$li[0];}}
if(li){selectItem(li);return true;}else{return false;}};function selectItem(li){if(!li){li=document.createElement("li");li.extra=[];li.selectValue="";}
var v=$.trim(li.selectValue?li.selectValue:li.innerHTML);input.lastSelected=v;prev=v;$results.html("");$input.val(v);hideResultsNow();if(options.onItemSelect)setTimeout(function(){options.onItemSelect(li)},1);};function createSelection(start,end){var field=$input.get(0);if(field.createTextRange){var selRange=field.createTextRange();selRange.collapse(true);selRange.moveStart("character",start);selRange.moveEnd("character",end);selRange.select();}else if(field.setSelectionRange){field.setSelectionRange(start,end);}else{if(field.selectionStart){field.selectionStart=start;field.selectionEnd=end;}}
field.focus();};function autoFill(sValue){if(lastKeyPressCode!=8){$input.val($input.val()+sValue.substring(prev.length));createSelection(prev.length,sValue.length);}};function showResults(){var pos=findPos(input);var iWidth=(options.width>0)?options.width:$input.width();$results.css({width:parseInt(iWidth)+"px",top:(pos.y+input.offsetHeight)+"px",left:pos.x+"px"}).show();};function hideResults(){if(timeout)clearTimeout(timeout);timeout=setTimeout(hideResultsNow,200);};function hideResultsNow(){if(timeout)clearTimeout(timeout);$input.removeClass(options.loadingClass);if($results.is(":visible")){$results.hide();}
if(options.mustMatch){var v=$input.val();if(v!=input.lastSelected){selectItem(null);}}};function receiveData(q,data){if(data){$input.removeClass(options.loadingClass);results.innerHTML="";if(!hasFocus||data.length==0)return hideResultsNow();if($.browser.msie){$results.append(document.createElement('iframe'));}
results.appendChild(dataToDom(data));if(options.autoFill&&($input.val().toLowerCase()==q.toLowerCase()))autoFill(data[0][0]);showResults();}else{hideResultsNow();}};function parseData(data){if(!data)return null;var parsed=[];var rows=data.split(options.lineSeparator);for(var i=0;i<rows.length;i++){var row=$.trim(rows[i]);if(row){parsed[parsed.length]=row.split(options.cellSeparator);}}
return parsed;};function dataToDom(data){var ul=document.createElement("ul");var num=data.length;if((options.maxItemsToShow>0)&&(options.maxItemsToShow<num))num=options.maxItemsToShow;for(var i=0;i<num;i++){var row=data[i];if(!row)continue;var li=document.createElement("li");if(options.formatItem){li.innerHTML=options.formatItem(row,i,num);li.selectValue=row[0];}else{li.innerHTML=row[0];li.selectValue=row[0];}
var extra=null;if(row.length>1){extra=[];for(var j=1;j<row.length;j++){extra[extra.length]=row[j];}}
li.extra=extra;ul.appendChild(li);$(li).hover(function(){$("li",ul).removeClass("ac_over");$(this).addClass("ac_over");active=$("li",ul).indexOf($(this).get(0));},function(){$(this).removeClass("ac_over");}).click(function(e){e.preventDefault();e.stopPropagation();selectItem(this)});}
return ul;};function requestData(q){if(!options.matchCase)q=q.toLowerCase();var data=options.cacheLength?loadFromCache(q):null;if(data){receiveData(q,data);}else if((typeof options.url=="string")&&(options.url.length>0)){$.get(makeUrl(q),function(data){data=parseData(data);addToCache(q,data);receiveData(q,data);});}else{$input.removeClass(options.loadingClass);}};function makeUrl(q){if($("#empiece").css("display")=="inline"){q+="[EXPANSOR]";}
else{q="[EXPANSOR]"+q+"[EXPANSOR]";}
var url=options.url+"/partialText:"+encodeURI(q);for(var i in options.extraParams){url+="/parametro"+i+":"+encodeURI(options.extraParams[i]);}
return url;};function loadFromCache(q){if(!q)return null;if(cache.data[q])return cache.data[q];if(options.matchSubset){for(var i=q.length-1;i>=options.minChars;i--){var qs=q.substr(0,i);var c=cache.data[qs];if(c){var csub=[];for(var j=0;j<c.length;j++){var x=c[j];var x0=x[0];if(matchSubset(x0,q)){csub[csub.length]=x;}}
return csub;}}}
return null;};function matchSubset(s,sub){if(!options.matchCase)s=s.toLowerCase();var i=s.indexOf(sub);if(i==-1)return false;return i==0||options.matchContains;};this.flushCache=function(){flushCache();};this.setExtraParams=function(p){options.extraParams=p;};this.findValue=function(){var q=$input.val();if(!options.matchCase)q=q.toLowerCase();var data=options.cacheLength?loadFromCache(q):null;if(data){findValueCallback(q,data);}else if((typeof options.url=="string")&&(options.url.length>0)){$.get(makeUrl(q),function(data){data=parseData(data)
addToCache(q,data);findValueCallback(q,data);});}else{findValueCallback(q,null);}}
function findValueCallback(q,data){if(data)$input.removeClass(options.loadingClass);var num=(data)?data.length:0;var li=null;for(var i=0;i<num;i++){var row=data[i];if(row[0].toLowerCase()==q.toLowerCase()){li=document.createElement("li");if(options.formatItem){li.innerHTML=options.formatItem(row,i,num);li.selectValue=row[0];}else{li.innerHTML=row[0];li.selectValue=row[0];}
var extra=null;if(row.length>1){extra=[];for(var j=1;j<row.length;j++){extra[extra.length]=row[j];}}
li.extra=extra;}}
if(options.onFindValue)setTimeout(function(){options.onFindValue(li)},1);}
function addToCache(q,data){if(!data||!q||!options.cacheLength)return;if(!cache.length||cache.length>options.cacheLength){flushCache();cache.length++;}else if(!cache[q]){cache.length++;}
cache.data[q]=data;};function findPos(obj){var curleft=obj.offsetLeft||0;var curtop=obj.offsetTop||0;while(obj=obj.offsetParent){curleft+=obj.offsetLeft
curtop+=obj.offsetTop}
return{x:curleft,y:curtop};}}
jQuery.fn.autocomplete=function(url,options,data){options=options||{};options.url=url;options.data=((typeof data=="object")&&(data.constructor==Array))?data:null;options.inputClass=options.inputClass||"ac_input";options.resultsClass=options.resultsClass||"ac_results";options.lineSeparator=options.lineSeparator||"\n";options.cellSeparator=options.cellSeparator||"|";options.minChars=options.minChars||1;options.delay=options.delay||400;options.matchCase=options.matchCase||0;options.matchSubset=options.matchSubset||1;options.matchContains=options.matchContains||0;options.cacheLength=options.cacheLength||1;options.mustMatch=options.mustMatch||0;options.extraParams=options.extraParams||{};options.loadingClass=options.loadingClass||"ac_loading";options.selectFirst=options.selectFirst||false;options.selectOnly=options.selectOnly||false;options.maxItemsToShow=options.maxItemsToShow||-1;options.autoFill=options.autoFill||false;options.width=parseInt(options.width,10)||0;this.each(function(){var input=this;new jQuery.autocomplete(input,options);});return this;}
jQuery.fn.autocompleteArray=function(data,options){return this.autocomplete(null,options,data);}
jQuery.fn.indexOf=function(e){for(var i=0;i<this.length;i++){if(this[i]==e)return i;}
return-1;};
/* jquery.jqmodal.js */
(function($){$.fn.jqm=function(p){var o={zIndex:3000,overlay:50,overlayClass:'jqmOverlay',closeClass:'jqmClose',trigger:'.jqModal',ajax:false,target:false,modal:false,toTop:false,onShow:false,onHide:false,onLoad:false};return this.each(function(){if(this._jqm)return;s++;this._jqm=s;H[s]={c:$.extend(o,p),a:false,w:$(this).addClass('jqmID'+s),s:s};o.trigger&&$(this).jqmAddTrigger(o.trigger);});};$.fn.jqmAddClose=function(e){return HS(this,e,'jqmHide');};$.fn.jqmAddTrigger=function(e){return HS(this,e,'jqmShow');};$.fn.jqmShow=function(t){return this.each(function(){!H[this._jqm].a&&$.jqm.open(this._jqm,t)});};$.fn.jqmHide=function(t){return this.each(function(){H[this._jqm].a&&$.jqm.close(this._jqm,t)});};$.jqm={hash:{},open:function(s,t){var h=H[s],c=h.c,cc='.'+c.closeClass,z=/^\d+$/.test(h.w.css('z-index'))&&h.w.css('z-index')||c.zIndex,o=$('<div></div>').css({height:'100%',width:'100%',position:'fixed',left:0,top:0,'z-index':z-1,opacity:c.overlay/100});h.t=t;h.a=true;h.w.css('z-index',z);if(c.modal){!A[0]&&F('bind');A.push(s);o.css('cursor','wait');}
else if(c.overlay>0)h.w.jqmAddClose(o);else o=false;h.o=(o)?o.addClass(c.overlayClass).prependTo('body'):false;if(ie6&&$('html,body').css({height:'100%',width:'100%'})&&o){o=o.css({position:'absolute'})[0];for(var y in{Top:1,Left:1})o.style.setExpression(y.toLowerCase(),"(_=(document.documentElement.scroll"+y+" || document.body.scroll"+y+"))+'px'");}
if(c.ajax){var r=c.target||h.w,u=c.ajax,r=(typeof r=='string')?$(r,h.w):$(r),u=(u.substr(0,1)=='@')?$(t).attr(u.substring(1)):u;r.load(u,function(){c.onLoad&&c.onLoad.call(this,h);cc&&h.w.jqmAddClose($(cc,h.w));O(h);});}
else cc&&h.w.jqmAddClose($(cc,h.w));c.toTop&&h.o&&h.w.before('<span id="jqmP'+h.w[0]._jqm+'"></span>').insertAfter(h.o);c.onShow&&c.onShow(h);h.w.show();O(h);return false;},close:function(s){var h=H[s];h.a=false;if(h.c.modal){A.pop();!A[0]&&F('unbind');}
h.c.toTop&&h.o&&$('#jqmP'+h.w[0]._jqm).after(h.w).remove();if(h.c.onHide)h.c.onHide(h);else{h.w.hide()&&h.o&&h.o.remove()}return false;}};var s=0,H=$.jqm.hash,A=[],ie6=$.browser.msie&&($.browser.version=="6.0"),i=$('<iframe src="javascript:false;document.write(\'\');" class="jqm"></iframe>').css({opacity:0}),O=function(h){if(ie6)h.o&&h.o.html('<p style="width:100%;height:100%"/>').prepend(i)||(!$('iframe.jqm',h.w)[0]&&h.w.prepend(i));f(h);},f=function(h){try{$(':input:visible',h.w)[0].focus();}catch(e){}},F=function(t){$()[t]("keypress",x)[t]("keydown",x)[t]("mousedown",x);},x=function(e){var h=H[A[A.length-1]],r=(!$(e.target).parents('.jqmID'+h.s)[0]);r&&f(h);return!r;},HS=function(w,e,y){var s=[];w.each(function(){s.push(this._jqm)});$(e).each(function(){if(this[y])$.extend(this[y],s);else{this[y]=s;$(this).click(function(){for(var i in{jqmShow:1,jqmHide:1})for(var s in this[i])if(H[this[i][s]])H[this[i][s]].w[i](this);return false;});}});return w;};})(jQuery);
/* jquery.jeditable.js */
(function($){$.fn.editable=function(target,options){var settings={update:'self',target:target,name:'value',id:'id',type:'text',width:'auto',height:'auto',event:'click',onblur:'cancel',loadtype:'GET',loadtext:'Loading...',placeholder:'Click to edit',loaddata:{},submitdata:{}};if(options){$.extend(settings,options);}
var plugin=$.editable.types[settings.type].plugin||function(){};var submit=$.editable.types[settings.type].submit||function(){};var buttons=$.editable.types[settings.type].buttons||$.editable.types['defaults'].buttons;var content=$.editable.types[settings.type].content||$.editable.types['defaults'].content;var element=$.editable.types[settings.type].element||$.editable.types['defaults'].element;var reset=$.editable.types[settings.type].reset||$.editable.types['defaults'].reset;var callback=settings.callback||function(){};if(!$.isFunction($(this)[settings.event])){$.fn[settings.event]=function(fn){return fn?this.bind(settings.event,fn):this.trigger(settings.event);}}
$(this).attr('title',settings.tooltip);settings.autowidth='auto'==settings.width;settings.autoheight='auto'==settings.height;return this.each(function(){var self=this;var savedwidth=$(self).width();var savedheight=$(self).height();if(!$.trim($(this).html())){$(this).html(settings.placeholder);}
$(this)[settings.event](function(e){if(self.editing){return;}
if(0==$(self).width()){settings.width=savedwidth;settings.height=savedheight;}else{if(settings.width!='none'){settings.width=settings.autowidth?$(self).width():settings.width;}
if(settings.height!='none'){settings.height=settings.autoheight?$(self).height():settings.height;}}
if($(this).html().toLowerCase().replace(/;/,'')==settings.placeholder.toLowerCase().replace(/;/,'')){$(this).html('');}
self.editing=true;self.revert=$(self).html();$(self).html('');var form=$('<form/>');if(settings.cssclass){if('inherit'==settings.cssclass){form.attr('class',$(self).attr('class'));}else{form.attr('class',settings.cssclass);}}
if(settings.style){if('inherit'==settings.style){form.attr('style',$(self).attr('style'));form.css('display',$(self).css('display'));}else{form.attr('style',settings.style);}}
var input=element.apply(form,[settings,self]);var input_content;if(settings.loadurl){var t=setTimeout(function(){input.disabled=true;content.apply(form,[settings.loadtext,settings,self]);},100);var loaddata={};loaddata[settings.id]=self.id;if($.isFunction(settings.loaddata)){$.extend(loaddata,settings.loaddata.apply(self,[self.revert,settings]));}else{$.extend(loaddata,settings.loaddata);}
$.ajax({type:settings.loadtype,url:settings.loadurl,data:loaddata,async:false,success:function(result){window.clearTimeout(t);input_content=result;input.disabled=false;}});}else if(settings.data){input_content=settings.data;if($.isFunction(settings.data)){input_content=settings.data.apply(self,[self.revert,settings]);}}else{input_content=self.revert;}
content.apply(form,[input_content,settings,self]);input.attr('name',settings.name);buttons.apply(form,[settings,self]);$(self).append(form);plugin.apply(form,[settings,self]);$(':input:visible:enabled:first',form).focus();if(settings.select){input.select();}
input.keydown(function(e){if(e.keyCode==27){e.preventDefault();reset.apply(form,[settings,self]);}});var t;if('cancel'==settings.onblur){input.blur(function(e){t=setTimeout(function(){reset.apply(form,[settings,self]);},500);});}else if('submit'==settings.onblur){input.blur(function(e){form.submit();});}else if($.isFunction(settings.onblur)){input.blur(function(e){settings.onblur.apply(self,[input.val(),settings]);});}else{input.blur(function(e){});}
form.submit(function(e){if(t){clearTimeout(t);}
e.preventDefault();if(false!==submit.apply(form,[settings,self])){if($.isFunction(settings.target)){var str=settings.target.apply(self,[input.val(),settings]);$(self).html(str);self.editing=false;callback.apply(self,[self.innerHTML,settings]);if(!$.trim($(self).html())){$(self).html(settings.placeholder);}}else{var submitdata={};submitdata[settings.name]=input.val();submitdata[settings.id]=self.id;if($.isFunction(settings.submitdata)){$.extend(submitdata,settings.submitdata.apply(self,[self.revert,settings]));}else{$.extend(submitdata,settings.submitdata);}
if('PUT'==settings.method){submitdata['_method']='put';}
$(self).html(settings.indicator);$.post(settings.target,submitdata,function(str){if(settings.update=='self'){$(self).html(str);}
else if($.isFunction(settings.update)){$(settings.update.apply(self)).html(str);}
else{$(settings.update).html(str);}
self.editing=false;callback.apply(self,[self.innerHTML,settings]);if(!$.trim($(self).html())){$(self).html(settings.placeholder);}});}}
return false;});});this.reset=function(){$(self).html(self.revert);self.editing=false;if(!$.trim($(self).html())){$(self).html(settings.placeholder);}}});};$.editable={types:{defaults:{element:function(settings,original){var input=$('<input type="hidden">');$(this).append(input);return(input);},content:function(string,settings,original){$(':input:first',this).val(string);},reset:function(settings,original){original.reset();},buttons:function(settings,original){var form=this;if(settings.submit){if(settings.submit.match(/>$/)){var submit=$(settings.submit).click(function(){if(submit.attr("type")!="submit"){form.submit();}});}else{var submit=$('<button type="submit">');submit.html(settings.submit);}
$(this).append(submit);}
if(settings.cancel){if(settings.cancel.match(/>$/)){var cancel=$(settings.cancel);}else{var cancel=$('<button type="cancel">');cancel.html(settings.cancel);}
$(this).append(cancel);$(cancel).click(function(event){if($.isFunction($.editable.types[settings.type].reset)){var reset=$.editable.types[settings.type].reset;}else{var reset=$.editable.types['defaults'].reset;}
reset.apply(form,[settings,original]);return false;});}}},text:{element:function(settings,original){var input=$('<input>');if(settings.width!='none'){input.width(settings.width);}
if(settings.height!='none'){input.height(settings.height);}
input.attr('autocomplete','off');$(this).append(input);return(input);}},textarea:{element:function(settings,original){var textarea=$('<textarea>');if(settings.rows){textarea.attr('rows',settings.rows);}else{textarea.height(settings.height);}
if(settings.cols){textarea.attr('cols',settings.cols);}else{textarea.width(settings.width);}
$(this).append(textarea);return(textarea);}},select:{element:function(settings,original){var select=$('<select>');$(this).append(select);return(select);},content:function(string,settings,original){if(String==string.constructor){eval('var json = '+string);for(var key in json){if(!json.hasOwnProperty(key)){continue;}
if('selected'==key){continue;}
var option=$('<option>').val(key).append(json[key]);$('select',this).append(option);}}
$('select',this).children().each(function(){if($(this).val()==json['selected']||$(this).text()==original.revert){$(this).attr('selected','selected');};});}}},addInputType:function(name,input){$.editable.types[name]=input;}};})(jQuery);
/* jquery.form.js */
(function($){$.fn.ajaxSubmit=function(options){if(typeof options=='function')
options={success:options};options=$.extend({url:this.attr('action')||window.location.toString(),type:this.attr('method')||'GET'},options||{});var veto={};$.event.trigger('form.pre.serialize',[this,options,veto]);if(veto.veto)return this;var a=this.formToArray(options.semantic);if(options.data){for(var n in options.data)
a.push({name:n,value:options.data[n]});}
if(options.beforeSubmit&&options.beforeSubmit(a,this,options)===false)return this;$.event.trigger('form.submit.validate',[a,this,options,veto]);if(veto.veto)return this;var q=$.param(a);if(options.type.toUpperCase()=='GET'){options.url+=(options.url.indexOf('?')>=0?'&':'?')+q;options.data=null;}
else
options.data=q;var $form=this,callbacks=[];if(options.resetForm)callbacks.push(function(){$form.resetForm();});if(options.clearForm)callbacks.push(function(){$form.clearForm();});if(!options.dataType&&options.target){var oldSuccess=options.success||function(){};callbacks.push(function(data){if(this.evalScripts)
$(options.target).attr("innerHTML",data).evalScripts().each(oldSuccess,arguments);else
$(options.target).html(data).each(oldSuccess,arguments);});}
else if(options.success)
callbacks.push(options.success);options.success=function(data,status){for(var i=0,max=callbacks.length;i<max;i++)
callbacks[i](data,status,$form);};var files=$('input:file',this).fieldValue();var found=false;for(var j=0;j<files.length;j++)
if(files[j])
found=true;if(options.iframe||found){if($.browser.safari&&options.closeKeepAlive)
$.get(options.closeKeepAlive,fileUpload);else
fileUpload();}
else
$.ajax(options);$.event.trigger('form.submit.notify',[this,options]);return this;function fileUpload(){var form=$form[0];var opts=$.extend({},$.ajaxSettings,options);var id='jqFormIO'+$.fn.ajaxSubmit.counter++;var $io=$('<iframe id="'+id+'" name="'+id+'" />');var io=$io[0];var op8=$.browser.opera&&window.opera.version()<9;if($.browser.msie||op8)io.src='javascript:false;document.write("");';$io.css({position:'absolute',top:'-1000px',left:'-1000px'});var xhr={responseText:null,responseXML:null,status:0,statusText:'n/a',getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){}};var g=opts.global;if(g&&!$.active++)$.event.trigger("ajaxStart");if(g)$.event.trigger("ajaxSend",[xhr,opts]);var cbInvoked=0;var timedOut=0;setTimeout(function(){var encAttr=form.encoding?'encoding':'enctype';var t=$form.attr('target'),a=$form.attr('action');$form.attr({target:id,method:'POST',action:opts.url});form[encAttr]='multipart/form-data';if(opts.timeout)
setTimeout(function(){timedOut=true;cb();},opts.timeout);$io.appendTo('body');io.attachEvent?io.attachEvent('onload',cb):io.addEventListener('load',cb,false);form.submit();$form.attr({action:a,target:t});},10);function cb(){if(cbInvoked++)return;io.detachEvent?io.detachEvent('onload',cb):io.removeEventListener('load',cb,false);var ok=true;try{if(timedOut)throw'timeout';var data,doc;doc=io.contentWindow?io.contentWindow.document:io.contentDocument?io.contentDocument:io.document;xhr.responseText=doc.body?doc.body.innerHTML:null;xhr.responseXML=doc.XMLDocument?doc.XMLDocument:doc;if(opts.dataType=='json'||opts.dataType=='script'){var ta=doc.getElementsByTagName('textarea')[0];data=ta?ta.value:xhr.responseText;if(opts.dataType=='json')
eval("data = "+data);else
$.globalEval(data);}
else if(opts.dataType=='xml'){data=xhr.responseXML;if(!data&&xhr.responseText!=null)
data=toXml(xhr.responseText);}
else{data=xhr.responseText;}}
catch(e){ok=false;$.handleError(opts,xhr,'error',e);}
if(ok){opts.success(data,'success');if(g)$.event.trigger("ajaxSuccess",[xhr,opts]);}
if(g)$.event.trigger("ajaxComplete",[xhr,opts]);if(g&&!--$.active)$.event.trigger("ajaxStop");if(opts.complete)opts.complete(xhr,ok?'success':'error');setTimeout(function(){$io.remove();xhr.responseXML=null;},100);};function toXml(s,doc){if(window.ActiveXObject){doc=new ActiveXObject('Microsoft.XMLDOM');doc.async='false';doc.loadXML(s);}
else
doc=(new DOMParser()).parseFromString(s,'text/xml');return(doc&&doc.documentElement&&doc.documentElement.tagName!='parsererror')?doc:null;};};};$.fn.ajaxSubmit.counter=0;$.fn.ajaxForm=function(options){return this.ajaxFormUnbind().submit(submitHandler).each(function(){this.formPluginId=$.fn.ajaxForm.counter++;$.fn.ajaxForm.optionHash[this.formPluginId]=options;$(":submit,input:image",this).click(clickHandler);});};$.fn.ajaxForm.counter=1;$.fn.ajaxForm.optionHash={};function clickHandler(e){var $form=this.form;$form.clk=this;if(this.type=='image'){if(e.offsetX!=undefined){$form.clk_x=e.offsetX;$form.clk_y=e.offsetY;}else if(typeof $.fn.offset=='function'){var offset=$(this).offset();$form.clk_x=e.pageX-offset.left;$form.clk_y=e.pageY-offset.top;}else{$form.clk_x=e.pageX-this.offsetLeft;$form.clk_y=e.pageY-this.offsetTop;}}
setTimeout(function(){$form.clk=$form.clk_x=$form.clk_y=null;},10);};function submitHandler(){var id=this.formPluginId;var options=$.fn.ajaxForm.optionHash[id];$(this).ajaxSubmit(options);return false;};$.fn.ajaxFormUnbind=function(){this.unbind('submit',submitHandler);return this.each(function(){$(":submit,input:image",this).unbind('click',clickHandler);});};$.fn.formToArray=function(semantic){var a=[];if(this.length==0)return a;var form=this[0];var els=semantic?form.getElementsByTagName('*'):form.elements;if(!els)return a;for(var i=0,max=els.length;i<max;i++){var el=els[i];var n=el.name;if(!n)continue;if(semantic&&form.clk&&el.type=="image"){if(!el.disabled&&form.clk==el)
a.push({name:n+'.x',value:form.clk_x},{name:n+'.y',value:form.clk_y});continue;}
var v=$.fieldValue(el,true);if(v&&v.constructor==Array){for(var j=0,jmax=v.length;j<jmax;j++)
a.push({name:n,value:v[j]});}
else if(v!==null&&typeof v!='undefined')
a.push({name:n,value:v});}
if(!semantic&&form.clk){var inputs=form.getElementsByTagName("input");for(var i=0,max=inputs.length;i<max;i++){var input=inputs[i];var n=input.name;if(n&&!input.disabled&&input.type=="image"&&form.clk==input)
a.push({name:n+'.x',value:form.clk_x},{name:n+'.y',value:form.clk_y});}}
return a;};$.fn.formSerialize=function(semantic){return $.param(this.formToArray(semantic));};$.fn.fieldSerialize=function(successful){var a=[];this.each(function(){var n=this.name;if(!n)return;var v=$.fieldValue(this,successful);if(v&&v.constructor==Array){for(var i=0,max=v.length;i<max;i++)
a.push({name:n,value:v[i]});}
else if(v!==null&&typeof v!='undefined')
a.push({name:this.name,value:v});});return $.param(a);};$.fn.fieldValue=function(successful){for(var val=[],i=0,max=this.length;i<max;i++){var el=this[i];var v=$.fieldValue(el,successful);if(v===null||typeof v=='undefined'||(v.constructor==Array&&!v.length))
continue;v.constructor==Array?$.merge(val,v):val.push(v);}
return val;};$.fieldValue=function(el,successful){var n=el.name,t=el.type,tag=el.tagName.toLowerCase();if(typeof successful=='undefined')successful=true;if(successful&&(!n||el.disabled||t=='reset'||t=='button'||(t=='checkbox'||t=='radio')&&!el.checked||(t=='submit'||t=='image')&&el.form&&el.form.clk!=el||tag=='select'&&el.selectedIndex==-1))
return null;if(tag=='select'){var index=el.selectedIndex;if(index<0)return null;var a=[],ops=el.options;var one=(t=='select-one');var max=(one?index+1:ops.length);for(var i=(one?index:0);i<max;i++){var op=ops[i];if(op.selected){var v=$.browser.msie&&!(op.attributes['value'].specified)?op.text:op.value;if(one)return v;a.push(v);}}
return a;}
return el.value;};$.fn.clearForm=function(){return this.each(function(){$('input,select,textarea',this).clearFields();});};$.fn.clearFields=$.fn.clearInputs=function(){return this.each(function(){var t=this.type,tag=this.tagName.toLowerCase();if(t=='text'||t=='password'||tag=='textarea')
this.value='';else if(t=='checkbox'||t=='radio')
this.checked=false;else if(tag=='select')
this.selectedIndex=-1;});};$.fn.resetForm=function(){return this.each(function(){if(typeof this.reset=='function'||(typeof this.reset=='object'&&!this.reset.nodeType))
this.reset();});};$.fn.enable=function(b){if(b==undefined)b=true;return this.each(function(){this.disabled=!b});};$.fn.select=function(select){if(select==undefined)select=true;return this.each(function(){var t=this.type;if(t=='checkbox'||t=='radio')
this.checked=select;else if(this.tagName.toLowerCase()=='option'){var $sel=$(this).parent('select');if(select&&$sel[0]&&$sel[0].type=='select-one'){$sel.find('option').select(false);}
this.selected=select;}});};})(jQuery);
/* jquery.flydom.js */
jQuery.fn.createAppend=function(element,attrs,content)
{var parentElement=this[0];if(jQuery.browser.msie&&element=='input'&&attrs.type){var element=document.createElement('<'+element+' type="'+attrs.type+'" />');}else{var element=document.createElement(element);};if(parentElement.nodeName.toLowerCase()=='table'&&element.nodeName.toLowerCase()=='tr'){if(parentElement.parentNode&&parentElement.parentNode.getElementsByTagName('tbody')[0]){var tbody=parentElement.parentNode.getElementsByTagName('tbody')[0];}else{var tbody=parentElement.appendChild(document.createElement('tbody'));};var element=tbody.appendChild(element);}else{var element=parentElement.appendChild(element);};element=__FlyDOM_parseAttrs(element,attrs);if(typeof content=='object'&&content!=null){for(var i=0;i<content.length;i=i+3){jQuery(element).createAppend(content[i],content[i+1]||{},content[i+2]||[]);};}else if(content!=null){element=__FlyDOM_setText(element,content);};return jQuery(element);}
jQuery.fn.createPrepend=function(element,attrs,content)
{var element=document.createElement(element);if(this[0].hasChildNodes()==false){var element=this[0].appendChild(element);};element=__FlyDOM_parseAttrs(element,attrs);if(typeof content=='object'&&content!=null){for(var i=0;i<content.length;i=i+3){jQuery(element).createAppend(content[i],content[i+1]||{},content[i+2]||[]);};}else if(content!=null){element=__FlyDOM_setText(element,content);};if(this[0].hasChildNodes()==true){var element=this[0].insertBefore(element,this[0].firstChild);};return jQuery(element);}
jQuery.fn.tplAppend=function(json,tpl)
{if(json.constructor!=Array){json=[json];};if(json.length==0){return false;};for(var i=0;i<json.length;i++){var results=tpl.apply(json[i]);for(var j=0;j<results.length;j=j+3){jQuery(this).createAppend(results[j],results[j+1],results[j+2]);};};return this;}
jQuery.fn.tplPrepend=function(json,tpl){var self=this[0];if(json.constructor!=Array){json=[json];};if(json.length==0){return false;};var div=document.createElement('div');for(var i=0;i<json.length;i++){var results=tpl.apply(json[i]);for(var j=0;j<results.length;j=j+3){jQuery(div).createAppend(results[j],results[j+1],results[j+2]);};};for(i=div.childNodes.length-1;i>=0;i--){if(jQuery.browser.msie&&self.nodeName.toLowerCase()=='table'&&div.childNodes[i].nodeName.toLowerCase()=='tr'){if(self.getElementsByTagName('tbody')[0]){var tbodyElement=self.getElementsByTagName('tbody')[0];tbodyElement.insertBefore(div.childNodes[i],tbodyElement.firstChild);}else{var tbodyElement=self.insertBefore(document.createElement('tbody'),self.firstChild);tbodyElement.appendChild(tbodyElement.appendChild(div.childNodes[i]));};}else{self.insertBefore(div.childNodes[i],self.firstChild);};};return this;};String.prototype.toCamelCase=function()
{var self=this;var special={'class':'className','colspan':'colSpan','rowspan':'rowSpan','for':'htmlFor','httpequiv':'httpEquiv','alink':'aLink','vlink':'vLink','bgcolor':'bgColor','acceptcharset':'acceptCharset','selectedindex':'selectedIndex','tabindex':'tabIndex','selected':'defaultSelected','checked':'defaultChecked','value':'defaultValue','accesskey':'accessKey','noshade':'noShade','datetime':'dateTime','usemap':'useMap','lowsrc':'lowSrc','longdesc':'longDesc','ismap':'isMap','codebase':'codeBase','codetype':'codeType','valuetype':'valueType','nohref':'noHref','thead':'tHead','tfoot':'tFoot','cellpadding':'cellPadding','cellspacing':'cellSpacing','charoff':'chOff','valign':'vAlign','frameborder':'frameBorder','marginheight':'marginHeight','marginwidth':'marginWidth','noresize':'noResize'};if(special[self]!=''&&typeof special[self]!='undefined'){return special[self];}
if(self.indexOf('-')>0){var parts=self.split('-');self=parts[0];for(i=1;i<parts.length;i++){self+=parts[i].substr(0,1).toUpperCase()+parts[i].substr(1).toLowerCase();};};return self;};String.prototype.trim=function()
{return this.replace(/^\s+|\s+$/g,'');};__FlyDOM_parseAttrs=function(element,attrs)
{for(attr in attrs){var attrName=attr;var attrValue=attrs[attr];switch(attrName){case'style':if(typeof attrValue=='string'){var params=attrValue.split(';');for(var i=0;i<params.length;i++){if(params[i].trim()!=''){var styleName=params[i].split(':')[0].trim();var styleValue=params[i].split(':')[1].trim();styleName=styleName.toCamelCase();if(styleName!=''){element.style[styleName]=styleValue;};};};}else if(typeof attrValue=='object'){for(styleName in attrValue){var styleNameCamel=styleName.toCamelCase();if(styleName.trim()!=''){element.style[styleNameCamel]=attrValue[styleName];};};};break;default:if(attrName.substr(0,2)=='on'){var event=attrName.substr(2);attrValue=(typeof attrValue!='function')?eval('f = function() { '+attrValue+'}'):attrValue;jQuery(element).bind(event,attrValue);}else{element[attrName.toCamelCase()]=attrValue;}};};return element;};__FlyDOM_setText=function(element,content)
{var isHtml=/(<\S[^><]*>)|(&.+;)/g;if(content.match(isHtml)!=null&&element.tagName.toUpperCase()!='TEXTAREA'){element.innerHTML=content;}else{var textNode=document.createTextNode(content);element.appendChild(textNode);};return element;};
/* jquery.maskedinput.js */
(function($){$.fn.caret=function(begin,end){if(this.length==0)return;if(typeof begin=='number'){end=(typeof end=='number')?end:begin;return this.each(function(){if(this.setSelectionRange){this.focus();this.setSelectionRange(begin,end);}else if(this.createTextRange){var range=this.createTextRange();range.collapse(true);range.moveEnd('character',end);range.moveStart('character',begin);range.select();}});}else{if(this[0].setSelectionRange){begin=this[0].selectionStart;end=this[0].selectionEnd;}else if(document.selection&&document.selection.createRange){var range=document.selection.createRange();begin=0-range.duplicate().moveStart('character',-100000);end=begin+range.text.length;}
return{begin:begin,end:end};}};var charMap={'9':"[0-9]",'a':"[A-Za-z]",'*':"[A-Za-z0-9]"};$.mask={addPlaceholder:function(c,r){charMap[c]=r;}};$.fn.unmask=function(){return this.trigger("unmask");};$.fn.mask=function(mask,settings){settings=$.extend({placeholder:"_",completed:null},settings);var re=new RegExp("^"+
$.map(mask.split(""),function(c,i){return charMap[c]||((/[A-Za-z0-9]/.test(c)?"":"\\")+c);}).join('')+"$");return this.each(function(){var input=$(this);var buffer=new Array(mask.length);var locked=new Array(mask.length);var valid=false;var ignore=false;var firstNonMaskPos=null;$.each(mask.split(""),function(i,c){locked[i]=(charMap[c]==null);buffer[i]=locked[i]?c:settings.placeholder;if(!locked[i]&&firstNonMaskPos==null)
firstNonMaskPos=i;});function focusEvent(){checkVal();writeBuffer();setTimeout(function(){$(input[0]).caret(valid?mask.length:firstNonMaskPos);},0);};function keydownEvent(e){var pos=$(this).caret();var k=e.keyCode;ignore=(k<16||(k>16&&k<32)||(k>32&&k<41));if((pos.begin-pos.end)!=0&&(!ignore||k==8||k==46)){clearBuffer(pos.begin,pos.end);}
if(k==8){while(pos.begin-->=0){if(!locked[pos.begin]){buffer[pos.begin]=settings.placeholder;if($.browser.opera){s=writeBuffer();input.val(s.substring(0,pos.begin)+" "+s.substring(pos.begin));$(this).caret(pos.begin+1);}else{writeBuffer();$(this).caret(Math.max(firstNonMaskPos,pos.begin));}
return false;}}}else if(k==46){clearBuffer(pos.begin,pos.begin+1);writeBuffer();$(this).caret(Math.max(firstNonMaskPos,pos.begin));return false;}else if(k==27){clearBuffer(0,mask.length);writeBuffer();$(this).caret(firstNonMaskPos);return false;}};function keypressEvent(e){if(ignore){ignore=false;return(e.keyCode==8)?false:null;}
e=e||window.event;var k=e.charCode||e.keyCode||e.which;var pos=$(this).caret();if(e.ctrlKey||e.altKey){return true;}else if((k>=41&&k<=122)||k==32||k>186){var p=seekNext(pos.begin-1);if(p<mask.length){if(new RegExp(charMap[mask.charAt(p)]).test(String.fromCharCode(k))){buffer[p]=String.fromCharCode(k);writeBuffer();var next=seekNext(p);$(this).caret(next);if(settings.completed&&next==mask.length)
settings.completed.call(input);}}}
return false;};function clearBuffer(start,end){for(var i=start;i<end&&i<mask.length;i++){if(!locked[i])
buffer[i]=settings.placeholder;}};function writeBuffer(){return input.val(buffer.join('')).val();};function checkVal(){var test=input.val();var pos=0;for(var i=0;i<mask.length;i++){if(!locked[i]){buffer[i]=settings.placeholder;while(pos++<test.length){var reChar=new RegExp(charMap[mask.charAt(i)]);if(test.charAt(pos-1).match(reChar)){buffer[i]=test.charAt(pos-1);break;}}}}
var s=writeBuffer();if(!s.match(re)){input.val("");clearBuffer(0,mask.length);valid=false;}else
valid=true;};function seekNext(pos){while(++pos<mask.length){if(!locked[pos])
return pos;}
return mask.length;};input.one("unmask",function(){input.unbind("focus",focusEvent);input.unbind("blur",checkVal);input.unbind("keydown",keydownEvent);input.unbind("keypress",keypressEvent);if($.browser.msie)
this.onpaste=null;else if($.browser.mozilla)
this.removeEventListener('input',checkVal,false);});input.bind("focus",focusEvent);input.bind("blur",checkVal);input.bind("keydown",keydownEvent);input.bind("keypress",keypressEvent);if($.browser.msie)
this.onpaste=function(){setTimeout(checkVal,0);};else if($.browser.mozilla)
this.addEventListener('input',checkVal,false);checkVal();});};})(jQuery);
/* jquery.accordion.js */
jQuery.iAccordion={build:function(options)
{return this.each(function()
{if(!options.headerSelector||!options.panelSelector)
return;var el=this;el.accordionCfg={panelHeight:options.panelHeight||300,headerSelector:options.headerSelector,panelSelector:options.panelSelector,activeClass:options.activeClass||'fakeAccordionClass',hoverClass:options.hoverClass||'fakeAccordionClass',onShow:options.onShow&&typeof options.onShow=='function'?options.onShow:false,onHide:options.onShow&&typeof options.onHide=='function'?options.onHide:false,onClick:options.onClick&&typeof options.onClick=='function'?options.onClick:false,headers:jQuery(options.headerSelector,this),panels:jQuery(options.panelSelector,this),speed:options.speed||400,currentPanel:options.currentPanel||0};el.accordionCfg.panels.hide().css('height','1px').eq(el.accordionCfg.currentPanel).css({height:el.accordionCfg.panelHeight+'px',display:'block'}).end();el.accordionCfg.headers.each(function(nr)
{this.accordionPos=nr;}).hover(function()
{jQuery(this).addClass(el.accordionCfg.hoverClass);},function()
{jQuery(this).removeClass(el.accordionCfg.hoverClass);}).bind('click',function(e)
{if(el.accordionCfg.currentPanel==this.accordionPos)
return;el.accordionCfg.headers.eq(el.accordionCfg.currentPanel).removeClass(el.accordionCfg.activeClass).end().eq(this.accordionPos).addClass(el.accordionCfg.activeClass).end();el.accordionCfg.panels.eq(el.accordionCfg.currentPanel).animate({height:0},el.accordionCfg.speed,function()
{this.style.display='none';if(el.accordionCfg.onHide){el.accordionCfg.onHide.apply(el,[this]);}}).end().eq(this.accordionPos).show().animate({height:el.accordionCfg.panelHeight},el.accordionCfg.speed,function()
{this.style.display='block';if(el.accordionCfg.onShow){el.accordionCfg.onShow.apply(el,[this]);}}).end();if(el.accordionCfg.onClick){el.accordionCfg.onClick.apply(el,[this,el.accordionCfg.panels.get(this.accordionPos),el.accordionCfg.headers.get(el.accordionCfg.currentPanel),el.accordionCfg.panels.get(el.accordionCfg.currentPanel)]);}
el.accordionCfg.currentPanel=this.accordionPos;}).eq(el.accordionCfg.currentPanel).addClass(el.accordionCfg.activeClass).end();jQuery(this).css('height',jQuery(this).css('height')).css('overflow','hidden');});}};jQuery.fn.Accordion=jQuery.iAccordion.build;
/* jquery.checkbox.js */
jQuery.fn.checkbox=function(mode){var mode=mode||'seleccionar';var c=0;var ids=new Array();var id;this.each(function(){switch(mode){case'seleccionar':this.checked=true;break;case'deseleccionar':this.checked=false;break;case'invertir':this.checked=!this.checked;break;case'contar':if(this.checked){c++;}
break;case'retornarIds':if(this.checked){id=this.id.toString();ids.push(id.substring(19));}
break;}});if(mode=='contar'){return c;}
else if(mode=='retornarIds'){return ids.join("|");}};