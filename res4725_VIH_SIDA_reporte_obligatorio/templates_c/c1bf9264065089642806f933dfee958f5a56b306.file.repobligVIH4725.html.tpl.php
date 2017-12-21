<?php /* Smarty version Smarty-3.1.17, created on 2015-10-09 10:31:12
         compiled from "repobligVIH4725.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1831153ba3e6377d2f1-45840019%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1bf9264065089642806f933dfee958f5a56b306' => 
    array (
      0 => 'repobligVIH4725.html.tpl',
      1 => 1441893098,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1831153ba3e6377d2f1-45840019',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53ba3e638b1cc9_79888923',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_fechas_corte' => 1,
    'campo_periodo' => 1,
    'campo_prestador' => 0,
    'campo_eapb' => 1,
    'mensaje_proceso' => 1,
    'mostrarResultado' => 1,
    'resultado_definitivo' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53ba3e638b1cc9_79888923')) {function content_53ba3e638b1cc9_79888923($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>
	<script src="../librerias_externas/jquery_y_ui_actualizado/jquery-ui.js"></script>
	<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">
        <script type="text/javascript" src="repobligVIH4725.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="repobligVIH4725.css" rel="stylesheet" />
       <title>Reporte Obligatorio VIH 4725</title>
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
					<td style="text-align:left;">
					<form action="" method="post" id="reporte_obligatorio" name="reporte_obligatorio" enctype= "multipart/form-data">
					<table>
					
					<tr>
					<td style="text-align:left;"><h5>Generaci&oacuten del reporte obligatorio VIH-SIDA 4725:</h5></h5></td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Estado de la informaci&oacuten:</td>
					</tr>
					<tr>
					<td style="text-align:left;">
					<select id='selector_estado_info' name='selector_estado_info' class='campo_azul' >
					<option value='none'>Seleccione el estado de la informaci&oacuten a buscar</option>
					<option value='validada'>Informaci&oacuten validada</option>
					<option value='rechazada'>Informaci&oacuten rechazada</option>
					</select>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;">A&ntildeo de corte:</td>
					</tr>
					<tr>
					<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' onchange='escribiendo_year_corte();' onkeyup='escribiendo_year_corte();' onkeypress="return isNumberKey(event)"/> <?php echo $_smarty_tpl->tpl_vars['campo_fechas_corte']->value;?>
</td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Periodo:</td>
					</tr>
					<tr>
					<td style="text-align:left;position:relative;"><?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>
<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%"></div></td>
					</tr>
					
					<!--
					<tr>					
					<td style="text-align:left;">Nombre Prestador:</td>					
					</tr>
					<tr>					
					<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>
</td><td style="text-align:left;">					
					</tr>
					-->
					
					<tr>
					<td style="text-align:left;">Nombre EAPB:</td>
					</tr>
					<tr>
					<td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</td>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;"><input type="button" value="Generar" class="btn btn-success color_boton" onclick="enviar_formulario();"/> <input type="reset" value="limpiar" class="btn btn-success color_boton"/></td>
					</tr>
					
					<tr>
					<td style="text-align:left;">
						<div  id="mensaje">

							<label id="msj"><?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>
</label>

						</div>  

						<!--Ventana informacion exito-->
						<div id='div_mensaje_exito' class="sec2" style="width:90%;display:none;">

							<div class="alert-success alert-block alert-success fade in">

								<a class="close" data-dismiss="alert" href="#">&times</a>
								<h4 id='titulo_mensaje_exito' class="alert-heading" style="text-align: center;">Mensaje:</h4>
								<p id='parrafo_mensaje_exito' style="text-align: center;"></p>

							</div>

						</div>
						
						<!--Ventana informacion error-->
						<div id='div_mensaje_error' class="sec2" style="width:90%;display:none;">

							<div class="alert-success alert-block alert-error fade in">

								<a class="close" data-dismiss="alert" href="#">&times</a>
								<h4 id='titulo_mensaje_error' class="alert-heading" style="text-align: center;">&iexclERROR!</h4>
								<p id='parrafo_mensaje_error' style="text-align: center;"></p>

							</div>

						</div>

						<div id="grilla" style="display: <?php echo $_smarty_tpl->tpl_vars['mostrarResultado']->value;?>
"> 

						
						
						 <?php echo $_smarty_tpl->tpl_vars['resultado_definitivo']->value;?>


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
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
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
