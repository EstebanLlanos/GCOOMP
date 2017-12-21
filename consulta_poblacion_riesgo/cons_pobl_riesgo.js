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


//especificar none si el resultado de la peticion ajax no sera contenida en un div
//es asincrona, no pone warning
function ConsultaAJAX_Async(parametros,filePHP,divContent)
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
	    xmlhttp.onreadystatechange=function()
	    {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		    document.getElementById(divContent).innerHTML=xmlhttp.responseText;
		}
	    }
	    
	    xmlhttp.open("GET",filePHP+"?"+parametros+"&campodiv="+divContent,true);
	    xmlhttp.send();
		
	}
	else
	{
	    xmlhttp.onreadystatechange=function()
	    {
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
		    alert(xmlhttp.responseText);
		    return xmlhttp.responseText;
		}
	    }
	    
	    xmlhttp.open("GET",filePHP+"?"+parametros,true);
	    xmlhttp.send();
		
	}

}//fin funcion consulta ajax


//SELECCION TIPO PERIODO MENSUAL O TRIMESTRAL


function cambio_tipo_tiempo_periodo()
{
    var tipo_tiempo_periodo = document.getElementById("tipo_tiempo_periodo").value;
    
    if (tipo_tiempo_periodo!="none" && tipo_tiempo_periodo!="")
    {
	ConsultaAJAX_Async("cambio_tipo_tiempo_periodo="+tipo_tiempo_periodo,"cambio_tipo_tiempo_periodo.php","div_selector_periodo");
    }
    
    document.getElementById("fecha_corte_periodo_actual").innerHTML="";
}

function seleccionar_fecha_de_corte()
{
	var periodo = document.getElementById("periodo").value;
	var tipo_tiempo_periodo = document.getElementById("tipo_tiempo_periodo").value;
	if (tipo_tiempo_periodo=="trimestral")
	{
	    
	    if(periodo==1)
	    {
		    document.getElementById("fechas_corte").value="03-31";
	    }
	    if(periodo==2)
	    {
		    document.getElementById("fechas_corte").value="06-30";
	    }
	    if(periodo==3)
	    {
		    document.getElementById("fechas_corte").value="09-30";
	    }
	    if(periodo==4)
	    {
		    document.getElementById("fechas_corte").value="12-31";
	    }
	    if(periodo==5)
	    {
		    document.getElementById("fechas_corte").value="12-31";
	    }
	
	}//fin if trimestral
	else if (tipo_tiempo_periodo=="mensual")
	{
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
	    if(periodo==13)
	    {
		    document.getElementById("fechas_corte").value="12-31";
	    }
	}//fin else if mensual
	
	document.getElementById("fecha_corte_periodo_actual").innerHTML="<input type='text' style='width:100px' id='texto_fecha_c' name='texto_fecha_c'  class='campo_azul' value='"+document.getElementById("fechas_corte").value+"' readonly/>";
}//fin selecciona fecha de corte

//FIN SELECCION TIPO PERIODO MENSUAL O TRIMESTRAL


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
		mensaje_error+="Seleccione el estado de la informaci&oacuten de los reportes PyP 4505.<br></br>";
	}
	
	if($('#periodo').val()=="none")
	{
		mensaje_error+="Seleccione el periodo de los reportes.<br></br>";
	}
	
	if($('#eapb').val()=="none")
	{
		mensaje_error+="Seleccione la entidad administradora EAPB<br></br>";
	}
	
	if($('#prestador').val()=="none")
	{
		mensaje_error+="Seleccione la entidad prestadora IPS<br></br>";
	}
	
	if(mensaje_error=="")
	{
		window.onbeforeunload = function(e){};
		document.formulario.submit();
	}
	else
	{
		$("h3#tituloVentana").html("Advertencia");
        $("div#mensajeVentana").html("<p>" + mensaje_error + "</p>");
        $('#myModal').modal('toggle');
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

function escribiendo_year_corte()
{
	var year_c= document.getElementById("year_de_corte").value;
	
	if(document.getElementById("texto_fecha_c"))
	{
		document.getElementById("texto_fecha_c").value=document.getElementById("fechas_corte").value+"-"+year_c;
	}
}

$(document).ready(function(){
    $("#eapb").combobox();
    $("#prestador").combobox();
    $("#riesgo_poblacion").combobox();
})