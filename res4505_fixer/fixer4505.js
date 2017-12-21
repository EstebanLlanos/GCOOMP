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
	  .addClass( "campo_azul custom-combobox-input ui-corner-left " )
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

function seleccionar_periodo()
{
	var fecha_corte = document.getElementById("fechas_corte").value;
	if(fecha_corte=="1-31")
	{
		document.getElementById("periodo").value="1";
	}
	if(fecha_corte=="2-28")
	{
		document.getElementById("periodo").value="2";
	}
	if(fecha_corte=="3-31")
	{
		document.getElementById("periodo").value="3";
	}
	if(fecha_corte=="4-30")
	{
		document.getElementById("periodo").value="4";
	}
	if(fecha_corte=="5-31")
	{
		document.getElementById("periodo").value="5";
	}
	if(fecha_corte=="6-30")
	{
		document.getElementById("periodo").value="6";
	}
	if(fecha_corte=="7-31")
	{
		document.getElementById("periodo").value="7";
	}
	if(fecha_corte=="8-31")
	{
		document.getElementById("periodo").value="8";
	}
	if(fecha_corte=="9-30")
	{
		document.getElementById("periodo").value="9";
	}
	if(fecha_corte=="10-31")
	{
		document.getElementById("periodo").value="10";
	}
	if(fecha_corte=="11-30")
	{
		document.getElementById("periodo").value="11";
	}
	if(fecha_corte=="12-31")
	{
		document.getElementById("periodo").value="12";
	}
}

function seleccionar_fecha_de_corte()
{
	var periodo = document.getElementById("periodo").value;
	
	if(periodo==1)
	{
		document.getElementById("fechas_corte").value="1-31";
	}
	if(periodo==2)
	{
		document.getElementById("fechas_corte").value="2-28";
	}
	if(periodo==3)
	{
		document.getElementById("fechas_corte").value="3-31";
	}
	if(periodo==4)
	{
		document.getElementById("fechas_corte").value="4-30";
	}
	if(periodo==5)
	{
		document.getElementById("fechas_corte").value="5-31";
	}
	if(periodo==6)
	{
		document.getElementById("fechas_corte").value="6-30";
	}
	if(periodo==7)
	{
		document.getElementById("fechas_corte").value="7-31";
	}
	if(periodo==8)
	{
		document.getElementById("fechas_corte").value="8-31";
	}
	if(periodo==9)
	{
		document.getElementById("fechas_corte").value="9-30";
	}
	if(periodo==10)
	{
		document.getElementById("fechas_corte").value="10-31";
	}
	if(periodo==11)
	{
		document.getElementById("fechas_corte").value="11-30";
	}
	if(periodo==12)
	{
		document.getElementById("fechas_corte").value="12-31";
	}
}


