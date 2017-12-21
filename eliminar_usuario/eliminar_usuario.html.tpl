<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="eliminar_usuario.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="eliminar_usuario.css" rel="stylesheet" />
       <title>Eliminar Usuarios del sistema</title>
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
							<div id='menu_div'>{$menu}</div>
							</td>
							<td id='nombre'>
							{$nombre}
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
						Tipo Identificaci&oacuten
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">{$selector_tipo_id} </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						Numero Identificaci&oacuten
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;"><input type='text' id='identificacion' name='identificacion' class='campo_azul'/> </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						Codigo entidad de salud Asociada * 
						</td>
						</tr>
												
						<tr>
						<td style="text-align:left;">
						<input type='text' id='cod_entidad_salud_0' name='cod_entidad_salud_0' autocomplete="off" autocorrect="off" class='campo_azul' placeholder='Ingrese el codigo de la entidad de salud a asociar' autocomplete='off' /> 
						</td>
						</tr>
						
						<input type="hidden" id="index_inicio" name="index_inicio" value="0"/>
						<input type="hidden" id="index_fin" name="index_fin" value="0"/>
						
						<input id='rango_resultados' name='rango_resultados' type='hidden' value='10' />
						
						<input id='accion' name='accion' type='hidden' value='buscar' />
						
						<input id='email' name='email' type='hidden' value='' />
						
						<input id='fecha_expiracion_hid' name='fecha_expiracion_hid' type='hidden' value='' />
						
						<tr>
						<td colspan='100' style="text-align:left;"><input type='button' value='Buscar usuario(s)' class="btn btn-success color_boton" onclick="enviar();" /></td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<div id="grilla" style="display:none"> 
						
						<table>
						<tr>
						<td style="text-align:left;">
						Estado Usuario *
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						{$estado_usuario}
						</td>
						</tr>
						
						
						<tr>
						<td style="text-align:left;">
						Fecha Expiraci&oacuten(mm/dd/yyyy)*
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" id="fecha_expiracion_text_box" name="fecha_expiracion_text_box" class="campo_azul" placeholder="clic aqui para seleccionar una fecha" />
						</td>
						</tr>
						
						
						
						<tr>
						<td colspan='100' style="text-align:left;"><input type='button' value='Cambiar estado de  activacion del usuario' class="btn btn-success color_boton" onclick="cambiar_estado();" /></td>
						</tr>
						
						</table>
						</div>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
							<div  id="mensaje">
								{$mensaje_proceso}
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
</html>