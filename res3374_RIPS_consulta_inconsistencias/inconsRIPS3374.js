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


function enviar()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	var year = $('#year_de_corte').val();
	
	var busq_num_seq=document.getElementById("busq_numero_secuencia");
	var busq_num_rem=document.getElementById("busq_numero_remision");
	var busq_fecha_rem=document.getElementById("busq_fecha_remision");
	if(busq_num_seq.checked==true)
	{
		if(!intRegex.test($('#numero_secuencia').val())) 
		{
			mensaje_error+="Digite un numero de secuencia que corresponda a un numero entero.<br></br>";
		}
		if($('#numero_secuencia').val()=="")
		{
			mensaje_error+="Digite un numero de secuencia que corresponda a un numero entero.<br></br>";
		}
	}
	if(busq_num_rem.checked==true)
	{
		if(!intRegex.test($('#numero_remision').val()) || $('#numero_remision').val().length!=6) 
		{
			mensaje_error+="Digite un numero de remision valido (entero de 6 caracteres).<br></br>";
		}
		if($('#numero_remision').val()=="")
		{
			mensaje_error+="El espacio esta en blanco escriba el numero de remision.<br></br>";
		}
		
		if(document.getElementById("numeros_secuencias_varios"))
		{
			if(document.getElementById("numeros_secuencias_varios").value=="none")
			{
				mensaje_error+="Seleccione un numero de secuencia. <br></br>";
			}
		}
		else
		{
			mensaje_error+="No existen numeros de secuencia asociados al numero de remision digitado, por favor verifique el numero de remision a consultar y continuar con el proceso de consulta. <br></br>";
		}
	}
	if(busq_fecha_rem.checked==true)
	{
		if(!intRegex.test(year))
		{
			mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
		}
		if($('#periodo').val()=="none" && year=="")
		{
			mensaje_error+="Seleccione un periodo. <br></br>";
		}
		if(document.getElementById("numeros_remision_varios"))
		{
			if(document.getElementById("numeros_remision_varios").value=="none")
			{
				mensaje_error+="Seleccione un numero de remision. <br></br>";
			}
		}
		else
		{
			mensaje_error+="No existen datos asociados al a&ntildeo y mes seleccionado, por favor verifique los datos de la consulta y continue con el proceso de consulta. <br></br>";
		}
	}
	
	if($('#eapb').val()=="none")
	{
		mensaje_error+="Seleccione la entidad administradora EAPB.<br></br>";
	}
	
	
	if($('#prestador').val()=="none")
	{
		mensaje_error+="Seleccione el prestador asociado al usuario.<br></br>";
	}
	
	if($('#numero_secuencia').val()=="") 
	{
		
			
		
	
	}//solo si no se ha digitado el numero de secuencia se pasa a buscar el numero de secuencia en la base de datos por los datos proporcionados
	
	
	if(mensaje_error=="")
	{
		document.formulario.submit();
	}
	else
	{
		$("h3#tituloVentana").html("ERROR");
		$("div#mensajeVentana").html("<p>" + mensaje_error + "</p>");
		$('#myModal').modal('toggle');
	}
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
		//alert(xmlhttp.responseText);
		return xmlhttp.responseText;
	}

}//fin funcion consulta ajax


function consultar_numeros_remision()
{
	var year= $('#year_de_corte').val();
	var periodo =$('#periodo').val();
	var prestador= $('#prestador').val();
	if(year!="")
	{
		ConsultaAJAX("year="+year+"&periodo="+periodo+"&prestador="+prestador,"consulta_numeros_remision.php","div_selector_numeros_de_remision");
	}
}

function consultar_numeros_secuencia_para_el_nombre()
{
	var numero_remision =$('#numero_remision').val();
	var prestador= $('#prestador').val();
	if(numero_remision!="")
	{
		ConsultaAJAX("numero_remision="+numero_remision+"&prestador="+prestador,"consulta_numeros_secuencia_archivo.php","div_selector_numeros_secuencias");
	}
	else
	{
		document.getElementById("div_selector_numeros_secuencias").innerHTML="";
	}
}

function consultar_eapb(tipo_id,identificacion)
{
	var prestador_cod= document.getElementById("prestador").value;
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}


$(function() 
{
    $("#numero_secuencia").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, digite un numero de secuencia<br>'});
    $("#fechas_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la fecha de corte correspondiente al reporte<br>'});
	$("#year_de_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione el a&ntildeo para la fecha de corte correspondiente al reporte<br>'});
    $("#periodo").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el periodo correspondiente a la fecha de remision de los archivos RIPS 3374<br>'});
    $("#prestador").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el prestador al que reporto los archivos RIPS 3374<br>'});
    $("#eapb").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione la eps al que reporto los archivos RIPS 3374<br>'});
   
});

function isNumberKey(evt) 
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function mostrar_metodos_busqueda()
{
	var busq_num_seq=document.getElementById("busq_numero_secuencia");
	var busq_num_rem=document.getElementById("busq_numero_remision");
	var busq_fecha_rem=document.getElementById("busq_fecha_remision");
	if(busq_num_seq.checked==true)
	{
		document.getElementById("mostrar_num_seq").style.display="inline";
		document.getElementById("mostrar_num_rem").style.display="none";
		document.getElementById("mostrar_fecha_rem").style.display="none";
	}
	if(busq_num_rem.checked==true)
	{
		document.getElementById("mostrar_num_rem").style.display="inline";
		document.getElementById("mostrar_num_seq").style.display="none";
		document.getElementById("mostrar_fecha_rem").style.display="none";
	}
	if(busq_fecha_rem.checked==true)
	{
		document.getElementById("mostrar_fecha_rem").style.display="inline";
		document.getElementById("mostrar_num_seq").style.display="none";
		document.getElementById("mostrar_num_rem").style.display="none";
	}
}

function download_inconsistencias_campos(ruta)
{
	
	window.open(ruta,'Download');
}

/*
function enviar_formulario_reportes_inconsistencias(val_seq)
{
		var anteriorTarget=document.formulario.target;
		var anteriorAction=document.formulario.action;
		document.getElementById('consecutivo').value=val_seq;
		document.formulario.target='blank_'; 
		document.formulario.action='../res4505_carga_validacion/ReporteIncidenciasPyP_funcional.php';
		document.formulario.submit();
		document.formulario.target=anteriorTarget;
		document.formulario.action=anteriorAction;
}
*/