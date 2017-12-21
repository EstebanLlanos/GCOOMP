<?php /* Smarty version Smarty-3.1.17, created on 2014-09-02 09:28:43
         compiled from "portafolio_servicios.html.tpl" */ ?>
<?php /*%%SmartyHeaderCode:227505335b70cb585a6-73976128%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '5b6ce9b4e545692623c627aa2a3331a34d93a63a' => 
    array (
      0 => 'portafolio_servicios.html.tpl',
      1 => 1405711392,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '227505335b70cb585a6-73976128',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.17',
  'unifunc' => 'content_5335b70cb9eab9_64286405',
  'variables' => 
  array (
    'menu' => 1,
    'nombre' => 1,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5335b70cb9eab9_64286405')) {function content_5335b70cb9eab9_64286405($_smarty_tpl) {?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">       
        <script type="text/javascript" src="../librerias_externas/jquery_min/jquery-1.10.2.js"> </script>       
        <script type="text/javascript" src="portafolio_servicios.js"></script>
        <script type="text/javascript" src="../librerias_externas/bootstrap/js/bootstrap.js"></script>
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap.css" rel="stylesheet" />
        <link type="text/css" href="../librerias_externas/bootstrap/css/bootstrap-responsive.css" rel="stylesheet" />
        <link type="text/css" href="portafolio_servicios.css" rel="stylesheet" />
       <title>Portafolio de Servicios</title>
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
							<div id='menu_div'><?php echo $_smarty_tpl->tpl_vars['menu']->value;?>
</div>
							</td>
							<td id='nombre'>
							<?php echo $_smarty_tpl->tpl_vars['nombre']->value;?>

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
			
					<td colspan='100' style="text-align:left;text-justify:inter-word;">
						<div id="info"> 
							
							<div id="quees">                
							<h3>Que es?</h3>
								<p>Herramienta orientada a brindar a
								las Entidades Administradoras de Planes de Beneficios y  a los Prestadores de Servicios de salud
								la solucion a los procesos de cargue, validacion y procesamiento de la informaci&oacuten que debe ser reportada, 
								seg&uacuten la normatividad expedida por las entidades de regulaci&oacuten, vigilancia y control
								del sistema de Seguridad Social en Salud.</p>                    
							</div>
							
							<div id="objetivo">                
							 <h3>Objetivo</h3>
							 <p>Facilitar y asegurar los procesos de transferencia, consolidaci&oacuten y poblamiento
							 de las bases de datos, para la construccion de la informacion que los Actores del sistema de Seguridad
							 Social en Salud deben reportar a las entidades de Regulacion vigilancia y control.</p>                
							</div>
							
						   <div id="beneficios">                
						   <h3>Beneficios</h3>
						   <p>Proporciona el procesamiento y visualizacion de informaci&oacuten estadistica b&aacutesica, relacionada con
							   oferta y demanda de servicios, asi como del estado de salud y enfermedad de la poblaci&oacuten
							   y la siniestralidad de los eventos, en funci&oacuten de la informaci&oacuten reportada. </p>
						
							</div> 



					   </div>
				   
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
            
    </body>
</html><?php }} ?>
