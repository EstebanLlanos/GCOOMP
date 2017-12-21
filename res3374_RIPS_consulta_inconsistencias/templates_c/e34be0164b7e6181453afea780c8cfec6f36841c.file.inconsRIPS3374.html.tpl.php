<?php /* Smarty version Smarty-3.1.17, created on 2014-09-08 13:32:34
         compiled from "inconsRIPS3374.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:149695377ad8b77b988-81162043%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e34be0164b7e6181453afea780c8cfec6f36841c' => 
    array (
      0 => 'inconsRIPS3374.html.tpl',
      1 => 1409689028,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '149695377ad8b77b988-81162043',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5377ad8c1cab47_83286236',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'campo_fechas_corte' => 1,
    'campo_prestador' => 1,
    'campo_eapb' => 1,
    'mensaje_proceso' => 1,
    'mostrarResultado' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5377ad8c1cab47_83286236')) {function content_5377ad8c1cab47_83286236($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="inconsRIPS3374.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="inconsRIPS3374.css" rel="stylesheet" />
       <title>Consulta Inconsistencias RIPS</title>
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
					<form name="formulario" id="formulario"  method="post">
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h5 style="color:blue;">Seleccione la forma en que desea buscar las inconsistencias de los archivos RIPS 3374:</h5>
						<br>
						&#32
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_numero_secuencia" value="1" onclick="mostrar_metodos_busqueda();" />Por Numero de secuencia</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_numero_remision" value="2" onclick="mostrar_metodos_busqueda();" />Por Numero de remisi&oacuten del archivo CT </td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_fecha_remision" value="3" onclick="mostrar_metodos_busqueda();" />Por fecha de remisi&oacuten </td>
						</tr>
						
						<tr>
						<td>
						<br>
						&#32
						</td>
						</tr>
						
						<!--NUMERO DE SECUENCIA-->
						<tr>
						<td style="text-align:left;">
						
						<div id='mostrar_num_seq' style="display:none;" >
						<table>
						<tr>
						<td style="text-align:left;">
						Numero de secuencia:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" class="campo_azul" id="numero_secuencia" name="numero_secuencia" placeholder="numero de secuencia" onkeypress="return isNumberKey(event)"/>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--NUMERO DE REMISION-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_num_rem' style="display:none;" >
						<table>
						<tr>
						<td style="text-align:left;">
						Numero de remisi&oacuten del archivo CT:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" class="campo_azul" id="numero_remision" name="numero_remision" placeholder="numero de remision" maxlength="6" onkeypress="return isNumberKey(event)"  onchange="consultar_numeros_secuencia_para_el_nombre();" onkeyup="consultar_numeros_secuencia_para_el_nombre();"/>
						</td>
						
						<td style="text-align:left;">
						<div id='div_selector_numeros_secuencias' style="color:red;">						
						</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--FECHA DE REMISION-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_fecha_rem' style="display:none;">
						<table>
						<tr>
						<td style="text-align:left;">Fecha de remisi&oacuten: </td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)" onchange="consultar_numeros_remision();" onkeyup="consultar_numeros_remision();"/> <?php echo $_smarty_tpl->tpl_vars['campo_fechas_corte']->value;?>
</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<div id='div_selector_numeros_de_remision' style="color:red;">						
						</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						Nombre Prestador:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<?php echo $_smarty_tpl->tpl_vars['campo_prestador']->value;?>

						</td>
						</tr>
						
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
						
						<input type="hidden" id="consecutivo" name="consecutivo" value="" />
												
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Consultar" onclick="enviar();" class="btn btn-success color_boton"  /> <input type="reset" value="limpiar" class="btn btn-success color_boton"/>
						</td>
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
							
							<div id="grilla" style="display:none;"><?php echo $_smarty_tpl->tpl_vars['mostrarResultado']->value;?>
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
