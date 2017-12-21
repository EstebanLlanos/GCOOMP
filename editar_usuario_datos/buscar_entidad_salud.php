<?php

include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();
$html_busqueda="";

if(isset($_REQUEST['rango_resultados']))
{
$inicio=$_REQUEST['inicio'];
$fin=$_REQUEST['fin'];

$campo_div=$_REQUEST["campo_div"];
$filtro_nombre=$_REQUEST["filtro_nombre"];

$query_entidad="";
$query_entidad.=" SELECT *  FROM gioss_entidades_sector_salud ";
if($filtro_nombre!="")
{
$query_entidad.=" WHERE nombre_de_la_entidad ~* '.*".$filtro_nombre.".*' ";
}

$query_entidad.=" ORDER BY codigo_entidad LIMIT $fin OFFSET $inicio ; ";
$resultado_consulta_entidad=$coneccionBD->consultar2($query_entidad);

$html_busqueda.="<span style='color:white;'>".$query_entidad."</span>";

$html_busqueda.="<span >Resultados desde ".$inicio." hasta ".($inicio+$fin)."</span>";

if(count($resultado_consulta_entidad)>0)
{
	//BOTONES NAVEGACION
	
	$html_busqueda.="<p align='left' width='100%'>";
	$html_busqueda.="<table>";
	$html_busqueda.="<tr>";
	$html_busqueda.="<td style='text-align:left;'>";
	$html_busqueda.="<input type='button' class='btn btn-success color_boton' value='<-Atras' onclick='consultar_entidades_atras_ajax(\"$campo_div\");' /> ";
	$html_busqueda.="<input type='button' class='btn btn-success color_boton' value='Siguiente->' onclick='consultar_entidades_adelante_ajax(\"$campo_div\");' />";
	$html_busqueda.="</td>";
	$html_busqueda.="</tr>";
	$html_busqueda.="</table>";
	$html_busqueda.="</p>";
	
	$html_busqueda.="<br></br>";
	//FIN BOTONES NAVEGACION
	
	$html_busqueda.="<div id='resultados' style='overflow: scroll;width:450px;height:580px;border-style:solid;border-width:medium;'>";
	$html_busqueda.="<table>";
	$html_busqueda.="<tr >";
	$html_busqueda.="<td style='border-style:solid;border-width:medium;background-color:#006CBA;color:white;'>Seleccionar</td>";
	//$html_busqueda.="<td style='border-style:solid;border-width:medium;background-color:#006CBA;color:white;'>nombre_tipo_entidad</td>";
	//$html_busqueda.="<td style='border-style:solid;border-width:medium;background-color:#006CBA;color:white;'>cod_tipo_entidad</td>";
	$html_busqueda.="<td style='border-style:solid;border-width:medium;background-color:#006CBA;color:white;'>codigo_entidad</td>";
	$html_busqueda.="<td style='border-style:solid;border-width:medium;background-color:#006CBA;color:white;'>nombre_de_la_entidad</td>";
	$html_busqueda.="</tr>";
	
	
	
	foreach($resultado_consulta_entidad as $entidad)
	{
		$codigo_entidad=$entidad["codigo_entidad"];
		
		
		$html_busqueda.="<tr>";
		//$html_busqueda.="<script>var entidad_$codigo_entidad='$codigo_entidad'; var campo_div_insertar_$campo_div='$campo_div';</script>";
		$html_busqueda.="<td style='border-style:solid;border-width:1px;' > ";
		$html_busqueda.="<input type='button' class='btn btn-success color_boton' style='width:90%;' value='Seleccionar Entidad' onclick='seleccionar(\"$codigo_entidad\",\"$campo_div\");' /> ";
		$html_busqueda.="</td> ";
		
		//$html_busqueda.="<td style='border-style:solid;border-width:1px;'>".$entidad["nombre_tipo_entidad"]."</td>";
		//$html_busqueda.="<td style='border-style:solid;border-width:1px;'>".$entidad["cod_tipo_entidad"]."</td>";
		$html_busqueda.="<td style='border-style:solid;border-width:1px;'>".$entidad["codigo_entidad"]."</td>";
		$html_busqueda.="<td style='border-style:solid;border-width:1px;'>".$entidad["nombre_de_la_entidad"]."</td>";
		
					
		$html_busqueda.="</tr>";
	}//fin foreach
	
	
	$html_busqueda.="</table>";
	$html_busqueda.="</div>";
	
	
	
}//fin if hay resultados
else
{
	$html_busqueda.="<br></br><h5>NO SE ENCONTRARON COINCIDENCIAS.</h5>";
}
echo $html_busqueda;

}//fin if se enviaron los valores por get con el ajax


?>