$(function() {
    $("#fecha_cumple").datepicker();
    $("#fecha_cumple").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_cumple").datepicker($.datepicker.regional[ "es" ]);
});

$(function() {
    $("#fecha_vence").datepicker();
    $("#fecha_vence").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_vence").datepicker($.datepicker.regional[ "es" ]);
});

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

function enviar()
{
	document.getElementById("tipo_identificacion").disabled=false;
	var mensaje_error="";
	
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	if($('#primer_nombre').val()=="")
	{
		mensaje_error+="Escriba el primer nombre del usuario<br></br>";
	}
	
	//primer_apellido
	
	if($('#primer_apellido').val()=="")
	{
		mensaje_error+="Escriba el primer apellido del usuario<br></br>";
	}
	
	/*
	//nick_usuario
	
	if($('#nick_usuario').val()=="")
	{
		mensaje_error+="Escriba el nick que usara el usuario para el logueo<br></br>";
	}
	*/
	
	//password_user
	
	if($('#password_user').val()=="")
	{
		mensaje_error+="Escriba el password que usara el usuario para el logueo<br></br>";
	}
	
	//confirmar_password
	if($('#confirmar_password').val()=="")
	{
		mensaje_error+="confirme el password que usara el usuario para el logueo<br></br>";
	}
	
	if($('#confirmar_password').val()!="" && $('#confirmar_password').val()!=$('#password_user').val())
	{
		mensaje_error+="El password es diferente del password confirmado<br></br>";
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
	
	//direccion
	if($('#direccion').val()=="")
	{
		mensaje_error+="escriba la direccion del usuario<br></br>";
	}
	
	//telefono
	if($('#telefono').val()!="")
	{
		if(!intRegex.test($('#telefono').val()))
		{
			mensaje_error+="El telefono no es un numero entero.<br></br>";
		}
		
	}
	
	//celular
	if($('#celular').val()!="")
	{
		if(!intRegex.test($('#celular').val()))
		{
			mensaje_error+="El celular no es un numero entero.<br></br>";
		}
		
	}
	
	//fecha_cumple
	if($('#fecha_cumple').val()!="")
	{
		var fecha_cumple_array=$('#fecha_cumple').val().split("/");
		if(fecha_cumple_array.length==3)
		{
			if(!intRegex.test(fecha_cumple_array[0]) || !intRegex.test(fecha_cumple_array[1]) || !intRegex.test(fecha_cumple_array[2]))
			{
				mensaje_error+="la fecha de nacimiento no es valida.<br></br>";
			}
		}
		else
		{
			mensaje_error+="la fecha de nacimiento  no es valida.<br></br>";
		}
		
	}
	else
	{
		mensaje_error+="la fecha de nacimiento  no es valida.<br></br>";
	}
	
	//fecha_vence
	if($('#fecha_vence').val()!="")
	{
		var fecha_vence_array=$('#fecha_vence').val().split("/");
		if(fecha_vence_array.length==3)
		{
			if(!intRegex.test(fecha_vence_array[0]) || !intRegex.test(fecha_vence_array[1]) || !intRegex.test(fecha_vence_array[2]))
			{
				mensaje_error+="la fecha de vencimiento no es valida.<br></br>";
			}
		}
		else
		{
			mensaje_error+="la fecha de vencimiento  no es valida.<br></br>";
		}
		
	}
	else
	{
		mensaje_error+="la fecha de vencimiento  no es valida.<br></br>";
	}
	
	//identificacion
	if($('#identificacion').val()=="")
	{
		mensaje_error+="La identificaci&oacuten esta vacia.<br></br>";
	}
	
	if($('#identificacion').val()!="")
	{
		if(!intRegex.test($('#identificacion').val()))
		{
			mensaje_error+="La identificaci&oacuten no es un numero entero.<br></br>";
		}
		
	}
	
	
	
	if($('#tipo_identificacion').val()=="none")
	{
		mensaje_error+="Seleccione un tipo de identificaci&oacuten<br></br>";
	}
	
	/*
	if($('#perfil').val()=="none")
	{
		mensaje_error+="Seleccione un perfil para el usuario<br></br>";
	}
	*/
	
	cont_entidades=0;
	while(document.getElementById('cod_entidad_salud_'+cont_entidades)!=null)
	{
		if(document.getElementById('cod_entidad_salud_'+cont_entidades).value=="")
		{
			mensaje_error+="Seleccione o escriba el codigo de una entidad de saluda asociada para el nick "+cont_entidades+" del usuario. <br></br>";
		}
		cont_entidades++;
	}
	
	cont_perfiles=0;
	while(document.getElementById('perfil_'+cont_perfiles)!=null)
	{
		if(document.getElementById('perfil_'+cont_perfiles).value=="none")
		{
			mensaje_error+="Seleccione el perfil de acceso para el nick "+cont_perfiles+" del usuario. <br></br>";
		}
		cont_perfiles++;
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

function buscar_seleccionar_entidad(campo_actual_entidad)
{
	var html_interno="";
	html_interno+="";
	
	html_interno+="<select id='rango_resultados_"+campo_actual_entidad.id+"' name='rango_resultados_"+campo_actual_entidad.id+"' class='campo_azul' >";
	html_interno+="<option value='none'>Seleccione el rango de resultados</option>";
	html_interno+="<option value='10'>10</option>";
	html_interno+="<option value='20'>20</option>";
	html_interno+="<option value='30'>30</option>";
	html_interno+="<option value='40'>40</option>";
	html_interno+="<option value='50'>50</option>";
	html_interno+="</select>";
	html_interno+=" <input type='text' id='nombre_entidad_"+campo_actual_entidad.id+"' nombre='nombre_entidad_"+campo_actual_entidad.id+"' placeholder='Nombre de la entidad' class='campo_azul'/>";
	html_interno+="<br></br> <input type='button' id='buscar_"+campo_actual_entidad.id+"' class=\"btn btn-success color_boton\" value='Buscar' onclick=\"consultar_entidades_ajax('"+campo_actual_entidad.id+"');\" />";
	html_interno+="<div id='div_"+campo_actual_entidad.id+"'></div>"
	$("h3#tituloVentana").html("Buscar Entidad");
    $("div#mensajeVentana").html(html_interno);
    $('#myModal').modal('toggle');
}

var inicio_index=0;
var fin_index=0;
function consultar_entidades_ajax(campo_div)
{
	var rango=document.getElementById('rango_resultados_'+campo_div).value;
	var nombre_entidad_filtro=document.getElementById('nombre_entidad_'+campo_div).value;
	if(rango!="none")
	{
		inicio_index=0;
		fin_index=parseInt(rango);
		ConsultaAJAX("rango_resultados="+rango+"&inicio="+inicio_index+"&fin="+fin_index+"&campo_div="+campo_div+"&filtro_nombre="+nombre_entidad_filtro,"buscar_entidad_salud.php","div_"+campo_div);
	}
}
function consultar_entidades_atras_ajax(campo_div)
{
	var rango=document.getElementById('rango_resultados_'+campo_div).value;
	var nombre_entidad_filtro=document.getElementById('nombre_entidad_'+campo_div).value;
	if(rango!="none")
	{
		var valor_anterior=parseInt(inicio_index) - parseInt(rango);
		if(valor_anterior<0)
		{
			inicio_index=0;
		}
		else
		{
			inicio_index=valor_anterior;
		}
		
		fin_index=parseInt(rango);
		ConsultaAJAX("rango_resultados="+rango+"&inicio="+inicio_index+"&fin="+fin_index+"&campo_div="+campo_div+"&filtro_nombre="+nombre_entidad_filtro,"buscar_entidad_salud.php","div_"+campo_div);
	}
}
function consultar_entidades_adelante_ajax(campo_div)
{
	var rango=document.getElementById('rango_resultados_'+campo_div).value;
	var nombre_entidad_filtro=document.getElementById('nombre_entidad_'+campo_div).value;
	if(rango!="none")
	{
		var valor_siguiente=parseInt(inicio_index) + parseInt(rango);
		
		inicio_index=valor_siguiente;
		
		fin_index=parseInt(rango);
	
		ConsultaAJAX("rango_resultados="+rango+"&inicio="+inicio_index+"&fin="+fin_index+"&campo_div="+campo_div+"&filtro_nombre="+nombre_entidad_filtro,"buscar_entidad_salud.php","div_"+campo_div);
	}
}

function seleccionar(codigo_entidad,campo)
{
	//alert(campo+" "+codigo_entidad);
	document.getElementById(campo).value=codigo_entidad;
}

function adicionar_entidad(valor_incremental)
{
	var nuevo_valor=valor_incremental+1;
	
	var html="";
	html+="<tr>";
	html+="<td style=\"text-align:left;\">";
	html+="<input type='text' id='cod_entidad_salud_"+nuevo_valor+"' name='cod_entidad_salud_"+nuevo_valor+"' autocomplete=\"off\" class='campo_azul' placeholder='Ingrese el codigo de la entidad de salud a asociar' onclick='buscar_seleccionar_entidad(this);'/> ";
	html+=" <select id='perfil_"+nuevo_valor+"' name='perfil_"+nuevo_valor+"' class='campo_azul'>";
	html+=document.getElementById('perfil_0').innerHTML;
	html+="</select>";
	html+=" <select id='estado_usuario_"+nuevo_valor+"' name='estado_usuario_"+nuevo_valor+"' class='campo_azul'>";
	html+=document.getElementById('estado_usuario_0').innerHTML;
	html+="</select>";
	html+="<input type='hidden' id='nick_usuario_"+nuevo_valor+"' name='nick_usuario_"+nuevo_valor+"' />";
	html+=" <input type='button' id='boton_inc_"+nuevo_valor+"' class=\"btn btn-success color_boton\" value='+' onclick='adicionar_entidad("+nuevo_valor+");' />";
	html+=" <input type='button' id='boton_limp_"+nuevo_valor+"' class=\"btn btn-success color_boton\" value='-' onclick='limpiar_entidad("+nuevo_valor+");' />";
	html+="</td>";
	html+="</tr>";
	html+="<div id='ext_cod_ent_"+nuevo_valor+"'>";
	html+="<tr>";
	html+="<td style=\"text-align:left;\">";
	html+="</td>";
	html+="</tr>";
	html+="</div>";
	
	if(document.getElementById('ext_cod_ent_'+nuevo_valor)==null)
	{
		document.getElementById('ext_cod_ent_'+valor_incremental).innerHTML=html;
	}
}
function limpiar_entidad(valor_decremental)
{
	var html="";
	document.getElementById('ext_cod_ent_'+valor_decremental).innerHTML=html;
}