<?php
include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST['identificador_archivo']) 
	&& trim($_REQUEST['identificador_archivo'])!=""
	)
{
	$array_identificador=explode("_", trim($_REQUEST['identificador_archivo']) );

	if(count($array_identificador)>=3 )
	{
		$nombre_archivo=$array_identificador[0];
		$fecha_y_hora_validacion=$array_identificador[1];
		$fecha_de_corte=$array_identificador[2];

		$query_verificar_ips_del_archivo="";
		$query_verificar_ips_del_archivo.="SELECT distinct(gaa.campo2) , gent.nombre_de_la_entidad
		 from gioss_archivo_para_analisis_0123 gaa LEFT JOIN gioss_entidades_sector_salud gent on (gent.codigo_entidad=gaa.campo2)
		WHERE 
		gaa.nombre_archivo='$nombre_archivo' 
		AND gaa.fecha_y_hora_validacion='$fecha_y_hora_validacion'
		AND gaa.fecha_de_corte='$fecha_de_corte' order by  gent.nombre_de_la_entidad ";

		$html_selector="";
		$html_selector.="<select id='prestador' name='prestador' class='campo_azul'>";
		$resultado_query_ips_por_archivo=$coneccionBD->consultar2_no_crea_cierra($query_verificar_ips_del_archivo);
		if(is_array($resultado_query_ips_por_archivo) && count($resultado_query_ips_por_archivo)>0 )
		{

			$html_selector.="<option value='none'>Seleccione Un Prestador</option>";
			foreach ($resultado_query_ips_por_archivo as $key => $registro_actual) 
			{			
				$ips_actual=$registro_actual['campo2'];
				$nombre_ips=$registro_actual['nombre_de_la_entidad'];
				$html_selector.="<option value='$ips_actual'>$ips_actual $nombre_ips</option>";
			}
		}//fin if encontro resultados	
		else
		{

			$html_selector.="<option value='none'>...</option>";
		}	
		$html_selector.="</select>";
		echo $html_selector;

	}//fin if el array posee la cantidad de datos necesitados
	else
	{
		$html_selector="";
		$html_selector.="<select id='prestador' name='prestador' class='campo_azul'>";
		$html_selector.="<option value='none'>...</option>";
		$html_selector.="</select>";
		echo $html_selector;

	}//fin else identificador esta en blanco o no existe

}//fin if
else
{
	$html_selector="";
	$html_selector.="<select id='prestador' name='prestador' class='campo_azul'>";
	$html_selector.="<option value='none'>...</option>";
	$html_selector.="</select>";
	echo $html_selector;

}//fin else identificador esta en blanco o no existe


$coneccionBD->cerrar_conexion();
?>