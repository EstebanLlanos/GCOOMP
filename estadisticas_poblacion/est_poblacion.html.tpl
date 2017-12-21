<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="est_poblacion.js?v=1.3"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="est_poblacion.css" rel="stylesheet" />
       <title>Estadisticas De la Poblaci&oacute;n</title>
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
						<p>Fase 1, carga de archivos de la poblacion para analizar<br>						
						</p>
						</td>
						</tr>

						<tr>
						<td style="text-align:left;">
						<input type="file" id='archivo_poblacion' name='archivo_poblacion'  />
						</td>
						</tr>

						<!--
						<tr>
						<td style="text-align:left;">
						<label>Separador</label>
						<input type="text" id='separador' name='separador' value='\t'  />
						</td>
						</tr>
						-->
						
						<tr>
						<td style="text-align:left;">
						<input type="submit" value="Cargar Poblacion" class="btn btn-success color_boton" />
						</td>
						</tr>

						<script>
							function generar_reporte()
							{
								document.getElementById('activa_generar').value="SI";
								document.getElementById('formulario').submit();
							}
						</script>

						<tr>
						<td style="text-align:left;">
						<select id="selector_years" name="selector_years">
							<option value='0'>Seleccione Un A&ntilde;o</option>
							<option value='todos'>TODOS</option>
							<option value='2012'>2012</option>
							<option value='2013'>2013</option>
							<option value='2014'>2014</option>
							<option value='2015'>2015</option>
							<option value='2016'>2016</option>
						</select>
						</td>
						</tr>

						<tr>
						<td style="text-align:left;">
						<select id="tipo_programa" name="tipo_programa">
							<option value='0'>Seleccione Un Programa</option>
							<option value='ORO'>ORO</option>
							<option value='PLAT'>PLATA</option>
							<option value='CLAS'>CLASICO</option>
							<option value='SAOR'>HCM</option>
							<option value='TRAD'>TRADICIONAL</option>
							<option value='CEM'>CEM</option>
						</select>
						</td>
						</tr>

						<tr>
						<td style="text-align:left;">
						<input type="hidden" name="activa_generar" id="activa_generar" value="NO">
						<select id="tipo_reporte" name="tipo_reporte">
							<option value='0'>Seleccione Un Tipo De Reporte</option>
							<option value='1'>Reporte 1: cuenta c32 year periodo, c31 mes periodo, c48 = c0|c5|c10|c12 TODOS LOS PROGRAMAS</option>
							<option value='1.2'>Reporte 1.2: cuenta c32 year periodo, c31 mes periodo, c48 = c0|c5|c10|c12, c37 especifica todos programa</option>
							<option value='1.5'>Reporte 1.5: cuenta c32 year periodo, c31 mes periodo, c48 = c0|c5|c10|c12, c37 programa</option>
							<option value='2'>Reporte 2: cuenta por sexo c17, grupo edad c35, c32 year periodo, c31 mes periodo, solo c48 diferentes entre si, c37 programa</option>
							<option value='3'>Reporte 3: cuenta por c26 parentesco , solo c48 diferentes entre si, c37 programa</option>
							<option value='4'>Reporte 4: Distribucion de la poblacion por sucursal y genero, solo c48 diferentes entre si, c37 programa</option>
							<option value='5'>Reporte 5: Distribucion de la poblacion por sucursal y grupo edad, solo c48 diferentes entre si, c37 programa</option>			
							<option value='6'>Reporte 6: Distribucion de la poblacion por regional y genero, solo c48 diferentes entre si, c37 programa</option>
							<option value='7'>Reporte 7: Distribucion de la poblacion por regional y grupo edad, solo c48 diferentes entre si, c37 programa</option>
							<option value='8'>Reporte 8: cuenta por sexo c17, grupo edad c35, c32 year periodo, c31 mes periodo, c38 regional, solo c48 diferentes entre si, c37 programa</option>
							<option value='9'>Reporte 9: cuenta por c26 parentesco , c38 regional, solo c48 diferentes entre si, c37 programa</option>
							<option value='10'>Reporte 10: cuenta por c44 plan tarifario, c17 sexo , solo c48 diferentes entre si, c37 programa</option>
							<option value='11'>Reporte 11: cuenta por c44 plan tarifario, c17 sexo , c38 regional,  solo c48 diferentes entre si, c37 programa</option>
							<option value='12'>Reporte 12: cuenta por c44 plan tarifario, c35 grupo edad, solo c48 diferentes entre si, c37 programa</option>
							<option value='13'>Reporte 13: cuenta por c44 plan tarifario, c35 grupo edad, c38 regional,  solo c48 diferentes entre si, c37 programa</option>
							<option value='14'>Reporte 14: cuenta por c44 tipo cotizante, c17 sexo , solo c48 diferentes entre si, c37 programa</option>
							<option value='15'>Reporte 15: cuenta por c44 tipo cotizante, c17 sexo , c38 regional, solo c48 diferentes entre si, c37 programa</option>
							<option value='16'>Reporte 16: cuenta por c44 tipo cotizante, c35 grupo edad , solo c48 diferentes entre si, c37 programa</option>
							<option value='17'>Reporte 17: cuenta por c44 tipo cotizante, c35 grupo edad, c38 regional, solo c48 diferentes entre si, c37 programa</option>
							<option value='18'>Reporte 18: antiguedad ingreso a CMP, solo c48 diferentes entre si, c37 programa</option>
							<option value='19'>Reporte 19: antiguedad ingreso a sistema, c35 grupo edad, c38 regional, solo c48 diferentes entre si, c37 programa</option>
						</select>
						<input type="button" value="Generar Reporte Seleccionado" class="btn btn-success color_boton" onclick="generar_reporte();"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:center;" colspan='100'>
							<div  id="mensaje">
								{$mensaje_proceso}
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
</html>