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
//$smarty->assign("campo_fechas_corte", $selector_periodo, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('estructurado_inconsistencias_4505.html.tpl');

echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros_vista_1=0;
$contador_offset=0;
$hubo_resultados=false;
$html_abrir_ventana="";

if(isset($_POST["year_de_validacion"]) && isset($_POST["eapb"]) && isset($_POST["tipo_busqueda_inconsistencias"])
   && $_POST["year_de_validacion"]!="" && $_POST["eapb"]!="none")
{
	$cod_eapb=$_POST["eapb"];
	$year=$_POST["year_de_validacion"];
	
	
	$fecha_inicio=$year."-01-01";
	$fecha_fin=$year."-12-31";
	$fecha_de_corte_periodo=$year."-12-31";
	/*
	$periodo=$_POST["periodo"];
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
	*/
	
	$clasificacion_inconsistencia=$_POST["tipo_busqueda_inconsistencias"];
	
	//echo "<script>alert('$clasificacion_inconsistencia');</script>";
	
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
	
	$nombre_vista_para_estructurado_inconsistencias_1="vestincon1pyp_".$nick_user."_".$tipo_id."_".$identificacion;
	
	$sql_vista_inconsistencias_1="";
	$sql_vista_inconsistencias_1.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_1 ";
	$sql_vista_inconsistencias_1.=" AS SELECT * ";
	$sql_vista_inconsistencias_1.=" FROM gioss_reporte_inconsistencia_archivos_4505 grir INNER JOIN gioss_tabla_estado_informacion_4505 gteir ";
	$sql_vista_inconsistencias_1.=" ON (grir.numero_orden=gteir.numero_secuencia)  WHERE  ";
	$sql_vista_inconsistencias_1.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	$sql_vista_inconsistencias_1.=" AND ";
	$sql_vista_inconsistencias_1.=" codigo_eapb='$cod_eapb' ";
	if($codigo_departamento!="")
	{
		$sql_vista_inconsistencias_1.=" AND ";
		$sql_vista_inconsistencias_1.=" codigo_departamento='$codigo_departamento' ";
	}
	if($codigo_departamento!="" && $codigo_municipio!="")
	{
		$sql_vista_inconsistencias_1.=" AND ";
		$sql_vista_inconsistencias_1.=" codigo_municipio='$codigo_municipio' ";
	}
	if($cod_prestador!="")
	{
		$sql_vista_inconsistencias_1.=" AND ";
		$sql_vista_inconsistencias_1.=" codigo_prestador_servicios='$cod_prestador' ";
	}
	$sql_vista_inconsistencias_1.=" order by fecha_corte_periodo,numero_secuencia ; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_1, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_1)."');</script>";
	}
	
	//echo "<script>alert(' ".procesar_mensaje($sql_vista_inconsistencias_1)."');</script>";
	
	$numero_registros_vista_1=0;
	$sql_numero_registros_vista_1="";
	$sql_numero_registros_vista_1.="SELECT count(*) as contador FROM $nombre_vista_para_estructurado_inconsistencias_1  ";	
	$sql_numero_registros_vista_1.=" ; ";
	$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros_vista_1);
	if(count($resultado_query_numero_registros) && is_array($resultado_query_numero_registros))
	{
		$numero_registros_vista_1=intval($resultado_query_numero_registros[0]["contador"]);
	}
	//echo "<script>alert('se genero vista $numero_registros_vista_1');</script>";
	$bool_datos_consultados_con_exito=false;
	
	if($numero_registros_vista_1>0)
	{
		$bool_datos_consultados_con_exito=true;
		$hubo_resultados=true;
	}
	
	if($bool_datos_consultados_con_exito==true)
	{
		$nombre_archivo_estructurado_inconsistencias=$cod_eapb."_consolidados_inconsistencias_4505.csv";
		$ruta_archivo_estructurado_inconsistencias=$rutaTemporal.$nombre_archivo_estructurado_inconsistencias;
		
		$file_estructurado_inconsistencias= fopen($ruta_archivo_estructurado_inconsistencias, "w") or die("fallo la creacion del archivo");
		fclose($file_estructurado_inconsistencias);
		
		
		
		$html_nueva_ventana="";
		$html_nueva_ventana.="<html>";
		
		$html_nueva_ventana.="<head>";
		$html_nueva_ventana.="<title>Consolidado de Inconsistencias para el a&ntildeo $year</title>";
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
		
		
		
		$file_estructurado_inconsistencias= fopen($ruta_archivo_estructurado_inconsistencias, "a") or die("fallo la creacion del archivo");
		
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
		
		fwrite($file_estructurado_inconsistencias, $linea_encabezado."\n");
		//encabezado
			
		$titulos="";
		/*
		$titulos.="Cod. Departamento,";
		$titulos.="Departamento,";
		$titulos.="Cod. Municipio,";
		$titulos.="Municipio,";
		$titulos.="Cod. Prestador,";
		$titulos.="Nombre Prestador,";
		*/
		if($clasificacion_inconsistencia=="1_ti")
		{
			$titulos.="Cod. Tipo Inconsistencia,";
			$titulos.="Descripcion,";
			$titulos.="Total Inconsistencias Periodo I,";
			$titulos.="Total Inconsistencias Periodo II,";
			$titulos.="Total Inconsistencias Periodo III,";
			$titulos.="Total Inconsistencias Periodo IV,";
			$titulos.="Total Inconsistencias,";
			$titulos.="Porcentaje de Participacion.";
		}
		if($clasificacion_inconsistencia=="2_gi")
		{
			$titulos.="Cod. Tipo Inconsistencia,";
			$titulos.="Descripcion,";
			$titulos.="Cod. Grupo Inconsistencia,";
			$titulos.="Descripcion,";
			$titulos.="Total Inconsistencias Periodo I,";
			$titulos.="Total Inconsistencias Periodo II,";
			$titulos.="Total Inconsistencias Periodo III,";
			$titulos.="Total Inconsistencias Periodo IV,";
			$titulos.="Total Inconsistencias,";
			$titulos.="Porcentaje de Participacion.";
		}
		if($clasificacion_inconsistencia=="3_di")
		{
			$titulos.="Cod. Tipo Inconsistencia,";
			$titulos.="Descripcion,";
			$titulos.="Cod. Grupo Inconsistencia,";
			$titulos.="Descripcion,";
			$titulos.="Cod. Detalle Inconsistencia,";
			$titulos.="Descripcion,";
			$titulos.="Total Inconsistencias Periodo I,";
			$titulos.="Total Inconsistencias Periodo II,";
			$titulos.="Total Inconsistencias Periodo III,";
			$titulos.="Total Inconsistencias Periodo IV,";
			$titulos.="Total Inconsistencias,";
			$titulos.="Porcentaje de Participacion.";
		}
		
		fwrite($file_estructurado_inconsistencias, $titulos."\n");
		
		
		$html_nueva_ventana.="<table id='tabla_ventana_cons_incons' >";
		$html_nueva_ventana.="<tr>";
		$array_titulos=explode(",",$titulos);
		$cont_titulos=0;
		foreach($array_titulos as $titulo_columna)
		{
			
			$html_nueva_ventana.="<th>$titulo_columna</th>";
			
			$cont_titulos++;
		}
		$html_nueva_ventana.="</tr>";
		
		$query_clasificacion_inconsistencias="";
		if($clasificacion_inconsistencia=="1_ti")
		{
			$query_clasificacion_inconsistencias.=" SELECT * FROM gioss_tipo_inconsistencias ORDER BY tipo_validacion; ";
		}
		if($clasificacion_inconsistencia=="2_gi")
		{
			$query_clasificacion_inconsistencias.=" SELECT * FROM gioss_grupo_inconsistencias t1 ";
			$query_clasificacion_inconsistencias.=" INNER JOIN gioss_tipo_inconsistencias t2 ON (t1.codigo_tipo_validacion=t2.tipo_validacion) ";
			$query_clasificacion_inconsistencias.=" ORDER BY grupo_validacion ; ";
		}
		if($clasificacion_inconsistencia=="3_di")
		{
			$query_clasificacion_inconsistencias.=" SELECT * FROM gioss_detalle_inconsistecias_4505 t1 ";
			$query_clasificacion_inconsistencias.=" INNER JOIN gioss_grupo_inconsistencias t2 ON (t1.codigo_grupo_inconsistencia=t2.grupo_validacion) ";
			$query_clasificacion_inconsistencias.=" INNER JOIN gioss_tipo_inconsistencias t3 ON (t1.codigo_tipo_inconsistencia=t3.tipo_validacion) ";
			$query_clasificacion_inconsistencias.=" ORDER BY codigo_detalle_inconsistencia ; ";
		}
		$resultados_clasificacion_inconsistencias=array();
		$resultados_clasificacion_inconsistencias=$coneccionBD->consultar2($query_clasificacion_inconsistencias);
		
		$cont_linea=0;
		foreach($resultados_clasificacion_inconsistencias as $res_clas_incons)
		{
			//reinicia la linea
			$linea_estado_informacion="";
			
			$cod_tipo_inconsistencia="";
			$cod_grupo_inconsistencia="";
			$cod_detalle_inconsistencia="";
			
			if($clasificacion_inconsistencia=="1_ti")
			{
				$cod_tipo_inconsistencia=$res_clas_incons["tipo_validacion"];
				$linea_estado_informacion.=$cod_tipo_inconsistencia.",";//
				
				$descripcion=$res_clas_incons["descripcion_tipo_validacion"];
				$linea_estado_informacion.=$descripcion.",";//
			}//fin if
			if($clasificacion_inconsistencia=="2_gi")
			{				
				$cod_tipo_inconsistencia=$res_clas_incons["tipo_validacion"];
				$linea_estado_informacion.=$cod_tipo_inconsistencia.",";//
				
				$descripcion=$res_clas_incons["descripcion_tipo_validacion"];
				$linea_estado_informacion.=$descripcion.",";//
				
				$cod_grupo_inconsistencia=$res_clas_incons["grupo_validacion"];
				$linea_estado_informacion.=$cod_grupo_inconsistencia.",";//
				
				$descripcion=$res_clas_incons["descripcion_grupo_validacion"];
				$linea_estado_informacion.=$descripcion.",";//
			}//fin if
			if($clasificacion_inconsistencia=="3_di")
			{
				$cod_tipo_inconsistencia=$res_clas_incons["tipo_validacion"];
				$linea_estado_informacion.=$cod_tipo_inconsistencia.",";//
				
				$descripcion=$res_clas_incons["descripcion_tipo_validacion"];
				$linea_estado_informacion.=$descripcion.",";//
				
				$cod_grupo_inconsistencia=$res_clas_incons["grupo_validacion"];
				$linea_estado_informacion.=$cod_grupo_inconsistencia.",";//
				
				$descripcion=$res_clas_incons["descripcion_grupo_validacion"];
				$linea_estado_informacion.=$descripcion.",";//
				
				$cod_detalle_inconsistencia=$res_clas_incons["codigo_detalle_inconsistencia"];
				$linea_estado_informacion.=$cod_detalle_inconsistencia.",";//
				
				$descripcion_array=array();
				$descripcion_array=explode(";;",$res_clas_incons["descripcion_inconsistencia"]);
				$linea_estado_informacion.=$descripcion_array[1].",";//
			}//fin if
			
			//periodo 1
			$valor_periodo_1=0;
			$year_mes_dia_ini=$year."-01-01";
			$year_mes_dia_fin=$year."-03-31";
			//crea una vista con los nombres de los archivos distintos para el periodo
			$nombre_vista_para_estructurado_inconsistencias_2="vestincon2pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_2="";
			$sql_vista_inconsistencias_2.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_2 ";
			$sql_vista_inconsistencias_2.=" AS SELECT DISTINCT nombre_archivo_datos_originales ";
			$sql_vista_inconsistencias_2.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 ";
			$sql_vista_inconsistencias_2.="  WHERE  ";
			$sql_vista_inconsistencias_2.=" (fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_2.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_2, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_2)."');</script>";
			}
			//fin crea vista
			
			//crea una vista uniendo la vista de nombres distintos con su mayor numero de secuencia
			$nombre_vista_para_estructurado_inconsistencias_3="vestincon3pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_3="";
			$sql_vista_inconsistencias_3.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_3 ";
			$sql_vista_inconsistencias_3.=" AS SELECT max(t1.numero_secuencia),t1.nombre_archivo_datos_originales as nombre_pyp ";
			$sql_vista_inconsistencias_3.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
			$sql_vista_inconsistencias_3.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_2 t2 ON (t1.nombre_archivo_datos_originales=t2.nombre_archivo_datos_originales)";
			$sql_vista_inconsistencias_3.="  WHERE  ";
			$sql_vista_inconsistencias_3.=" (t1.fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_3.=" GROUP BY nombre_pyp; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_3, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_3)."');</script>";
			}
			//fin vista			
			$numero_inconsistencias_mes="";
			if($clasificacion_inconsistencia=="1_ti")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_tipo_inconsitencia='$cod_tipo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="2_gi")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_grupo_inconsistencia='$cod_grupo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="3_di")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_detalle_inconsistencia='$cod_detalle_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
				
			}//fin if
			$valor_periodo_1=intval($numero_inconsistencias_mes);
			//fin periodo 1
			
			//periodo 2
			$valor_periodo_2=0;
			$year_mes_dia_ini=$year."-04-01";
			$year_mes_dia_fin=$year."-06-30";
			//crea una vista con los nombres de los archivos distintos para el periodo
			$nombre_vista_para_estructurado_inconsistencias_2="vestincon2pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_2="";
			$sql_vista_inconsistencias_2.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_2 ";
			$sql_vista_inconsistencias_2.=" AS SELECT DISTINCT nombre_archivo_datos_originales ";
			$sql_vista_inconsistencias_2.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 ";
			$sql_vista_inconsistencias_2.="  WHERE  ";
			$sql_vista_inconsistencias_2.=" (fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_2.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_2, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_2)."');</script>";
			}
			//fin crea vista
			
			//crea una vista uniendo la vista de nombres distintos con su mayor numero de secuencia
			$nombre_vista_para_estructurado_inconsistencias_3="vestincon3pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_3="";
			$sql_vista_inconsistencias_3.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_3 ";
			$sql_vista_inconsistencias_3.=" AS SELECT max(t1.numero_secuencia),t1.nombre_archivo_datos_originales as nombre_pyp ";
			$sql_vista_inconsistencias_3.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
			$sql_vista_inconsistencias_3.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_2 t2 ON (t1.nombre_archivo_datos_originales=t2.nombre_archivo_datos_originales)";
			$sql_vista_inconsistencias_3.="  WHERE  ";
			$sql_vista_inconsistencias_3.=" (t1.fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_3.=" GROUP BY nombre_pyp; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_3, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_3)."');</script>";
			}
			//fin vista			
			$numero_inconsistencias_mes="";
			if($clasificacion_inconsistencia=="1_ti")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_tipo_inconsitencia='$cod_tipo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="2_gi")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_grupo_inconsistencia='$cod_grupo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="3_di")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_detalle_inconsistencia='$cod_detalle_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
				
			}//fin if
			$valor_periodo_2=intval($numero_inconsistencias_mes);
			//fin periodo 2
			
			//periodo 3
			$valor_periodo_3=0;
			$year_mes_dia_ini=$year."-07-01";
			$year_mes_dia_fin=$year."-09-30";
			//crea una vista con los nombres de los archivos distintos para el periodo
			$nombre_vista_para_estructurado_inconsistencias_2="vestincon2pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_2="";
			$sql_vista_inconsistencias_2.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_2 ";
			$sql_vista_inconsistencias_2.=" AS SELECT DISTINCT nombre_archivo_datos_originales ";
			$sql_vista_inconsistencias_2.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 ";
			$sql_vista_inconsistencias_2.="  WHERE  ";
			$sql_vista_inconsistencias_2.=" (fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_2.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_2, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_2)."');</script>";
			}
			//fin crea vista
			
			//crea una vista uniendo la vista de nombres distintos con su mayor numero de secuencia
			$nombre_vista_para_estructurado_inconsistencias_3="vestincon3pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_3="";
			$sql_vista_inconsistencias_3.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_3 ";
			$sql_vista_inconsistencias_3.=" AS SELECT max(t1.numero_secuencia),t1.nombre_archivo_datos_originales as nombre_pyp ";
			$sql_vista_inconsistencias_3.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
			$sql_vista_inconsistencias_3.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_2 t2 ON (t1.nombre_archivo_datos_originales=t2.nombre_archivo_datos_originales)";
			$sql_vista_inconsistencias_3.="  WHERE  ";
			$sql_vista_inconsistencias_3.=" (t1.fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_3.=" GROUP BY nombre_pyp; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_3, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_3)."');</script>";
			}
			//fin vista			
			$numero_inconsistencias_mes="";
			if($clasificacion_inconsistencia=="1_ti")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_tipo_inconsitencia='$cod_tipo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="2_gi")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_grupo_inconsistencia='$cod_grupo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="3_di")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_detalle_inconsistencia='$cod_detalle_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
				
			}//fin if
			$valor_periodo_3=intval($numero_inconsistencias_mes);
			//fin periodo 3
			
			//periodo 4
			$valor_periodo_4=0;
			$year_mes_dia_ini=$year."-10-01";
			$year_mes_dia_fin=$year."-12-31";
			//crea una vista con los nombres de los archivos distintos para el periodo
			$nombre_vista_para_estructurado_inconsistencias_2="vestincon2pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_2="";
			$sql_vista_inconsistencias_2.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_2 ";
			$sql_vista_inconsistencias_2.=" AS SELECT DISTINCT nombre_archivo_datos_originales ";
			$sql_vista_inconsistencias_2.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 ";
			$sql_vista_inconsistencias_2.="  WHERE  ";
			$sql_vista_inconsistencias_2.=" (fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_2.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_2, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_2)."');</script>";
			}
			//fin crea vista
			
			//crea una vista uniendo la vista de nombres distintos con su mayor numero de secuencia
			$nombre_vista_para_estructurado_inconsistencias_3="vestincon3pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_3="";
			$sql_vista_inconsistencias_3.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_3 ";
			$sql_vista_inconsistencias_3.=" AS SELECT max(t1.numero_secuencia),t1.nombre_archivo_datos_originales as nombre_pyp ";
			$sql_vista_inconsistencias_3.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
			$sql_vista_inconsistencias_3.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_2 t2 ON (t1.nombre_archivo_datos_originales=t2.nombre_archivo_datos_originales)";
			$sql_vista_inconsistencias_3.="  WHERE  ";
			$sql_vista_inconsistencias_3.=" (t1.fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_3.=" GROUP BY nombre_pyp; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_3, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_3)."');</script>";
			}
			//fin vista			
			$numero_inconsistencias_mes="";
			if($clasificacion_inconsistencia=="1_ti")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_tipo_inconsitencia='$cod_tipo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="2_gi")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_grupo_inconsistencia='$cod_grupo_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
			}//fin if
			if($clasificacion_inconsistencia=="3_di")
			{
				$sql_consulta_sumatoria="";
				$sql_consulta_sumatoria.=" SELECT count(*) as numero_inconsistencias FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
				$sql_consulta_sumatoria.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max) WHERE ";
				$sql_consulta_sumatoria.=" t1.cod_detalle_inconsistencia='$cod_detalle_inconsistencia' ;";
				
				$resultados_consulta_sumatoria=array();
				$resultados_consulta_sumatoria=$coneccionBD->consultar2($sql_consulta_sumatoria);
				$numero_inconsistencias_mes=$resultados_consulta_sumatoria[0]["numero_inconsistencias"];
				$linea_estado_informacion.=$numero_inconsistencias_mes.",";//
				
			}//fin if
			$valor_periodo_4=intval($numero_inconsistencias_mes);
			//fin periodo 4
			
			
			
			$total_inconsistencias_year=0;
			$total_inconsistencias_year=$valor_periodo_1+$valor_periodo_2+$valor_periodo_3+$valor_periodo_4;
			$linea_estado_informacion.=$total_inconsistencias_year.",";//
			
			//porcentaje de participacion
			
			$year_mes_dia_ini=$year."-01-01";
			$year_mes_dia_fin=$year."-12-31";
			//crea una vista con los nombres de los archivos distintos para el periodo
			$nombre_vista_para_estructurado_inconsistencias_2="vestincon2pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_2="";
			$sql_vista_inconsistencias_2.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_2 ";
			$sql_vista_inconsistencias_2.=" AS SELECT DISTINCT nombre_archivo_datos_originales ";
			$sql_vista_inconsistencias_2.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 ";
			$sql_vista_inconsistencias_2.="  WHERE  ";
			$sql_vista_inconsistencias_2.=" (fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_2.=" ; ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_2, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_2)."');</script>";
			}
			//fin crea vista
			
			//crea una vista uniendo la vista de nombres distintos con su diciembrer numero de secuencia
			$nombre_vista_para_estructurado_inconsistencias_3="vestincon3pyp_".$nick_user."_".$tipo_id."_".$identificacion;
			$sql_vista_inconsistencias_3="";
			$sql_vista_inconsistencias_3.="CREATE OR REPLACE VIEW $nombre_vista_para_estructurado_inconsistencias_3 ";
			$sql_vista_inconsistencias_3.=" AS SELECT max(t1.numero_secuencia),t1.nombre_archivo_datos_originales as nombre_pyp ";
			$sql_vista_inconsistencias_3.=" FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
			$sql_vista_inconsistencias_3.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_2 t2 ON (t1.nombre_archivo_datos_originales=t2.nombre_archivo_datos_originales)";
			$sql_vista_inconsistencias_3.="  WHERE  ";
			$sql_vista_inconsistencias_3.=" (t1.fecha_corte_periodo BETWEEN '".$year_mes_dia_ini."' AND '".$year_mes_dia_fin."' ) ";	
			$sql_vista_inconsistencias_3.=" GROUP BY nombre_pyp ";
			$error_bd_seq="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_inconsistencias_3, $error_bd_seq);
			if($error_bd_seq!="")
			{
				//echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_inconsistencias_3)."');</script>";
			}
			//fin vista
			
			$porcentaje_de_participacion=0;
			
			$sql_consulta_sumatoria_year_todas_las_inconsistencias="";
			$sql_consulta_sumatoria_year_todas_las_inconsistencias.=" SELECT count(*) as numero_inconsistencias_all FROM $nombre_vista_para_estructurado_inconsistencias_1 t1 ";
			$sql_consulta_sumatoria_year_todas_las_inconsistencias.=" INNER JOIN $nombre_vista_para_estructurado_inconsistencias_3 t3 ON (t1.numero_secuencia=t3.max)  ";
			$sql_consulta_sumatoria_year_todas_las_inconsistencias.=" ; ";
			
			$resultados_consulta_sumatoria_all=array();
			$resultados_consulta_sumatoria_all=$coneccionBD->consultar2($sql_consulta_sumatoria_year_todas_las_inconsistencias);
			$numero_inconsistencias_all=0;
			$numero_inconsistencias_all=intval($resultados_consulta_sumatoria_all[0]["numero_inconsistencias_all"]);
			if($numero_inconsistencias_all>0)
			{
				$porcentaje_de_participacion=round((intval($total_inconsistencias_year)/$numero_inconsistencias_all)*100,1,PHP_ROUND_HALF_UP);
			}
			$linea_estado_informacion.=$porcentaje_de_participacion;//
			
			//fin porcentaje de participacion
			
			if(intval($total_inconsistencias_year)>0)
			{
				fwrite($file_estructurado_inconsistencias, $linea_estado_informacion."\n");
				
				$html_nueva_ventana.="<tr>";
				$array_linea_estado_informacion=explode(",",$linea_estado_informacion);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_estado_informacion as $columna_estado_informacion)
				{
					$html_nueva_ventana.="<td>$columna_estado_informacion</td>";				
					$cont_columnas_estado_informacion++;
				}
				$html_nueva_ventana.="</tr>";
			}//fin if solo muestra si el acumulado final es diferente de cero
			
			$porcentaje_actual=0;
			$contador_clasificacion_inconsistencias=count($resultados_clasificacion_inconsistencias);
			if($contador_clasificacion_inconsistencias>0)
			{
				$porcentaje_actual=round(($cont_linea/$contador_clasificacion_inconsistencias)*100,1,PHP_ROUND_HALF_UP);
			}
			echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $porcentaje_actual % registros recuperados del 100% .';</script>";
			ob_flush();
			flush();
			$cont_linea++;
		
		}//fin foreach
		fclose($file_estructurado_inconsistencias);
		
		echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, 100 % registros recuperados del 100% .';</script>";
		ob_flush();
		flush();
		
		$html_abrir_ventana="";
		$html_abrir_ventana.="<script>";
		$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_consolidado_inconsistencias', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
		$html_abrir_ventana.="</script>";
		echo $html_abrir_ventana;
		ob_flush();
		flush();
		
		//fin tabla ventana
		$html_nueva_ventana.="</table>";
		
		$html_nueva_ventana.="</body>";
		$html_nueva_ventana.="</html>";
	
		$insertar_html_nueva_ventana="";
		$insertar_html_nueva_ventana.="<script>ventana_detalle.document.write(\"$html_nueva_ventana\");</script>";
		echo $insertar_html_nueva_ventana;
		ob_flush();
		flush();		
		
	}
	
	if($hubo_resultados)
	{
		//borrando vistas
		
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_estructurado_inconsistencias_3 ; ";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_estructurado_inconsistencias_2 ; ";
		$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_estructurado_inconsistencias_1 ; ";
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			echo "<script>alert('error al borrar vista(s) ".procesar_mensaje($error_bd)."');</script>";
		}
		
		//fin borrando vistas
	
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
		
		$boton_ventana=" <input type=\'button\' value=\'Ver la ventana de estructurado inconsistencias PyP\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el reporte de de estructurado inconsistencias PyP\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_estructurado_inconsistencias\');\"/> ";
	
		echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
		
	}
	else
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se hallaron datos en el periodo especificado para generar el reporte.';</script>";
	}
	
	
	
}//fin isset year y periodo

//FIN PARTE BUSQUEDA

?>