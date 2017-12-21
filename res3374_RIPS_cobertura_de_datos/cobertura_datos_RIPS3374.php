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



//SELECTOR FECHAS MESES PERIDOS RIPS
$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);
$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul'  >\n";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>\n";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["cod_periodo"];
	$nombre_periodo=$periodo["nombre_periodo"];
	$fecha_de_corte=$periodo["fecha_corte"];
	$selector_periodo.="<option value='$fecha_de_corte'>Periodo $cod_periodo ($nombre_periodo $fecha_de_corte)</option>\n";
}
$selector_periodo.="</select>\n";
//FIN SELECTOR FECHAS MESES PERIDOS RIPS

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
$smarty->display('cobertura_datos_RIPS3374.html.tpl');

echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros_vista=0;
$contador_offset=0;
$hubo_resultados=false;

if(isset($_POST["year_de_validacion"]) && isset($_POST["periodo"]) && isset($_POST["eapb"]) && $_POST["year_de_validacion"]!="" && $_POST["periodo"]!="none" && $_POST["eapb"]!="none")
{
	$cod_eapb=$_POST["eapb"];
	$year=$_POST["year_de_validacion"];
	$periodo=$_POST["periodo"];
	$fecha_inicio=$year."-01-01";
	$fecha_fin=$year."-12-31";
	$fecha_ini_trimestre="";
	$fecha_fin_timestre="";
	if($periodo!="none")
	{
		$mes=explode("/",$periodo)[1];
		$dia_fin=explode("/",$periodo)[0];
		$fecha_inicio=$year."-".$mes."-01";
		$fecha_fin=$year."-".$mes."-".$dia_fin;
		
		$fecha_fin_timestre=$year."-".$mes."-".$dia_fin;
		
		$mes_inicio_trimestre=0;
		$bool_bajo_year=false;
		if(intval($mes)>=3)
		{
			$mes_inicio_trimestre=intval($mes)-2;
		}
		elseif(intval($mes)==2)
		{
			$mes_inicio_trimestre=12;
			$bool_bajo_year=true;
		}
		elseif(intval($mes)==1)
		{
			$mes_inicio_trimestre=11;
			$bool_bajo_year=true;
		}
		
		$string_mes_inicio_trimestre="".$mes_inicio_trimestre;
		if(strlen($string_mes_inicio_trimestre)!=2)
		{
			$string_mes_inicio_trimestre="0".$string_mes_inicio_trimestre;
		}
		
		$year_para_trimestre=0;
		if($bool_bajo_year==true)
		{
			$year_para_trimestre=intval($year)-1;
		}
		else
		{
			$year_para_trimestre=intval($year);
		}
		$fecha_ini_trimestre=$year_para_trimestre."-".$string_mes_inicio_trimestre."-01";
	}//fin periodo
	
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
	
	$nombre_vista_para_cobertura_de_datos="vcobdatr_".$nick_user."_".$tipo_id."_".$identificacion;
	
	$sql_vista_cobertura_de_datos="";
	$sql_vista_cobertura_de_datos.="CREATE OR REPLACE VIEW $nombre_vista_para_cobertura_de_datos ";
	$sql_vista_cobertura_de_datos.=" AS SELECT DISTINCT codigo_prestador_servicios  ";
	$sql_vista_cobertura_de_datos.=" FROM gioss_tabla_estado_informacion_rips WHERE  ";
	$sql_vista_cobertura_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	$sql_vista_cobertura_de_datos.=" AND ";
	$sql_vista_cobertura_de_datos.=" codigo_eapb='$cod_eapb' ";
	if($codigo_departamento!="")
	{
		$sql_vista_cobertura_de_datos.=" AND ";
		$sql_vista_cobertura_de_datos.=" codigo_departamento='$codigo_departamento' ";
	}
	if($codigo_departamento!="" && $codigo_municipio!="")
	{
		$sql_vista_cobertura_de_datos.=" AND ";
		$sql_vista_cobertura_de_datos.=" codigo_municipio='$codigo_municipio' ";
	}
	if($cod_prestador!="")
	{
		$sql_vista_cobertura_de_datos.=" AND ";
		$sql_vista_cobertura_de_datos.=" codigo_prestador_servicios='$cod_prestador' ";
	}
	$sql_vista_cobertura_de_datos.=" ; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_cobertura_de_datos, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_cobertura_de_datos)."');</script>";
	}
	
	//echo "<script>alert(' ".procesar_mensaje($sql_vista_cobertura_de_datos)."');</script>";
	
	$numero_registros_vista=0;
	$sql_numero_registros_vista="";
	$sql_numero_registros_vista.="SELECT count(*) as contador FROM $nombre_vista_para_cobertura_de_datos  ";	
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
	$html_nueva_ventana="";
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
		$sql_query_busqueda.="SELECT * FROM $nombre_vista_para_cobertura_de_datos LIMIT $limite OFFSET $contador_offset;  ";
		$resultado_para_calidad_de_datos_desde_estado_informacion=$coneccionBD->consultar2($sql_query_busqueda);
	
		if(count($resultado_para_calidad_de_datos_desde_estado_informacion)>0)
		{
			
			$nombre_archivo_calidad_de_datos=$cod_eapb."_cobertura_datos_rips.csv";
			$ruta_archivo_calidad_de_datos=$rutaTemporal.$nombre_archivo_calidad_de_datos;
			
			//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			if($hubo_resultados==false)
			{
				$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "w") or die("fallo la creacion del archivo");
				fclose($file_calidad_de_datos);
			
				$html_abrir_ventana="";
				$html_abrir_ventana.="<script>";
				$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_info_cobertura_de_datos', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
				$html_abrir_ventana.="</script>";
				echo $html_abrir_ventana;
				
				$html_nueva_ventana="";
				$html_nueva_ventana.="<html>";
				
				$html_nueva_ventana.="<head>";
				$html_nueva_ventana.="<title>Cobertura de Reportes Esperados</title>";
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
				
				$html_encabezado.="<td>Periodo seleccionado:</td>";
				$periodo_seleccionado="";
				if($periodo!="none")
				{
					$periodo_seleccionado=$periodo;
					$html_encabezado.="<td>$periodo_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$periodo_seleccionado="Todos";
				}
				
				
				$html_encabezado.="</tr>";
				$html_encabezado.="</table>";
				$html_nueva_ventana.=$html_encabezado;
				
				$linea_encabezado="Departamento,$depto_seleccionado,Municipio,$mpio_seleccionado,Prestador,$prestador_seleccionado,Year,$year_seleccionado,Periodo,$periodo";
				
				fwrite($file_calidad_de_datos, $linea_encabezado."\n");
				//encabezado
				
				$titulos="";
				$titulos.="Periodo,Cod. EAPB,Nombre de la EAPB,Cod. Prestador,Nombre del Prestador de servicios,";
				$titulos.="Cod. Departamento,Departamento,Cod. Municipio,Municipio,Cobertura de Servicios Esperados,";
				$titulos.="N. Atenciones Reportadas,Variacion Sobre Esperado,Porcentaje de Variacion";
				fwrite($file_calidad_de_datos, $titulos."\n");
				
				$html_nueva_ventana.="<table id='tabla_ventana_estado_info' >";
				$html_nueva_ventana.="<tr>";
				$array_titulos=explode(",",$titulos);
				$cont_titulos=0;
				foreach($array_titulos as $titulo_columna)
				{
					if($cont_titulos!=1
					   && $cont_titulos!=3
					   && $cont_titulos!=5
					   && $cont_titulos!=7
					   )
					{
						$html_nueva_ventana.="<th>$titulo_columna</th>";
					}
					$cont_titulos++;
				}
				$html_nueva_ventana.="</tr>";
				fclose($file_calidad_de_datos);
			}//fin if
			
			$hubo_resultados=true;
			//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			
			$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "a") or die("fallo la creacion del archivo");
			
			foreach($resultado_para_calidad_de_datos_desde_estado_informacion as $resultado)
			{
				$linea_estado_informacion="";
				
				//identificacion
				$query_info_identificacion="";
				$query_info_identificacion.=" SELECT * ";
				$query_info_identificacion.=" FROM gioss_tabla_estado_informacion_rips WHERE  ";
				/*
				$query_info_identificacion.=" numero_secuencia=(select max(numero_secuencia) from gioss_tabla_estado_informacion_rips where codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."') ";
				$query_info_identificacion.=" AND ";
				*/
				$query_info_identificacion.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				$query_info_identificacion.=" AND ";
				$query_info_identificacion.=" codigo_eapb='$cod_eapb' ";				
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
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["fecha_validacion"].",";//1
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_eapb"].",";//2
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_eapb"].",";//3
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_prestador_servicios"].",";//4
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_prestador_servicios"].",";//5
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_departamento"].",";//6
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_del_departamento"].",";//7
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_municipio"].",";//8
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_de_municipio"].",";//9
				}
				//fin identificacion
				
				
				//conteo
				
				
				$cobertura_de_servicios_esperados="";
				
				$numero_registros_validados_trimestre=0;
				$sql_consulta_estado_informacion_1="";
				$sql_consulta_estado_informacion_1.=" SELECT ";
				$sql_consulta_estado_informacion_1.=" ( ";
				$sql_consulta_estado_informacion_1.="  SUM(COALESCE(numero_registros_ac,0)+COALESCE(numero_registros_ap,0)+ ";
				$sql_consulta_estado_informacion_1.="  COALESCE(numero_registros_au,0)+COALESCE(numero_registros_ah,0)+ ";
				$sql_consulta_estado_informacion_1.="  COALESCE(numero_registros_am,0)+COALESCE(numero_registros_at,0) )";
				$sql_consulta_estado_informacion_1.=" ) AS numero_registros_validados_trimestre ";
				$sql_consulta_estado_informacion_1.=" FROM gioss_tabla_estado_informacion_rips WHERE  ";
				$sql_consulta_estado_informacion_1.=" (fecha_validacion BETWEEN '$fecha_ini_trimestre' AND '$fecha_fin_timestre' ) ";
				$sql_consulta_estado_informacion_1.=" AND ";
				$sql_consulta_estado_informacion_1.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_estado_informacion_1.=" AND ";
				$sql_consulta_estado_informacion_1.=" codigo_estado_informacion='1' ";
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_1.=" AND ";
					$sql_consulta_estado_informacion_1.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_1.=" AND ";
					$sql_consulta_estado_informacion_1.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_1.=" AND ";
				$sql_consulta_estado_informacion_1.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_1.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_1=$coneccionBD->consultar_no_warning_get_error($sql_consulta_estado_informacion_1, $error_bd_seq);				
				if(is_array($resultados_consulta_1))
				{
					$numero_registros_validados_trimestre=intval($resultados_consulta_1[0]["numero_registros_validados_trimestre"]);
				}
				else
				{
					$numero_registros_validados_trimestre=0;
				}
				$cobertura_de_servicios_esperados=round($numero_registros_validados_trimestre/3,1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$cobertura_de_servicios_esperados.",";//10
				
				$numero_atenciones_reportadas="";
				$numero_registros_validados_periodo=0;
				$sql_consulta_estado_informacion_1="";
				$sql_consulta_estado_informacion_1.=" SELECT ";
				$sql_consulta_estado_informacion_1.=" ( ";
				$sql_consulta_estado_informacion_1.="  SUM(COALESCE(numero_registros_ac,0)+COALESCE(numero_registros_ap,0)+ ";
				$sql_consulta_estado_informacion_1.="  COALESCE(numero_registros_au,0)+COALESCE(numero_registros_ah,0)+ ";
				$sql_consulta_estado_informacion_1.="  COALESCE(numero_registros_am,0)+COALESCE(numero_registros_at,0) )";
				$sql_consulta_estado_informacion_1.=" ) AS numero_registros_validados_trimestre ";
				$sql_consulta_estado_informacion_1.=" FROM gioss_tabla_estado_informacion_rips WHERE  ";
				$sql_consulta_estado_informacion_1.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				$sql_consulta_estado_informacion_1.=" AND ";
				$sql_consulta_estado_informacion_1.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_estado_informacion_1.=" AND ";
				$sql_consulta_estado_informacion_1.=" codigo_estado_informacion='1' ";
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_1.=" AND ";
					$sql_consulta_estado_informacion_1.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_1.=" AND ";
					$sql_consulta_estado_informacion_1.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_1.=" AND ";
				$sql_consulta_estado_informacion_1.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_1.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_1=$coneccionBD->consultar_no_warning_get_error($sql_consulta_estado_informacion_1, $error_bd_seq);				
				if(is_array($resultados_consulta_1))
				{
					$numero_registros_validados_periodo=intval($resultados_consulta_1[0]["numero_registros_validados_trimestre"]);
				}
				else
				{
					$numero_registros_validados_periodo=0;
				}
				$numero_atenciones_reportadas=$numero_registros_validados_periodo;
				$linea_estado_informacion.=$numero_atenciones_reportadas.",";//11
				
				$variacion_sobre_esperado=0;
				$variacion_sobre_esperado=abs(floatval($numero_atenciones_reportadas)-floatval($cobertura_de_servicios_esperados));
				$linea_estado_informacion.=$variacion_sobre_esperado.",";//12
				
				$porcentaje_de_variacion=0;
				if(intval($cobertura_de_servicios_esperados)>0)
				{
					$porcentaje_de_variacion=round((floatval($variacion_sobre_esperado)/floatval($cobertura_de_servicios_esperados))*100,1,PHP_ROUND_HALF_UP);
				}
				$linea_estado_informacion.=$porcentaje_de_variacion;//13
				
				fwrite($file_calidad_de_datos, $linea_estado_informacion."\n");
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros_vista.';</script>";
				
				$html_nueva_ventana.="<tr>";
				$array_linea_estado_informacion=explode(",",$linea_estado_informacion);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_estado_informacion as $columna_estado_informacion)
				{
					if($cont_columnas_estado_informacion!=1
					&& $cont_columnas_estado_informacion!=3
					&& $cont_columnas_estado_informacion!=5
					&& $cont_columnas_estado_informacion!=7
					)
					{
					     $html_nueva_ventana.="<td>$columna_estado_informacion</td>";
					}
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
			}//fin if ultima seccion
			
			
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
		$html_reabrir_ventana.="ventana_detalle=window.open ('','ventana_info_cobertura_de_datos', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
		$html_reabrir_ventana.="ventana_detalle.document.body.innerHTML='';";
		$html_reabrir_ventana.="ventana_detalle.document.write(\"$html_nueva_ventana\");";	
		$html_reabrir_ventana.="}";
		$html_reabrir_ventana.="</script>";
		echo $html_reabrir_ventana;
		
		$boton_ventana=" <input type=\'button\' value=\'Ver la ventana de cobertura de datos para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el reporte de cobertura de datos para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_calidad_de_datos\');\"/> ";
	
		echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
		
	}
	else
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se hallaron datos en el periodo especificado para generar el reporte.';</script>";
	}
	
	
	//borrando vistas
	$sql_borrar_vistas="";
	$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_cobertura_de_datos ; ";
	
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