$(function() 
{
    $("#prestador").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la sede de la entidad prestadora a la que le corresponde el cargue<br>'});
    $("#eapb").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la entidad administradora asociada al cargue<br>'});
    $("#numero_remision").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Este campo corresponde al numero de remision correspondiente al cargue<br>'});
	$("#fecha_remision").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la fecha de remisi&oacuten correspondiente al cargue<br>'});
    $("#fechas_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la fecha de corte correspondiente al cargue<br>'});
	$("#year_de_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione el a&ntildeo para la fecha de corte correspondiente al cargue<br>'});
    $("#periodo").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el periodo correspondiente al cargue de PyP<br>'});
    $("#detalle4505").popover({placement: 'bottom', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'El tama&ntildeo del archivo no debe superar los 1024 Mega Bytes<br>'});

});


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
	var prestador_completo= document.getElementById('prestador').value;
	var array_prestador=prestador_completo.split(";;");
	var prestador_cod= array_prestador[0];
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}


function mirar_fecha_entre(fecha_revisar,fecha_ini,fecha_fin)
{
	//alert(fecha_ini+" "+fecha_fin);
	var d1 = fecha_ini.split("/");
	var d2 = fecha_fin.split("/");
	var c = fecha_revisar.split("/");

	var ini = new Date();  // -1 because months are from 0 to 11
	var fin   = new Date();
	var check = new Date();
	
	ini.setFullYear(d1[2], d1[0]-1, d1[1]);
	fin.setFullYear(d2[2], d2[0]-1, d2[1]);
	check.setFullYear(c[2], c[0]-1, c[1]);

	
	if (check >= ini && check <= fin)
	{
		//alert("aceptable");
		return true;
	}
	else
	{		
		//alert("no "+ini+" "+fin+" "+check);
		return false;
	}
}//fin funcion

var fecha_dentro_de_periodo=false; 
function seleccionar_periodo_automaticamente(modo)
{
	var fecha_a_revisar=$("#fecha_remision").val();
	//alert(fecha_a_revisar);
	if(isDate(fecha_a_revisar))
	{
		var fecha_dividida = fecha_a_revisar.split("/");
		
		var year=fecha_dividida[2];
		//alert(year);
		
		/*
		var fecha_ini_p1="03/31/"+year;
		var fecha_ini_p2="06/30/"+year;
		var fecha_ini_p3="09/30/"+year;
		var fecha_ini_p4_1="12/31/"+year;
		var fecha_ini_p4_2="01/01/"+year;
		
		var fecha_fin_p1="04/25/"+year;
		var fecha_fin_p2="07/25/"+year;
		var fecha_fin_p3="10/25/"+year;
		var fecha_fin_p4_1="12/31/"+year;
		var fecha_fin_p4_2="01/25/"+year;
		*/
		
		//es el rango que puede enviar empieza desde el ultimo
		var fecha_ini_p1="01/31/"+year;
		var fecha_ini_p2="02/28/"+year;
		var fecha_ini_p3="03/31/"+year;
		var fecha_ini_p4="04/30/"+year;
		var fecha_ini_p5="05/31/"+year;
		var fecha_ini_p6="06/30/"+year;
		var fecha_ini_p7="07/31/"+year;
		var fecha_ini_p8="08/31/"+year;
		var fecha_ini_p9="09/30/"+year;
		var fecha_ini_p10="10/31/"+year;
		var fecha_ini_p11="11/30/"+year;
		var fecha_ini_p12_1="12/31/"+year;
		var fecha_ini_p12_2="01/01/"+(parseInt(year)+1);
		
		/*
		var fecha_fin_p1="04/25/"+year;
		var fecha_fin_p2="07/25/"+year;
		var fecha_fin_p3="10/25/"+year;
		var fecha_fin_p4_1="12/31/"+year;
		var fecha_fin_p4_2="01/25/"+year;
		*/
		
		var fecha_fin_p1="02/25/"+year;
		var fecha_fin_p2="03/25/"+year;
		var fecha_fin_p3="04/25/"+year;
		var fecha_fin_p4="05/25/"+year;
		var fecha_fin_p5="06/25/"+year;
		var fecha_fin_p6="07/25/"+year;
		var fecha_fin_p7="08/25/"+year;
		var fecha_fin_p8="09/25/"+year;
		var fecha_fin_p9="10/25/"+year;
		var fecha_fin_p10="11/25/"+year;
		var fecha_fin_p11="12/25/"+year;
		var fecha_fin_p12="01/25/"+(parseInt(year)+1);
		
		var encontro_periodo=false;
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p1,fecha_fin_p1))
		{
			$('#periodo').val('1');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p2,fecha_fin_p2))
		{
			$('#periodo').val('2');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p3,fecha_fin_p3))
		{
			$('#periodo').val('3');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p4,fecha_fin_p4))
		{
			$('#periodo').val('4');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p5,fecha_fin_p5))
		{
			$('#periodo').val('5');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p6,fecha_fin_p6))
		{
			$('#periodo').val('6');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p7,fecha_fin_p7))
		{
			$('#periodo').val('7');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p8,fecha_fin_p8))
		{
			$('#periodo').val('8');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p9,fecha_fin_p9))
		{
			$('#periodo').val('9');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p10,fecha_fin_p10))
		{
			$('#periodo').val('10');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p11,fecha_fin_p11))
		{
			$('#periodo').val('11');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p12_1,fecha_fin_p12))
		{
			$('#periodo').val('12');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p12_2,fecha_fin_p12))
		{
			$('#periodo').val('12');
			encontro_periodo=true;
		}
		
		if(encontro_periodo==false)
		{
			fecha_dentro_de_periodo=false;
			if(modo!="verificar")
			{
				alert("La fecha no corresponde a ningun periodo");
			}
		}
		else
		{
			fecha_dentro_de_periodo=true;			
		}
	}
}

