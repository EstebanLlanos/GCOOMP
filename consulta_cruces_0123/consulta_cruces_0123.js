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
        //poner aqui funcion
        consultar_valor_permitido_campo_especifico();
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


function acomodar_tipo_periodo_tiempo(valor_selector)
{
  var valor_selector_tipo_periodo= valor_selector.value;
  
  if (valor_selector_tipo_periodo=="anual")
  {
    //code
    
    
    periodo_html="";
    periodo_html+="<select style='width:230px;' id='periodo' name='periodo' class='campo_azul' onchange='consultar_archivos_subidos_para_periodo_year();'>";
    periodo_html+="<option value='none'>Seleccione un Periodo</option>";
    periodo_html+="<option value='1'>Anual  (AAAA anterior)-04-01 hasta (AAAA actual)-03-31</option>";
    periodo_html+="</select>";
    document.getElementById("mod_campo_periodo").innerHTML=periodo_html;

    
  }
  else if (valor_selector_tipo_periodo=="semestral")
  {
    //code
    
    
    periodo_html="";
    periodo_html+="<select style='width:230px;' id='periodo' name='periodo' class='campo_azul' onchange='consultar_archivos_subidos_para_periodo_year();'>";
    periodo_html+="<option value='none'>Seleccione un Periodo</option>";
    periodo_html+="<option value='1'>Semestre 1</option>";
    periodo_html+="<option value='2'>Semestre 2</option>";
    periodo_html+="</select>";
    document.getElementById("mod_campo_periodo").innerHTML=periodo_html;

    
  }
  else if (valor_selector_tipo_periodo=="trimestral")
  {
    //code
    
    
    periodo_html="";
    periodo_html+="<select style='width:230px;' id='periodo' name='periodo' class='campo_azul' onchange='consultar_archivos_subidos_para_periodo_year();'>";
    periodo_html+="<option value='none'>Seleccione un Periodo</option>";
    periodo_html+="<option value='1'>Periodo 1</option>";
    periodo_html+="<option value='2'>Periodo 2</option>";
    periodo_html+="<option value='3'>Periodo 3</option>";
    periodo_html+="<option value='4'>Periodo 4</option>";
    periodo_html+="</select>";
    document.getElementById("mod_campo_periodo").innerHTML=periodo_html;

    
  }
  else if(valor_selector_tipo_periodo=="mensual")
  {
    //code
    
    
    periodo_html="";
    periodo_html+="<select style='width:230px;' id='periodo' name='periodo' class='campo_azul' onchange='consultar_archivos_subidos_para_periodo_year();'>";
    periodo_html+="<option value='none'>Seleccione un Periodo</option>";
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

    
  }
}//fin function acomodar periodo

async function consultar_ips_archivo(identificador_archivo)
{
    if(identificador_archivo!="none" && identificador_archivo!="")
    {
      ConsultaAJAX_Async("identificador_archivo="+identificador_archivo,"consulta_prestadores_en_archivo.php","div_entidades_prestadoras_archivo");
      await sleep(3000);
      $("#prestador").combobox();
    }//fin if
}//fin function

async function consultar_archivos_subidos_para_periodo_year()
{
     var year_corte=document.getElementById('year_de_corte').value.trim();
     var tipo_periodo_tiempo=document.getElementById('tipo_periodo_tiempo').value.trim();
     var periodo=document.getElementById('periodo').value.trim();
    if(periodo!="none" && periodo!="" && year_corte!="")
    {
      ConsultaAJAX_Async("periodo="+periodo+"&year_corte="+year_corte+"&tipo_periodo_tiempo="+tipo_periodo_tiempo,"consultar_archivos_subidos_para_periodo.php","div_archivos_subidos");
    }//fin if
    await sleep(1000);
    consultar_ips_archivo("no");
}//fin function

