<?php /* Smarty version Smarty-3.1.17, created on 2015-07-09 19:08:42
         compiled from "contactenos_pqr.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8454551d9b44589d31-89484048%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '87bc4cfa4928d4f05d31820c838fdc1093cef441' => 
    array (
      0 => 'contactenos_pqr.html.tpl',
      1 => 1434560402,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8454551d9b44589d31-89484048',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_551d9b44b90e17_66550917',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_551d9b44b90e17_66550917')) {function content_551d9b44b90e17_66550917($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="contactenos_pqr.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="contactenos_pqr.css" rel="stylesheet" />
       <title>contactenos_pqr</title>
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
						    <h4 style="color:blue">Peticiones, quejas y reclamos</h4>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <select id='selector_receptor' name='selector_receptor'  class="campo_azul"  style="size: auto;" >
							<option value="none">Seleccione el receptor del mensaje</option>
							<option value="torres.miguel.52@gmail.com " selected>ADMIN. GIOSS</option>
							<option value="jdmejia2009@gmail.com" >SOPORTE TECNICO</option>							
							<option value="sistemagioss@gmail.com" >SUGERENCIAS</option>
						    </select>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <label for="asunto">Asunto:</label>
						    <input id='asunto' name='asunto' style="width:50%;height: auto;font-size: medium;"  class="campo_azul"/>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <label for="mensaje_pqr">Mensaje:</label>
						    <textarea id='mensaje_pqr' name='mensaje_pqr' style="width:800px;height: 300px;font-size: medium;"  class="campo_azul">Digite su mensaje</textarea>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <input type="button" id='enviar_pqr' name='enviar_pqr' value="Enviar PQR" class="btn btn-success color_boton" onclick="enviar_mensaje();" />
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
									<h4 id='titulo_mensaje_exito' class="alert-heading">&iexclMensaje Enviado!</h4>
									<p id='parrafo_mensaje_exito'></p>

								</div>

							</div>
							
							<!--Ventana informacion error-->
							<div id='div_mensaje_error' class="sec2" style="width:90%;display:none;">

								<div class="alert-success alert-block alert-error fade in">

									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_error' class="alert-heading">&iexclError al enviar el mensaje!</h4>
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
