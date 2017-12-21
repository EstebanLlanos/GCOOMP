<?php /* Smarty version Smarty-3.1.17, created on 2015-04-09 08:57:30
         compiled from "consulta_reporte_obligatorio0247.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1528453bfcb5d961330-19711546%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '190eae02336be43649855b4f8c812d63784dcb8b' => 
    array (
      0 => 'consulta_reporte_obligatorio0247.html.tpl',
      1 => 1428432888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1528453bfcb5d961330-19711546',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53bfcb5e440d95_84125127',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_periodo' => 1,
    'campo_eapb' => 1,
    'mensaje_proceso' => 1,
    'mostrarResultado' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53bfcb5e440d95_84125127')) {function content_53bfcb5e440d95_84125127($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="consulta_reporte_obligatorio0247.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="consulta_reporte_obligatorio0247.css" rel="stylesheet" />
       <title>consulta_reporte_obligatorio 0247</title>
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
					<form name="formulario" id="formulario" action="" method="post">
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h5 style="color:blue;">Consultar el reporte obligatorio de los archivos CANCER 0247:</h5>
						<br>
						&#32
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<b>Tipo de consulta:</b>
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						Detallado <input type="radio" id='tipo_consulta_radio_detallado' name='tipo_consulta_radio' value='detallado' checked='true'/>
						Consolidado <input type="radio" id='tipo_consulta_radio_consolidado' name='tipo_consulta_radio' value='consolidado' />						
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Estado de la informaci&oacuten:</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<select id='selector_estado_info' name='selector_estado_info' class='campo_azul' onchange="consultar_fecha_generacion_usuario();" >
						<option value='none'>Seleccione el estado de la informaci&oacuten a buscar</option>
						<option value='validada'>Informaci&oacuten validada</option>
						<option value='rechazada'>Informaci&oacuten rechazada</option>
						</select>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">A&ntildeo de corte(detallado)/A&ntildeo Generaci&oacuten Reporte(Consolidado):</td>
						</tr>
						<tr>
						<td style="text-align:left;font-size: small;">El a&ntildeo de corte corresponde a la fecha indicada en los archivos de orig&eacuten;
						<br>
						    el a&ntildeo de generaci&oacuten corresponde a la fecha en la que se consolido el reporte obligatorio</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' onchange='escribiendo_year_corte();consultar_fecha_generacion_usuario();' onkeyup='escribiendo_year_corte();consultar_fecha_generacion_usuario();' onkeypress="return isNumberKey(event)"/> </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Periodo:</td>
						</tr>
						<tr>
						<td style="text-align:left;position:relative;"><?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>
<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%" onchange="consultar_fecha_generacion_usuario();"></div></td>
						</tr>
						
						
						
						<tr>
						<td style="text-align:left;">Nombre EAPB:</td>
						</tr>
						<tr>
						<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</td>
						</td>
						</tr>
						
						<!--
						<tr>
						<td style="text-align:left;">Fecha generaci&oacuten, hora generaci&oacuten, usuario consolido:</td>
						</tr>
						<tr>
						<td style="text-align:left;font-size: small">(Diligencie los campos anteriores)</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						    <div id="contenedor_selector_archivo_pfgu">
							<select id="selector_archivo_por_fecha_generacion_usuario" name="selector_archivo_por_fecha_generacion_usuario" class="campo_azul" size="2" style="width: 600px;">
							    <option value="none">Diligencie los campos anteriores</option>
							    <option value="none">Seleccione el archivo por fecha generacion y/o usuario</option>
							</select>
						    </div>
						</td>
						</tr>
						-->
								
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
							
							<!--Ventana informacion exito-->
							<div id='div_mensaje_exito' class="sec2" style="width:90%;display:none;">
	
								<div class="alert-success alert-block alert-success fade in">
	
									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_exito' class="alert-heading" style="text-align: center;">Mensaje:</h4>
									<p id='parrafo_mensaje_exito' style="text-align: center;"></p>
	
								</div>
	
							</div>
							
							<!--Ventana informacion error-->
							<div id='div_mensaje_error' class="sec2" style="width:90%;display:none;">
	
								<div class="alert-success alert-block alert-error fade in">
	
									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_error' class="alert-heading" style="text-align: center;">&iexclERROR!</h4>
									<p id='parrafo_mensaje_error' style="text-align: center;"></p>
	
								</div>
	
							</div>

							<div id="grilla" style="display:none;"> <?php echo $_smarty_tpl->tpl_vars['mostrarResultado']->value;?>
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
