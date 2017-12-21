<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="conseapb2463.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="conseapb2463.css" rel="stylesheet" />
       <title>Consulta EAPB 2463</title>
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
						<h5 style="color:blue;">Interfaz para la consulta del estado de informaci&oacuten de los archivos ERC 2463(EAPB):</h5>
						<br>
						&#32
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						Nombre EAPB:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						{$campo_eapb}
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_area_geografica" value="1" onclick="mostrar_metodos_busqueda();" />Por area geografica</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_prestador" value="2" onclick="mostrar_metodos_busqueda();" />Por prestador asociado</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type="radio" name="tipo_busqueda" id="busq_periodo" value="3" onclick="mostrar_metodos_busqueda();" />Por periodo / fecha </td>
						</tr>
						
						<tr>
						<td>
						<br>
						&#32
						</td>
						</tr>
						
						<!--AREA GEOGRAFICA-->
						<tr>
						<td style="text-align:left;">
						
						<div id='mostrar_area_geografica' style="display:none;" >
						<table>
						<tr>
						<td style="text-align:left;">
						Area geografica:
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						{$campo_dpto} <div id='mostrar_mpio' style='display:none;'>{$campo_mpio}</div>
						</td>
						</tr>
						</table>
						</div>
						
						</td>
						</tr>
						
						<!--PRESTADORE-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_prestador' style="display:none;" >
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
						
						<!--FECHA DE REMISION-->
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_fecha_rem' style="display:none;">
						<table>
						<tr>
						<td style="text-align:left;">Fecha: </td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<input type='text' id='year_de_corte' name='year_de_corte' class='campo_azul' placeholder='AAAA' maxlength="4" onkeypress="return isNumberKey(event)"  />
						</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<div id='mostrar_mes' style='display:none;'>{$campo_fechas_corte}</div>
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