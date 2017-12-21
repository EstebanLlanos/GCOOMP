<?php /* Smarty version Smarty-3.1.17, created on 2014-08-01 08:46:46
         compiled from "HC_registro_atencion.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2790153d73c41500237-64240182%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a18b04dc7f9e9a4e76261d42966add40d266d3d3' => 
    array (
      0 => 'HC_registro_atencion.html.tpl',
      1 => 1406868170,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2790153d73c41500237-64240182',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53d73c41b499b3_25219597',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'fecha_actual_consulta' => 1,
    'selector_tipo_id_paciente' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53d73c41b499b3_25219597')) {function content_53d73c41b499b3_25219597($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="HC_registro_atencion.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="HC_registro_atencion.css" rel="stylesheet" />
       <title>Historia Clinica Registro Atencion</title>
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
						<?php echo $_smarty_tpl->tpl_vars['fecha_actual_consulta']->value;?>

						</td>
						</tr>
						
						<!-- DATOS DEL PACIENTE -->
						<tr>
						<td style="text-align:left;">
						<h5>Datos del paciente</h5>
						</td>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="+" id='boton_mo_div_datos_paciente' name='boton_mo_div_datos_paciente' onclick="mostrar_desaparecer_divisor('datos_paciente_div','boton_mo_div_datos_paciente');"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<div id="datos_paciente_div" style="display:none;">
						
						    <table>
							<tr>
							    <td style="text-align:left;">
								Numero ID paciente
							    </td>
							    <td style="text-align:left;">
								Tipo ID Paciente
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="identificacion" name="identificacion" placeholder="Numero identificacion paciente" style="width:70%;"/>
							    </td>
							    <td style="text-align:left;">
							    <?php echo $_smarty_tpl->tpl_vars['selector_tipo_id_paciente']->value;?>

							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
								Primer Nombre
							    </td>
							    <td style="text-align:left;">
								Segundo Nombre
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="primer_nombre" name="primer_nombre" placeholder="Primer nombre del paciente" style="width:70%;"/>
							    </td>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="segundo_nombre" name="segundo_nombre" placeholder="Segundo nombre del paciente" style="width:70%;"/>
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
								Primer Apellido
							    </td>
							    <td style="text-align:left;">
								Segundo Apellido
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="primer_apellido" name="primer_apellido" placeholder="Primer apellido del paciente" style="width:70%;"/>
							    </td>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="segundo_apellido" name="segundo_apellido" placeholder="Segundo apellido del paciente" style="width:70%;"/>
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;" colspan="2">
								Direcci&oacuten
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;" colspan="2">
								<input type="text" class="campo_azul" id="direccion" name="direccion" placeholder="Direccion de la vivienda del paciente" style="width:70%;"/>							    
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
								Telefono fijo
							    </td>
							    <td style="text-align:left;">
								Telefono movil(celular):
							    </td>
							</tr>
							<tr>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="telefono_fijo" name="telefono_fijo" placeholder="Telefono fijo del paciente" style="width:70%;"/>
							    </td>
							    <td style="text-align:left;">
							    <input type="text" class="campo_azul" id="telefono_movil" name="telefono_movil" placeholder="Telefono movil del paciente" style="width:70%;"/>
							    </td>
							</tr>
						    </table>
						
						</div>
						</td>
						</tr>
						
						<!-- SINTOMATOLOGIA ANAMNESIS-->
						<tr>
						<td style="text-align:left;">
						<h5>Anamnesis-Sintomatologia</h5>
						</td>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="+" id='boton_mo_div_anamnesis' name='boton_mo_div_anamnesis' onclick="mostrar_desaparecer_divisor('datos_anamnesis_div','boton_mo_div_anamnesis');"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<div id="datos_anamnesis_div" style="display:none;">
						
						contenido
						
						</div>
						</td>
						</tr>
						
						<!-- MOTIVO ATENCION-->
						<tr>
						<td style="text-align:left;">
						<h5>Motivo de atenci&oacuten</h5>
						</td>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="+" id='boton_mo_div_mo_atencion' name='boton_mo_div_mo_atencion' onclick="mostrar_desaparecer_divisor('datos_mo_atencion_div','boton_mo_div_mo_atencion');"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<div id="datos_mo_atencion_div" style="display:none;">
						
						contenido
						
						</div>
						</td>
						</tr>
						
						
						<!-- CONDUCTA -->
						<tr>
						<td style="text-align:left;">
						<h5>Conducta</h5>
						</td>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="+" id='boton_mo_div_conducta' name='boton_mo_div_conducta' onclick="mostrar_desaparecer_divisor('datos_conducta_div','boton_mo_div_conducta');"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<div id="datos_conducta_div" style="display:none;">
						
						contenido
						
						</div>
						</td>
						</tr>
						
						<!-- ORDENAMIENTO -->
						<tr>
						<td style="text-align:left;">
						<h5>Ordenamiento</h5>
						</td>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="+" id='boton_mo_div_ordenamiento' name='boton_mo_div_ordenamiento' onclick="mostrar_desaparecer_divisor('datos_ordenamiento_div','boton_mo_div_ordenamiento');"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<div id="datos_ordenamiento_div" style="display:none;">
						
						contenido
						
						</div>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="Validar Historia Clinica" id='boton_validar_hc' name='boton_validar_hc' onclick="validar_hc();" />
						<input type="button" class="btn btn-success color_boton" value="Cerrar Historia Clinica" id='boton_cerrar_hc' name='boton_cerrar_hc' onclick="cerrar_hc();"/>
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
