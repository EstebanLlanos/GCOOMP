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

function consultar_interfaces()
{
    var perfil_id=document.getElementById('selector_perfiles').value;
    if (perfil_id!="none")
    {
      //alert(perfil_id);
	    ConsultaAJAX_Async("perfil_id="+perfil_id,"consultar_interfaces.php","div_lista_de_interfaces");
    }
    else
    {
	     document.getElementById("div_lista_de_interfaces").innerHTML="Seleccione un perfil.";
    }
}

function activar_para_perfil(id_menu)
{
    //usar con el evento on change
    var is_checked=document.getElementById("checkbox_menu_id_"+id_menu).checked;
    var nombre_menu=document.getElementById("nombre_menu_"+id_menu).value;
    var perfil_id=document.getElementById('selector_perfiles').value;
    var id_principal=id_menu;
    
    var string_is_checked="";
    if (is_checked)
    {
	string_is_checked="adicionar";
    }
    else
    {
	string_is_checked="quitar";
    }
    
    ConsultaAJAX_Async("perfil_id="+perfil_id
		       +"&action="+string_is_checked
		       +"&nombre_menu="+nombre_menu
		       +"&id_principal="+id_principal
		       ,"activar_interfaz.php","none");
    
    if (is_checked)
    {
	alert(nombre_menu+" se adiciono al perfil.\nPara visualizar el cambio reinicie su sesion.");
    }
    else
    {
	alert(nombre_menu+" se quito del perfil.\nPara visualizar el cambio reinicie su sesion.");
    }
    
}

function crear_perfil()
{
    var nombre_nuevo_perfil=document.getElementById("nombre_nuevo_perfil").value;
    var tendra_derechos_admin=document.getElementById("tendra_derechos_admin").value;
    
    
    if (nombre_nuevo_perfil.trim()!="")
    {
	
	
	ConsultaAJAX_Async("nombre_nuevo_perfil="+nombre_nuevo_perfil
		       +"&tendra_derechos_admin="+tendra_derechos_admin
		       ,"crear_perfil.php","none");
	
	alert("Nombre nuevo perfil: "+nombre_nuevo_perfil+", Derechos admin: "+tendra_derechos_admin+"\n Actualice la pagina actual para ver el nuevo perfil en el selector.");
    }
    else
    {
	alert("Digite un nombre para el nuevo perfil.");
    }
}


function cambiar_valor(id_input, incremento,operacion)
{
  if(document.getElementById(id_input) )
  {
    var valor_actual=new BigNumber(document.getElementById(id_input).value);
    var incremento_float=new BigNumber(incremento);
    var resultado=0;

    resultado=valor_actual;
    if(operacion=='suma')
    {
      resultado=valor_actual.plus(incremento_float);
    }//fin if
    else if(operacion=='resta')
    {
      resultado=valor_actual.minus(incremento_float);
      if(parseFloat(resultado)<0)
      {
        resultado=0;
      }//fin if
    }//fin else
    
    document.getElementById(id_input).value=resultado;

  }//fin if
  else
  {
    alert('Elemento '+id_input+' no existe ');
  }
}//fin function


function cambiar_prioridad_bd(id_principal)
{
  if(document.getElementById('valor_prio_'+id_principal) )
  {
    var valor_actual=document.getElementById('valor_prio_'+id_principal).value;

    ConsultaAJAX_Async("valor_actual="+valor_actual+"&id_principal="+id_principal,"prioridad_opcion.php","res_prio"+id_principal);
  }//fin if
}//fin function


function isNumberKey(evt) 
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
        return false;

    return true;
}