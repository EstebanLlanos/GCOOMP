<?php /* Smarty version Smarty-3.1.17, created on 2014-04-10 09:02:25
         compiled from "mod_datos_usuario.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:367253429ebc5685e0-50535614%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd26c90f4324440408227f592a1c5543571f51e20' => 
    array (
      0 => 'mod_datos_usuario.html.tpl',
      1 => 1397129602,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '367253429ebc5685e0-50535614',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53429ebc5c2374_56114092',
  'variables' => 
  array (
    'menu' => 0,
    'nombre' => 1,
    'selector_tipo_id' => 1,
    'selector_perfil' => 1,
    'estado_usuario' => 1,
    'fecha_creado' => 1,
    'error' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53429ebc5c2374_56114092')) {function content_53429ebc5c2374_56114092($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
		<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>			
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>		
        <script type="text/javascript" src="mod_datos_usuario.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="mod_datos_usuario.css" rel="stylesheet" />
       <title>Modificar Datos Usuario</title>
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
							<!--
							<td>
							<div id='menu_div'><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</div>
							</td>
							-->
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
							<!--<div id="logo">-->
								<img id="imgLogo" src="../assets/imagenes/logo_gios.png" />
							<!--</div>-->
						</td> 
						<td >
							<form id='formulario' name='formulario' action='' method='post'>
							<table >
							
							<tr>
							<td style="text-align:left;">
							<h4 style="color:blue;">Formulario de modificaci&oacuten/actualizaci&oacuten de usuarios del sistema, los campos con (*) al lado son obligatorios:</h4>
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Primer Nombre *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='primer_nombre' name='primer_nombre' class='campo_azul' /></td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Segundo Nombre
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='segundo_nombre' name='segundo_nombre' class='campo_azul' /></td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Primer Apellido *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='primer_apellido' name='primer_apellido' class='campo_azul'/></td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Segundo Apellido
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='segundo_apellido' name='segundo_apellido' class='campo_azul'/></td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Tipo Identificaci&oacuten *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['selector_tipo_id']->value;?>
 </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Numero Identificaci&oacuten *
							</td>
							</tr>
							
							
							<tr>
							<td style="text-align:left;"><input type='text' id='identificacion' name='identificacion' class='campo_azul' readonly/> </td>
							</tr>
							
							<!--
							<tr>
							<td style="text-align:left;">
							Nick Usuario *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='nick_usuario' name='nick_usuario' autocomplete="off" class='campo_azul'/> </td>
							</tr>
							
							-->
							
							<tr>
							<td style="text-align:left;">
							Codigo entidad de salud Asociada * (presione + para asociara mas entidades)
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							<input type='text' id='cod_entidad_salud_0' name='cod_entidad_salud_0' autocomplete="off" class='campo_azul' placeholder='Ingrese el codigo de la entidad de salud a asociar' onclick='buscar_seleccionar_entidad(this);'/> 
							<select id='perfil_0' name='perfil_0' class='campo_azul'>
							<option value='none'>Seleccione un tipo de perfil</option>
							<?php echo $_smarty_tpl->tpl_vars['selector_perfil']->value;?>
 
							</select>
							<select id='estado_usuario_0' name='estado_usuario_0' class='campo_azul'>
							<option value='none'>Seleccione el estado del usuario</option>
							<?php echo $_smarty_tpl->tpl_vars['estado_usuario']->value;?>

							</select>
							<input type='hidden' id='nick_usuario_0' name='nick_usuario_0' />
							<!--
							<input type='button' id='boton_inc_0' class="btn btn-success color_boton" value='+' onclick='adicionar_entidad(0);' />
							<input type='button' id='boton_limp_0' class="btn btn-success color_boton" value='-' onclick='limpiar_entidad(0);' />
							-->
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							<div id='ext_cod_ent_0'>
							
							
							</div>
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Password *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='password' id='password_user' name='password_user' autocomplete="off" class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Confirme el password *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='password' id='confirmar_password' name='confirmar_password' autocomplete="off" class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							EMAIL *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='email' name='email' class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Direcci&oacuten *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='direccion' name='direccion' class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Telefono
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='telefono' name='telefono' class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Celular
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='celular' name='celular' class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Fecha de nacimiento
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type="text" name="fecha_cumple" id="fecha_cumple" placeholder="clic aqui para seleccionar una fecha" class='campo_azul'/> </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Fecha Expiraci&oacuten
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type="text" name="fecha_vence" id="fecha_vence" placeholder="clic aqui para seleccionar una fecha" class='campo_azul'/>  </td>
							</tr>
							
							<!--
							<tr>
							<td style="text-align:left;">
							Tipo Perfil *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['selector_perfil']->value;?>
 </td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Estado Usuario *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							<?php echo $_smarty_tpl->tpl_vars['estado_usuario']->value;?>

							</td>
							</tr>
							-->
							
							<input type='hidden' id='fecha_creacion' name='fecha_creacion' value='<?php echo $_smarty_tpl->tpl_vars['fecha_creado']->value;?>
'/>
							<input type='hidden' id='fecha_ultimo_acceso' name='fecha_ultimo_acceso' value=''/>
							
							
							<tr>
							<td colspan='100' style="text-align:left;"><h5>Fecha de creaci&oacuten: <?php echo $_smarty_tpl->tpl_vars['fecha_creado']->value;?>
</h5></td>
							</tr>
							
							<tr>
							<td colspan='100' style="text-align:left;"><input type='button' value='Actualizar' class="btn btn-success color_boton" onclick="enviar();" /></td>
							</tr>
							
							<tr>
							<td colspan='100' style="text-align:left;"><div id='error_div' style='color:red;'><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</div></td>
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
