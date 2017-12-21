<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

if(isset($_REQUEST["periodo"]) && isset($_REQUEST["year"]) && isset($_REQUEST["eapb"]) && isset($_REQUEST["estado_info"]))
{
	$fecha_de_corte_periodo="";
	
	$year=$_REQUEST["year"];
	$periodo=$_REQUEST["periodo"];	
	$estado_info_text=$_REQUEST["estado_info"];	
	$eapb=$_REQUEST["eapb"];
	
	$estado_info="2";
	
	if($estado_info_text=="validada")
	{
		$estado_info="1";
	}
	else
	{
		$estado_info="2";
	}
	
	//PERIODOS CANCER
	if(intval($periodo)==1)
	{
	   $fecha_de_corte_periodo=$year."-01-31";
	}
	if(intval($periodo)==2)
	{
	   $fecha_de_corte_periodo=$year."-02-28";
	}
	if(intval($periodo)==3)
	{
	   $fecha_de_corte_periodo=$year."-03-31";
	}
	if(intval($periodo)==4)
	{
	   $fecha_de_corte_periodo=$year."-04-30";
	}
	if(intval($periodo)==5)
	{
	   $fecha_de_corte_periodo=$year."-05-31";
	}
	if(intval($periodo)==6)
	{
	   $fecha_de_corte_periodo=$year."-06-30";
	}
	if(intval($periodo)==7)
	{
	   $fecha_de_corte_periodo=$year."-07-31";
	}
	if(intval($periodo)==8)
	{
	   $fecha_de_corte_periodo=$year."-08-31";
	}
	if(intval($periodo)==9)
	{
	   $fecha_de_corte_periodo=$year."-09-30";
	}
	if(intval($periodo)==10)
	{
	   $fecha_de_corte_periodo=$year."-10-31";
	}
	if(intval($periodo)==11)
	{
	   $fecha_de_corte_periodo=$year."-11-30";
	}
	if(intval($periodo)==12)
	{
	   $fecha_de_corte_periodo=$year."-12-31";
	}	
	//FIN PERIODOS CANCER
	
	
	
	//SELECTOR SELECTOR_ARCHIVO_POR_FECHA_GENERACION_USUARIO
	$selector_archivo_por_fecha_generacion_usuario="";
	$selector_archivo_por_fecha_generacion_usuario.="<select id='selector_archivo_por_fecha_generacion_usuario' name='selector_archivo_por_fecha_generacion_usuario' class='campo_azul' size='2' style='width: 600px;'>";
	$selector_archivo_por_fecha_generacion_usuario.="<option value='none'>Seleccione el archivo por fecha generacion y/o usuario</option>";

	$coneccionBD = new conexion();
	
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario="SELECT * FROM ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" gioss_archivos_obligatorios_reportados_cancer  ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" WHERE ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" fecha_corte_reporte='".$fecha_de_corte_periodo."' ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" AND ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" codigo_entidad_eapb_generadora='".$eapb."' ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" AND ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" estado_informacion='".$estado_info."' ";
	$sql_consulta_selector_archivo_por_fecha_generacion_usuario.=" ; ";
	$resultado_query_fecha_gen_usuario=$coneccionBD->consultar2($sql_consulta_selector_archivo_por_fecha_generacion_usuario);

	$cantidad_resultados=count($resultado_query_fecha_gen_usuario)+1;	
	
	//SELECTOR SELECTOR_ARCHIVO_POR_FECHA_GENERACION_USUARIO
	$selector_archivo_por_fecha_generacion_usuario="";
	$selector_archivo_por_fecha_generacion_usuario.="<select id='selector_archivo_por_fecha_generacion_usuario' name='selector_archivo_por_fecha_generacion_usuario' class='campo_azul' size='$cantidad_resultados' style='width: 600px;'>";
	$selector_archivo_por_fecha_generacion_usuario.="<option value='none'>Seleccione el archivo por fecha generacion y/o usuario</option>";

	if(count($resultado_query_fecha_gen_usuario)>0)
	{
		foreach($resultado_query_fecha_gen_usuario as $opcion_fecha_gen_usuario)
		{
			$selector_archivo_por_fecha_generacion_usuario.="<option value='".$opcion_fecha_gen_usuario['usuario_que_genero']."_s3p_".$opcion_fecha_gen_usuario['fecha_de_generacion']."_s3p_".$opcion_fecha_gen_usuario['hora_generacion']."'>Usuario: ".$opcion_fecha_gen_usuario['usuario_que_genero'].", Fecha generaci&oacuten: ".$opcion_fecha_gen_usuario['fecha_de_generacion'].", hora generaci&oacuten: ".$opcion_fecha_gen_usuario['hora_generacion']."</option>";
		}
	}

	$selector_archivo_por_fecha_generacion_usuario.="</select>";
	//FIN

	echo $selector_archivo_por_fecha_generacion_usuario;
}
else
{
	//SELECTOR SELECTOR_ARCHIVO_POR_FECHA_GENERACION_USUARIO
	$selector_archivo_por_fecha_generacion_usuario="";
	$selector_archivo_por_fecha_generacion_usuario.="<select id='selector_archivo_por_fecha_generacion_usuario' name='selector_archivo_por_fecha_generacion_usuario' class='campo_azul'>";
	$selector_archivo_por_fecha_generacion_usuario.="<option value='none'>No se hayar&oacuten coincidencias</option>";
	$selector_archivo_por_fecha_generacion_usuario.="<option value='none'>Seleccione el archivo por fecha generacion y/o usuario</option>";
	$selector_archivo_por_fecha_generacion_usuario.="</select>";
	//FIN

	echo $selector_archivo_por_fecha_generacion_usuario;
}
?>