function isDate(txtDate)
{
    var reg = /^(0[1-9]|1[012])([\/-])(0[1-9]|[12][0-9]|3[01])\2(\d{4})$/;
    return reg.test(txtDate);
}

function funciones_fecha_restriciones()
{
seleccionar_periodo_automaticamente();
seleccionar_fecha_de_corte();
poner_year();
}

function poner_year()
{
	var fecha_a_revisar=$("#fecha_remision").val();
	//alert(fecha_a_revisar);
	if(isDate(fecha_a_revisar))
	{
		var fecha_dividida = fecha_a_revisar.split("/");
		
		var year=fecha_dividida[2];
		
		document.getElementById("year_de_corte").value=year;
	}
}

$(function() {
    $("#fecha_remision").datepicker();
    $("#fecha_remision").datepicker("option", "dateFormat", 'mm/dd/yy');
    $("#fecha_remision").datepicker($.datepicker.regional[ "es" ]);
});



function cargar4505()
{
	
	var hay_errores = validarCampos();		   
	if (hay_errores==false)
	{
		ventana_resumen_reparacion();
	}
}


function empezar_reparacion()
{
	document.getElementById('boton_continuar_modal').innerHTML= 'Espere Mientras Se Hace Upload Del Archivo Para Reparar';
	document.getElementById('boton_continuar_modal').disabled= true;
	window.onbeforeunload = function(e){};
	document.forms['formulario_carga_4505'].submit();
}

function ventana_resumen_reparacion()
{
	var tipo_entidad_seleccionada=document.getElementById("tipo_archivo_4505").value;
	var fecha_corte=document.getElementById("fecha_remision").value;
	
	var prestador_completo= document.getElementById('prestador').value;
	var array_prestador=prestador_completo.split(";;");
	var entidad_1=array_prestador[0];
	
	var entidad_2=document.getElementById("eapb").value;
	var tipo_periodo_tiempo=document.getElementById("tipo_periodo_tiempo").value;
	var nombre_archivo=document.getElementById("nombre_archivo").innerHTML;
	
	
	var mensaje="";
	mensaje+="Nombre del Archivo a Reparar: "+nombre_archivo+".<br>";
	if (tipo_entidad_seleccionada=="individual_ips")
	{
		mensaje+="Tipo Entidad que reportara: Prestador (Archivo individual).<br>";
		mensaje+="Entidad que reportara: "+entidad_1+".<br>";
		mensaje+="Entidad a la cual se realizara el cargue: "+entidad_2+".<br>";
		
	}
	else if (tipo_entidad_seleccionada=="agrupado_eapb")
	{
		mensaje+="Tipo Entidad que reportara: EAPB (Archivo agrupado de la EAPB de varias IPS).<br>";
		mensaje+="Entidad que reparara agrupado: "+entidad_2+".<br>";
	}
	mensaje+="Tipo Rango de tiempo a del archivo a reparar: "+tipo_periodo_tiempo+".<br>";
	mensaje+="Fecha de Corte: "+fecha_corte+".<br>";
	
	$("h3#tituloVentana").html("Resumen");
        $("div#mensajeVentana").html("<p>" + mensaje + "</p>");
        $('#myModal').modal('toggle');
	document.getElementById('boton_continuar_modal').style.display="inline";
}

