<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

$smarty = new Smarty;
$coneccionBD = new conexion();

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

function alphanumericAndSpace( $string )
{
    return trim(preg_replace('/[^a-zA-Z0-9\s, :;\-@.\/]/', '', $string));
}

function alphanumericAndSpace_include_br( $string )
{
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_\:]/', '', $string));
}

function procesar_mensaje($mensaje)
{
	$mensaje_procesado = str_replace("á","a",$mensaje);
	$mensaje_procesado = str_replace("é","e",$mensaje_procesado);
	$mensaje_procesado = str_replace("í","i",$mensaje_procesado);
	$mensaje_procesado = str_replace("ó","o",$mensaje_procesado);
	$mensaje_procesado = str_replace("ú","u",$mensaje_procesado);
	$mensaje_procesado = str_replace("ñ","n",$mensaje_procesado);
	$mensaje_procesado = str_replace("Á","A",$mensaje_procesado);
	$mensaje_procesado = str_replace("É","E",$mensaje_procesado);
	$mensaje_procesado = str_replace("Í","I",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ó","O",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ú","U",$mensaje_procesado);
	$mensaje_procesado = str_replace("Ñ","N",$mensaje_procesado);
	$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
	$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
	$mensaje_procesado = alphanumericAndSpace_include_br($mensaje_procesado);
	
	return $mensaje_procesado;
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];


$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mostrarResultado = "none";
$mensaje="";
$resultadoDefinitivo="";
$utilidades = new Utilidades();


$conexionbd = new conexion();

$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);


$selector_fechas_corte="";
/*
$selector_fechas_corte.="<select id='fechas_corte' name='fechas_corte' class='campo_azul' onchange='seleccionar_periodo();'>";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$fecha_prr_array=explode("/",$periodo["fecha_corte"]);
	$selector_fechas_corte.="<option value='".$fecha_prr_array[1]."-".$fecha_prr_array[0]."'>".$fecha_prr_array[1]."-".$fecha_prr_array[0]."-".$fecha_prr_array[2]."</option>";
}
$selector_fechas_corte.="</select>";
*/
$selector_fechas_corte.="<input type='hidden' id='fechas_corte' name='fechas_corte' >";

$selector_periodo="";

$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["cod_periodo"];
	$nombre_periodo=$periodo["nombre_periodo"];
	$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo)</option>";
}
$selector_periodo.="</select>";



//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad


//SELECTOR EAPB-ASOCIADA_ASOCIADA_USUARIO
$eapb="";
$eapb.="<div id='div_eapb'>";
$eapb.="<select id='eapb' name='eapb' class='campo_azul'>";
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
$mensaje_div="<div id='mensaje_div'></div>";
$res_def_div="<div id='resultado_definitivo'></div>";
$smarty->assign("mensaje_proceso", $mensaje_div, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);
$smarty->assign("resultado_definitivo", $res_def_div, true);
$smarty->assign("campo_eapb", $eapb, true);
//$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('repobligRIPS3374.html.tpl');


