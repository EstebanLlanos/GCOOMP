<?php /* Smarty version Smarty-3.1.17, created on 2017-12-14 10:25:37
         compiled from "verif_dupl_inter_afil.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:14210664155a328af5be2d28-86365250%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd19dbcf48d18cea141226b5ed45db3439ba6456' => 
    array (
      0 => 'verif_dupl_inter_afil.html.tpl',
      1 => 1513265133,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '14210664155a328af5be2d28-86365250',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5a328af5ce7898_70017596',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'mensaje_proceso' => 1,
    'html_div_procesos' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a328af5ce7898_70017596')) {function content_5a328af5ce7898_70017596($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="verif_dupl_inter_afil.js?v=0.11"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="verif_dupl_inter_afil.css" rel="stylesheet" />
       <title>Verificacion Duplicados Internos En Tabla Afiliados MP</title>
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
					<td style="text-align:center;vertical-align:text-top;width:20%;min-width: 270px;">
					<div id="logo">
						<img id="imgLogo" src="../assets/imagenes/logo_gios.png" />
					</div>
					</td> 
					<td>
					<form name="formulario" id="formulario" action="" method="post" enctype= "multipart/form-data">
						<table>

						<tr>
							<td style="text-align:left;">
								<h4  style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Verificador de Afiliados Duplicados dentro de la misma Tabla</h1>
							</td>
						</tr>


						<tr>
							<td style='text-align:left;'>
								<h5  style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Iniciar Extraccion</h5>
							</td>
						</tr>

						<tr>
							<td style='text-align:left;' >
								<select id='iniciar' name='iniciar' class='campo_azul'>
									<option value=''>NO</option>
									<option value='SI'>SI</option>
								</select>
							</td>
						</tr>

						<!--
						<tr>
						<td><label>Nombre archivo a leer o Ruta Completa</label></td>
						<td>
						<input type='text' id='ruta_leer' name='ruta_leer' value='$ruta_archivo_leer' />
						</td>
						</tr>
						-->

						<tr>
							<td style='text-align:left;' align='center' >
								<input type='submit' id='enviar' name='enviar' value='INICIAR' class='btn btn-success color_boton'/>
							</td>
						</tr>
						
						<tr>
						<td style="text-align:center;" colspan='100'>
							<div  id="mensaje">
								<?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>

							</div>
							
							<div id='mensaje_div' style='text-align:center;'></div>
							<div id='resultado_definitivo' style='text-align:center;'></div>

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


						<tr>
						<td style="text-align:center;" colspan='100' align='center' ><div id='mensaje_estado'></div></td>
						</tr>

						<tr>
						<td style="text-align:center;" colspan='100' align='center' ><div id='mensaje_estado_particion'></div></td>
						</tr>

						<?php echo $_smarty_tpl->tpl_vars['html_div_procesos']->value;?>

						
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
