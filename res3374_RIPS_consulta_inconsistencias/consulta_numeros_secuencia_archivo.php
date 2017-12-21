<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();

if(isset($_REQUEST["prestador"]) && isset($_REQUEST["numero_remision"]) &&  $_REQUEST["numero_remision"]!="" && $_REQUEST["prestador"]!="none" )
{
	$prestador=$_REQUEST["prestador"];
	$numero_remision=$_REQUEST["numero_remision"];
			
	
	//parte base de datos
	$query_consulta="";
	$query_consulta.="SELECT numero_secuencia FROM gioss_numero_secuencia_archivos_rips WHERE ";
	$query_consulta.=" numero_remision='$numero_remision' ";
	$query_consulta.=" AND ";
	$query_consulta.=" codigo_prestador_servicios_salud='$prestador'; ";
	$resultado_query=$coneccionBD->consultar2($query_consulta);
	//fin parte base de datos
	
	//PARTE SELECTOR
	$selector_numeros_remision="";
	if(count($resultado_query)>0)
	{		
		$size_selector=count($resultado_query)+1;
		
		$selector_numeros_remision.="<select name='numeros_secuencias_varios' id='numeros_secuencias_varios' class='campo_azul' size='$size_selector' style='width:600px;' >";
		$selector_numeros_remision.="<option value='none' selected>Seleccione un numero de secuencia para el numero de remision</option>";
		foreach($resultado_query as $fila_resultado)
		{
			$selector_numeros_remision.="<option value='".$fila_resultado["numero_secuencia"]."'>Numero Secuencia: ".$fila_resultado["numero_secuencia"]."</option>";
		}
		$selector_numeros_remision.="</select>";
	}
	else
	{
		$selector_numeros_remision.="<b>No se encontro numero de secuencia asociado al numero de remision digitado.</b>";
	}
	
	echo $selector_numeros_remision;
	//FIN PARTE SELECTOR
}
else
{
	echo "";
}

if($_REQUEST["numero_remision"]=="")
{
	echo "";
}
?>