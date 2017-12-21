<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>
	<script src="../librerias_externas/jquery_y_ui_actualizado/jquery-ui.js"></script>
	<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">
        <script type="text/javascript" src="repobligCANCER0247.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="repobligCANCER0247.css" rel="stylesheet" />
       <title>Reporte Obligatorio CANCER 0247</title>
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
					<td style="text-align:left;">
					<form action="" method="post" id="reporte_obligatorio" name="reporte_obligatorio" enctype= "multipart/form-data">
					<table>
					    <tr>
						<td style="text-align:left;" colspan=2>
						    <h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Generaci&oacuten del reporte obligatorio cancer 0247:</h4>
						</td>
					    </tr>
					    
					    <tr>
						<td style="text-align:left;" colspan=2>
						    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Estado de la informaci&oacuten:</h5>
						</td>
						</tr>
					    <tr>
						<td style="text-align:left;" colspan=2>						    
						    <h6 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Se consolidar&aacuten los registros validados de forma exitosa dentro de el periodo de tiempo seleccionado</h6></td>
						    <input type='hidden' id='selector_estado_info' name='selector_estado_info' value='validada'/> 
						</select>
						</td>
					    </tr>
					    
					    <tr>
						<td>
						    <table>
							
							
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">A&ntildeo de corte:</h5>
							</td>
							</tr>
							<tr>
							<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' onchange='escribiendo_year_corte();' onkeyup='escribiendo_year_corte();' onkeypress="return isNumberKey(event)"/> {$campo_fechas_corte}</td>
							</tr>
							
							
							
							<!--
							<tr>					
							<td style="text-align:left;">Nombre Prestador:</td>					
							</tr>
							<tr>					
							<td style="text-align:left;">{$campo_prestador}</td><td style="text-align:left;">					
							</tr>
							-->
							
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Nombre EAPB:</h5>
							</td>
							</tr>
							<tr>
							<td style="text-align:left;">{$campo_eapb}</td>
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Tipo Periodo:</h5></td>
							</tr>
							<tr>
							<td style="text-align:left;position:relative;">
							    <select id='tipo_tiempo_periodo' name='tipo_tiempo_periodo' class='campo_azul' onchange="cambio_tipo_tiempo_periodo();">
								<option value='anual' >Anual</option>
								<option value='semestral' >Semestral</option>
								<option value='mensual' selected>Mensual</option>
							    </select>
							</td>
							</tr>
							
							<tr>
							<td style="text-align:left;">
							    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo:</h5>
							</td>
							</tr>
							<tr>
							    <td style="text-align:left;position:relative;">
								<div id='div_selector_periodo'>{$campo_periodo}</div>
								<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%">						    
								</div>
							    </td>
							</tr>
							
							<tr>
							<td style="text-align:left;"><input type="button" value="Generar" class="btn btn-success color_boton" onclick="enviar_formulario();"/> <input type="reset" value="limpiar" class="btn btn-success color_boton"/></td>
							</tr>
					
						    </table>
						    <td>
							<div  id="mensaje">
	
								<label id="msj">{$mensaje_proceso}</label>
								{$resultado_definitivo}
							</div>
						    </td>
						</td>
					    </tr>
				
					    <tr>
						<td style="text-align:left;">
							
							
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
	
	
							<div id="grilla" style="display: {$mostrarResultado}"> 
	
							
							
							 
	
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
</html>