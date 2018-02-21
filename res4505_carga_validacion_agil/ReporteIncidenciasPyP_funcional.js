
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
		//alert(xmlhttp.responseText);
		document.getElementById(divContent).innerHTML=xmlhttp.responseText;
	}
	else
	{
		xmlhttp.open("GET",filePHP+"?"+parametros,false);
		xmlhttp.send();
	
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

function traer_seccion_texto(inicio,fin,ruta,numero_lineas,divisor_destino,modo)
{
	
	ruta_procesada= ruta.replace(/\//g,"|");
	//alert(ruta+" "+ruta_procesada);
	ConsultaAJAX("ini="+inicio+"&fin="+fin+"&ruta="+ruta_procesada+"&nlineas="+numero_lineas+"&modo="+modo+"&divisor_destino="+divisor_destino,"paginacion_archivos.php",divisor_destino);
}

function avanzar_seccion_texto(inicio,fin,ruta,numero_lineas,divisor_destino,modo)
{	
	var new_fin=fin+10;
	
	if(fin < numero_lineas )
	{
		if(numero_lineas<new_fin)
		{
			new_fin=numero_lineas;
		}
		
		traer_seccion_texto(fin,new_fin,ruta,numero_lineas,divisor_destino,modo);
	}//fin if solo si no ah llegado al fin
}

function retroceder_seccion_texto(inicio,fin,ruta,numero_lineas,divisor_destino,modo)
{
	var new_inicio=inicio-10;
	
	if(inicio>0)
	{
		if(0>new_inicio)
		{
			new_inicio=0;
		}
		traer_seccion_texto(new_inicio,inicio,ruta,numero_lineas,divisor_destino,modo);
	}
}