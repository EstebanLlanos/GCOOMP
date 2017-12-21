tday  =new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
tmonth=new Array("January","February","March","April","May","June","July","August","September","October","November","December");

function GetClock()
{
d = new Date();
nday   = d.getDay();
nmonth = d.getMonth();
ndate  = d.getDate();
nyear = d.getYear();
nhour  = d.getHours();
nmin   = d.getMinutes();
nsec   = d.getSeconds();

if(nyear<1000) nyear=nyear+1900;

     if(nhour ==  0) {ap = " AM";nhour = 12;} 
else if(nhour <= 11) {ap = " AM";} 
else if(nhour == 12) {ap = " PM";} 
else if(nhour >= 13) {ap = " PM";nhour -= 12;}

if(nmin <= 9) {nmin = "0" +nmin;}
if(nsec <= 9) {nsec = "0" +nsec;}


//document.getElementById('clockbox').innerHTML=""+tday[nday]+", "+tmonth[nmonth]+" "+ndate+", "+nyear+" "+nhour+":"+nmin+":"+nsec+ap+"";
document.getElementById('clockbox').innerHTML=""+nhour+":"+nmin+":"+nsec+ap+"";
setTimeout("GetClock()", 1000);
}//fin if funcion obtener reloj
window.onload=GetClock;

$(function() {
    $( "#tabs" ).tabs();
  });

$(function() {
    $( "#tabs-2" ).tabs();
  });

$(function() {
    $( "#tabs-1" ).tabs();
  });

$(function() {
    $( "#tabs-3" ).tabs();
  });

$(function() {
    $( "#help_tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#help_tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
  });
function download_inconsistencias_campos(ruta)
{
	
	window.open(ruta,'Download');
}


//especificar none si el resultado de la peticion ajax no sera contenida en un div
function ConsultaAJAX(parametros,filePHP,divContent)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	if(divContent!="none")
	{
		xmlhttp.open("GET",filePHP+"?"+parametros+"&campodiv="+divContent,false);
		xmlhttp.send();
		document.getElementById(divContent).innerHTML=xmlhttp.responseText;
	}
	else
	{
		xmlhttp.open("GET",filePHP+"?"+parametros,false);
		xmlhttp.send();
		alert(xmlhttp.responseText);
		return xmlhttp.responseText;
	}

}//fin funcion consulta ajax

//especificar none si el resultado de la peticion ajax no sera contenida en un div
//es asincrona, no pone warning
function ConsultaAJAX_Async(parametros,filePHP,divContent)
{
	var xmlhttp;
	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	if(divContent!="none")
	{
	    xmlhttp.onreadystatechange=function()
	    {
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			    document.getElementById(divContent).innerHTML=xmlhttp.responseText;
			}
	    }
	    
	    xmlhttp.open("GET",filePHP+"?"+parametros+"&campodiv="+divContent,true);
	    xmlhttp.send();
		
	}
	else
	{
	    xmlhttp.onreadystatechange=function()
	    {
			if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			    alert(xmlhttp.responseText);
			    return xmlhttp.responseText;
			}
	    }
	    
	    xmlhttp.open("GET",filePHP+"?"+parametros,true);
	    xmlhttp.send();
		
	}

}//fin funcion consulta ajax


function indicar_descarga_exitosa(codigo_entidad_reportadora,nombre_archivo,fecha_remision,fecha_validacion,hora_validacion,nick_usuario,norma)
{
    var r = confirm("Esta seguro que desea descartar el archivo generado?");
    if (r==true)
    {
        ConsultaAJAX_Async("codigo_entidad_reportadora="+codigo_entidad_reportadora+"&nombre_archivo="+nombre_archivo+"&fecha_remision="+fecha_remision+"&fecha_validacion="+fecha_validacion+"&hora_validacion="+hora_validacion+"&nick_usuario="+nick_usuario+"&norma="+norma,"descarga_exitosa.php","none");
        var nick_hidden=document.getElementById("nick_hidden").value;
        administrador_de_tareas_ajax(nick_hidden);
    }
	
}

