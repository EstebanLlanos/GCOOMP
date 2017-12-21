<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();
if(isset($_REQUEST["cambio_tipo_tiempo_periodo"]))
{
	$cambio_tipo_tiempo_periodo=$_REQUEST["cambio_tipo_tiempo_periodo"];
	
	$selector_periodo="";
	
	if($cambio_tipo_tiempo_periodo=="trimestral")
	{
		$query_periodos_rips="SELECT * FROM gioss_periodo_informacion ORDER BY cod_periodo_informacion;";
		$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);
		
		
		$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
		$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
		foreach($resultado_query_periodos as $key=>$periodo_bd)
		{
			if(intval($periodo_bd["cod_periodo_informacion"])>0 && intval($periodo_bd["cod_periodo_informacion"])<5)
			{
				$cod_periodo=$periodo_bd["cod_periodo_informacion"];
				$nombre_periodo=$periodo_bd["nom_periodo_informacion"];
				$fecha_permitida=str_replace("2013","AAAA",$periodo_bd["fec_final_periodo"]);
				$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo $fecha_permitida)</option>";
			}
		}
		$selector_periodo.="</select>";
	}//fin if
	else if($cambio_tipo_tiempo_periodo=="mensual")
	{
		$query_periodos_rips="SELECT * FROM gioss_periodo_informacion_4505_mensual ORDER BY  cod_periodo_informacion::numeric;";
		$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);
		
		$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
		$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
		foreach($resultado_query_periodos as $key=>$periodo_bd)
		{
			if(intval($periodo_bd["cod_periodo_informacion"])>0 )
			{
				$cod_periodo=$periodo_bd["cod_periodo_informacion"];
				$nombre_periodo=$periodo_bd["nom_periodo_informacion"];
				$fecha_permitida=str_replace("2013","AAAA",$periodo_bd["fec_final_periodo"]);
				$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo $fecha_permitida)</option>";
			}
		}
		$selector_periodo.="</select>";
	}
	
	echo $selector_periodo;
}
else
{
	$query_periodos_rips="SELECT * FROM gioss_periodo_informacion ORDER BY cod_periodo_informacion;";
	$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);
	
	
	$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
	$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
	foreach($resultado_query_periodos as $key=>$periodo_bd)
	{
		if(intval($periodo_bd["cod_periodo_informacion"])>0 && intval($periodo_bd["cod_periodo_informacion"])<5)
		{
			$cod_periodo=$periodo_bd["cod_periodo_informacion"];
			$nombre_periodo=$periodo_bd["nom_periodo_informacion"];
			$fecha_permitida=str_replace("2013","AAAA",$periodo_bd["fec_final_periodo"]);
			$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo $fecha_permitida)</option>";
		}
	}
	$selector_periodo.="</select>";

	echo $selector_periodo;
}//fin else

$coneccionBD->cerrar_conexion();
?>