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

$coneccionBD->crearConexion();

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
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
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

	$resultado_query_eapb_usuario=$coneccionBD->consultar2_no_crea_cierra($sql_consulta_eapb_usuario_prestador);

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
	$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);

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
$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);

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
$res_dptos_query=$coneccionBD->consultar2_no_crea_cierra($query_departamentos);

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
$smarty->display('calidad_datos_4505.html.tpl');

echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros_vista=0;
$contador_offset=0;
$hubo_resultados=false;

if(isset($_POST["year_de_validacion"]) && isset($_POST["periodo"]) && isset($_POST["eapb"]) && $_POST["eapb"]!="none" && ($_POST["year_de_validacion"]==""||ctype_digit($_POST["year_de_validacion"])))
{
	$cod_eapb=$_POST["eapb"];
	$year=trim($_POST["year_de_validacion"]);
	$periodo=$_POST["periodo"];
	$fecha_inicio=$year."-01-01";
	$fecha_fin=$year."-12-31";
	$fecha_de_corte_periodo=$year."-12-31";
	if($periodo!="none" && $year!="")
	{		
		//PERIODOS PYP
		if(intval($periodo)==1)
		{
		   $fecha_inicio=$year."-01-01";
		   $fecha_fin=$year."-03-31";
		   $fecha_de_corte_periodo=$year."-03-31";;
		}
		if(intval($periodo)==2)
		{
		   $fecha_inicio=$year."-04-01";
		   $fecha_fin=$year."-06-30";
		   $fecha_de_corte_periodo=$year."-06-30";
		}
		if(intval($periodo)==3)
		{
		   $fecha_inicio=$year."-07-01";
		   $fecha_fin=$year."-09-30";
		   $fecha_de_corte_periodo=$year."-09-30";
		}
		if(intval($periodo)==4)
		{
		   $fecha_inicio=$year."-10-01";
		   $fecha_fin=$year."-12-31";
		   $fecha_de_corte_periodo=$year."-12-31";
		}	
		//FIN PERIODOS PYP
	}//fin if
	
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
	
	$sql_vista_calidad_de_datos="";
	$sql_vista_calidad_de_datos.="CREATE OR REPLACE VIEW $nombre_vista_para_calidad_de_datos ";
	$sql_vista_calidad_de_datos.=" AS SELECT DISTINCT codigo_prestador_servicios ";
	$sql_vista_calidad_de_datos.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
	$sql_vista_calidad_de_datos.=" codigo_eapb='$cod_eapb' ";
	if($year!="")
	{
		$sql_vista_calidad_de_datos.=" AND ";
		$sql_vista_calidad_de_datos.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
		//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	}
	if($codigo_departamento!="")
	{
		$sql_vista_calidad_de_datos.=" AND ";
		$sql_vista_calidad_de_datos.=" codigo_departamento='$codigo_departamento' ";
	}
	if($codigo_departamento!="" && $codigo_municipio!="")
	{
		$sql_vista_calidad_de_datos.=" AND ";
		$sql_vista_calidad_de_datos.=" codigo_municipio='$codigo_municipio' ";
	}
	if($cod_prestador!="")
	{
		$sql_vista_calidad_de_datos.=" AND ";
		$sql_vista_calidad_de_datos.=" codigo_prestador_servicios='$cod_prestador' ";
	}
	$sql_vista_calidad_de_datos.=" ; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_calidad_de_datos, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_calidad_de_datos)."');</script>";
	}
	
	//echo "<script>alert(' ".procesar_mensaje($sql_vista_calidad_de_datos)."');</script>";
	
	$numero_registros_vista=0;
	$sql_numero_registros_vista="";
	$sql_numero_registros_vista.="SELECT count(*) as contador FROM $nombre_vista_para_calidad_de_datos  ";	
	$sql_numero_registros_vista.=" ; ";
	$resultado_query_numero_registros=$coneccionBD->consultar2_no_crea_cierra($sql_numero_registros_vista);
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
		$resultado_para_calidad_de_datos_desde_estado_informacion=$coneccionBD->consultar2_no_crea_cierra($sql_query_busqueda);
	
		if(count($resultado_para_calidad_de_datos_desde_estado_informacion)>0 && is_array($resultado_para_calidad_de_datos_desde_estado_informacion))
		{
			
			$nombre_archivo_calidad_de_datos=$cod_eapb."_calidad_datos_pyp.csv";
			$ruta_archivo_calidad_de_datos=$rutaTemporal.$nombre_archivo_calidad_de_datos;
			
			//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			if($hubo_resultados==false)
			{
				$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "w") or die("fallo la creacion del archivo");
				fclose($file_calidad_de_datos);
			
				$html_abrir_ventana="";
				$html_abrir_ventana.="<script>";
				$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_info_calidad_de_datos', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
				$html_abrir_ventana.="</script>";
				echo $html_abrir_ventana;
				
				$html_nueva_ventana="";
				$html_nueva_ventana.="<html>";
				
				$html_nueva_ventana.="<head>";
				$html_nueva_ventana.="<title>Reporte de Calidad de Datos</title>";
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
					$res_dptos_query_sel=$coneccionBD->consultar2_no_crea_cierra($query_departamentos_sel);
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
					$res_mpios_query_sel=$coneccionBD->consultar2_no_crea_cierra($query_mpios_sel);
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
				$titulos.="Cod. Departamento,Departamento,Cod. Municipio,Municipio,No. Archivos Presentados Por periodo,";
				$titulos.="Archivos Presentados Aceptados,Archivos Presentados Rechazados,Porcentaje de Aceptacion,Registros Validados,Registros Aceptados,";
				$titulos.="Registros Rechazados,Porcentaje de Aceptacion Registros ,N. Inconsistencias Obligatorias,N. Inconsistencias Informativas,";
				$titulos.="Total Inconsistencias,Promedio Obligatorias Inconsistencias,Promedio Informativas Inconsistencias,";
				$titulos.="Promedio Total Inconsistencias,N. Registros Buenos,N. Registros Malos,";
				$titulos.="Porcentaje de registros con inconsistencias obligatorias,Porcentaje de registros correctos.";
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
			}//fin if inicio
			$hubo_resultados=true;
			//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			
			$file_calidad_de_datos= fopen($ruta_archivo_calidad_de_datos, "a") or die("fallo la creacion del archivo");
				
			foreach($resultado_para_calidad_de_datos_desde_estado_informacion as $resultado)
			{
				$linea_estado_informacion="";
				
				//identificacion
				$query_info_identificacion="";
				$query_info_identificacion.=" SELECT * ";
				$query_info_identificacion.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				$query_info_identificacion.=" numero_secuencia=(select max(numero_secuencia) from gioss_tabla_estado_informacion_4505 where ";
				$query_info_identificacion.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."') ";				
				$query_info_identificacion.=" AND ";
				$query_info_identificacion.=" codigo_eapb='$cod_eapb' ";
				if($year!="")
				{
					$query_info_identificacion.=" AND ";
					$query_info_identificacion.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				$query_info_identificacion.=" AND ";
				$query_info_identificacion.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";				
				$query_info_identificacion.=" order by fecha_corte_periodo,numero_secuencia ; ";
				$error_bd_seq="";
				$resultados_consulta_identificacion=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_info_identificacion, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('error al consultar datos de identificacion ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($query_info_identificacion)."');</script>";
				}
				if(is_array($resultados_consulta_identificacion))
				{
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["fecha_corte_periodo"].",";//1
					//$linea_estado_informacion.=$resultados_consulta_identificacion[0]["fecha_validacion"].",";//1
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_eapb"].",";//2
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_eapb"].",";//3
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_prestador_servicios"].",";//4
					$query_nombre_prestador="SELECT nom_entidad_prestadora FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$resultados_consulta_identificacion[0]["codigo_prestador_servicios"]."';";
					$error_bd_seq="";
					$resultados_nombre_prestador=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_nombre_prestador, $error_bd_seq);
					if(is_array($resultados_nombre_prestador) && count($resultados_nombre_prestador)>0)
					{
						$linea_estado_informacion.=$resultados_nombre_prestador[0]["nom_entidad_prestadora"].",";//5
					}
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_departamento"].",";//6
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_del_departamento"].",";//7
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["codigo_municipio"].",";//8
					$linea_estado_informacion.=$resultados_consulta_identificacion[0]["nombre_de_municipio"].",";//9
				}
				//fin identificacion
				
				//conteo
				$numero_de_rips_presentados="";
				$sql_consulta_estado_informacion_1="";
				$sql_consulta_estado_informacion_1.=" SELECT count(*) AS numero_rips_presentados ";
				$sql_consulta_estado_informacion_1.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				$sql_consulta_estado_informacion_1.=" codigo_eapb='$cod_eapb' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_1.=" AND ";
					$sql_consulta_estado_informacion_1.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
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
				$resultados_consulta_1=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_1, $error_bd_seq);
				$numero_de_rips_presentados=$resultados_consulta_1[0]["numero_rips_presentados"];
				$linea_estado_informacion.=$numero_de_rips_presentados.",";//10
				
				$numero_de_rips_aceptados="";
				$sql_consulta_estado_informacion_2="";
				$sql_consulta_estado_informacion_2.=" SELECT count(*) AS numero_de_rips_aceptados ";
				$sql_consulta_estado_informacion_2.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";				
				$sql_consulta_estado_informacion_2.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_estado_informacion_2.=" AND ";
				$sql_consulta_estado_informacion_2.=" codigo_estado_informacion='1' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_2.=" AND ";
					$sql_consulta_estado_informacion_2.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_2.=" AND ";
					$sql_consulta_estado_informacion_2.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_2.=" AND ";
					$sql_consulta_estado_informacion_2.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_2.=" AND ";
				$sql_consulta_estado_informacion_2.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_2.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_2=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_2, $error_bd_seq);
				$numero_de_rips_aceptados=$resultados_consulta_2[0]["numero_de_rips_aceptados"];
				$linea_estado_informacion.=$numero_de_rips_aceptados.",";//11
				
				$numero_rips_rechazados="";
				$sql_consulta_estado_informacion_3="";
				$sql_consulta_estado_informacion_3.=" SELECT count(*) AS numero_rips_rechazados ";
				$sql_consulta_estado_informacion_3.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				$sql_consulta_estado_informacion_3.=" codigo_eapb='$cod_eapb' ";
				$sql_consulta_estado_informacion_3.=" AND ";
				$sql_consulta_estado_informacion_3.=" codigo_estado_informacion='2' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_3.=" AND ";
					$sql_consulta_estado_informacion_3.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_3.=" AND ";
					$sql_consulta_estado_informacion_3.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_3.=" AND ";
					$sql_consulta_estado_informacion_3.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_3.=" AND ";
				$sql_consulta_estado_informacion_3.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_3.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_3=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_3, $error_bd_seq);
				$numero_rips_rechazados=$resultados_consulta_3[0]["numero_rips_rechazados"];
				$linea_estado_informacion.=$numero_rips_rechazados.",";//12
				
				$porcentaje_rips_aceptados=0;
				$porcentaje_rips_aceptados=round((floatval($numero_de_rips_aceptados)/(floatval($numero_de_rips_aceptados)+floatval($numero_rips_rechazados)))*100,1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$porcentaje_rips_aceptados.",";//19->13
				
				$numero_registros_validados="";
				$sql_consulta_estado_informacion_4="";
				$sql_consulta_estado_informacion_4.=" SELECT ";
				$sql_consulta_estado_informacion_4.=" SUM(COALESCE(total_registros,0))";
				$sql_consulta_estado_informacion_4.=" AS numero_registros_validados ";
				$sql_consulta_estado_informacion_4.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				$sql_consulta_estado_informacion_4.=" codigo_eapb='$cod_eapb' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_4.=" AND ";
					$sql_consulta_estado_informacion_4.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_4.=" AND ";
					$sql_consulta_estado_informacion_4.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_4.=" AND ";
					$sql_consulta_estado_informacion_4.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_4.=" AND ";
				$sql_consulta_estado_informacion_4.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_4.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_4=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_4, $error_bd_seq);				
				if(is_array($resultados_consulta_4))
				{
					$numero_registros_validados=$resultados_consulta_4[0]["numero_registros_validados"];
				}
				else
				{
					$numero_registros_validados=0;
				}
				$linea_estado_informacion.=$numero_registros_validados.",";//13->14
				
				$numero_registros_aceptados="";
				$sql_consulta_estado_informacion_5="";
				$sql_consulta_estado_informacion_5.=" SELECT ";
				$sql_consulta_estado_informacion_5.=" SUM(COALESCE(total_registros,0))";
				$sql_consulta_estado_informacion_5.=" AS numero_registros_aceptados ";
				$sql_consulta_estado_informacion_5.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				
				$sql_consulta_estado_informacion_5.=" codigo_eapb='$cod_eapb' ";
                                $sql_consulta_estado_informacion_5.=" AND ";
				$sql_consulta_estado_informacion_5.=" codigo_estado_informacion='1' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_5.=" AND ";
					$sql_consulta_estado_informacion_5.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_5.=" AND ";
					$sql_consulta_estado_informacion_5.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_5.=" AND ";
					$sql_consulta_estado_informacion_5.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_5.=" AND ";
				$sql_consulta_estado_informacion_5.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_5.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_5=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_5, $error_bd_seq);				
				if(is_array($resultados_consulta_5))
				{
					$numero_registros_aceptados=$resultados_consulta_5[0]["numero_registros_aceptados"];
					if(trim($numero_registros_aceptados)=="")
					{
						$numero_registros_aceptados=0;
					}
				}
				else
				{
					$numero_registros_aceptados=0;
				}
				$linea_estado_informacion.=$numero_registros_aceptados.",";//14->15
				
				//echo "<script>alert('numero_registros_aceptados $numero_registros_aceptados');</script>";
				
				$numero_registros_rechazados="";
				$sql_consulta_estado_informacion_6="";
				$sql_consulta_estado_informacion_6.=" SELECT ";
				$sql_consulta_estado_informacion_6.=" SUM(COALESCE(total_registros,0))";
				$sql_consulta_estado_informacion_6.=" AS numero_registros_rechazados ";
				$sql_consulta_estado_informacion_6.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				$sql_consulta_estado_informacion_6.=" codigo_eapb='$cod_eapb' ";
                                $sql_consulta_estado_informacion_6.=" AND ";
				$sql_consulta_estado_informacion_6.=" codigo_estado_informacion='2' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_6.=" AND ";
					$sql_consulta_estado_informacion_6.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_6.=" AND ";
					$sql_consulta_estado_informacion_6.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_6.=" AND ";
					$sql_consulta_estado_informacion_6.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_6.=" AND ";
				$sql_consulta_estado_informacion_6.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_6.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_6=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_6, $error_bd_seq);
				if(is_array($resultados_consulta_6) && count($resultados_consulta_6)>0)
				{
					$numero_registros_rechazados=$resultados_consulta_6[0]["numero_registros_rechazados"];
					if(trim($numero_registros_rechazados)=="")
					{
						$numero_registros_rechazados=0;
					}
				}
				else
				{
					$numero_registros_rechazados=0;
				}
				$linea_estado_informacion.=$numero_registros_rechazados.",";//15->16
				
				$porcentaje_de_registros_aceptados="";
				$porcentaje_de_registros_aceptados=round((floatval($numero_registros_aceptados)/(floatval($numero_registros_aceptados)+floatval($numero_registros_rechazados)))*100,1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$porcentaje_de_registros_aceptados.",";//20->17
				
				
				//consulta numeros de secuencia para consultar estadisticas sobre inconsistencias
				$sql_consulta_estado_informacion_7="";
				$sql_consulta_estado_informacion_7.=" SELECT ";
				$sql_consulta_estado_informacion_7.=" numero_secuencia ";
				$sql_consulta_estado_informacion_7.=" FROM gioss_tabla_estado_informacion_4505 WHERE  ";
				$sql_consulta_estado_informacion_7.=" codigo_eapb='$cod_eapb' ";
				if($year!="")
				{
					$sql_consulta_estado_informacion_7.=" AND ";
					$sql_consulta_estado_informacion_7.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					//$sql_vista_calidad_de_datos.=" (fecha_validacion BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_estado_informacion_7.=" AND ";
					$sql_consulta_estado_informacion_7.=" codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_estado_informacion_7.=" AND ";
					$sql_consulta_estado_informacion_7.=" codigo_municipio='$codigo_municipio' ";
				}
				
				$sql_consulta_estado_informacion_7.=" AND ";
				$sql_consulta_estado_informacion_7.=" codigo_prestador_servicios='".$resultado["codigo_prestador_servicios"]."' ";
				
				$sql_consulta_estado_informacion_7.=" ; ";
				$error_bd_seq="";
				$resultados_consulta_7=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_consulta_estado_informacion_7, $error_bd_seq);
				
				$acc_inconsistencias_obligatorias=0;
				$acc_inconsistencias_informativas=0;
				
				$acc_lineas_malas=0;
				$acc_lineas_buenas=0;
				
				if(is_array($resultados_consulta_7))
				{
					foreach($resultados_consulta_7 as $numero_secuencia_actual)
					{
						$query_numero_inconsistencias_obligatorias="";
						$query_numero_inconsistencias_obligatorias.="SELECT count(*) AS numero_inconsistencias_obligatorias FROM gioss_reporte_inconsistencia_archivos_4505 ";
						$query_numero_inconsistencias_obligatorias.=" WHERE ";
						$query_numero_inconsistencias_obligatorias.=" numero_orden='".$numero_secuencia_actual["numero_secuencia"]."' ";
						$query_numero_inconsistencias_obligatorias.=" AND ";
						$query_numero_inconsistencias_obligatorias.=" (cod_tipo_inconsitencia='01' OR cod_tipo_inconsitencia='03') ";
						$query_numero_inconsistencias_obligatorias.=" ; ";
						$resultados_consulta_inconsistencias_obligatorias=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_numero_inconsistencias_obligatorias, $error_bd_seq);
						$acc_inconsistencias_obligatorias=$acc_inconsistencias_obligatorias+intval($resultados_consulta_inconsistencias_obligatorias[0]["numero_inconsistencias_obligatorias"]);
						
						$query_numero_inconsistencias_informativas="";
						$query_numero_inconsistencias_informativas.="SELECT count(*) AS numero_inconsistencias_informativas FROM gioss_reporte_inconsistencia_archivos_4505 ";
						$query_numero_inconsistencias_informativas.=" WHERE ";
						$query_numero_inconsistencias_informativas.=" numero_orden='".$numero_secuencia_actual["numero_secuencia"]."' ";
						$query_numero_inconsistencias_informativas.=" AND ";
						$query_numero_inconsistencias_informativas.=" (cod_tipo_inconsitencia='02' OR cod_tipo_inconsitencia='04') ";
						$query_numero_inconsistencias_informativas.=" ; ";
						$resultados_consulta_inconsistencias_obligatorias=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_numero_inconsistencias_informativas, $error_bd_seq);
						$acc_inconsistencias_informativas=$acc_inconsistencias_informativas+intval($resultados_consulta_inconsistencias_obligatorias[0]["numero_inconsistencias_informativas"]);
						
						/*
						//parte lineas malas
						$nombre_vista_registros_malos="vnlmcd_".$nick_user."_".$tipo_id."_".$identificacion;
						$query_registros_malos="";
						$query_registros_malos.=" CREATE OR REPLACE VIEW $nombre_vista_registros_malos ";
						$query_registros_malos.=" AS SELECT DISTINCT numero_linea,cod_tipo_inconsitencia,numero_orden AS numero_lineas_malas ";
						$query_registros_malos.=" FROM gioss_reporte_inconsistencia_archivos_4505 WHERE ";
						$query_registros_malos.=" cod_tipo_inconsitencia='01' OR cod_tipo_inconsitencia='03' ORDER BY numero_linea ";
						$query_registros_malos.=" ; ";
						$error_bd_seq="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_registros_malos, $error_bd_seq);
						if($error_bd_seq!="")
						{
							echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($query_registros_malos)."');</script>";
						}
						
						$query_consulta_lineas_malas="";
						$query_consulta_lineas_malas.="SELECT count(*) AS lineas_malas FROM $nombre_vista_registros_malos;";
						$resultados_lineas_malas=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_consulta_lineas_malas, $error_bd_seq);
						$acc_lineas_malas=$acc_lineas_malas+intval($resultados_lineas_malas[0]["lineas_malas"]);
						
						//borrando vistas
						
						$sql_borrar_vistas="";
						$sql_borrar_vistas.=" DROP VIEW $nombre_vista_registros_malos ; ";
						
						$error_bd="";		
						$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
						if($error_bd!="")
						{
							echo "<script>alert('error al borrar vista(s) ".procesar_mensaje($error_bd)."');</script>";
						}
						
						//fin borrando vistas
						
						//fin lineas malas
						*/
						
						//parte lineas buenas y malas
						
						$query_consulta_lineas_malas="";
						$query_consulta_lineas_malas.="SELECT count(*) AS lineas_malas FROM gios_datos_rechazados_r4505 WHERE estado_registro='2'";
						$query_consulta_lineas_malas.=" AND numero_de_secuencia='".$numero_secuencia_actual["numero_secuencia"]."' ;";
						$resultados_lineas_malas=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_consulta_lineas_malas, $error_bd_seq);
						$acc_lineas_malas=$acc_lineas_malas+intval($resultados_lineas_malas[0]["lineas_malas"]);
						
						$query_consulta_lineas_buenas="";
						$query_consulta_lineas_buenas.="SELECT count(*) AS lineas_buenas FROM gios_datos_rechazados_r4505 WHERE estado_registro='1'";
						$query_consulta_lineas_buenas.=" AND numero_de_secuencia='".$numero_secuencia_actual["numero_secuencia"]."' ;";
						$resultados_lineas_buenas=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_consulta_lineas_buenas, $error_bd_seq);
						$acc_lineas_buenas=$acc_lineas_buenas+intval($resultados_lineas_buenas[0]["lineas_buenas"]);					
						
						//fin lineas buenas y malas
						
					}//fin foreach
				}//fin if es array
				//fin consulta numeros de secuencia para consultar estadisticas sobre inconsistencias
				
				$numero_inconsistencias_obligatorias="";
				$numero_inconsistencias_obligatorias=$acc_inconsistencias_obligatorias;
				$linea_estado_informacion.=$numero_inconsistencias_obligatorias.",";//
				
				$numero_inconsistencias_informativas="";
				$numero_inconsistencias_informativas=$acc_inconsistencias_informativas;
				$linea_estado_informacion.=$numero_inconsistencias_informativas.",";//
				
				$total_inconsistencias="";
				$total_inconsistencias=$acc_inconsistencias_informativas+$acc_inconsistencias_obligatorias;
				$linea_estado_informacion.=$total_inconsistencias.",";//
				
				$promedio_inconsistencias_obligatorias=0;
				$promedio_inconsistencias_obligatorias=round((floatval($numero_inconsistencias_obligatorias)/floatval($numero_registros_validados)),1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$promedio_inconsistencias_obligatorias.",";//
				
				$promedio_inconsistencias_informativas=0;
				$promedio_inconsistencias_informativas=round((floatval($numero_inconsistencias_informativas)/floatval($numero_registros_validados)),1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$promedio_inconsistencias_informativas.",";//
				
				$promedio_inconsistencias=0;
				$promedio_inconsistencias=round((floatval($total_inconsistencias)/floatval($numero_registros_validados)),1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$promedio_inconsistencias.",";//
				
				$numero_registros_buenos="";
				$numero_registros_buenos=floatval($acc_lineas_buenas);
				$linea_estado_informacion.=$numero_registros_buenos.",";//
				
				$numero_registros_malos="";
				$numero_registros_malos=floatval($acc_lineas_malas);
				$linea_estado_informacion.=$numero_registros_malos.",";//
				
				$total_registros_hasta_el_momento=0;
				$total_registros_hasta_el_momento=floatval($numero_registros_malos)+floatval($numero_registros_buenos);
				
				$porcentaje_registros_malos="";
				$porcentaje_registros_malos=round((floatval($numero_registros_malos)/floatval($total_registros_hasta_el_momento))*100,1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$porcentaje_registros_malos."%,";//
				
				$porcentaje_de_registros_correctos="";
				$porcentaje_de_registros_correctos=round((floatval($numero_registros_buenos)/floatval($total_registros_hasta_el_momento))*100,1,PHP_ROUND_HALF_UP);
				$linea_estado_informacion.=$porcentaje_de_registros_correctos."%";//25
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
		$html_reabrir_ventana.="ventana_detalle=window.open ('','ventana_info_calidad_de_datos', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
		$html_reabrir_ventana.="ventana_detalle.document.body.innerHTML='';";
		$html_reabrir_ventana.="ventana_detalle.document.write(\"$html_nueva_ventana\");";	
		$html_reabrir_ventana.="}";
		$html_reabrir_ventana.="</script>";
		echo $html_reabrir_ventana;
		
		$boton_ventana=" <input type=\'button\' value=\'Ver la ventana de calidad de datos para PyP\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el reporte de calidad de datos para PyP\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_calidad_de_datos\');\"/> ";
	
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
	$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
	if($error_bd!="")
	{
		echo "<script>alert('error al borrar vista(s) ".procesar_mensaje($error_bd)."');</script>";
	}
	
	//fin borrando vistas
}//fin isset year y periodo

//FIN PARTE BUSQUEDA
$coneccionBD->cerrar_conexion();
?>