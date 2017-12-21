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
$selector_periodo.="<option value='none'>Seleccione un Mes</option>\n";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["cod_periodo"];
	$nombre_periodo=$periodo["nombre_periodo"];
	$fecha_de_corte=$periodo["fecha_corte"];
	$selector_periodo.="<option value='$fecha_de_corte'>$nombre_periodo</option>\n";
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

//tipo consulta selector
$selector_tipo_actividad_consulta="";
$selector_tipo_actividad_consulta.="<select id='selector_grupo_procedimiento' name='selector_grupo_procedimiento' class='campo_azul' >";
$selector_tipo_actividad_consulta.="<option value='none'>Seleccione el grupo de procedimiento</option>";
$query_tipo_consulta="";
$query_tipo_consulta.="SELECT * FROM gioss_grupo_procedimientos_cups;";
$res_tipo_consulta_query=$coneccionBD->consultar2($query_tipo_consulta);

if(count($res_tipo_consulta_query)>0)
{
	foreach($res_tipo_consulta_query as $tipo_consulta_un_res)
	{
		$selector_tipo_actividad_consulta.="<option value='".$tipo_consulta_un_res['codigo_grupo_cups']."'>".$tipo_consulta_un_res['descripcion_grupos_de_cups']."</option>";
	}
}

$selector_tipo_actividad_consulta.="</select>";
//tipo consulta selector

//grupo sistema
$selector_grupo_sistema="";
$selector_grupo_sistema.="<select id='selector_grupo_sistemas' name='selector_grupo_sistemas' class='campo_azul' >";
$selector_grupo_sistema.="<option value='none'>Seleccione El Sistema</option>";
$query_especialidad="";
$query_especialidad.="SELECT * FROM gioss_grupos_sistemas_cups;";
$res_especialidad_query=$coneccionBD->consultar2($query_especialidad);

if(count($res_especialidad_query)>0)
{
	foreach($res_especialidad_query as $tipo_especialidad)
	{
		$selector_grupo_sistema.="<option value='".$tipo_especialidad['cod_sistemas_cups']."'>".$tipo_especialidad['descripcion_sistemas_cups']."</option>";
	}
}

$selector_grupo_sistema.="</select>";
//fin grupo sistema

//SELECTOR MUNICIPIO
$selector_municipio="";
$selector_municipio.="<div id='mpio_div'>";
$selector_municipio.="<select id='mpio' name='mpio' class='campo_azul' >";
$selector_municipio.="<option value='none'>Seleccione un municipio</option>";
$selector_municipio.="</select>";
$selector_municipio.="</div>";

$smarty->assign("campo_grupo_sistemas", $selector_grupo_sistema, true);
$smarty->assign("campo_grupo_procedimiento", $selector_tipo_actividad_consulta, true);

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);

