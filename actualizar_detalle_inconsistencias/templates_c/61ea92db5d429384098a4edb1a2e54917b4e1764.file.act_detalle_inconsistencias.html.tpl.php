<?php /* Smarty version Smarty-3.1.17, created on 2015-06-25 15:56:44
         compiled from "act_detalle_inconsistencias.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:23404537e5ff2878304-49702507%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '61ea92db5d429384098a4edb1a2e54917b4e1764' => 
    array (
      0 => 'act_detalle_inconsistencias.html.tpl',
      1 => 1432056938,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '23404537e5ff2878304-49702507',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_537e5ff32f62d1_95061800',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_537e5ff32f62d1_95061800')) {function content_537e5ff32f62d1_95061800($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="act_detalle_inconsistencias.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="act_detalle_inconsistencias.css" rel="stylesheet" />
       <title>Actualizar info. EAPB entidades administradoras</title>
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
						<h1>LLenado de datos para detalles de inconsistencias</h1>
						</td>
						</tr>
						
						
						<tr>
						<td style="text-align:left;">
						<p>Seleccione el detalle de inconsistencias a subir:</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_3374" value="3374" class='campo_azul' /> RIPS 3374
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_4505" value="4505" class='campo_azul' /> PyP 4505
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_r4725" value="4725" class='campo_azul' /> VIH-SIDA r4725
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_0247" value="0247" class='campo_azul' /> CANCER 0247
						</td>
						</tr>						
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_2463" value="2463" class='campo_azul' /> ERC 2463
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_0123" value="0123" class='campo_azul' /> HF - Hemofilia (HF) 0123
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="tipo_archivo" id="archivo_1393" value="1393" class='campo_azul' /> AR - Artritis Reumatoide (AR) 1393
						</td>
						</tr>
						
						
						
						<tr>
						<td style="text-align:left;">
						<p>El archivo tomara los campos separado por comas(,) para indicar las columnas:<br>
						por lo tanto los campos deber&aacuten estar organizados de acuerdo a la forma en que inserto los campos (NOTA: un numero diferente de comas evitara que se suba el archivo)<br>	
						primero el tipo de inconsistencia, luego el grupo de inconsistencia, el codigo del detalle de la inconsistencia y por ultimo la descripcion de la inconsistencia <br>
                        <b>(el algoritmo de subida se encargara de poner los codigos dentro con la descripci&oacuten)</b>. <br>
						codigo_tipo_inconsistencia, codigo_grupo_inconsistencia, codigo_detalle_inconsistencia, descripcion inconsistencia
						</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="file" id='archivo_info' name='archivo_info'  />
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="submit" value="Actualizar" class="btn btn-success color_boton" />
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
