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

var descargando=false;
function download_duplicados(ruta)
{ 
	
	window.open(ruta,'Download');
}

function download_inconsistencias_campos(ruta)
{
	
	window.open(ruta,'Download');
}

function download_errores_caracteres_especiales(ruta)
{	
	window.open(ruta,'Download');
}

function cambiar_amarillo(elemento)
{
	elemento.style.backgroundColor="#ffffe5";
}

function cambiar_blanco(elemento)
{
	elemento.style.backgroundColor="white";
}

function cambiar_gris(elemento)
{
	elemento.style.backgroundColor="#D3D3D3";
}