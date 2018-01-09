<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="consulta_multicampo_0123.js?v=1.600"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="consulta_multicampo_0123.css?v=1.0" rel="stylesheet" />
       <title>Consulta Multi Campo 0123</title>
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
						    	<h4 style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Analisis para 0123 Hemofilia</h4>
						    </td>
					    </tr>

					    <tr>
							<td style="text-align:left;">
								<h5 id='sub_titulo_1' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Consulta Cruces:</h5>
							</td>
						</tr>

						

					    <tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_2' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Rango de la consulta:</h6>
							</td>
						</tr>					    

						<tr>
							<td style="text-align:left;">
								<select id='selector_rango_conteo' name='selector_rango_conteo' class='campo_azul' onchange='mostrar_opciones_correspondientes_al_caso()' >
									<option value='none' selected>Seleccione el rango del conteo</option>
									<!--<option value='all_allips'>Todos los Campos, todas las IPS del archivo</option>									
									<option value='all_oneips'>Todos los Campos, una IPS especifica del archivo</option>
									<option value='specific_allips'>Campo Especifico, todas las IPS del archivo</option>
									<option value='specific_oneips'>Campo Especifico, una IPS especifica del archivo</option>-->							
									<option value='cross_allips' selected>Consulta Multi Campo, todas las IPS del archivo</option>
									<option value='cross_oneips'>Consulta Multi Campo, una IPS especifica del archivo</option>
									<option value='clone'>Descarga de copia del archivo almacenado para las consultas</option>
								</select>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_3' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">A&ntilde;o de Corte:</h6>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
							    <input type="text" id="year_de_corte" name="year_de_corte" class="campo_azul" placeholder="AAAA" onkeypress="return isNumberKey(event)" onchange='consultar_archivos_subidos_para_periodo_year();' maxlength="4" data-original-title="" title="">
							</td>
						</tr>
						


						<tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_4' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Tipo de Periodo Reportado:</h6>
							</td>
						</tr>
						
						<tr>
							<td style="text-align:left;">
							    <select class='campo_azul' id='tipo_periodo_tiempo' name='tipo_periodo_tiempo' onchange='acomodar_tipo_periodo_tiempo(this);' style='width:230px;'>
								<option value='mensual' selected>Mensual</option>
								<!--<option value='trimestral'>Trimestral</option>-->
								<!--<option value='semestral'>Semestral</option>-->
								<option value='anual'>Anual</option>					
							    </select>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_5' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Periodo:</h6>
							</td>
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
								<h6 id='sub_titulo_6' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Selector Archivo Para Analizar:</h6>
							</td>
						</tr>

						<tr>
						    <td style="text-align:left;">
								<div id='div_archivos_subidos'>{$campo_archivo_analizar}</div>						    
						    </td>
					    </tr>

						<tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_7' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Prestadores Encontrado En El Archivo:</h6>
							</td>
						</tr>

						<tr>
						    <td style="text-align:left;">
								<div id='div_entidades_prestadoras_archivo'>{$campo_prestador}</div>						    
						    </td>
					    </tr>

					    

						<tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_9' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Campo para el conteo especifico:</h6>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
								<div onmousemove="consultar_valor_permitido_campo_especifico();" id='mod_campo_1'>
									<select id='selector_campo_especifico' name='selector_campo_especifico' class='campo_azul' onchange="">
										<option value='none' selected>Seleccione Campo</option>
										{$campo_selector_campos}
									</select>
									<input type='hidden' id='copy_selector_campo_especifico' name='copy_selector_campo_especifico'>
								</div>
							</td>
						</tr>


						<tr>
							<td style="text-align:left;">
								<h6 id='sub_titulo_8' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Seleccione si se mostrara el detallado del campo seleccionado,<br>(registros con el valor permitido seleccionado para dicho campo, datos de identificacion y valor del campo en cuesti&oacute;n )<br> o solo su conteo:</h6>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
								<div id='div_detallado_o_especifico'>
									<select id='selector_general_o_detallado' name='selector_general_o_detallado' class='campo_azul' onchange='conteo_o_detallado();'>
										<option value='conteo' selected>Conteo Agrupado</option>
										<option value='detallado' >Detallado Por Registros</option>
									</select>
								</div>
							</td>
						</tr>

						

						<tr>
							<td style="text-align:left;" >
								<h6 id='sub_titulo_10' style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Valor Permitido Campo:</h6>
							</td>
						</tr>
						
						<tr>
							<td style="text-align:left;">
							    <div id='mod_campo_valor_permitido_1' style='display:none;'>
								<select id='selector_campo_valor_permitido_1' name='selector_campo_valor_permitido_1' class='campo_azul'>
									<option value='none' selected>Seleccione el valor permitido para el campo</option>
								</select>
							    </div>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
							    <div id='div_para_consultas_cruzadas' style='display:none;'>
							    	<table style='width: 50%;'>
							    		
							    		<tr>
											<td colspan='2' style='text-align:left;'>
												<!--
												<h6  style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;'>Seleccione si se comparara entre valores especificos seleccionados o se mostrara una tabla comparativa entre los valores permitidos de los campos cruzados</h6>
												<select id='selector_all_or_one_vp' name='selector_all_or_one_vp' class='campo_azul' onchange='all_or_one_vp();'>
													<option value='allvp' >Todos los Valores Permitidos</option>
													<option value='specificvp' selected>Valores Permitido Especifico</option>
												</select>
												-->
												<input type='hidden' id='selector_all_or_one_vp' name='selector_all_or_one_vp' value='specificvp'>
											</td>
										</tr>

										<tr>
											<td style="text-align:left;">
												<div  id='divcampocross_1'>
													<h6  style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Campo 1 para consulta cruzada:</h6>
													<select id='campocross_1' name='campocross_1' class='campo_azul' onchange="consultar_valor_permitido_campo_cruzado('divcampocrossvp_1','1');">
														<option value='none' selected>Seleccione Campo</option>
														{$campo_selector_campos}
													</select>
													<input type='hidden' id='copycampocross_1' name='copycampocross_1'>
												</div>
											</td>

											<td style="text-align:left;">
											    <div id='divcampocrossvp_1' >
											    	<h6  style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Valor Permitido del Campo 1:</h6>
													<select id='campocrossvp_1' name='campocrossvp_1' class='campo_azul'>
														<option value='none' selected>Seleccione el valor permitido para el campo 1</option>
													</select>
											    </div>
											</td>
										</tr>

										<tr>
											<td style="text-align:left;">
												<div id='divcampocross_2'>
													<h6  style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Campo 2 para consulta cruzada:</h6>
													<select id='campocross_2' name='campocross_2' class='campo_azul' onchange="consultar_valor_permitido_campo_cruzado('divcampocrossvp_2','2');">
														<option value='none' selected>Seleccione Campo</option>
														{$campo_selector_campos}
													</select>
													<input type='hidden' id='copycampocross_2' name='copycampocross_2'>
												</div>
											</td>

											<td style="text-align:left;">
											    <div id='divcampocrossvp_2' >
											    	<h6  style="color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;">Valor Permitido del Campo 2:</h6>
													<select id='campocrossvp_2' name='campocrossvp_2' class='campo_azul'>
														<option value='none' selected>Seleccione el valor permitido para el campo 2</option>
													</select>
											    </div>
											</td>
											
										</tr>

										<tr>
											<td colspan='2' style='text-align:left;'>
												<h6  id='sub_titulo_conteo_detallado_cross' style='color:#0000FF;text-shadow: 2px 2px 5px #A8A8FF;'>Seleccione si se mostrara el detallado del campo seleccionado,<br>(registros con el valor permitido seleccionado para dicho campo, datos de identificacion y valor del campo en cuesti&oacute;n )<br> o solo su conteo:</h6><br>
												<select id='selector_general_o_detallado_cross' name='selector_general_o_detallado_cross' class='campo_azul' >
													<option value='conteo' selected>Conteo Agrupado</option>
													<option value='detallado' >Detallado Por Registros</option>
												</select>
											</td>
										</tr>

										


							    	</table>
							    	<input type='button' id='boton_adicionar_campo' name='boton_adicionar_campo' class='btn btn-success color_boton' value='Cruzar Con campo Adicional (+)' onclick='adicionar_campo_para_cruce();' style='display:inline;'/>
							    	<input type='button' id='boton_reset_campos' name='boton_reset_campos' class='btn btn-success color_boton' value='Reset Numero Campos' onclick='reset_numero_campos();' style='display:inline;'/>		
							    						
							    </div>
							</td>
						</tr>

						<tr>
							<td style="text-align:left;">
								<span>&nbsp</span>	
							</td>
						</tr>



						<tr>
							<td style="text-align:left;">
								<div id='boton_consultar'>
									<input type="hidden" value="" id="comprobante_submit" name="comprobante_submit">
									<input type="button" value="Consultar" onclick="ejecutar_consulta();" class="btn btn-success color_boton">
								</div>
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

									<!--<a class="close" data-dismiss="alert" href="#">&times</a>-->
									<h4 id='titulo_mensaje_exito' class="alert-heading">&iexclConsulta Realizada!</h4>
									<p id='parrafo_mensaje_exito'></p>

								</div>

							</div>
							
							<!--Ventana informacion error-->
							<div id='div_mensaje_error' class="sec2" style="width:90%;display:none;">

								<div class="alert-success alert-block alert-error fade in">

									<a class="close" data-dismiss="alert" href="#">&times</a>
									<h4 id='titulo_mensaje_error' class="alert-heading">&iexclError Al Consultar!</h4>
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
                    <h6>Sistema de informaci&oacuten Gioss</h6>
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
		<script>			
			inicializador_javascript();

			mostrar_opciones_correspondientes_al_caso();
		</script>
    </body>
</html>