function consultar_valor_permitido_campo_especifico()
{
    if(document.getElementById('selector_campo_especifico') )
    {
        var selector_campo_especifico=document.getElementById('selector_campo_especifico').value.trim();
        var copy_selector_campo_especifico=document.getElementById('copy_selector_campo_especifico').value.trim();
         if(selector_campo_especifico!="" && selector_campo_especifico!="none" && selector_campo_especifico!=copy_selector_campo_especifico)
         {
            document.getElementById('copy_selector_campo_especifico').value=selector_campo_especifico;
            //mod_campo_valor_permitido_1
            ConsultaAJAX_Async("selector_campo_especifico="+selector_campo_especifico,"consulta_valor_permitido_campo.php","mod_campo_valor_permitido_1");
         }//fin if
   }//fin if existe
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

function isNumberKey(evt) 
{
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
    && (charCode < 48 || charCode > 57))
        return false;

    return true;
}

function mostrar_opciones_correspondientes_al_caso()
{
  var selector_rango_conteo=document.getElementById('selector_rango_conteo').value;
  if(selector_rango_conteo=="all_allips")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='none';
    document.getElementById('sub_titulo_7').style.display='none';
    
    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='none';
    document.getElementById('sub_titulo_9').style.display='none';

    document.getElementById('div_detallado_o_especifico').style.display='none';
    document.getElementById('sub_titulo_8').style.display='none';

  }//fin if
  else if(selector_rango_conteo=="specific_allips")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='none';
    document.getElementById('sub_titulo_7').style.display='none';

    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='inline';
    document.getElementById('sub_titulo_9').style.display='inline';

    document.getElementById('div_detallado_o_especifico').style.display='inline';
    document.getElementById('sub_titulo_8').style.display='inline';

    document.getElementById('selector_general_o_detallado').value="conteo";
  }//fin if
  else if(selector_rango_conteo=="all_oneips")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='inline';
    document.getElementById('sub_titulo_7').style.display='inline';

    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='none';
    document.getElementById('sub_titulo_9').style.display='none';

    document.getElementById('div_detallado_o_especifico').style.display='none';
    document.getElementById('sub_titulo_8').style.display='none';
  }//fin if 
  else if(selector_rango_conteo=="specific_oneips")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='inline';
    document.getElementById('sub_titulo_7').style.display='inline';

    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='inline';
    document.getElementById('sub_titulo_9').style.display='inline';

    document.getElementById('div_detallado_o_especifico').style.display='inline';
    document.getElementById('sub_titulo_8').style.display='inline';

    document.getElementById('selector_general_o_detallado').value="conteo";
  }//fin if
  else if(selector_rango_conteo=="clone")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='none';
    document.getElementById('sub_titulo_7').style.display='none';
    
    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='none';
    document.getElementById('sub_titulo_9').style.display='none';

    document.getElementById('div_detallado_o_especifico').style.display='none';
    document.getElementById('sub_titulo_8').style.display='none';

  }//fin if
  else if(selector_rango_conteo=="cross_allips")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='none';
    document.getElementById('sub_titulo_7').style.display='none';
    
    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='none';
    document.getElementById('sub_titulo_9').style.display='none';

    document.getElementById('div_detallado_o_especifico').style.display='none';
    document.getElementById('sub_titulo_8').style.display='none';

    document.getElementById('div_para_consultas_cruzadas').style.display='inline';

  }//fin if
  else if(selector_rango_conteo=="cross_oneips")
  {
    document.getElementById('div_entidades_prestadoras_archivo').style.display='inline';
    document.getElementById('sub_titulo_7').style.display='inline';
    
    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='none';
    document.getElementById('sub_titulo_9').style.display='none';

    document.getElementById('div_detallado_o_especifico').style.display='none';
    document.getElementById('sub_titulo_8').style.display='none';

    document.getElementById('div_para_consultas_cruzadas').style.display='inline';

  }//fin if
}//fin function

