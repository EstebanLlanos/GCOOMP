<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/crear_zip.php';

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mostrarResultado = "<div id='mostrar_resultado_div'></div>";
$mensaje="<div id='mensaje_div'></div>";

//consultar en gios_usuario_entidad_prestadora_eapb , la cual contiene la relacion entre usuario-ips-eapb 

//SELECTOR PRESTADOR-ASOCIADO-USUARIO
$prestador="";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' onchange='consultar_eapb(\"".$tipo_id."\",\"".$identificacion."\");'>";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
/*
$sql_consulta_prestador_usuario="SELECT gios_prestador_servicios_salud.cod_registro_especial_pss,gios_prestador_servicios_salud.nom_entidad_prestadora FROM ";
$sql_consulta_prestador_usuario.=" gios_usuario_entidad_prestadora_eapb INNER JOIN gios_prestador_servicios_salud ON (gios_usuario_entidad_prestadora_eapb.cod_registro_especial_pss = gios_prestador_servicios_salud.cod_registro_especial_pss) ";
$sql_consulta_prestador_usuario.=" WHERE tipo_identificacion_usuario='".$tipo_id."' AND  identificacion_usuario='".$identificacion."'; ";
*/
$sql_consulta_prestador_usuario="SELECT pss.codigo_entidad,pss.nombre_de_la_entidad FROM ";
$sql_consulta_prestador_usuario.=" gioss_entidad_nicklogueo_perfil_estado_persona nu INNER JOIN gioss_entidades_sector_salud pss ON (nu.entidad = pss.codigo_entidad) ";
$sql_consulta_prestador_usuario.=" WHERE nu.tipo_id='".$tipo_id."' AND  nu.identificacion_usuario='".$identificacion."' AND nu.entidad='".$entidad_salud_usuario_actual."'; ";
$resultado_query_prestador_usuario=$coneccionBD->consultar2($sql_consulta_prestador_usuario);

if(count($resultado_query_prestador_usuario)>0)
{
	foreach($resultado_query_prestador_usuario as $prestador_usuario_res)
	{
		$prestador.="<option value='".$prestador_usuario_res['codigo_entidad']."' selected>".$prestador_usuario_res['nombre_de_la_entidad']."</option>";
	}
}


$prestador.="</select>";
//FIN

//SELECTOR EAPB-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
$sql_consulta_eapb_usuario_prestador.=";";

$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

if(count($resultado_query_eapb_usuario)>0)
{
	foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
	{
		$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."'>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
	}
}
$eapb.="</select>";
$eapb.="</div>";
//FIN

/*
$selector_fechas_corte="";
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
$selector_fechas_corte.="<option value='3-31'>Periodo 1 (Marzo 31)</option>";
$selector_fechas_corte.="<option value='6-30'>Periodo 2 (Junio 30)</option>";
$selector_fechas_corte.="<option value='9-30'>Periodo 3 (Septiembre 30)</option>";
$selector_fechas_corte.="<option value='12-31'>Periodo 4 (Diciembre 31)</option>";
$selector_fechas_corte.="</select>";
*/

$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);
$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='consultar_numeros_remision();' >\n";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>\n";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["cod_periodo"];
	$nombre_periodo=$periodo["nombre_periodo"];
	$fecha_de_corte=$periodo["fecha_corte"];
	$selector_periodo.="<option value='$fecha_de_corte'>Periodo $cod_periodo ($nombre_periodo $fecha_de_corte)</option>\n";
}
$selector_periodo.="</select>\n";



$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);