function validarCampos() 
{

    //campos numericos: numero remision y periodo reportado y codigo administradora de salud
    var mensaje = "";
    var fechaActual = new Date();
    var fechaIngreso = new Date($("#fecha_remision").val());

	if ($("#year_de_corte").val() == "")
        mensaje += '<br>-El a&ntildeo de la fecha de corte es obligatoria\n';
    if ($("#fecha_remision").val() == "")
        mensaje += '<br>-La fecha de remision es obligatoria\n';
    if ($("#numero_remision").val() == "")
        mensaje += '<br>-El numero de remision es obligatorio\n';

    if ($("#prestador").val() == "none")
        mensaje += '<br>-La entidad IPS es obligatoria\n';
    if ($("#eapb").val() == "none")
        mensaje += '<br>-La entidad administradora es obligatoria\n';
    if (fechaIngreso > fechaActual)
        mensaje += '<br>-La fecha de remision no puede ser mayor a la actual\n';
    if (isNaN($("#numero_remision").val()))
        mensaje += '<br>-El numero de remision debe ser numerico\n';
		
	if(fecha_dentro_de_periodo==false)
	{
		mensaje+="<br>-Seleccione una fecha con un periodo Valido.\n";
	}
	
	//verificacion de carga del archivo pyp	
	if(document.getElementById("detalle4505").value=="")
	{
		mensaje+='<br>-Seleccione un archivo para  DETALLE R4505\n';
	}
	
	if (document.getElementById("SGD280RPED_hidden").value!="")
	{
	    mensaje+='<br>-Hay errores en el nombre del archivo\n';
	}
	
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
}


function mostrar_selectores_geograficos()
{
	//obtener_nombre_file_upload('uploader_rips','nombres_archivo_a_cargar');
	/*
	if (document.getElementById("tipo_archivo_4505").value=="ent_territoriales")
	{
	    document.getElementById("campos_filtro_geografico").style.display="block";
	}
	else
	{
	    document.getElementById("campos_filtro_geografico").style.display="none";
	}
	*/
	//var tipo_entidad=document.getElementById("tipo_entidad_asociada_hidden").value;
	if (document.getElementById("tipo_archivo_4505").value=="agrupado_eapb")
	{
		/*
		if (tipo_entidad!="6"
		    && tipo_entidad!="7"
		    && tipo_entidad!="8"
		    && tipo_entidad!="10")
		{
		    document.getElementById("sub_titulo_entidad_2").style.display="none";
		    document.getElementById("entidad_2").style.display="none";
		    $("#eapb").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", true).prop("disabled",true);
		    $("#eapb").parent().find("a.ui-button").button("disable");
		    
		    document.getElementById("sub_titulo_entidad_1").style.display="none";
		    document.getElementById("sub_titulo_entidad_1_oculto").style.display="block";
		    
		    document.getElementById("sub_titulo_entidad_4_oculto").style.display="block";
		}
		else if (tipo_entidad=="6"
		    || tipo_entidad=="7"
		    || tipo_entidad=="8"
		    || tipo_entidad=="10")
		{
		    document.getElementById("sub_titulo_entidad_1").style.display="none";
		    document.getElementById("entidad_1").style.display="none";
		    $("#prestador").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", true).prop("disabled",true);
		    $("#prestador").parent().find("a.ui-button").button("disable");
		    
		    document.getElementById("sub_titulo_entidad_2").style.display="none";
		    document.getElementById("sub_titulo_entidad_2_oculto").style.display="block";
		    
		    document.getElementById("sub_titulo_entidad_3_oculto").style.display="block";
		}
		*/
		
		document.getElementById("sub_titulo_entidad_3_oculto").style.display="block";
		document.getElementById("sub_titulo_entidad_4_oculto").style.display="none";
		
		document.getElementById("sub_titulo_entidad_1_oculto").style.display="none";
		document.getElementById("sub_titulo_entidad_2_oculto").style.display="none";
		
		document.getElementById("sub_titulo_entidad_1").style.display="none";
		document.getElementById("sub_titulo_entidad_2").style.display="block";
		document.getElementById("entidad_1").style.display="none";
		document.getElementById("entidad_2").style.display="block";
		$("#prestador").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", true).prop("disabled",true);
		$("#prestador").parent().find("a.ui-button").button("disable");
		
		$("#eapb").parent().find("input.ui-autocomplete-input").autocomplete("option", "enable", true).prop("enable",true);
		$("#eapb").parent().find("a.ui-button").button("enable");
	}
	else if (document.getElementById("tipo_archivo_4505").value=="agrupado_ips" || document.getElementById("tipo_archivo_4505").value=="agrupado_ips120")
	{
		document.getElementById("sub_titulo_entidad_3_oculto").style.display="none";
		document.getElementById("sub_titulo_entidad_4_oculto").style.display="none";
		
		document.getElementById("sub_titulo_entidad_1_oculto").style.display="none";
		document.getElementById("sub_titulo_entidad_2_oculto").style.display="none";
		
		document.getElementById("sub_titulo_entidad_1").style.display="block";
		document.getElementById("sub_titulo_entidad_2").style.display="none";
		document.getElementById("entidad_1").style.display="block";
		document.getElementById("entidad_2").style.display="none";

		$("#prestador").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", false).prop("disabled",false);
		$("#prestador").parent().find("a.ui-button").button("enable");
		
		$("#eapb").parent().find("input.ui-autocomplete-input").autocomplete("option", "disabled", true).prop("disabled",true);
		$("#eapb").parent().find("a.ui-button").button("disable");

		
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
	    
	}
}

