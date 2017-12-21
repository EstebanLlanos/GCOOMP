<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">   
		<link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>
        <script type="text/javascript" src="fixer4505.js?v=1.21"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="fixer4505.css" rel="stylesheet" />
       <title>Reparacion 4505</title>
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
					<td >
					<form id='formulario_carga_4505' name='formulario_carga_4505'  method='post' enctype= "multipart/form-data">
					<table>
					    <tr>
						<td id='columna_izq' style="text-align:left;width:40%;">
					    <table>
						
					
					<tr>
					<td style="text-align:left;">
					<h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Reparaci&oacuten archivo 4505 PyP</h4>
					</td>
					</tr>
					
					    
					{$proveniente_de_prestador_o_eapb}
					
					<tr>
					<td style="text-align:left;">
					    <h5 id='sub_titulo_entidad_1' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Entidad Que Efect&uacutea el Cargue:</h5>
					    <h5 id='sub_titulo_entidad_1_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;">Entidad a La Cual se Efect&uacutea el Reporte:</h5>
					    <h5 id='sub_titulo_entidad_3_oculto' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;display: none;position: relative;top:50%;">Se Reporta al Ministerio de Salud.</h5>
					</td>
					</tr>
					<tr>
					<td style="text-align:left;">
					    <div id='entidad_1'>{$campo_prestador}</div>	
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
					    <div id='entidad_2'>{$campo_eapb}</div>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Numero de remisi&oacuten:</h5></td>
					</tr>
					<tr>
					<td style="text-align:left;"><input type='text' style='width:215px;' id='numero_remision' name='numero_remision'  class='campo_azul' placeholder='Numero de remision' onkeyup="validar_antes_seleccionar_archivos();"/></td>
					</tr>
					
					
					
					<tr>
					<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo de tiempo:<br><b>(Nota: cuando se selecciona el periodo mensual efectuara los calculos contra la fecha de corte del trimestre)</b></h5></td>
					</tr>
					<tr>
					<td style="text-align:left;">
					    <select class='campo_azul' style='width:230px;' id='tipo_periodo_tiempo' name='tipo_periodo_tiempo' onchange='acomodar_tipo_periodo_tiempo(this);validar_antes_seleccionar_archivos();'>
						<option value='mensual' selected>Mensual</option>
						<option value='trimestral'>Trimestral</option>						
					    </select>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Fecha de remisi&oacuten(mm/dd/aaaa):</h5></td>
					</tr>
					<tr>
					<td style="text-align:left;">
					    <div id='div_fecha_remision'>
					    <input type="text" style='width:215px;' name="fecha_remision" id="fecha_remision" placeholder="clic aqui para seleccionar una fecha" class='campo_azul' onchange="funciones_fecha_restriciones(this);validar_antes_seleccionar_archivos();"/>
					    </div>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo:</h5></td>
					</tr>
					<tr>
					<td style="text-align:left;">
					    <div id='mod_campo_periodo'>
						{$campo_periodo}
					    </div>
					</td>
					</tr>
					
					<tr>
					    <td style="text-align:left;">
						<input type="button" value="Reparar" onclick="cargar4505();" class="btn btn-success color_boton"  />
						<input type="reset" value="Limpiar Campos" class="btn btn-success color_boton" />
					    </td>
					</tr>
					
					
					</table>
					</td>
						
					<td style="padding-top:0px;">
					    <table id='tabla_selector_y_resultados'>
						<!--hidden validar archivo, (debe contener la sigla)-->
						<input type='hidden' name='SGD280RPED_hidden' id='SGD280RPED_hidden' value='' />
						<!--fin hidden validar archivo-->
						<tr>
						    <td>
							<table>
							    <tr>
								<td style="text-align:left;">
								    <h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Archivo detalle 4505:</h5>
								    <div id='nombre_archivo' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;font-size: large !important;">no se ha subido un archivo</div>
								</td>						    
							    </tr>
							    <tr>
								<td style="text-align:left;">
								    <div class="upload">
									<input type="file" id='detalle4505' name='detalle4505' style="cursor:pointer;" onchange="obtener_nombre_file_upload();verificar_nombre_archivo(this.value,'SGD280RPED','nombre_archivo');" onclick="obtener_nombre_file_upload();verificar_nombre_archivo(this.value,'SGD280RPED','nombre_archivo');" />
								    </div>
								</td>
							    </tr>
							</table>
						    </td>
						</tr>
						<tr>
						    <td>
							<div  id="mensaje">
								{$mensaje_proceso}
							</div>  
						    </td>
						</tr>
					    </table>
					</td>
					</tr>
					
					<tr>		 
					<td colspan='100' style="text-align:center;">
					    <div id='advertencia'></div>
					</td>
					</tr>
					
					<!--html para mensajes error y exito-->
					<tr>		 
					<td colspan='100' style="text-align:center;">   
					
					    <div id='div_mensaje_error' class="sec2" style="width:90%;display:{$mostrarMsj}">
			
							<div class="alert alert-block alert-error fade in">
			
								<a class="close" data-dismiss="alert" href="#">&times</a>
								<h4 class="alert-heading">&iexclSe Ha Completado El Proceso De Reparacion Del Archivo!</h4>
								<p id='parrafo_error'>{$mensajeError}</p>
			
							</div>
			
						</div>
			
						<div id='div_mensaje_exito' class="sec2" style="width:90%;display:{$mostrarMsj2}">
			
							<div  class="alert-success alert-block alert-success fade in">
			
								<a class="close" data-dismiss="alert" href="#">&times</a>
								<h4 class="alert-heading">&iexclSe Ha Completado El Proceso De Reparacion Del Archivo!</h4>
								<p id='parrafo_exito'>{$mensajeExito}</p>
			
							</div>
			
						</div>
					</td>
					</tr>
					<!--fin html para mensajes error y exito-->
					
					<!--ADICIONAR ELEMENTOS ARRIBA-->
					<tr style="visibility: hidden;">
					<td style="text-align:left;"><h5 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Fecha de corte:</h5></td>
					</tr>
					<tr style="visibility: hidden;">
					<td style="text-align:left;">
					    <input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA'/>
					    <div id='mod_campo_fechas_corte'>
						{$campo_fechas_corte}
					    </div>
					</td>
					</tr>
					
					
					
					
					<input type='hidden' name='consecutivo' id='consecutivo' value='' />
					
					</table>
					</form>
					</td>
				</tr>
				
				 
					 
					
				
			</table>
		 </div>
         </td>
		 </tr>  

				 
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
			    <button class="btn" onclick="empezar_reparacion();" style="display: none;" id='boton_continuar_modal' >Continuar</button>
				<button class="btn" data-dismiss="modal" aria-hidden="true">Cerrar</button>
			</div>
		</div>  
			
			
			
    </body>
</html>