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
	var busq_num_rem=document.getElementById("busq_nombre_archivo");
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
		var array_nombre_4505=$('#nombre_archivo').val().split(".");
		
		if(array_nombre_4505.length==2 )
		{
			if(array_nombre_4505[1]=="txt")
			{
				document.getElementById('nombre_archivo').value=array_nombre_4505[0];
			}
		}
		
		if($('#nombre_archivo').val()=="")
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
			mensaje_error+="Digite el nombre del archivo para buscar numeros de secuencia asociados a este cunado fue validado. <br></br>";
		}
	}
	if(busq_fecha_rem.checked==true)
	{
		if(!intRegex.test(year))
		{
			mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
		}
		if($('#fechas_corte').val()=="none" && year=="")
		{
			mensaje_error+="Seleccione un periodo. <br></br>";
		}
		if(document.getElementById("nombres_archivos_4505_varios"))
		{
			if(document.getElementById("nombres_archivos_4505_varios").value=="none")
			{
				mensaje_error+="Seleccione un archivo. <br></br>";
			}
		}
		else
		{
			mensaje_error+="Seleccione un a&ntildeo y/o un periodo para buscar numeros de remision relacionados a esa fecha. <br></br>";
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
		$("h3#tituloVentana").html("Advertencia");
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

function download_inconsistencias_campos(ruta)
{
	
	window.open(ruta,'Download');
}

function consultar_eapb(tipo_id,identificacion)
{
	var prestador_cod= document.getElementById("prestador").value;
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}

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
	var busq_num_rem=document.getElementById("busq_nombre_archivo");
	var busq_fecha_rem=document.getElementById("busq_fecha_remision");
	if(busq_num_seq.checked==true)
	{
		document.getElementById("mostrar_num_seq").style.display="inline";
		document.getElementById("mostrar_nombre_arch").style.display="none";
		document.getElementById("mostrar_fecha_rem").style.display="none";
	}
	if(busq_num_rem.checked==true)
	{
		document.getElementById("mostrar_nombre_arch").style.display="inline";
		document.getElementById("mostrar_num_seq").style.display="none";
		document.getElementById("mostrar_fecha_rem").style.display="none";
	}
	if(busq_fecha_rem.checked==true)
	{
		document.getElementById("mostrar_fecha_rem").style.display="inline";
		document.getElementById("mostrar_num_seq").style.display="none";
		document.getElementById("mostrar_nombre_arch").style.display="none";
	}
}

function consultar_nombres_remision()
{
	var year= $('#year_de_corte').val();
	var periodo =$('#fechas_corte').val();
	var prestador= $('#prestador').val();
	if(year!="")
	{
		ConsultaAJAX("year="+year+"&periodo="+periodo+"&prestador="+prestador,"consulta_nombres_archivos_4505.php","div_selector_nombres_archivos");
	}
}

function consultar_numeros_secuencia_para_el_nombre()
{
	var nombre_archivo =$('#nombre_archivo').val();
	var prestador= $('#prestador').val();
	if(nombre_archivo!="")
	{
		ConsultaAJAX("nombre_archivo="+nombre_archivo+"&prestador="+prestador,"consulta_numeros_secuencia_archivo.php","div_selector_numeros_secuencias");
	}
}

$(function() 
{
    $("#numero_secuencia").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, digite un numero de secuencia<br>'});
    $("#fechas_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la fecha de corte correspondiente al reporte<br>'});
	$("#year_de_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione el a&ntildeo para la fecha de corte correspondiente al reporte<br>'});
    $("#periodo").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el periodo correspondiente al reporte de PyP<br>'});
    $("#prestador").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el prestador que reportara 4505 PyP<br>'});
    $("#eapb").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione la eps a reportar 4505 PyP<br>'});
   
});

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