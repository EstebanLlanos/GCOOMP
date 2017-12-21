<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();

if(isset($_REQUEST["prestador"]) && isset($_REQUEST["periodo"]) && isset($_REQUEST["year"]) && $_REQUEST["year"]!="" && strlen($_REQUEST["year"])==4 && $_REQUEST["prestador"]!="none" )
{
	$prestador=$_REQUEST["prestador"];
	$year=$_REQUEST["year"];
	$periodo=$_REQUEST["periodo"];
		
	
	$query_consulta="";
	$query_consulta.="SELECT numero_remision,numero_secuencia FROM gioss_numero_secuencia_archivos_rips WHERE ";
	if($periodo!="none")
	{
		$mes_dia_ini=explode("/",$periodo)[1]."-01";
		$mes_dia_fin=explode("/",$periodo)[1]."-".explode("/",$periodo)[0];
		$fecha_para_bd_ini=$year."-".$mes_dia_ini;
		$fecha_para_bd_fin=$year."-".$mes_dia_fin;
		$query_consulta.=" (fecha_de_corte BETWEEN '$fecha_para_bd_ini' AND '$fecha_para_bd_fin' )";
	}
	else
	{
		$inicio_year=$year."-01-1";
		$fin_year=$year."-12-31";
		$query_consulta.=" (fecha_de_corte BETWEEN  '$inicio_year' AND '$fin_year') ";
	}
	$query_consulta.=" AND ";
	$query_consulta.=" codigo_prestador_servicios_salud='$prestador'; ";
	$resultado_query=$coneccionBD->consultar2($query_consulta);
	
	$selector_numeros_remision="";
	if(count($resultado_query)>0)
	{		
		$size_selector=count($resultado_query)+1;
		
		$selector_numeros_remision.="<select name='numeros_remision_varios' id='numeros_remision_varios' class='campo_azul' size='$size_selector' style='width:600px;' >";
		$selector_numeros_remision.="<option value='none' selected>Seleccione un numero de remision</option>";
		foreach($resultado_query as $fila_resultado)
		{
			$selector_numeros_remision.="<option value='".$fila_resultado["numero_secuencia"]."'>Numero Secuencia: ".$fila_resultado["numero_secuencia"].", Numero Remision CT: ".$fila_resultado["numero_remision"]."</option>";
		}
		$selector_numeros_remision.="</select>";
	}
	else
	{
		$selector_numeros_remision.="No se encontraron numeros de remisi&oacuten para el periodo y/o a&ntildeo establecidos.";
	}
	echo $selector_numeros_remision;
}
else
{
	echo "";
}

if($_REQUEST["year"]=="")
{
	echo "";
}
?>