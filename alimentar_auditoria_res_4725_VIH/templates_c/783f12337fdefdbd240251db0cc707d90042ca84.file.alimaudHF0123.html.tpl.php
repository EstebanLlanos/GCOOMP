<?php /* Smarty version Smarty-3.1.17, created on 2018-03-12 12:05:22
         compiled from "alimaudHF0123.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16349773845aa19ae1174698-95521254%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '783f12337fdefdbd240251db0cc707d90042ca84' => 
    array (
      0 => 'alimaudHF0123.html.tpl',
      1 => 1520874237,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16349773845aa19ae1174698-95521254',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5aa19ae1191c40_06243420',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_fechas_corte' => 1,
    'campo_prestador' => 0,
    'campo_eapb' => 1,
    'campo_periodo' => 1,
    'mensaje_proceso' => 1,
    'resultado_definitivo' => 1,
    'mostrarResultado' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5aa19ae1191c40_06243420')) {function content_5aa19ae1191c40_06243420($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>
	<script src="../librerias_externas/jquery_y_ui_actualizado/jquery-ui.js"></script>
	<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">
        <script type="text/javascript" src="alimaudHF0123.js?v=1.01"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="alimaudHF0123.css" rel="stylesheet" />
       <title>Alimentar Soporte Auditoria HEMOFILIA 0123</title>
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
					<td style="text-align:left;">
					<form action="" method="post" id="reporte_obligatorio" name="reporte_obligatorio" enctype= "multipart/form-data">
					<table>
					    <tr>
						<td style="text-align:left;" colspan=2>
						    <h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Alimentar Tabla Auditoria Hemofilia 0123:</h4>
						</td>
					    </tr>
					    

					    <tr>
						<td style="text-align:left;" colspan=2>						    
						    <h6 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Se alimentara la tabla de usuarios reportados hemofilia requerida por el proceso de auditoria, tomara lso datos correspondientes al ultimo consolidado  generado por el proceso de reporte obligatorio</h6></td>
						    <input type='hidden' id='selector_estado_info' name='selector_estado_info' value='validada'/> 
						</select>
						</td>
					    </tr>
					    
					    <tr>
						<td>
						    <table>
							
							<!--
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">A&ntildeo de corte:</h5>
							</td>
							</tr>
							<tr>
							<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' onchange='escribiendo_year_corte();' onkeyup='escribiendo_year_corte();' onkeypress="return isNumberKey(event)"/> <?php echo $_smarty_tpl->tpl_vars['campo_fechas_corte']->value;?>
</td>
							</tr>
						-->
							
							
							
							<!--
							<tr>					
							<td style="text-align:left;">Nombre Prestador:</td>					
							</tr>
							<tr>					
							<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>
</td><td style="text-align:left;">					
							</tr>
							-->
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo a Procesar:</h5>
							</td>
							</tr>
							<tr>
								<td style="text-align:left;">
									<input id="periodo_indicado" name="periodo_indicado" style="width:230px;height:30px;" class="campo_azul" placeholder="AAAA" maxlength="4" onkeypress="return isNumberKey(event)" onchange="validar_antes_seleccionar_archivos();" onkeyup="validar_antes_seleccionar_archivos();" type="text">
								</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Nombre EAPB:</h5>
							</td>
							</tr>
							<tr>
							<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</td>
							</td>
							</tr>
							
							<!--
							<tr>
							<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Tipo Periodo:</h5></td>
							</tr>
							<tr>
							<td style="text-align:left;position:relative;">
							    <select id='tipo_tiempo_periodo' name='tipo_tiempo_periodo' class='campo_azul' onchange="cambio_tipo_tiempo_periodo();">
								<option value='anual' >Anual</option>
								<option value='semestral' >Semestral</option>
								<option value='mensual' selected>Mensual</option>
							    </select>
							</td>
							</tr>

						-->
							<!--
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo:</h5>
							</td>
							</tr>
							<tr>
							    <td style="text-align:left;position:relative;">
								<div id='div_selector_periodo'><?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>
</div>
								<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%">						    
								</div>
							    </td>
							</tr>
						-->

							
							
							<tr>
							<td style="text-align:left;"><input type="button" value="Generar" class="btn btn-success color_boton" onclick="enviar_formulario();"/> <!--<input type="reset" value="limpiar" class="btn btn-success color_boton"/></td>-->
							</tr>
					
						    </table>
						    <td>
							<div  id="mensaje">
	
								<label id="msj"><?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>
</label>
								<?php echo $_smarty_tpl->tpl_vars['resultado_definitivo']->value;?>

							</div>
						    </td>
						</td>
					    </tr>
				
					    <tr>
						<td style="text-align:left;">
							
							
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
	
	
							<div id="grilla" style="display: <?php echo $_smarty_tpl->tpl_vars['mostrarResultado']->value;?>
"> 
	
							
							
							 
	
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
		    <div id="medidor_ram" name="medidor_ram" style="font-size: xx-small;color: white;"></div>
                </div>                
            
         </td>
		 </tr>  
		</table>
        
		<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="tituloVentana" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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