function inicializador_javascript()
{
    document.getElementById('div_entidades_prestadoras_archivo').style.display='none';
    document.getElementById('sub_titulo_7').style.display='none';
    
    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
    document.getElementById('sub_titulo_10').style.display='none';

    document.getElementById('mod_campo_1').style.display='none';
    document.getElementById('sub_titulo_9').style.display='none';

    document.getElementById('div_detallado_o_especifico').style.display='none';
    document.getElementById('sub_titulo_8').style.display='none';
}//fin function inicializador

function ejecutar_consulta()
{
 var errores_previo_submit="";

 if(document.getElementById('selector_rango_conteo').value=="" || document.getElementById('selector_rango_conteo').value=="none")
 {
    errores_previo_submit+="Debe seleccionar una opcion para el rango de la consulta\n";
 }//fin if

 if(document.getElementById('year_de_corte').value=="" || isNaN(document.getElementById('year_de_corte').value)==true )
  {
    errores_previo_submit+="El ano de corte, posee un valor invalido\n";
  }//fin if

  //tipo_periodo_tiempo
  if(document.getElementById('tipo_periodo_tiempo').value=="" || document.getElementById('tipo_periodo_tiempo').value=="none")
 {
    errores_previo_submit+="Debe seleccionar el rango del periodo del archivo sobre el cual se realizara la consulta\n";
 }//fin if

  if(document.getElementById('periodo').value=="" || document.getElementById('periodo').value=="none")
 {
    errores_previo_submit+="Debe seleccionar el periodo del archivo sobre el cual se realizara la consulta\n";
 }//fin if

 //selector_archivos_subidos
  if(document.getElementById('selector_archivos_subidos').value=="" || document.getElementById('selector_archivos_subidos').value=="none")
 {
    errores_previo_submit+="Debe seleccionar el archivo sobre el cual realizara la consulta\n";
 }//fin if

 var selector_rango_conteo=document.getElementById('selector_rango_conteo').value;
  if(selector_rango_conteo=="all_allips")//todas las ips , todos los campos
  {
    //no requiere validacion de campos adicionales
  }//fin if
  else if(selector_rango_conteo=="specific_allips")//campo especifico, todas las ips
  {
    if(document.getElementById('selector_campo_especifico').value=="" || document.getElementById('selector_campo_especifico').value=="none")
   {
      errores_previo_submit+="Debe seleccionar el campo especifico sobre el cual se realizara la consulta\n";
   }//fin if

   if(document.getElementById('selector_general_o_detallado').value=="" || document.getElementById('selector_general_o_detallado').value=="none")
   {
      errores_previo_submit+="Debe seleccionar si el resultado para la consulta realizada sobre el campo seleccionado sera por conteo agrupado o detallada\n";
   }//fin if
   else if(document.getElementById('selector_general_o_detallado').value=="detallado")
    {
      if(document.getElementById('selector_campo_valor_permitido_1').value=="" || document.getElementById('selector_campo_valor_permitido_1').value=="none")
     {
        errores_previo_submit+="Debe seleccionar el valor permitido del campo seleccionado para la consulta detallada\n";
     }//fin if
    }//fin else if
  }//fin if
  else if(selector_rango_conteo=="all_oneips")//todos lso campos , ips especifica
  {
    if(document.getElementById('prestador').value=="" || document.getElementById('prestador').value=="none")
   {
      errores_previo_submit+="Debe seleccionar el prestador encontrado en el archivo sobre el cual se realizara la consulta\n";
   }//fin if
  }//fin if 
  else if(selector_rango_conteo=="specific_oneips")//campo especifico, ips especifica
  {
    if(document.getElementById('prestador').value=="" || document.getElementById('prestador').value=="none")
   {
      errores_previo_submit+="Debe seleccionar el prestador encontrado en el archivo sobre el cual se realizara la consulta\n";
   }//fin if

    if(document.getElementById('selector_campo_especifico').value=="" || document.getElementById('selector_campo_especifico').value=="none")
   {
      errores_previo_submit+="Debe seleccionar el campo especifico sobre el cual se realizara la consulta\n";
   }//fin if

   if(document.getElementById('selector_general_o_detallado').value=="" || document.getElementById('selector_general_o_detallado').value=="none")
   {
      errores_previo_submit+="Debe seleccionar si el resultado para la consulta realizada sobre el campo seleccionado sera por conteo agrupado o detallada\n";
   }//fin if
   else if(document.getElementById('selector_general_o_detallado').value=="detallado")
    {
      if(document.getElementById('selector_campo_valor_permitido_1').value=="" || document.getElementById('selector_campo_valor_permitido_1').value=="none")
     {
        errores_previo_submit+="Debe seleccionar el valor permitido del campo seleccionado para la consulta detallada\n";
     }//fin if
    }//fin else if
  }//fin if


 if(errores_previo_submit=="")
 {
  document.getElementById('comprobante_submit').value="enviado";
    document.getElementById('formulario').submit();
  }
  else
  {
    alert(errores_previo_submit);
  }
}//fin function