function cancelar_ejecucion(codigo_entidad_reportadora,nombre_archivo,fecha_remision,fecha_validacion,hora_validacion,nick_usuario,norma)
{
    var r = confirm("Esta seguro que desea parar la ejecucion del archivo generado?");
    if (r==true)
    {
        ConsultaAJAX_Async("codigo_entidad_reportadora="+codigo_entidad_reportadora+"&nombre_archivo="+nombre_archivo+"&fecha_remision="+fecha_remision+"&fecha_validacion="+fecha_validacion+"&hora_validacion="+hora_validacion+"&nick_usuario="+nick_usuario+"&norma="+norma,"cancelar_ejecucion.php","none");
        var nick_hidden=document.getElementById("nick_hidden").value;
        administrador_de_tareas_ajax(nick_hidden);
    }
	
}

function refresh_now()
{
    var nick_hidden=document.getElementById("nick_hidden").value;
    administrador_de_tareas_ajax(nick_hidden);
    alert("Se han actualizado los datos mostrados.");
}

var ultima_seleccion_vl="4505";
var ultima_seleccion_ro="4505";
var ultima_seleccion_ar="4505";

function administrador_de_tareas_ajax(nick_user)
{
    ConsultaAJAX_Async("nick_user="+nick_user+"&ultima_seleccion_vl="+ultima_seleccion_vl,"administrador_de_tareas.php","tabs-1");
    
    ConsultaAJAX_Async("nick_user="+nick_user+"&ultima_seleccion_ro="+ultima_seleccion_ro,"administrador_de_tareas_ro.php","tabs-2");
    
    ConsultaAJAX_Async("nick_user="+nick_user+"&ultima_seleccion_ar="+ultima_seleccion_ar,"administrador_de_tareas_ar.php","tabs-3");
    
}//fin function administrador de tareas


//funciones para tab de forma manual
function tab_mouse_enter_manual(tab_id)
{
    if (document.getElementById(tab_id))
    {
	
	if (document.getElementById("li-"+tab_id).getAttribute("aria-selected")=="false")
	{
	    document.getElementById(tab_id).className += " ui-state-hover";
	    document.getElementById(tab_id).style.color="#ffffff";
	}
	
    }
}//fin function

function tab_mouse_leave_manual(tab_id)
{
    if (document.getElementById(tab_id))
    {
	if (document.getElementById("li-"+tab_id).getAttribute("aria-selected")=="false")
	{
	    document.getElementById(tab_id).className = document.getElementById(tab_id).className.replace( /(?:^|\s)ui-state-hover(?!\S)/g , '' );	
	    document.getElementById(tab_id).style.color="#0073EA";
	}
    }
}//fin function


