<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once 'procesar_mensaje.php';

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mostrarResultado = "<div id='mostrar_resultado_div'></div>";
$mensaje="<div id='mensaje_div'></div>";

//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO POR LOGUEO

//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul' onchange='consultar_prestador();' >";
$eapb.="<option value='none'>Seleccione un EAPB</option>";

if(intval($perfil_usuario_actual)==5 && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_consulta_eapb_usuario_prestador="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_eapb_usuario_prestador.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad2 = ea.codigo_entidad) ";
	$sql_consulta_eapb_usuario_prestador.=" WHERE re.entidad1='".$entidad_salud_usuario_actual."' ";
	$sql_consulta_eapb_usuario_prestador.=";";

	$resultado_query_eapb_usuario=$coneccionBD->consultar2($sql_consulta_eapb_usuario_prestador);

	if(count($resultado_query_eapb_usuario)>0)
	{
		foreach($resultado_query_eapb_usuario as $eapb_prestador_usuario_res)
		{
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
		}
	}
}//fin if si el usuario es administrador y la entidad no es eapb, por lo tanto busca la eapb asociada a la entidad
else if((intval($perfil_usuario_actual)==3 || intval($perfil_usuario_actual)==4 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 y la entidad es de tipo eapb

$eapb.="</select>";
$eapb.="</div>";
//FIN


//SELECTOR PRESTADORES ASOCIADOS EAPB 
$prestador="";
$prestador.="<div id='div_prestador'>";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
$prestador.="</select>";
$prestador.="</div>";
//FIN



//SELECTOR FECHAS MESES PERIDOS PYP

$query_periodos_rips="SELECT * FROM gioss_periodo_informacion;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);

$selector_periodo="";
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
$selector_periodo.="</select>\n";
//FIN SELECTOR FECHAS MESES PERIDOS PYP

//SELECTOR DEPARTAMENTO
$selector_departamento="";
$selector_departamento.="<select id='dpto' name='dpto' class='campo_azul' onchange='consultar_mpio();'>";
$selector_departamento.="<option value='none'>Seleccione un departamento</option>";

$query_departamentos="select * from gios_dpto ORDER BY nom_departamento;";
$res_dptos_query=$coneccionBD->consultar2($query_departamentos);

if(count($res_dptos_query)>0)
{
	foreach($res_dptos_query as $departamento)
	{
		$selector_departamento.="<option value='".$departamento['cod_departamento']."'>".$departamento['nom_departamento']."</option>";
	}
}

$selector_departamento.="</select>";
//FIN SELECTOR DEPARTAMENTO

//SELECTOR MUNICIPIO
$selector_municipio="";
$selector_municipio.="<div id='mpio_div'>";
$selector_municipio.="<select id='mpio' name='mpio' class='campo_azul' >";
$selector_municipio.="<option value='none'>Seleccione un municipio</option>";
$selector_municipio.="</select>";
$selector_municipio.="</div>";


$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);

$smarty->assign("campo_dpto", $selector_departamento, true);
$smarty->assign("campo_mpio", $selector_municipio, true);
$smarty->assign("campo_fechas_corte", $selector_periodo, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('estruct_incons_prest_RIPS.html.tpl');

echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros_vista=0;
$contador_offset=0;
$hubo_resultados=false;

if(isset($_POST["year_de_validacion"]) 
   && isset($_POST["eapb"]) && $_POST["eapb"]!="none" && ctype_digit($_POST["year_de_validacion"]) )
{
	
	$cod_eapb=$_POST["eapb"];
	$year=trim($_POST["year_de_validacion"]);
	
	$fecha_inicio=$year."-01-01";
	$fecha_fin=$year."-12-31";
	$fecha_de_corte_periodo=$year."-12-31";
	
	
	$codigo_departamento="";
	if(isset($_POST["dpto"]) && $_POST["dpto"]!="none")
	{
		$codigo_departamento=$_POST["dpto"];
	}
	
	$codigo_municipio="";
	if(isset($_POST["mpio"]) && $_POST["mpio"]!="none")
	{
		$codigo_municipio=$_POST["mpio"];
	}
	
	$cod_prestador="";
	if(isset($_POST["prestador"]) && $_POST["prestador"]!="none")
	{
		$cod_prestador=$_POST["prestador"];
	}
	
	$nombre_vista_para_calidad_de_datos="vcdtpyp_".$nick_user."_".$tipo_id."_".$identificacion;
	
	$sql_vista_estruct_incons_prest="";
	$sql_vista_estruct_incons_prest.="CREATE OR REPLACE VIEW $nombre_vista_para_calidad_de_datos ";
	$sql_vista_estruct_incons_prest.=" AS SELECT DISTINCT tei.codigo_prestador_servicios ";
	
	$sql_vista_estruct_incons_prest.=" , ";
	$sql_vista_estruct_incons_prest.=" ri.cod_detalle_inconsistencia ";
	
	$sql_vista_estruct_incons_prest.=" FROM gioss_tabla_estado_informacion_rips tei ";
	$sql_vista_estruct_incons_prest.=" INNER JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
	$sql_vista_estruct_incons_prest.=" WHERE  ";
	$sql_vista_estruct_incons_prest.=" codigo_eapb='$cod_eapb' ";	
	if($year!="")
	{
		$sql_vista_estruct_incons_prest.=" AND ";
		$sql_vista_estruct_incons_prest.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
		//$sql_vista_estruct_incons_prest.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	}
	if($codigo_departamento!="")
	{
		$sql_vista_estruct_incons_prest.=" AND ";
		$sql_vista_estruct_incons_prest.=" codigo_departamento='$codigo_departamento' ";
	}
	if($codigo_departamento!="" && $codigo_municipio!="")
	{
		$sql_vista_estruct_incons_prest.=" AND ";
		$sql_vista_estruct_incons_prest.=" codigo_municipio='$codigo_municipio' ";
	}
	if($cod_prestador!="")
	{
		$sql_vista_estruct_incons_prest.=" AND ";
		$sql_vista_estruct_incons_prest.=" codigo_prestador_servicios='$cod_prestador' ";
	}		
	$sql_vista_estruct_incons_prest.=" ; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_estruct_incons_prest, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_estruct_incons_prest)."');</script>";
	}
	
	//echo "<script>alert(' ".procesar_mensaje($sql_vista_estruct_incons_prest)."');</script>";
	
	$numero_registros_vista=0;
	$sql_numero_registros_vista="";
	$sql_numero_registros_vista.="SELECT count(*) as contador FROM $nombre_vista_para_calidad_de_datos  ";	
	$sql_numero_registros_vista.=" ; ";
	$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros_vista);
	if(count($resultado_query_numero_registros) && is_array($resultado_query_numero_registros))
	{
		$numero_registros_vista=intval($resultado_query_numero_registros[0]["contador"]);
	}
	//echo "<script>alert('se genero vista $numero_registros_vista');</script>";
	
	$contador_offset=0;
	$hubo_resultados=false;
	$cont_linea=1;
	$bool_ultima_seccion_para_ventana=false;
	while($contador_offset<$numero_registros_vista)
	{
		$limite=2000;
		
		if( ($contador_offset+2000)>=$numero_registros_vista)
		{
			$limite=2000+($numero_registros_vista-$contador_offset);
			$bool_ultima_seccion_para_ventana=true;
		}
	
		//Ejemplo: SELECT *  FROM vista_inconsistencias_rips WHERE numero_orden='29'  order by numero_linea, numero_campo limit 5 offset 0; 
		$sql_query_busqueda="";
		$sql_query_busqueda.="SELECT * FROM $nombre_vista_para_calidad_de_datos LIMIT $limite OFFSET $contador_offset;  ";
		$resultado_para_calidad_de_datos_desde_estado_informacion=$coneccionBD->consultar2($sql_query_busqueda);
	
		if(count($resultado_para_calidad_de_datos_desde_estado_informacion)>0 && is_array($resultado_para_calidad_de_datos_desde_estado_informacion))
		{
			
			$nombre_archivo_calidad_de_datos=$cod_eapb."_estruct_incons_prestador_pyp.csv";
			$ruta_archivo_calidad_de_datos=$rutaTemporal.$nombre_archivo_calidad_de_datos;
			
			//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			if($hubo_resultados==false)
			{
				if(file_exists($ruta_archivo_calidad_de_datos))
				{
					unlink($ruta_archivo_calidad_de_datos);
				}
				$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "w") or die("fallo la creacion del archivo");
				fclose($file_calidad_de_datos);
			
				$html_abrir_ventana="";
				$html_abrir_ventana.="<script>";
				$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_estruct_incons_prestadores', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
				$html_abrir_ventana.="</script>";
				echo $html_abrir_ventana;
				
				$html_nueva_ventana="";
				$html_nueva_ventana.="<html>";
				
				$html_nueva_ventana.="<head>";
				$html_nueva_ventana.="<title>Reporte inconsistencias por prestador</title>";
				$html_nueva_ventana.="<style>";
				$html_nueva_ventana.="table";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    border-collapse: collapse;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="table,th,td";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    border: 1px solid black;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="td,th";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    padding: 15px;";
				$html_nueva_ventana.="    text-align: right;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="th";
				$html_nueva_ventana.="{";
				//$html_nueva_ventana.="   background-color: #0000FF;";
				$html_nueva_ventana.="   background: -webkit-linear-gradient(#0066FF, #001433); /* For Safari 5.1 to 6.0 */";
				$html_nueva_ventana.="   background: -o-linear-gradient(#0066FF, #001433); /* For Opera 11.1 to 12.0 */";
				$html_nueva_ventana.="   background: -moz-linear-gradient(#0066FF, #001433); /* For Firefox 3.6 to 15 */";
				$html_nueva_ventana.="   background: linear-gradient(#0066FF, #001433); /* Standard syntax */";
				$html_nueva_ventana.="   color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="td:hover";
				$html_nueva_ventana.="{";
				$html_nueva_ventana.="    background-color: #0066FF;";
				$html_nueva_ventana.="    color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.=".resultados";
				$html_nueva_ventana.="{";
				//$html_nueva_ventana.="    background-color: #FF0000;";
				$html_nueva_ventana.="   background: -webkit-linear-gradient(#FF0000, #990000); /* For Safari 5.1 to 6.0 */";
				$html_nueva_ventana.="   background: -o-linear-gradient(#FF0000, #990000); /* For Opera 11.1 to 12.0 */";
				$html_nueva_ventana.="   background: -moz-linear-gradient(#FF0000, #990000); /* For Firefox 3.6 to 15 */";
				$html_nueva_ventana.="   background: linear-gradient(#FF0000, #990000); /* Standard syntax */";
				$html_nueva_ventana.="    color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.=".resultados:hover";
				$html_nueva_ventana.="{";
				//$html_nueva_ventana.="    background-color: #990000;";
				$html_nueva_ventana.="   background: -webkit-linear-gradient(#990000, #FF0000); /* For Safari 5.1 to 6.0 */";
				$html_nueva_ventana.="   background: -o-linear-gradient(#990000, #FF0000); /* For Opera 11.1 to 12.0 */";
				$html_nueva_ventana.="   background: -moz-linear-gradient(#990000, #FF0000); /* For Firefox 3.6 to 15 */";
				$html_nueva_ventana.="   background: linear-gradient(#990000, #FF0000); /* Standard syntax */";
				$html_nueva_ventana.="    color: white;";
				$html_nueva_ventana.="}";
				$html_nueva_ventana.="</style>";
				$html_nueva_ventana.="</head>";
				$html_nueva_ventana.="<body>";
				
				
				
				
				$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "a") or die("fallo la creacion del archivo");
				
				$titulos="";
				$titulos.="Cod. Departamento,Departamento,";
				$titulos.="Cod. Municipio,";
				$titulos.="Municipio,";
				$titulos.="Cod. Prestador,";
				$titulos.="Nombre del Prestador de servicios,";
				$titulos.="Codigo Tipo Inconsistencia,";
				$titulos.="Descripcion Tipo,";
				$titulos.="Codigo Grupo Inconsistencia,";
				$titulos.="Descripcion Grupo,";
				$titulos.="Codigo Detalle Inconsistencia,";
				$titulos.="Descripcion Detalle,";
				$titulos.="Enero,";
				$titulos.="Febrero,";
				$titulos.="Marzo,";
				$titulos.="Abril,";
				$titulos.="Mayo,";
				$titulos.="Junio,";
				$titulos.="Julio,";
				$titulos.="Agosto,";
				$titulos.="Septiembre,";
				$titulos.="Octubre,";
				$titulos.="Noviembre,";
				$titulos.="Diciembre,";
				$titulos.="Total Acumulado";
				//$titulos.="Porcentaje de Participacion.";
				
				
				//encabezado
				$html_encabezado="";
				$html_encabezado.="<table id='tabla_encabezado_ventana' align='center'>";
				$html_encabezado.="<tr>";
				$html_encabezado.="<td>Departamento seleccionado:</td>";
				$depto_seleccionado="";
				if($codigo_departamento!="")
				{
					$query_departamentos_sel="select * from gios_dpto WHERE cod_departamento='$codigo_departamento' ORDER BY nom_departamento ;";
					$res_dptos_query_sel=$coneccionBD->consultar2($query_departamentos_sel);
					if(is_array($res_dptos_query_sel))
					{
						$depto_seleccionado=$res_dptos_query_sel[0]["nom_departamento"];
					}
					
					$html_encabezado.="<td>$depto_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$depto_seleccionado="Todos";
				}
				
				$html_encabezado.="<td>Municipio seleccionado:</td>";
				$mpio_seleccionado="";				
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$query_mpios_sel="select * from gios_mpio  WHERE cod_departamento='$codigo_departamento' AND  cod_municipio='$codigo_municipio' ORDER BY nom_municipio;";
					$res_mpios_query_sel=$coneccionBD->consultar2($query_mpios_sel);
					if(is_array($res_mpios_query_sel))
					{
						$mpio_seleccionado=$res_mpios_query_sel[0]["nom_municipio"];
					}
					$html_encabezado.="<td>$mpio_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$mpio_seleccionado="Todos";
				}
				
				$prestador_seleccionado="";
				$html_encabezado.="<td>Prestador seleccionado:</td>";
				if($cod_prestador!="")
				{
					$prestador_seleccionado=$cod_prestador;
					$html_encabezado.="<td>$prestador_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$prestador_seleccionado="Todos";
				}
				
				$html_encabezado.="<td>A&ntildeo seleccionado:</td>";
				$year_seleccionado="";
				if($year!="")
				{
					$year_seleccionado=$year;
					$html_encabezado.="<td>$year_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$year_seleccionado="Todos";
				}
				
				$html_encabezado.="</tr>";
				$html_encabezado.="</table>";
				$html_nueva_ventana.=$html_encabezado;
				
				$linea_encabezado="Departamento,$depto_seleccionado,Municipio,$mpio_seleccionado,Prestador,$prestador_seleccionado,Year,$year_seleccionado";
				
				fwrite($file_calidad_de_datos, $linea_encabezado."\n");
				//encabezado
				
				fwrite($file_calidad_de_datos, $titulos."\n");
				
				$html_nueva_ventana.="<table id='tabla_ventana_estado_info' >";
				$html_nueva_ventana.="<tr>";
				$array_titulos=explode(",",$titulos);
				$cont_titulos=0;
				foreach($array_titulos as $titulo_columna)
				{
					$html_nueva_ventana.="<th>$titulo_columna</th>";
					
					$cont_titulos++;
				}
				$html_nueva_ventana.="</tr>";
				
				fclose($file_calidad_de_datos);
			}//fin if inicio
			$hubo_resultados=true;
			//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			
			$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "a") or die("fallo la creacion del archivo");
			
				
			foreach($resultado_para_calidad_de_datos_desde_estado_informacion as $resultado)
			{
				//echo "<script>alert('".count($resultado_para_calidad_de_datos_desde_estado_informacion)."');</script>";
				$linea_estado_informacion="";
				
				//identificacion
				$query_info_identificacion="";
				$query_info_identificacion.=" SELECT * ";
				$query_info_identificacion.=" FROM gioss_tabla_estado_informacion_rips WHERE  ";
				$query_info_identificacion.=" numero_secuencia=(select max(numero_secuencia) from gioss_tabla_estado_informacion_rips where ";
				$query_info_identificacion.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."') ";				
				$query_info_identificacion.=" AND ";
				$query_info_identificacion.=" codigo_eapb='$cod_eapb' ";
				if($year!="")
				{
					$query_info_identificacion.=" AND ";
					$query_info_identificacion.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_estruct_incons_prest.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				$query_info_identificacion.=" AND ";
				$query_info_identificacion.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				$query_info_identificacion.=" order by fecha_validacion,numero_secuencia ; ";
				$error_bd_seq="";
				$resultados_consulta_identificacion=$coneccionBD->consultar_no_warning_get_error($query_info_identificacion, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de identificacion ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($query_info_identificacion)."');</script>";
				}
				if(is_array($resultados_consulta_identificacion))
				{
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_departamento"].",";//6
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_del_departamento"].",";//7
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_municipio"].",";//8
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_de_municipio"].",";//9
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_prestador_servicios"].",";//4
					$query_nombre_prestador="SELECT nom_entidad_prestadora FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$resultados_consulta_identificacion[0]["codigo_prestador_servicios"]."';";
					$error_bd_seq="";
					$resultados_nombre_prestador=$coneccionBD->consultar_no_warning_get_error($query_nombre_prestador, $error_bd_seq);
					if(is_array($resultados_nombre_prestador) && count($resultados_nombre_prestador)>0)
					{
						$linea_estado_informacion.=$resultados_nombre_prestador[0]["nom_entidad_prestadora"].",";//5
					}
					
				}
				//fin identificacion
				
				$query_consulta_inconsistencias="";
				$query_consulta_inconsistencias.="SELECT * FROM gioss_detalle_inconsistencias_rips ";
				$query_consulta_inconsistencias.=" WHERE cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."';";
				$error_bd_seq="";
				$resultados_consulta_detalles_inconsistencias=$coneccionBD->consultar_no_warning_get_error($query_consulta_inconsistencias, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos inconsistencias detalles ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($query_consulta_inconsistencias)."');</script>";
				}
				if(is_array($resultados_consulta_detalles_inconsistencias))
				{
					$linea_estado_informacion.=$resultados_consulta_detalles_inconsistencias[0]["cod_tipo_validacion"].",";
					$query_desc_tipo_incons="SELECT * FROM gioss_tipo_inconsistencias WHERE tipo_validacion='".$resultados_consulta_detalles_inconsistencias[0]["cod_tipo_validacion"]."';";
					$error_bd_seq="";
					$resultados_consulta_desc_tipo_incons=$coneccionBD->consultar_no_warning_get_error($query_desc_tipo_incons, $error_bd_seq);
					if($error_bd_seq!="")
					{
						echo "<script>alert('error al consultar datos inconsistencias detalles ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($query_desc_tipo_incons)."');</script>";
					}
					$linea_estado_informacion.=$resultados_consulta_desc_tipo_incons[0]["descripcion_tipo_validacion"].",";
					
					$linea_estado_informacion.=$resultados_consulta_detalles_inconsistencias[0]["codigo_grupo_inconsistencia"].",";
					$query_desc_grupo_incons="SELECT * FROM gioss_grupo_inconsistencias WHERE grupo_validacion='".$resultados_consulta_detalles_inconsistencias[0]["codigo_grupo_inconsistencia"]."';";
					$error_bd_seq="";
					$resultados_consulta_desc_grupo_incons=$coneccionBD->consultar_no_warning_get_error($query_desc_grupo_incons, $error_bd_seq);
					if($error_bd_seq!="")
					{
						echo "<script>alert('error al consultar datos inconsistencias detalles ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($query_desc_grupo_incons)."');</script>";
					}
					$linea_estado_informacion.=$resultados_consulta_desc_grupo_incons[0]["descripcion_grupo_validacion"].",";
					
					$linea_estado_informacion.=$resultados_consulta_detalles_inconsistencias[0]["cod_detalle_inconsistencia"].",";
					$linea_estado_informacion.=explode(";;",$resultados_consulta_detalles_inconsistencias[0]["descripcion_detalle_inconsistencia"])[1].",";
				}
				
				
				//periodo 1 enero
				$sql_consulta_cantidad_inconsistencias_periodo_1="";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" SELECT count(*) cantidad_inconsistencias_periodo_1 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
			
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_1.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_1.=" (fecha_validacion BETWEEN '$year-01-01' AND '$year-01-30' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_1.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_1.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_1.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_1.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_1.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_1.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_1=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_1, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos periodo1 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_1)."');</script>";
				}
				$numero_incons_periodo_1=0;
				if(is_array($resultados_consulta_cant_incons_periodo_1))
				{
					$numero_incons_periodo_1=intval($resultados_consulta_cant_incons_periodo_1[0]["cantidad_inconsistencias_periodo_1"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_1.",";
				
				//periodo 2 febrero
				$sql_consulta_cantidad_inconsistencias_periodo_2="";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" SELECT count(*) cantidad_inconsistencias_periodo_2 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_2.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_2.=" (fecha_validacion BETWEEN '$year-02-01' AND '$year-02-28' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_2.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_2.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_2.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_2.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_2.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_2.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_2=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_2, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos periodo2 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_2)."');</script>";
				}
				$numero_incons_periodo_2=0;
				if(is_array($resultados_consulta_cant_incons_periodo_2))
				{
					$numero_incons_periodo_2=intval($resultados_consulta_cant_incons_periodo_2[0]["cantidad_inconsistencias_periodo_2"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_2.",";
				
				//periodo 3
				$sql_consulta_cantidad_inconsistencias_periodo_3="";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" SELECT count(*) cantidad_inconsistencias_periodo_3 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_3.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_3.=" (fecha_validacion BETWEEN '$year-03-01' AND '$year-03-31' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_3.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_3.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_3.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_3.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_3.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_3.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_3=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_3, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos periodo3 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_3)."');</script>";
				}
				$numero_incons_periodo_3=0;
				if(is_array($resultados_consulta_cant_incons_periodo_3))
				{
					$numero_incons_periodo_3=intval($resultados_consulta_cant_incons_periodo_3[0]["cantidad_inconsistencias_periodo_3"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_3.",";
				
				//periodo 4
				$sql_consulta_cantidad_inconsistencias_periodo_4="";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" SELECT count(*) cantidad_inconsistencias_periodo_4 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_4.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_4.=" (fecha_validacion BETWEEN '$year-04-01' AND '$year-04-30' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_4.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_4.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_4.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_4.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_4.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_4.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_4=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_4, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_4)."');</script>";
				}
				$numero_incons_periodo_4=0;
				if(is_array($resultados_consulta_cant_incons_periodo_4))
				{
					$numero_incons_periodo_4=intval($resultados_consulta_cant_incons_periodo_4[0]["cantidad_inconsistencias_periodo_4"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_4.",";
				
				//periodo 5
				$sql_consulta_cantidad_inconsistencias_periodo_5="";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" SELECT count(*) cantidad_inconsistencias_periodo_5 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_5.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_5.=" (fecha_validacion BETWEEN '$year-05-01' AND '$year-05-31' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_5.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_5.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_5.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_5.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_5.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_5.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_5=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_5, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_5)."');</script>";
				}
				$numero_incons_periodo_5=0;
				if(is_array($resultados_consulta_cant_incons_periodo_5))
				{
					$numero_incons_periodo_5=intval($resultados_consulta_cant_incons_periodo_5[0]["cantidad_inconsistencias_periodo_5"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_5.",";
				
				//periodo 6
				$sql_consulta_cantidad_inconsistencias_periodo_6="";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" SELECT count(*) cantidad_inconsistencias_periodo_6 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_6.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_6.=" (fecha_validacion BETWEEN '$year-06-01' AND '$year-06-30' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_6.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_6.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_6.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_6.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_6.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_6.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_6=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_6, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_6)."');</script>";
				}
				$numero_incons_periodo_6=0;
				if(is_array($resultados_consulta_cant_incons_periodo_6))
				{
					$numero_incons_periodo_6=intval($resultados_consulta_cant_incons_periodo_6[0]["cantidad_inconsistencias_periodo_6"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_6.",";
				
				//periodo 7
				$sql_consulta_cantidad_inconsistencias_periodo_7="";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" SELECT count(*) cantidad_inconsistencias_periodo_7 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_7.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_7.=" (fecha_validacion BETWEEN '$year-07-01' AND '$year-07-31' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_7.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_7.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_7.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_7.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_7.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_7.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_7=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_7, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_7)."');</script>";
				}
				$numero_incons_periodo_7=0;
				if(is_array($resultados_consulta_cant_incons_periodo_7))
				{
					$numero_incons_periodo_7=intval($resultados_consulta_cant_incons_periodo_7[0]["cantidad_inconsistencias_periodo_7"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_7.",";
				
				//periodo 8
				$sql_consulta_cantidad_inconsistencias_periodo_8="";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" SELECT count(*) cantidad_inconsistencias_periodo_8 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_8.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_8.=" (fecha_validacion BETWEEN '$year-08-01' AND '$year-08-31' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_8.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_8.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_8.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_8.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_8.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_8.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_8=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_8, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_8)."');</script>";
				}
				$numero_incons_periodo_8=0;
				if(is_array($resultados_consulta_cant_incons_periodo_8))
				{
					$numero_incons_periodo_8=intval($resultados_consulta_cant_incons_periodo_8[0]["cantidad_inconsistencias_periodo_8"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_8.",";
				
				//periodo 9
				$sql_consulta_cantidad_inconsistencias_periodo_9="";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" SELECT count(*) cantidad_inconsistencias_periodo_9 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_9.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_9.=" (fecha_validacion BETWEEN '$year-09-01' AND '$year-09-30' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_9.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_9.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_9.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_9.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_9.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_9.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_9=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_9, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_9)."');</script>";
				}
				$numero_incons_periodo_9=0;
				if(is_array($resultados_consulta_cant_incons_periodo_9))
				{
					$numero_incons_periodo_9=intval($resultados_consulta_cant_incons_periodo_9[0]["cantidad_inconsistencias_periodo_9"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_9.",";
				
				//periodo 10
				$sql_consulta_cantidad_inconsistencias_periodo_10="";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" SELECT count(*) cantidad_inconsistencias_periodo_10 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_10.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_10.=" (fecha_validacion BETWEEN '$year-10-01' AND '$year-10-31' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_10.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_10.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_10.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_10.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_10.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_10.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_10=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_10, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_10)."');</script>";
				}
				$numero_incons_periodo_10=0;
				if(is_array($resultados_consulta_cant_incons_periodo_10))
				{
					$numero_incons_periodo_10=intval($resultados_consulta_cant_incons_periodo_10[0]["cantidad_inconsistencias_periodo_10"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_10.",";
				
				//periodo 11
				$sql_consulta_cantidad_inconsistencias_periodo_11="";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" SELECT count(*) cantidad_inconsistencias_periodo_11 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_11.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_11.=" (fecha_validacion BETWEEN '$year-11-01' AND '$year-11-30' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_11.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_11.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_11.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_11.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_11.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_11.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_11=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_11, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_11)."');</script>";
				}
				$numero_incons_periodo_11=0;
				if(is_array($resultados_consulta_cant_incons_periodo_11))
				{
					$numero_incons_periodo_11=intval($resultados_consulta_cant_incons_periodo_11[0]["cantidad_inconsistencias_periodo_11"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_11.",";
				
				//periodo 12
				$sql_consulta_cantidad_inconsistencias_periodo_12="";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" SELECT count(*) cantidad_inconsistencias_periodo_12 ";	
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" WHERE  ";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" AND ";
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" cod_detalle_inconsistencia='".$resultado["cod_detalle_inconsistencia"]."' ";
				
				if($year!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_12.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_12.=" (fecha_validacion BETWEEN '$year-12-01' AND '$year-12-31' ) ";
					//$sql_consulta_cantidad_inconsistencias_periodo_12.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_12.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_12.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_inconsistencias_periodo_12.=" AND ";
					$sql_consulta_cantidad_inconsistencias_periodo_12.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_inconsistencias_periodo_12.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_cant_incons_periodo_12=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_inconsistencias_periodo_12, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de periodo4 ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_inconsistencias_periodo_12)."');</script>";
				}
				$numero_incons_periodo_12=0;
				if(is_array($resultados_consulta_cant_incons_periodo_12))
				{
					$numero_incons_periodo_12=intval($resultados_consulta_cant_incons_periodo_12[0]["cantidad_inconsistencias_periodo_12"]);
				}
				$linea_estado_informacion.=$numero_incons_periodo_12.",";
				
				//total acumulado
				$total_acumulado=0;
				$total_acumulado=$numero_incons_periodo_1+$numero_incons_periodo_2+$numero_incons_periodo_3+$numero_incons_periodo_4;
				$total_acumulado=$total_acumulado+$numero_incons_periodo_5+$numero_incons_periodo_6+$numero_incons_periodo_7+$numero_incons_periodo_8;
				$total_acumulado=$total_acumulado+$numero_incons_periodo_9+$numero_incons_periodo_10+$numero_incons_periodo_11+$numero_incons_periodo_12;
				$linea_estado_informacion.=$total_acumulado;
				
				
				/*
				//porcentaje participacion
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores="";
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" SELECT count(*) cantidad_inconsistencias_total_prestadores ";	
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" FROM gioss_tabla_estado_informacion_rips tei ";
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" LEFT JOIN gioss_reporte_inconsistencia_archivos_rips ri on (tei.numero_secuencia=ri.numero_orden )";
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" WHERE  ";
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" codigo_eapb='$cod_eapb' ";
				if($year!="")
				{
					$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" AND ";
					$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" AND ";
					$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" AND ";
					$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" codigo_municipio='$codigo_municipio' ";
				}
				$sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores.=" ; ";				
				$error_bd_seq="";
				$resultados_consulta_cant_incons_total_prestadores=$coneccionBD->consultar_no_warning_get_error($sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de identificacion ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_consulta_cantidad_total_inconsistencias_todos_los_prestadores)."');</script>";
				}
				$numero_incons_total_prestadores=0;
				if(is_array($resultados_consulta_cant_incons_total_prestadores))
				{
					$numero_incons_total_prestadores=intval($resultados_consulta_cant_incons_total_prestadores[0]["cantidad_inconsistencias_total_prestadores"]);
				}
				
				$porcentaje_participacion=0;
				if($numero_incons_total_prestadores>0)
				{
					$porcentaje_participacion=round(($total_acumulado/$numero_incons_total_prestadores)*100,1,PHP_ROUND_HALF_UP);
				}
				$linea_estado_informacion.=$porcentaje_participacion;
				*/
				
				fwrite($file_calidad_de_datos, $linea_estado_informacion."\n");
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros_vista.';</script>";
				
				$html_nueva_ventana.="<tr>";
				$array_linea_estado_informacion=explode(",",$linea_estado_informacion);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_estado_informacion as $columna_estado_informacion)
				{
					$html_nueva_ventana.="<td>$columna_estado_informacion</td>";
					
					$cont_columnas_estado_informacion++;
				}
				$html_nueva_ventana.="</tr>";
				
				$cont_linea++;
			}
			fclose($file_calidad_de_datos);
			
			if($bool_ultima_seccion_para_ventana==true)
			{		
				$html_nueva_ventana.="</table>";
			
				$html_nueva_ventana.="</body>";
				$html_nueva_ventana.="</html>";
			
				$insertar_html_nueva_ventana="";
				$insertar_html_nueva_ventana.="<script>ventana_detalle.document.write(\"$html_nueva_ventana\");</script>";
				echo $insertar_html_nueva_ventana;
			}//fin ultima seccion ventana
			
			
		}//fin if hallo resultados
		
		$contador_offset+=2000;
	
	}//fin while
	
	if($hubo_resultados)
	{
		echo "<script>document.getElementById('grilla').style.display='inline';</script>";
		
		$html_reabrir_ventana="";
		$html_reabrir_ventana.="<script>";
		$html_reabrir_ventana.="function re_abrir_nueva_ventana()";
		$html_reabrir_ventana.="{";
		$html_reabrir_ventana.="ventana_detalle=window.open ('','ventana_estruct_incons_prestadores', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
		$html_reabrir_ventana.="ventana_detalle.document.body.innerHTML='';";
		$html_reabrir_ventana.="ventana_detalle.document.write(\"$html_nueva_ventana\");";	
		$html_reabrir_ventana.="}";
		$html_reabrir_ventana.="</script>";
		echo $html_reabrir_ventana;
		
		$boton_ventana=" <input type=\'button\' value=\'Ver la ventana de estructurado inconsistencias por prestador para PyP\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el estructurado inconsistencias por prestador para PyP\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_calidad_de_datos\');\"/> ";
	
		echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
		
	}
	else
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se hallaron datos en el periodo especificado para generar el reporte.';</script>";
	}
	
	
	//borrando vistas
	$sql_borrar_vistas="";
	$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_calidad_de_datos ; ";
	
	$error_bd="";		
	$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
	if($error_bd!="")
	{
		echo "<script>alert('error al borrar vista(s) ".procesar_mensaje($error_bd)."');</script>";
	}
	
	//fin borrando vistas
}//fin isset year y periodo

//FIN PARTE BUSQUEDA

?>