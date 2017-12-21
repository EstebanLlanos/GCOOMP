<?php
include_once ('../utiles/clase_coneccion_bd.php');

$coneccionBD = new conexion();
$coneccionBD->crearConexion();

if(isset($_REQUEST['periodo']) 
	&& trim($_REQUEST['periodo'])!=""
	&& isset($_REQUEST['year_corte']) 
	&& trim($_REQUEST['year_corte'])!=""
	&& isset($_REQUEST['tipo_periodo_tiempo']) 
	&& trim($_REQUEST['tipo_periodo_tiempo'])!=""
	)
{
	$codigo_periodo=trim($_REQUEST['periodo']);
	$year_de_corte=trim($_REQUEST['year_corte']);
	$tipo_periodo_tiempo=trim($_REQUEST['tipo_periodo_tiempo']);

	$fecha_inicio_bd="";
	$fecha_corte_bd="";

	$resultados_consulta_periodo_informacion_4505=array();
	if($tipo_periodo_tiempo=="trimestral")
	{
	    $consultar_periodo_informacion_4505="";
	    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion WHERE cod_periodo_informacion='".$codigo_periodo."'; ";
	    $resultados_consulta_periodo_informacion_4505=$coneccionBD->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
	}//fin if
	else if($tipo_periodo_tiempo=="mensual")
	{
	    $consultar_periodo_informacion_4505="";
	    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion_4505_mensual WHERE cod_periodo_informacion='".$codigo_periodo."'; ";
	    $resultados_consulta_periodo_informacion_4505=$coneccionBD->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
	}//fin if

	if(count($resultados_consulta_periodo_informacion_4505)>0
	   && is_array($resultados_consulta_periodo_informacion_4505)
	   )
	{				    
	    $fecha_inicio_periodo_bd=$resultados_consulta_periodo_informacion_4505[0]["fec_inicio_periodo"];
	    $array_fecha_inicio_periodo_bd=explode("-",$fecha_inicio_periodo_bd);
	    $fecha_inicio_bd=$year_de_corte."-".$array_fecha_inicio_periodo_bd[1]."-".$array_fecha_inicio_periodo_bd[2];

	    $fecha_final_periodo_bd=$resultados_consulta_periodo_informacion_4505[0]["fec_final_periodo"];
	    $array_fecha_final_periodo_bd=explode("-",$fecha_final_periodo_bd);
	    $fecha_corte_bd=$year_de_corte."-".$array_fecha_final_periodo_bd[1]."-".$array_fecha_final_periodo_bd[2];

	}//fin if verificacion fecha inicial con fecha inicial del periodo

	$selector_archivos_subidos="";

	$selector_archivos_subidos.="<select id='selector_archivos_subidos' name='selector_archivos_subidos' class='campo_azul' onchange='consultar_ips_archivo(this.value)'>";
	

	$query_archivos_subidos_para_analisis="";
	$query_archivos_subidos_para_analisis.=" SELECT aa.* , eva.nick_usuario FROM gioss_indice_archivo_para_analisis_4505 aa LEFT JOIN gioss_4505_esta_validando_actualmente eva ON (aa.fecha_y_hora_validacion::text = concat_ws(' ',eva.fecha_validacion::text,eva.hora_validacion::text) AND aa.nombre_archivo=aa.nombre_archivo)
	where fecha_inicio_periodo='$fecha_inicio_bd' AND fecha_de_corte='$fecha_corte_bd'
	ORDER BY fecha_y_hora_validacion asc ; ";
	$resultado_query_archivos_subidos_para_analisis=$coneccionBD->consultar2_no_crea_cierra($query_archivos_subidos_para_analisis);
	if(is_array($resultado_query_archivos_subidos_para_analisis) && count($resultado_query_archivos_subidos_para_analisis)>0 )
	{
		$selector_archivos_subidos.="<option value='none'>Seleccione el Archivo de 4505 que desea analizar</option>";
		foreach ($resultado_query_archivos_subidos_para_analisis as $key => $archivo_subido_actual) 
		{		
			$identificador_archivo=$archivo_subido_actual['nombre_archivo']."_".$archivo_subido_actual['fecha_y_hora_validacion']."_".$archivo_subido_actual['fecha_de_corte'];

			$numero_secuencia_si_lo_hay="";
			if(trim($archivo_subido_actual['fecha_de_corte'])!="")
			{
				$numero_secuencia_si_lo_hay=", y con numero de secuencia:".$archivo_subido_actual['numero_de_secuencia'];
			}//fin if

			$usuario_que_valido="";
			if(isset($archivo_subido_actual['nick_usuario'])==true
				&& trim($archivo_subido_actual['nick_usuario'])!=""
				)
			{
				$usuario_que_valido="Validado Por: ".trim($archivo_subido_actual['nick_usuario']).", ";

			}//fin if
			
			$descripcion_archivo=$usuario_que_valido.$archivo_subido_actual['nombre_archivo']." , validado en el: ".$archivo_subido_actual['fecha_y_hora_validacion'].", con la fecha de corte: ".$archivo_subido_actual['fecha_de_corte'].$numero_secuencia_si_lo_hay;
			$selector_archivos_subidos.="<option value='$identificador_archivo'>$descripcion_archivo</option>";
		}//fin foreach
	}//fin if
	else
	{
		$selector_archivos_subidos.="<option value='none'>No se encontraron resultados para fi: $fecha_inicio_bd fc: $fecha_corte_bd .</option>";
	}

	$selector_archivos_subidos.="</select>";

	echo $selector_archivos_subidos;
}//fin if


$coneccionBD->cerrar_conexion();
?>