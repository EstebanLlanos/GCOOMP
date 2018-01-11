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



(function( $ ) {
    $.widget( "custom.combobox",
	     {
      _create: function() {
        this.wrapper = $( "<span>" )
          .addClass( "custom-combobox" )
          .insertAfter( this.element );
 
        this.element.hide();
        this._createAutocomplete();
        this._createShowAllButton();
      },
 
      _createAutocomplete: function()
      {
        var selected = this.element.children( ":selected" ),
          value = selected.val() ? selected.text() : "";
 
        this.input = $( "<input>" )
          .appendTo( this.wrapper )
          .val( value )
          .attr( "title", "" )
          //.addClass( "custom-combobox-input ui-widget ui-widget-content ui-state-default ui-corner-left" )
	  .addClass( "campo_azul custom-combobox-input  ui-corner-left" )
          .autocomplete({
            delay: 0,
            minLength: 0,
            source: $.proxy( this, "_source" )
          })
	  //parte seleccionar todo el texto del input
	  .on('mouseup', function() {
              $(this).select();
          })
	  //fin parte seleccionar todo el texto del input
          .tooltip({
            tooltipClass: "ui-state-highlight"
          });
 
        this._on( this.input, {
          autocompleteselect: function( event, ui ) {
            ui.item.option.selected = true;
            this._trigger( "select", event, {
              item: ui.item.option
            });
          },
 
          autocompletechange: "_removeIfInvalid"
        });
      },
 
      _createShowAllButton: function()
      {
        var input = this.input,
          wasOpen = false;
 
        $( "<a>" )
          .attr( "tabIndex", -1 )
          .attr( "title", "Show All Items" )
          .tooltip()
          .appendTo( this.wrapper )
          .button({
            icons: {
              primary: "ui-icon-triangle-1-s"
            },
            text: false
          })
          .removeClass( "ui-corner-all" )
          .addClass( "custom-combobox-toggle ui-corner-right altura_flecha arrow-up" )
          .mousedown(function() {
            wasOpen = input.autocomplete( "widget" ).is( ":visible" );
          })
          .click(function() {
            input.focus();
 
            // Close if already visible
            if ( wasOpen ) {
              return;
            }
 
            // Pass empty string as value to search for, displaying all results
            input.autocomplete( "search", "" );
          });
      },
 
      _source: function( request, response ) {
        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
        response( this.element.children( "option" ).map(function()
	{
          var text = $( this ).text();
          if ( this.value && ( !request.term || matcher.test(text) ) )
            return {
              label: text,
              value: text,
              option: this
            };
	    
        }) );
      },
 
      _removeIfInvalid: function( event, ui ) {
 
        // Selected an item, nothing to do
        if ( ui.item )
	{
		validar_antes_seleccionar_archivos();
          return;
        }
 
        // Search for a match (case-insensitive)
        var value = this.input.val(),
          valueLowerCase = value.toLowerCase(),
          valid = false;
        this.element.children( "option" ).each(function()
	{
          if ( $( this ).text().toLowerCase() === valueLowerCase ) {
            this.selected = valid = true;
            return false;
          }
	  
        });
 
        // Found a match, nothing to do
        if ( valid ) {
		
          return;
        }
 
        // Remove invalid value
        this.input
          .val( "" )
          .attr( "title", value + " didn't match any item" )
          .tooltip( "open" );
        this.element.val( "" );
        this._delay(function() {
          this.input.tooltip( "close" ).attr( "title", "" );
        }, 2500 );
        this.input.autocomplete( "instance" ).term = "";
      },
 
      _destroy: function() {
        this.wrapper.remove();
        this.element.show();
      }
    });
  })( jQuery );


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


