<?php /* Smarty version Smarty-3.1.17, created on 2018-03-07 14:17:15
         compiled from "audsoporclinicos_HF.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18031643995a7b08ffe1efc8-93844277%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '442a5329b8857eae1c7981a14a6195865d8c934c' => 
    array (
      0 => 'audsoporclinicos_HF.html.tpl',
      1 => 1520450223,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18031643995a7b08ffe1efc8-93844277',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5a7b090002dcc8_06962955',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'info_entidad' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a7b090002dcc8_06962955')) {function content_5a7b090002dcc8_06962955($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="audsoporclinicos_HF.js?v=1.12"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="audsoporclinicos_HF.css?=1.1" rel="stylesheet" />
       <title>Auditoria Hemofilia Contra Soportes Clinicos</title>
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
						</td>
						</tr>


						<table>

							<tr>
							<td style="text-align:left;">
							<h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">MODULO ACCESO PARA SELECCI&Oacute;N ARCHIVOS DE AUDITORIA DE INFORMACI&Oacute;N</h4>
							</td>
							</tr>

							<tr>
							<td style="text-align:left;">
							<?php echo $_smarty_tpl->tpl_vars['info_entidad']->value;?>
							
							</td>
							</tr>


							<tr>
							<td style="text-align:left;">
								<table style='text-align:left;'>
								<tr>
								<td style='text-align:left;width:250px;'>
								<input type='text' value='PERIODO' class='campo_azul' readonly='true'>
								</td>
								<td style='text-align:left;'>
								<input id='anio_auditoria' name='anio_auditoria'  class='campo_azul' placeholder='AAAA' maxlength='4' onkeypress='return isNumberKey(event)' type='text'> 
								</td>
								</tr>
								</table>
							</td>
							</tr>


							

						    <tr>
								<td style="text-align:left;vertical-align: middle;">
									<div style="position: relative; width: 55%; text-align:right;">
										<input type="hidden" value="no"  id="desplegar_formulario" name="desplegar_formulario" >
										<input type="button" value="Desplegar Formulario" onclick="cargarArchivo();" style="width:250px;height: 60px;font-size: 22px;border-style: solid; border-width: 5px;border-color:#c2c2a3;" class="btn btn-success color_boton label_espacio">
									</div>
								</td>
							</tr>
							
					    </table>
						
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
