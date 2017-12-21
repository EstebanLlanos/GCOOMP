<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();

if(isset($_REQUEST["nick_user"]) )
{
	$nick_user=$_REQUEST["nick_user"];
	
	$ultima_seleccion_ar=$_REQUEST["ultima_seleccion_ar"];

	$parrafo_archivos_ejecutando="";
	
	//PARTE TABS		
	$parrafo_archivos_ejecutando.="
	<ul id=\"lista_menus_ar_normas\" role=\"tablist\" class=\"ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all\">
	<li id=\"li-ui-id-17\" ";
	if($ultima_seleccion_ar=="4505")
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"true\" ";
	}
	else
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"false\" ";
	}
	$parrafo_archivos_ejecutando.="aria-labelledby=\"ui-id-17\" aria-controls=\"tab_ar_1\" tabindex=\"0\" role=\"tab\" class=\"ui-state-default ui-corner-top";
	if($ultima_seleccion_ar=="4505")
	{
		$parrafo_archivos_ejecutando.="ui-tabs-active ui-state-active";
	}
	$parrafo_archivos_ejecutando.="\">
	<a id=\"ui-id-17\" tabindex=\"-1\" role=\"presentation\" class=\"ui-tabs-anchor\" href=\"#tab_ar_1\"
	onmouseenter=\"tab_mouse_enter_manual('ui-id-17')\" onmouseleave=\"tab_mouse_leave_manual('ui-id-17')\"
	onclick=\"tab_onclick_manual('ui-id-17','lista_menus_ar_normas')\"
	>Norma 4505</a>
	</li>
	<li id=\"li-ui-id-18\" ";
	if($ultima_seleccion_ar=="0123")
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"true\" ";
	}
	else
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"false\" ";
	}
	$parrafo_archivos_ejecutando.="aria-labelledby=\"ui-id-18\" aria-controls=\"tab_ar_2\" tabindex=\"-1\" role=\"tab\" class=\"ui-state-default ui-corner-top";
	if($ultima_seleccion_ar=="0123")
	{
		$parrafo_archivos_ejecutando.="ui-tabs-active ui-state-active";
	}
	$parrafo_archivos_ejecutando.="\">
	<a id=\"ui-id-18\" tabindex=\"-1\" role=\"presentation\" class=\"ui-tabs-anchor\" href=\"#tab_ar_2\"
	onmouseenter=\"tab_mouse_enter_manual('ui-id-18')\" onmouseleave=\"tab_mouse_leave_manual('ui-id-18')\"
	onclick=\"tab_onclick_manual('ui-id-18','lista_menus_ar_normas')\"
	>Norma 0123</a>
	</li>
	<li id=\"li-ui-id-19\" ";
	if($ultima_seleccion_ar=="0247")
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"true\" ";
	}
	else
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"false\" ";
	}
	$parrafo_archivos_ejecutando.="aria-labelledby=\"ui-id-19\" aria-controls=\"tab_ar_3\" tabindex=\"-1\" role=\"tab\" class=\"ui-state-default ui-corner-top";
	if($ultima_seleccion_ar=="0247")
	{
		$parrafo_archivos_ejecutando.="ui-tabs-active ui-state-active";
	}
	$parrafo_archivos_ejecutando.="\">
	<a id=\"ui-id-19\" tabindex=\"-1\" role=\"presentation\" class=\"ui-tabs-anchor\" href=\"#tab_ar_3\"
	onmouseenter=\"tab_mouse_enter_manual('ui-id-19')\" onmouseleave=\"tab_mouse_leave_manual('ui-id-19')\"
	onclick=\"tab_onclick_manual('ui-id-19','lista_menus_ar_normas')\"
	>Norma 0247</a>
	</li>
	<li id=\"li-ui-id-20\" ";
	if($ultima_seleccion_ar=="4725")
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"true\" ";
	}
	else
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"false\" ";
	}
	$parrafo_archivos_ejecutando.="aria-labelledby=\"ui-id-20\" aria-controls=\"tab_ar_4\" tabindex=\"-1\" role=\"tab\" class=\"ui-state-default ui-corner-top";
	if($ultima_seleccion_ar=="4725")
	{
		$parrafo_archivos_ejecutando.="ui-tabs-active ui-state-active";
	}
	$parrafo_archivos_ejecutando.="\">
	<a id=\"ui-id-20\" tabindex=\"-1\" role=\"presentation\" class=\"ui-tabs-anchor\" href=\"#tab_ar_4\"
	onmouseenter=\"tab_mouse_enter_manual('ui-id-20')\" onmouseleave=\"tab_mouse_leave_manual('ui-id-20')\"
	onclick=\"tab_onclick_manual('ui-id-20','lista_menus_ar_normas')\"
	>Norma 4725</a>
	</li>
	<li id=\"li-ui-id-21\" ";
	if($ultima_seleccion_ar=="2463")
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"true\" ";
	}
	else
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"false\" ";
	}
	$parrafo_archivos_ejecutando.="aria-labelledby=\"ui-id-21\" aria-controls=\"tab_ar_5\" tabindex=\"-1\" role=\"tab\" class=\"ui-state-default ui-corner-top";
	if($ultima_seleccion_ar=="2463")
	{
		$parrafo_archivos_ejecutando.="ui-tabs-active ui-state-active";
	}
	$parrafo_archivos_ejecutando.="\">
	<a id=\"ui-id-21\" tabindex=\"-1\" role=\"presentation\" class=\"ui-tabs-anchor\" href=\"#tab_ar_5\"
	onmouseenter=\"tab_mouse_enter_manual('ui-id-21')\" onmouseleave=\"tab_mouse_leave_manual('ui-id-21')\"
	onclick=\"tab_onclick_manual('ui-id-21','lista_menus_ar_normas')\"
	>Norma 2463</a>
	</li>
	<li id=\"li-ui-id-22\" ";
	if($ultima_seleccion_ar=="1393")
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"true\" ";
	}
	else
	{
		$parrafo_archivos_ejecutando.=" aria-selected=\"false\" ";
	}
	$parrafo_archivos_ejecutando.="aria-labelledby=\"ui-id-22\" aria-controls=\"tab_ar_6\" tabindex=\"-1\" role=\"tab\" class=\"ui-state-default ui-corner-top";
	if($ultima_seleccion_ar=="1393")
	{
		$parrafo_archivos_ejecutando.="ui-tabs-active ui-state-active";
	}
	$parrafo_archivos_ejecutando.="\">
	<a id=\"ui-id-22\" tabindex=\"-1\" role=\"presentation\" class=\"ui-tabs-anchor\" href=\"#tab_ar_6\"
	onmouseenter=\"tab_mouse_enter_manual('ui-id-22')\" onmouseleave=\"tab_mouse_leave_manual('ui-id-22')\"
	onclick=\"tab_onclick_manual('ui-id-22','lista_menus_ar_normas')\"
	>Norma 1393</a>
	</li>
	</ul>
	";
	//FIN PARTE TABS
	
	if($ultima_seleccion_ar=="4505")
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"false\"
		aria-expanded=\"true\"
		style=\"display: block;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-17\"
		id=\"tab_ar_1\"
		>";
	}
	else
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"true\"
		aria-expanded=\"false\"
		style=\"display: none;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-17\"
		id=\"tab_ar_1\"
		>";
	}
	
	//4505
	$query_verificacion_esta_siendo_procesado_4505="";
	$query_verificacion_esta_siendo_procesado_4505.=" SELECT * FROM gioss_4505_esta_reparando_ar_actualmente WHERE nick_usuario='$nick_user' ORDER BY fecha_validacion DESC, hora_validacion DESC ";
	$query_verificacion_esta_siendo_procesado_4505.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado_4505=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado_4505);
	
	if(count($resultados_query_verificar_esta_siendo_procesado_4505)>0)
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 4505</h5>";
		$cont_4505=0;
		$parrafo_archivos_ejecutando.="<table border='1'>";
		$parrafo_archivos_ejecutando.="
		<tr>
		    <th>Nombre Archivo</th>
		    <th>Entidad A Reportar</th>
		    <th>Fecha Corte Periodo</th>
		    <th>Fecha Reparaci&oacuten</th>
		    <th>Hora Reparaci&oacuten</th>
		    <th>Mensaje Reparaci&oacuten</th>
		    <th>Estado Reparaci&oacuten</th>
		    <th>Fue el archivo descargado?</th>
		    <th>DL. Archivo Reparado</th>
		    <th>Otros</th>
		    <th>Descartar</th>
		    <th>Cancelar Ejecuci&oacuten</th>
		</tr>
		";
		foreach($resultados_query_verificar_esta_siendo_procesado_4505 as $archivo_validando_actual_4505)
		{
			if($archivo_validando_actual_4505["se_pudo_descargar"]=="NO")
			{
				$entidad_reportadora_4505=$archivo_validando_actual_4505["codigo_entidad_reportadora"];
				$nombre_archivo_4505=$archivo_validando_actual_4505["nombre_archivo"];
				$fecha_remision_4505=$archivo_validando_actual_4505["fecha_corte_archivo_en_reparacion"];
				$fecha_validacion_4505=$archivo_validando_actual_4505["fecha_validacion"];
				$hora_validacion_4505=$archivo_validando_actual_4505["hora_validacion"];
				$mensaje_estado_registros_4505=$archivo_validando_actual_4505["mensaje_estado_registros"];
				$estado=$archivo_validando_actual_4505["esta_ejecutando"];
				$ha_sido_descargado_con_satisfaccion=$archivo_validando_actual_4505["se_pudo_descargar"];
				$ruta_archivo=$archivo_validando_actual_4505["ruta_archivo_descarga"];
				//archivos para reparacion
				$ruta_archivo_filtrado=$archivo_validando_actual_4505["ruta_archivo_descarga_dupl"];
				$ruta_archivo_cambios_1=$archivo_validando_actual_4505["ruta_archivo_cambios_1"];
				$ruta_archivo_cambios_2=$archivo_validando_actual_4505["ruta_archivo_cambios_2"];
				$ruta_archivo_cambios_3=$archivo_validando_actual_4505["ruta_archivo_cambios_3"];
				//fin archivos para reparacion
				
				$mensaje_estado_registros_4505=str_replace("<table style=text-align:center;width:60%;left:25%;position:relative;border-style:solid;border-width:5px; id=tabla_estado_1>",
									   "<table style=text-align:center;width:100%;left:0%;position:relative;border-style:solid;border-width:5px; id=tabla_estado_1>",
									   $mensaje_estado_registros_4505);
				
				
				
				$parrafo_archivos_ejecutando.="<tr>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$nombre_archivo_4505." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.="".$entidad_reportadora_4505." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_remision_4505." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_validacion_4505." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$hora_validacion_4505." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$mensaje_estado_registros_4505."  ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Esta ejecutandose: ".$estado." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Se ha descargado el archivo: ".$ha_sido_descargado_con_satisfaccion." ";
				$parrafo_archivos_ejecutando.="</td>";
				if($estado=="NO")
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo."');\"/>";
					$parrafo_archivos_ejecutando.="</td>";
					//archivo con duplicados
					if($ruta_archivo_filtrado!="")
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo_filtrado."');\"/>";
						$parrafo_archivos_ejecutando.="</td>";
					}
					else
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.=" No se genero archivo. ";
						$parrafo_archivos_ejecutando.="</td>";
					}
					//fin archivo con duplicados
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Descartar\" class=\"btn btn-success color_boton\" onclick=\"indicar_descarga_exitosa('".$entidad_reportadora_4505."','".$nombre_archivo_4505."','".$fecha_remision_4505."','".$fecha_validacion_4505."','".$hora_validacion_4505."','".$nick_user."','ar_norma_4505');\"/>";
					$parrafo_archivos_ejecutando.="</td>";			
				}
				else
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descartar. ";
					$parrafo_archivos_ejecutando.="</td>";
				}
				
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Cancelar\" class=\"btn btn-success color_boton\" onclick=\"cancelar_ejecucion('".$entidad_reportadora_4505."','".$nombre_archivo_4505."','".$fecha_remision_4505."','".$fecha_validacion_4505."','".$hora_validacion_4505."','".$nick_user."','ar_norma_4505');\"/>";
				$parrafo_archivos_ejecutando.="</td>";
				
				$parrafo_archivos_ejecutando.="</tr>";
				
				
				$cont_4505++;
			}
		}//fin foreach
		$parrafo_archivos_ejecutando.="</table>";
	}//fin if hubo resultados en 4505
	else
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 4505</h5>";
		$parrafo_archivos_ejecutando.="<h7>No se encontraron archivos reparados previamente.</h5>";
	}
	
	//fin 4505
	
	$parrafo_archivos_ejecutando.="</div>";
	
	
	if($ultima_seleccion_ar=="0123")
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"false\"
		aria-expanded=\"true\"
		style=\"display: block;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-18\"
		id=\"tab_ar_2\"
		>";
	}
	else
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"true\"
		aria-expanded=\"false\"
		style=\"display: none;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-18\"
		id=\"tab_ar_2\"
		>";
	}
	
	//0123
	$query_verificacion_esta_siendo_procesado_0123="";
	$query_verificacion_esta_siendo_procesado_0123.=" SELECT * FROM gioss_0123_esta_reparando_ar_actualmente WHERE nick_usuario='$nick_user' ORDER BY fecha_validacion DESC, hora_validacion DESC ";
	$query_verificacion_esta_siendo_procesado_0123.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado_0123=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado_0123);
	
	if(count($resultados_query_verificar_esta_siendo_procesado_0123)>0)
	{
		$parrafo_archivos_ejecutando.="<br><br>";
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 0123</h5>";
		$cont_0123=0;
		$parrafo_archivos_ejecutando.="<table border='1'>";
		$parrafo_archivos_ejecutando.="
		<tr>
		    <th>Nombre Archivo</th>
		    <th>Entidad A Reportar</th>
		    <th>Fecha Corte Periodo</th>
		    <th>Fecha Reparaci&oacuten</th>
		    <th>Hora Reparaci&oacuten</th>
		    <th>Mensaje Reparaci&oacuten</th>
		    <th>Estado Reparaci&oacuten</th>
		    <th>Fue el archivo descargado?</th>
		    <th>DL. Archivo Reparado</th>
		    <th>Otros</th>
		    <th>Descartar</th>
		    <th>Cancelar Ejecuci&oacuten</th>
		</tr>
		";
		foreach($resultados_query_verificar_esta_siendo_procesado_0123 as $archivo_validando_actual_0123)
		{
			if($archivo_validando_actual_0123["se_pudo_descargar"]=="NO")
			{
				$entidad_reportadora_0123=$archivo_validando_actual_0123["codigo_entidad_reportadora"];
				$nombre_archivo_0123=$archivo_validando_actual_0123["nombre_archivo"];
				$fecha_remision_0123=$archivo_validando_actual_0123["fecha_corte_archivo_en_reparacion"];
				$fecha_validacion_0123=$archivo_validando_actual_0123["fecha_validacion"];
				$hora_validacion_0123=$archivo_validando_actual_0123["hora_validacion"];
				$mensaje_estado_registros_0123=$archivo_validando_actual_0123["mensaje_estado_registros"];
				$estado=$archivo_validando_actual_0123["esta_ejecutando"];
				$ha_sido_descargado_con_satisfaccion=$archivo_validando_actual_0123["se_pudo_descargar"];
				$ruta_archivo=$archivo_validando_actual_0123["ruta_archivo_descarga"];
				//archivos para reparacion
				$ruta_archivo_filtrado=$archivo_validando_actual_0123["ruta_archivo_descarga_dupl"];
				$ruta_archivo_cambios_1=$archivo_validando_actual_0123["ruta_archivo_cambios_1"];
				$ruta_archivo_cambios_2=$archivo_validando_actual_0123["ruta_archivo_cambios_2"];
				$ruta_archivo_cambios_3=$archivo_validando_actual_0123["ruta_archivo_cambios_3"];
				//fin archivos para reparacion			
				
				
				$parrafo_archivos_ejecutando.="<tr>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$nombre_archivo_0123." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.="".$entidad_reportadora_0123." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_remision_0123." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_validacion_0123." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$hora_validacion_0123." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$mensaje_estado_registros_0123."  ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Esta ejecutandose: ".$estado." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Se ha descargado el archivo: ".$ha_sido_descargado_con_satisfaccion." ";
				$parrafo_archivos_ejecutando.="</td>";
				if($estado=="NO")
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo."');\"/>";
					$parrafo_archivos_ejecutando.="</td>";
					
					//archivo con duplicados
					if($ruta_archivo_filtrado!="")
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo_filtrado."');\"/>";
						$parrafo_archivos_ejecutando.="</td>";
					}
					else
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.=" No se genero archivo. ";
						$parrafo_archivos_ejecutando.="</td>";
					}
					//fin archivo con duplicados
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Descartar\" class=\"btn btn-success color_boton\" onclick=\"indicar_descarga_exitosa('".$entidad_reportadora_0123."','".$nombre_archivo_0123."','".$fecha_remision_0123."','".$fecha_validacion_0123."','".$hora_validacion_0123."','".$nick_user."','ar_norma_0123');\"/>";
					$parrafo_archivos_ejecutando.="</td>";			
				}
				else
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" No Aplica. ";
					$parrafo_archivos_ejecutando.="</td>";
									
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descartar. ";
					$parrafo_archivos_ejecutando.="</td>";
				}
				
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Cancelar\" class=\"btn btn-success color_boton\" onclick=\"cancelar_ejecucion('".$entidad_reportadora_0123."','".$nombre_archivo_0123."','".$fecha_remision_0123."','".$fecha_validacion_0123."','".$hora_validacion_0123."','".$nick_user."','ar_norma_0123');\"/>";
				$parrafo_archivos_ejecutando.="</td>";
				
				$parrafo_archivos_ejecutando.="</tr>";
				
				
				$cont_0123++;
			}
		}//fin foreach
		$parrafo_archivos_ejecutando.="</table>";
	}//fin if hubo resultados en 0123
	else
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 0123</h5>";
		$parrafo_archivos_ejecutando.="<h7>No se encontraron archivos reparados previamente.</h5>";
	}
	//fin 0123
	
	$parrafo_archivos_ejecutando.="</div>";
	
	if($ultima_seleccion_ar=="0247")
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"false\"
		aria-expanded=\"true\"
		style=\"display: block;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-19\"
		id=\"tab_ar_3\"
		>";
	}
	else
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"true\"
		aria-expanded=\"false\"
		style=\"display: none;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-19\"
		id=\"tab_ar_3\"
		>";
	}
	
	//0247
	$query_verificacion_esta_siendo_procesado_0247="";
	$query_verificacion_esta_siendo_procesado_0247.=" SELECT * FROM gioss_0247_esta_reparando_ar_actualmente WHERE nick_usuario='$nick_user' ORDER BY fecha_validacion DESC, hora_validacion DESC ";
	$query_verificacion_esta_siendo_procesado_0247.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado_0247=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado_0247);
	
	if(count($resultados_query_verificar_esta_siendo_procesado_0247)>0)
	{
		$parrafo_archivos_ejecutando.="<br><br>";
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 0247</h5>";
		$cont_0247=0;
		$parrafo_archivos_ejecutando.="<table border='1'>";
		$parrafo_archivos_ejecutando.="
		<tr>
		    <th>Nombre Archivo</th>
		    <th>Entidad A Reportar</th>
		    <th>Fecha Corte Periodo</th>
		    <th>Fecha Reparaci&oacuten</th>
		    <th>Hora Reparaci&oacuten</th>
		    <th>Mensaje Reparaci&oacuten</th>
		    <th>Estado Reparaci&oacuten</th>
		    <th>Fue el archivo descargado?</th>
		    <th>DL. Archivo Reparado</th>
		    <th>Otros</th>
		    <th>Descartar</th>
		    <th>Cancelar Ejecuci&oacuten</th>
		</tr>
		";
		foreach($resultados_query_verificar_esta_siendo_procesado_0247 as $archivo_validando_actual_0247)
		{
			if($archivo_validando_actual_0247["se_pudo_descargar"]=="NO")
			{
				$entidad_reportadora_0247=$archivo_validando_actual_0247["codigo_entidad_reportadora"];
				$nombre_archivo_0247=$archivo_validando_actual_0247["nombre_archivo"];
				$fecha_remision_0247=$archivo_validando_actual_0247["fecha_corte_archivo_en_reparacion"];
				$fecha_validacion_0247=$archivo_validando_actual_0247["fecha_validacion"];
				$hora_validacion_0247=$archivo_validando_actual_0247["hora_validacion"];
				$mensaje_estado_registros_0247=$archivo_validando_actual_0247["mensaje_estado_registros"];
				$estado=$archivo_validando_actual_0247["esta_ejecutando"];
				$ha_sido_descargado_con_satisfaccion=$archivo_validando_actual_0247["se_pudo_descargar"];
				$ruta_archivo=$archivo_validando_actual_0247["ruta_archivo_descarga"];
				//archivos para reparacion
				$ruta_archivo_filtrado=$archivo_validando_actual_0247["ruta_archivo_descarga_dupl"];
				$ruta_archivo_cambios_1=$archivo_validando_actual_0247["ruta_archivo_cambios_1"];
				$ruta_archivo_cambios_2=$archivo_validando_actual_0247["ruta_archivo_cambios_2"];
				$ruta_archivo_cambios_3=$archivo_validando_actual_0247["ruta_archivo_cambios_3"];
				//fin archivos para reparacion
				
				$mensaje_estado_registros_0247=str_replace("<table style=text-align:center;width:60%;left:25%;border-style:solid;border-width:5px; id=tabla_estado_1>",
									   "<table style=text-align:center;width:100%;left:0%;border-style:solid;border-width:5px; id=tabla_estado_1>",
									   $mensaje_estado_registros_0247);
				
				$parrafo_archivos_ejecutando.="<tr>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$nombre_archivo_0247." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.="".$entidad_reportadora_0247." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_remision_0247." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_validacion_0247." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$hora_validacion_0247." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$mensaje_estado_registros_0247."  ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Esta ejecutandose: ".$estado." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Se ha descargado el archivo: ".$ha_sido_descargado_con_satisfaccion." ";
				$parrafo_archivos_ejecutando.="</td>";
				if($estado=="NO")
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo."');\"/>";
					$parrafo_archivos_ejecutando.="</td>";
					
					//archivo con duplicados
					if($ruta_archivo_filtrado!="")
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo_filtrado."');\"/>";
						$parrafo_archivos_ejecutando.="</td>";
					}
					else
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.=" No se genero archivo. ";
						$parrafo_archivos_ejecutando.="</td>";
					}
					//fin archivo con duplicados
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Descartar\" class=\"btn btn-success color_boton\" onclick=\"indicar_descarga_exitosa('".$entidad_reportadora_0247."','".$nombre_archivo_0247."','".$fecha_remision_0247."','".$fecha_validacion_0247."','".$hora_validacion_0247."','".$nick_user."','ar_norma_0247');\"/>";
					$parrafo_archivos_ejecutando.="</td>";			
				}
				else
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" No Aplica. ";
					$parrafo_archivos_ejecutando.="</td>";
									
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descartar. ";
					$parrafo_archivos_ejecutando.="</td>";
				}
				
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Cancelar\" class=\"btn btn-success color_boton\" onclick=\"cancelar_ejecucion('".$entidad_reportadora_0247."','".$nombre_archivo_0247."','".$fecha_remision_0247."','".$fecha_validacion_0247."','".$hora_validacion_0247."','".$nick_user."','ar_norma_0247');\"/>";
				$parrafo_archivos_ejecutando.="</td>";
				
				$parrafo_archivos_ejecutando.="</tr>";
				
				
				$cont_0247++;
			}
		}//fin foreach
		$parrafo_archivos_ejecutando.="</table>";
	}//fin if hubo resultados en 0247
	else
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 0247</h5>";
		$parrafo_archivos_ejecutando.="<h7>No se encontraron archivos reparados previamente.</h5>";
	}
	//fin 0247
	
	$parrafo_archivos_ejecutando.="</div>";
	
	if($ultima_seleccion_ar=="4725")
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"false\"
		aria-expanded=\"true\"
		style=\"display: block;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-20\"
		id=\"tab_ar_4\"
		>";
	}
	else
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"true\"
		aria-expanded=\"false\"
		style=\"display: none;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-20\"
		id=\"tab_ar_4\"
		>";
	}
	
	//4725
	$query_verificacion_esta_siendo_procesado_4725="";
	$query_verificacion_esta_siendo_procesado_4725.=" SELECT * FROM gioss_4725_esta_reparando_ar_actualmente WHERE nick_usuario='$nick_user' ORDER BY fecha_validacion DESC, hora_validacion DESC ";
	$query_verificacion_esta_siendo_procesado_4725.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado_4725=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado_4725);
	
	if(count($resultados_query_verificar_esta_siendo_procesado_4725)>0)
	{
		$parrafo_archivos_ejecutando.="<br><br>";
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 4725</h5>";
		$cont_4725=0;
		$parrafo_archivos_ejecutando.="<table border='1'>";
		$parrafo_archivos_ejecutando.="
		<tr>
		    <th>Nombre Archivo</th>
		    <th>Entidad A Reportar</th>
		    <th>Fecha Corte Periodo</th>
		    <th>Fecha Reparaci&oacuten</th>
		    <th>Hora Reparaci&oacuten</th>
		    <th>Mensaje Reparaci&oacuten</th>
		    <th>Estado Reparaci&oacuten</th>
		    <th>Fue el archivo descargado?</th>
		    <th>DL. Archivo Reparado</th>
		    <th>Otros</th>
		    <th>Descartar</th>
		    <th>Cancelar Ejecuci&oacuten</th>
		</tr>
		";
		foreach($resultados_query_verificar_esta_siendo_procesado_4725 as $archivo_validando_actual_4725)
		{
			if($archivo_validando_actual_4725["se_pudo_descargar"]=="NO")
			{
				$entidad_reportadora_4725=$archivo_validando_actual_4725["codigo_entidad_reportadora"];
				$nombre_archivo_4725=$archivo_validando_actual_4725["nombre_archivo"];
				$fecha_remision_4725=$archivo_validando_actual_4725["fecha_corte_archivo_en_reparacion"];
				$fecha_validacion_4725=$archivo_validando_actual_4725["fecha_validacion"];
				$hora_validacion_4725=$archivo_validando_actual_4725["hora_validacion"];
				$mensaje_estado_registros_4725=$archivo_validando_actual_4725["mensaje_estado_registros"];
				$estado=$archivo_validando_actual_4725["esta_ejecutando"];
				$ha_sido_descargado_con_satisfaccion=$archivo_validando_actual_4725["se_pudo_descargar"];
				$ruta_archivo=$archivo_validando_actual_4725["ruta_archivo_descarga"];
				//archivos para reparacion
				$ruta_archivo_filtrado=$archivo_validando_actual_4725["ruta_archivo_descarga_dupl"];
				$ruta_archivo_cambios_1=$archivo_validando_actual_4725["ruta_archivo_cambios_1"];
				$ruta_archivo_cambios_2=$archivo_validando_actual_4725["ruta_archivo_cambios_2"];
				$ruta_archivo_cambios_3=$archivo_validando_actual_4725["ruta_archivo_cambios_3"];
				//fin archivos para reparacion
				
				$parrafo_archivos_ejecutando.="<tr>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$nombre_archivo_4725." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.="".$entidad_reportadora_4725." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_remision_4725." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_validacion_4725." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$hora_validacion_4725." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$mensaje_estado_registros_4725."  ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Esta ejecutandose: ".$estado." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Se ha descargado el archivo: ".$ha_sido_descargado_con_satisfaccion." ";
				$parrafo_archivos_ejecutando.="</td>";
				if($estado=="NO")
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo."');\"/>";
					$parrafo_archivos_ejecutando.="</td>";
					
					//archivo con duplicados
					if($ruta_archivo_filtrado!="")
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo_filtrado."');\"/>";
						$parrafo_archivos_ejecutando.="</td>";
					}
					else
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.=" No se genero archivo. ";
						$parrafo_archivos_ejecutando.="</td>";
					}
					//fin archivo con duplicados
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Descartar\" class=\"btn btn-success color_boton\" onclick=\"indicar_descarga_exitosa('".$entidad_reportadora_4725."','".$nombre_archivo_4725."','".$fecha_remision_4725."','".$fecha_validacion_4725."','".$hora_validacion_4725."','".$nick_user."','ar_norma_4725');\"/>";
					$parrafo_archivos_ejecutando.="</td>";			
				}
				else
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" No Aplica. ";
					$parrafo_archivos_ejecutando.="</td>";
									
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descartar. ";
					$parrafo_archivos_ejecutando.="</td>";
				}
				
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Cancelar\" class=\"btn btn-success color_boton\" onclick=\"cancelar_ejecucion('".$entidad_reportadora_4725."','".$nombre_archivo_4725."','".$fecha_remision_4725."','".$fecha_validacion_4725."','".$hora_validacion_4725."','".$nick_user."','ar_norma_4725');\"/>";
				$parrafo_archivos_ejecutando.="</td>";
				
				$parrafo_archivos_ejecutando.="</tr>";
				
				
				$cont_4725++;
			}
		}//fin foreach
		$parrafo_archivos_ejecutando.="</table>";
	}//fin if hubo resultados en 4725
	else
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 4725</h5>";
		$parrafo_archivos_ejecutando.="<h7>No se encontraron archivos reparados previamente.</h5>";
	}
	//fin 4725
	
	$parrafo_archivos_ejecutando.="</div>";
	
	if($ultima_seleccion_ar=="2463")
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"false\"
		aria-expanded=\"true\"
		style=\"display: block;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-21\"
		id=\"tab_ar_5\"
		>";
	}
	else
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"true\"
		aria-expanded=\"false\"
		style=\"display: none;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-21\"
		id=\"tab_ar_5\"
		>";
	}
	
	//2463
	$query_verificacion_esta_siendo_procesado_2463="";
	$query_verificacion_esta_siendo_procesado_2463.=" SELECT * FROM gioss_2463_esta_reparando_ar_actualmente WHERE nick_usuario='$nick_user' ORDER BY fecha_validacion DESC, hora_validacion DESC ";
	$query_verificacion_esta_siendo_procesado_2463.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado_2463=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado_2463);
	
	if(count($resultados_query_verificar_esta_siendo_procesado_2463)>0)
	{
		$parrafo_archivos_ejecutando.="<br><br>";
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 2463</h5>";
		$cont_2463=0;
		$parrafo_archivos_ejecutando.="<table border='1'>";
		$parrafo_archivos_ejecutando.="
		<tr>
		    <th>Nombre Archivo</th>
		    <th>Entidad A Reportar</th>
		    <th>Fecha Corte Periodo</th>
		    <th>Fecha Reparaci&oacuten</th>
		    <th>Hora Reparaci&oacuten</th>
		    <th>Mensaje Reparaci&oacuten</th>
		    <th>Estado Reparaci&oacuten</th>
		    <th>Fue el archivo descargado?</th>
		    <th>DL. Archivo Reparado</th>
		    <th>Otros</th>
		    <th>Descartar</th>
		    <th>Cancelar Ejecuci&oacuten</th>
		</tr>
		";
		foreach($resultados_query_verificar_esta_siendo_procesado_2463 as $archivo_validando_actual_2463)
		{
			if($archivo_validando_actual_2463["se_pudo_descargar"]=="NO")
			{
				$entidad_reportadora_2463=$archivo_validando_actual_2463["codigo_entidad_reportadora"];
				$nombre_archivo_2463=$archivo_validando_actual_2463["nombre_archivo"];
				$fecha_remision_2463=$archivo_validando_actual_2463["fecha_corte_archivo_en_reparacion"];
				$fecha_validacion_2463=$archivo_validando_actual_2463["fecha_validacion"];
				$hora_validacion_2463=$archivo_validando_actual_2463["hora_validacion"];
				$mensaje_estado_registros_2463=$archivo_validando_actual_2463["mensaje_estado_registros"];
				$estado=$archivo_validando_actual_2463["esta_ejecutando"];
				$ha_sido_descargado_con_satisfaccion=$archivo_validando_actual_2463["se_pudo_descargar"];
				$ruta_archivo=$archivo_validando_actual_2463["ruta_archivo_descarga"];
				//archivos para reparacion
				$ruta_archivo_filtrado=$archivo_validando_actual_2463["ruta_archivo_descarga_dupl"];
				$ruta_archivo_cambios_1=$archivo_validando_actual_2463["ruta_archivo_cambios_1"];
				$ruta_archivo_cambios_2=$archivo_validando_actual_2463["ruta_archivo_cambios_2"];
				$ruta_archivo_cambios_3=$archivo_validando_actual_2463["ruta_archivo_cambios_3"];
				//fin archivos para reparacion
				
				$parrafo_archivos_ejecutando.="<tr>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$nombre_archivo_2463." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.="".$entidad_reportadora_2463." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_remision_2463." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_validacion_2463." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$hora_validacion_2463." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$mensaje_estado_registros_2463."  ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Esta ejecutandose: ".$estado." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Se ha descargado el archivo: ".$ha_sido_descargado_con_satisfaccion." ";
				$parrafo_archivos_ejecutando.="</td>";
				if($estado=="NO")
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo."');\"/>";
					$parrafo_archivos_ejecutando.="</td>";
					
					//archivo con duplicados
					if($ruta_archivo_filtrado!="")
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo_filtrado."');\"/>";
						$parrafo_archivos_ejecutando.="</td>";
					}
					else
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.=" No se genero archivo. ";
						$parrafo_archivos_ejecutando.="</td>";
					}
					//fin archivo con duplicados
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Descartar\" class=\"btn btn-success color_boton\" onclick=\"indicar_descarga_exitosa('".$entidad_reportadora_2463."','".$nombre_archivo_2463."','".$fecha_remision_2463."','".$fecha_validacion_2463."','".$hora_validacion_2463."','".$nick_user."','ar_norma_2463');\"/>";
					$parrafo_archivos_ejecutando.="</td>";			
				}
				else
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" No Aplica. ";
					$parrafo_archivos_ejecutando.="</td>";
									
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descartar. ";
					$parrafo_archivos_ejecutando.="</td>";
				}
				
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Cancelar\" class=\"btn btn-success color_boton\" onclick=\"cancelar_ejecucion('".$entidad_reportadora_2463."','".$nombre_archivo_2463."','".$fecha_remision_2463."','".$fecha_validacion_2463."','".$hora_validacion_2463."','".$nick_user."','ar_norma_2463');\"/>";
				$parrafo_archivos_ejecutando.="</td>";
				
				$parrafo_archivos_ejecutando.="</tr>";
				
				
				$cont_2463++;
			}
		}//fin foreach
		$parrafo_archivos_ejecutando.="</table>";
	}//fin if hubo resultados en 2463
	else
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 2463</h5>";
		$parrafo_archivos_ejecutando.="<h7>No se encontraron archivos reparados previamente.</h5>";
	}
	//fin 2463
	
	$parrafo_archivos_ejecutando.="</div>";
	
	if($ultima_seleccion_ar=="1393")
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"false\"
		aria-expanded=\"true\"
		style=\"display: block;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-22\"
		id=\"tab_ar_6\"
		>";
	}
	else
	{
		$parrafo_archivos_ejecutando.="<div
		aria-hidden=\"true\"
		aria-expanded=\"false\"
		style=\"display: none;\"
		role=\"tabpanel\"
		class=\"ui-tabs-panel ui-widget-content ui-corner-bottom\"
		aria-labelledby=\"ui-id-22\"
		id=\"tab_ar_6\"
		>";
	}
	
	//1393
	$query_verificacion_esta_siendo_procesado_1393="";
	$query_verificacion_esta_siendo_procesado_1393.=" SELECT * FROM gioss_1393_esta_reparando_ar_actualmente WHERE nick_usuario='$nick_user' ORDER BY fecha_validacion DESC, hora_validacion DESC ";
	$query_verificacion_esta_siendo_procesado_1393.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado_1393=$coneccionBD->consultar2($query_verificacion_esta_siendo_procesado_1393);
	
	if(count($resultados_query_verificar_esta_siendo_procesado_1393)>0)
	{
		$parrafo_archivos_ejecutando.="<br><br>";
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 1393</h5>";
		$cont_1393=0;
		$parrafo_archivos_ejecutando.="<table border='1'>";
		$parrafo_archivos_ejecutando.="
		<tr>
		    <th>Nombre Archivo</th>
		    <th>Entidad A Reportar</th>
		    <th>Fecha Corte Periodo</th>
		    <th>Fecha Reparaci&oacuten</th>
		    <th>Hora Reparaci&oacuten</th>
		    <th>Mensaje Reparaci&oacuten</th>
		    <th>Estado Reparaci&oacuten</th>
		    <th>Fue el archivo descargado?</th>
		    <th>DL. Archivo Reparado</th>
		    <th>Otros</th>
		    <th>Descartar</th>
		    <th>Cancelar Ejecuci&oacuten</th>
		</tr>
		";
		foreach($resultados_query_verificar_esta_siendo_procesado_1393 as $archivo_validando_actual_1393)
		{
			if($archivo_validando_actual_1393["se_pudo_descargar"]=="NO")
			{
				$entidad_reportadora_1393=$archivo_validando_actual_1393["codigo_entidad_reportadora"];
				$nombre_archivo_1393=$archivo_validando_actual_1393["nombre_archivo"];
				$fecha_remision_1393=$archivo_validando_actual_1393["fecha_corte_archivo_en_reparacion"];
				$fecha_validacion_1393=$archivo_validando_actual_1393["fecha_validacion"];
				$hora_validacion_1393=$archivo_validando_actual_1393["hora_validacion"];
				$mensaje_estado_registros_1393=$archivo_validando_actual_1393["mensaje_estado_registros"];
				$estado=$archivo_validando_actual_1393["esta_ejecutando"];
				$ha_sido_descargado_con_satisfaccion=$archivo_validando_actual_1393["se_pudo_descargar"];
				$ruta_archivo=$archivo_validando_actual_1393["ruta_archivo_descarga"];
				//archivos para reparacion
				$ruta_archivo_filtrado=$archivo_validando_actual_1393["ruta_archivo_descarga_dupl"];
				$ruta_archivo_cambios_1=$archivo_validando_actual_1393["ruta_archivo_cambios_1"];
				$ruta_archivo_cambios_2=$archivo_validando_actual_1393["ruta_archivo_cambios_2"];
				$ruta_archivo_cambios_3=$archivo_validando_actual_1393["ruta_archivo_cambios_3"];
				//fin archivos para reparacion
				
				$parrafo_archivos_ejecutando.="<tr>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$nombre_archivo_1393." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.="".$entidad_reportadora_1393." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_remision_1393." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$fecha_validacion_1393." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$hora_validacion_1393." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" ".$mensaje_estado_registros_1393."  ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Esta ejecutandose: ".$estado." ";
				$parrafo_archivos_ejecutando.="</td>";
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.=" Se ha descargado el archivo: ".$ha_sido_descargado_con_satisfaccion." ";
				$parrafo_archivos_ejecutando.="</td>";
				if($estado=="NO")
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo."');\"/>";
					$parrafo_archivos_ejecutando.="</td>";
					
					//archivo con duplicados
					if($ruta_archivo_filtrado!="")
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.="<input type=\"button\" value=\"Descargar\" class=\"btn btn-success color_boton\" onclick=\"download_inconsistencias_campos('".$ruta_archivo_filtrado."');\"/>";
						$parrafo_archivos_ejecutando.="</td>";
					}
					else
					{
						$parrafo_archivos_ejecutando.="<td>";
						$parrafo_archivos_ejecutando.=" No se genero archivo. ";
						$parrafo_archivos_ejecutando.="</td>";
					}
					//fin archivo con duplicados
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Descartar\" class=\"btn btn-success color_boton\" onclick=\"indicar_descarga_exitosa('".$entidad_reportadora_1393."','".$nombre_archivo_1393."','".$fecha_remision_1393."','".$fecha_validacion_1393."','".$hora_validacion_1393."','".$nick_user."','ar_norma_1393');\"/>";
					$parrafo_archivos_ejecutando.="</td>";			
				}
				else
				{
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descargar. ";
					$parrafo_archivos_ejecutando.="</td>";
					
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" No Aplica. ";
					$parrafo_archivos_ejecutando.="</td>";
									
					$parrafo_archivos_ejecutando.="<td>";
					$parrafo_archivos_ejecutando.=" Aun no puede descartar. ";
					$parrafo_archivos_ejecutando.="</td>";
				}
				
				$parrafo_archivos_ejecutando.="<td>";
				$parrafo_archivos_ejecutando.= "<input type=\"button\" value=\"Cancelar\" class=\"btn btn-success color_boton\" onclick=\"cancelar_ejecucion('".$entidad_reportadora_1393."','".$nombre_archivo_1393."','".$fecha_remision_1393."','".$fecha_validacion_1393."','".$hora_validacion_1393."','".$nick_user."','ar_norma_1393');\"/>";
				$parrafo_archivos_ejecutando.="</td>";
				
				$parrafo_archivos_ejecutando.="</tr>";
				
				
				$cont_1393++;
			}
		}//fin foreach
		$parrafo_archivos_ejecutando.="</table>";
	}//fin if hubo resultados en 1393
	else
	{
		$parrafo_archivos_ejecutando.="<h5>ARCHIVOS REPARADOS 1393</h5>";
		$parrafo_archivos_ejecutando.="<h7>No se encontraron archivos reparados previamente.</h5>";
	}
	//fin 1393
	
	$parrafo_archivos_ejecutando.="</div>";
	
	$parrafo_archivos_ejecutando.="<br><br>";
	
	if($parrafo_archivos_ejecutando!="")
	{
		
		echo "<p>$parrafo_archivos_ejecutando</p>";
	}	
}
else
{
	
}
?>