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


function isDate(txtDate)
{
    var reg = /^(0[1-9]|1[012])([\/-])(0[1-9]|[12][0-9]|3[01])\2(\d{4})$/;
    return reg.test(txtDate);
}

function isDate2(txtDate)
{
    var reg = /^(0[1-9]|[12][0-9]|3[01])([\/-])(0[1-9]|1[012])\2(\d{4})$/;
    return reg.test(txtDate);
}

$(function() {
    $("#fecha_remision").datepicker();
    $("#fecha_remision").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_remision").datepicker($.datepicker.regional[ "es" ]);
});

function isNumberKey(evt) 
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
        return false;

    return true;
}


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


function verificar_nombre_archivo(path_val,sigla,div_nombre)
{
	var fullPath = path_val;
	if (fullPath) 
	{
		var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
		var filename = fullPath.substring(startIndex);
		if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) 
		{
			filename = filename.substring(1);
		}
		var nombre_sin_extension=filename.split(".");
		var array_nombre_archivo= nombre_sin_extension[0].split(sigla);
		
		mensaje="";
		if(array_nombre_archivo.length!=2)
		{
			mensaje+="ERROR: Archivo debe ser "+sigla+". <br>";
		}
		
		var prestador = document.getElementById('prestador').value;
		
		
		while(prestador.length<12 && prestador!="none")
		{
			prestador="0"+prestador;
		}
		if(prestador!="none")
		{			
			var prestador_nombre=array_nombre_archivo[0].substring(8,20);
			
			if(prestador_nombre!=prestador)
			{
				
				mensaje+="ERROR: El prestador "+prestador+" no corresponde al prestador indicado en el archivo "+prestador_nombre+". <br>";
			}
			
			
		}
		
		//regimen
		var regimen_nombre=array_nombre_archivo[0].substring(20,21);
		if(regimen_nombre!="C" && regimen_nombre!="S" && regimen_nombre!="P" && regimen_nombre!="N" && regimen_nombre!="E")
		{
			
			mensaje+="ERROR: el regimen "+regimen_nombre+" no corresponde a C-S-P-N-E. <br>";
		}
		//fin regimen
		
		var eapb =document.getElementById('eapb').value;
		while(eapb.length<6 && eapb!="none")
		{
			eapb="0"+eapb;
		}
		if(eapb!="none")
		{			
			var eapb_nombre=array_nombre_archivo[0].substring(21,28);
			//alert(eapb);
			if(eapb_nombre!=eapb)
			{
				
				mensaje+="ERROR: La EAPB "+eapb+" no corresponde al la EAPB indicada en el archivo "+eapb_nombre+". <br>";
			}
			
			
		}
		
		var numero_de_remision_registrado=document.getElementById('numero_de_remision').value;
		if(numero_de_remision_registrado!=array_nombre_archivo[1] && array_nombre_archivo.length==2 && numero_de_remision_registrado!="")
		{
			mensaje+="ERROR: El numero de remision "+numero_de_remision_registrado+" no corresponde al numero de remision "+array_nombre_archivo[1]+" registrado  en el archivo "+nombre_sin_extension[0]+" . <br>";
		}
		
		var year_de_corte_registrado=document.getElementById('year_de_corte').value;
		if(year_de_corte_registrado!="")
		{
			var year_indicado=array_nombre_archivo[0].substring(0,4);
			if(year_de_corte_registrado!=year_indicado)
			{
				mensaje+="ERROR: El a&ntildeo de corte "+year_de_corte_registrado+" no corresponde al a&ntildeo indicado "+year_indicado+" en el archivo "+nombre_sin_extension[0]+". <br>";
			}
		}
		
		var periodo_de_corte_registrado=document.getElementById('periodo').value;
		if(periodo_de_corte_registrado!="none")
		{
			fecha_de_corte_registrada=periodo_de_corte_registrado.split("::")[1];
			mes_de_corte_registrado=fecha_de_corte_registrada.split("-")[0];
			dia_de_corte_registrado=fecha_de_corte_registrada.split("-")[1];
			var mes_indicado=array_nombre_archivo[0].substring(4,6);
			var dia_indicado=array_nombre_archivo[0].substring(6,8);
			if(mes_indicado!=mes_de_corte_registrado)
			{
				mensaje+="ERROR: El mes de corte "+mes_de_corte_registrado+" no corresponde al mes indicado "+mes_indicado+" en el archivo "+nombre_sin_extension[0]+". <br>";
			}
			if(dia_indicado!=dia_de_corte_registrado)
			{
				mensaje+="ERROR: El dia de corte "+dia_de_corte_registrado+" no corresponde al dia indicado "+dia_indicado+" en el archivo "+nombre_sin_extension[0]+". <br>";
			}
		}
		
		if(mensaje!="")
		{
			//alert(mensaje);
			document.getElementById(div_nombre).innerHTML="<span >"+mensaje+"</span>";
			document.getElementById(sigla+"_hidden").value="error";
		}
		else
		{
			document.getElementById(div_nombre).innerHTML=filename;
			document.getElementById(sigla+"_hidden").value="";
		}
	}
	else
	{
		document.getElementById(div_nombre).innerHTML="";
		document.getElementById(sigla+"_hidden").value="";
	}
}


