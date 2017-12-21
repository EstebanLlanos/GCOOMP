<?php /* Smarty version Smarty-3.1.17, created on 2015-01-15 09:09:22
         compiled from "carga_val_2463_ERC.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1087053ed4cc16e14e2-10848906%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '644b4a22dd9fb67ef152f627e1059c91894bd58d' => 
    array (
      0 => 'carga_val_2463_ERC.html.tpl',
      1 => 1421319154,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1087053ed4cc16e14e2-10848906',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53ed4cc1c9a3b0_90756422',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'campo_periodo' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ed4cc1c9a3b0_90756422')) {function content_53ed4cc1c9a3b0_90756422($_smarty_tpl) {?><!DOERCYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="carga_val_2463_ERC.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="carga_val_2463_ERC.css" rel="stylesheet" />
       <title>Carga y Validacion de archivos para Enfermedad renal cronica 2463</title>
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
						<h5>Formulario de carga y validacion de documentos 2463 para Enfermedad Renal Cronica</h5>
						</td>
						</tr>
						
						<tr>
						
						<td style="text-align:left;width:15.05%;">
						
							<table>
							
								<tr>
								<td style="text-align:left;">
								Nombre Prestador:
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
								
								<tr>
								<td style="text-align:left;">Numero de remisi&oacuten:</td>
								</tr>
								<tr>
								<td style="text-align:left;"><input type='text' id='numero_de_remision' name='numero_de_remision'  class='campo_azul' placeholder='Numero de remision 2463' onchange="cuando_se_escribe_el_nombre_del_archivo();validar_antes_seleccionar_archivos();" onkeyup="cuando_se_escribe_el_nombre_del_archivo();validar_antes_seleccionar_archivos();" onkeypress="return isNumberKey(event)" maxlength="2" /></td>
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
								<td style="text-align:left;">A&ntildeo de corte:</td>
								</tr>
								<tr>
								<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)" onchange="validar_antes_seleccionar_archivos();" onkeyup="validar_antes_seleccionar_archivos();"/> </td>
								</tr>	
								
								<tr>
								<td style="text-align:left;">Fecha/Periodo de corte:</td>
								</tr>
								<tr>
								<td style="text-align:left;">
								<?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>

								</td>
								</tr>
							
							</table>
						</td>
						
						<td style="text-align:right;width:40.05%;">
							<table id='tabla_seleccion_archivos' style='display:inline;'>
							
								<!--hidden validar archivo, (debe contener la sigla)-->
								<input type='hidden' name='ERC_hidden' id='ERC_hidden' value='' />
								<!--fin hidden validar archivo-->
								
								
								<!--ARCHIVO ERC-->
								<tr>
								<td style="text-align:left;width:25%;">
								<h5>2463 ENF. RENAL <BR> CRONICA:</h5>
								</td>
								
								<td style="position:relative;text-align:left;width:25%;">
								
								<table>
								<tr>
								
								<td style="position:absolute;left:-65%;top:5%;width:5%;">
								<div class="upload"><input type="file" id='2463_ERC_file' name='2463_ERC_file' style="cursor:pointer;" onchange="obtener_nombre_file_upload('2463_ERC_file','nombre_archivo_2463');verificar_nombre_archivo(this.value,'ERC','nombre_archivo_2463');" onclick="obtener_nombre_file_upload('2463_ERC_file','nombre_archivo_2463');verificar_nombre_archivo(this.value,'ERC','nombre_archivo_2463');" /></div>
								</td>
								
								<td style="position:absolute;left:-50%;top:15%;">
								<div id='nombre_archivo_2463'></div>
								</td>
								
								</tr>
								</table>
								
								</td>
								</tr>
								<!--FIN ARCHIVO ERC-->
						
							</table>
						</td>
						
						</tr>	
						
						<input type='hidden' name='accion' id='accion' value='validar' />
						
						<!--botones para cargar los archivos y validar o limpiar el formulario-->
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Cargar Y Validar" onclick="cargarERC();" class="btn btn-success color_boton"  /> 
						<input type="reset" value="Limpiar Campos" class="btn btn-success color_boton" onclick='limpiar_files();' />
						</td>
						</tr>
						<!--fin botones para cargar los archivos y validar o limpiar el formulario-->
						
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
