<?php /* Smarty version Smarty-3.1.17, created on 2016-05-26 09:13:25
         compiled from "fixer_VIH.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:881154a5e3791b5c85-91011278%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '22c544b627c871c69e9901b8cb13fa490269b6e9' => 
    array (
      0 => 'fixer_VIH.html.tpl',
      1 => 1464250020,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '881154a5e3791b5c85-91011278',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_54a5e379324fd1_20003318',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'proveniente_de_prestador_o_eapb' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'campo_periodo' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_54a5e379324fd1_20003318')) {function content_54a5e379324fd1_20003318($_smarty_tpl) {?><!DOVIHYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="fixer_VIH.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="fixer_VIH.css" rel="stylesheet" />
       <title>Reparacion de archivos para VIH-SIDA 4725</title>
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
					<form name="formulario" id="formulario" action="" method="post" enctype= "multipart/form-data">
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Formulario de Reparacion de documentos R4725 para VIH-SIDA</h4>
						</td>
						</tr>
						
						<tr>
						
						<td style="text-align:left;width:7.05%;">
						
							<table>
							    
							    <?php echo $_smarty_tpl->tpl_vars['proveniente_de_prestador_o_eapb']->value;?>

							
								<tr>
								<td style="text-align:left;">
								<h5 id='sub_titulo_entidad_1' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Entidad Que Efect&uacutea el Cargue:</h5>
								    <h5 id='sub_titulo_entidad_1_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;">Entidad a La Cual se Efect&uacutea el Reporte:</h5>
								    <h5 id='sub_titulo_entidad_3_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;position: relative;top:50%;">Se Reporta al Ministerio de Salud.</h5>
								</td>
								</tr>
								
								<tr>
								<td style="text-align:left;">
								<div id='entidad_1'><?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>
</div>
								</td>
								</tr>
								
								<tr>
								<td style="text-align:left;">
								    <h5 id='sub_titulo_entidad_2' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Entidad a La Cual se Efect&uacutea el Reporte:</h5>
								    <h5 id='sub_titulo_entidad_2_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;">Entidad Que Efect&uacutea el Cargue:</h5>
								    <h5 id='sub_titulo_entidad_4_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;position: relative;top:50%;">Se Reporta al Ministerio de Salud.</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;">
								<div id='entidad_2'><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</div>						
								</td>
								</tr>
								
								<tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;" id='titulo_numero_de_remision'>Numero de Remisi&oacuten:</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;">
								    <div id='separador_numero_de_remision'>
								    <input type='text' style='width:230px;height:30px;' id='numero_de_remision' name='numero_de_remision'  class='campo_azul' placeholder='Numero de remision VIH' onchange="cuando_se_escribe_el_nombre_del_archivo();validar_antes_seleccionar_archivos();" onkeyup="cuando_se_escribe_el_nombre_del_archivo();validar_antes_seleccionar_archivos();" onkeypress="return isNumberKey(event)" maxlength="2"/>
								    </div>
								</td>
								<td><p id='error_nombre_archivo'></p></td>
								</tr>
								
								<!--
								<tr>
								<td style="text-align:left;">Fecha de remisi&oacuten del archivo:</td>
								</tr>
								<tr>
								<td style="text-align:left;"><input type="text" name="fecha_remision" id="fecha_remision" placeholder="clic aqui para seleccionar una fecha" class='campo_azul' onchange="validar_antes_seleccionar_archivos();" onkeyup="validar_antes_seleccionar_archivos();" /></td>
								</tr>
								-->
								
								<tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">A&ntildeo de corte:</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;"><input type='text' style='width:230px;height:30px;' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)" onchange="validar_antes_seleccionar_archivos();" onkeyup="validar_antes_seleccionar_archivos();"/> </td>
								</tr>	
								
								<tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Fecha/Periodo de corte:</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;">
								<?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>

								</td>
								</tr>
							
							</table>
						</td>
						
						<td style="text-align:left;width:20.05%;">
						    <table id='tabla_selector_y_resultados' style='display:inline;'>
							<tr>
							    <td>
							    <table id='tabla_seleccion_archivos' style='display:inline;'>
							    
								    <!--hidden validar archivo, (debe contener la sigla)-->
								    <input type='hidden' name='SIDA_hidden' id='SIDA_hidden' value='' />
								    <!--fin hidden validar archivo-->
								    
								    
								    <!--ARCHIVO VIH-->
								    <tr>
								    <td style="text-align:left;width:150px;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">R4725 VIH-SIDA:</h5>
								    </td>
								    
								    <td style="display:block;position:relative;text-align:left;">
								    
								    <table style="display: block;">
								    <tr style="display: block;">
								    
								    <td >
								    <div class="upload"><input type="file" id='r4725_file' name='r4725_file' style="cursor:pointer;" onchange="obtener_nombre_file_upload('r4725_file','nombre_archivo_r4725');verificar_nombre_archivo(this.value,'VIH','nombre_archivo_r4725');" onclick="obtener_nombre_file_upload('r4725_file','nombre_archivo_r4725');verificar_nombre_archivo(this.value,'VIH','nombre_archivo_r4725');" /></div>
								    </td>
								    
								    <td style="position:absolute;left:50px;top:15%;">
								    <div style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;" id='nombre_archivo_r4725'></div>
								    </td>
								    
								    </tr>
								    </table>
								    
								    </td>
								    </tr>
								    <!--FIN ARCHIVO VIH-->
						    
							    </table>
							</td>
							</tr>
							<tr><td><br></br></td></tr>
							<tr>
							    <td>
								<div  id="mensaje">
									<?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>

								</div>  
							    </td>
							</tr>
						    </table>
						</td>
						
						</tr>	
						
						<input type='hidden' name='accion' id='accion' value='validar' />
						
						<!--botones para cargar los archivos y validar o limpiar el formulario-->
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Reparar" onclick="cargarVIH();" class="btn btn-success color_boton"  /> 
						<input type="reset" value="Limpiar Campos" class="btn btn-success color_boton" onclick='limpiar_files();' />
						</td>
						</tr>
						<!--fin botones para cargar los archivos y validar o limpiar el formulario-->
						
						<tr>
						<td style="text-align:center;" colspan='100'>
							

							<div id="grilla" style="display:inline"> 	</div>
							
							<div id='advertencia'></div>
							
							<!--Ventana informacion exito-->
							<div id='div_mensaje_exito' class="sec2" style="width:90%;display:none;">

								<div class="alert-success alert-block alert-success fade in">

									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_exito' class="alert-heading">&iexclSe ha reparado el archivo!</h4>
									<p id='parrafo_mensaje_exito'></p>

								</div>

							</div>
							
							<!--Ventana informacion error-->
							<div id='div_mensaje_error' class="sec2" style="width:90%;display:none;">

								<div class="alert-success alert-block alert-error fade in">

									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_error' class="alert-heading">&iexclSe ha reparado el archivo!</h4>
									<p id='parrafo_mensaje_error'></p>

								</div>

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
