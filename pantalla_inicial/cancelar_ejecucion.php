<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();

if(isset($_REQUEST["codigo_entidad_reportadora"])
   && isset($_REQUEST["nombre_archivo"])
   && isset($_REQUEST["fecha_remision"])
   && isset($_REQUEST["fecha_validacion"])
   && isset($_REQUEST["hora_validacion"])
   && isset($_REQUEST["nick_usuario"])
   && isset($_REQUEST["norma"])
   )
{
	$codigo_entidad_reportadora=$_REQUEST["codigo_entidad_reportadora"];
	$nombre_archivo=$_REQUEST["nombre_archivo"];
	$fecha_remision=$_REQUEST["fecha_remision"];
	$fecha_validacion=$_REQUEST["fecha_validacion"];
	$hora_validacion=$_REQUEST["hora_validacion"];
	$nick_usuario=$_REQUEST["nick_usuario"];
	$norma=$_REQUEST["norma"];
	
	if($norma=="norma_4505")
	{
		$query_descartar_archivo_4505="";
		$query_descartar_archivo_4505.=" UPDATE gioss_4505_esta_validando_actualmente ";
		$query_descartar_archivo_4505.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_4505.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_4505.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_4505.=" AND fecha_remision='$fecha_remision' ";
		$query_descartar_archivo_4505.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_4505.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_4505.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_4505.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_4505, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 4505.";
	}
	
	if($norma=="norma_0123")
	{
		$query_descartar_archivo_0123="";
		$query_descartar_archivo_0123.=" UPDATE gioss_0123_esta_validando_actualmente ";
		$query_descartar_archivo_0123.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_0123.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_0123.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_0123.=" AND fecha_remision='$fecha_remision' ";
		$query_descartar_archivo_0123.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_0123.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_0123.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_0123.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_0123, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 0123.";
	}
	
	if($norma=="norma_0247")
	{
		$query_descartar_archivo_0247="";
		$query_descartar_archivo_0247.=" UPDATE gioss_0247_esta_validando_actualmente ";
		$query_descartar_archivo_0247.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_0247.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_0247.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_0247.=" AND fecha_remision='$fecha_remision' ";
		$query_descartar_archivo_0247.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_0247.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_0247.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_0247.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_0247, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 0247.";
	}
	
	if($norma=="norma_4725")
	{
		$query_descartar_archivo_4725="";
		$query_descartar_archivo_4725.=" UPDATE gioss_4725_esta_validando_actualmente ";
		$query_descartar_archivo_4725.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_4725.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_4725.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_4725.=" AND fecha_remision='$fecha_remision' ";
		$query_descartar_archivo_4725.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_4725.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_4725.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_4725.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_4725, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 4725.";
	}
	
	if($norma=="norma_2463")
	{
		$query_descartar_archivo_2463="";
		$query_descartar_archivo_2463.=" UPDATE gioss_2463_esta_validando_actualmente ";
		$query_descartar_archivo_2463.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_2463.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_2463.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_2463.=" AND fecha_remision='$fecha_remision' ";
		$query_descartar_archivo_2463.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_2463.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_2463.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_2463, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 2463.";
	}
	
	if($norma=="norma_1393")
	{
		$query_descartar_archivo_1393="";
		$query_descartar_archivo_1393.=" UPDATE gioss_1393_esta_validando_actualmente ";
		$query_descartar_archivo_1393.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_1393.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_1393.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_1393.=" AND fecha_remision='$fecha_remision' ";
		$query_descartar_archivo_1393.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_1393.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_1393.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_1393.=" ; ";		
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_1393, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 1393.";
	}
	
	//REPORTE OBLIGATORIO CANCELAR
	
	if($norma=="ro_norma_4505")
	{
		$query_descartar_archivo_4505="";
		$query_descartar_archivo_4505.=" UPDATE gioss_4505_esta_consolidando_ro_actualmente ";
		$query_descartar_archivo_4505.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_4505.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_4505.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_4505.=" AND fecha_corte_periodo_consolidar='$fecha_remision' ";
		$query_descartar_archivo_4505.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_4505.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_4505.=" AND cod_eapb='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_4505.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_4505, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 4505.";
	}
	
	if($norma=="ro_norma_0123")
	{
		$query_descartar_archivo_0123="";
		$query_descartar_archivo_0123.=" UPDATE gioss_0123_esta_consolidando_ro_actualmente ";
		$query_descartar_archivo_0123.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_0123.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_0123.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_0123.=" AND fecha_corte_periodo_consolidar='$fecha_remision' ";
		$query_descartar_archivo_0123.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_0123.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_0123.=" AND cod_eapb='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_0123.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_0123, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 0123.";
	}
	
	if($norma=="ro_norma_0247")
	{
		$query_descartar_archivo_0247="";
		$query_descartar_archivo_0247.=" UPDATE gioss_0247_esta_consolidando_ro_actualmente ";
		$query_descartar_archivo_0247.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_0247.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_0247.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_0247.=" AND fecha_corte_periodo_consolidar='$fecha_remision' ";
		$query_descartar_archivo_0247.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_0247.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_0247.=" AND cod_eapb='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_0247.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_0247, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 0247.";
	}
	
	if($norma=="ro_norma_4725")
	{
		$query_descartar_archivo_4725="";
		$query_descartar_archivo_4725.=" UPDATE gioss_4725_esta_consolidando_ro_actualmente ";
		$query_descartar_archivo_4725.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_4725.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_4725.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_4725.=" AND fecha_corte_periodo_consolidar='$fecha_remision' ";
		$query_descartar_archivo_4725.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_4725.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_4725.=" AND cod_eapb='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_4725.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_4725, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 4725.";
	}
	
	if($norma=="ro_norma_2463")
	{
		$query_descartar_archivo_2463="";
		$query_descartar_archivo_2463.=" UPDATE gioss_2463_esta_consolidando_ro_actualmente ";
		$query_descartar_archivo_2463.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_2463.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_2463.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_2463.=" AND fecha_corte_periodo_consolidar='$fecha_remision' ";
		$query_descartar_archivo_2463.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_2463.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_2463.=" AND cod_eapb='$codigo_entidad_reportadora' ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_2463, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 2463.";
	}
	
	if($norma=="ro_norma_1393")
	{
		$query_descartar_archivo_1393="";
		$query_descartar_archivo_1393.=" UPDATE gioss_1393_esta_consolidando_ro_actualmente ";
		$query_descartar_archivo_1393.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_1393.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_1393.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_1393.=" AND fecha_corte_periodo_consolidar='$fecha_remision' ";
		$query_descartar_archivo_1393.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_1393.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_1393.=" AND cod_eapb='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_1393.=" ; ";		
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_1393, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 1393.";
	}
	
	
	//REPARACION
	
	if($norma=="ar_norma_4505")
	{
		$query_descartar_archivo_4505="";
		$query_descartar_archivo_4505.=" UPDATE gioss_4505_esta_reparando_ar_actualmente ";
		$query_descartar_archivo_4505.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_4505.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_4505.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_4505.=" AND fecha_corte_archivo_en_reparacion='$fecha_remision' ";
		$query_descartar_archivo_4505.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_4505.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_4505.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_4505.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_4505, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 4505.";
	}
	
	if($norma=="ar_norma_0123")
	{
		$query_descartar_archivo_0123="";
		$query_descartar_archivo_0123.=" UPDATE gioss_0123_esta_reparando_ar_actualmente ";
		$query_descartar_archivo_0123.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_0123.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_0123.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_0123.=" AND fecha_corte_archivo_en_reparacion='$fecha_remision' ";
		$query_descartar_archivo_0123.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_0123.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_0123.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_0123.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_0123, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 0123.";
	}
	
	if($norma=="ar_norma_0247")
	{
		$query_descartar_archivo_0247="";
		$query_descartar_archivo_0247.=" UPDATE gioss_0247_esta_reparando_ar_actualmente ";
		$query_descartar_archivo_0247.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_0247.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_0247.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_0247.=" AND fecha_corte_archivo_en_reparacion='$fecha_remision' ";
		$query_descartar_archivo_0247.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_0247.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_0247.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_0247.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_0247, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 0247.";
	}
	
	if($norma=="ar_norma_4725")
	{
		$query_descartar_archivo_4725="";
		$query_descartar_archivo_4725.=" UPDATE gioss_4725_esta_reparando_ar_actualmente ";
		$query_descartar_archivo_4725.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_4725.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_4725.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_4725.=" AND fecha_corte_archivo_en_reparacion='$fecha_remision' ";
		$query_descartar_archivo_4725.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_4725.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_4725.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_4725.=" ; ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_4725, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 4725.";
	}
	
	if($norma=="ar_norma_2463")
	{
		$query_descartar_archivo_2463="";
		$query_descartar_archivo_2463.=" UPDATE gioss_2463_esta_reparando_ar_actualmente ";
		$query_descartar_archivo_2463.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_2463.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_2463.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_2463.=" AND fecha_corte_archivo_en_reparacion='$fecha_remision' ";
		$query_descartar_archivo_2463.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_2463.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_2463.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_2463, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 2463.";
	}
	
	if($norma=="ar_norma_1393")
	{
		$query_descartar_archivo_1393="";
		$query_descartar_archivo_1393.=" UPDATE gioss_1393_esta_reparando_ar_actualmente ";
		$query_descartar_archivo_1393.=" SET esta_ejecutando='NO', se_pudo_descargar='SI' ";
		$query_descartar_archivo_1393.=" WHERE nick_usuario='$nick_usuario' ";
		$query_descartar_archivo_1393.=" AND nombre_archivo='$nombre_archivo' ";
		$query_descartar_archivo_1393.=" AND fecha_corte_archivo_en_reparacion='$fecha_remision' ";
		$query_descartar_archivo_1393.=" AND fecha_validacion='$fecha_validacion' ";
		$query_descartar_archivo_1393.=" AND hora_validacion='$hora_validacion' ";
		$query_descartar_archivo_1393.=" AND codigo_entidad_reportadora='$codigo_entidad_reportadora' ";
		$query_descartar_archivo_1393.=" ; ";		
		$error_bd="";
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_descartar_archivo_1393, $error_bd);
		
		echo "Se ha descartado las inconsistencias del archivo 1393.";
	}
	
}
else
{
	
}
?>