<?php /* Smarty version Smarty-3.1.17, created on 2018-03-08 10:00:37
         compiled from "act_perfil_interfaz.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1341555ca2c0ab0cd71-81739626%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e342793c7a7e63c4ba80581b588af45b7046b56e' => 
    array (
      0 => 'act_perfil_interfaz.html.tpl',
      1 => 1520521211,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1341555ca2c0ab0cd71-81739626',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_55ca2c0ab52aa6_84387279',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'perfiles' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_55ca2c0ab52aa6_84387279')) {function content_55ca2c0ab52aa6_84387279($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="act_perfil_interfaz.js?v=1.19"></script>
        <script type="text/javascript" src="bignumber.js?v=1.0"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="act_perfil_interfaz.css?v=1.1" rel="stylesheet" />
       <title>Actualizar Permisos a interfaces</title>
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
					<form name="formulario" id="formulario" action="" method="post" enctype= "multipart/form-data" >
						<table>
						<tr>
						<td style="text-align:left;">
						    <span style="color:blue;">Diligencie los siguientes campos si desea <b>crear un perfil</b>:</span>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;" >
						    <input type="text" id='nombre_nuevo_perfil' nombre='nombre_nuevo_perfil' placeholder="Escriba el nombre del nuevo perfil" class='campo_azul' />
						    <label>Poseera derechos de administrador</label>
						    <select id='tendra_derechos_admin' class='campo_azul'>
							<option value='NO'>NO</option>
							<option value='SI'>SI</option>
						    </select>
						    
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <input type="button" id='crear_perfil_b' nombre='crear_perfil_b' class="btn btn-success color_boton" value="Crear Perfil" onclick="crear_perfil();" />
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <br>
						</td>
						</tr>
						    
						<tr>
						<td style="text-align:left;">
						    <span style="color:blue;"><b>Seleccione un perfil existente,</b><br> al cual desea asociar o des-asociar<br>el acceso a las interfaces del sistema:</span>
						</td>
						</tr>
						    
						<tr>
						<td style="text-align:left;">
						    <?php echo $_smarty_tpl->tpl_vars['perfiles']->value;?>

						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						    <div id='div_lista_de_interfaces'></div>
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
