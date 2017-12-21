<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="facturas_radicadas.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="facturas_radicadas.css" rel="stylesheet" />
       <title>facturas_radicadas</title>
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
						<input type="hidden" id="oculto_envio" name="oculto_envio" value="no_envio" />
						</td>
						</tr>  
			    
			    
						<tr>
						<td style="text-align:left;">
						<h4 style="color:black;">
						Se&ntildeor usuario al momento de efectuar el cargue de las facturas radicadas para el periodo,<br>
						verifique que cumple con la estructura definida, las especificaci&oacuten puede ser consultada en<br>
						la opci&oacuten "NORMATIVIDAD".
						</h4>
						</td>
						</tr>
						
						<!--
						<tr>
						<td style="text-align:left;">
						La idea es que la EAPB envie un TXT con la informaci&oacuten o si se instala el aplicativo en la EAPB, <br>
						pueda extraer la informaci&oacuten del sistema de informaci&oacuten.
						</td>
						</tr>
						-->
						
						
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
						<!--
						<tr>
						<td style="text-align:left;">A&ntildeo:</td>
						</tr>
						<tr>
						<td style="text-align:left;"><input type='text' id='year_para_periodo' name='year_para_periodo' class='campo_azul' placeholder='AAAA' onkeypress="return isNumberKey(event)" maxlength="4"/> </td>
						</tr>
						
						<tr>
						<td style="text-align:left;">Periodo:</td>
						</tr>
						<tr>
						<td style="text-align:left;position:relative;">{$campo_periodo}<div id='fecha_corte_periodo_actual' name='fecha_corte_periodo_actual' style="position:absolute;left:25%;top:5%"></div></td>
						</tr>
						-->
						<!--
						<tr>
						<td style="text-align:left;">
						<h5>El archivo de facturas radicadas a cargar debera poseer<br>
						la siguiente estructura para cada fila(cada fila es una factura) separadas por coma:<br>
						</h5>
						<p>
						Codigo de la EAPB,<br>
						Codigo del Prestador de Servicios,<br>
						Tipo de Identificacion del Prestador,<br>
						Numero de Identificacion del Prestador,<br>
						Tipo de Regimen,<br>
						Numero de la factura,<br>
						Valor de la Factura  Radicada(numero decimal separado por punto),<br>
						Fecha de Radicacion(dd/mm/aaaa),<br>
						Fecha de Factura(dd/mm/aaaa),<br>
						Modalidad de Contratacion(Uno de los Codigos: 01, 02, 03, 04, 05 ,06)<br>
						</p>
						
						</td>
						</tr>
						-->
						
						<tr>
						    <td style="text-align:right;width:40.05%;">
							<table id='tabla_seleccion_archivos' style='display:inline;'>
							
								<!--hidden validar archivo, (debe contener la sigla)-->
								<input type='hidden' name='FACTURAS_hidden' id='FACTURAS_hidden' value='' />
								<!--fin hidden validar archivo-->
								
								
								<!--ARCHIVO ARCHIVO FACTURAS RADICADAS-->
								<tr>
								<td style="text-align:left;width:25%;">
								<h5>CARGAR ARCHIVO DE <br>FACTURAS RADICADAS:</h5>
								</td>
								
								<td style="position:relative;text-align:left;width:25%;">
								
								<table>
								<tr>
								
								<td style="position:absolute;left:-45%;top:5%;width:5%;">
								<div class="upload"><input type="file" id='facturas_file' name='facturas_file' style="cursor:pointer;" onchange="obtener_nombre_file_upload('facturas_file','nombre_archivo_facturas');" onclick="obtener_nombre_file_upload('facturas_file','nombre_archivo_facturas');" /></div>
								</td>
								
								<td style="position:absolute;left:-20%;top:15%;">
								<div id='nombre_archivo_facturas'></div>
								</td>
								
								</tr>
								</table>
								
								</td>
								</tr>
								<!--FIN ARCHIVO FACTURAS RADICADAS-->
						
							</table>
						</td>
						</tr>
						
						
						
						<tr>
						<td style="text-align:left;">
						<input type="button" class="btn btn-success color_boton" value="Cargar" id='cargar_factura' name='cargar_factura' onclick="cargar();" />
						<input type="reset" class="btn btn-success color_boton" value="Limpiar" id='limpiar_factura' name='limpiar_factura' />
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