function download_inconsistencias_campos(ruta)
{
	
	window.open(ruta,'Download');
}

function obtener_nombre_file_upload()
{
	var fullPath = document.getElementById('detalle4505').value;
	if (fullPath) 
	{
		var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
		var filename = fullPath.substring(startIndex);
		if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) 
		{
			filename = filename.substring(1);
		}
		//alert(filename);
		document.getElementById('nombre_archivo').innerHTML=filename;
	}
	else
	{
		document.getElementById('nombre_archivo').innerHTML="no se ha subido un archivo";
	}
}


function acomodar_tipo_periodo_tiempo(valor_selector)
{
	var valor_selector_tipo_periodo= valor_selector.value;
	
	if (valor_selector_tipo_periodo=="trimestral")
	{
		//code
		fechas_corte_html="";
		fechas_corte_html+="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo_trimestral();'>";
		fechas_corte_html+="<option value='3-31'>3-31</option>";
		fechas_corte_html+="<option value='6-30'>6-30</option>";
		fechas_corte_html+="<option value='9-30'>9-30</option>";
		fechas_corte_html+="<option value='12-31'>12-31</option>";
		fechas_corte_html+="</select>";
		document.getElementById("mod_campo_fechas_corte").innerHTML=fechas_corte_html;
		
		periodo_html="";
		periodo_html+="<select style='width:230px;' id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte_trimestral();validar_antes_seleccionar_archivos();'>";
		periodo_html+="<option value='1'>Periodo 1</option>";
		periodo_html+="<option value='2'>Periodo 2</option>";
		periodo_html+="<option value='3'>Periodo 3</option>";
		periodo_html+="<option value='4'>Periodo 4</option>";
		periodo_html+="</select>";
		document.getElementById("mod_campo_periodo").innerHTML=periodo_html;
		
		fecha_remision_html="";
		fecha_remision_html+="<input type='text' style='width:215px;' name='fecha_remision' id='fecha_remision' placeholder='clic aqui para seleccionar una fecha' class='campo_azul' onchange='funciones_fecha_restriciones_trimestral(this);validar_antes_seleccionar_archivos();'/>";
		document.getElementById("div_fecha_remision").innerHTML=fecha_remision_html;
		
		$(function()
		{
			$("#fecha_remision").datepicker();
			$("#fecha_remision").datepicker("option", "dateFormat", 'mm/dd/yy');
			$("#fecha_remision").datepicker($.datepicker.regional[ "es" ]);
		});
	}
	else if(valor_selector_tipo_periodo=="mensual")
	{
		//code
		fechas_corte_html="";
		fechas_corte_html+="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
		fechas_corte_html+="<option value='1-31'>1-31</option>";
		fechas_corte_html+="<option value='2-28'>2-28</option>";
		fechas_corte_html+="<option value='3-31'>3-31</option>";
		fechas_corte_html+="<option value='4-30'>4-30</option>";
		fechas_corte_html+="<option value='5-31'>5-31</option>";
		fechas_corte_html+="<option value='6-30'>6-30</option>";
		fechas_corte_html+="<option value='7-31'>7-31</option>";
		fechas_corte_html+="<option value='8-31'>8-31</option>";
		fechas_corte_html+="<option value='9-30'>9-30</option>";
		fechas_corte_html+="<option value='10-31'>10-31</option>";
		fechas_corte_html+="<option value='11-30'>11-30</option>";
		fechas_corte_html+="<option value='12-31'>12-31</option>";
		fechas_corte_html+="</select>";
		document.getElementById("mod_campo_fechas_corte").innerHTML=fechas_corte_html;
		
		periodo_html="";
		periodo_html+="<select style='width:230px;' id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();validar_antes_seleccionar_archivos();'>";
		periodo_html+="<option value='1'>Periodo 1</option>";
		periodo_html+="<option value='2'>Periodo 2</option>";
		periodo_html+="<option value='3'>Periodo 3</option>";
		periodo_html+="<option value='4'>Periodo 4</option>";
		periodo_html+="<option value='5'>Periodo 5</option>";
		periodo_html+="<option value='6'>Periodo 6</option>";
		periodo_html+="<option value='7'>Periodo 7</option>";
		periodo_html+="<option value='8'>Periodo 8</option>";
		periodo_html+="<option value='9'>Periodo 9</option>";
		periodo_html+="<option value='10'>Periodo 10</option>";
		periodo_html+="<option value='11'>Periodo 11</option>";
		periodo_html+="<option value='12'>Periodo 12</option>";
		periodo_html+="</select>";
		document.getElementById("mod_campo_periodo").innerHTML=periodo_html;
		
		fecha_remision_html="";
		fecha_remision_html+="<input type='text' style='width:215px;' name='fecha_remision' id='fecha_remision' placeholder='clic aqui para seleccionar una fecha' class='campo_azul' onchange='funciones_fecha_restriciones(this);validar_antes_seleccionar_archivos();'/>";
		document.getElementById("div_fecha_remision").innerHTML=fecha_remision_html;
		
		$(function()
		{
			$("#fecha_remision").datepicker();
			$("#fecha_remision").datepicker("option", "dateFormat", 'mm/dd/yy');
			$("#fecha_remision").datepicker($.datepicker.regional[ "es" ]);
		});
	}
}

