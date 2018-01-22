<?php /* Smarty version Smarty-3.1.17, created on 2018-01-22 12:19:29
         compiled from "carga_val_CANCER.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15977223515a4e67e55ea0b8-35203907%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ebbbf5beed0a9b6069c99c900a13d5e6d1a412dc' => 
    array (
      0 => 'carga_val_CANCER.html.tpl',
      1 => 1516641508,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15977223515a4e67e55ea0b8-35203907',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5a4e67e565d2b4_85022532',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'proveniente_de_prestador_o_eapb' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'get_entidad_personalizada' => 1,
    'campo_periodo' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5a4e67e565d2b4_85022532')) {function content_5a4e67e565d2b4_85022532($_smarty_tpl) {?><!DOCANCERYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="carga_val_CANCER.js?v=2.112"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="carga_val_CANCER.css" rel="stylesheet" />
       <title>Carga y Validacion de archivos para CANCER 0247</title>
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
						<h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Formulario de carga y validacion de documentos 0247 para CANCER</h4>
						</td>
						</tr>
						
						<tr>
						
						<td style="text-align:left;width:7.05%;">
						
							<table>
							    
								<?php echo $_smarty_tpl->tpl_vars['proveniente_de_prestador_o_eapb']->value;?>

							
								<tr>
								<td style="text-align:left;">
								    <h5 id='sub_titulo_entidad_1' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Entidad Que Efect&uacutea el Cargue:</h5>
								    <h5 id='sub_titulo_entidad_1_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;">Entidad a La Cual se Efect&uacutea el Reporte:</h5>
								    <h5 id='sub_titulo_entidad_3_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;position: relative;top:50%;">Se Reporta al Ministerio de Salud.</h5>
								</td>
								</tr>
								
								<tr>
								<td style="text-align:left;">
								    <div id='entidad_1'><?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>
</div>
								</td>
								</tr>
								
								<tr>
								<td style="text-align:left;">
								    <h5 id='sub_titulo_entidad_2' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Entidad a La Cual se Efect&uacutea el Reporte:</h5>
								    <h5 id='sub_titulo_entidad_2_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;">Entidad Que Efect&uacutea el Cargue:</h5>
								    <h5 id='sub_titulo_entidad_4_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;position: relative;top:50%;">Se Reporta al Ministerio de Salud.</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;">
								    <div id='entidad_2'><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</div>						
								</td>
								</tr>
								
								<tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;" id='titulo_numero_de_remision'>Numero de remisi&oacuten:</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;">
									<input type='hidden' id='numero_de_remision' name='numero_de_remision'  class='campo_azul' style='width:230px;height:30px;' placeholder='Numero de remision CANCER'  />
								    <div id='separador_numero_de_remision'>
								    <!--<input type='text' id='numero_de_remision' name='numero_de_remision'  class='campo_azul' style='width:230px;height:30px;' placeholder='Numero de remision CANCER' onchange="cuando_se_escribe_el_nombre_del_archivo();validar_antes_seleccionar_archivos();" onkeyup="cuando_se_escribe_el_nombre_del_archivo();validar_antes_seleccionar_archivos();" onkeypress="return isNumberKey(event)" maxlength="2" />-->
								    </div>
								</td>
								<td><p id='error_nombre_archivo'></p></td>
								</tr>
								
								<!--
								<tr>
								<td style="text-align:left;">Fecha de remisi&oacuten del archivo:</td>
								</tr>
								<tr>
								<td style="text-align:left;"><input type="text" name="fecha_remision" id="fecha_remision" placeholder="clic aqui para seleccionar una fecha" class='campo_azul' onchange="validar_antes_seleccionar_archivos();" onkeyup="validar_antes_seleccionar_archivos();" /></td>
								</tr>
								-->
								
								<tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">A&ntildeo de corte:</h5>
								</td>
								</tr>
								<tr>
								<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' style='width:230px;height:30px;' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)" onchange="validar_antes_seleccionar_archivos();" onkeyup="validar_antes_seleccionar_archivos();"/> </td>
								</tr>	
								
								<tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Fecha/Periodo de corte:(Nota: la fecha de corte usada en los calculos sera la del -01-01 del a&ntilde;o siguiente de la fecha limite indicada en el archivo)</h5>
								    <input type="hidden" name="get_entidad_personalizada" id="get_entidad_personalizada" value="<?php echo $_smarty_tpl->tpl_vars['get_entidad_personalizada']->value;?>
">
								</td>
								</tr>
								<tr>
								<td style="text-align:left;">
								<?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>

								</td>
								</tr>
								
								
								<tr>
								    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display:inline;" id="titulo_regimen">Regimen:</h5></td>
								</tr>
								<tr>
								    <td style="text-align:left;">
									<div id="separador_regimen" style="display: inline;" >
									    <select id='selector_regimen_para_agrupados' name='selector_regimen_para_agrupados' class='campo_azul' onchange="validar_antes_seleccionar_archivos();">
										<option value='none'>Seleccione un regimen</option>
										<option value='C'>C (Contributivo)</option>
										<option value='S'>S (Subsidiado)</option>
										<option value='E'>E (Especial)</option>
										<option value='P'>P (Excepcion)</option>
										<option value='N'>N (No Asegurado)</option>
									    </select>
									</div>
								    </td>
								</tr>
								
								
							
							</table>
						</td>
						
						<td style="text-align:left;width:20.05%;">
							<table id='tabla_seleccion_archivos' style='display:inline;'>
							
								<!--hidden validar archivo, (debe contener la sigla)-->
								<input type='hidden' name='CANCER_hidden' id='CANCER_hidden' value='' />
								<!--fin hidden validar archivo-->
								
								
								<!--ARCHIVO CANCER-->
								<tr>
								<td style="text-align:left;width: 100px">
								<h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;" >0247 CANCER:</h5>
								</td>
								
								<td style="display:block;position:relative;text-align:left;">
								
								<table style="display: block;">
								<tr style="display: block;">
								
								<td >
								<div class="upload"><input type="file" id='0247_cancer_file' name='0247_cancer_file' style="cursor:pointer;" onchange="obtener_nombre_file_upload('0247_cancer_file','nombre_archivo_0247');verificar_nombre_archivo(this.value,'CANCER','nombre_archivo_0247');" onclick="obtener_nombre_file_upload('0247_cancer_file','nombre_archivo_0247');verificar_nombre_archivo(this.value,'CANCER','nombre_archivo_0247');" /></div>
								
								</td>
								
								<td style="position:absolute;left: 50px;top:15%;">
								
								<div style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;" id='nombre_archivo_0247' ></div>
								</td>
								
								</tr>
								</table>
								
								</td>
								</tr>
								<!--FIN ARCHIVO CANCER-->
						
							</table>
						</td>
						
						</tr>	
						
						<input type='hidden' name='accion' id='accion' value='validar' />
						
						<!--botones para cargar los archivos y validar o limpiar el formulario-->
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Cargar Y Validar" onclick="cargarCANCER();" class="btn btn-success color_boton"  /> 
						<input type="reset" value="Limpiar Campos" class="btn btn-success color_boton" onclick='limpiar_files();' />
						</td>
						</tr>
						<!--fin botones para cargar los archivos y validar o limpiar el formulario-->
						
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
		    <div id="medidor_ram" name="medidor_ram" style="font-size: xx-small;color: white;"></div>
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
