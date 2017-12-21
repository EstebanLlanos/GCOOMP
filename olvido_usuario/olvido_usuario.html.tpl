<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">			
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>  		
        <script type="text/javascript" src="olvido_usuario.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="olvido_usuario.css" rel="stylesheet" />
       <title>Olvido Usuario</title>
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
						<form action="olvido_usuario.php" method="post" id='formulario_olvido'>
						<table>
						<tr>
						<td  style="text-align:left;">
						<p>
						Registre el Tipo y Numero de Su  Documento de Identidad, tambi&eacuten el codigo de la entidad a la cual su usuario esta asociado 
						<br>(Recuerde que debe corresponder al registrado en la base de datos GIOSS)
						</p>
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">{$tipo_id_selector} Tipo de Identificaci&oacuten </td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="text" id="identificacion" name="identificacion" placeholder="identificacion" class="campo_azul"/> Identificaci&oacuten </td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="text" id="codigo_entidad" name="codigo_entidad" placeholder="codigo de la entidad de salud asociada" class="campo_azul"/> codigo de la entidad de salud asociada </td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="text" id="fecha_nacimiento" name="fecha_nacimiento" placeholder="fecha_nacimiento" class="campo_azul"/> fecha de nacimiento (yyyy-mm-dd) </td>
						</tr>
						<tr >
						<td style="text-align:left;"><input type="submit" value='Enviar a E-mail' class="btn btn-success color_boton"/></td>
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