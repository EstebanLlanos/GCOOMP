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
		return xmlhttp.responseText;
	}
	else
	{
		xmlhttp.open("GET",filePHP+"?"+parametros,false);
		xmlhttp.send();
		//alert(xmlhttp.responseText);
		return xmlhttp.responseText;
	}

}//fin funcion consulta ajax

function mostrar_ocultar_divisor(divisor,boton)
{
	elemento_divisor=document.getElementById(divisor);
	elemento_boton=document.getElementById(boton);
	if(elemento_divisor.style.visibility=="collapse")
	{
		elemento_divisor.style.visibility="visible";
		elemento_boton.value="-";
	}
	else if(elemento_divisor.style.visibility=="visible")
	{
		elemento_divisor.style.visibility="collapse";
		elemento_boton.value="+";
	}
}

function mostrar_desaparecer_divisor(divisor,boton)
{
	elemento_divisor=document.getElementById(divisor);
	elemento_boton=document.getElementById(boton);
	if(elemento_divisor.style.display=="none")
	{
		elemento_divisor.style.display="inline";
		elemento_boton.value="-";
	}
	else if(elemento_divisor.style.display=="inline")
	{
		elemento_divisor.style.display="none";
		elemento_boton.value="+";
	}
}

historia_clinica_validada=false;

function validar_hc()
{
	//alert("validar_hc");
	document.getElementById("datos_paciente_div").style.display="inline";
	document.getElementById("datos_anamnesis_div").style.display="inline";
	document.getElementById("datos_mo_atencion_div").style.display="inline";
	document.getElementById("datos_conducta_div").style.display="inline";
	document.getElementById("datos_ordenamiento_div").style.display="inline";
	
	document.getElementById("boton_mo_div_datos_paciente").value="-";
	document.getElementById("boton_mo_div_anamnesis").value="-";
	document.getElementById("boton_mo_div_mo_atencion").value="-";
	document.getElementById("boton_mo_div_conducta").value="-";
	document.getElementById("boton_mo_div_ordenamiento").value="-";
	
	historia_clinica_validada=true;
}

function cerrar_hc()
{
	//alert("cerrar_hc");
	document.getElementById("datos_paciente_div").style.display="inline";
	document.getElementById("datos_anamnesis_div").style.display="inline";
	document.getElementById("datos_mo_atencion_div").style.display="inline";
	document.getElementById("datos_conducta_div").style.display="inline";
	document.getElementById("datos_ordenamiento_div").style.display="inline";
	
	document.getElementById("boton_mo_div_datos_paciente").value="-";
	document.getElementById("boton_mo_div_anamnesis").value="-";
	document.getElementById("boton_mo_div_mo_atencion").value="-";
	document.getElementById("boton_mo_div_conducta").value="-";
	document.getElementById("boton_mo_div_ordenamiento").value="-";
	
	if (historia_clinica_validada==true)
	{
		document.forms['formulario'].submit();
	}
	else
	{
		alert("No se ha validado la historia clinica.");
	}
}