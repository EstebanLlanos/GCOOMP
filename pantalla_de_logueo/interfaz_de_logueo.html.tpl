<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="interfaz_de_logueo.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="interfaz_de_logueo.css" rel="stylesheet" />
       <title>Inicio de sesion</title>
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
									
									<div id="inicioSesion">
									<table>
										<tr>
										<td>
													 <div id="log">
														 <form  action="interfaz_de_logueo.php" method="post" id="logueo">
														 <table>
														 
															<tr>
															<td style="text-align:left;">
														   <h2 >Ingrese</h2>
														   </td>
														   </tr>
														   
														   <tr>
														   <td style="text-align:left;">
														   <input class="campo_azul" type="text" id="login" name="login" placeholder="Nombre de usuario" style="width:20%;min-width:230px;min-height:40px;"  autocomplete="off"/>
														   </td>
														   </tr>
														   
														   <tr>
														   <td style="text-align:left;">
														   <input class="campo_azul" type="password" id="password" name="password" placeholder="Contrasena" style="width:20%;min-width:230px;min-height:40px;"  autocomplete="off"/>
														   </td>
														   </tr>
														   
														   <tr>
														   <td style="text-align:left;">

														   <input type="submit" value="Ingresar"  id="btnlog" class="btn btn-success color_boton" />
														   </td>
														   </tr>
														   
														   <tr>
														   <td style="text-align:left;">

														   <div id="mensaje" style="color: red; font-size: 10px; width: 250px; font-weight: bold">{$error}</div>
														   </td>
														   </tr>
														   </table>
														 </form>

													 </div> 
										</td>
										</tr>
										<tr>
										<td style="white-space: nowrap;text-align:left;">
														
											<a id="clave_olvidada" href="../olvido_usuario/olvido_usuario.php" class="btn btn-success color_boton">&iquestOlvido su Usuario?</a>
														
											<a id="usuario_olvidado" href="../olvido_password/olvido_password.php" class="btn btn-success color_boton">&iquestOlvido su contrase&ntildea?</a>
											
										</td>
										</tr>
										<tr>
										<td>
													<div id="terminos" >  
															<h5 style="text-align:left"><a>Terminos de uso</a></h5>                            
													</div> 
										</td>
										</tr>
									</table>
									</div>
								
								</td>
							</tr>
						
				   </table>
						

					
					
					
									   
					</div>
					
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
            
    </body>
</html>