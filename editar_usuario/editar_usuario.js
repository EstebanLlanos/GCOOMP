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

function validateEmail(email) 
{ 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 

$(function() {
    $("#fecha_vence").datepicker();
    $("#fecha_vence").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_vence").datepicker($.datepicker.regional[ "es" ]);
});

function actualizar()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	
	document.getElementById('tipo_accion_post').value="actualizar";
	
	//nick_logueo
	
	if($('#nick_logueo').val()=="")
	{
		mensaje_error+="Escriba el nick que usara el usuario para el logueo<br></br>";
	}
	
	var logueo_nick= document.getElementById("nick_logueo").value;
	
	if(logueo_nick.split("_").length==1)
	{
		document.getElementById("nick_logueo").value=logueo_nick+"_"+1
	}
	
	//password_user
	
	if($('#password_user').val()=="")
	{
		mensaje_error+="Escriba el password que usara el usuario para el logueo<br></br>";
	}
	
	//email
	if($('#email').val()=="")
	{
		mensaje_error+="escriba el e-mail del usuario<br></br>";
	}
	
	if($('#email').val()!="" && !validateEmail($('#email').val()))
	{
		mensaje_error+="escriba un e-mail valido<br></br>";
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

function enviar()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	
	document.getElementById('tipo_accion_post').value="consultar";
	
	if($('#tipo_identificacion').val()=="none")
	{
		mensaje_error+="Seleccione un tipo de identificaci&oacuten<br></br>";
	}
	
	if($('#rango_resultados').val()=="none")
	{
		mensaje_error+="Seleccione el numero de resultados a ver por pagina de la consulta<br></br>";
	}
	
	if($('#identificacion').val()!="")
	{
		if(!intRegex.test($('#identificacion').val()))
		{
			mensaje_error+="La identificaci&oacuten no es un numero entero.<br></br>";
		}
		
	}
	else
	{
		mensaje_error+="La identificaci&oacuten no debe estar vacia.<br></br>";
	}
	
	if($('#cod_entidad_salud_0').val()=="")
	{
		mensaje_error+="El codigo de la entidad de salud no debe estar vacio.<br></br>";
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
}//fin enviar normal

/*
function enviar_atras()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	
	if($('#tipo_identificacion').val()=="none")
	{
		mensaje_error+="Seleccione un tipo de identificaci&oacuten<br></br>";
	}
	
	if($('#rango_resultados').val()=="none")
	{
		mensaje_error+="Seleccione el numero de resultados a ver por pagina de la consulta<br></br>";
	}
	
	if($('#identificacion').val()!="")
	{
		if(!intRegex.test($('#identificacion').val()))
		{
			mensaje_error+="La identificaci&oacuten no es un numero entero.<br></br>";
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
}//fin enviar atras

function enviar_adelante()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	
	if($('#tipo_identificacion').val()=="none")
	{
		mensaje_error+="Seleccione un tipo de identificaci&oacuten<br></br>";
	}
	
	if($('#rango_resultados').val()=="none")
	{
		mensaje_error+="Seleccione el numero de resultados a ver por pagina de la consulta<br></br>";
	}
	
	if($('#identificacion').val()!="")
	{
		if(!intRegex.test($('#identificacion').val()))
		{
			mensaje_error+="La identificaci&oacuten no es un numero entero.<br></br>";
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
}//fin enviar adelante
*/

function mostrar_opciones_edicion(tr_usuario_tipoid_id)
{
	if(document.getElementById(tr_usuario_tipoid_id).style.display=="none")
	{
	document.getElementById(tr_usuario_tipoid_id).style.display="initial";
	}
	else
	{
	document.getElementById(tr_usuario_tipoid_id).style.display="none";
	}
}