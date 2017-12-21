<?php /* Smarty version Smarty-3.1.17, created on 2014-11-28 10:53:27
         compiled from "cargaRIPS.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6025534d3fee85fb03-37709607%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd68e41201cc5c6692060ce7ebad204e52927bf71' => 
    array (
      0 => 'cargaRIPS.html.tpl',
      1 => 1417019388,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6025534d3fee85fb03-37709607',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_534d3feed7c5b9_07596375',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'hidden_user' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'campo_dpto' => 1,
    'campo_mpio' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_534d3feed7c5b9_07596375')) {function content_534d3feed7c5b9_07596375($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="cargaRIPS.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="cargaRIPS.css" rel="stylesheet" />
       <title>Carga y Validaci&oacuten de RIPS</title>
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
						
						<td style="text-align:left;width:15.05%;">
						
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h5>Diligencie todos los campos para poder subir los archivos RIPS</h5>
						<?php echo $_smarty_tpl->tpl_vars['hidden_user']->value;?>

						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						Nombre Entidad Reportadora:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>

						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Nombre EAPB:</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
							
						</td>
						</tr>
						
						<!--
						<tr>
						<td style="text-align:left;">Nombre Archivo:</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type='text' id='nombre_archivo_rips' name='nombre_archivo_rips'  class='campo_azul' placeholder='Numero de remision' onchange="cuando_se_escribe_el_numero_de_remision();" onkeyup="cuando_se_escribe_el_numero_de_remision();" /></td>
						
						<td><p id='error_nombre_archivo_rips'></p></td>
						</tr>
						-->
						
						<tr><td style="text-align:left;">Seleccione el tipo de archivo RIPS a validar</td></tr>
						<tr>
						    <td style="text-align:left;">
							<select id="tipo_archivo_rips" name="tipo_archivo_rips" class="campo_azul" onchange="mostrar_selectores_geograficos();">							    
							    <option value="ips">RIPS proveniente de Prestador</option>
							    <option value="eapb">RIPS proveniente de EAPB</option>
							</select>
							<input type="hidden" id='ultimos_archivos_subido' name='ultimos_archivos_subido' value=''/>
							<input type="hidden" id='nombre_archivo_rips' name='nombre_archivo_rips' value=''/>
						    </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Fecha de remisi&oacuten(mm/dd/aaaa):</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="text" name="fecha_remision" id="fecha_remision" placeholder="clic aqui para seleccionar una fecha" class='campo_azul'  /></td>
						</tr>
						
						<tr>
						    <td>
							<table id='campos_filtro_geografico' style="display:none;">							    
							    <tr>
							    <td style="text-align:left;">Filtrar por Departamento:</td>
							    </tr>
							    <tr>
							    <td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_dpto']->value;?>
</td>
							    </tr>
							    
							    <tr>
							    <td style="text-align:left;">Filtrar por Municipio:</td>
							    </tr>
							    <tr>
							    <td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_mpio']->value;?>
</td>
							    </tr>
							</table>
						    </td>
						</tr>
						
						<input type='hidden' name='consecutivo' id='consecutivo' value='' />
						
						<input type='hidden' name='date_ruta' id='date_ruta' value='' />
						
						</table>
						</td>
						
						<td style="text-align:left;width:40.05%;">
						<table id='tabla_seleccion_archivos' style='display:inline;'>
						
						<!--Archivos RIPS -->
						<tr>
						<td style="text-align:left;width:25%;">
						<h5>ARCHIVOS RIPS:</h5>
						</td>
						
						<td style="display:block;position:relative;text-align:left;width:25%;">
						
						<table style="display:block;">
						<tr style="display:block;">
						
						<td style="width:5%;">
						<div class="upload"><input type="file" id='uploader_rips' name='uploader_rips[]' style="cursor:pointer;" onchange="obtener_nombre_file_upload('uploader_rips','nombres_archivo_a_cargar');" onclick="obtener_nombre_file_upload('uploader_rips','nombres_archivo_a_cargar');" multiple=""/></div>
						</td>
												
						</tr>
						</table>
						</td>
						</tr>
						
						<tr>
						    <td>
							<div id='nombres_archivo_a_cargar'></div>
						    </td>
						    <td>
							<div id='archivos_cargados_en_server'></div>
						    </td>
						</tr>
						
						<tr>
						    <td>
						    <input type="button" id='boton_cargar_archivos' name='boton_cargar_archivos' class="btn btn-success color_boton" value='Subir Archivos' onclick='sube_archivos();'/>
						    </td>
						</tr>
						
						<tr>
						    <td colspan='2'>
						    <label id='error_upload' name='error_upload' style="color:red;"></label>
						    </td>
						</tr>
						
						<tr>
						    <td colspan='2'>
						    <label id='progress_upload' name='progress_upload' style="color:blue;"></label>
						    </td>
						</tr>
						
						</table>
						</td>
						
						
						<input type='hidden' name='accion' id='accion' value='validar' />
						
						<!--botones para cargar los archivos y validar o limpiar el formulario-->
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Validar RIPS" onclick="cargarRIPS();" class="btn btn-success color_boton"  /> 
						<input type="reset" value="Limpiar Campos" class="btn btn-success color_boton" onclick='limpiar_files();' />
						</td>
						</tr>
						
						<tr>
						<td style="text-align:center;" colspan='100'>
							<div  id="mensaje">
								<?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>

							</div>  

							<div id="grilla" style="display:inline"> 	</div>
							
							<!--Ventana informacion exito-->
							<div id='div_mensaje_exito' class="sec2" style="width:90%;display:none;">

								<div class="alert-success alert-block alert-success fade in">

									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_exito' class="alert-heading">&iexclCargue completo!</h4>
									<p id='parrafo_mensaje_exito'></p>

								</div>

							</div>
							
							<!--Ventana informacion error-->
							<div id='div_mensaje_error' class="sec2" style="width:90%;display:none;">

								<div class="alert-success alert-block alert-error fade in">

									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_error' class="alert-heading">&iexclErrores en la validaci&oacuten de los archivos!</h4>
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