$smarty->assign("campo_fechas_corte", $selector_periodo, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('inconsRIPS3374.html.tpl');

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$esta_validado_exitoso=false;
$existe_validacion=false;

$numero_registros=0;
$contador_offset=0;
$hubo_resultados=false;
$nombre_archivo_inconsistencias="";

if(isset($_POST["tipo_busqueda"]))
{
	$codigo_eapb=$_POST["eapb"];
	$codigo_entidad_reportadora=$_POST["prestador"];
	
	//PARTE QUE VERIFICA LA EXISTENCIA Y EL ESTADO DE LA VALIDACION
	if($_POST["tipo_busqueda"]=="1" && isset($_POST["numero_secuencia"]) )
	{
		$numero_secuencia_para_bd=$_POST["numero_secuencia"];
		
		$query_estado_validacion="";
		$query_estado_validacion.="SELECT * FROM gioss_tabla_consolidacion_registros_validados_rips WHERE numero_secuencia='$numero_secuencia_para_bd' AND codigo_eapb='$codigo_eapb' AND codigo_entidad_reportadora='$codigo_entidad_reportadora'; ";
		$resultado_query_estado_validacion=$coneccionBD->consultar2($query_estado_validacion);
		
		if(count($resultado_query_estado_validacion)>0)
		{
			$existe_validacion=true;
			if(intval($resultado_query_estado_validacion[0]["estado_validacion"])==1)
			{
				$esta_validado_exitoso=true;
			}
			else if(intval($resultado_query_estado_validacion[0]["estado_validacion"])==2)
			{
				$esta_validado_exitoso=false;
			}
		}
		else
		{
			$existe_validacion=false;
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede efectuar la consulta, no existen datos asociados al parametro de busqueda digitado.';</script>";
		}
	}
	if($_POST["tipo_busqueda"]=="2" && isset($_POST["numero_remision"]) && isset($_POST["numeros_secuencias_varios"])  && strlen($_POST["numeros_secuencias_varios"])>0)
	{
		$numero_secuencia_para_bd=$_POST["numeros_secuencias_varios"];
		
		$query_estado_validacion="";
		$query_estado_validacion.="SELECT * FROM gioss_tabla_consolidacion_registros_validados_rips WHERE numero_secuencia='$numero_secuencia_para_bd' AND codigo_eapb='$codigo_eapb' AND codigo_entidad_reportadora='$codigo_entidad_reportadora'; ";
		$resultado_query_estado_validacion=$coneccionBD->consultar2($query_estado_validacion);
		
		if(count($resultado_query_estado_validacion)>0)
		{
			$existe_validacion=true;
			if(intval($resultado_query_estado_validacion[0]["estado_validacion"])==1)
			{
				$esta_validado_exitoso=true;
			}
			else if(intval($resultado_query_estado_validacion[0]["estado_validacion"])==2)
			{
				$esta_validado_exitoso=false;
			}
		}
		else
		{
			$existe_validacion=false;
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede efectuar la consulta, no existen datos asociados al parametro de busqueda digitado.';</script>";
		}
	}
	if($_POST["tipo_busqueda"]=="3" && isset($_POST["numeros_remision_varios"])  && strlen($_POST["numeros_remision_varios"])>0)
	{
		$numero_secuencia_para_bd=$_POST["numeros_remision_varios"];
		
		$query_estado_validacion="";
		$query_estado_validacion.="SELECT * FROM gioss_tabla_consolidacion_registros_validados_rips WHERE numero_secuencia='$numero_secuencia_para_bd' AND codigo_eapb='$codigo_eapb' AND codigo_entidad_reportadora='$codigo_entidad_reportadora'; ";
		$resultado_query_estado_validacion=$coneccionBD->consultar2($query_estado_validacion);
		
		if(count($resultado_query_estado_validacion)>0)
		{
			$existe_validacion=true;
			if(intval($resultado_query_estado_validacion[0]["estado_validacion"])==1)
			{
				$esta_validado_exitoso=true;
			}
			else if(intval($resultado_query_estado_validacion[0]["estado_validacion"])==2)
			{
				$esta_validado_exitoso=false;
			}
		}
		else
		{
			$existe_validacion=false;
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede efectuar la consulta, no existen datos asociados al parametro de busqueda digitado.';</script>";
		}
	}
	//FIN PARTE QUE VERIFICA LA EXISTENCIA Y EL ESTADO DE LA VALIDACION
	
	
	
	//if si tiene inconsistencias
	if($existe_validacion==true)
	{
		if($_POST["tipo_busqueda"]=="1" && isset($_POST["numero_secuencia"]) )
		{
			//POR NUMERO DE SECUENCIA
			$numero_secuencia_para_bd=$_POST["numero_secuencia"];
			
			$sql_numero_registros="";
			$sql_numero_registros.="SELECT count(*) as contador FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd';  ";
			$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
			$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
			
			$sql_vista_inconsistencias="";
			$sql_vista_inconsistencias.="CREATE OR REPLACE VIEW vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion."_".$nick_user."_".$tipo_id."_".$identificacion." ";
			$sql_vista_inconsistencias.=" AS SELECT * FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd' order by numero_linea, numero_campo ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias, $error_bd_seq);
			
			//echo "<script>alert('se genero vista $numero_registros');</script>";
			
			$cont_linea=1;
			while($contador_offset<$numero_registros)
			{
				$limite=2000;
				
				if( ($contador_offset+2000)>=$numero_registros)
				{
					$limite=2000+($numero_registros-$contador_offset);
				}
			
				//Ejemplo: SELECT *  FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." WHERE numero_orden='29'  order by numero_linea, numero_campo limit 5 offset 0; 
				$sql_query_busqueda="";
				$sql_query_busqueda.="SELECT * FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion."_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
				$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
			
				if(count($resultado_query_inconsistencias)>0)
				{
					$nombre_ct=$resultado_query_inconsistencias[0]["nombre_archivo_ct"];
					$numero_seq=$resultado_query_inconsistencias[0]["numero_orden"];
					$nombre_archivo_inconsistencias=$nombre_ct."_inconsistencias.csv";
					$ruta_archivo_inconsistencias=$rutaTemporal.$nombre_archivo_inconsistencias;
					
					//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
					if($hubo_resultados==false)
					{
						$file_inconsistencias= fopen($ruta_archivo_inconsistencias, "w") or die("fallo la creacion del archivo");
						fclose($file_inconsistencias);
					}
					
					$hubo_resultados=true;
					//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
					
					
					$file_inconsistencias= fopen($ruta_archivo_inconsistencias, "a") or die("fallo la creacion del archivo");
					
					$titulos="";
					$titulos.="consecutivo,numero de secuencia,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
					$titulos.="codigo detalle inconsistencia,detalle inconsistencia,nombre archivo rips, numero de linea, numero de campo";
					fwrite($file_inconsistencias, $titulos."\n");
					
					foreach($resultado_query_inconsistencias as $resultado)
					{
						$linea_inconsistencia="";
						$linea_inconsistencia.=$cont_linea.",".$resultado["nombre_archivo_ct"].",".$resultado["cod_tipo_inconsitencia"].",";
						$linea_inconsistencia.=$resultado["nombre_tipo_inconsistencia"].",".$resultado["cod_grupo_inconsistencia"].",".$resultado["nombre_grupo_inconsistencia"].",";
						$linea_inconsistencia.=$resultado["cod_detalle_inconsistencia"].",".$resultado["detalle_inconsistencia"].",".$resultado["nombre_archivo_rips"].",";
						$linea_inconsistencia.=$resultado["numero_linea"].",".$resultado["numero_campo"];
						fwrite($file_inconsistencias, $linea_inconsistencia."\n");
						
						echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros.';</script>";
						
						$cont_linea++;
					}
					fclose($file_inconsistencias);
				
					
					
					
				}//fin if hallo resultados
				
				$contador_offset+=2000;
			
			}//fin while
			
			if($hubo_resultados)
			{
				//CREAR ZIP
				$archivos_a_comprimir=array();
				$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias;
				$ruta_zip=$rutaTemporal.$nombre_archivo_inconsistencias.'.zip';
				$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
				ob_flush();
				flush();
				//FIN CREAR ZIP
				
				echo "<script>document.getElementById('grilla').style.display='inline';</script>";
					
				$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con las inconsistencias consultadas para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
			
				echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_descarga';</script>";
				
			}
			else
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede efectuar la consulta, no existen datos asociados al parametro de busqueda digitado.';</script>";
			}		
			
			
		}//fin if
		if($_POST["tipo_busqueda"]=="2" && isset($_POST["numero_remision"]) && isset($_POST["numeros_secuencias_varios"])  && strlen($_POST["numeros_secuencias_varios"])>0 )
		{
			//POR NUMERO DE REMISION
			$numero_secuencia_para_bd=$_POST["numeros_secuencias_varios"];
			
			$sql_numero_registros="";
			$sql_numero_registros.="SELECT count(*) as contador FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd';  ";
			$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
			$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
			
			$sql_vista_inconsistencias="";
			$sql_vista_inconsistencias.="CREATE OR REPLACE VIEW vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." ";
			$sql_vista_inconsistencias.=" AS SELECT * FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd' order by numero_linea, numero_campo ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias, $error_bd_seq);
			
			//echo "<script>alert('se genero vista $numero_registros');</script>";
			
			$cont_linea=1;
			while($contador_offset<$numero_registros)
			{
				$limite=2000;
				
				if( ($contador_offset+2000)>=$numero_registros)
				{
					$limite=2000+($numero_registros-$contador_offset);
				}
			
				//Ejemplo: SELECT *  FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." WHERE nombre_archivo_ct='CT054037'  order by numero_linea, numero_campo limit 5 offset 0; 
				$sql_query_busqueda="";
				$sql_query_busqueda.="SELECT * FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
				$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
			
				if(count($resultado_query_inconsistencias)>0)
				{
					$nombre_ct=$resultado_query_inconsistencias[0]["nombre_archivo_ct"];
					$numero_seq=$resultado_query_inconsistencias[0]["numero_orden"];
					$nombre_archivo_inconsistencias=$nombre_ct."_inconsistencias.csv";
					$ruta_archivo_inconsistencias=$rutaTemporal.$nombre_archivo_inconsistencias;
					
					//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
					if($hubo_resultados==false)
					{
						$file_inconsistencias= fopen($ruta_archivo_inconsistencias, "w") or die("fallo la creacion del archivo");
						fclose($file_inconsistencias);
					}
					
					$hubo_resultados=true;
					//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
					
					
					$file_inconsistencias= fopen($ruta_archivo_inconsistencias, "a") or die("fallo la creacion del archivo");
					
					$titulos="";
					$titulos.="consecutivo,numero de secuencia,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
					$titulos.="codigo detalle inconsistencia,detalle inconsistencia,nombre archivo rips, numero de linea, numero de campo";
					fwrite($file_inconsistencias, $titulos."\n");
					
					foreach($resultado_query_inconsistencias as $resultado)
					{
						$linea_inconsistencia="";
						$linea_inconsistencia.=$cont_linea.",".$resultado["nombre_archivo_ct"].",".$resultado["cod_tipo_inconsitencia"].",";
						$linea_inconsistencia.=$resultado["nombre_tipo_inconsistencia"].",".$resultado["cod_grupo_inconsistencia"].",".$resultado["nombre_grupo_inconsistencia"].",";
						$linea_inconsistencia.=$resultado["cod_detalle_inconsistencia"].",".$resultado["detalle_inconsistencia"].",".$resultado["nombre_archivo_rips"].",";
						$linea_inconsistencia.=$resultado["numero_linea"].",".$resultado["numero_campo"];
						fwrite($file_inconsistencias, $linea_inconsistencia."\n");
						
						echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros.';</script>";
						
						$cont_linea++;
					}
					fclose($file_inconsistencias);
				
					
					
					
				}//fin if hallo resultados
				
				$contador_offset+=2000;
			
			}//fin while
			
			if($hubo_resultados)
			{
				//CREAR ZIP
				$archivos_a_comprimir=array();
				$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias;
				$ruta_zip=$rutaTemporal.$nombre_archivo_inconsistencias.'.zip';
				$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
				ob_flush();
				flush();
				//FIN CREAR ZIP
				
				echo "<script>document.getElementById('grilla').style.display='inline';</script>";
					
				$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con las inconsistencias consultadas para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
			
				echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_descarga';</script>";
				
			}
			else
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede efectuar la consulta, no existen datos asociados al parametro de busqueda digitado.';</script>";
			}		
			
		}//fin if
		if($_POST["tipo_busqueda"]=="3" && isset($_POST["numeros_remision_varios"])  && strlen($_POST["numeros_remision_varios"])>0)
		{
			//POR FECHA DE REMISION
			$numero_secuencia_para_bd=$_POST["numeros_remision_varios"];
			
			
			$sql_numero_registros="";
			$sql_numero_registros.="SELECT count(*) as contador FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd';  ";
			$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
			$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
			
			$sql_vista_inconsistencias="";
			$sql_vista_inconsistencias.="CREATE OR REPLACE VIEW vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." ";
			$sql_vista_inconsistencias.=" AS SELECT * FROM gioss_reporte_inconsistencia_archivos_rips WHERE numero_orden='$numero_secuencia_para_bd' order by numero_linea, numero_campo ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias, $error_bd_seq);
			
			//echo "<script>alert('se genero vista $numero_registros');</script>";
			
			$cont_linea=1;
			while($contador_offset<$numero_registros)
			{
				$limite=2000;
				
				if( ($contador_offset+2000)>=$numero_registros)
				{
					$limite=2000+($numero_registros-$contador_offset);
				}
			
				//Ejemplo: SELECT *  FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." WHERE nombre_archivo_ct='CT054037'  order by numero_linea, numero_campo limit 5 offset 0; 
				$sql_query_busqueda="";
				$sql_query_busqueda.="SELECT * FROM vista_inconsistencias_rips_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
				$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
			
				if(count($resultado_query_inconsistencias)>0)
				{
					$nombre_ct=$resultado_query_inconsistencias[0]["nombre_archivo_ct"];
					$numero_seq=$resultado_query_inconsistencias[0]["numero_orden"];
					$nombre_archivo_inconsistencias=$nombre_ct."_inconsistencias.csv";
					$ruta_archivo_inconsistencias=$rutaTemporal.$nombre_archivo_inconsistencias;
					
					//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
					if($hubo_resultados==false)
					{
						$file_inconsistencias= fopen($ruta_archivo_inconsistencias, "w") or die("fallo la creacion del archivo");
						fclose($file_inconsistencias);
					}
					
					$hubo_resultados=true;
					//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
					
					
					$file_inconsistencias= fopen($ruta_archivo_inconsistencias, "a") or die("fallo la creacion del archivo");
					
					$titulos="";
					$titulos.="consecutivo,numero de secuencia,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
					$titulos.="codigo detalle inconsistencia,detalle inconsistencia,nombre archivo rips, numero de linea, numero de campo";
					fwrite($file_inconsistencias, $titulos."\n");
					
					foreach($resultado_query_inconsistencias as $resultado)
					{
						$linea_inconsistencia="";
						$linea_inconsistencia.=$cont_linea.",".$resultado["nombre_archivo_ct"].",".$resultado["cod_tipo_inconsitencia"].",";
						$linea_inconsistencia.=$resultado["nombre_tipo_inconsistencia"].",".$resultado["cod_grupo_inconsistencia"].",".$resultado["nombre_grupo_inconsistencia"].",";
						$linea_inconsistencia.=$resultado["cod_detalle_inconsistencia"].",".$resultado["detalle_inconsistencia"].",".$resultado["nombre_archivo_rips"].",";
						$linea_inconsistencia.=$resultado["numero_linea"].",".$resultado["numero_campo"];
						fwrite($file_inconsistencias, $linea_inconsistencia."\n");
						
						echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros.';</script>";
						
						$cont_linea++;
					}
					fclose($file_inconsistencias);
				
					
					
					
				}//fin if hallo resultados
				
				$contador_offset+=2000;
			
			}//fin while
			
			if($hubo_resultados)
			{
				//CREAR ZIP
				$archivos_a_comprimir=array();
				$archivos_a_comprimir[0]=$ruta_archivo_inconsistencias;
				$ruta_zip=$rutaTemporal.$nombre_archivo_inconsistencias.'.zip';
				$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
				ob_flush();
				flush();
				//FIN CREAR ZIP
				
				echo "<script>document.getElementById('grilla').style.display='inline';</script>";
					
				$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con las inconsistencias consultadas para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
			
				echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_descarga';</script>";
				
			}
			else
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede efectuar la consulta, no existen datos asociados al parametro de busqueda digitado.';</script>";
			}
		}//fin if
	}//fin if encontro inconsistencias
	
	//if si fue validado como exitoso
	if($esta_validado_exitoso==true && $existe_validacion==true && $hubo_resultados==false)
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='El archivo consultado no presenta inconsistencias.';</script>";
			
	}
	if($esta_validado_exitoso==true && $existe_validacion==true && $hubo_resultados==true)
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='Archivo validado con exito con inconsistencias informativas.';</script>";	
	}
	//fin if si fue validado como exitoso
	
}//fin if se selecciono
//FIN PRTE BUSQUEDA

?>