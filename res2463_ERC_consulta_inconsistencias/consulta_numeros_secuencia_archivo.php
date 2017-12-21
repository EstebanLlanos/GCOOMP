<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();

if(isset($_REQUEST["prestador"]) && isset($_REQUEST["nombre_archivo"]) &&  $_REQUEST["nombre_archivo"]!="" && $_REQUEST["prestador"]!="none" )
{
	$prestador=$_REQUEST["prestador"];
	$nombre_archivo=$_REQUEST["nombre_archivo"];
	$array_nombre_archivo=explode(".",$nombre_archivo);
	if(count($array_nombre_archivo)<2)
	{
		$nombre_archivo=$nombre_archivo.".txt";
	}
		
	
	//parte base de datos
	$query_consulta="";
	$query_consulta.="SELECT numero_secuencia FROM gioss_numero_de_secuencia_r4725_sida WHERE ";
	$query_consulta.=" nombre_archivo='$nombre_archivo' ";
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
		$selector_numeros_remision.="<option value='none' selected>Seleccione un numero de secuencia de los asociados al archivo SIDA</option>";
		foreach($resultado_query as $fila_resultado)
		{
			$selector_numeros_remision.="<option value='".$fila_resultado["numero_secuencia"]."'>Numero Secuencia: ".$fila_resultado["numero_secuencia"]."</option>";
		}
		$selector_numeros_remision.="</select>";
	}
	else
	{
		$selector_numeros_remision.="No se encontraron numeros de secuencias para el nombre digitado.";
	}
	
	echo $selector_numeros_remision;
	//FIN PARTE SELECTOR
}
else
{
	echo "";
}

if($_REQUEST["nombre_archivo"]=="")
{
	echo "";
}
?>