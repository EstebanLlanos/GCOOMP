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
	var year = $('#year_de_validacion').val();
	
	var busqueda_grupo_procedimiento=document.getElementById('por_grupo_procedimiento');
	var busqueda_sistema=document.getElementById('por_grupo_sistema');
	
	if (busqueda_sistema.checked==false && busqueda_grupo_procedimiento.checked==false)
	{
		mensaje_error+="Seleccione un criterio de busqueda, sea por grupo de procedimiento o de sistema.<br></br>";
	}
	
	if(!intRegex.test(year))
	{
		mensaje_error+="Digite un A&ntildeo.<br></br>";
	}
		
	
	
	if($('#eapb').val()=="none")
	{
		mensaje_error+="Seleccione la entidad administradora EAPB.<br></br>";
	}
	
	
	if($('#periodo').val()=="none")
	{
		mensaje_error+="Seleccione el periodo para el cual se generara el reporte.<br></br>";
	}
	
	if($('#sexo').val()=="none")
	{
		mensaje_error+="Seleccione el sexo para el cual se generara el reporte.<br></br>";
	}
	
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

/*
function consultar_eapb(tipo_id,identificacion)
{
	var prestador_cod= document.getElementById("prestador").value;
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}
*/

function consultar_prestador()
{
	var eapb_cod= document.getElementById("eapb").value;
	
	ConsultaAJAX("eapb="+eapb_cod,"consulta_prestador.php","div_prestador");
	
}

function mostrar_meses()
{
	var year= $('#year_de_corte').val();
	if(year!="")
	{
		document.getElementById("mostrar_mes").style.display="inline";
	}
	else
	{
		document.getElementById("mostrar_mes").style.display="none";
	}
}

$(function() 
{
    $("#dpto").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el departamento<br>'});
    $("#mpio").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el municipio<br>'});
	$("#year_de_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione el a&ntildeo <br>'});
    $("#periodo").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el periodo <br>'});
    $("#prestador").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el prestador asociado a la EAPB o deje sin seleccionar para usar todos los prestadores asociados<br>'});
    $("#eapb").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione la EAPB al cual esta asociado<br>'});
   
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
	var busqueda_area_geografica=document.getElementById("busq_area_geografica");
	var busqueda_prestador=document.getElementById("busq_prestador");
	var busqueda_periodo=document.getElementById("busq_periodo");
	if(busqueda_area_geografica.checked==true)
	{
		document.getElementById("mostrar_area_geografica").style.display="inline";
		document.getElementById("mostrar_prestador").style.display="none";
		document.getElementById("mostrar_fecha_rem").style.display="none";
	}
	if(busqueda_prestador.checked==true)
	{
		document.getElementById("mostrar_prestador").style.display="inline";
		document.getElementById("mostrar_area_geografica").style.display="none";
		document.getElementById("mostrar_fecha_rem").style.display="none";
	}
	if(busqueda_periodo.checked==true)
	{
		document.getElementById("mostrar_fecha_rem").style.display="inline";
		document.getElementById("mostrar_area_geografica").style.display="none";
		document.getElementById("mostrar_prestador").style.display="none";
	}
}

function mostrar_grupo_procedimiento_o_sistema()
{
	var busqueda_grupo_procedimiento=document.getElementById('por_grupo_procedimiento');
	var busqueda_sistema=document.getElementById('por_grupo_sistema');
	
	if(busqueda_grupo_procedimiento.checked==true)
	{
		document.getElementById("tr_titulo_grupo_procedimiento").style.display="inline";
		document.getElementById("tr_selector_procedimiento").style.display="inline";
		document.getElementById("tr_titulo_grupo_sistema").style.display="none";
		document.getElementById("tr_selector_grupo_sistema").style.display="none";
	}
	if(busqueda_sistema.checked==true)
	{
		document.getElementById("tr_titulo_grupo_sistema").style.display="inline";
		document.getElementById("tr_selector_grupo_sistema").style.display="inline";
		document.getElementById("tr_titulo_grupo_procedimiento").style.display="none";
		document.getElementById("tr_selector_procedimiento").style.display="none";
	}
}

function consultar_mpio()
{
	var cod_dpto = document.getElementById("dpto").value;
	if(cod_dpto!="none")
	{
		document.getElementById("mostrar_mpio").style.display="inline";
		ConsultaAJAX("cod_dpto="+cod_dpto,"consulta_mpio.php","mpio_div");
	}
	else
	{
		document.getElementById("mostrar_mpio").style.display="none";
	}
	//colocar ayuda
	$("#mpio").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el municipio<br>'});
}

function download_archivo(ruta)
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