<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	   <!-- <meta http-equiv="refresh" content="5">-->
        <link rel="stylesheet" href="../librerias_externas/jquery_min/themes/flick/jquery-ui.css">	
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script> 
		<script src="../librerias_externas/jquery_min/jquery-ui.js"></script>     
        <script type="text/javascript" src="pantalla_inicial.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="pantalla_inicial.css" rel="stylesheet" />
       <title>Pantalla Inicial</title>
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
					<td style="text-align:center;vertical-align:text-top;width:20%;position: absolute;">
					<div id="logo">
						<img id="imgLogo" src="../assets/imagenes/logo_gios.png" />
					</div>
					</td> 
					<td style="text-align:center;vertical-align:text-top;width:80% !important;max-width: 80% !important;position: absolute;left: 20%;overflow: scroll;height: 80% !important;max-height: 80% !important;">
					<form name="formulario" id="formulario" action="" method="post" enctype= "multipart/form-data">
						<table>
						
						<tr>
						    <td>
							<div id='div_mensaje_en_ejecucion' class="sec2" style="display:block;border-style: solid;">

								<div  class="alert-success alert-block alert-success fade in" >
									<input type="hidden" id="nick_hidden" name="nick_hidden" value="{$nick_hidden}">
									<!--<a class="close" data-dismiss="alert" href="#">&times</a>-->
									<h4 class="alert-heading">
									    ADMINISTRADOR DE TAREAS:
									    <img src='../assets/imagenes/refresh.png'
										 id="refrescar"
										 onclick="refresh_now();"
										 
										 />
									</h4>
									<div id='parrafo_en_ejecucion'>
									<div id="tabs" style="">
									<ul>
									  <li><a href="#tabs-1">Validaciones</a></li>
									  <li><a href="#tabs-2">Consolidaciones</a></li>
									  <li><a href="#tabs-3">Reparaciones</a></li>
									  <li><a href="#tabs-4">Ayuda</a></li>
									</ul>
									<div id="tabs-1" >
									  <p>
									    <ul id="lista_menus_vl_normas">
									    <li><a href="#tab_vl_1">Norma 4505</a></li>
									    <li><a href="#tab_vl_2">Norma 0123</a></li>
									    <li><a href="#tab_vl_3">Norma 0247</a></li>
									    <li><a href="#tab_vl_4">Norma 4725</a></li>
									    <li><a href="#tab_vl_5">Norma 2463</a></li>
									    <li><a href="#tab_vl_6">Norma 1393</a></li>
									    </ul>
									    <div id="tab_vl_1"><p>test 4505</p></div>
									    <div id="tab_vl_2"><p>test 0123</p></div>
									    <div id="tab_vl_3"><p>test 0247</p></div>
									    <div id="tab_vl_4"><p>test 4725</p></div>
									    <div id="tab_vl_5"><p>test 2463</p></div>
									    <div id="tab_vl_6"><p>test 1393</p></div>
									  </p>
									</div>
									<div id="tabs-2">
									  <p>
									    <ul id="lista_menus_ro_normas">
									    <li><a href="#tab_ro_1">Norma 4505</a></li>
									    <li><a href="#tab_ro_2">Norma 0123</a></li>
									    <li><a href="#tab_ro_3">Norma 0247</a></li>
									    <li><a href="#tab_ro_4">Norma 4725</a></li>
									    <li><a href="#tab_ro_5">Norma 2463</a></li>
									    <li><a href="#tab_ro_6">Norma 1393</a></li>
									    </ul>
									    <div id="tab_ro_1"><p>test 4505</p></div>
									    <div id="tab_ro_2"><p>test 0123</p></div>
									    <div id="tab_ro_3"><p>test 0247</p></div>
									    <div id="tab_ro_4"><p>test 4725</p></div>
									    <div id="tab_ro_5"><p>test 2463</p></div>
									    <div id="tab_ro_6"><p>test 1393</p></div>
									    
									  </p>
									</div>
									<div id="tabs-3">
									  <p>
									    <ul id="lista_menus_ar_normas">
									    <li><a href="#tab_ar_1">Norma 4505</a></li>
									    <li><a href="#tab_ar_2">Norma 0123</a></li>
									    <li><a href="#tab_ar_3">Norma 0247</a></li>
									    <li><a href="#tab_ar_4">Norma 4725</a></li>
									    <li><a href="#tab_ar_5">Norma 2463</a></li>
									    <li><a href="#tab_ar_6">Norma 1393</a></li>
									    </ul>
									    <div id="tab_ar_1"><p>test 4505</p></div>
									    <div id="tab_ar_2"><p>test 0123</p></div>
									    <div id="tab_ar_3"><p>test 0247</p></div>
									    <div id="tab_ar_4"><p>test 4725</p></div>
									    <div id="tab_ar_5"><p>test 2463</p></div>
									    <div id="tab_ar_6"><p>test 1393</p></div>
									  </p>
									</div>
									<div id="tabs-4">
									    <div id='help_tabs' >
										<ul>
										    <li><a href="#help1">General</a></li>
										    <li><a href="#help2">Manual De Usuario</a></li>
										</ul>
										<div id='help1'>
										    <p style="text-align: justify;">
										    Se&ntildeor usuario, tenga en cuenta las siguientes indicaciones al utilizar el aplicativo:
										    <br>
										    <b>1.</b>Para ejecutar los procesos de validaci&oacuten, cargue o consultas sobre la informaci&oacuten obligatoria,<br>
										    presione la opci&oacuten "INF. OBLIGATORIA".
										    <br>
										    <b>2.</b>Si desea regresar ala pantalla principal, presione "INICIO".
										    <br>
										    <b>3.</b>Si desea cerrar la sesi&oacuten de trabajo y volver a la pantalla de logueo, presione la opci&oacuten <br>
										    "CERRAR SESI&OacuteN".
										    <br>
										    <b>4.</b>Verifique que el nombre de su usuario, corresponda al registrado en la esquina superior derecha de la <br>
										    pantalla
										    <br>
										    <b>5.</b>Las opciones de men&uacute se despliegan de acuerdo al perfil asignado a su usuario.
										    <br><br>
										    </p>
										</div>
										<div id='help2'>
										    <iframe src = "../librerias_externas/ViewerJS/#../../assets/pdf_files/manual.pdf" width='600' height='300' allowfullscreen webkitallowfullscreen></iframe>
										</div>
									    </div>
									    
									</div>
								      </div>
								    </div><!--fin div parrafo_en_ejecucion-->				
								</div>
				
							</div>
						    </td>
						</tr>
						<tr>
						    <td style="text-align:left;">
						    
						    <b>{$info_ultimo_acceso}</b>
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
        <td colspan='100' style="text-align:center;">   
			<div  id="mensaje">
				
			</div>  
		

			
		 </td>
		 </tr>
		 
		<tr>		 
        <td colspan='100' style="text-align:right;position: absolute;left:80%;top: 93%;z-index: -1;">    
                <div id="footer">
                    <h6>Sistema de informaci&oacuten Gioss</h6>
                </div>                
            
         </td>
		 </tr>  
		</table>
            
    </body>
</html>