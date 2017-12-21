<?php /* Smarty version Smarty-3.1.17, created on 2016-04-14 17:26:52
         compiled from "crear_usuario.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:95005332df77dd6946-22299720%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6d30a72f87f41b95e96d789051e009dfaf119d31' => 
    array (
      0 => 'crear_usuario.html.tpl',
      1 => 1448549764,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '95005332df77dd6946-22299720',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5332df77e18fd6_84782997',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'selector_tipo_id' => 1,
    'selector_perfil' => 1,
    'fecha_creado' => 1,
    'error' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5332df77e18fd6_84782997')) {function content_5332df77e18fd6_84782997($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
		<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>			
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>		
        <script type="text/javascript" src="crear_usuario.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="crear_usuario.css" rel="stylesheet" />
       <title>Registrar Usuario</title>
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
							<!--<div id="logo">-->
								<img id="imgLogo" src="../assets/imagenes/logo_gios.png" />
							<!--</div>-->
						</td> 
						<td >
							<form id='formulario_crear_usuario' name='formulario_crear_usuario' action='crear_usuario.php' method='post'>
							<table >
							
							<div id='info_persona'><script>function f_info_persona(){}</script></div>
							
							<tr>
							<td style="text-align:left;">
							<h4 style="color:blue;">Formulario de registro de usuarios del sistema, los campos con (*) al lado son obligatorios:</h4>
							</td>
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
							<td style="text-align:left;">
							<input type='text' id='identificacion' name='identificacion' class='campo_azul' onchange='traer_info_persona();'  /> 
							</td>
							</tr>
							
						
							
							<tr>
							<td style="text-align:left;">
							Primer Nombre *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='primer_nombre' name='primer_nombre' class='campo_azul' onkeyup='crear_nick_logueo();' onchange='crear_nick_logueo();'/></td>
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
							<td style="text-align:left;"><input type='text' id='primer_apellido' name='primer_apellido' class='campo_azul' onkeyup='crear_nick_logueo();' onchange='crear_nick_logueo();'/></td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Segundo Apellido
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='segundo_apellido' name='segundo_apellido' class='campo_azul'/></td>
							</tr>
							
							
							
							
							
							<!--
							<tr>
							<td style="text-align:left;">
							Nick Usuario  *
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type='text' id='nick_usuario' name='nick_usuario' autocomplete="off" class='campo_azul'/> </td>
							</tr>
							-->
							
							<tr>
							<td style="text-align:left;">
							Nick Logueo(Usuario) * 
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							<input type='text' id='nick_logueo' name='nick_logueo' autocomplete="off" autocorrect="off" class='campo_azul' placeholder='Ingrese el usuario' autocomplete='off'   /> 
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Codigo entidad de salud Asociada * 
							</td>
							</tr>
							
							
							
							<tr>
							<td style="text-align:left;">
							<input type='text' id='cod_entidad_salud_0' name='cod_entidad_salud_0' autocomplete="off" autocorrect="off" class='campo_azul' placeholder='Ingrese el codigo de la entidad de salud a asociar' autocomplete='off' /> 
							<!--<input type='button' id='buscar_0' name='buscar_0' class="btn btn-success color_boton" value='buscar' onclick='buscar_seleccionar_entidad(document.getElementById("cod_entidad_salud_0"));' />	-->						
							
							<!--
							<input type='button' id='boton_inc_0' class="btn btn-success color_boton" value='+' onclick='adicionar_entidad(0);' />
							<input type='button' id='boton_limp_0' class="btn btn-success color_boton" value='-' onclick='limpiar_entidad(0);' />
							-->
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							Seleccione el perfil asociado
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							<select id='perfil_0' name='perfil_0' class='campo_azul'>
							<option value='none'>Seleccione un tipo de perfil</option>
							<?php echo $_smarty_tpl->tpl_vars['selector_perfil']->value;?>
 
							</select>
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
							
							
							
							
							<input type='hidden' id='fecha_creacion' name='fecha_creacion' value='<?php echo $_smarty_tpl->tpl_vars['fecha_creado']->value;?>
'/>
							
							
							<tr>
							<td colspan='100' style="text-align:left;"><h5>Fecha de creaci&oacuten: <?php echo $_smarty_tpl->tpl_vars['fecha_creado']->value;?>
</h5></td>
							</tr>
							
							<tr>
							<td colspan='100' style="text-align:left;"><input type='button' value='Registrar' class="btn btn-success color_boton" onclick="enviar();" /></td>
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
		
		<script>
		if (document.getElementsByTagName) 
		{

			var inputElements = document.getElementsByTagName('input');

			for (i=0; inputElements[i]; i++) 
			{

				if (inputElements[i].className && (inputElements[i].className.indexOf('disableAutoComplete') != -1)) 
				{

				inputElements[i].setAttribute('autocomplete','off');

				}

			}

		}
		</script>
    </body>
</html><?php }} ?>
