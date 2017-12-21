<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="conseapb4505.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="conseapb4505.css" rel="stylesheet" />
       <title>Consulta 4505 EAPB</title>
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
						<td style="text-align:left;">Departamento</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						{$selector_departamento}
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Municipio</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						{$selector_municipio}
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
						
						<input type="hidden" id="index_inicio" name="index_inicio" value="0"/>
						<input type="hidden" id="index_fin" name="index_fin" value="0"/>
						
						
						
						<tr>
						<td style="text-align:left;">Numero de Resultados:</td>
						</tr>
						<tr>
						<td style="text-align:left;">
						<select id='rango_resultados' name='rango_resultados' class='campo_azul' >
						<option value='none'>Seleccione el rango de resultados</option>
						<option value='10'>10</option>
						<option value='20'>20</option>
						<option value='30'>30</option>
						<option value='40'>40</option>
						<option value='50'>50</option>
						</select>
						</td>
						</tr>
						
						<tr>
						<td style="text-align:left;">
						<input type="button" value="Consultar" onclick="enviar();" class="btn btn-success color_boton"  /> <input type="reset" value="limpiar" class="btn btn-success color_boton"/>
						</td>
						</tr>
						
						<tr>
						<td colspan='100'>
							<div  id="mensaje" >
								{$mensaje_proceso}
							</div>  

							<div id="grilla" style="display: {$mostrarResultado}"> 	</div>
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