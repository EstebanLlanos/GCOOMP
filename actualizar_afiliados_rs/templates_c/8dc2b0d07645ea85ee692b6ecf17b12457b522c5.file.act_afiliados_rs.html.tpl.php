<?php /* Smarty version Smarty-3.1.17, created on 2014-07-18 21:49:22
         compiled from "act_afiliados_rs.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1862953a04d83d43705-42567886%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8dc2b0d07645ea85ee692b6ecf17b12457b522c5' => 
    array (
      0 => 'act_afiliados_rs.html.tpl',
      1 => 1405710896,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1862953a04d83d43705-42567886',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_53a04d83d844a5_15259704',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
    'mensaje_proceso' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53a04d83d844a5_15259704')) {function content_53a04d83d844a5_15259704($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="act_afiliados_rs.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="act_afiliados_rs.css" rel="stylesheet" />
       <title>Actualizar info. Afiliados</title>
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
						<h1>LLenado de datos para la tabla de afiliados regim&eacuten subsidiado</h1>
						</td>
						</tr>
						
						
						<tr>
						<td style="text-align:left;">
						<p>Nombre de la tabla afiliados:</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" name="nombre_tabla" id="nombre_tabla" style="width:300px;" class='campo_azul' placeholder="nombre tabla" value="gioss_afiliados_regimen_subsidiado" readonly />
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<p>Llaves primarias de la tabla afiliados:</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" name="llaves" id="llaves" class='campo_azul' style="width:300px;" placeholder="llaves" value="codigo_eapb,tipo_id_afiliado,id_afiliado"  readonly />
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<p>Columnas de la tabla(En este orden deberan estar los datos para que se asignen a su columna correspondiente):</p>
						</td>
						</tr>
						
						<!--
						  codigo_eapb character varying(24) NOT NULL,
						  tipo_id_eapb character varying(2) NOT NULL,
						  numero_id_eapb character varying(30),
						  tipo_id_afiliado character varying(2) NOT NULL,
						  id_afiliado character varying(24) NOT NULL,
						  primer_nombre character varying(320) NOT NULL,
						  segundo_nombre character varying(320),
						  primer_apellido character varying(320) NOT NULL,
						  segundo_apellido character varying(320),
						  codigo_tipo_regimen character varying(1) NOT NULL,
						  cod_tipo_afiliado character varying(1) NOT NULL,
						  cod_tipo_poblacion_especial character varying(2) NOT NULL,
						  cod_estado_afiliado character varying(2) NOT NULL,
						  cod_nivel_sisben character varying(1) NOT NULL,
						  sexo character varying(1) NOT NULL,
						  fecha_nacimiento date NOT NULL,
						  cod_mpio character varying(320) NOT NULL,
						  cod_dpto character varying(320) NOT NULL,
						  fecha_ultima_actualizacion date NOT NULL,
						  cod_zona character varying(320)
						  
						  Codigo EPS;TI EAPB;NI AEPB;Tipo Ident;Numero Identi;Regimen;Tipo Afiliado;Tipo Pblacio Especial;Estado;codigo sisben;1er Apellido;2do Apellido;1er Nombre ;2do Nombre;Sexo;Fecha Nac;Depto;Mpio;Zona

						-->
						
						<tr>
						<td style="text-align:left;">
						<textarea name="nombres_columnas_tablas" style="width:500px;height:150px;" id="nombres_columnas_tablas" class='campo_azul' readonly >
						codigo_eapb,tipo_id_eapb,numero_id_eapb,tipo_id_afiliado,id_afiliado,codigo_tipo_regimen,cod_tipo_afiliado,cod_tipo_poblacion_especial,cod_estado_afiliado,cod_nivel_sisben,primer_apellido,segundo_apellido,primer_nombre,segundo_nombre,sexo,fecha_nacimiento,cod_dpto,cod_mpio,cod_zona
						</textarea>
						</td>
						</tr>
						
						
						
						<tr>
						<td style="text-align:left;">
						<p>El archivo tomara los campos separado por comas(,) para indicar las columnas:<br>
						por lo tanto los campos deber&aacuten estar organizados de acuerdo a la forma en que inserto los campos (NOTA: un numero diferente de comas evitara que se suba el archivo)<br>						
						</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="file" id='archivo_info' name='archivo_info'  />
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="submit" value="Actualizar" class="btn btn-success color_boton" />
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
