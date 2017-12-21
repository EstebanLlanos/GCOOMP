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

/*
$(function() {
    $("#fecha_prueba").datepicker();
    $("#fecha_prueba").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_prueba").datepicker($.datepicker.regional[ "es" ]);
});

$(function() {
    $("#fecha_example").datepicker();
    $("#fecha_example").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_example").datepicker($.datepicker.regional[ "es" ]);
});
*/

function obtener_nombre_file_upload(id_file,id_div_destino)
{
	var fullPath = document.getElementById(id_file).value;
	if (fullPath) 
	{
		var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
		var filename = fullPath.substring(startIndex);
		if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) 
		{
			filename = filename.substring(1);
		}
		//alert(filename);
		document.getElementById(id_div_destino).innerHTML=filename;
	}
	else
	{
		document.getElementById(id_div_destino).innerHTML="";
	}
}

function consultar_prestador()
{
	var eapb_cod= document.getElementById("eapb").value;
	
	ConsultaAJAX("eapb="+eapb_cod,"consulta_prestador.php","div_prestador");
	
}

function isNumberKey(evt) 
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function cargar()
{
	//alert('carga');
	document.getElementById('oculto_envio').value="envio";
	document.getElementById("formulario").submit();
}

function limpiar()
{
	alert('limpia');
}