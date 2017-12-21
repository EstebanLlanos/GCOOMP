<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="edad_simple_y_sexo_RIPS3374.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="edad_simple_y_sexo_RIPS3374.css" rel="stylesheet" />
       <title>Edad y Sexo Simple</title>
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
					<form name="formulario" id="formulario"  method="post">
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h5 style="color:blue;">CONSULTA DE EDAD SIMPLE Y SEXO RIPS(EAPB):</h5>
						<br>
						&#32
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<b>Nombre EAPB:</b>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						{$campo_eapb}
						</td>
						</tr>
						
						<!--
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_area_geografica" value="1" onclick="mostrar_metodos_busqueda();" />Por area geografica</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_prestador" value="2" onclick="mostrar_metodos_busqueda();" />Por prestador asociado</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_periodo" value="3" onclick="mostrar_metodos_busqueda();" />Por periodo / fecha </td>
						</tr>
						-->
						
						<!--FECHA DE VALIDACION-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_fecha_rem' style="display:inline;">
						<table>
						<tr>
						<td style="text-align:left;"><b>Periodo De Informaci&oacuten:</b></td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type='text' id='year_de_validacion' name='year_de_validacion' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)" onchange="mostrar_meses();" onkeyup="mostrar_meses();" />
						
						<div id='mostrar_mes' style='display:inline;'>{$campo_fechas_corte}</div>
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<div id='div_selector_numeros_de_remision'>						
						</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						
						
						<!--AREA GEOGRAFICA-->
						<tr>
						<td style="text-align:left;">
						
						<div id='mostrar_area_geografica' style="display:inline;" >
						<table>
						    
						<tr>
						<td style="text-align:left;">
						<b>Area geografica:</b>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<i>
						Seleccione el area geografica, si desea restringir la busqueda por un departamento o incluso un municipio.<br>
						O deje este campo sin seleccionar para generar el reporte por todas las areas geoigraficas con las que haya relaci&oacuten.
						</i>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						{$campo_dpto} <div id='mostrar_mpio' style='display:inline;'>{$campo_mpio}</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--PRESTADORES-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_prestador' style="display:inline;" >
						<table>
						
						<tr>
						<td style="text-align:left;">
						<b>Prestador:</b><br>
						<i>(Seleccione un prestador para obtener resultados sobre ese prestador o deje sin seleccionar para traer <br>
						informaci&oacuten sobre todos los prestadores asociados a la EAPB)</i>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						{$campo_prestador}
						</td>
						</tr>
						
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--SELECTOR OBLIGATORIO TIPO CONSULTA O ESPECIALIDAD-->
						<tr>
						<td style="text-align:left;">
						<b>Seleccione el criterio de busqueda:</b>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="criterio_busqueda" id="por_tipo_consulta" value="cb_1_tipo_consulta" onclick="mostrar_especialidad_o_tipo_de_consulta();"/>Por Tipo de Actividad Consulta.
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type="radio" name="criterio_busqueda" id="por_especialidad" value="cb_2_especialidad" onclick="mostrar_especialidad_o_tipo_de_consulta();"/>Por Especialidad.						
						</td>
						</tr>
						
						<!--TIPO CONSULTA-->
						<tr id='tr_titulo_tipo_consulta' style="display: none">
						<td style="text-align:left;">
						<b>Tipo Actividad Consulta:</b>
						</td>
						</tr>
						
						<tr id='tr_selector_tipo_consulta' style="display: none">
						<td style="text-align:left;">
						{$campo_tipo_consulta}
						</td>
						</tr>
						
						<!--ESPECIALIDAD-->
						<tr id='tr_titulo_especialidad' style="display: none">
						<td  style="text-align:left;">
						<b>Especialidad Profesional:</b>
						</td>
						</tr>
						
						<tr id='tr_selector_especialidad' style="display: none">
						<td style="text-align:left;">
						{$campo_especialidad}
						</td>
						</tr>
						
						<!--SEXO A BUSCAR-->
						<tr>
						<td style="text-align:left;">
						<b>Sexo a consultar:</b>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<select id='sexo' name='sexo' class='campo_azul'>
						    <option value='none' selected='true'>Seleccione el Sexo Para Consultar</option>
						    <option value='M'>Masculino</option>
						    <option value='F'>Femenino</option>
						    <option value='A'>Ambos</option>
						</select>
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
								<label id="msj">{$mensaje_proceso}</label>
							</div>  

							<div id="grilla" style="display:none;">{$mostrarResultado}</div>
							
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
									<h4 id='titulo_mensaje_error' class="alert-heading" style="text-align: center">&iexclError al generar el reporte de calidad de datos!</h4>
									<p id='parrafo_mensaje_error' style="text-align: center"></p>

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