function conteo_o_detallado()
{
  var selector_general_o_detallado=document.getElementById('selector_general_o_detallado').value;
  if(selector_general_o_detallado=="conteo")
  {
    document.getElementById('sub_titulo_10').style.display='none';
    document.getElementById('mod_campo_valor_permitido_1').style.display='none';
  }//fin if
  else if(selector_general_o_detallado=="detallado")
  {
    document.getElementById('sub_titulo_10').style.display='inline';
    document.getElementById('mod_campo_valor_permitido_1').style.display='inline';
  }//fin else if

}//fin function


$(document).ready(function(){

    $("#selector_campo_especifico").combobox();

})

function download(ruta_descarga_archivo)
{ 
  window.open(ruta_descarga_archivo,'Download');
}

async function reasignar_valores(selector_rango_conteo_param,tipo_periodo_tiempo_param,codigo_periodo_param,year_de_corte_param,identificador_archivo_param)
{
  document.getElementById('selector_rango_conteo').value=selector_rango_conteo_param; 
  mostrar_opciones_correspondientes_al_caso();
  document.getElementById('tipo_periodo_tiempo').value=tipo_periodo_tiempo_param;
  acomodar_tipo_periodo_tiempo(document.getElementById('tipo_periodo_tiempo'));
  document.getElementById('periodo').value=codigo_periodo_param;
  document.getElementById('year_de_corte').value=year_de_corte_param;
  consultar_archivos_subidos_para_periodo_year();
  await sleep(2000);
  document.getElementById('selector_archivos_subidos').value=identificador_archivo_param;
  await sleep(1000);
  consultar_ips_archivo(document.getElementById('selector_archivos_subidos').value);
}//fin function

async function reasignar_valores_consulta_cruzada(selector_all_or_one_vp_param,selector_general_o_detallado_cross_param,numero_campo_1_sel_para_reasignar_param,numero_campo_2_sel_para_reasignar_param,vp_campo_1_sel_para_reasignar_param,vp_campo_2_sel_para_reasignar_param)
{
  document.getElementById('selector_all_or_one_vp').value=selector_all_or_one_vp_param;
  all_or_one_vp();
  document.getElementById('selector_general_o_detallado_cross').value=selector_general_o_detallado_cross_param;

  document.getElementById('campocross_1').value=numero_campo_1_sel_para_reasignar_param;
  document.getElementById('campocross_2').value=numero_campo_2_sel_para_reasignar_param;
  consultar_valor_permitido_campo_cruzado('divcampocrossvp_1','1');
  consultar_valor_permitido_campo_cruzado('divcampocrossvp_2','2');
  await sleep(2000);
  document.getElementById('campocrossvp_1').value=vp_campo_1_sel_para_reasignar_param;
  document.getElementById('campocrossvp_2').value=vp_campo_2_sel_para_reasignar_param;
}//fin function




