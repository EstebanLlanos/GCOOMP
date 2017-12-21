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
	
	
	
	if($('#selector_estado_info').val()=="none")
	{
		mensaje_error+="Seleccione el estado de la informaci&oacuten.<br></br>";
	}
	
	
	if($('#rango_resultados').val()=="none")
	{
		mensaje_error+="Seleccione el rango de los resultados a mostrar.<br></br>";
	}
	
	if($('#year_de_corte').val()!="")
	{
		if(!intRegex.test($('#year_de_corte').val()))
		{
			mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
		}
	}
	
	
	
	if(mensaje_error=="")
	{
		document.getElementById('index_inicio').value=0;
		document.getElementById('index_fin').value=document.getElementById('rango_resultados').value;
		document.formulario.submit();
	}
	else
	{
		$("h3#tituloVentana").html("Advertencia");
        $("div#mensajeVentana").html("<p>" + mensaje_error + "</p>");
        $('#myModal').modal('toggle');
	}
}

function enviar_atras()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	
	
	
	if($('#selector_estado_info').val()=="none")
	{
		mensaje_error+="Seleccione el estado de la informaci&oacuten.<br></br>";
	}
	
	
	if($('#rango_resultados').val()=="none")
	{
		mensaje_error+="Seleccione el rango de los resultados a mostrar.<br></br>";
	}
	
	if($('#year_de_corte').val()!="")
	{
		if(!intRegex.test($('#year_de_corte').val()))
		{
			mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
		}
	}
	
	
	
	if(mensaje_error=="")
	{
		var valor_anterior=parseInt(document.getElementById('index_inicio').value) - parseInt(document.getElementById('rango_resultados').value);
		if(valor_anterior<0)
		{
			document.getElementById('index_inicio').value=0;
		}
		else
		{
			document.getElementById('index_inicio').value=valor_anterior;
		}
		
		document.getElementById('index_fin').value=document.getElementById('rango_resultados').value;
		document.formulario.submit();
	}
	else
	{
		$("h3#tituloVentana").html("Advertencia");
        $("div#mensajeVentana").html("<p>" + mensaje_error + "</p>");
        $('#myModal').modal('toggle');
	}
}

function enviar_adelante()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	
	
	
	if($('#selector_estado_info').val()=="none")
	{
		mensaje_error+="Seleccione el estado de la informaci&oacuten.<br></br>";
	}
	
	
	if($('#rango_resultados').val()=="none")
	{
		mensaje_error+="Seleccione el rango de los resultados a mostrar.<br></br>";
	}
	
	if($('#year_de_corte').val()!="")
	{
		if(!intRegex.test($('#year_de_corte').val()))
		{
			mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
		}
	}
	
	
	
	if(mensaje_error=="")
	{
		var valor_siguiente=parseInt(document.getElementById('index_inicio').value) + parseInt(document.getElementById('rango_resultados').value);
		
		document.getElementById('index_inicio').value=valor_siguiente;
		
		document.getElementById('index_fin').value=document.getElementById('rango_resultados').value;
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


function consultar_eapb(tipo_id,identificacion)
{
	var prestador_cod= document.getElementById("prestador").value;
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}

function consultar_mpio()
{
	var cod_dpto = document.getElementById("dpto").value;
	if(cod_dpto!="none")
	{
		ConsultaAJAX("cod_dpto="+cod_dpto,"consulta_mpio.php","mpio_div");
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