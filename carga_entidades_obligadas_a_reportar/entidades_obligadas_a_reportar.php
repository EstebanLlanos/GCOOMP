<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once "procesar_mensaje.php";

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

session_write_close();

//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO POR LOGUEO

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

$eapb="";
$eapb.="<div id='div_eapb'>";
//$eapb.="<select id='eapb' name='eapb' class='campo_azul' onchange='consultar_prestador();' >";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'  >";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

if(intval($perfil_usuario_actual)==5 && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
	$sql_consulta_eapb_usuario_prestador.=";";

	$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad no es eapb, por lo tanto busca la eapb asociada a la entidad
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad es de tipo eapb

$eapb.="</select>";
$eapb.="</div>";
//FIN

/*
//selector periodos
$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo_bd)
{
	$cod_periodo=$periodo_bd["cod_periodo"];
	$nombre_periodo=$periodo_bd["nombre_periodo"];
	$fecha_inicio=$periodo_bd["fecha_inicio_periodo"];
	$fecha_final=$periodo_bd["fecha_final_periodo"];
	$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo)</option>";
}
$selector_periodo.="</select>";
//fin selector periodos
*/

/*
//SELECTOR PRESTADORES ASOCIADOS EAPB 
$prestador="";
$prestador.="<div id='div_prestador'>";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
$prestador.="</select>";
$prestador.="</div>";
//FIN
*/

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');

$mensaje="";
$mostrarResultado="none";

$smarty->assign("campo_eapb", $eapb, true);

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('entidades_obligadas_a_reportar.html.tpl');

//echo "<script>consultar_prestador();</script>";

if(isset($_FILES["ent_obl_a_rep_file"]) && isset($_POST["oculto_envio"])
   && isset($_POST["eapb"]) && $_POST["eapb"]!="none" && $_POST["oculto_envio"]=="envio" && $_FILES["ent_obl_a_rep_file"]["error"] == 0)
{
	$rutaTemporal = '../TEMPORALES/';
	
	$codigo_eapb=$_POST["eapb"];
	
	$mensajes_error_bd="";
	$mensajes_error_normales="";
	$mensajes_exitosos_bd="";
	
	$archivo_entidades_obligadas_a_reportar=$_FILES["ent_obl_a_rep_file"];
	$ruta_archivo_entidades_obligadas_a_reportar = $rutaTemporal . $archivo_entidades_obligadas_a_reportar['name'];
	move_uploaded_file($archivo_entidades_obligadas_a_reportar['tmp_name'], $ruta_archivo_entidades_obligadas_a_reportar);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_entidades_obligadas_a_reportar)); 
	$archivo_cargar = fopen($ruta_archivo_entidades_obligadas_a_reportar, 'r') or exit("No se pudo abrir el archivo con los datos");
	$numero_linea=1;
	while (!feof($archivo_cargar)) 
	{
		$linea = fgets($archivo_cargar);
		$linea_res = procesar_mensaje($linea);
		$campos = explode(",",$linea_res);
		
		$cont_pre_campos=0;
		while($cont_pre_campos<count($campos))
		{
			$campos[$cont_pre_campos]=trim($campos[$cont_pre_campos]);
			$cont_pre_campos++;		
		}
		
		//validacion antes de cargar
		$hubo_errores=false;
		$existe_entidad=false;
		if($linea_res!="" && count($campos)==14)
		{
			if($_POST["eapb"]!=$campos[0])
			{
				$mensajes_error_normales.="El codigo de la EAPB en el archivo, con linea $numero_linea no corresponde con El codigo EAPB registrado. <br>";
				$hubo_errores=true;
			}
			else
			{
				$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
				$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
				$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$codigo_eapb."' ";
				$error_bd="";
				$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar_no_warning_get_error($sql_consulta_prestadores_asociados_eapb,$error_bd);
				if($error_bd!="")
				{
					$mensajes_error_bd.="ERROR AL CONSULTAR LOS PRESTADORES ASOCIADOS DE LA EAPB. <br>";
					$hubo_errores=true;
				}
				
				if(count($resultado_query_prestadores_asociados_eapb)>0 && is_array($resultado_query_prestadores_asociados_eapb))
				{
					$existe_asociacion_con_prestador=false;
					foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado)
					{
						if($prestador_asociado["codigo_entidad"]==$campos[2])
						{
							$existe_asociacion_con_prestador=true;
						}
					}
					
					if($existe_asociacion_con_prestador==false)
					{
						$query_asociar_prestadores_con_eapb="";
						$query_asociar_prestadores_con_eapb.="INSERT INTO gioss_relacion_entre_entidades_salud";
						$query_asociar_prestadores_con_eapb.="(entidad1,entidad2)";
						$query_asociar_prestadores_con_eapb.=" VALUES ";
						$query_asociar_prestadores_con_eapb.="('".$campos[2]."','$codigo_eapb');";
						$error_bd="";
						$bool_funciono_asociacion=$coneccionBD->insertar_no_warning_get_error($query_asociar_prestadores_con_eapb,$error_bd);
						if($error_bd!="")
						{
							$mensajes_error_bd.="ERROR AL ASOCIAR LOS PRESTADORES A LA EAPB EN LA LINEA $numero_linea. <br>";							
							$hubo_errores=true;
							
							$existe_prestador="";
							$existe_prestador.="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$campos[2]."' ; ";
							$error_bd="";
							$resultado_query_existe_prestador=$coneccionBD->consultar_no_warning_get_error($existe_prestador,$error_bd);
							if($error_bd!="")
							{
								$mensajes_error_bd.="ERROR AL CONSULTAR EXISTENCIA DEL PRESTADOR. <br>";
								$hubo_errores=true;
							}
							if(count($resultado_query_existe_prestador)==0 || !is_array($resultado_query_existe_prestador))
							{
								$mensajes_error_normales.="El prestador ".$campos[2]." de la linea $numero_linea no existe  <br>";
								$hubo_errores=true;
							}
							
						}
						else
						{
							$mensajes_exitosos_bd.="Se asocio el prestador ".$campos[2]." a la EAPB $codigo_eapb , debido a que no estaba asociada .<br> ";
						}
					}//fin if no hay asociacion con dicho prestador
				}
				else
				{
					$mensajes_error_normales.="La EAPB no tiene prestadores asociados <br>";
					$hubo_errores=true;
				}
			}
			
			if(!ctype_digit($campos[10]))
			{
				$mensajes_error_normales.="El a&ntildeo no es un numero entero <br>";
				$hubo_errores=true;
			}
			
			$query_verificar_entidad_obligada_a_reportar="";
			$query_verificar_entidad_obligada_a_reportar.="SELECT * FROM gioss_entidades_obligadas_a_reportar WHERE ";
			$query_verificar_entidad_obligada_a_reportar.=" codigo_eapb='".$campos[0]."' ";
			$query_verificar_entidad_obligada_a_reportar.=" AND codigo_prestador='".$campos[2]."' ";
			$query_verificar_entidad_obligada_a_reportar.=" AND tipo_informacion_a_reportar='".$campos[7]."' ";
			$query_verificar_entidad_obligada_a_reportar.=" AND tipo_archivo_norma='".$campos[8]."' ";
			$query_verificar_entidad_obligada_a_reportar.=" AND year_actual='".$campos[11]."' ";
			$query_verificar_entidad_obligada_a_reportar.=" ; ";
			$error_bd="";
			$resultados_entidades_que_fueron_obligadas_a_reportar=array();
			$resultados_entidades_que_fueron_obligadas_a_reportar=$coneccionBD->consultar_no_warning_get_error($query_verificar_entidad_obligada_a_reportar, $error_bd);
			if(count($resultados_entidades_que_fueron_obligadas_a_reportar)>0)
			{
				$existe_entidad=true;
			}
			
		}//fin if
		//fin validacion antes de cargar
		
		//echo "<script>alert('".count($campos)."');</script>";
		
		if($linea_res!="" && count($campos)==14 && $hubo_errores==false)
		{
			$query_upsert_entidades_obligadas_a_reportar="";
			if($existe_entidad==false)
			{
				$query_upsert_entidades_obligadas_a_reportar.="INSERT into gioss_entidades_obligadas_a_reportar ";
				$query_upsert_entidades_obligadas_a_reportar.="(";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_eapb,";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_regimen,";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_prestador,";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_identificacion,";
				$query_upsert_entidades_obligadas_a_reportar.="numero_identificacion,";
				$query_upsert_entidades_obligadas_a_reportar.="nombre_razon_social_prestador,";
				$query_upsert_entidades_obligadas_a_reportar.="estado_prestador,";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_informacion_a_reportar,";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_archivo_norma,";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_municipio,";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_departamento,";			
				$query_upsert_entidades_obligadas_a_reportar.="year_actual,";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_sede_principal,";
				$query_upsert_entidades_obligadas_a_reportar.="nombre_prestador_sede_principal";
				$query_upsert_entidades_obligadas_a_reportar.=")";
				$query_upsert_entidades_obligadas_a_reportar.=" VALUES ";
				$query_upsert_entidades_obligadas_a_reportar.="(";
				$cont_campos=0;
				while($cont_campos<count($campos))
				{
					if($cont_campos<(count($campos)-1)) //los demas campos
					{
						$query_upsert_entidades_obligadas_a_reportar.="'".$campos[$cont_campos]."',";
					}
					else if($cont_campos==(count($campos)-1))//ultimo campo sin coma
					{
						$query_upsert_entidades_obligadas_a_reportar.="'".$campos[$cont_campos]."'";
					}
					$cont_campos++;
				}
				$query_upsert_entidades_obligadas_a_reportar.=")";
				$query_upsert_entidades_obligadas_a_reportar.=";";
			}//fin if si existe entidad
			else
			{
				$query_upsert_entidades_obligadas_a_reportar.="UPDATE gioss_entidades_obligadas_a_reportar SET ";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_eapb='".$campos[0]."',";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_regimen='".$campos[1]."',";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_prestador='".$campos[2]."',";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_identificacion='".$campos[3]."',";
				$query_upsert_entidades_obligadas_a_reportar.="numero_identificacion='".$campos[4]."',";
				$query_upsert_entidades_obligadas_a_reportar.="nombre_razon_social_prestador='".$campos[5]."',";
				$query_upsert_entidades_obligadas_a_reportar.="estado_prestador='".$campos[6]."',";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_informacion_a_reportar='".$campos[7]."',";
				$query_upsert_entidades_obligadas_a_reportar.="tipo_archivo_norma='".$campos[8]."',";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_municipio='".$campos[9]."',";
				$query_upsert_entidades_obligadas_a_reportar.="codigo_departamento='".$campos[10]."',";			
				$query_upsert_entidades_obligadas_a_reportar.="year_actual='".$campos[11]."'";
				$query_upsert_entidades_obligadas_a_reportar.=" WHERE ";
				$query_upsert_entidades_obligadas_a_reportar.=" codigo_eapb='".$campos[0]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=" AND codigo_prestador='".$campos[2]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=" AND tipo_informacion_a_reportar='".$campos[7]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=" AND tipo_archivo_norma='".$campos[8]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=" AND year_actual='".$campos[11]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=" AND codigo_sede_principal='".$campos[12]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=" AND nombre_prestador_sede_principal='".$campos[13]."' ";
				$query_upsert_entidades_obligadas_a_reportar.=";";
			}
			$error_bd="";			
			$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_upsert_entidades_obligadas_a_reportar, $error_bd);
			if($error_bd!="")
			{
				$mensajes_error_bd.="ERROR AL CARGAR LA ENTIDAD EN LA LINEA $numero_linea OBLIGADAS A REPORTAR: ".procesar_mensaje($error_bd)." <br> ";
			}
			else
			{
				if($existe_entidad==false)
				{
					$mensajes_exitosos_bd.="La entidad ".$campos[2]." de la linea $numero_linea se subio correctamente. <br> ";
				}
				else
				{
					$mensajes_exitosos_bd.="La entidad ".$campos[2]." de la linea $numero_linea se actualizo correctamente. <br> ";
				}
			}//fin else
			
			//echo "<script>alert('entro antes $error_bd  ');</script>";
			//parte donde se consultan los datos adicionales
			if($existe_entidad==false && $error_bd=="" && $campos[7]=="02")//CUANDO ES 4505
			{
				//echo "<script>alert('entro datos adicionales');</script>";
				$estado_validacion_periodo_1=3;
				$numero_registros_periodo_1=0;
				
				$estado_validacion_periodo_2=3;
				$numero_registros_periodo_2=0;
				
				$estado_validacion_periodo_3=3;
				$numero_registros_periodo_3=0;
				
				$estado_validacion_periodo_4=3;
				$numero_registros_periodo_4=0;
				
				$query_consultar_estado_cumplimiento_periodo_1="";
				$query_consultar_estado_cumplimiento_periodo_1.=" SELECT * FROM gioss_tabla_estado_informacion_4505 WHERE ";
				$query_consultar_estado_cumplimiento_periodo_1.=" codigo_eapb='".$campos[0]."' ";
				$query_consultar_estado_cumplimiento_periodo_1.=" AND codigo_prestador_servicios='".$campos[2]."' ";
				$query_consultar_estado_cumplimiento_periodo_1.=" AND (fecha_corte_periodo BETWEEN '".$campos[11]."-01-01' AND  '".$campos[11]."-12-01') ";
				$query_consultar_estado_cumplimiento_periodo_1.=" AND periodo_reporte='1' ";
				$query_consultar_estado_cumplimiento_periodo_1.=" ; ";
				$error_bd="";
				$resultados_periodo_1=array();
				$resultados_periodo_1=$coneccionBD->consultar_no_warning_get_error($query_consultar_estado_cumplimiento_periodo_1, $error_bd);
				if(count($resultados_periodo_1)>0)//ya se hizo el arreglo en clase_conexion para esta salvedad
				{
					$estado_validacion_periodo_1=$resultados_periodo_1[0]["codigo_estado_informacion"];
					$numero_registros_periodo_1=$resultados_periodo_1[0]["total_registros"];
				}
				
				$query_consultar_estado_cumplimiento_periodo_2="";
				$query_consultar_estado_cumplimiento_periodo_2.=" SELECT * FROM gioss_tabla_estado_informacion_4505 WHERE ";
				$query_consultar_estado_cumplimiento_periodo_2.=" codigo_eapb='".$campos[0]."' ";
				$query_consultar_estado_cumplimiento_periodo_2.=" AND codigo_prestador_servicios='".$campos[2]."' ";
				$query_consultar_estado_cumplimiento_periodo_2.=" AND (fecha_corte_periodo BETWEEN '".$campos[11]."-01-01' AND  '".$campos[11]."-12-01') ";
				$query_consultar_estado_cumplimiento_periodo_2.=" AND periodo_reporte='2' ";
				$query_consultar_estado_cumplimiento_periodo_2.=" ; ";
				$error_bd="";
				$resultados_periodo_2=array();
				$resultados_periodo_2=$coneccionBD->consultar_no_warning_get_error($query_consultar_estado_cumplimiento_periodo_2, $error_bd);
				if(count($resultados_periodo_2)>0)//ya se hizo el arreglo en clase_conexion para esta salvedad
				{
					$estado_validacion_periodo_2=$resultados_periodo_2[0]["codigo_estado_informacion"];
					$numero_registros_periodo_2=$resultados_periodo_2[0]["total_registros"];
				}
				
				$query_consultar_estado_cumplimiento_periodo_3="";
				$query_consultar_estado_cumplimiento_periodo_3.=" SELECT * FROM gioss_tabla_estado_informacion_4505 WHERE ";
				$query_consultar_estado_cumplimiento_periodo_3.=" codigo_eapb='".$campos[0]."' ";
				$query_consultar_estado_cumplimiento_periodo_3.=" AND codigo_prestador_servicios='".$campos[2]."' ";
				$query_consultar_estado_cumplimiento_periodo_3.=" AND (fecha_corte_periodo BETWEEN '".$campos[11]."-01-01' AND  '".$campos[11]."-12-01') ";
				$query_consultar_estado_cumplimiento_periodo_3.=" AND periodo_reporte='3' ";
				$query_consultar_estado_cumplimiento_periodo_3.=" ; ";
				$error_bd="";
				$resultados_periodo_3=array();
				$resultados_periodo_3=$coneccionBD->consultar_no_warning_get_error($query_consultar_estado_cumplimiento_periodo_3, $error_bd);
				if(count($resultados_periodo_3)>0)//ya se hizo el arreglo en clase_conexion para esta salvedad
				{
					$estado_validacion_periodo_3=$resultados_periodo_3[0]["codigo_estado_informacion"];
					$numero_registros_periodo_3=$resultados_periodo_3[0]["total_registros"];
				}
				
				$query_consultar_estado_cumplimiento_periodo_4="";
				$query_consultar_estado_cumplimiento_periodo_4.=" SELECT * FROM gioss_tabla_estado_informacion_4505 WHERE ";
				$query_consultar_estado_cumplimiento_periodo_4.=" codigo_eapb='".$campos[0]."' ";
				$query_consultar_estado_cumplimiento_periodo_4.=" AND codigo_prestador_servicios='".$campos[2]."' ";
				$query_consultar_estado_cumplimiento_periodo_4.=" AND (fecha_corte_periodo BETWEEN '".$campos[11]."-01-01' AND  '".$campos[11]."-12-01') ";
				$query_consultar_estado_cumplimiento_periodo_4.=" AND periodo_reporte='4' ";
				$query_consultar_estado_cumplimiento_periodo_4.=" ; ";
				$error_bd="";
				$resultados_periodo_4=array();
				$resultados_periodo_4=$coneccionBD->consultar_no_warning_get_error($query_consultar_estado_cumplimiento_periodo_4, $error_bd);
				if(count($resultados_periodo_4)>0)//ya se hizo el arreglo en clase_conexion para esta salvedad
				{
					$estado_validacion_periodo_4=$resultados_periodo_4[0]["codigo_estado_informacion"];
					$numero_registros_periodo_4=$resultados_periodo_4[0]["total_registros"];
				}
				
				$query_update_datos_adicionales="";
				$query_update_datos_adicionales.="UPDATE gioss_entidades_obligadas_a_reportar SET ";
				$query_update_datos_adicionales.="periodo_1='".$estado_validacion_periodo_1."',";
				$query_update_datos_adicionales.="periodo_2='".$estado_validacion_periodo_2."',";
				$query_update_datos_adicionales.="periodo_3='".$estado_validacion_periodo_3."',";
				$query_update_datos_adicionales.="periodo_4='".$estado_validacion_periodo_4."',";
				$query_update_datos_adicionales.="numero_registros_periodo_1='".$numero_registros_periodo_1."',";
				$query_update_datos_adicionales.="numero_registros_periodo_2='".$numero_registros_periodo_2."',";
				$query_update_datos_adicionales.="numero_registros_periodo_3='".$numero_registros_periodo_3."',";
				$query_update_datos_adicionales.="numero_registros_periodo_4='".$numero_registros_periodo_4."'";
				$query_update_datos_adicionales.=" WHERE ";
				$query_update_datos_adicionales.=" codigo_eapb='".$campos[0]."' ";
				$query_update_datos_adicionales.=" AND codigo_prestador='".$campos[2]."' ";
				$query_update_datos_adicionales.=" AND tipo_informacion_a_reportar='".$campos[7]."' ";
				$query_update_datos_adicionales.=" AND tipo_archivo_norma='".$campos[8]."' ";
				$query_update_datos_adicionales.=" AND year_actual='".$campos[11]."' ";
				$query_update_datos_adicionales.=";";
				$error_bd="";			
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_update_datos_adicionales, $error_bd);
				if($error_bd!="")
				{
					$mensajes_error_bd.="ERROR AL ACTUALIZAR DATOS ADICIONALES LA ENTIDAD EN LA LINEA ".($numero_linea+1)." OBLIGADAS A REPORTAR: ".procesar_mensaje($error_bd)." <br> ";
				}
				
			}//FIN IF
			//fin parte dodne se consultan los datos adicionales
			
		}//fin if
		else if(count($campos)!=14)
		{
			$mensajes_error_normales.="El numero de campos es incorrecto, deben ser 14 campos en la linea $numero_linea y son solo ".count($campos).". <br>";
		}
		else if($linea_res=="")
		{
			$mensajes_error_normales.="La linea $numero_linea esta en blanco. <br>";
		}
		$numero_linea++;
	}//fin while lectura linea
	
	if($mensajes_error_bd!="" || $mensajes_error_normales!="")
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_error').innerHTML=\"<u>Error en la carga de las entidades obligadas a reportar:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML=\"$mensajes_error_bd $mensajes_error_normales\";</script>";
	}
	else if($mensajes_exitosos_bd=="")
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_exito').innerHTML=\"<u>Carga de todas las entidades obligadas a reportar fue exitosa:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"No hubo errores al subir las entidades obligadas a reportar\";</script>";
	}
	
	if($mensajes_exitosos_bd!="")
	{
		echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_exito').innerHTML=\"<u>Carga de algunas entidades obligadas a reportar fue exitosa:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"$mensajes_exitosos_bd\";</script>";
	}
}
else if(isset($_POST["oculto_envio"]) && $_POST["oculto_envio"]=="envio")
{
	$mensaje_error="";
	
	if($_POST["eapb"]=="none")
	{
		$mensaje_error.="Seleccione la EAPB para la cual se subiran las entidades obligadas a reportar. <br>";
	}
	
	
	
	if ($_FILES["ent_obl_a_rep_file"]["error"] > 0)
	{
		$mensaje_error.="No hay un archivo subido. <br>";
	}
	
	if($mensaje_error!="")
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('titulo_mensaje_error').innerHTML=\"<u>Error en la carga de las entidades obligadas a reportar:</u>\";</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML=\"$mensaje_error\";</script>";
	}
}

?>