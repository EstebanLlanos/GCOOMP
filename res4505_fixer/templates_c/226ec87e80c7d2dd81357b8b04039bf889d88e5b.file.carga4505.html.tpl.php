<?php /* Smarty version Smarty-3.1.17, created on 2014-08-08 09:06:06
         compiled from "carga4505.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:94505336d7d7aa3030-20171096%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '226ec87e80c7d2dd81357b8b04039bf889d88e5b' => 
    array (
      0 => 'carga4505.html.tpl',
      1 => 1407335136,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '94505336d7d7aa3030-20171096',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5336d7d7ae1847_23341544',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'campo_fechas_corte' => 1,
    'campo_periodo' => 1,
    'mensaje_proceso' => 1,
    'mostrarMsj' => 1,
    'mensajeError' => 1,
    'mostrarMsj2' => 1,
    'mensajeExito' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5336d7d7ae1847_23341544')) {function content_5336d7d7ae1847_23341544($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">   
		<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>
        <script type="text/javascript" src="carga4505.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="carga4505.css" rel="stylesheet" />
       <title>Carga y Validacion 4505</title>
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
					<td >
					<form id='formulario_carga_4505' name='formulario_carga_4505' action='carga4505.php' method='post' enctype= "multipart/form-data">
					<table>
					
					<tr>
					<td style="text-align:left;">
						<table>
						<tr>
						<td style="text-align:left;width:40.05%;">Nombre Prestador:</td><td style="text-align:left;">Archivo detalle 4505:<div id='nombre_archivo'>no se ha subido un archivo</div></td>
						</tr>
						</table>
					</td>
					</tr>
					<tr>
					<td style="text-align:left;">
						<table>
						<tr>
						<td style="text-align:left;width:40%;"><?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>
</td><td style="text-align:left;"> <div class="upload"><input type="file" id='detalle4505' name='4505' style="cursor:pointer;" onchange="obtener_nombre_file_upload();" onclick="obtener_nombre_file_upload();" /></div></td>
						</tr>
						</table>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Nombre EAPB:</td>
					</tr>
					<tr>
					<td style="text-align:left;">
						<table>
						<tr>
						<td style="text-align:left;width:40%;"><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</td><td style="text-align:left;"><input type="button" value="Cargar Y Validar" onclick="cargar4505();" class="btn btn-success color_boton"  /> <input type="reset" value="Limpiar Campos" class="btn btn-success color_boton" /></td>
						</tr>
						</table>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Numero de remisi&oacuten:</td>
					</tr>
					<tr>
					<td style="text-align:left;"><input type='text' id='numero_remision' name='numero_remision'  class='campo_azul' placeholder='Numero de remision' /></td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Fecha de remisi&oacuten(mm/dd/aaaa):</td>
					</tr>
					<tr>
					<td style="text-align:left;"><input type="text" name="fecha_remision" id="fecha_remision" placeholder="clic aqui para seleccionar una fecha" class='campo_azul' onchange="funciones_fecha_restriciones();"/></td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Fecha de corte:</td>
					</tr>
					<tr>
					<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA'/> <?php echo $_smarty_tpl->tpl_vars['campo_fechas_corte']->value;?>
</td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Periodo:</td>
					</tr>
					<tr>
					<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>
</td>
					</tr>
					
					
					<input type='hidden' name='consecutivo' id='consecutivo' value='' />
					
					</table>
					</form>
					</td>
				</tr>	
			</table>
		 </div>
         </td>
		 </tr>  

		<tr>		 
        <td colspan='100' style="text-align:center;">   
			<div  id="mensaje">
				<?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>

			</div>  
		
		    <div id='div_mensaje_error' class="sec2" style="display:<?php echo $_smarty_tpl->tpl_vars['mostrarMsj']->value;?>
">

				<div class="alert alert-block alert-error fade in">

					<a class="close" data-dismiss="alert" href="#">&times</a>
					<h4 class="alert-heading">&iexclHa ocurrido un error....!</h4>
					<p id='parrafo_error'><?php echo $_smarty_tpl->tpl_vars['mensajeError']->value;?>
</p>

				</div>

			</div>

			<div id='div_mensaje_exito' class="sec2" style="display:<?php echo $_smarty_tpl->tpl_vars['mostrarMsj2']->value;?>
">

				<div  class="alert-success alert-block alert-success fade in">

					<a class="close" data-dismiss="alert" href="#">&times</a>
					<h4 class="alert-heading">&iexclCargue completo!</h4>
					<p id='parrafo_exito'><?php echo $_smarty_tpl->tpl_vars['mensajeExito']->value;?>
</p>

				</div>

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
