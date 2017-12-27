<?php /* Smarty version Smarty-3.1.17, created on 2017-12-27 12:16:22
         compiled from "repobligHF0123.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:12955811205a43d5662ba841-00729647%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '72fc926f451dfd7200f43d25d23963a0098c2d04' => 
    array (
      0 => 'repobligHF0123.html.tpl',
      1 => 1514394963,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '12955811205a43d5662ba841-00729647',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_fechas_corte' => 1,
    'campo_periodo' => 1,
    'campo_eapb' => 1,
    'mensaje_proceso' => 1,
    'mostrarResultado' => 1,
    'resultado_definitivo' => 1,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5a43d5662cdb45_02564725',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a43d5662cdb45_02564725')) {function content_5a43d5662cdb45_02564725($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>
	<script src="../librerias_externas/jquery_y_ui_actualizado/jquery-ui.js"></script>
	<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">
        <script type="text/javascript" src="repobligHF0123.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="repobligHF0123.css" rel="stylesheet" />
       <title>Reporte Obligatorio HF 0123</title>
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
					<td style="text-align:left;"><h5>Generaci&oacuten del reporte obligatorio HF 0123:</h5></h5></td>
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
