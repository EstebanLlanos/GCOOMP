<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="act_adaptable.js?v=2.916"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="act_adaptable.css" rel="stylesheet" />
       <title>Actualizar info. EAPB entidades administradoras</title>
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
						<h1>LLenado de datos para cualquier tabla</h1>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<p>
						(Excepto las tablas de detalles inconsistencia gioss_detalle_inconsistencias_rips,gioss_detalle_inconsistencia_r4725_sida_vih,gioss_detalle_inconsistecias_4505
						<br>
						Use la interfaz para actualizar detalles inconsistencia en cambio, tambi&eacute se recomienda usar las interfaces de actualizaci&oacuten de tablas correspondientes 
						y solo usar la interfaz de adaptable para tablas sencillas)
						</p>
						</td>
						</tr>
						<br>

						<tr>
						<td style="text-align:left;">
						<p>Actualizar y/o Insertar:</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<select class='campo_azul' id='upsert_select' name='upsert_select' >
							<option value='insert' selected>Insertar</option>
							<option value='update' >Actualizar</option>
							<option value='upsert' >UPSERT</option>
						</select>
						</td>
						</tr>

						<tr>
						<td style="text-align:left;">
						<p>Seleccione la tabla a llenar para evitar diligenciar los campos (OPCIONAL):</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<select class='campo_azul' id='tabla_pre_diligenciada' name='tabla_pre_diligenciada' onchange='prediligenciar_datos_tabla_seleccionada_para_cargar(this.value);'>
							<option value='0'>Seleccione una tabla (limpiar)</option>
							<option value='1' >Tabla Codigos Medicamentos CUM</option>
							<option value='1.5' >Tabla Codigos Medicamentos CUM(12 campos)</option>
							<option value='2' >Tabla Codigos Procedimientos CUPS</option>
							<option value='3' >Tabla Grupos Procedimientos CUPS</option>
							<option value='4' >Tabla Subgrupos Procedimientos CUPS</option>
							<option value='5' >Tabla Categoria Procedimientos CUPS</option>
							<option value='6' >Tabla Sistema Procedimientos CUPS</option>
							<option value='7' >Tabla Afiliados Medicina Prepagada</option>
							<option value='7.5' >Tabla Afiliados Medicina Prepagada Orden BD</option>
							<option value='7.7' >Tabla Afiliados Medicina Prepagada Mini</option>
							<option value='8' >Tabla Valores Permitidos 4505 Consultas Cruces Razonabilidad</option>
							<option value='8.1' >Tabla Valores Permitidos 0247 Consultas Cruces Razonabilidad</option>
							<option value='8.2' >Tabla Valores Permitidos 0123 Consultas Cruces Razonabilidad</option>
							<option value='8.3' >Tabla Valores Permitidos 2463 Consultas Cruces Razonabilidad</option>							
							<option value='8.4' >Tabla Valores Permitidos 4725 Consultas Cruces Razonabilidad</option>
							<option value='9' >Tabla prestadores servicios salud</option>
							<option value='10' >Tabla gioss entidades sector salud</option>
							<option value='11' >Tabla 4505 Consultas</option>
							<option value='12.1' >Tabla gioss_cancer_ips_radioterapia</option>
							<option value='12.2' >Tabla gioss_cancer_ips_quimioterapia</option>
							<option value='12.3' >Tabla gioss_cancer_ips_cirugia_reconstructiva</option>
							<option value='12.4' >Tabla gioss_cancer_ips_cirugia</option>
							<option value='12.5' >Tabla gioss_cancer_ips_trasplante</option>
							<option value='12.6' >Tabla gioss_cancer_ips_conformacion_dx</option>
							<option value='12.7' >Tabla gioss_cancer_ips_cuidado_paliativo</option>
							<option value='12.8' >Tabla gioss_cancer_ips_nutricion</option>
							<option value='12.9' >Tabla gioss_cancer_ips_psiquiatria</option>
						</select>
						</td>
						</tr>
						
						
						<tr>
						<td style="text-align:left;">
						<p>Ingrese el nombre de la tabla a llenar:</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" name="nombre_tabla" id="nombre_tabla" class='campo_azul' placeholder="nombre tabla"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<p>LLave o llaves primarias(Si son varias separadas por comas):</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="text" name="llaves" id="llaves" class='campo_azul' placeholder="llaves" style="width:300px;"/>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<p>Ingrese el nombre de las columnas de la tabla(En este orden deberan estar los datos para que se asignen a su columna correspondiente):</p>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<textarea name="nombres_columnas_tablas" id="nombres_columnas_tablas" class='campo_azul' placeholder="nombre columnas" style="width:500px;height: 200px;"></textarea> 
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
								{$mensaje_proceso}
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
</html>