function tab_onclick_manual(tab_id, nombre_lista_ul)
{
    if (document.getElementById(tab_id))
    {
	if (document.getElementById("li-"+tab_id).getAttribute("aria-selected")=="false")
	{
	    //recorre los li
	    $('#'+nombre_lista_ul+' li').each
	    (	function()
		{
		    //alert($(this).attr("id"));
		    $(this).attr('aria-selected',"false");
		    $(this).attr('tabindex',"-1");
		    $(this).attr('class',$(this).attr('class').replace( /(?:^|\s)ui-tabs-active(?!\S)/g , '' ));
		    $(this).attr('class',$(this).attr('class').replace( /(?:^|\s)ui-state-active(?!\S)/g , '' ));
		    
		    //recorre los a
		    var id_li_actual=$(this).attr("id");
		    $('#'+id_li_actual+' a').each(
			function()
			{
			    //alert("<a>:"+$(this).attr("id"));
			    $(this).attr('class',$(this).attr('class').replace( /(?:^|\s)ui-state-hover(?!\S)/g , '' ));
			    $(this).css("color","#0073EA");
			}
		    );
		}
	    );
	    
	    document.getElementById("li-"+tab_id).setAttribute("aria-selected","true");
	    document.getElementById("li-"+tab_id).setAttribute("tabindex","0");
	    document.getElementById("li-"+tab_id).className += " ui-tabs-active ui-state-active";	    
	    document.getElementById("li-"+tab_id).style.backgroundColor = "#FFFFFF";
	    document.getElementById(tab_id).style.color = "#FF0084";
	    
	    //tabs consolidado
	    if (tab_id=="ui-id-5")
	    {
		document.getElementById("tab_ro_1").style.display = "block";
		document.getElementById("tab_ro_1").setAttribute("aria-expanded","true");
		document.getElementById("tab_ro_1").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ro="4505";
		
		//oculta los demas
		document.getElementById("tab_ro_2").style.display = "none";
		document.getElementById("tab_ro_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_3").style.display = "none";
		document.getElementById("tab_ro_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_4").style.display = "none";
		document.getElementById("tab_ro_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_5").style.display = "none";
		document.getElementById("tab_ro_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_6").style.display = "none";
		document.getElementById("tab_ro_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_6").setAttribute("aria-hidden","true");

	    }
	    if (tab_id=="ui-id-6")
	    {
		document.getElementById("tab_ro_2").style.display = "block";
		document.getElementById("tab_ro_2").setAttribute("aria-expanded","true");
		document.getElementById("tab_ro_2").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ro="0123";
		
		//oculta los demas
		document.getElementById("tab_ro_1").style.display = "none";
		document.getElementById("tab_ro_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_3").style.display = "none";
		document.getElementById("tab_ro_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_4").style.display = "none";
		document.getElementById("tab_ro_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_5").style.display = "none";
		document.getElementById("tab_ro_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_6").style.display = "none";
		document.getElementById("tab_ro_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-7")
	    {
		document.getElementById("tab_ro_3").style.display = "block";
		document.getElementById("tab_ro_3").setAttribute("aria-expanded","true");
		document.getElementById("tab_ro_3").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ro="0247";
		
		//oculta los demas
		document.getElementById("tab_ro_2").style.display = "none";
		document.getElementById("tab_ro_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_1").style.display = "none";
		document.getElementById("tab_ro_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_4").style.display = "none";
		document.getElementById("tab_ro_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_5").style.display = "none";
		document.getElementById("tab_ro_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_6").style.display = "none";
		document.getElementById("tab_ro_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-8")
	    {
		document.getElementById("tab_ro_4").style.display = "block";
		document.getElementById("tab_ro_4").setAttribute("aria-expanded","true");
		document.getElementById("tab_ro_4").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ro="4725";
		
		//oculta los demas
		document.getElementById("tab_ro_2").style.display = "none";
		document.getElementById("tab_ro_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_3").style.display = "none";
		document.getElementById("tab_ro_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_1").style.display = "none";
		document.getElementById("tab_ro_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_5").style.display = "none";
		document.getElementById("tab_ro_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_6").style.display = "none";
		document.getElementById("tab_ro_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-9")
	    {
		document.getElementById("tab_ro_5").style.display = "block";
		document.getElementById("tab_ro_5").setAttribute("aria-expanded","true");
		document.getElementById("tab_ro_5").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ro="2463";
		
		//oculta los demas
		document.getElementById("tab_ro_2").style.display = "none";
		document.getElementById("tab_ro_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_3").style.display = "none";
		document.getElementById("tab_ro_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_4").style.display = "none";
		document.getElementById("tab_ro_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_1").style.display = "none";
		document.getElementById("tab_ro_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_6").style.display = "none";
		document.getElementById("tab_ro_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-10")
	    {
		document.getElementById("tab_ro_6").style.display = "block";
		document.getElementById("tab_ro_6").setAttribute("aria-expanded","true");
		document.getElementById("tab_ro_6").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ro="1393";
		
		//oculta los demas
		document.getElementById("tab_ro_2").style.display = "none";
		document.getElementById("tab_ro_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_3").style.display = "none";
		document.getElementById("tab_ro_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_4").style.display = "none";
		document.getElementById("tab_ro_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_5").style.display = "none";
		document.getElementById("tab_ro_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ro_1").style.display = "none";
		document.getElementById("tab_ro_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ro_1").setAttribute("aria-hidden","true");
	    }
	    //fin tabs consolidado
	    
	    //tabs validaciones
	    if (tab_id=="ui-id-11")
	    {
		document.getElementById("tab_vl_1").style.display = "block";
		document.getElementById("tab_vl_1").setAttribute("aria-expanded","true");
		document.getElementById("tab_vl_1").setAttribute("aria-hidden","false");
		
		ultima_seleccion_vl="4505";
		
		//oculta los demas
		document.getElementById("tab_vl_2").style.display = "none";
		document.getElementById("tab_vl_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_3").style.display = "none";
		document.getElementById("tab_vl_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_4").style.display = "none";
		document.getElementById("tab_vl_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_5").style.display = "none";
		document.getElementById("tab_vl_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_6").style.display = "none";
		document.getElementById("tab_vl_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_6").setAttribute("aria-hidden","true");

	    }
	    if (tab_id=="ui-id-12")
	    {
		document.getElementById("tab_vl_2").style.display = "block";
		document.getElementById("tab_vl_2").setAttribute("aria-expanded","true");
		document.getElementById("tab_vl_2").setAttribute("aria-hidden","false");
		
		ultima_seleccion_vl="0123";
		
		//oculta los demas
		document.getElementById("tab_vl_1").style.display = "none";
		document.getElementById("tab_vl_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_3").style.display = "none";
		document.getElementById("tab_vl_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_4").style.display = "none";
		document.getElementById("tab_vl_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_5").style.display = "none";
		document.getElementById("tab_vl_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_6").style.display = "none";
		document.getElementById("tab_vl_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-13")
	    {
		document.getElementById("tab_vl_3").style.display = "block";
		document.getElementById("tab_vl_3").setAttribute("aria-expanded","true");
		document.getElementById("tab_vl_3").setAttribute("aria-hidden","false");
		
		ultima_seleccion_vl="0247";
		
		//oculta los demas
		document.getElementById("tab_vl_2").style.display = "none";
		document.getElementById("tab_vl_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_1").style.display = "none";
		document.getElementById("tab_vl_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_4").style.display = "none";
		document.getElementById("tab_vl_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_5").style.display = "none";
		document.getElementById("tab_vl_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_6").style.display = "none";
		document.getElementById("tab_vl_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-14")
	    {
		document.getElementById("tab_vl_4").style.display = "block";
		document.getElementById("tab_vl_4").setAttribute("aria-expanded","true");
		document.getElementById("tab_vl_4").setAttribute("aria-hidden","false");
		
		ultima_seleccion_vl="4725";
		
		//oculta los demas
		document.getElementById("tab_vl_2").style.display = "none";
		document.getElementById("tab_vl_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_3").style.display = "none";
		document.getElementById("tab_vl_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_1").style.display = "none";
		document.getElementById("tab_vl_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_5").style.display = "none";
		document.getElementById("tab_vl_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_6").style.display = "none";
		document.getElementById("tab_vl_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-15")
	    {
		document.getElementById("tab_vl_5").style.display = "block";
		document.getElementById("tab_vl_5").setAttribute("aria-expanded","true");
		document.getElementById("tab_vl_5").setAttribute("aria-hidden","false");
		
		ultima_seleccion_vl="2463";
		
		//oculta los demas
		document.getElementById("tab_vl_2").style.display = "none";
		document.getElementById("tab_vl_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_3").style.display = "none";
		document.getElementById("tab_vl_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_4").style.display = "none";
		document.getElementById("tab_vl_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_1").style.display = "none";
		document.getElementById("tab_vl_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_6").style.display = "none";
		document.getElementById("tab_vl_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-16")
	    {
		document.getElementById("tab_vl_6").style.display = "block";
		document.getElementById("tab_vl_6").setAttribute("aria-expanded","true");
		document.getElementById("tab_vl_6").setAttribute("aria-hidden","false");
		
		ultima_seleccion_vl="1393";
		
		//oculta los demas
		document.getElementById("tab_vl_2").style.display = "none";
		document.getElementById("tab_vl_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_3").style.display = "none";
		document.getElementById("tab_vl_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_4").style.display = "none";
		document.getElementById("tab_vl_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_5").style.display = "none";
		document.getElementById("tab_vl_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_vl_1").style.display = "none";
		document.getElementById("tab_vl_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_vl_1").setAttribute("aria-hidden","true");
	    }
	    //fin tabs validaciones
	    
	    //tabs reparaciones
	    if (tab_id=="ui-id-17")
	    {
		document.getElementById("tab_ar_1").style.display = "block";
		document.getElementById("tab_ar_1").setAttribute("aria-expanded","true");
		document.getElementById("tab_ar_1").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ar="4505";
		
		//oculta los demas
		document.getElementById("tab_ar_2").style.display = "none";
		document.getElementById("tab_ar_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_3").style.display = "none";
		document.getElementById("tab_ar_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_4").style.display = "none";
		document.getElementById("tab_ar_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_5").style.display = "none";
		document.getElementById("tab_ar_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_6").style.display = "none";
		document.getElementById("tab_ar_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_6").setAttribute("aria-hidden","true");

	    }
	    if (tab_id=="ui-id-18")
	    {
		document.getElementById("tab_ar_2").style.display = "block";
		document.getElementById("tab_ar_2").setAttribute("aria-expanded","true");
		document.getElementById("tab_ar_2").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ar="0123";
		
		//oculta los demas
		document.getElementById("tab_ar_1").style.display = "none";
		document.getElementById("tab_ar_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_3").style.display = "none";
		document.getElementById("tab_ar_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_4").style.display = "none";
		document.getElementById("tab_ar_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_5").style.display = "none";
		document.getElementById("tab_ar_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_6").style.display = "none";
		document.getElementById("tab_ar_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-19")
	    {
		document.getElementById("tab_ar_3").style.display = "block";
		document.getElementById("tab_ar_3").setAttribute("aria-expanded","true");
		document.getElementById("tab_ar_3").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ar="0247";
		
		//oculta los demas
		document.getElementById("tab_ar_2").style.display = "none";
		document.getElementById("tab_ar_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_1").style.display = "none";
		document.getElementById("tab_ar_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_4").style.display = "none";
		document.getElementById("tab_ar_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_5").style.display = "none";
		document.getElementById("tab_ar_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_6").style.display = "none";
		document.getElementById("tab_ar_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-20")
	    {
		document.getElementById("tab_ar_4").style.display = "block";
		document.getElementById("tab_ar_4").setAttribute("aria-expanded","true");
		document.getElementById("tab_ar_4").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ar="4725";
		
		//oculta los demas
		document.getElementById("tab_ar_2").style.display = "none";
		document.getElementById("tab_ar_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_3").style.display = "none";
		document.getElementById("tab_ar_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_1").style.display = "none";
		document.getElementById("tab_ar_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_5").style.display = "none";
		document.getElementById("tab_ar_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_6").style.display = "none";
		document.getElementById("tab_ar_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-21")
	    {
		document.getElementById("tab_ar_5").style.display = "block";
		document.getElementById("tab_ar_5").setAttribute("aria-expanded","true");
		document.getElementById("tab_ar_5").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ar="2463";
		
		//oculta los demas
		document.getElementById("tab_ar_2").style.display = "none";
		document.getElementById("tab_ar_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_3").style.display = "none";
		document.getElementById("tab_ar_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_4").style.display = "none";
		document.getElementById("tab_ar_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_1").style.display = "none";
		document.getElementById("tab_ar_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_1").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_6").style.display = "none";
		document.getElementById("tab_ar_6").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_6").setAttribute("aria-hidden","true");
	    }
	    if (tab_id=="ui-id-22")
	    {
		document.getElementById("tab_ar_6").style.display = "block";
		document.getElementById("tab_ar_6").setAttribute("aria-expanded","true");
		document.getElementById("tab_ar_6").setAttribute("aria-hidden","false");
		
		ultima_seleccion_ar="1393";
		
		//oculta los demas
		document.getElementById("tab_ar_2").style.display = "none";
		document.getElementById("tab_ar_2").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_2").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_3").style.display = "none";
		document.getElementById("tab_ar_3").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_3").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_4").style.display = "none";
		document.getElementById("tab_ar_4").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_4").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_5").style.display = "none";
		document.getElementById("tab_ar_5").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_5").setAttribute("aria-hidden","true");
		
		document.getElementById("tab_ar_1").style.display = "none";
		document.getElementById("tab_ar_1").setAttribute("aria-expanded","false");
		document.getElementById("tab_ar_1").setAttribute("aria-hidden","true");
	    }
	    //fin tabs reparaciones
	    
	}//fin if
    }//fin if
}//fin function
