<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="repoblig4505.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="repoblig4505.css" rel="stylesheet" />
       <title>Reporte Obligatorio 4505</title>
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
					<form action="repoblig4505.php" method="post" id="reporte_obligatorio" name="reporte_obligatorio">
					<table>
					    
					<tr>
					<td style="text-align:left;"><h5>Generaci&oacuten del reporte obligatorio PyP 4505:</h5></h5></td>
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
					<td style="text-align:left;">Fecha de corte:</td>
					</tr>
					<tr>
					<td style="text-align:left;"><input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA'/> {$campo_fechas_corte}</td>
					</tr>
					
					<tr>
					<td style="text-align:left;">Periodo:</td>
					</tr>
					<tr>
					<td style="text-align:left;">{$campo_periodo}</td>
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
					<td style="text-align:left;">Nombre EAPB:</td>
					</tr>
					<tr>
					<td style="text-align:left;">{$campo_eapb}</td>
					</td>
					</tr>
					
					<tr>
					<td style="text-align:left;"><input type="button" value="Generar" class="btn btn-success color_boton" onclick="enviar_formulario();"/> <input type="reset" value="limpiar" class="btn btn-success color_boton"/></td>
					</tr>
					
					<tr>
					<td style="text-align:left;">
						<div  id="mensaje">

							<label id="msj">{$mensaje_proceso}</label>

						</div>  


						<div id="grilla" style="display: {$mostrarResultado}"> 

						
						
						 <input type="button" value="Descargar Reporte Obligatorio" id="Descargar" class="btn btn-success color_boton" onclick="download_reporte('{$resultadoDefinitivo}');" />  


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