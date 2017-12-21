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
		//alert(xmlhttp.responseText);
		return xmlhttp.responseText;
	}

}//fin funcion consulta ajax


function consultar_eapb(tipo_id,identificacion)
{
	var prestador_cod= document.getElementById("prestador").value;
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}


function seleccionar_periodo()
{
	var fecha_corte = document.getElementById("fechas_corte").value;
	if(fecha_corte=="3-31")
	{
		document.getElementById("periodo").value="1";
	}
	if(fecha_corte=="6-30")
	{
		document.getElementById("periodo").value="2";
	}
	if(fecha_corte=="9-30")
	{
		document.getElementById("periodo").value="3";
	}
	if(fecha_corte=="12-31")
	{
		document.getElementById("periodo").value="4";
	}
}

function seleccionar_fecha_de_corte()
{
	var periodo = document.getElementById("periodo").value;
	
	if(periodo==1)
	{
		document.getElementById("fechas_corte").value="3-31";
	}
	if(periodo==2)
	{
		document.getElementById("fechas_corte").value="6-30";
	}
	if(periodo==3)
	{
		document.getElementById("fechas_corte").value="9-30";
	}
	if(periodo==4)
	{
		document.getElementById("fechas_corte").value="12-31";
	}
}

$(function() 
{
    $("#selector_estado_info").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione un estado para la informaci&oacuten<br>'});
    $("#fechas_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la fecha de corte correspondiente al reporte<br>'});
	$("#year_de_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione el a&ntildeo para la fecha de corte correspondiente al reporte<br>'});
    $("#periodo").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el periodo correspondiente al reporte de PyP<br>'});
    $("#prestador").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el prestador que reportara 4505 PyP<br>'});
    $("#eapb").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione la eps a reportar 4505 PyP<br>'});
   
});

function download_reporte(ruta_descarga_archivo)
{ 
	window.open(ruta_descarga_archivo,'Download');
}

function enviar_formulario()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	var year = $('#year_de_corte').val();
	
	if(intRegex.test(year)) 
	{
		
	}
	else
	{
		mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
	}
	
	if($('#selector_estado_info').val()=="none")
	{
		mensaje_error+="Seleccione el estado de la informaci&oacuten de los reportes 4505.<br></br>";
	}
	
	if($('#prestador').val()=="none")
	{
		mensaje_error+="Seleccione el prestador para el cual reportara 4505.<br></br>";
	}
	
	if($('#eapb').val()=="none")
	{
		mensaje_error+="Seleccione la entidad administradora EAPB para el cual reportara 4505.<br></br>";
	}
	
	if(mensaje_error=="")
	{
		document.reporte_obligatorio.submit();
	}
	else
	{
		$("h3#tituloVentana").html("Advertencia");
        $("div#mensajeVentana").html("<p>" + mensaje_error + "</p>");
        $('#myModal').modal('toggle');
	}
}