function mostrar_selectores_geograficos()
{
	
	if (document.getElementById("tipo_archivo_norma").value=="agrupado_eapb")
	{
		
		
		document.getElementById("sub_titulo_entidad_1").style.display="none";
		document.getElementById("entidad_1").style.display="none";
		/*
		$("#prestador").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", true).prop("disabled",true);
		$("#prestador").parent().find("a.ui-button").button("disable");
		*/
		
		document.getElementById("sub_titulo_entidad_2").style.display="none";
		document.getElementById("sub_titulo_entidad_2_oculto").style.display="block";
		
		document.getElementById("sub_titulo_entidad_3_oculto").style.display="block";
		
		//document.getElementById("separador_regimen").style.display="block";
		//document.getElementById("titulo_regimen").style.display="block";
		
		document.getElementById("titulo_numero_de_remision").style.display="none";
		document.getElementById("separador_numero_de_remision").style.display="none";
	}
	else
	{
		document.getElementById("sub_titulo_entidad_3_oculto").style.display="none";
		document.getElementById("sub_titulo_entidad_4_oculto").style.display="none";
		
		document.getElementById("sub_titulo_entidad_1_oculto").style.display="none";
		document.getElementById("sub_titulo_entidad_2_oculto").style.display="none";
		
		document.getElementById("sub_titulo_entidad_1").style.display="block";
		document.getElementById("sub_titulo_entidad_2").style.display="block";
		document.getElementById("entidad_1").style.display="block";
		document.getElementById("entidad_2").style.display="block";
		
		$("#eapb").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", false).prop("disabled",false);
		$("#eapb").parent().find("a.ui-button").button("enable");
		$("#prestador").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", false).prop("disabled",false);
		$("#prestador").parent().find("a.ui-button").button("enable");
		
		//document.getElementById("separador_regimen").style.display="none";
		//document.getElementById("titulo_regimen").style.display="none";
		
		//document.getElementById("titulo_numero_de_remision").style.display="block";
		//document.getElementById("separador_numero_de_remision").style.display="block";
	    
	}
}


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

		//CONDICIONES ESPECIFICAS VERSIONES
		var get_entidad_personalizada = document.getElementById('get_entidad_personalizada').value;
		//verificacion solo gcoomp
		//nota: comentar otras versiones
		if(nombre_sin_extension[0].length!=30 && nombre_sin_extension[0].length!=28 && get_entidad_personalizada=="PrepagadaCoomeva" )
		{
			mensaje+="ERROR: El nombre de archivo no es valido para esta version. "+nombre_sin_extension[0].length+" <br>";
		}//fin if
		else if (nombre_sin_extension[0].length!=35 && nombre_sin_extension[0].length!=22 && get_entidad_personalizada!="PrepagadaCoomeva")
		{
			mensaje+="ERROR: El nombre de archivo no es valido para esta version. <br>";
		}//fin if
		//FIN CONDICIONES ESPECIFICAS VERSIONES
		
		var prestador = document.getElementById('prestador').value;
		
		
		while(prestador.length<12 && prestador!="none")
		{
			prestador="0"+prestador;
		}
		if(prestador!="none")
		{			
			var prestador_nombre=array_nombre_archivo[0].substring(8,20);
			if(nombre_sin_extension[0].length==30 || nombre_sin_extension[0].length==28)
			{
				prestador_nombre=array_nombre_archivo[0].substring(9,21);
			}//fin if

			//alert(prestador_nombre);
			
			if(prestador_nombre!=prestador  && document.getElementById("tipo_archivo_norma").value=="individual_ips")
			{
				
				mensaje+="ERROR: El prestador "+prestador+" no corresponde al prestador indicado en el archivo "+prestador_nombre+". <br>";
			}
			else if (document.getElementById("tipo_archivo_norma").value=="agrupado_eapb")
			{
				var eapb_ver_new_name =document.getElementById('eapb').value;
				while(eapb_ver_new_name.length<12 && eapb_ver_new_name!="none")
				{
					eapb_ver_new_name="0"+eapb_ver_new_name;
				}//fin while eapb caso agrupado nuevo nombre
			    if (prestador_nombre!="0000AGRUPADO" && nombre_sin_extension[0].length==35 && nombre_sin_extension[0].length!=30 && nombre_sin_extension[0].length!=28)
			    {
					mensaje+="ERROR: El archivo no es AGRUPADO <br>";
			    }//fin if
			    else if (prestador_nombre!=eapb_ver_new_name  && (nombre_sin_extension[0].length==30 || nombre_sin_extension[0].length==28) )
			    {
			    	mensaje+="ERROR: La EAPB "+eapb_ver_new_name+" no corresponde al la EAPB indicada en el archivo "+prestador_nombre+" para un archivo AGRUPADO <br>";
			    }//fin else if
			}//fin else if
			
			
		}//fin if
		
		//LONGITUD
		if (document.getElementById("tipo_archivo_norma").value=="individual_ips")
		{
		    if (nombre_sin_extension[0].length!=35 && nombre_sin_extension[0].length!=30 && nombre_sin_extension[0].length!=28)
		    {
			mensaje+="ERROR: La longitud del archivo de "+nombre_sin_extension[0].length+" caracteres no corresponde a 35, 30, 28 caracteres <br>";
		    }
		}
		else if (document.getElementById("tipo_archivo_norma").value=="agrupado_eapb")
		{
		    if (nombre_sin_extension[0].length!=35 && nombre_sin_extension[0].length!=22 && nombre_sin_extension[0].length!=30 && nombre_sin_extension[0].length!=28)
		    {
			mensaje+="ERROR: La longitud del archivo de "+nombre_sin_extension[0].length+" caracteres no corresponde a 35, 35, 30, 28, 22 caracteres para agrupado <br>";
		    }
		    //28 30
		}
		//FIN LONGITUD

		//VERIFICACION REGIMEN
		if(document.getElementById('selector_regimen_para_agrupados') && nombre_sin_extension[0].length!=30 && nombre_sin_extension[0].length!=28 )
		{
			if(document.getElementById('selector_regimen_para_agrupados').style.display!="none")
			{
				var regimen_seleccionado=document.getElementById('selector_regimen_para_agrupados').value;
				var regimen_nombre_archivo=array_nombre_archivo[0].substring(20,21);
				if(regimen_seleccionado!=regimen_nombre_archivo && regimen_seleccionado!="none")
				{
					mensaje+="ERROR: El regimen seleccionado "+regimen_seleccionado+" no corresponde al regimen indicado en el archivo "+regimen_nombre_archivo+". <br>";
				}//fin if
				else if(regimen_seleccionado=="none")
				{
					mensaje+="ERROR: Seleccione un regimen equivalente al regimen indicado en el archivo "+regimen_nombre_archivo+". <br>";
				}//fin if
			}//fin if
		}//fin if existe selector_regimen_para_agrupados
		//FIN VERIFICACION REGIMEN

		
		
		var eapb =document.getElementById('eapb').value;
		while(eapb.length<6 && eapb!="none")
		{
			eapb="0"+eapb;
		}
		if(eapb!="none" && nombre_sin_extension[0].length!=30 && nombre_sin_extension[0].length!=28)
		{			
			var eapb_nombre="";
			if (document.getElementById("tipo_archivo_norma").value=="individual_ips")
			{
			    eapb_nombre=array_nombre_archivo[0].substring(21,28);
			}
			else if (document.getElementById("tipo_archivo_norma").value=="agrupado_eapb")
			{
			    if (nombre_sin_extension[0].length==35)
			    {
				eapb_nombre=array_nombre_archivo[0].substring(21,28);
			    }
			    else if (nombre_sin_extension[0].length==22)
			    {
				$barra_al_piso=array_nombre_archivo[0].substring(8,9);
				if ($barra_al_piso=="_")
				{
				    eapb_nombre=array_nombre_archivo[0].substring(9,15);
				}
				else
				{
				    eapb_nombre=array_nombre_archivo[0].substring(8,14);
				}
			    }
			    
			}
			
			//alert(eapb);
			if(eapb_nombre!=eapb)
			{
				
				mensaje+="ERROR: La EAPB "+eapb+" no corresponde al la EAPB indicada en el archivo "+eapb_nombre+". <br>";
			}
			
			
		}
		
		var numero_de_remision_registrado=document.getElementById('numero_de_remision').value;
		if(document.getElementById('numero_de_remision').value=="")
		{
			document.getElementById('numero_de_remision').value=array_nombre_archivo[1];
		}//fin if
		if (document.getElementById("tipo_archivo_norma").value=="individual_ips")
		{
		    if(numero_de_remision_registrado!=array_nombre_archivo[1] && array_nombre_archivo.length==2 && numero_de_remision_registrado!="")
		    {
			    mensaje+="ERROR: El numero de remision "+numero_de_remision_registrado+" no corresponde al numero de remision "+array_nombre_archivo[1]+" registrado  en el archivo "+nombre_sin_extension[0]+" . <br>";
		    }
		}//fin if
		else if (document.getElementById("tipo_archivo_norma").value=="agrupado_eapb")
		{
		    $barra_al_piso=array_nombre_archivo[0].substring(8,9);
		    if ($barra_al_piso=="_")
		    {
			document.getElementById("titulo_numero_de_remision").style.display="none";
			document.getElementById("separador_numero_de_remision").style.display="none";
		    }
		    else
		    {
			document.getElementById("titulo_numero_de_remision").style.display="block";
			document.getElementById("separador_numero_de_remision").style.display="block";
			if(numero_de_remision_registrado!=array_nombre_archivo[1] && array_nombre_archivo.length==2 && numero_de_remision_registrado!="")
			{
				mensaje+="ERROR: El numero de remision "+numero_de_remision_registrado+" no corresponde al numero de remision "+array_nombre_archivo[1]+" registrado  en el archivo "+nombre_sin_extension[0]+" . <br>";
			}
		    }//fin else
		}//fin else if
		
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
	if(document.getElementById("numero_de_remision").value==""
	   && document.getElementById("separador_numero_de_remision").style.display!="none"
	   )
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
	
	//verificacion selecciono regimen si es agrupado
	if (document.getElementById("tipo_archivo_norma").value=="agrupado_eapb")
	{
	    if (document.getElementById("selector_regimen_para_agrupados").value=="none")
	    {
		mensaje+='<br>-Seleccione un Regimen para el archivo agrupado \n';
	    }
	}
	//fin verificacion selecciono regimen si es agrupado
	
	//verificacion de la carga del archivo CANCER
    if(document.getElementById("0247_cancer_file").value=="")
	{
		mensaje+='<br>-Seleccione un archivo CANCER a validar \n';
	}
	
	
	if(document.getElementById("CANCER_hidden").value=="error")
	{
		mensaje+='<br>-EL archivo seleccionado para CANCER no tiene un nombre valido \n';
	}
	
	//fin verificacion de la carga del archivo CANCER
	
	
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

function cargarCANCER()
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
	if(document.getElementById('0247_cancer_file'))
	{
		reset_file_elem(document.getElementById('0247_cancer_file'));
		verificar_nombre_archivo(document.getElementById('0247_cancer_file').value,'CANCER','nombre_archivo_0247');
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
	
	verificar_nombre_archivo(document.getElementById('0247_cancer_file').value,'CANCER','nombre_archivo_0247');
	
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
	verificar_nombre_archivo(document.getElementById('0247_cancer_file').value,'CANCER','nombre_archivo_0247')
	
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

$(document).ready(function(){
    $("#eapb").combobox();
    $("#prestador").combobox();
    //nota: descomentar para versiones general y prestador
    var get_entidad_personalizada = document.getElementById('get_entidad_personalizada').value;
    if(get_entidad_personalizada=="PrepagadaCoomeva" )
    {
	    document.getElementById("separador_regimen").style.display="none";
		document.getElementById("titulo_regimen").style.display="none";
		document.getElementById("selector_regimen_para_agrupados").value="E";
		document.getElementById("titulo_numero_de_remision").style.display="none";
		document.getElementById("separador_numero_de_remision").style.display="none";
	}//fin if es coomeva mp
})

