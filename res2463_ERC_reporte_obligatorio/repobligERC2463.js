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


function consultar_eapb(tipo_id,identificacion)
{
	var prestador_cod= document.getElementById("prestador").value;
	if(prestador_cod!="none")
	{
		ConsultaAJAX("prestador="+prestador_cod+"&tipo_id="+tipo_id+"&identificacion="+identificacion,"consulta_eapb.php","div_eapb");
	}
}


function seleccionar_periodo()
{
	var fecha_corte = document.getElementById("fechas_corte").value;
	if(fecha_corte=="01-31")
	{
		document.getElementById("periodo").value="1";
	}
	if(fecha_corte=="02-28")
	{
		document.getElementById("periodo").value="2";
	}
	if(fecha_corte=="03-31")
	{
		document.getElementById("periodo").value="3";
	}
	if(fecha_corte=="04-30")
	{
		document.getElementById("periodo").value="4";
	}
	if(fecha_corte=="05-31")
	{
		document.getElementById("periodo").value="5";
	}
	if(fecha_corte=="06-30")
	{
		document.getElementById("periodo").value="6";
	}
	if(fecha_corte=="07-31")
	{
		document.getElementById("periodo").value="7";
	}
	if(fecha_corte=="08-31")
	{
		document.getElementById("periodo").value="8";
	}
	if(fecha_corte=="09-30")
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
		document.getElementById("fechas_corte").value="01-31";
	}
	if(periodo==2)
	{
		document.getElementById("fechas_corte").value="02-28";
	}
	if(periodo==3)
	{
		document.getElementById("fechas_corte").value="03-31";
	}
	if(periodo==4)
	{
		document.getElementById("fechas_corte").value="04-30";
	}
	if(periodo==5)
	{
		document.getElementById("fechas_corte").value="05-31";
	}
	if(periodo==6)
	{
		document.getElementById("fechas_corte").value="06-30";
	}
	if(periodo==7)
	{
		document.getElementById("fechas_corte").value="07-31";
	}
	if(periodo==8)
	{
		document.getElementById("fechas_corte").value="08-31";
	}
	if(periodo==9)
	{
		document.getElementById("fechas_corte").value="09-30";
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
	//para semestrales
	if(periodo==13)
	{
		document.getElementById("fechas_corte").value="06-30";
	}
	if(periodo==14)
	{
		document.getElementById("fechas_corte").value="12-31";
	}
	document.getElementById("fecha_corte_periodo_actual").innerHTML="<input type='text' style='width:100px' id='texto_fecha_c' name='texto_fecha_c'  class='campo_azul' value='"+document.getElementById("fechas_corte").value+"' readonly/>";
}//fin selecciona fecha de corte

function isNumberKey(evt) 
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function escribiendo_year_corte()
{
	var year_c= document.getElementById("year_de_corte").value;
	
	if(document.getElementById("texto_fecha_c"))
	{
		document.getElementById("texto_fecha_c").value=document.getElementById("fechas_corte").value+"-"+year_c;
	}
}

$(function() 
{
    $("#selector_estado_info").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione un estado para la informaci&oacuten<br>'});
    $("#fechas_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione la fecha de corte correspondiente al reporte<br>'});
	$("#year_de_corte").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor seleccione el a&ntildeo para la fecha de corte correspondiente al reporte<br>'});
    $("#periodo").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el periodo correspondiente al reporte de PyP<br>'});
    $("#prestador").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione el prestador que reportara 4505 PyP<br>'});
    $("#eapb").popover({placement: 'right', html: true, trigger: 'hover', title: '<b>Descripcion<b>', content: 'Por favor, seleccione la eps a reportar 4505 PyP<br>'});
   
});

function download_reporte(ruta_descarga_archivo)
{ 
	window.open(ruta_descarga_archivo,'Download');
}

function enviar_formulario()
{
	var intRegex = /^\d+$/;
	var floatRegex = /^((\d+(\.\d *)?)|((\d*\.)?\d+))$/;
	
	var mensaje_error="";
	var year = $('#year_de_corte').val();
	
	if(intRegex.test(year)) 
	{
		
	}
	else
	{
		mensaje_error+="El a&ntildeo no es un numero entero.<br></br>";
	}
	
	if($('#selector_estado_info').val()=="none")
	{
		mensaje_error+="Seleccione el estado de la informaci&oacuten de los reportes 4505.<br></br>";
	}
	
	if($('#periodo').val()=="none")
	{
		mensaje_error+="Seleccione el periodo de los reportes CANCER 0247.<br></br>";
	}
	
	if($('#eapb').val()=="none")
	{
		mensaje_error+="Seleccione la entidad administradora EAPB para el cual reportara 4505.<br></br>";
	}
	
	if(mensaje_error=="")
	{
		document.reporte_obligatorio.submit();
	}
	else
	{
		$("h3#tituloVentana").html("Advertencia");
        $("div#mensajeVentana").html("<p>" + mensaje_error + "</p>");
        $('#myModal').modal('toggle');
	}
}

onbeforeunload = function(e){	
		return 'Recuerde que se perderan los cambios realizados.';	
}

$(document).ready(function(){
    $("#eapb").combobox();
    //$("#prestador").combobox();
})