function validar_campos()
{
	var hay_errores= false;
	var mensaje ="";
	
	var fechaActual = new Date();
    //var fechaIngreso = new Date($("#fecha_remision").val());
	
	if(document.getElementById("prestador").value=="none")
	{
		mensaje+='<br>-Seleccione un prestador \n';
	}
	if(document.getElementById("eapb").value=="none")
	{
		mensaje+='<br>-Seleccione un eapb \n';
	}	
	if(document.getElementById("numero_de_remision").value=="")
	{
		mensaje+='<br>-Registre el numero de remision del archivo \n';
	}
	
	if(document.getElementById("periodo").value=="none")
	{
		mensaje+='<br>-Seleccione un periodo \n';
	}
	
	if(document.getElementById("year_de_corte").value=="")
	{
		mensaje+='<br>-Digite el a&ntildeo de corte \n';
	}
	/*
	if (fechaIngreso > fechaActual)
	{
        mensaje += '<br>-La fecha de remision no puede ser mayor a la actual\n';
	}
	
	if ($("#fecha_remision").val() == "")
	{
        mensaje += '<br>-La fecha de remision es obligatoria\n';
	}
	
	if(isDate($("#fecha_remision").val())==false)
	{	
		mensaje += '<br>-La fecha de remision no es una fecha valida\n';
	}
	*/
	
	//verificacion de la carga del archivo ARTE
    if(document.getElementById("1393_ARTE_file").value=="")
	{
		mensaje+='<br>-Seleccione un archivo AR a validar \n';
	}
	
	
	if(document.getElementById("ARTE_hidden").value=="error")
	{
		mensaje+='<br>-EL archivo seleccionado para ARTE no tiene un nombre valido \n';
	}
	
	//fin verificacion de la carga del archivo ARTE
	
	
	if (mensaje == "") 
	{
        return false;
    }
    else 
	{

        $("h3#tituloVentana").html("Advertencia");
        $("div#mensajeVentana").html("<p>" + mensaje + "</p>");
        $('#myModal').modal('toggle');


        return true;
    }
	
	return hay_errores;
}

function cargarARTE()
{
	var hay_errores = validar_campos();		   
	
	document.getElementById('accion').value="validar";
	
	if (hay_errores==false)
	{
		window.onbeforeunload = function(e){};
		document.forms['formulario'].submit();
	}
}

function reset_file_elem(elem)
{
	try{
		elem.value = '';
		if(elem.value)
		{
			elem.type = "text";
			elem.type = "file";
		}
	}
	catch(e){}
}

function limpiar_files()
{
	if(document.getElementById('1393_ARTE_file'))
	{
		reset_file_elem(document.getElementById('1393_ARTE_file'));
		verificar_nombre_archivo(document.getElementById('1393_ARTE_file').value,'ARTE','nombre_archivo_1393');
	}
	
	document.getElementById('eapb').value="none";
	document.getElementById('periodo').value="none";
	document.getElementById('numero_de_remision').value="";
	document.getElementById('year_de_corte').value="";
	document.getElementById('fecha_remision').value="";
	
	validar_antes_seleccionar_archivos();
}


function validar_antes_seleccionar_archivos()
{
	var prestador = document.getElementById('prestador').value;
	var eapb = document.getElementById('eapb').value;
	var numero_de_remision = document.getElementById('numero_de_remision').value;
	//var fecha_remision = document.getElementById('fecha_remision').value;
	var year_de_corte = document.getElementById('year_de_corte').value;
	var periodo = document.getElementById('periodo').value;
	
	verificar_nombre_archivo(document.getElementById('1393_ARTE_file').value,'ARTE','nombre_archivo_1393');
	
	//var array_fecha_remision = fecha_remision.split("/");
	
	/*
	if(prestador!="none" && eapb!="none" && numero_de_remision!=""  && array_fecha_remision.length==3 && year_de_corte!="" && periodo!="none")
	{
		document.getElementById('tabla_seleccion_archivos').style.display="inline";
	}
	else
	{
		document.getElementById('tabla_seleccion_archivos').style.display="none";
	}
	*/
	
}

function cuando_se_escribe_el_nombre_del_archivo()
{
	verificar_nombre_archivo(document.getElementById('1393_ARTE_file').value,'ARTE','nombre_archivo_1393')
	
	var numero_de_remision = document.getElementById('numero_de_remision').value;
	
	
	if(numero_de_remision.length!=(2))
	{
		document.getElementById('error_nombre_archivo').innerHTML="El numero de remisi&oacuten debe contener "+(2)+" digitos.";
	}
	else
	{
		document.getElementById('error_nombre_archivo').innerHTML="";
	}
	
	
}

function download_inconsistencias_campos(ruta)
{
	
	window.open(ruta,'Download');
}

onbeforeunload = function(e){	
		return 'Recuerde que se perderan los cambios realizados.';	
}