if(isset($_POST["year_de_corte"]) && isset($_POST['eapb']) && $_POST['eapb']!="none" && $_POST["year_de_corte"]!="" && ctype_digit($_POST["year_de_corte"]) )
{

	$fecha_de_corte=$_POST['year_de_corte']."-".$_POST['fechas_corte'];
	$periodo=$_POST['periodo'];
	$accion=$_POST['selector_estado_info'];
	//echo "<script>alert(\"".$fecha_de_corte." ".$periodo." ".$accion."\");</script>";
	
	$mensajes_error_bd="";
	
	//$cod_pss_IPS = $_POST['prestador'];
	$cod_eapb=$_POST['eapb'];
	//echo "<script>alert(\"".$cod_pss_IPS."\");</script>";
	
	$numero_remision_de_bd="";
	
	$numero_registros_ct=0;
	$numero_registros_us=0;
	$numero_registros_ac=0;
	$numero_registros_ap=0;
	$numero_registros_au=0;
	$numero_registros_ah=0;
	$numero_registros_an=0;
	$numero_registros_am=0;
	
	$hay_datos_para_reporte=false;

	
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('H:i:s');
	
	$fecha_array= explode("-",$fecha_de_corte);
	$year=$fecha_array[0];
	
	$fecha_revisar = date('Y-m-d',strtotime($fecha_de_corte));
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";
	
	//echo "<script>alert(\"".$fecha_revisar."\");</script>";
	
	//PERIODOS RIPS
	if(intval($periodo)==1)
	{
	   $fecha_ini_bd="01/01/".$year;
	   $fecha_fin_bd="01/31/".$year;
	   $fecha_de_corte_periodo="01/31/".$year;
	}
	if(intval($periodo)==2)
	{
	   $fecha_ini_bd="02/01/".$year;
	   $fecha_fin_bd="02/28/".$year;
	   $fecha_de_corte_periodo="02/28/".$year;
	}
	if(intval($periodo)==3)
	{
	   $fecha_ini_bd="03/01/".$year;
	   $fecha_fin_bd="03/31/".$year;
	   $fecha_de_corte_periodo="03/31/".$year;
	}
	if(intval($periodo)==4)
	{
	   $fecha_ini_bd="04/01/".$year;
	   $fecha_fin_bd="04/30/".$year;
	   $fecha_de_corte_periodo="04/30/".$year;
	}
	if(intval($periodo)==5)
	{
	   $fecha_ini_bd="05/01/".$year;
	   $fecha_fin_bd="05/31/".$year;
	   $fecha_de_corte_periodo="05/31/".$year;
	}
	if(intval($periodo)==6)
	{
	   $fecha_ini_bd="06/01/".$year;
	   $fecha_fin_bd="06/30/".$year;
	   $fecha_de_corte_periodo="06/30/".$year;
	}
	if(intval($periodo)==7)
	{
	   $fecha_ini_bd="07/01/".$year;
	   $fecha_fin_bd="07/31/".$year;
	   $fecha_de_corte_periodo="07/31/".$year;
	}
	if(intval($periodo)==8)
	{
	   $fecha_ini_bd="08/01/".$year;
	   $fecha_fin_bd="08/31/".$year;
	   $fecha_de_corte_periodo="08/31/".$year;
	}
	if(intval($periodo)==9)
	{
	   $fecha_ini_bd="9/01/".$year;
	   $fecha_fin_bd="9/30/".$year;
	   $fecha_de_corte_periodo="09/30/".$year;
	}
	if(intval($periodo)==10)
	{
	   $fecha_ini_bd="10/01/".$year;
	   $fecha_fin_bd="10/31/".$year;
	   $fecha_de_corte_periodo="10/31/".$year;
	}
	if(intval($periodo)==11)
	{
	   $fecha_ini_bd="11/01/".$year;
	   $fecha_fin_bd="11/30/".$year;
	   $fecha_de_corte_periodo="11/30/".$year;
	}
	if(intval($periodo)==12)
	{
	   $fecha_ini_bd="12/01/".$year;
	   $fecha_fin_bd="12/31/".$year;
	   $fecha_de_corte_periodo="12/31/".$year;
	}
	//FIN PERIODOS RIPS
	
	//echo "<script>alert(\"".$fecha_ini_bd." ".$fecha_fin_bd."\");</script>";
	
	$array_fibd=explode("/",$fecha_ini_bd);
	$fecha_ini_bd=$array_fibd[2]."-".$array_fibd[0]."-".$array_fibd[1];
	
	$array_ffbd=explode("/",$fecha_fin_bd);
	$fecha_fin_bd=$array_ffbd[2]."-".$array_ffbd[0]."-".$array_ffbd[1];
	
	$array_fcbd=explode("/",$fecha_de_corte_periodo);
	$fecha_corte_bd=$array_fcbd[2]."-".$array_fcbd[0]."-".$array_fcbd[1];
	
	//echo "<script>alert(\"".$fecha_ini_bd." ".$fecha_fin_bd."\");</script>";
	
	$string_periodo= "".$periodo;
	
	if(strlen($string_periodo)!=2)
	{
		$string_periodo="0".$string_periodo;
	}
	
	$intervalo_permite_validar = date_diff(date_create($fecha_corte_bd),date_create($fecha_actual));
	$tiempo_validar= (float)($intervalo_permite_validar->format("%r%a"));
	
	//echo "<script>alert('$fecha_corte_bd $fecha_actual $tiempo_validar');</script>";
	
	$bool_se_puede_generar_en_esta_fecha=true;
	if($tiempo_validar<0)
	{
		$bool_se_puede_generar_en_esta_fecha=false;
	}
	
	
	//queries para estructura nombre zip
	$query_tipo_entidad="SELECT cod_tipo_ident_entidad_reportadora,cod_tipo_regimen_rips FROM gios_entidad_administradora WHERE cod_entidad_administradora='$cod_eapb';";
	$resultado_query_tipo_entidad_reportadora=$coneccionBD->consultar2($query_tipo_entidad);
	
	$cod_tipo_ident_entidad_reportadora=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_ident_entidad_reportadora"];
	$cod_tipo_regimen_rips=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_regimen_rips"];
	
	$string_cod_eapb=$cod_eapb;
	while(strlen($string_cod_eapb)<12)
	{
		$string_cod_eapb="0".$string_cod_eapb;
	}
	//fin queries  para estructura nombre zip
	
	$nombre_zip_sin_consecutivo="RIP170RIPS".$array_fcbd[2].$array_fcbd[0].$array_fcbd[1].$cod_tipo_ident_entidad_reportadora.$string_cod_eapb.$cod_tipo_regimen_rips;
	
	//verificar que el nombre de archivo no haya sido reportado
	$fecha_generacion_si_se_reporto="";
	$ya_se_reporto_archivo=false;
	$query_verificar_nombre_archivo="";
	$query_verificar_nombre_archivo.="SELECT * FROM gioss_archivos_obligatorios_reportados_rips WHERE codigo_entidad_eapb_generadora='$cod_eapb' ";
	$query_verificar_nombre_archivo.=" AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ";
	if($accion=="validada")
	{
		$query_verificar_nombre_archivo.=" AND estado_informacion='1' ;";
	}
	else if($accion=="rechazada")
	{
		$query_verificar_nombre_archivo.=" AND estado_informacion='2' ;";
	}
	$resultado_verificar_nombre_archivo=$coneccionBD->consultar2($query_verificar_nombre_archivo);
	if(count($resultado_verificar_nombre_archivo)>0)
	{
		$ya_se_reporto_archivo=true;
		$fecha_generacion_si_se_reporto=$resultado_verificar_nombre_archivo[0]["fecha_de_generacion"];
	}
	//fin verificar
	
	$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
	
	$array_rutas_archivos_generados=array();
	
	$rutaTemporal = '../TEMPORALES/';
	
	
	
	if($ya_se_reporto_archivo==false && $bool_se_puede_generar_en_esta_fecha==true)
	{
		//son varios archivos no uno solo
		if(!file_exists($rutaTemporal.$nombre_zip_sin_consecutivo.$tiempo_actual_string))
		{
			mkdir($rutaTemporal.$nombre_zip_sin_consecutivo.$tiempo_actual_string, 0700);
		}
		else
		{
			$files_to_erase = glob($rutaTemporal.$nombre_zip_sin_consecutivo.$tiempo_actual_string."/*"); // get all file names
			foreach($files_to_erase as $file_to_be_erased)
			{ // iterate files
			  if(is_file($file_to_be_erased))
			  {
			    unlink($file_to_be_erased); // delete file
			  }
			}
		}
		$rutaTemporal=$rutaTemporal.$nombre_zip_sin_consecutivo.$tiempo_actual_string."/";
		
		//GENERANDO CT PARTE 1(parte 2 al final para poder obtener la cantidad de registros de los demas archivos, antes de consultar)
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado USA FECHA EN LA QUESE VALIDO NO LA DE REMISION
		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_ct WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_ct WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc  ";
		$sql_datos_reporte_obligatorio .=";";		
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="error al crear vista ct: ".$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_ct=$numero_registros;
		//echo "<script>alert('$numero_registros $fecha_ini_bd $fecha_fin_bd ');</script>";
		
		if($numero_registros>0)
		{
			//echo "<script>alert(' hay registros en el ct $numero_registros $fecha_ini_bd $fecha_fin_bd ');</script>";
			$hay_datos_para_reporte=true;
		}
		
		
		//FIN GENERANDO CT PARTE 1
		
			
		
		//GENERANDO US
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		$columnas_us="";
		$columnas_us.=" DISTINCT ON(us.tipo_identificacion_usuario,us.numero_identificacion_usuario) ";
		$columnas_us.="us.codigo_eapb,us.tipo_identificacion_usuario,us.numero_identificacion_usuario,us.codigo_tipo_usuario ,";
		$columnas_us.="(select afi.cod_tipo_afiliado from gioss_afiliados_eapb_rc afi where afi.tipo_id_afiliado=us.tipo_identificacion_usuario and afi.id_afiliado=us.numero_identificacion_usuario limit 1 ) as tipo_afiliado,";
		$columnas_us.="(select afi.cod_ocupacion from gioss_afiliados_eapb_rc afi where afi.tipo_id_afiliado=us.tipo_identificacion_usuario and afi.id_afiliado=us.numero_identificacion_usuario limit 1 ) as codigo_ocupacion,";
		$columnas_us.="us.edad,us.unidad_medida_edad,sexo,us.primer_apellido,us.segundo_apellido,us.primer_nombre,us.segundo_nombre,";
		$columnas_us.="us.codigo_departamento_residencia,us.codigo_municipio_residencia,us.codigo_zona_residencia, us.fila, us.numero_secuencia, us.codigo_prestador_servicios_salud, ";
		$columnas_us.=" us.fecha_remision ";		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT $columnas_us from gioss_archivo_cargado_us us WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT $columnas_us from gioss_archivo_rechazado_us us WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ";
		$sql_datos_reporte_obligatorio .=" ORDER BY us.tipo_identificacion_usuario asc ,us.numero_identificacion_usuario asc, numero_secuencia asc, fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_us=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE US RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_us_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_us_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=13)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_us_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_tipo_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["primer_apellido"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["segundo_apellido"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["primer_nombre"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["segundo_nombre"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["edad"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["unidad_medida_edad"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["sexo"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_departamento_residencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_municipio_residencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_zona_residencia"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_tipo_usuario"]."',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $contador_offset registros recuperados de BD $numero_registros para US.';</script>";
				ob_flush();
				flush();
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE US RIPS
		
		
		//FIN GENERANDO US
		
		//GENERANDO AC
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_ac WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_ac WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_ac=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE AC RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ac_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ac_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=16)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_ac_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_factura"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_atencion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_autorizacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_cups_consulta"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["finalidad_consulta"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["causa_externa_consulta"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_principal"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_relacionado_1"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_relacionado_2"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_relacionado_3"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_diagnostico_principal"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["valor_consulta"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["valor_cuota_moderadora"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["valor_neto_pagado"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'1',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $contador_offset registros recuperados de BD $numero_registros para AC.';</script>";
				ob_flush();
				flush();
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE AC RIPS
		
		//FIN GENERANDO AC
		
		//GENERANDO AH
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_ah WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_ah WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_ah=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE AH RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ah_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ah_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=18)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_ah_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_factura"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["via_ingreso_institucion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["hora_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_autorizacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["causa_externa"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_principal_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_principal_egreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_relacionado_egreso_1"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_relacionado_egreso_2"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_relacionado_egreso_3"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_complicacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["estado_a_salida"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_muerte"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_egreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["hora_egreso"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'1',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE AH RIPS
		
		//FIN GENERANDO AH
			
		
		//GENERANDO AP
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_ap WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_ap WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_ap=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE AP RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ap_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ap_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=14)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_ap_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_factura"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_procedimiento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_autorizacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_cups_procedimiento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["ambito_realizacion_procedimiento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["finalidad_procedimiento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["personal_que_atiende"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["diagnostico_principal"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["diagnostico_relacionado"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["diagnostico_complicacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["forma_realizacion_acto_quirurgico"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["valor_procedimiento"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'1',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $contador_offset registros recuperados de BD $numero_registros para AP.';</script>";
				ob_flush();
				flush();
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE AP RIPS
		
		
		//FIN GENERANDO AP
		
		//GENERANDO AU
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_au WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_au WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_au=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE AU RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_au_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_au_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=16)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_au_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_factura"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["hora_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_autorizacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["causa_externa"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_de_salida"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_realcionado_1"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_realcionado_2"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_realcionado_3"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["destino_usuario_salida"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["estado_salida_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["diagnostico_causa_muerte"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_salida"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["hora_salida_ingreso"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'1',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $contador_offset registros recuperados de BD $numero_registros para AU.';</script>";
				ob_flush();
				flush();
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE AU RIPS
		
		//FIN GENERANDO AU
		
		//GENERANDO AN
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_an WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_an WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_an=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE AN RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_an_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_an_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=13)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_an_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_factura"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_madre"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_madre"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["hora_ingreso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["edad_gestacional"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["control_prenatal"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["sexo"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["peso"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_recien_nacido"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_diagnostico_causa_muerte"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_muerte_recien_nacido"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["hora_muerte_recien_nacido"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'1',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $contador_offset registros recuperados de BD $numero_registros para AN.';</script>";
				ob_flush();
				flush();
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE AN RIPS
		
		//FIN GENERANDO AN
		
		//GENERANDO AM
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_cargado_am   ";
			$sql_datos_reporte_obligatorio .=" WHERE ";
			$sql_datos_reporte_obligatorio .=" (fecha_validacion_exito BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_archivo_rechazado_am  ";
			$sql_datos_reporte_obligatorio .=" WHERE ";
			$sql_datos_reporte_obligatorio .=" (fecha_de_rechazo   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		}
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ORDER BY numero_secuencia asc,fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$numero_registros_am=$numero_registros;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE AM RIPS
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_rips=$coneccionBD->consultar2($sql_query_busqueda);
			foreach($resultados_query_rips as $linea_consulta)
			{				
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
				$sql_insert_consulta_reporte_obligatorio="";
				$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_am_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_am_rechazado ";
				}
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				$cont_orden_campo_rips=0;
				while($cont_orden_campo_rips<=13)
				{
					$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_am_con_numero_orden_".$cont_orden_campo_rips." , ";
					$cont_orden_campo_rips++;
				}
				$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
				$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
				$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
				$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
				$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
				$sql_insert_consulta_reporte_obligatorio.=" ( ";
				//inicia campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_factura"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_identificacion_usuario"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_autorizacion"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_del_medicamento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["tipo_medicamento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["nombre_generico_medicamento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["forma_farmaceutica"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["concetracion_medicamento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["unidad_medida_medicamento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_unidades"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["valor_unitario_medicamento"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["valor_total_medicamento"]."',";
				//fin campos archivo norma
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";				
				$sql_insert_consulta_reporte_obligatorio.="'1',";//regimen esta en us
				if($accion=="validada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				else if($accion=="rechazada")
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fecha_remision"]."',";
				}
				$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_servicios_salud"]."',";
				$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
				$sql_insert_consulta_reporte_obligatorio.=" ) ";
				$sql_insert_consulta_reporte_obligatorio.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $contador_offset registros recuperados de BD $numero_registros para AM.';</script>";
				ob_flush();
				flush();
				
			}//fin foreach
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA REPORTE AM RIPS
		
		
		//FIN GENERANDO AM
		
		//GENERANDO CT PARTE 2
		
		//nuevo ct trae para campo 1 la eapb, campo 2 la fecha de generacion, campo 3 los archivos subidos a reporte oblig, campo 4 su numero de registros
		$array_archivos_nombre_para_nuevo_ct=array();
		$array_registros_archivo_para_nuevo_ct=array();
		if($numero_registros_ac>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="AC".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_ac;
		}
		if($numero_registros_ah>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="AH".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_ah;
		}
		if($numero_registros_am>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="AM".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_am;
		}
		if($numero_registros_an>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="AN".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_an;
		}
		if($numero_registros_ap>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="AP".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_ap;
		}
		if($numero_registros_au>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="AU".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_au;
		}
		if($numero_registros_us>0)
		{
			$array_archivos_nombre_para_nuevo_ct[]="US".$string_periodo.intval($year);
			$array_registros_archivo_para_nuevo_ct[]=$numero_registros_us;
		}
		
		$numero_registros=$numero_registros_ct;
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		//SUBIDA A TABLA CONSULTA REPORTE CT RIPS
		$cont_archivos_para_nuevo_ct=0;
		while($cont_archivos_para_nuevo_ct<count($array_archivos_nombre_para_nuevo_ct))
		{				
			//echo "<script>alert('entro a subir ct parte 2')</script>";
			
			//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO 
			$sql_insert_consulta_reporte_obligatorio="";
			$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
			if($accion=="validada")
			{
				$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ct_exitoso ";
			}
			else if($accion=="rechazada")
			{
				$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_rips3374_ct_rechazado ";
			}
			$sql_insert_consulta_reporte_obligatorio.=" ( ";
			$cont_orden_campo_rips=0;
			while($cont_orden_campo_rips<=3)
			{
				$sql_insert_consulta_reporte_obligatorio.=" campo_rips3374_ct_con_numero_orden_".$cont_orden_campo_rips." , ";
				$cont_orden_campo_rips++;
			}
			$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
			$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
			$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
			$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
			$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
			$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_rips ";
			$sql_insert_consulta_reporte_obligatorio.=" ) ";
			$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
			$sql_insert_consulta_reporte_obligatorio.=" ( ";
			//inicia campos archivo norma
			$sql_insert_consulta_reporte_obligatorio.="'".$cod_eapb."',";
			$sql_insert_consulta_reporte_obligatorio.="'".$fecha_corte_bd."',";
			$sql_insert_consulta_reporte_obligatorio.="'".$array_archivos_nombre_para_nuevo_ct[$cont_archivos_para_nuevo_ct]."',";
			$sql_insert_consulta_reporte_obligatorio.="'".$array_registros_archivo_para_nuevo_ct[$cont_archivos_para_nuevo_ct]."',";
			//fin campos archivo norma
			$sql_insert_consulta_reporte_obligatorio.="'".$cont_archivos_para_nuevo_ct."',";//valor del numero_registro				
			$sql_insert_consulta_reporte_obligatorio.="'".$fecha_corte_bd."',";				
			$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
			$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
			$sql_insert_consulta_reporte_obligatorio.="'".$cod_eapb."',";
			$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
			$sql_insert_consulta_reporte_obligatorio.=" ) ";
			$sql_insert_consulta_reporte_obligatorio.=" ; ";
			$error_bd_seq="";		
			$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.=" ERROR AL SUBIR  EN BD CT OBLIGATORIOS ".$error_bd_seq."<br>";
			}
			//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
			
			echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_archivos_para_nuevo_ct registros recuperados de BD ".count($array_archivos_nombre_para_nuevo_ct)." para CT.';</script>";
			ob_flush();
			flush();
			
			$cont_archivos_para_nuevo_ct++;
		}//fin while
		
		//FIN SUBIDA A TABLA CONSULTA REPORTE CT RIPS
		
		//FIN GENERANDO CT PARTE 2
		
		
		//PARTE REGISTRO TABLA ARCHIVOS OBLIGATORIOS REPORTADOS
		$sql_insert_registro_archivos_reportados_obligatorios="";
		$sql_insert_registro_archivos_reportados_obligatorios.="INSERT INTO gioss_archivos_obligatorios_reportados_rips ";
		$sql_insert_registro_archivos_reportados_obligatorios.="(";
		$sql_insert_registro_archivos_reportados_obligatorios.="nombre_archivo_rips,";
		$sql_insert_registro_archivos_reportados_obligatorios.="fecha_de_generacion,";
		$sql_insert_registro_archivos_reportados_obligatorios.="hora_generacion,";
		$sql_insert_registro_archivos_reportados_obligatorios.="fecha_corte_reporte,";
		$sql_insert_registro_archivos_reportados_obligatorios.="regimen,";
		$sql_insert_registro_archivos_reportados_obligatorios.="codigo_entidad_eapb_generadora,";
		$sql_insert_registro_archivos_reportados_obligatorios.="estado_informacion,";
		$sql_insert_registro_archivos_reportados_obligatorios.="cantidad_registros_ct,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_ct,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_us,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_ac,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_ap,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_au,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_ah,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_an,";
		$sql_insert_registro_archivos_reportados_obligatorios.="archivo_am";
		$sql_insert_registro_archivos_reportados_obligatorios.=")";
		$sql_insert_registro_archivos_reportados_obligatorios.=" VALUES ";
		$sql_insert_registro_archivos_reportados_obligatorios.="(";
		$sql_insert_registro_archivos_reportados_obligatorios.="'".$nombre_zip_sin_consecutivo."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'".$fecha_actual."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'".$tiempo_actual."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'".$fecha_corte_bd."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'1',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'".$cod_eapb."',";
		if($accion=="validada")
		{
			$sql_insert_registro_archivos_reportados_obligatorios.="'1',";
		}
		else if($accion=="rechazada")
		{
			$sql_insert_registro_archivos_reportados_obligatorios.="'2',";
		}
		$sql_insert_registro_archivos_reportados_obligatorios.="'".$numero_registros_ct."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'CT".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'US".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'AC".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'AP".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'AU".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'AH".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'AN".$string_periodo.intval($year)."',";
		$sql_insert_registro_archivos_reportados_obligatorios.="'AM".$string_periodo.intval($year)."'";
		$sql_insert_registro_archivos_reportados_obligatorios.=")";
		$sql_insert_registro_archivos_reportados_obligatorios.=";";
		$error_bd_seq="";
		if($hay_datos_para_reporte==true)
		{
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_insert_registro_archivos_reportados_obligatorios, $error_bd_seq);
		}
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" MOSTRAR ERROR registro archivos reportados: ".$error_bd_seq."<br>";
		}
		//FIN PARTE REGISTRO TABLA ARCHIVOS OBLIGATORIOS REPORTADOS
		
		
		
		
		$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
		//echo "<script>alert('$tiempo_actual_string')</script>";
		
		
		//PARTE ESCRIBE CSV CT
		
		$ruta_escribir_archivo=$rutaTemporal."CT".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		//echo "<script>alert('$ruta_escribir_archivo')</script>";
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ct_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ct_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" MOSTRAR ERROR  VISTA CONSULTA: ".$error_bd_seq."<br>";
		}
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		//echo "<script>alert('$numero_registros')</script>";
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			//echo "<script>alert('entro $numero_registros')</script>";
			
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$error_bd_seq="";
			$resultado_query_inconsistencias=$coneccionBD->consultar_no_warning_get_error($sql_query_busqueda,$error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.=" MOSTRAR ERROR TRAER CONSULTA: ".$error_bd_seq."<br>";
			}
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					while($cont_orden_campo_rips<=3)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						//formato bd postgre year-month-day
						$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ct_con_numero_orden_".$cont_orden_campo_rips]);
						if(count($array_fecha_campo_archivo)==3)
						{
							//formato rips day/month/year
							$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
							$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
						}
						else
						{
							$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ct_con_numero_orden_".$cont_orden_campo_rips]);
						}
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para CT.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV CT
		
		$array_campo_7_us_para_am=array();
		$array_campo_8_us_para_am=array();
		
		//PARTE ESCRIBE CSV US
		$ruta_escribir_archivo=$rutaTemporal."US".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;		
		
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_us_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_us_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					//son 14 campos para el us pero su archivo de reporte obligatorio tiene 12 campos
					while($cont_orden_campo_rips<=11)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}//fin if es igual a
						if($cont_orden_campo_rips==1)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_0"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_1"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_1"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_3"]);
							}
						}//fin if es igual a
						
						$campo_regimen_us=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_3"]);
						$tipo_id_us=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_0"]);
						$numero_id_us=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_1"]);
						
						if($cont_orden_campo_rips==4)
						{
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_tipo_afiliado="";
								$query_tipo_afiliado.=" SELECT cod_tipo_afiliado FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_tipo_afiliado_us=$coneccionBD->consultar2($query_tipo_afiliado);
								if(count($resultado_query_tipo_afiliado_us)>0)
								{
									$tipo_afiliado_encontrado_us=intval($resultado_query_tipo_afiliado_us[0]["cod_tipo_afiliado"]);
									$cadena_escribir_linea.=$tipo_afiliado_encontrado_us;
								}
							}
						}//fin if es igual a
						
						if($cont_orden_campo_rips==5)
						{
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_ocupacion="";
								$query_ocupacion.=" SELECT cod_ocupacion FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_ocupacion_us=$coneccionBD->consultar2($query_ocupacion);
								if(count($resultado_query_ocupacion_us)>0)
								{
									$ocupacion_encontrado_us=intval($resultado_query_ocupacion_us[0]["cod_ocupacion"]);
									$cadena_escribir_linea.=$ocupacion_encontrado_us;
								}
							}
							
							if($campo_regimen_us==2 || $campo_regimen_us==7)
							{
								$query_poblacion_especial="";
								$query_poblacion_especial.=" SELECT cod_tipo_poblacion_especial FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_poblacion_especial=$coneccionBD->consultar2($query_poblacion_especial);
								if(count($resultado_query_tipo_afiliado_us)>0)
								{
									$poblacion_especial_encontrado_us=intval($resultado_query_poblacion_especial[0]["cod_ocupacion"]);
									$cadena_escribir_linea.=$poblacion_especial_encontrado_us;
								}
							}
						}//fin if es igual a
						
						if($cont_orden_campo_rips==6)
						{
							$valor_campo_7_us_obligatorio="";
							
							if($campo_regimen_us!=1 && $campo_regimen_us!=2 && $campo_regimen_us!=6 && $campo_regimen_us!=7)
							{
								//formato bd postgre year-month-day
								$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_8"]);
								if(count($array_fecha_campo_archivo)==3)
								{
									//formato rips day/month/year
									$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
									$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
									$valor_campo_7_us_obligatorio=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
								}
								else
								{
									$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_8"]);
									$valor_campo_7_us_obligatorio=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_8"]);
								}
							}//fin if no es rc o subsidiado
							
							$fecha_nacimiento_encontrado_us="";
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_fecha_nacimiento="";
								$query_fecha_nacimiento.=" SELECT fecha_nacimiento FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_fecha_nacimiento_us=$coneccionBD->consultar2($query_fecha_nacimiento);
								if(count($resultado_query_fecha_nacimiento_us)>0)
								{
									//aaaa-mm-dd;
									$fecha_nacimiento_encontrado_us=alphanumericAndSpace($resultado_query_fecha_nacimiento_us[0]["fecha_nacimiento"]);
									
								}
							}
							
							if($campo_regimen_us==2 || $campo_regimen_us==7)
							{
								$query_fecha_nacimiento="";
								$query_fecha_nacimiento.=" SELECT fecha_nacimiento FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_fecha_nacimiento_us=$coneccionBD->consultar2($query_fecha_nacimiento);
								if(count($resultado_query_fecha_nacimiento_us)>0)
								{
									//aaaa-mm-dd;
									$fecha_nacimiento_encontrado_us=alphanumericAndSpace($resultado_query_fecha_nacimiento_us[0]["fecha_nacimiento"]);
								}
							}
							
							
							if($fecha_nacimiento_encontrado_us!="")
							{
								$array_fecha_nacimiento_encontrado=explode("-",$fecha_nacimiento_encontrado_us);
								if(count($array_fecha_nacimiento_encontrado)==3)
								{
									if(checkdate(intval($array_fecha_nacimiento_encontrado[1]),intval($array_fecha_nacimiento_encontrado[0]),intval($array_fecha_nacimiento_encontrado[2])))
									{
										$interval = date_diff(date_create($fecha_nacimiento_encontrado_us),date_create($fecha_actual));
										$tiempo= (float)($interval->format("%r%a"));
										$edad_years=intval($tiempo/365);
										$cadena_escribir_linea.=$edad_years;
										$valor_campo_7_us_obligatorio=$edad_years;
									}
								}
							}
							
							if(!isset($array_campo_7_us_para_am[$tipo_id_us.$numero_id_us]))
							{
								$array_campo_7_us_para_am[$tipo_id_us.$numero_id_us]=$valor_campo_7_us_obligatorio;
							}
							
						}//fin if es igual a
						
						if($cont_orden_campo_rips==7)
						{
							$valor_campo_8_us_obligatorio="";
							
							if($campo_regimen_us!=1 && $campo_regimen_us!=2 && $campo_regimen_us!=6 && $campo_regimen_us!=7)
							{
								//formato bd postgre year-month-day
								$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_9"]);
								if(count($array_fecha_campo_archivo)==3)
								{
									//formato rips day/month/year
									$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
									$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
									$valor_campo_8_us_obligatorio=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
								}
								else
								{
									$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_9"]);
									$valor_campo_8_us_obligatorio=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_9"]);
								}
							}//fin if no es rc o subsidiado
							else
							{
								$cadena_escribir_linea.="1";
								$valor_campo_8_us_obligatorio="1";
							}
							
							if(!isset($array_campo_8_us_para_am[$tipo_id_us.$numero_id_us]))
							{
								$array_campo_8_us_para_am[$tipo_id_us.$numero_id_us]=$valor_campo_8_us_obligatorio;
							}
						}//fin if es igual a
						
						if($cont_orden_campo_rips==8)
						{
							if($campo_regimen_us!=1 && $campo_regimen_us!=2 && $campo_regimen_us!=6 && $campo_regimen_us!=7)
							{
								//formato bd postgre year-month-day
								$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_10"]);
								if(count($array_fecha_campo_archivo)==3)
								{
									//formato rips day/month/year
									$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
									$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
								}
								else
								{
									$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_10"]);
								}
							}//fin if no es rc o subsidiado
							
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_sexo="";
								$query_sexo.=" SELECT sexo FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_sexo=$coneccionBD->consultar2($query_sexo);
								if(count($resultado_query_sexo)>0)
								{
									$sexo_encontrado_us=alphanumericAndSpace($resultado_query_sexo[0]["sexo"]);
									$cadena_escribir_linea.=$sexo_encontrado_us;
								}
							}
							
							if($campo_regimen_us==2 || $campo_regimen_us==7)
							{
								$query_sexo="";
								$query_sexo.=" SELECT sexo FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_sexo=$coneccionBD->consultar2($query_sexo);
								if(count($resultado_query_sexo)>0)
								{
									$sexo_encontrado_us=alphanumericAndSpace($resultado_query_sexo[0]["sexo"]);
									$cadena_escribir_linea.=$sexo_encontrado_us;
								}
							}
							
						}//fin if es igual a
						
						if($cont_orden_campo_rips==9)
						{
							if($campo_regimen_us!=1 && $campo_regimen_us!=2 && $campo_regimen_us!=6 && $campo_regimen_us!=7)
							{
								//formato bd postgre year-month-day
								$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_11"]);
								if(count($array_fecha_campo_archivo)==3)
								{
									//formato rips day/month/year
									$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
									$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
								}
								else
								{
									$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_11"]);
								}
							}//fin if no es rc o subsidiado
							
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_dpto="";
								$query_dpto.=" SELECT cod_dpto FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_dpto=$coneccionBD->consultar2($query_dpto);
								if(count($resultado_query_dpto)>0)
								{
									$dpto_encontrado_us=alphanumericAndSpace($resultado_query_dpto[0]["cod_dpto"]);
									$cadena_escribir_linea.=$dpto_encontrado_us;
								}
							}
							
							if($campo_regimen_us==2 || $campo_regimen_us==7)
							{
								$query_dpto="";
								$query_dpto.=" SELECT cod_dpto FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_dpto=$coneccionBD->consultar2($query_dpto);
								if(count($resultado_query_dpto)>0)
								{
									$dpto_encontrado_us=alphanumericAndSpace($resultado_query_dpto[0]["cod_dpto"]);
									$cadena_escribir_linea.=$dpto_encontrado_us;
								}
							}
							
						}//fin if es igual a
						
						if($cont_orden_campo_rips==10)
						{
							if($campo_regimen_us!=1 && $campo_regimen_us!=2 && $campo_regimen_us!=6 && $campo_regimen_us!=7)
							{
								//formato bd postgre year-month-day
								$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_12"]);
								if(count($array_fecha_campo_archivo)==3)
								{
									//formato rips day/month/year
									$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
									$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
								}
								else
								{
									$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_12"]);
								}
							}//fin if no es rc o subsidiado
							
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_mpio="";
								$query_mpio.=" SELECT cod_mpio FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_mpio=$coneccionBD->consultar2($query_mpio);
								if(count($resultado_query_mpio)>0)
								{
									$mpio_encontrado_us=alphanumericAndSpace($resultado_query_mpio[0]["cod_mpio"]);
									$cadena_escribir_linea.=$mpio_encontrado_us;
								}
							}
							
							if($campo_regimen_us==2 || $campo_regimen_us==7)
							{
								$query_mpio="";
								$query_mpio.=" SELECT cod_mpio FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_mpio=$coneccionBD->consultar2($query_mpio);
								if(count($resultado_query_mpio)>0)
								{
									$mpio_encontrado_us=alphanumericAndSpace($resultado_query_mpio[0]["cod_mpio"]);
									$cadena_escribir_linea.=$mpio_encontrado_us;
								}
							}
							
						}//fin if es igual a
						
						if($cont_orden_campo_rips==11)
						{
							if($campo_regimen_us!=1 && $campo_regimen_us!=2 && $campo_regimen_us!=6 && $campo_regimen_us!=7)
							{
								//formato bd postgre year-month-day
								$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_us_con_numero_orden_13"]);
								if(count($array_fecha_campo_archivo)==3)
								{
									//formato rips day/month/year
									$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
									$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
								}
								else
								{
									$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_us_con_numero_orden_13"]);
								}
							}//fin if no es rc o subsidiado							
							
							
							if($campo_regimen_us==1 || $campo_regimen_us==6)
							{
								$query_zona="";
								$query_zona.=" SELECT cod_zona FROM gioss_afiliados_eapb_rc WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_zona=$coneccionBD->consultar2($query_zona);
								if(count($resultado_query_zona)>0)
								{
									$zona_encontrado_us=alphanumericAndSpace($resultado_query_zona[0]["cod_zona"]);
									$cadena_escribir_linea.=$zona_encontrado_us;
								}
							}
							
							if($campo_regimen_us==2 || $campo_regimen_us==7)
							{
								$query_zona="";
								$query_zona.=" SELECT cod_zona FROM gioss_afiliados_regimen_subsidiado WHERE tipo_id_afiliado='$tipo_id_us' AND id_afiliado='$numero_id_us';";
								$resultado_query_zona=$coneccionBD->consultar2($query_zona);
								if(count($resultado_query_mpio)>0)
								{
									$zona_encontrado_us=alphanumericAndSpace($resultado_query_zona[0]["cod_zona"]);
									$cadena_escribir_linea.=$zona_encontrado_us;
								}
							}
							
						}//fin if es igual a
						
						
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para US.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV US
		
		
		
		//PARTE ESCRIBE CSV AC
		$ruta_escribir_archivo=$rutaTemporal."AC".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ac_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ac_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					while($cont_orden_campo_rips<=16)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips!=0 && $cont_orden_campo_rips!=2 && $cont_orden_campo_rips!=3 && $cont_orden_campo_rips!=4 && $cont_orden_campo_rips!=5 )
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ac_con_numero_orden_".$cont_orden_campo_rips]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ac_con_numero_orden_".$cont_orden_campo_rips]);
							}
						}//entra normalmente si es diferente de
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}//fin if si es igual a
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ac_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ac_con_numero_orden_0"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ac_con_numero_orden_2"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ac_con_numero_orden_2"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==4)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ac_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ac_con_numero_orden_3"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==5)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ac_con_numero_orden_4"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ac_con_numero_orden_4"]);
							}
						}//fin if si es igual a
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para AC.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV AC
		
		//PARTE ESCRIBE CSV AH
		$ruta_escribir_archivo=$rutaTemporal."AH".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ah_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ah_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					while($cont_orden_campo_rips<=18)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips!=0
						   && $cont_orden_campo_rips!=2
						   && $cont_orden_campo_rips!=3
						   && $cont_orden_campo_rips!=4
						   && $cont_orden_campo_rips!=5
						   && $cont_orden_campo_rips!=6
						   && $cont_orden_campo_rips!=7
						   )
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_".$cont_orden_campo_rips]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_".$cont_orden_campo_rips]);
							}
						}//entra normalmente si es diferente de
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}//fin if si es igual a
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_0"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_2"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_2"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==4)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_3"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==5)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_4"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_4"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==6)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_5"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_5"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==7)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ah_con_numero_orden_6"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ah_con_numero_orden_6"]);
							}
						}//fin if si es igual a
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para AH.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV AH
		
		//PARTE ESCRIBE CSV AP
		$ruta_escribir_archivo=$rutaTemporal."AP".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ap_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ap_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					while($cont_orden_campo_rips<=13)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips!=0
						   && $cont_orden_campo_rips!=2
						   && $cont_orden_campo_rips!=3
						   && $cont_orden_campo_rips!=4
						   && $cont_orden_campo_rips!=5
						   && $cont_orden_campo_rips!=6
						   )
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ap_con_numero_orden_".$cont_orden_campo_rips]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ap_con_numero_orden_".$cont_orden_campo_rips]);
							}
						}//fin if entra normalmente si es diferente de
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}//fin if es igual a
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ap_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ap_con_numero_orden_0"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ap_con_numero_orden_2"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ap_con_numero_orden_2"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==4)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ap_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ap_con_numero_orden_3"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==5)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ap_con_numero_orden_4"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ap_con_numero_orden_4"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==6)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_ap_con_numero_orden_5"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_ap_con_numero_orden_5"]);
							}
						}//fin if es igual a
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para AP.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV AP
		
		
		
		//PARTE ESCRIBE CSV AU
		$ruta_escribir_archivo=$rutaTemporal."AU".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_au_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_au_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					//son 17 campos pero para el reporte obligatorio son 15
					while($cont_orden_campo_rips<=14)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}//fin if es igual a
						if($cont_orden_campo_rips==1)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_1"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_1"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_0"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_2"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_2"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==4)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_3"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==5)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_4"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_4"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==6)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_7"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_7"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==7)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_8"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_8"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==8)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_9"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_9"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==9)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_10"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_10"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==10)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_11"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_11"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==11)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_12"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_12"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==12)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_13"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_13"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==13)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_14"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_14"]);
							}
						}//fin if es igual a
						if($cont_orden_campo_rips==14)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_au_con_numero_orden_15"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_au_con_numero_orden_15"]);
							}
						}//fin if es igual a
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para AU.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV AU
		
		
		//PARTE ESCRIBE CSV AN
		$ruta_escribir_archivo=$rutaTemporal."AN".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
	
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_an_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_an_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					//son 14 campos para el an pero para el reporte obligatorio son 15
					while($cont_orden_campo_rips<=14)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}
						if($cont_orden_campo_rips==1)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_1"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_1"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_0"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_2"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_2"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==4)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_3"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==5)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_4"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_4"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==6)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_5"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_5"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==7)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_6"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_6"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==8)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_7"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_7"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==9)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_8"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_8"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==10)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_9"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_9"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==11)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_10"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_10"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==12)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_11"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_11"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==13)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_12"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_12"]);
							}
						}//fin if si es igual a
						if($cont_orden_campo_rips==14)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_an_con_numero_orden_13"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_an_con_numero_orden_13"]);
							}
						}//fin if si es igual a
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para AN.';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV AN
		
		
		//PARTE ESCRIBE CSV AM
		$ruta_escribir_archivo=$rutaTemporal."AM".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($accion=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_am_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($accion=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_am_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vconsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$contador_offset=0;
		$limite=0;
		
		$cont_linea=1;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vconsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_inconsistencias=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_inconsistencias)>0)
			{
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				$flag_para_salto_linea_inicial=false;
				foreach($resultado_query_inconsistencias as $resultado)
				{
					$cadena_escribir_linea="";
					$cont_orden_campo_rips=0;
					//son 14 campos para el am pero para el reporte obligatorio son 15
					while($cont_orden_campo_rips<=14)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.=",";
						}
						if($cont_orden_campo_rips==0)
						{
							$cadena_escribir_linea.=$cod_eapb;
						}
						if($cont_orden_campo_rips==1)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_1"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_1"]);
							}
						}
						if($cont_orden_campo_rips==2)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_0"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_0"]);
							}
						}
						if($cont_orden_campo_rips==3)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_2"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_2"]);
							}
						}
						if($cont_orden_campo_rips==4)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_3"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_3"]);
							}
						}
						if($cont_orden_campo_rips==5)
						{
							
							//campo 7 us obligatorio
							$tipo_id_am=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_2"]);
							$numero_id_am=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_3"]);
							if(isset($array_campo_7_us_para_am[$tipo_id_am.$numero_id_am]))
							{
								$cadena_escribir_linea.=$array_campo_7_us_para_am[$tipo_id_am.$numero_id_am];
							}
							
						}
						if($cont_orden_campo_rips==6)
						{
							//campo 8 us obligatorio
							$tipo_id_am=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_2"]);
							$numero_id_am=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_3"]);
							if(isset($array_campo_8_us_para_am[$tipo_id_am.$numero_id_am]))
							{
								$cadena_escribir_linea.=$array_campo_8_us_para_am[$tipo_id_am.$numero_id_am];
							}
						}
						if($cont_orden_campo_rips==7)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_7"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_7"]);
							}
						}
						if($cont_orden_campo_rips==8)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_6"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_6"]);
							}
						}
						if($cont_orden_campo_rips==9)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_8"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_8"]);
							}
						}
						if($cont_orden_campo_rips==10)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_9"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_9"]);
							}
						}
						if($cont_orden_campo_rips==11)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_10"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_10"]);
							}
						}
						if($cont_orden_campo_rips==12)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_11"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_11"]);
							}
						}
						if($cont_orden_campo_rips==13)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_12"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_12"]);
							}
						}
						if($cont_orden_campo_rips==14)
						{
							//formato bd postgre year-month-day
							$array_fecha_campo_archivo=explode("-",$resultado["campo_rips3374_am_con_numero_orden_13"]);
							if(count($array_fecha_campo_archivo)==3)
							{
								//formato rips day/month/year
								$nueva_fecha_para_archivo_rips=$array_fecha_campo_archivo[2]."/".$array_fecha_campo_archivo[1]."/".$array_fecha_campo_archivo[0];
								$cadena_escribir_linea.=alphanumericAndSpace($nueva_fecha_para_archivo_rips);
							}
							else
							{
								$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_rips3374_am_con_numero_orden_13"]);
							}
						}
						$cont_orden_campo_rips++;
					}
					//fwrite($reporte_obligatorio_file, $cadena_escribir_linea."\n");
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros para AM. ';</script>";
					ob_flush();
					flush();
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV AM
		
		//borrando vistas
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_us_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_ac_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_ah_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_ap_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_au_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_an_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vconsror3374_am_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			$mensajes_error_bd.="error al borrar vistas ".$error_bd."<br>";
		}
		
		//fin borrando vistas
		
		
				
		
		//obteniendo consecutivo
		
		$consecutivo="00";
		if($hay_datos_para_reporte==true)
		{
			$consecutivo_num=0;
			$query_obtener_consecutivo="SELECT * FROM gioss_numero_consecutivo_por_eapb WHERE cod_eapb='$cod_eapb' ; ";
			$resultados_consecutivo=$conexionbd->consultar2($query_obtener_consecutivo);
			if(count($resultados_consecutivo)>0)
			{
				if(count($array_rutas_archivos_generados)>0)
				{
					$consecutivo_num=intval($resultados_consecutivo[0]["numero_consecutivo"]);
					if($consecutivo_num<99)
					{
						$consecutivo_num++;
					}
					else
					{
						$consecutivo_num=0;
					}
					$query_actualizar_consecutivo="UPDATE gioss_numero_consecutivo_por_eapb SET numero_consecutivo='$consecutivo_num' WHERE cod_eapb='$cod_eapb' ; ";
					$error_bd="";
					try
					{
					$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_actualizar_consecutivo, $error_bd);
					
					}
					catch (Exception $e) {}
				}//aumenta el consecutivo si hay archivos a generar
			}//fin if si existe actualiza
			else
			{
				$query_insertar_consecutivo="INSERT INTO gioss_numero_consecutivo_por_eapb(cod_eapb,numero_consecutivo) VALUES('$cod_eapb','$consecutivo_num'); ";
				$error_bd="";
				try
				{
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_insertar_consecutivo, $error_bd);
				
				}
				catch (Exception $e) {}
			}//fin else si no existe inserta
			$consecutivo=$consecutivo_num;
			if(strlen($consecutivo)<2)
			{
				$consecutivo="0".$consecutivo;
			}
			//fin obteniendo consecutivo
			
			//UPDATE PARA INSERTAR EL NUMERO CONSECUTIVO
			$query_update_para_consecutivo="";
			$query_update_para_consecutivo.="UPDATE gioss_archivos_obligatorios_reportados_rips ";
			$query_update_para_consecutivo.=" SET consecutivo='$consecutivo' ";
			$query_update_para_consecutivo.=" WHERE codigo_entidad_eapb_generadora='$cod_eapb' ";
			$query_update_para_consecutivo.=" AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ";
			$query_update_para_consecutivo.=" ; ";
			$error_bd="";
			try
			{
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error($query_update_para_consecutivo, $error_bd);
				if($error_bd!="")
				{
					echo "<script>alert('error consecutivo');</script>";
				}
			}
			catch (Exception $e) {}
			//FIN UPDATE PARA INSERTAR EL NUMERO CONSECUTIVO
			
			//obteniendo consecutivo		
			if($accion=="rechazada")
			{
				$consecutivo.="R";
			}		
			//fin obteniendo consecutivo
		}//fin if si hay datos para reporte
		
		
		//echo "<script>alert('numero archivos ".count($array_rutas_archivos_generados)." ')</script>";
		
		if(count($array_rutas_archivos_generados)>0 && $hay_datos_para_reporte==true)
		{
			//GENERANDO ARCHIVO ZIP
					
			$ruta_zip=$rutaTemporal.$nombre_zip_sin_consecutivo.$consecutivo.".zip";		
			
			$ruta_dat=$rutaTemporal.$nombre_zip_sin_consecutivo.$consecutivo.".DAT";
			
			$result_zip = create_zip($array_rutas_archivos_generados,$ruta_zip);
			$result_dat = create_zip($array_rutas_archivos_generados,$ruta_dat);
			$mensaje.="<br>Se genero el consolidado de los archivos RIPS de forma comprimida.";
			echo "<script>document.getElementById('grilla').style.display='inline';</script>";
			echo "<script>var ruta_zip= '$ruta_zip'; </script>";
			echo "<script>var ruta_dat= '$ruta_dat'; </script>";
			$resultadoDefinitivo.="<input type='button' value='Descargar Reporte Obligatorio .zip' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_zip);' />  ";
			$resultadoDefinitivo.="<input type='button' value='Descargar Reporte Obligatorio .DAT' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_dat);' />  ";
			
			
			
			//FIN GENERANDO ARCHIVO ZIP
			
			// inicio envio de mail
	
		    $mail = new PHPMailer();
		    $mail->IsSMTP();
		    $mail->SMTPAuth = true;
		    $mail->SMTPSecure = "ssl";
		    $mail->Host = "smtp.gmail.com";
		    $mail->Port = 465;
		    $mail->Username = "sistemagioss@gmail.com";
		    $mail->Password = "gioss001";
		    $mail->From = "sistemagioss@gmail.com";
		    $mail->FromName = "GIOSS";
		    $mail->Subject = "Reporte Obligatorio RIPS 3374 ";
		    $mail->AltBody = "Cordial saludo,\n El sistema ha generado el reporte obligatorio para el periodo escogido.";
	
		    $mail->MsgHTML("Cordial saludo,\n El sistema le ha enviado el comprimido con los archivos correspondientes al reporte obligatorio.<strong>GIOSS</strong>.");
				$mail->AddAttachment($ruta_zip);
				$mail->AddAttachment($ruta_dat);
		    $mail->AddAddress($correo_electronico, "Destinatario");
	
		    $mail->IsHTML(true);
	
		    if (!$mail->Send()) 
				{
			//echo "Error: " . $mail->ErrorInfo;
		    } else 
				{
			// echo "Mensaje enviado.";
					echo "<script>alert('Se ha enviado una copia del consolidado a su correo $correo_electronico ')</script>";
		    }
	
		    //fin envio de mail
			
		}
		else
		{
			//$mensaje.="<br>No se encontraron resultados.";
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede generar el archivo, no existen datos asociados al a&ntildeo y periodo seleccionados.';</script>";
		
		}
	
	}//fin if se reporto archivo es false (no se reporto un archivo con ese nombre)
	else
	{
		if($ya_se_reporto_archivo==true)
		{
			//$mensaje.="<br>El archivo ya fue reportado.";
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='El archivo obligatorio que intenta cargar, ya fue generado en la fecha $fecha_generacion_si_se_reporto. Por favor verifique en el \"Menu de consulta reporte obligatorio\" y descarguelo. ';</script>";
		}
		
		if($bool_se_puede_generar_en_esta_fecha==false)
		{
			//$mensaje.="<br>El archivo ya fue reportado.";
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Se&ntildeor usuario el reporte obligatorio para el periodo especificado debe generarse a partir del dia siguiente de la fecha de corte. ';</script>";
		
		}
		
	}
	
	$mensaje_procesado=procesar_mensaje($mensajes_error_bd);	
	
	echo "<script>document.getElementById('mensaje_div').innerHTML=\"$mensaje <bd> $mensaje_procesado\"</script>";
	echo "<script>document.getElementById('resultado_definitivo').innerHTML=\"$resultadoDefinitivo\"</script>";
}//fin if cuando se hizo submit



?>