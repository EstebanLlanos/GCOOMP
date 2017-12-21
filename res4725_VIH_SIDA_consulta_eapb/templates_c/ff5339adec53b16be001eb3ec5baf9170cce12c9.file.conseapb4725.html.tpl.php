<?php /* Smarty version Smarty-3.1.17, created on 2014-12-05 02:35:57
         compiled from "conseapb4725.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:674753b410e0119c10-11301850%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff5339adec53b16be001eb3ec5baf9170cce12c9' => 
    array (
      0 => 'conseapb4725.html.tpl',
      1 => 1405712162,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '674753b410e0119c10-11301850',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53b410e0166ff8_38499526',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_eapb' => 1,
    'campo_dpto' => 1,
    'campo_mpio' => 1,
    'campo_prestador' => 1,
    'campo_fechas_corte' => 1,
    'mensaje_proceso' => 1,
    'mostrarResultado' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53b410e0166ff8_38499526')) {function content_53b410e0166ff8_38499526($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="conseapb4725.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="conseapb4725.css" rel="stylesheet" />
       <title>Consulta EAPB 4725</title>
       <link rel="icon" href="../assets/imagenes/logo_gioss_fav.ico" />
    </head>
    <body>
        <div id="contenedor" >
        <table>
				<tr>
					<td>    
						<div id="cabezera">           
							<table>
							<tr>
							<td>
							<div id="titulo" style="text-align: left;padding-left:20px;">
							<h4>GIOSS - Sistema de informaci&oacuten</h4>
							</div>
							</td>
							<td>
							<div id='menu_div'><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</div>
							</td>
							<td id='nombre'>
							<?php echo $_smarty_tpl->tpl_vars['nombre']->value;?>

							</td>
							<td>
							<div id="clockbox"></div>
							</td>
							</tr>
							</table>
						</div>
					</td>
				</tr> 
		 <tr>
		 <td> 
         <div id="modPrincipal">
			<table>
				<tr>
					<td style="text-align:center;vertical-align:text-top;width:20%;">
					<div id="logo">
						<img id="imgLogo" src="../assets/imagenes/logo_gios.png" />
					</div>
					</td> 
					<td>
					<form name="formulario" id="formulario"  method="post">
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h5 style="color:blue;">Interfaz para la consulta del estado de informaci&oacuten de los archivos VIH 4725(EAPB):</h5>
						<br>
						&#32
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						Nombre EAPB:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>

						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_area_geografica" value="1" onclick="mostrar_metodos_busqueda();" />Por area geografica</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_prestador" value="2" onclick="mostrar_metodos_busqueda();" />Por prestador asociado</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_periodo" value="3" onclick="mostrar_metodos_busqueda();" />Por periodo / fecha </td>
						</tr>
						
						<tr>
						<td>
						<br>
						&#32
						</td>
						</tr>
						
						<!--AREA GEOGRAFICA-->
						<tr>
						<td style="text-align:left;">
						
						<div id='mostrar_area_geografica' style="display:none;" >
						<table>
						<tr>
						<td style="text-align:left;">
						Area geografica:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_dpto']->value;?>
 <div id='mostrar_mpio' style='display:none;'><?php echo $_smarty_tpl->tpl_vars['campo_mpio']->value;?>
</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--PRESTADORE-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_prestador' style="display:none;" >
						<table>
						
						<tr>
						<td style="text-align:left;">
						<b>Prestador:</b><br>
						<i>(Seleccione un prestador para obtener resultados sobre ese prestador o deje sin seleccionar para traer <br>
						informaci&oacuten sobre todos los prestadores asociados a la EAPB)</i>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>

						</td>
						</tr>
						
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--FECHA DE REMISION-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_fecha_rem' style="display:none;">
						<table>
						<tr>
						<td style="text-align:left;">Fecha: </td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)"  />
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_mes' style='display:none;'><?php echo $_smarty_tpl->tpl_vars['campo_fechas_corte']->value;?>
</div>
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<div id='div_selector_numeros_de_remision'>						
						</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						
						
						
						
						<input type="hidden" id="consecutivo" name="consecutivo" value="" />
												
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Consultar" onclick="enviar();" class="btn btn-success color_boton"  /> <input type="reset" value="limpiar" class="btn btn-success color_boton"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
							<div  id="mensaje">
								<label id="msj"><?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>
</label>
							</div>  

							<div id="grilla" style="display:none;"><?php echo $_smarty_tpl->tpl_vars['mostrarResultado']->value;?>
</div>
						</td>
						</tr>
						
						</table>
					</form>
					</td>
				</tr>	
			</table>
		 </div>
         </td>
		 </tr>   
		<tr>		 
        <td colspan='100' style="text-align:right;">    
                <div id="footer">
                    <h6>Sistema de informaci&oacuten Gios</h6>
                </div>                
            
         </td>
		 </tr>  
		</table>
		
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="tituloVentana" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times</button>
				<h3 id="tituloVentana"></h3>
			</div>
			<div class="modal-body" id="mensajeVentana">

			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
			</div>
		</div> 
            
    </body>
</html><?php }} ?>