$smarty->assign("campo_dpto", $selector_departamento, true);
$smarty->assign("campo_mpio", $selector_municipio, true);
$smarty->assign("campo_fechas_corte", $selector_periodo, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('actividades_RIPS3374.html.tpl');

echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros_vista=0;
$contador_offset=0;
$hubo_resultados=false;

if(isset($_POST["year_de_validacion"]) && isset($_POST["periodo"]) && isset($_POST["eapb"]) && isset($_POST["sexo"])
   && $_POST["year_de_validacion"]!="" && $_POST["periodo"]!="none" && $_POST["eapb"]!="none" && $_POST["sexo"]!="none"
   && isset($_POST["criterio_busqueda"])
   && (
       ($_POST["criterio_busqueda"]=="cb_1_grupo_procedimiento" && isset($_POST["selector_grupo_procedimiento"]) )
       ||
       ($_POST["criterio_busqueda"]=="cb_2_grupo_sistema" && isset($_POST["selector_grupo_sistemas"]))
       )
   )
{
	$criterio_busqueda_grupo_proc_o_grupo_sistema=$_POST["criterio_busqueda"];
	//echo "<script>alert('Criterio de busqueda: ".$criterio_busqueda_grupo_proc_o_grupo_sistema."');</script>";
	
	$grupo_procedimiento="";
	$grupo_sistema="";
	if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento")
	{
		$grupo_procedimiento=$_POST["selector_grupo_procedimiento"];
	}
	if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema")
	{
		$grupo_sistema=$_POST["selector_grupo_sistemas"];
	}
	
	
	$cod_eapb=$_POST["eapb"];
	$year=$_POST["year_de_validacion"];
	$periodo=$_POST["periodo"];
	$fecha_inicio=$year."-01-01";
	$fecha_fin=$year."-12-31";
	if($periodo!="none")
	{
		$mes=explode("/",$periodo)[1];
		$dia_fin=explode("/",$periodo)[0];
		$fecha_inicio=$year."-".$mes."-01";
		$fecha_fin=$year."-".$mes."-".$dia_fin;
	}
	
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
	
	$cod_sexo="";
	if(isset($_POST["sexo"]) && $_POST["sexo"]!="none")
	{
		$cod_sexo=$_POST["sexo"];
	}
	
	$nombre_vista_para_codigo_actividad="vcoprocr_".$nick_user."_".$tipo_id."_".$identificacion;
	
	$sql_vista_procedmientos="";
	$sql_vista_procedmientos.="CREATE OR REPLACE VIEW $nombre_vista_para_codigo_actividad ";
	$sql_vista_procedmientos.=" AS SELECT DISTINCT ap.codigo_cups_procedimiento ";
	$sql_vista_procedmientos.=" FROM gioss_archivo_cargado_ap ap ";
	$sql_vista_procedmientos.=" INNER JOIN gioss_tabla_estado_informacion_rips ei ON (ap.numero_secuencia=ei.numero_secuencia)";
	$sql_vista_procedmientos.=" INNER JOIN gioss_cups tcp ON (ap.codigo_cups_procedimiento=tcp.codigo_procedimiento)";
	$sql_vista_procedmientos.=" WHERE  ";
	$sql_vista_procedmientos.=" (ap.fecha_validacion_exito BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	$sql_vista_procedmientos.=" AND ";
	$sql_vista_procedmientos.=" ap.codigo_eapb='$cod_eapb' ";
	if($cod_sexo!="" && $cod_sexo!="A")
	{
		$sql_vista_procedmientos.=" AND ";
		$sql_vista_procedmientos.=" ap.sexo_afiliado='$cod_sexo' ";
	}
	if($codigo_departamento!="")
	{
		$sql_vista_procedmientos.=" AND ";
		$sql_vista_procedmientos.=" ei.codigo_departamento='$codigo_departamento' ";
	}
	if($codigo_departamento!="" && $codigo_municipio!="")
	{
		$sql_vista_procedmientos.=" AND ";
		$sql_vista_procedmientos.=" ei.codigo_municipio='$codigo_municipio' ";
	}
	if($cod_prestador!="")
	{
		$sql_vista_procedmientos.=" AND ";
		$sql_vista_procedmientos.=" ap.codigo_prestador_servicios_salud='$cod_prestador' ";
	}
	
	if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento" && $grupo_procedimiento!="none")
	{
		$sql_vista_procedmientos.=" AND ";
		$sql_vista_procedmientos.=" tcp.codigo_grupo_cups='$grupo_procedimiento' ";
	}
	if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema" && $grupo_sistema!="none")
	{
		$sql_vista_procedmientos.=" AND ";
		$sql_vista_procedmientos.=" tcp.codigo_sistema_cups='$grupo_sistema' ";
	}
	
	$sql_vista_procedmientos.=" ORDER BY ap.codigo_cups_procedimiento; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_procedmientos, $error_bd_seq);
	if($error_bd_seq!="")
	{
		echo "<script>alert('error al crear vista(s) ".procesar_mensaje($error_bd_seq)." ".procesar_mensaje($sql_vista_procedmientos)."');</script>";
	}
	else
	{
		//echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
		//echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML=\"$sql_vista_procedmientos\";</script>";
	}
	
	//echo "<script>alert(' ".procesar_mensaje($sql_vista_procedmientos)."');</script>";
	
	$numero_registros_vista=0;
	$sql_numero_registros_vista="";
	$sql_numero_registros_vista.="SELECT count(*) as contador FROM $nombre_vista_para_codigo_actividad  ";	
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
		$sql_query_busqueda.="SELECT * FROM $nombre_vista_para_codigo_actividad LIMIT $limite OFFSET $contador_offset;  ";
		$resultado_para_edad_simple_y_sexo=$coneccionBD->consultar2($sql_query_busqueda);
	
		if(count($resultado_para_edad_simple_y_sexo)>0 && is_array($resultado_para_edad_simple_y_sexo))
		{
			
			$nombre_archivo_edad_simple_y_sexo=$cod_eapb."_edad_simple_y_sexo_rips.csv";
			$ruta_archivo_calidad_de_datos=$rutaTemporal.$nombre_archivo_edad_simple_y_sexo;
			
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
				$html_nueva_ventana.="<title>Reporte de Edad Simple Y Sexo</title>";
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
				
				$html_encabezado.="<td>Sexo seleccionado:</td>";
				$sexo_seleccionado="";
				if($cod_sexo!="")
				{
					$sexo_seleccionado=$cod_sexo;
					$html_encabezado.="<td>$sexo_seleccionado</td>";
				}
				else
				{
					$html_encabezado.="<td>Todos</td>";
					$sexo_seleccionado="Todos";
				}
				
				$html_encabezado.="</tr>";
				$html_encabezado.="</table>";
				$html_nueva_ventana.=$html_encabezado;
				
				$linea_encabezado="Departamento,$depto_seleccionado,Municipio,$mpio_seleccionado,Prestador,$prestador_seleccionado,Year,$year_seleccionado,Periodo,$periodo,Sexo,$cod_sexo";
				
				fwrite($file_calidad_de_datos, $linea_encabezado."\n");
				//encabezado
				
				$titulos="";
				
				
				$titulos.="Codigo Grupo,";
				$titulos.="Descripcion de la Agrupacion,";
				$titulos.="Codigo Actividad,";
				$titulos.="Descripcion de la Actividad,";
				$cont_edad_titulo=0;
				while($cont_edad_titulo<=120)
				{
					$titulos.=$cont_edad_titulo.",";
					$cont_edad_titulo++;
				}
				$titulos.="Sin Dato,";
				$titulos.="Total";
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
			
			$cont_para_verificar_fin_array=0;
			foreach($resultado_para_edad_simple_y_sexo as $resultado)
			{
				$linea_edad_simple_y_sexo="";
				
				if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento")
				{
					$string_descripcion_grupo_procedimiento="";
					$string_codigo_grupo="";
					$query_descripcion_tipo_consulta="";
					$query_descripcion_tipo_consulta.=" SELECT * FROM gioss_cups ccr ";
					$query_descripcion_tipo_consulta.=" INNER JOIN gioss_grupo_procedimientos_cups ggpc ON (ccr.codigo_grupo_cups=ggpc.codigo_grupo_cups) ";
					$query_descripcion_tipo_consulta.=" WHERE codigo_procedimiento='".$resultado["codigo_cups_procedimiento"]."'; ";
					$res_descripcion_tipo_consulta=$coneccionBD->consultar2($query_descripcion_tipo_consulta);
					if(is_array($res_descripcion_tipo_consulta))
					{
						$string_codigo_grupo=$res_descripcion_tipo_consulta[0]["codigo_grupo_cups"];
						$string_descripcion_grupo_procedimiento=$res_descripcion_tipo_consulta[0]["descripcion_grupos_de_cups"];
					}
					$linea_edad_simple_y_sexo.=$string_codigo_grupo.",";
					$linea_edad_simple_y_sexo.=$string_descripcion_grupo_procedimiento.",";
				}
				if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema")
				{					
					$string_descripcion_grupo_sistema="";
					$string_codigo_sistemas="";
					$query_descripcion_especialidad="";
					$query_descripcion_especialidad.=" SELECT * FROM gioss_cups ccr ";
					$query_descripcion_especialidad.=" INNER JOIN gioss_grupos_sistemas_cups ggsc ON (ccr.codigo_sistema_cups=ggsc.cod_sistemas_cups) ";
					$query_descripcion_especialidad.=" WHERE codigo_procedimiento='".$resultado["codigo_cups_procedimiento"]."'; ";
					$res_descripcion_especialidad=$coneccionBD->consultar2($query_descripcion_especialidad);
					if(is_array($res_descripcion_especialidad))
					{
						$string_codigo_sistemas=$res_descripcion_tipo_consulta[0]["codigo_grupo_cups"];
						$string_descripcion_grupo_sistema=$res_descripcion_especialidad[0]["cod_sistemas_cups"];
					}
					$linea_edad_simple_y_sexo.=$string_codigo_sistemas.",";
					$linea_edad_simple_y_sexo.=$string_descripcion_grupo_sistema.",";
				}
				
				$linea_edad_simple_y_sexo.=$resultado["codigo_cups_procedimiento"].",";
				
				$string_descripcion_actividad="";
				$query_descripcion_actividad="";
				$query_descripcion_actividad.=" SELECT * FROM gioss_cups WHERE codigo_procedimiento='".$resultado["codigo_cups_procedimiento"]."';";
				$res_descripcion_actividad=$coneccionBD->consultar2($query_descripcion_actividad);
				if(is_array($res_descripcion_actividad))
				{
					$string_descripcion_actividad=$res_descripcion_actividad[0]["descripcion_procedimiento"];
				}
				$linea_edad_simple_y_sexo.=strtolower($string_descripcion_actividad).",";
				
				//consultas edades
				$cont_edad_linea=0;
				$array_resultado_codigo_edades=array();
				while($cont_edad_linea<=120)
				{
					$numero_coincidencias_edad_actividad=0;
					
					$sql_consulta_contador_edad="";
					$sql_consulta_contador_edad.=" SELECT count(*) AS numero_procedimientos_para_edad ";
					$sql_consulta_contador_edad.=" FROM gioss_archivo_cargado_ap ap ";
					$sql_consulta_contador_edad.=" INNER JOIN gioss_tabla_estado_informacion_rips ei ON (ap.numero_secuencia=ei.numero_secuencia)";
					$sql_consulta_contador_edad.=" INNER JOIN gioss_cups tcp ON (ap.codigo_cups_procedimiento=tcp.codigo_procedimiento)";
					$sql_consulta_contador_edad.=" WHERE  ";
					$sql_consulta_contador_edad.=" (ap.fecha_validacion_exito BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ap.codigo_eapb='$cod_eapb' ";
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ap.codigo_cups_procedimiento='".$resultado["codigo_cups_procedimiento"]."' ";
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ap.edad_years_afiliado='$cont_edad_linea' ";
					if($cod_sexo!="" && $cod_sexo!="A")
					{
						$sql_consulta_contador_edad.=" AND ";
						$sql_consulta_contador_edad.=" ap.sexo_afiliado='$cod_sexo' ";
					}
					if($codigo_departamento!="")
					{
						$sql_consulta_contador_edad.=" AND ";
						$sql_consulta_contador_edad.=" ei.codigo_departamento='$codigo_departamento' ";
					}
					if($codigo_departamento!="" && $codigo_municipio!="")
					{
						$sql_consulta_contador_edad.=" AND ";
						$sql_consulta_contador_edad.=" ei.codigo_municipio='$codigo_municipio' ";
					}
					if($cod_prestador!="")
					{
						$sql_consulta_contador_edad.=" AND ";
						$sql_consulta_contador_edad.=" ap.codigo_prestador_servicios_salud='$cod_prestador' ";
					}
					if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento" && $grupo_procedimiento!="none")
					{
						$sql_consulta_contador_edad.=" AND ";
						$sql_consulta_contador_edad.=" tcp.codigo_grupo_cups='$grupo_procedimiento' ";
					}
					if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema" && $grupo_sistema!="none")
					{
						$sql_consulta_contador_edad.=" AND ";
						$sql_consulta_contador_edad.=" tcp.codigo_sistema_cups='$grupo_sistema' ";
					}
					$sql_consulta_contador_edad.=" ; ";
					$res_consulta_contador_edad=$coneccionBD->consultar2($sql_consulta_contador_edad);
					if(is_array($res_consulta_contador_edad))
					{
						$numero_coincidencias_edad_actividad=intval($res_consulta_contador_edad[0]["numero_procedimientos_para_edad"]);
						$array_resultado_codigo_edades[$cont_edad_linea]=$numero_coincidencias_edad_actividad;
					}
					
					
					$linea_edad_simple_y_sexo.=$numero_coincidencias_edad_actividad.",";
					$cont_edad_linea++;
				}//fin consultas edades
				
				$numero_coincidencias_edad_actividad_sin_dato=0;
					
				$sql_consulta_contador_edad="";
				$sql_consulta_contador_edad.=" SELECT count(*) AS numero_procedimientos_para_edad ";
				$sql_consulta_contador_edad.=" FROM gioss_archivo_cargado_ap ap ";
				$sql_consulta_contador_edad.=" INNER JOIN gioss_tabla_estado_informacion_rips ei ON (ap.numero_secuencia=ei.numero_secuencia)";
				$sql_consulta_contador_edad.=" INNER JOIN gioss_cups tcp ON (ap.codigo_cups_procedimiento=tcp.codigo_procedimiento)";
				$sql_consulta_contador_edad.=" WHERE  ";
				$sql_consulta_contador_edad.=" (ap.fecha_validacion_exito BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
				$sql_consulta_contador_edad.=" AND ";
				$sql_consulta_contador_edad.=" ap.codigo_eapb='$cod_eapb' ";
				$sql_consulta_contador_edad.=" AND ";
				$sql_consulta_contador_edad.=" ap.codigo_cups_procedimiento='".$resultado["codigo_cups_procedimiento"]."' ";
				$sql_consulta_contador_edad.=" AND ";
				$sql_consulta_contador_edad.=" ap.edad_years_afiliado='999' ";
				if($cod_sexo!="" && $cod_sexo!="A")
				{
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ap.sexo_afiliado='$cod_sexo' ";
				}
				if($codigo_departamento!="")
				{
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ei.codigo_departamento='$codigo_departamento' ";
				}
				if($codigo_departamento!="" && $codigo_municipio!="")
				{
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ei.codigo_municipio='$codigo_municipio' ";
				}
				if($cod_prestador!="")
				{
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" ap.codigo_prestador_servicios_salud='$cod_prestador' ";
				}
				if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento" && $grupo_procedimiento!="none")
				{
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" tcp.codigo_grupo_cups='$grupo_procedimiento' ";
				}
				if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema" && $grupo_sistema!="none")
				{
					$sql_consulta_contador_edad.=" AND ";
					$sql_consulta_contador_edad.=" tcp.codigo_sistema_cups='$grupo_sistema' ";
				}
				$sql_consulta_contador_edad.=" ; ";
				$res_consulta_contador_edad=$coneccionBD->consultar2($sql_consulta_contador_edad);
				if(is_array($res_consulta_contador_edad))
				{
					$numero_coincidencias_edad_actividad_sin_dato=intval($res_consulta_contador_edad[0]["numero_procedimientos_para_edad"]);
					$array_resultado_codigo_edades[999]=$numero_coincidencias_edad_actividad_sin_dato;
				}
				
				
				$linea_edad_simple_y_sexo.=$numero_coincidencias_edad_actividad_sin_dato.",";
				
				$total_por_codigo=0;
				foreach($array_resultado_codigo_edades as $resultado_codigo_edades)
				{
					$total_por_codigo+=$resultado_codigo_edades;
				}
				$linea_edad_simple_y_sexo.=$total_por_codigo;
				
				
				//ESCRIBE LA LINEA 
				fwrite($file_calidad_de_datos, $linea_edad_simple_y_sexo."\n");
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros_vista.';</script>";
				ob_flush();
				flush();
				
				$html_nueva_ventana.="<tr>";
				$array_linea_estado_informacion=explode(",",$linea_edad_simple_y_sexo);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_estado_informacion as $columna_estado_informacion)
				{
					$html_nueva_ventana.="<td>$columna_estado_informacion</td>";					
					$cont_columnas_estado_informacion++;
				}
				$html_nueva_ventana.="</tr>";
				
				//parte para calculo total edades HORIZONTAL
				if($cont_para_verificar_fin_array==count($resultado_para_edad_simple_y_sexo)-1)
				{
					$linea_final_horizontal="";
					$linea_final_horizontal.="Linea Final,Total por Edad,----,Aqui se mostraran las sumatorias para cada columna por edad,";
					
					
					//consultas edades
					$cont_edad_linea_total=0;
					$array_resultado_codigo_edades_total=array();
					while($cont_edad_linea_total<=120)
					{
						$numero_coincidencias_edad_total=0;
					
						$sql_consulta_contador_total_edad="";
						$sql_consulta_contador_total_edad.=" SELECT count(*) AS numero_procedimientos_para_edad ";
						$sql_consulta_contador_total_edad.=" FROM gioss_archivo_cargado_ap ap ";
						$sql_consulta_contador_total_edad.=" INNER JOIN gioss_tabla_estado_informacion_rips ei ON (ap.numero_secuencia=ei.numero_secuencia)";
						$sql_consulta_contador_total_edad.=" INNER JOIN gioss_cups tcp ON (ap.codigo_cups_procedimiento=tcp.codigo_procedimiento)";
						$sql_consulta_contador_total_edad.=" WHERE  ";
						$sql_consulta_contador_total_edad.=" (ap.fecha_validacion_exito BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" ap.codigo_eapb='$cod_eapb' ";
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" ap.edad_years_afiliado='$cont_edad_linea_total' ";
						if($cod_sexo!="" && $cod_sexo!="A")
						{
							$sql_consulta_contador_total_edad.=" AND ";
							$sql_consulta_contador_total_edad.=" ap.sexo_afiliado='$cod_sexo' ";
						}
						if($codigo_departamento!="")
						{
							$sql_consulta_contador_total_edad.=" AND ";
							$sql_consulta_contador_total_edad.=" ei.codigo_departamento='$codigo_departamento' ";
						}
						if($codigo_departamento!="" && $codigo_municipio!="")
						{
							$sql_consulta_contador_total_edad.=" AND ";
							$sql_consulta_contador_total_edad.=" ei.codigo_municipio='$codigo_municipio' ";
						}
						if($cod_prestador!="")
						{
							$sql_consulta_contador_total_edad.=" AND ";
							$sql_consulta_contador_total_edad.=" ap.codigo_prestador_servicios_salud='$cod_prestador' ";
						}
						if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento" && $grupo_procedimiento!="none")
						{
							$sql_consulta_contador_total_edad.=" AND ";
							$sql_consulta_contador_total_edad.=" tcp.codigo_grupo_cups='$grupo_procedimiento' ";
						}
						if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema" && $grupo_sistema!="none")
						{
							$sql_consulta_contador_total_edad.=" AND ";
							$sql_consulta_contador_total_edad.=" tcp.codigo_sistema_cups='$grupo_sistema' ";
						}
						$sql_consulta_contador_total_edad.=" ; ";
						$res_consulta_contador_edad_total=$coneccionBD->consultar2($sql_consulta_contador_total_edad);
						if(is_array($res_consulta_contador_edad_total))
						{
							$numero_coincidencias_edad_total=intval($res_consulta_contador_edad_total[0]["numero_procedimientos_para_edad"]);
							$array_resultado_codigo_edades_total[$cont_edad_linea_total]=$numero_coincidencias_edad_total;
						}
						
						$linea_final_horizontal.=$numero_coincidencias_edad_total.",";
						
						//contador ciclo
						$cont_edad_linea_total++;
					}//fin while
					
					$coincidencias_sin_dato_total=0;
					
					$sql_consulta_contador_total_edad="";
					$sql_consulta_contador_total_edad.=" SELECT count(*) AS numero_procedimientos_para_edad ";
					$sql_consulta_contador_total_edad.=" FROM gioss_archivo_cargado_ap ap ";
					$sql_consulta_contador_total_edad.=" INNER JOIN gioss_tabla_estado_informacion_rips ei ON (ap.numero_secuencia=ei.numero_secuencia)";
					$sql_consulta_contador_total_edad.=" INNER JOIN gioss_cups tcp ON (ap.codigo_cups_procedimiento=tcp.codigo_procedimiento)";
					$sql_consulta_contador_total_edad.=" WHERE  ";
					$sql_consulta_contador_total_edad.=" (ap.fecha_validacion_exito BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
					$sql_consulta_contador_total_edad.=" AND ";
					$sql_consulta_contador_total_edad.=" ap.codigo_eapb='$cod_eapb' ";
					$sql_consulta_contador_total_edad.=" AND ";
					$sql_consulta_contador_total_edad.=" ap.edad_years_afiliado='999' ";
					if($cod_sexo!="" && $cod_sexo!="A")
					{
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" ap.sexo_afiliado='$cod_sexo' ";
					}
					if($codigo_departamento!="")
					{
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" ei.codigo_departamento='$codigo_departamento' ";
					}
					if($codigo_departamento!="" && $codigo_municipio!="")
					{
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" ei.codigo_municipio='$codigo_municipio' ";
					}
					if($cod_prestador!="")
					{
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" ap.codigo_prestador_servicios_salud='$cod_prestador' ";
					}
					if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_1_grupo_procedimiento" && $grupo_procedimiento!="none")
					{
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" tcp.codigo_grupo_cups='$grupo_procedimiento' ";
					}
					if($criterio_busqueda_grupo_proc_o_grupo_sistema=="cb_2_grupo_sistema" && $grupo_sistema!="none")
					{
						$sql_consulta_contador_total_edad.=" AND ";
						$sql_consulta_contador_total_edad.=" tcp.codigo_sistema_cups='$grupo_sistema' ";
					}
					$sql_consulta_contador_total_edad.=" ; ";
					$res_consulta_contador_edad_total=$coneccionBD->consultar2($sql_consulta_contador_total_edad);
					if(is_array($res_consulta_contador_edad_total))
					{
						$coincidencias_sin_dato_total=intval($res_consulta_contador_edad_total[0]["numero_procedimientos_para_edad"]);
						$array_resultado_codigo_edades_total[999]=$coincidencias_sin_dato_total;
					}
					
					$linea_final_horizontal.=$coincidencias_sin_dato_total.",";
					
					$total_total=0;
					foreach($array_resultado_codigo_edades_total as $resultado_codigo_edades_total)
					{
						$total_total+=$resultado_codigo_edades_total;
					}
					$linea_final_horizontal.=$total_total;
					
					//escribe la linea final horizontal
					fwrite($file_calidad_de_datos, $linea_final_horizontal);
					
					$html_nueva_ventana.="<tr>";
					$array_linea_final_horizontal=explode(",",$linea_final_horizontal);
					$cont_columnas_linea_final_horizontal=0;
					foreach($array_linea_final_horizontal as $columna_final_horizontal)
					{
						$html_nueva_ventana.="<td class='resultados' >$columna_final_horizontal</td>";					
						$cont_columnas_linea_final_horizontal++;
					}
					$html_nueva_ventana.="</tr>";
				}
				//fin parte calculo total edades HORIZONTAL
				
				
				$cont_para_verificar_fin_array++;
				
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
		
		$boton_ventana=" <input type=\'button\' value=\'Ver la ventana de edad simple y sexo para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el reporte de edad simple y sexo para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_calidad_de_datos\');\"/> ";
	
		echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
		
	}
	else
	{
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se hallaron datos en el periodo especificado para generar el reporte.';</script>";
	}
	
	
	//borrando vistas
	$sql_borrar_vistas="";
	$sql_borrar_vistas.=" DROP VIEW $nombre_vista_para_codigo_actividad ; ";
	
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