function funciones_fecha_restriciones_trimestral()
{
seleccionar_periodo_automaticamente_trimestral();
seleccionar_fecha_de_corte_trimestral();
poner_year();
}


function seleccionar_periodo_trimestral()
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

function seleccionar_fecha_de_corte_trimestral()
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


function seleccionar_periodo_automaticamente_trimestral(modo)
{
	var fecha_a_revisar=$("#fecha_remision").val();
	//alert(fecha_a_revisar);
	if(isDate(fecha_a_revisar))
	{
		var fecha_dividida = fecha_a_revisar.split("/");
		
		var year=fecha_dividida[2];
		//alert(year);
		
		var fecha_ini_p1="03/31/"+year;
		var fecha_ini_p2="06/30/"+year;
		var fecha_ini_p3="09/30/"+year;
		var fecha_ini_p4_1="12/31/"+year;
		var fecha_ini_p4_2="01/01/"+year;
		
		var fecha_fin_p1="04/25/"+year;
		var fecha_fin_p2="07/25/"+year;
		var fecha_fin_p3="10/25/"+year;
		var fecha_fin_p4_1="12/31/"+year;
		var fecha_fin_p4_2="01/25/"+year;
		
		var encontro_periodo=false;
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p1,fecha_fin_p1))
		{
			$('#periodo').val('1');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p2,fecha_fin_p2))
		{
			$('#periodo').val('2');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p3,fecha_fin_p3))
		{
			$('#periodo').val('3');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p4_1,fecha_fin_p4_1))
		{
			$('#periodo').val('4');
			encontro_periodo=true;
		}
		if(mirar_fecha_entre(fecha_a_revisar,fecha_ini_p4_2,fecha_fin_p4_2))
		{
			$('#periodo').val('4');
			encontro_periodo=true;
		}
		
		if(encontro_periodo==false)
		{
			fecha_dentro_de_periodo=false;
			if(modo!="verificar")
			{
				alert("La fecha no corresponde a ningun periodo");
			}
		}
		else
		{
			fecha_dentro_de_periodo=true;			
		}
	}
}


