<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

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


//SELECTOR PRESTADORES ASOCIADOS USUARIO
$prestador="";
$prestador.="<div id='div_prestador'>";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' >";
$prestador.="<option value='none'>Seleccione un prestador de salud</option>";
if(intval($perfil_usuario_actual)==5 && (intval($tipo_entidad)!=6 && intval($tipo_entidad)!=7 && intval($tipo_entidad)!=8 && intval($tipo_entidad)!=10) )
{
	$sql_consulta_prestadores_asociados_eapb="SELECT ea.codigo_entidad,ea.nombre_de_la_entidad FROM ";
	$sql_consulta_prestadores_asociados_eapb.=" gioss_relacion_entre_entidades_salud re INNER JOIN gioss_entidades_sector_salud ea ON (re.entidad1 = ea.codigo_entidad) ";
	$sql_consulta_prestadores_asociados_eapb.=" WHERE re.entidad2='".$entidad_salud_usuario_actual."' ";
	$resultado_query_prestadores_asociados_eapb=$coneccionBD->consultar2($sql_consulta_prestadores_asociados_eapb);

	if(count($resultado_query_prestadores_asociados_eapb)>0)
	{
		foreach($resultado_query_prestadores_asociados_eapb as $prestador_asociado_eapb)
		{
			$prestador.="<option value='".$prestador_asociado_eapb['codigo_entidad']."'>".$prestador_asociado_eapb['nombre_de_la_entidad']."</option>";
		}
	}
}//si el tipo entidad es diferente de 6,7,8,10
else if((intval($perfil_usuario_actual)==1 || intval($perfil_usuario_actual)==2 || intval($perfil_usuario_actual)==5) && (intval($tipo_entidad)==6 || intval($tipo_entidad)==7 || intval($tipo_entidad)==8 || intval($tipo_entidad)==10) )
{
	$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
	$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);

	if(count($resultado_query_tipo_entidad)>0)
	{
		foreach($resultado_query_tipo_entidad as $eapb_entidad)
		{
			$prestador.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else if en caso de que el perfil sea 1 o 2 y el tipo de la entidad sea igual a 6,7,8,10
$prestador.="</select>";
$prestador.="</div>";
//FIN



//SELECTOR FECHAS MESES PERIODOS RIPS
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

$query_departamentos="select * from gios_dpto;";
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
$smarty->display('consprestador4505.html.tpl');

//echo "<script>consultar_prestador();</script>";

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$numero_registros=0;
$contador_offset=0;
$hubo_resultados=false;

$html_nueva_ventana="";

if(isset($_POST["year_de_corte"]) && isset($_POST["prestador"]) && $_POST["year_de_corte"]!="" && $_POST["prestador"]!="none")
{	
	//consulta estado el estado de la informacion	
	$prestador=$_POST["prestador"];
	
	//POR PERIODO
	$year=$_POST["year_de_corte"];
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
	
	
	$sql_numero_registros="";
	$sql_numero_registros.="SELECT count(*) as contador FROM gioss_tabla_estado_informacion_4505 WHERE codigo_prestador_servicios='$prestador'  ";		
	$sql_numero_registros.=" AND ";
	$sql_numero_registros.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	$sql_numero_registros.=" ; ";
	$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
	$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
					
	
	$sql_vista_estado_informacion="";
	$sql_vista_estado_informacion.="CREATE OR REPLACE VIEW veipyp_".$nick_user."_".$tipo_id."_".$identificacion." ";
	$sql_vista_estado_informacion.=" AS SELECT * FROM gioss_tabla_estado_informacion_4505 WHERE codigo_prestador_servicios='$prestador'  ";
	$sql_vista_estado_informacion.=" AND ";
	$sql_vista_estado_informacion.=" (fecha_corte_periodo BETWEEN '$fecha_inicio' AND '$fecha_fin' ) ";
	$sql_vista_estado_informacion.=" order by fecha_corte_periodo,numero_secuencia ; ";
	$error_bd_seq="";
	$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_estado_informacion, $error_bd_seq);
	//echo "<script>alert('se genero vista $numero_registros');</script>";
	
	$cont_linea=1;
	while($contador_offset<$numero_registros)
	{
		$limite=2000;
		
		if( ($contador_offset+2000)>=$numero_registros)
		{
			$limite=2000+($numero_registros-$contador_offset);
		}
	
		//Ejemplo: SELECT *  FROM vista_inconsistencias_rips WHERE nombre_archivo_ct='CT054037'  order by numero_linea, numero_campo limit 5 offset 0; 
		$sql_query_busqueda="";
		$sql_query_busqueda.="SELECT * FROM veipyp_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
		$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
	
		if(count($resultado_query_inconsistencias)>0)
		{
			$nombre_archivo_estado_informacion=$prestador."_estado_info_4505.csv";
			$ruta_archivo_estado_informacion=$rutaTemporal.$nombre_archivo_estado_informacion;
			
			//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			if($hubo_resultados==false)
			{
				$file_estado_informacion= fopen($ruta_archivo_estado_informacion, "w") or die("fallo la creacion del archivo");
				fclose($file_estado_informacion);
				
				$html_abrir_ventana="";
				$html_abrir_ventana.="<script>";
				$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_estado_info_prestador', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
				$html_abrir_ventana.="</script>";
				echo $html_abrir_ventana;
				
				$html_nueva_ventana="";
				$html_nueva_ventana.="<html>";
				
				$html_nueva_ventana.="<head>";
				$html_nueva_ventana.="<title>Estado Informacion</title>";
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
				
				$html_nueva_ventana.="<table id='tabla_ventana_estado_info' >";
				
				$file_estado_informacion= fopen($ruta_archivo_estado_informacion, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,codigo_estado_informacion,nombre_estado_informacion,fecha_validacion,periodo_reporte,descripcion_periodo_reporte,";
				$titulos.="fecha_corte_periodo,numero_secuencia,codigo_eapb,nombre_eapb,codigo_prestador_servicios,tipo_identificacion_prestador,";
				$titulos.="numero_identificacion_prestado,nombre_del_archivo,total_registros,codigo_departamento,nombre_del_departamento,codigo_municipio,nombre_de_municipio";
				*/
				$titulos="";
				$titulos.="N. Orden,Cod. Estado,Nombre Estado,Fech Validacion,Periodo de Reporte,Desc. Periodo de reporte,";
				$titulos.="Fecha de Corte Periodo,N. Secuencia,Cod. EAPB,Nombre EAPB,Cod. Prestador,Tipo ID. Prestador,";
				$titulos.="N. ID. Prestador,Nombre de archivo PyP,N. Total de Registros,Cod. Dept.,Nom. Dept.,Cod. Mpio.,Nom. Mpio.";
				fwrite($file_estado_informacion, $titulos."\n");
				
				$html_nueva_ventana.="<tr>";
				$array_titulos=explode(",",$titulos);
				$cont_titulos=0;
				foreach($array_titulos as $titulo_columna)
				{
					if($cont_titulos!=(count($array_titulos)-1)
					   && $cont_titulos!=(count($array_titulos)-2)
					   && $cont_titulos!=(count($array_titulos)-3)
					   && $cont_titulos!=(count($array_titulos)-4)
					   )
					{
						$html_nueva_ventana.="<th>$titulo_columna</th>";
					}
					$cont_titulos++;
				}
				$html_nueva_ventana.="</tr>";
				fclose($file_estado_informacion);
			}
			
			$hubo_resultados=true;
			//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
			
			
			
			$file_estado_informacion= fopen($ruta_archivo_estado_informacion, "a") or die("fallo la creacion del archivo");
			foreach($resultado_query_inconsistencias as $resultado)
			{
				$linea_estado_informacion="";
				$linea_estado_informacion.=$cont_linea.",".$resultado["codigo_estado_informacion"].",".$resultado["nombre_estado_informacion"].",";
				$linea_estado_informacion.=$resultado["fecha_validacion"].",".$resultado["periodo_reporte"].",".$resultado["descripcion_periodo_reporte"].",";
				$linea_estado_informacion.=$resultado["fecha_corte_periodo"].",".$resultado["numero_secuencia"].",".$resultado["codigo_eapb"].",";
				$linea_estado_informacion.=$resultado["nombre_eapb"].",".$resultado["codigo_prestador_servicios"].",".$resultado["tipo_identificacion_prestador"].",";
				$linea_estado_informacion.=$resultado["numero_identificacion_prestado"].",".$resultado["nombre_del_archivo"].",".$resultado["total_registros"].",";
				$linea_estado_informacion.=$resultado["codigo_departamento"].",".$resultado["nombre_del_departamento"].",";
				$linea_estado_informacion.=$resultado["codigo_municipio"].",".$resultado["nombre_de_municipio"];
				fwrite($file_estado_informacion, $linea_estado_informacion."\n");
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros.';</script>";
				
				$html_nueva_ventana.="<tr>";
				$array_linea_estado_informacion=explode(",",$linea_estado_informacion);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_estado_informacion as $columna_estado_informacion)
				{
					if($cont_columnas_estado_informacion!=(count($array_linea_estado_informacion)-1)
					&& $cont_columnas_estado_informacion!=(count($array_linea_estado_informacion)-2)
					&& $cont_columnas_estado_informacion!=(count($array_linea_estado_informacion)-3)
					&& $cont_columnas_estado_informacion!=(count($array_linea_estado_informacion)-4)
					)
					{
						$html_nueva_ventana.="<td>$columna_estado_informacion</td>";
					}
					$cont_columnas_estado_informacion++;
				}
				$html_nueva_ventana.="</tr>";
				
				$cont_linea++;
			}
			fclose($file_estado_informacion);
		
			$html_nueva_ventana.="</table>";
			
			$html_nueva_ventana.="</body>";
			$html_nueva_ventana.="</html>";
		
			$insertar_html_nueva_ventana="";
			$insertar_html_nueva_ventana.="<script>ventana_detalle.document.write(\"$html_nueva_ventana\");</script>";
			echo $insertar_html_nueva_ventana;
			
			
		}//fin if hallo resultados
		
		$contador_offset+=2000;
	
	}//fin while
	
	if($hubo_resultados)
	{
		//borrando vistas
	
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW veipyp_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			echo "<script>alert('error al borrar vista(s)');</script>";
		}
		
		//fin borrando vistas
		
		echo "<script>document.getElementById('grilla').style.display='inline';</script>";
		
		$html_reabrir_ventana="";
		$html_reabrir_ventana.="<script>";
		$html_reabrir_ventana.="function re_abrir_nueva_ventana()";
		$html_reabrir_ventana.="{";
		$html_reabrir_ventana.="ventana_detalle=window.open ('','ventana_estado_info_prestador', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
		$html_reabrir_ventana.="ventana_detalle.document.body.innerHTML='';";
		$html_reabrir_ventana.="ventana_detalle.document.write(\"$html_nueva_ventana\");";	
		$html_reabrir_ventana.="}";
		$html_reabrir_ventana.="</script>";
		echo $html_reabrir_ventana;
		
		$boton_ventana=" <input type=\'button\' value=\'Ver el estado informacion para PyP\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
		
			
		$boton_descarga=" <input type=\'button\' value=\'Descargar archivo con el estado informacion para PyP 4505\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_archivo_estado_informacion\');\"/> ";
	
		echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
		
	}
	else
	{
		//borrando vistas
	
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW veipyp_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			echo "<script>alert('error al borrar vista(s)');</script>";
		}
		
		//fin borrando vistas
		echo "<script>document.getElementById('mensaje_div').innerHTML='No se hallaron coincidencias con la fecha especificada.';</script>";
	}


	//consulta estado el estado de la informacion
	
}//fin if se selecciono
//FIN PARTE BUSQUEDA

?>