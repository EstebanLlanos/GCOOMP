<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="consulta_reporte_obligatorio4725.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="consulta_reporte_obligatorio4725.css" rel="stylesheet" />
       <title>consulta_reporte_obligatorio 4725</title>
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
					<form name="formulario" id="formulario" action="" method="post">
						<table>
						
						<tr>
						<td style="text-align:left;">
						<h5 style="color:blue;">Consultar el reporte obligatorio de los archivos VIH 4725:</h5>
						<br>
						&#32
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<b>Tipo de consulta:</b>
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						Detallado <input type="radio" id='tipo_consulta_radio_detallado' name='tipo_consulta_radio' value='detallado' checked='true'/>
						Consolidado <input type="radio" id='tipo_consulta_radio_consolidado' name='tipo_consulta_radio' value='consolidado' />						
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Estado de la informaci&oacuten:</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<select id='selector_estado_info' name='selector_estado_info' class='campo_azul' >
						<option value='none'>Seleccione el estado de la informaci&oacuten a buscar</option>
						<option value='validada'>Informaci&oacuten validada</option>
						<option value='rechazada'>Informaci&oacuten rechazada</option>
						</select>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">A&ntildeo de corte(detallado)/A&ntildeo Generaci&oacuten Reporte(Consolidado):</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' onchange='escribiendo_year_corte();' onkeyup='escribiendo_year_corte();' onkeypress="return isNumberKey(event)"/> </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Periodo:</td>
						</tr>
						<tr>
						<td style="text-align:left;position:relative;">{$campo_periodo}<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%"></div></td>
						</tr>
						
						
						
						<tr>
						<td style="text-align:left;">Nombre EAPB:</td>
						</tr>
						<tr>
						<td style="text-align:left;">{$campo_eapb}</td>
						</td>
						</tr>					
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

							<div id="grilla" style="display:none;"> {$mostrarResultado}	</div>
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