function validar_antes_seleccionar_archivos()
{
	var prestador = document.getElementById('prestador').value;
	var eapb = document.getElementById('eapb').value;
	var rango_periodo=document.getElementById('tipo_periodo_tiempo').value;
	var fecha_remision = document.getElementById('fecha_remision').value;
	var periodo = document.getElementById('periodo').value;
	
	verificar_nombre_archivo(document.getElementById('detalle4505').value,'SGD280RPED','nombre_archivo');
	
	
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
			mensaje+="ERROR: Archivo debe iniciar por "+sigla+". para pyp 4505 <br>";
		}
		
		
		//LONGITUD
		if (document.getElementById("tipo_archivo_4505").value=="individual_ips")
		{
		    if (nombre_sin_extension[0].length!=35)
		    {
			mensaje+="ERROR: La longitud del archivo de "+nombre_sin_extension[0].length+" caracteres no corresponde a 35 caracteres <br>";
		    }
		}
		else if (document.getElementById("tipo_archivo_4505").value=="agrupado_eapb")
		{
		    if (nombre_sin_extension[0].length!=35 && nombre_sin_extension[0].length!=22)
		    {
			mensaje+="ERROR: La longitud del archivo de "+nombre_sin_extension[0].length+" caracteres no corresponde a 35 o 22 caracteres para agrupado <br>";
		    }
		    
		}
		//FIN LONGITUD
		//debido a que se toma sin la parte sigla arreglar porque aca indica el nit
		//para comparar con codigo habilitacion hay una segunda posicion y longitud
		//a tener en cuenta
		//fecha de corte posicion 11 hasta longitud 18 pero pos 0 y longitud 8
		// NI pos 18 longitud 20 pero pos 8 longitud 10
		//prestador pos 20 longitud 32 pero pos 10 y longitud 22 
		//regimen pos 32 longitud 33 pero pos 22 longitud 23
		//consecutivo pos 33 longitud 35 pero pos 23 y longitud 25
		
		//FECHA REMISION
		if(array_nombre_archivo.length==2)
		{
		    var periodo = document.getElementById('periodo').value;
		    var fecha_remision = document.getElementById('fecha_remision').value;
		    var array_fecha_remision = fecha_remision.split("/");
		    if (array_fecha_remision.length==3)
		    {
			if (isNaN(array_fecha_remision[0])==false //si es un numero
			    && isNaN(array_fecha_remision[1])==false //si es un numero
			    && isNaN(array_fecha_remision[2])==false //si es un numero
			    )
			{
			    //code
			    var fecha_corte_del_nombre=array_nombre_archivo[1].substring(0,8);
			    var fecha_unida_digitada=array_fecha_remision[2]+array_fecha_remision[0]+array_fecha_remision[1];
			    if (fecha_corte_del_nombre!=fecha_unida_digitada)
			    {
				mensaje+="ERROR: La fecha digitada "+fecha_remision+" "+fecha_unida_digitada+" no corresponde a la fecha del archivo "+fecha_corte_del_nombre+"<br>";
			    }
			}
			else
			{
			    mensaje+="ERROR: La fecha digitada "+fecha_remision+" no esta bien escrita <br>";
			}//fin else
		    }//fin if
		    else
		    {
			mensaje+="ERROR: La fecha digitada "+fecha_remision+" no esta bien escrita (no posee los tres componentes de fecha) <br>";
		    }
		}//fin if
		//FIN FECHA REMISION
		
		
		
		//PRESTADOR
		
		if(array_nombre_archivo.length==2)
		{
		    var prestador_completo= document.getElementById('prestador').value;
		    var array_prestador=prestador_completo.split(";;");
		    var prestador= array_prestador[0];
		    var prestador_nit= array_prestador[1];
		    
		    
		    while(prestador_nit.length<12 && prestador_nit!="none")
		    {
			    prestador_nit="0"+prestador_nit;
		    }
		    if(prestador_nit!="none")
		    {			
			    var prestador_nombre=array_nombre_archivo[1].substring(10,22);
			    
			    if(prestador_nombre!=prestador_nit  && document.getElementById("tipo_archivo_4505").value=="individual_ips")
			    {
				    
				    mensaje+="ERROR: El prestador "+prestador+" con nit "+prestador_nit+" no corresponde al nit de prestador indicado en el archivo "+prestador_nombre+". <br>";
			    }
			    		    
			    
		    }//fin if prestador
		    
		    
		
		}//fin if la sigla fue correcta
		
		//FIN PRESTADOR
		
		//tipo nit
		var tipo_nit=array_nombre_archivo[1].substring(8,10);
		if (tipo_nit!="MU"
		    && tipo_nit!="DI"
		    && tipo_nit!="DE"
		    && tipo_nit!="NI"
		    )
		{
		    mensaje+="ERROR: el tipo nit  "+tipo_nit+" no corresponde a MU, DI, DE, NI.<br>";
		}
		//fin tipo nit
		
		//regimen
		var regimen=array_nombre_archivo[1].substring(22,23);
		if (regimen!="C"
		    && regimen!="S"
		    && regimen!="P"
		    && regimen!="E"
		    && regimen!="N"
			&& regimen!="O"
		    )
		{
		    mensaje+="ERROR: el regimen  "+regimen+" no corresponde a C, S, P, E, N, O.<br>";
		}
		//fin regimen
		
		//numero remision
		var numero_remision_de_archivo=array_nombre_archivo[1].substring(23,25);
		var numero_remision_gui= document.getElementById('numero_remision').value;
		if (isNaN(numero_remision_de_archivo)==true)
		{
			mensaje+="ERROR: el numero de remision no es numerico.<br>";
		}
		else if (numero_remision_gui!=numero_remision_de_archivo)
		{
			mensaje+="ERROR: el numero de remision digitado "+numero_remision_gui+" es diferente del numero de remision indicado en el nombre del archivo "+numero_remision_de_archivo+".<br>";
		}
		//fin numero remision
		
		if(mensaje!="")
		{
			//alert(mensaje);
			document.getElementById(div_nombre).innerHTML="<span >"+nombre_sin_extension[0]+"<br>"+mensaje+"</span>";
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

onbeforeunload = function(e){	
		return 'Recuerde que se perderan los cambios realizados.';	
}


$(document).ready(function(){
    $("#eapb").combobox();
    $("#prestador").combobox();
})