function consultar_valor_permitido_campo_cruzado(nombre_div_valor_permitido,id_campo_cruzado)
{
    if(document.getElementById("campocross_"+id_campo_cruzado) )
    {
        var selector_campo_cruzado=document.getElementById("campocross_"+id_campo_cruzado).value.trim();
        var copy_selector_campo_cruzado=document.getElementById("copycampocross_"+id_campo_cruzado).value.trim();
         if(selector_campo_cruzado!="" && selector_campo_cruzado!="none" && selector_campo_cruzado!=copy_selector_campo_cruzado)
         {
            document.getElementById("copycampocross_"+id_campo_cruzado).value=selector_campo_cruzado;
            //mod_campo_valor_permitido_1
            ConsultaAJAX_Async("selector_campo_cruzado="+selector_campo_cruzado+"&id_para_selector=campocrossvp_"+id_campo_cruzado,"consulta_valor_permitido_campo_cruzado.php",nombre_div_valor_permitido);
         }//fin if
   }//fin if existe
}//fin function

function adicionar_campo_para_cruce()
{
 numero_campocross_actual=1;//inicia desde uno
 string_valores_seleccionados_campo="";
 string_valores_seleccionados_valor_permitido_campo="";
 while(document.getElementById('campocross_'+numero_campocross_actual) && numero_campocross_actual<=5)
 {
    if(string_valores_seleccionados_campo!=""){string_valores_seleccionados_campo+="ZsepZ";}
    string_valores_seleccionados_campo+=document.getElementById('campocross_'+numero_campocross_actual).value;
    if(document.getElementById('campocrossvp_'+numero_campocross_actual) )
    {
      if(string_valores_seleccionados_valor_permitido_campo!=""){string_valores_seleccionados_valor_permitido_campo+="ZsepZ";}
      string_valores_seleccionados_valor_permitido_campo+=document.getElementById('campocrossvp_'+numero_campocross_actual).value;
    }//fin if
    numero_campocross_actual++;
 }//fin while

 //alert('1: '+string_valores_seleccionados_campo+'\n2:'+string_valores_seleccionados_valor_permitido_campo);

 if(numero_campocross_actual>=2  && numero_campocross_actual<=5)
 {
    ConsultaAJAX_Async("numero_campocross_actual="+numero_campocross_actual+"&str_array_campos="+string_valores_seleccionados_campo+"&str_array_vpcampos="+string_valores_seleccionados_valor_permitido_campo,"selectores_campos_cruzados.php","div_para_consultas_cruzadas");
  }
  else
  {
    alert('El limite es de '+(numero_campocross_actual-1)+' campos para cruzar.');
  }

}//fin function


function all_or_one_vp()
{

  var selector_all_or_one_vp=document.getElementById('selector_all_or_one_vp').value;

  //alert('all_or_one_vp: '+selector_all_or_one_vp);

  
  var cont_divs=1;//inicia en 1
  if(selector_all_or_one_vp=="allvp")
  {

    while(document.getElementById('divcampocrossvp_'+cont_divs))
    {
      document.getElementById('divcampocrossvp_'+cont_divs).style.display='none';
      

      cont_divs++;
    }//fin while

    document.getElementById('sub_titulo_conteo_detallado_cross').style.display='none';
    document.getElementById('selector_general_o_detallado_cross').value='conteo';
    document.getElementById('selector_general_o_detallado_cross').style.display='none';
    
  }//fin if
  else if(selector_all_or_one_vp=="specificvp")
  {
    while(document.getElementById('divcampocrossvp_'+cont_divs))
    {
      document.getElementById('divcampocrossvp_'+cont_divs).style.display='inline';
      cont_divs++;
    }//fin while
    document.getElementById('sub_titulo_conteo_detallado_cross').style.display='inline';
    document.getElementById('selector_general_o_detallado_cross').style.display='inline';
  }//fin else if

}//fin function

/*
$(document).ready(function(){

    $("#campocross_1").combobox();
    $("#campocross_2").combobox();

})
*/

