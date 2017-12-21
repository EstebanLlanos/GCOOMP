<?php /* Smarty version Smarty-3.1.17, created on 2015-07-01 11:44:03
         compiled from "entidades_obligadas_a_reportar.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:300325400aecf83a978-99462861%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6f377a8353551b6ba8ef846a2c33a761021821a0' => 
    array (
      0 => 'entidades_obligadas_a_reportar.html.tpl',
      1 => 1415090092,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '300325400aecf83a978-99462861',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5400aecf8840a5_76776245',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_eapb' => 1,
    'campo_periodo' => 0,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5400aecf8840a5_76776245')) {function content_5400aecf8840a5_76776245($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="entidades_obligadas_a_reportar.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="entidades_obligadas_a_reportar.css" rel="stylesheet" />
       <title>entidades_obligadas_a_reportar</title>
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
						<input type="hidden" id="oculto_envio" name="oculto_envio" value="no_envio" />
						</td>
						</tr>  
			    
			    
						<tr>
						<td style="text-align:left;">
						<h5>Registre las entidades obligadas a reportar(proceso para la EAPB).</h5>
						</td>
						</tr>
						
						<!--
						<tr>
						<td style="text-align:left;">
						La idea es que la EAPB envie un TXT con la informaci&oacuten o si se instala el aplicativo en la EAPB, <br>
						pueda extraer la informaci&oacuten del sistema de informaci&oacuten.
						</td>
						</tr>
						-->
						
						
						<tr>
						<td style="text-align:left;">
						Nombre EAPB:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
							
						</td>
						</tr>
						
						<!--
						<tr>
						<td style="text-align:left;">A&ntildeo:</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type='text' id='year_para_periodo' name='year_para_periodo' class='campo_azul' placeholder='AAAA' onkeypress="return isNumberKey(event)" maxlength="4"/> </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Periodo:</td>
						</tr>
						<tr>
						<td style="text-align:left;position:relative;"><?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>
<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%"></div></td>
						</tr>
						-->
						
						<tr>
						<td style="text-align:left;">
						<h5>El archivo de entidades obligadas a reportar a cargar debera poseer<br>
						la siguiente estructura para cada fila(cada fila es una entidad obligada a reportar tienen sus campos separados por coma):<br>
						</h5>
						<p>
						Codigo de la EAPB,<br>
						Tipo de Regimen,<br>
						Codigo del Prestador de Servicios,<br>
						Tipo de Identificacion del Prestador,<br>
						Numero de Identificacion del Prestador,<br>
						Raz&oacuten Social del Prestador,<br>
						Estado del prestador,<br>
						Tipo de la informaci&oacuten a reportar(norma),<br>
						Tipo de archivo de la norma(en el caso de RIPS ponga el codigo para CT),<br>
						Codigo del municipio,<br>
						Codigo del departamento<br>
						A&ntildeo<br>
						Codigo Sede Principal<br>
						Nombre Prestador Sede Prncipal<br>
						</p>
						
						</td>
						</tr>
						
						<tr>
						    <td style="text-align:right;width:40.05%;">
							<table id='tabla_seleccion_archivos' style='display:inline;'>
							
								<!--hidden validar archivo, (debe contener la sigla)-->
								<input type='hidden' name='ENTIDADES_OBLIGADAS_A_REPORTAR_hidden' id='ENTIDADES_OBLIGADAS_A_REPORTAR_hidden' value='' />
								<!--fin hidden validar archivo-->
								
								
								<!--ARCHIVO ARCHIVO ENTIDADES OBLIGADAS A REPORTAR-->
								<tr>
								<td style="text-align:left;width:25%;">
								<h5>CARGAR ARCHIVO CON LOS DATOS<BR>DE LAS ENTIDADES OBLIGADAS A REPORTAR:</h5>
								</td>
								
								<td style="position:relative;text-align:left;width:25%;">
								
								<table>
								<tr>
								
								<td style="position:absolute;left:-35%;top:5%;width:5%;">
								<div class="upload"><input type="file" id='ent_obl_a_rep_file' name='ent_obl_a_rep_file' style="cursor:pointer;" onchange="obtener_nombre_file_upload('ent_obl_a_rep_file','nombre_archivo_ent_obl_a_rep');" onclick="obtener_nombre_file_upload('ent_obl_a_rep_file','nombre_archivo_ent_obl_a_rep');" /></div>
								</td>
								
								<td style="position:absolute;left:-20%;top:15%;">
								<div id='nombre_archivo_ent_obl_a_rep'></div>
								</td>
								
								</tr>
								</table>
								
								</td>
								</tr>
								<!--FIN ARCHIVO ENTIDADES OBLIGADAS A REPORTAR-->
						
							</table>
						</td>
						</tr>
						
						
						
						<tr>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="Cargar" id='cargar_ent_obl_a_rep' name='cargar_ent_obl_a_rep' onclick="cargar();" />
						<input type="reset" class="btn btn-success color_boton" value="Limpiar" id='limpiar_ent_obl_a_rep' name='limpiar_ent_obl_a_rep' />
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
