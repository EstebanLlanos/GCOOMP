<?php /* Smarty version Smarty-3.1.17, created on 2017-05-23 15:34:01
         compiled from "cons_pobl_riesgo.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:7148571ccc35803c63-46782094%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e42b26a686180c6e194885192cd5b427886c088c' => 
    array (
      0 => 'cons_pobl_riesgo.html.tpl',
      1 => 1464378592,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '7148571ccc35803c63-46782094',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_571ccc36595b84_12925134',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_fechas_corte' => 1,
    'campo_periodo' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_571ccc36595b84_12925134')) {function content_571ccc36595b84_12925134($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="cons_pobl_riesgo.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="cons_pobl_riesgo.css" rel="stylesheet" />
       <title>cons_pobl_riesgo</title>
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
						</td>
						</tr>
						
						    
						<tr>
						    <td style="text-align:left;">
							<h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Consulta datos poblaci&oacuten riesgo PyP 4505:</h4>
						    </td>
						</tr>
						
						<tr>
						    <td style="width:25%">
							<table >
							    <tr>
							    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">A&ntildeo de corte:</h5></td>
							    </tr>
							    <tr>
								<td style="text-align:left;">
								    <input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' onchange='escribiendo_year_corte();' onkeyup='escribiendo_year_corte();' onkeypress="return isNumberKey(event)"/>
								    <?php echo $_smarty_tpl->tpl_vars['campo_fechas_corte']->value;?>

								</td>
							    </tr>
							    
							    
							    <tr>
							    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Tipo Periodo:</h5></td>
							    </tr>
							    <tr>
							    <td style="text-align:left;position:relative;">
								<select id='tipo_tiempo_periodo' name='tipo_tiempo_periodo' class='campo_azul' onchange="cambio_tipo_tiempo_periodo();">
								    <option value='trimestral' selected>Trimestral</option>
								    <option value='mensual'>Mensual</option>
								</select>
							    </td>
							    </tr>
							    
							    <tr>
							    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo:</h5></td>
							    </tr>
							    <tr>
							    <td style="text-align:left;position:relative;">
								<div id='div_selector_periodo'><?php echo $_smarty_tpl->tpl_vars['campo_periodo']->value;?>
</div>
								<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:relative;left:25%;top:5%">					    
								</div>
							    </td>
							    </tr>
							    
							    
							    <tr>					
							    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Nombre Prestador:</h5></td>					
							    </tr>
							    <tr>					
							    <td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>
</td><td style="text-align:left;">					
							    </tr>
							    
							    
							    <tr>
							    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Nombre EAPB:</h5></td>
							    </tr>
							    <tr>
							    <td style="text-align:left;"><?php echo $_smarty_tpl->tpl_vars['campo_eapb']->value;?>
</td>
							    </td>
							    </tr>
							    
							    <tr>
							    <td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Riesgo Poblaci&oacuten:</h5></td>
							    </tr>
							    <tr>
							    <td style="text-align:left;">
								<select id='riesgo_poblacion' name='riesgo_poblacion' class='campo_azul'>
								    <option value='none'>Seleccione la pobl. riesgo</option>
								    <option value='999'>Todas las poblaciones riesgo</option>
								    <option value='1'>Poblacion riesgo partos</option>
								    <option value='2'>Poblacion riesgo cancer cervix</option>
								    <option value='3'>Poblacion riesgo adulto</option>
								    <option value='4'>Poblacion riesgo cancer seno</option>
								    <option value='5'>Poblacion riesgo enfermedad mental</option>
								    <option value='6'>Poblacion riesgo infeccion trasmision sexual</option>
								    <option value='7'>Poblacion riesgo joven</option>
								    <option value='8'>Poblacion riesgo lepra</option>
								    <option value='9'>Poblacion riesgo vacunacion</option>
								    <option value='10'>Poblacion riesgo obesidad desnutricion</option>
								    <option value='11'>Poblacion riesgo gestacion</option>
								    <option value='12'>Poblacion riesgo victima maltrato</option>
								    <option value='13'>Poblacion riesgo violencia sexual</option>
								    <option value='14'>Poblacion riesgo menor 10 a&ntildeos</option>
								    <option value='15'>Poblacion riesgo odontologico</option>
								    <option value='16'>Poblacion riesgo sintomatico respiratorio</option>
								    <option value='17'>Poblacion riesgo edad gestacional nacer</option>
								    <option value='18'>Poblacion riesgo enfermedad renal</option>
								    <option value='19'>Poblacion riesgo enfermedad leishmaniasis</option>
								    <option value='20'>Poblacion riesgo control recien nacido</option>
								    <option value='21'>Poblacion riesgo enfermedad anemica</option>
								    <option value='22'>Poblacion riesgo problemas vision</option>
								    <option value='23'>Poblacion riesgo planificacion familiar</option>
								    <option value='24'>Poblacion riesgo enfermedad diabetica</option>
								    <option value='25'>Poblacion riesgo hipotiroidismo congenito</option>
								    <option value='26'>Poblacion riesgo enfermedad colesterol</option>
								    <option value='27'>Poblacion riesgo atencion por psicologia</option>
								</select>
							    </td>
							    </td>
							    </tr>
							    
							    <tr>
							    <td style="text-align:left;"><input type="button" value="Generar" class="btn btn-success color_boton" onclick="enviar_formulario();"/>
								<input type="reset" value="limpiar" class="btn btn-success color_boton"/></td>
							    </tr>
						
							</table>
							
						    </td>
						    <td>
							<table>
							    <tr>
								<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Regimen:</h5></td>
							    </tr>
							    <tr>
								<td style="text-align:left;">
								    <select id='regimen' name='regimen' class='campo_azul'>
									<option value='none'>Seleccione un regimen</option>
									<option value='C'>C (Contributivo)</option>
									<option value='S'>S (Subsidiado)</option>
									<option value='E'>E (Especial)</option>
									<option value='P'>P (Excepcion)</option>
									<option value='N'>N (No Asegurado)</option>
								    </select>
								</td>
							    </tr>
							    
							    <tr>
								<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Sexo:</h5></td>
							    </tr>
							    <tr>
								<td style="text-align:left;">
								    <!--<input type='text' id='regimen' name='regimen' class='campo_azul' placeholder='Regimen'/>-->
								    <select id='sexo' name='sexo' class='campo_azul'>
									<option value='A' selected>Ambos</option>
									<option value='M'>Masculino</option>
									<option value='F'>Femenino</option>
								    </select>
								</td>
							    </tr>
							</table>
						    </td>
						</tr>
						
						<tr>
						<td style="text-align:center;" colspan='100'>
							
							<div  id="mensaje">	
							    <label id="msj"><?php echo $_smarty_tpl->tpl_vars['mensaje_proceso']->value;?>
</label>
							</div>
							
							<div id='mensaje_div' style='text-align:center;'></div>
							<div id='resultado_definitivo' style='text-align:center;'></div>

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
