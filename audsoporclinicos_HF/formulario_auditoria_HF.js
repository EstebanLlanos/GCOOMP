tday  =new Array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
tmonth=new Array("January","February","March","April","May","June","July","August","September","October","November","December");
var cohorte_seleccionada = "";
var calificacion_aud_archivo = "";
var archivo_seleccionado = "";
var desplegado = false;

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
//document.getElementById('clockbox').innerHTML=""+nhour+":"+nmin+":"+nsec+ap+"";
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
}

$(document).ready(function() {
  
  $('.tree').treegrid();

  // DESPLIEGUE DE MODAL INICIO

  $("#modal_cohorte").modal();

  // SELECCION DE COHORTE DESDE EL INICIO

  $("#inicio_hemo_nuevos").click(function() {
    cohorte_seleccionada = $("#inicio_hemo_nuevos").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $('#modal_cohorte').modal('toggle');
    $("#modal_pacientes").modal();
  });

  $( "#inicio_hemo_anteriores" ).click(function() {
    cohorte_seleccionada = $("#inicio_hemo_anteriores").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $('#modal_cohorte').modal('toggle');
    $("#modal_pacientes").modal();
  });

  $( "#inicio_coagu_nuevos" ).click(function() {
    cohorte_seleccionada = $("#inicio_coagu_nuevos").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $('#modal_cohorte').modal('toggle');
    $("#modal_pacientes").modal();
  });

  $( "#inicio_coagu_anteriores" ).click(function() {
    cohorte_seleccionada = $("#inicio_coagu_anteriores").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $('#modal_cohorte').modal('toggle');
    $("#modal_pacientes").modal();
  });

  $( "#inicio_diagnostico_severidad" ).click(function() {
    cohorte_seleccionada = $("#inicio_diagnostico_severidad").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $('#modal_cohorte').modal('toggle');
    $("#modal_pacientes").modal();
  });

  // SELECCION DE COHORTE DESDE EL MENU

  $("#hemofilia_nuevo").click(function() {
    cohorte_seleccionada = $("#hemofilia_nuevo").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $("#modal_pacientes").modal();
  });

  $( "#hemofilia_anterior" ).click(function() {
    cohorte_seleccionada = $("#hemofilia_anterior").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $("#modal_pacientes").modal();
  });

  $( "#coagulo_nuevo" ).click(function() {
    cohorte_seleccionada = $("#coagulo_nuevo").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $("#modal_pacientes").modal();
  });

  $( "#coagulo_anterior" ).click(function() {
    cohorte_seleccionada = $("#coagulo_anterior").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $("#modal_pacientes").modal();
  });

  $( "#diagnostico_severidad" ).click(function() {
    cohorte_seleccionada = $("#diagnostico_severidad").text();
    $("#cohorte_actual").text(cohorte_seleccionada);
    $("#modal_pacientes").modal();
  });

  // CALIFICACIÓN DE AUDITORÍA DE ARCHIVOS

  $( "#selector_calificacion" ).click(function() {
    $("#elementos_calificacion").css("visibility", "visible");
  });

  $( "#calificacion_aud_archivo_1" ).click(function() {
    calificacion_aud_archivo = $("#calificacion_aud_archivo_1").text();
    $("#calificacion_archivo_selec").text(calificacion_aud_archivo);
    $("#elementos_calificacion").css("visibility", "hidden");
  });

  $( "#calificacion_aud_archivo_2" ).click(function() {
    calificacion_aud_archivo = $("#calificacion_aud_archivo_2").text();
    $("#calificacion_archivo_selec").text(calificacion_aud_archivo);
    $("#elementos_calificacion").css("visibility", "hidden");
  });

  $( "#calificacion_aud_archivo_3" ).click(function() {
    calificacion_aud_archivo = $("#calificacion_aud_archivo_3").text();
    $("#calificacion_archivo_selec").text(calificacion_aud_archivo);
    $("#elementos_calificacion").css("visibility", "hidden");
  });

    // SELECCIÓN DE ARCHIVO DE SOPORTE DE AUDITORÍA

  $("#selector_archivo a").click(function(){
      var archivo_sel = $(this).text()
      $("#btn_selector_archivo").text(archivo_sel);
  });

  $("#modifica_dato a").click(function(){
      var mod_dato = $(this).text()
      $("#btn_modifica_dato").text(mod_dato);
  });

  $("#modifica_archivo_original a").click(function(){
      var archivo_mod = $(this).text()
      $("#btn_modifica_archivo_original").text(archivo_mod);
  });

  // INICIO DE INTERFAZ DE AUDITORIA AL SELECCIONAR UN PACIENTE

  $("#iniciar_auditoria").click(function() {
    $("#modal_pacientes").modal('toggle');
    $("#contenedor_info_paciente").css("display", "block");
    $("#contenedor_campos_auditoria").css("display", "block");
    $("#contenedor_interno_archivos").css("display", "block");
  });

  $("#continuar_auditoria").click(function() {
    $("#modal_pacientes").modal('toggle');
    $("#contenedor_info_paciente").css("display", "block");
    $("#contenedor_campos_auditoria").css("display", "block");
    $("#contenedor_interno_archivos").css("display", "block");
  });

  $("#campos_auditar_btn").click(function() {

    $("#cuerpo_pagina").css("overflow", "auto");
    $("#contenedor_archivos").css("height", "68vh");

    if (desplegado == false) {

      $(function(){
         function scrollDown(){
            $(document).scrollTop($(document).height());
         };
         window.setTimeout( scrollDown, 270 );
      });

      desplegado = true;

    } else {

      $("#cuerpo_pagina").css("overflow", "hidden");
      $("#contenedor_archivos").css("height", "58vh");

      $(function(){
         function scrollDown(){
            $(document).scrollTop(-$(document).height());
         };
         window.setTimeout( scrollDown, 270 );
      });

      desplegado = false;

    }
    
  });

  $("#btn_guardar_campo_1").click(function(){

    $("#success_alert").removeClass("d-none").fadeIn('slow').delay(5000).fadeOut('slow');

  });

  $("#btn_guardar_campo_2").click(function(){
    $("#danger_alert").removeClass("d-none").fadeIn('slow').delay(5000).fadeOut('slow');
  });

});