<?php /* Smarty version Smarty-3.1.17, created on 2015-11-09 12:38:56
         compiled from "act_ent_salud.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:24675535a73e1398262-58738779%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ec6f3cf9850b3ad69699aa4529d831053ed4181' => 
    array (
      0 => 'act_ent_salud.html.tpl',
      1 => 1446639182,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '24675535a73e1398262-58738779',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_535a73e1a2fbe4_36290760',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535a73e1a2fbe4_36290760')) {function content_535a73e1a2fbe4_36290760($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="act_ent_salud.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="act_ent_salud.css" rel="stylesheet" />
       <title>Actualizar info. entidades de salud</title>
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
						<h1>Seleccione el archivo que contenga los datos de las entidades de salud</h1>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<p>El archivo tomara los campos separado por comas(,) para indicar las columnas:<br>
						por lo tanto los campos deber&aacuten estar organizados en la siguiente forma <br>
						(NOTA: un numero diferente de comas evitara que se suba el archivo,<br>
						seleccione el orden de las columnas en como desea subirlas):<br>
						<table>
						    <tr>
							<th>
							    <input type="radio" name="orden" value="orden_1" id="orden_1" /><u>Orden Personalizado</u>
							</th>
							<th>
							    <input type="radio" name="orden" value="orden_2" id="orden_2" checked/><u>Orden BD</u>
							</th>
						    </tr>
						    <tr><td colspan="2"><br></td></tr>
						    <tr>
							<td style="text-align: left;">
							    <b>
								cod_tipo_entidad<br>
								des_tipo_entidad_salud<br>
								codigo_entidad<br>
								nombre_de_la_entidad<br>
								numero_identificacion<br>
								digito_verificacion<br>
								codigo_dpto<br>
								cod_mpio<br>
							    </b>
							</td>
							<td style="text-align: left;">
							    <b>
								cod_tipo_entidad<br>
								codigo_entidad<br>
								nombre_de_la_entidad<br>
								codigo_dpto<br>
								cod_mpio<br>
								des_tipo_entidad_salud<br>
								numero_identificacion<br>
								digito_verificacion<br>
							    </b>
							</td>
						    </tr>
						</table>
						<input type="checkbox" name="check_llenar_otras_tablas" id="casilla_1_actualizar_otras_tablas" value="act_yes"/>Actualizar/llenar Tablas prestadores/entidades administradoras
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
						<input type="button" value="Actualizar/Insertar" class="btn btn-success color_boton" onclick="enviar();" />
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
