<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

if(isset($_REQUEST["cod_dpto"]))
{
	$cod_dpto=$_REQUEST["cod_dpto"];
	
	//SELECTOR selector_municipio-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
	$selector_municipio="";
	$selector_municipio.="<div id='mpio_div'>";
	$selector_municipio.="<select id='mpio' name='mpio' class='campo_azul'>";
	$selector_municipio.="<option value='none'>Seleccione un municipio</option>";

	$coneccionBD = new conexion();
	$sql_consulta_municipio="SELECT * FROM ";
	$sql_consulta_municipio.=" gios_mpio ";
	$sql_consulta_municipio.=" WHERE cod_departamento='".$cod_dpto."' ORDER BY nom_municipio  ; ";
	$resultado_query_municipio=$coneccionBD->consultar2($sql_consulta_municipio);

	if(count($resultado_query_municipio)>0)
	{
		foreach($resultado_query_municipio as $municipio)
		{
			$selector_municipio.="<option value='".$municipio['cod_municipio']."'>".$municipio['nom_municipio']."</option>";
		}
	}

	$selector_municipio.="</select>";
	$selector_municipio.="</div>";
	//FIN

	echo $selector_municipio;
}
else
{
	//SELECTOR selector_municipio-ASOCIADA_PRESTADOR_ASOCIADO_USUARIO
	$selector_municipio="";
	$selector_municipio.="<div id='mpio_div'>";
	$selector_municipio.="<select id='mpio' name='mpio' class='campo_azul'>";
	$selector_municipio.="<option value='none'>Seleccione un municipio</option>";
	$selector_municipio.="</select>";
	$selector_municipio.="</div>";
	//FIN

	echo $selector_municipio;
}
?>