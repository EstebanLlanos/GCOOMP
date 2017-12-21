<?php
ignore_user_abort(true);
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

require_once 'reparacion_campos_duplicados.php';

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/configuracion_global_email.php';

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

function alphanumericAndSpace( $string )
{
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string));
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
$rutaTemporal = '../TEMPORALES/';

$conexionbd = new conexion();


$hubo_al_menos_un_duplicado=false;

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

$query_periodos_rips="SELECT * FROM gioss_periodo_reporte_2463_erc;";
$resultado_query_periodos=$coneccionBD->consultar2_no_crea_cierra($query_periodos_rips);

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo)
{
	$cod_periodo=$periodo["codigo_periodo"];
	$nombre_periodo=$periodo["descripcion_periodo"];
	$fecha_permitida=$periodo["valor_fecha_permitida"];
	$selector_periodo.="<option value='$cod_periodo'>Periodo $cod_periodo ($nombre_periodo $fecha_permitida)</option>";
}

$selector_periodo.="<option value='13'>Periodo 1er semestre (Enero 01-01 Junio 06-30)</option>";
$selector_periodo.="<option value='14'>Periodo 2do semestre (Julio 07-01 Diciembre 12-31)</option>";
$selector_periodo.="</select>";



//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
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
$smarty->display('repobligERC2463.html.tpl');

if(isset($_POST["year_de_corte"]) && isset($_POST['eapb']) && $_POST['eapb']!="none" && $_POST["year_de_corte"]!="" && ctype_digit($_POST["year_de_corte"]) )
{

	$fecha_de_corte=$_POST['year_de_corte']."-".$_POST['fechas_corte'];
	$periodo=$_POST['periodo'];
	$accion=$_POST['selector_estado_info'];
	//echo "<script>alert(\"".$fecha_de_corte." ".$periodo." ".$accion."\");</script>";
	
	
	
	$secuencia_actual=$utilidades->obtenerSecuenciaActual("gioss_numero_secuencia_rips_3374");	
	//$cod_pss_IPS = $_POST['prestador'];
	$cod_eapb=$_POST['eapb'];
	//echo "<script>alert(\"".$secuencia_actual."\");</script>";
	//echo "<script>alert(\"".$cod_pss_IPS."\");</script>";
	
	
	
	
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('h:i:s');
	
	$fecha_array= explode("-",$fecha_de_corte);
	$year=$fecha_array[0];
	
	$fecha_revisar = date('Y-m-d',strtotime($fecha_de_corte));
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";
	
	//echo "<script>alert(\"".$fecha_revisar."\");</script>";
	
	//PERIODOS ERC
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
	   $fecha_ini_bd="09/01/".$year;
	   $fecha_fin_bd="09/30/".$year;
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
	//FIN PERIODOS ERC
	
	//PERIODOS SEMESTRALES
	if(intval($periodo)==13)
	{
	   $fecha_ini_bd="01/01/".$year;
	   $fecha_fin_bd="06/30/".$year;
	   $fecha_de_corte_periodo="06/30/".$year;
	}
	if(intval($periodo)==14)
	{
	   $fecha_ini_bd="07/01/".$year;
	   $fecha_fin_bd="12/31/".$year;
	   $fecha_de_corte_periodo="12/31/".$year;
	}
	//FIN PERIODOS SEMESTRALES
	
	$array_fecha_de_corte_simple=explode("/",$fecha_de_corte_periodo);
	$fecha_de_corte_simple=$array_fecha_de_corte_simple[2].$array_fecha_de_corte_simple[0].$array_fecha_de_corte_simple[1];
	
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
	
	//queries para estructura nombre zip
	$query_tipo_entidad="SELECT cod_tipo_ident_entidad_reportadora,cod_tipo_regimen_rips FROM gios_entidad_administradora WHERE cod_entidad_administradora='$cod_eapb';";
	$resultado_query_tipo_entidad_reportadora=$coneccionBD->consultar2_no_crea_cierra($query_tipo_entidad);
	
	$cod_tipo_ident_entidad_reportadora=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_ident_entidad_reportadora"];
	$cod_tipo_regimen_rips=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_regimen_rips"];
	
	$string_cod_eapb=$cod_eapb;
	while(strlen($string_cod_eapb)<6)
	{
		$string_cod_eapb="0".$string_cod_eapb;
	}
	//fin queries  para estructura nombre zip
	
	$nombre_zip_sin_consecutivo=$array_fcbd[2].$array_fcbd[0].$array_fcbd[1]."_".$string_cod_eapb."_"."ERC";
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	$esta_validando_actualmente=false;	
					
	$query_verificacion_esta_siendo_procesado="";
	$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_2463_esta_consolidando_ro_actualmente ";
	$query_verificacion_esta_siendo_procesado.=" WHERE ";
	$query_verificacion_esta_siendo_procesado.=" nick_usuario='".$nick_user."'  ";
	$query_verificacion_esta_siendo_procesado.=" ; ";
	$resultados_query_verificar_esta_siendo_procesado=$coneccionBD->consultar2_no_crea_cierra($query_verificacion_esta_siendo_procesado);
	if(count($resultados_query_verificar_esta_siendo_procesado)>0)
	{
		foreach($resultados_query_verificar_esta_siendo_procesado as $estado_tiempo_real_archivo)
		{
			if($estado_tiempo_real_archivo["esta_ejecutando"]=="SI")
			{
				$esta_validando_actualmente=true;
			}
		}
		
	}	
	//FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO VALIDADO ACTUALMENTE
	
	//verificar que el nombre de archivo no haya sido reportado
	$ya_se_reporto_archivo=false;
	$fecha_generacion_si_se_reporto="";
	$query_verificar_nombre_archivo="SELECT * FROM gioss_archivos_obligatorios_reportados_erc WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_erc='$nombre_zip_sin_consecutivo' ;";
	$resultado_verificar_nombre_archivo=$coneccionBD->consultar2_no_crea_cierra($query_verificar_nombre_archivo);
	
	//si el archivo esa siendo validado actualmente lo marca como si se hubiese generado
	if($esta_validando_actualmente==true)
	{
		$ya_se_reporto_archivo=true;
	}
	
	$query_es_administrador="SELECT * FROM gios_perfiles_sistema WHERE id_perfil='$perfil_usuario_actual' ; ";
	$resultado_verificar_es_administrador=$coneccionBD->consultar2_no_crea_cierra($query_es_administrador);
	
	$mensajes_error_bd="";
	
	$es_administrador=false;
	if(count($resultado_verificar_es_administrador)>0 && $esta_validando_actualmente==false)
	{
		$cadena_permisos=$resultado_verificar_es_administrador[0]["permisos_administrador"];
		$array_permisos=explode(";",$cadena_permisos);
		if(isset($array_permisos[0]))
		{
			if($array_permisos[0]=="SI")
			{
				$es_administrador=true;
				
				if($accion=="validada" && count($resultado_verificar_nombre_archivo)>0 )
				{
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_consulta_reporte_obligatorio_erc2463_exitoso ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_erc='$nombre_zip_sin_consecutivo' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_erc='$nombre_zip_sin_consecutivo' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_dupl_incluidos_excluidos_hor_erc2463 ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_erc='$nombre_zip_sin_consecutivo' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR ".$error_bd_seq."<br>";
					}
					
				}
				else if(count($resultado_verificar_nombre_archivo)>0 )
				{
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_consulta_reporte_obligatorio_erc2463_rechazado ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_erc='$nombre_zip_sin_consecutivo' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR ".$error_bd_seq."<br>";
					}
				}
			}
		}
	}
	
	if(count($resultado_verificar_nombre_archivo)>0 && is_array($resultado_verificar_nombre_archivo) && $es_administrador==false)
	{
		$ya_se_reporto_archivo=true;
		$fecha_generacion_si_se_reporto=$resultado_verificar_nombre_archivo[0]["fecha_de_generacion"];
	}
	//fin verificar
	
	$array_rutas_archivos_generados=array();	
	$bool_hubo_error_query=false;
	
	if($ya_se_reporto_archivo==false)
	{
		$query_insert_esta_siendo_procesado="";
		$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_2463_esta_consolidando_ro_actualmente ";
		$query_insert_esta_siendo_procesado.=" ( ";
		$query_insert_esta_siendo_procesado.=" cod_eapb,";
		$query_insert_esta_siendo_procesado.=" nombre_archivo,";
		$query_insert_esta_siendo_procesado.=" fecha_corte_periodo_consolidar,";
		$query_insert_esta_siendo_procesado.=" fecha_validacion,";
		$query_insert_esta_siendo_procesado.=" hora_validacion,";
		$query_insert_esta_siendo_procesado.=" nick_usuario,";
		$query_insert_esta_siendo_procesado.=" esta_ejecutando,";
		$query_insert_esta_siendo_procesado.=" se_pudo_descargar,";
		$query_insert_esta_siendo_procesado.=" mensaje_estado_registros";
		$query_insert_esta_siendo_procesado.=" ) ";
		$query_insert_esta_siendo_procesado.=" VALUES ";
		$query_insert_esta_siendo_procesado.=" ( ";
		$query_insert_esta_siendo_procesado.=" '".$cod_eapb."',  ";
		$query_insert_esta_siendo_procesado.=" '".$nombre_zip_sin_consecutivo."',  ";
		$query_insert_esta_siendo_procesado.=" '".$fecha_corte_bd."',  ";
		$query_insert_esta_siendo_procesado.=" '".$fecha_actual."',  ";
		$query_insert_esta_siendo_procesado.=" '".$tiempo_actual."',  ";
		$query_insert_esta_siendo_procesado.=" '".$nick_user."',  ";
		$query_insert_esta_siendo_procesado.=" 'SI',  ";
		$query_insert_esta_siendo_procesado.=" 'NO',  ";
		$query_insert_esta_siendo_procesado.=" 'inicio el proceso'  ";
		$query_insert_esta_siendo_procesado.=" ) ";
		$query_insert_esta_siendo_procesado.=" ; ";
		$error_bd="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_esta_siendo_procesado, $error_bd);
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  2463 ');</script>";
			}
		}
		
		//crea directorio para evitar que se descarguen archivos pasados
		$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
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
		
		//GENERANDO ERC
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW vsroerc_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_datos_reporte_obligatorio.=" AS  ";	
		if($accion=="validada")
		{
			$sql_datos_reporte_obligatorio .=" ( ";
			$sql_datos_reporte_obligatorio .=" SELECT * from gioss_tabla_registros_cargados_exito_r2463_erc WHERE ";			
			$sql_datos_reporte_obligatorio .=" (fecha_corte BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
			$sql_datos_reporte_obligatorio .=" AND ";
			$sql_datos_reporte_obligatorio .=" estado_registro='1' ";
			$sql_datos_reporte_obligatorio .=" AND ";
			$sql_datos_reporte_obligatorio .=" codigo_eapb_a_reportar='".$cod_eapb."' ";
			$sql_datos_reporte_obligatorio .=" UNION ";
			$sql_datos_reporte_obligatorio .=" SELECT * from gioss_tabla_registros_no_cargados_rechazados_r2463_erc WHERE ";			
			$sql_datos_reporte_obligatorio .=" (fecha_corte BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
			$sql_datos_reporte_obligatorio .=" AND ";
			$sql_datos_reporte_obligatorio .=" estado_registro='1' ";
			$sql_datos_reporte_obligatorio .=" AND ";
			$sql_datos_reporte_obligatorio .=" codigo_eapb_a_reportar='".$cod_eapb."' ";
			$sql_datos_reporte_obligatorio .=" ) ";
		}
		else if($accion=="rechazada")
		{
			$sql_datos_reporte_obligatorio .="SELECT * from gioss_tabla_registros_no_cargados_rechazados_r2463_erc WHERE ";
			
			$sql_datos_reporte_obligatorio .=" (fecha_corte   BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
			$sql_datos_reporte_obligatorio .=" AND ";
			$sql_datos_reporte_obligatorio .=" codigo_eapb_a_reportar='".$cod_eapb."' ";
		}
		
		$sql_datos_reporte_obligatorio .=" ORDER BY numero_secuencia asc,fila asc ";
		
		
		$sql_datos_reporte_obligatorio .=";";
		$error_bd_seq="";
		$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM vsroerc_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$resultado_query_numero_registros=$coneccionBD->consultar2_no_crea_cierra($sql_query_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		$ruta_escribir_archivo=$rutaTemporal.$nombre_zip_sin_consecutivo.".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		fclose($reporte_obligatorio_file);
		
		$numero_registros_bloque=1000;
		$contador_offset=0;
		$limite=0;
		$regimen_almacenado="";
		
		
		//SUBIDA A TABLA CONSULTA ERC Y ESCRIBIENDO EN EL ARCHIVO
		$fue_cerrada_la_gui=false;
		while($contador_offset<$numero_registros)
		{
			if($fue_cerrada_la_gui==false)
			{
			    if(connection_aborted()==true)
			    {
				$fue_cerrada_la_gui=true;
			    }
			}//fin if verifica si el usuario cerro la pantalla
			
			//PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
			$verificar_si_ejecucion_fue_cancelada="";
			$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_2463_esta_consolidando_ro_actualmente ";
			$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
			$verificar_si_ejecucion_fue_cancelada.=" AND cod_eapb='".$cod_eapb."' ";
			$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
			$verificar_si_ejecucion_fue_cancelada.=" ; ";
			$error_bd_seq="";
			$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd_seq);		
			if($error_bd_seq!="")
			{
			    if($fue_cerrada_la_gui==false)
			    {
				echo "<script>alert(' error al consultar si se cancelo la ejecucion ');</script>";
			    }
			}
			
			if(count($resultados_si_ejecucion_fue_cancelada)>0 && is_array($resultados_si_ejecucion_fue_cancelada))
			{
			    $esta_ejecutando=$resultados_si_ejecucion_fue_cancelada[0]["esta_ejecutando"];
			    if($esta_ejecutando=="NO")
			    {
				exit(0);
			    }
			}
			//FIN PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
			
			$limite=$numero_registros_bloque;
				
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vsroerc_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultados_query_vih=$coneccionBD->consultar2_no_crea_cierra($sql_query_busqueda);
			foreach($resultados_query_vih as $linea_consulta)
			{
				//PARTE ACOMODACION DEL REGIMEN
				$regimen_afiliacion=$linea_consulta["campo_erc_de_numero_orden_2"];
				$sql_insert_consulta_reporte_obligatorio="";
				$temp_regimen_almacenado=1;
				if($regimen_afiliacion=="C")
				{
					$sql_insert_consulta_reporte_obligatorio.="'1',";
					$temp_regimen_almacenado=1;
				}
				else if($regimen_afiliacion=="S")
				{
					$sql_insert_consulta_reporte_obligatorio.="'2',";
					$temp_regimen_almacenado=2;
				}
				else if($regimen_afiliacion=="E")
				{
					$sql_insert_consulta_reporte_obligatorio.="'3',";
					$temp_regimen_almacenado=3;
				}
				else if($regimen_afiliacion=="P")
				{
					$sql_insert_consulta_reporte_obligatorio.="'4',";
					$temp_regimen_almacenado=4;
				}
				else if($regimen_afiliacion=="N")
				{
					$sql_insert_consulta_reporte_obligatorio.="'4',";
					$temp_regimen_almacenado=5;
				}
				else if( ctype_digit($regimen_afiliacion))
				{
					$sql_insert_consulta_reporte_obligatorio.="'".$regimen_afiliacion."',";
					if(ctype_digit($regimen_afiliacion))
					{
						$temp_regimen_almacenado=intval($regimen_afiliacion);
					}
				}
				$regimen_almacenado=$temp_regimen_almacenado;
				//echo "<script>alert('$regimen_almacenado');</script>";
				//FIN PARTE ACOMODACION DEL REGIMEN
				
				//SEPARA DUPLICADOS
				$sql_verifica_existencia_duplicado="";
				$sql_verifica_existencia_duplicado="";
				
				/*
				$sql_delete_duplicados="";
				$sql_delete_duplicados.="DELETE FROM ";
				if($accion=="validada")
				{
					$sql_delete_duplicados.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso ";
				}
				else if($accion=="rechazada")
				{
					$sql_delete_duplicados.=" gioss_consulta_reporte_obligatorio_erc2463_rechazado ";
				}
				$sql_delete_duplicados.="WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
				$sql_delete_duplicados.=" AND ";
				$sql_delete_duplicados.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
				$sql_delete_duplicados.=" AND ";
				$sql_delete_duplicados.=" campo_erc_de_numero_orden_4='".trim($linea_consulta["campo_erc_de_numero_orden_4"])."'  ";
				$sql_delete_duplicados.=" AND ";
				$sql_delete_duplicados.=" campo_erc_de_numero_orden_5='".trim($linea_consulta["campo_erc_de_numero_orden_5"])."'  ";
				$sql_delete_duplicados.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_delete_duplicados, $error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.=$error_bd_seq."<br>";
				}
				*/
				
				$hubo_duplicado=false;
				
				//SEPARA OS DUPLICADOS QUE ENCUENTRA
				//Y LOS ARREGLA DEPENDIENDO EL VALOR DE SUS CAMPOS				
				if($accion=="validada")
				{
					//CASO 1 SE CONSULTA EN TABLA DUPLICADOS SI HAY COINCIDENCIAS
					$registros_desde_tabla_duplicados=array();
					
					$sql_hubo_en_duplicados="";
					$sql_hubo_en_duplicados.=" SELECT * FROM ";
					$sql_hubo_en_duplicados.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado ";
					$sql_hubo_en_duplicados.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
					$sql_hubo_en_duplicados.=" AND ";
					$sql_hubo_en_duplicados.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
					$sql_hubo_en_duplicados.=" AND ";
					$sql_hubo_en_duplicados.=" nombre_archivo_erc='".$nombre_zip_sin_consecutivo."'  ";
					$sql_hubo_en_duplicados.=" AND ";
					$sql_hubo_en_duplicados.=" campo_erc_de_numero_orden_4='".trim($linea_consulta["campo_erc_de_numero_orden_4"])."'  ";
					$sql_hubo_en_duplicados.=" AND ";
					$sql_hubo_en_duplicados.=" campo_erc_de_numero_orden_5='".trim($linea_consulta["campo_erc_de_numero_orden_5"])."'  ";
					$sql_hubo_en_duplicados.=" AND ";
					$sql_hubo_en_duplicados.=" ( ";
					$sql_hubo_en_duplicados.=" (numero_registro<>'".$linea_consulta["fila"]."' AND numero_secuencia='".$linea_consulta["numero_secuencia"]."' ) ";					
					$sql_hubo_en_duplicados.=" OR ";
					$sql_hubo_en_duplicados.=" (numero_secuencia<>'".$linea_consulta["numero_secuencia"]."')  ";
					$sql_hubo_en_duplicados.=" ) ";
					$sql_hubo_en_duplicados.=" ; ";
					$error_bd_seq="";
					$registros_desde_tabla_duplicados=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_hubo_en_duplicados, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR al consultar desde la tabla de duplicados  dejado: ".$error_bd_seq."<br>";
					}
					
					//subiendo a duplicados
					if(count($registros_desde_tabla_duplicados)>0 && is_array($registros_desde_tabla_duplicados))
					{
						$hubo_duplicado=true;
						$hubo_al_menos_un_duplicado=true;
						
						$sql_insert_duplicados_rep_oblig_erc="";
						$sql_insert_duplicados_rep_oblig_erc.=" INSERT INTO ";
						$sql_insert_duplicados_rep_oblig_erc.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado ";				
						$sql_insert_duplicados_rep_oblig_erc.=" ( ";				
						$cont_orden_campo_erc=0;
						while($cont_orden_campo_erc<=118)
						{
							$sql_insert_duplicados_rep_oblig_erc.=" campo_erc_de_numero_orden_".$cont_orden_campo_erc." , ";
							$cont_orden_campo_erc++;
						}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar				
						$sql_insert_duplicados_rep_oblig_erc.=" numero_secuencia, ";
						$sql_insert_duplicados_rep_oblig_erc.=" numero_registro, ";
						$sql_insert_duplicados_rep_oblig_erc.=" regimen, ";
						$sql_insert_duplicados_rep_oblig_erc.=" fecha_corte_reporte, ";
						$sql_insert_duplicados_rep_oblig_erc.=" fecha_de_generacion, ";
						$sql_insert_duplicados_rep_oblig_erc.=" hora_generacion, ";
						$sql_insert_duplicados_rep_oblig_erc.=" codigo_entidad_eapb_generadora, ";
						$sql_insert_duplicados_rep_oblig_erc.=" codigo_entidad_prestadora, ";
						$sql_insert_duplicados_rep_oblig_erc.=" nombre_archivo_erc ";
						$sql_insert_duplicados_rep_oblig_erc.=" ) ";
						$sql_insert_duplicados_rep_oblig_erc.=" VALUES ";
						$sql_insert_duplicados_rep_oblig_erc.=" ( ";				
						$cont_orden_campo_erc=0;
						while($cont_orden_campo_erc<=118)
						{
							$sql_insert_duplicados_rep_oblig_erc.="'".alphanumericAndSpace($linea_consulta["campo_erc_de_numero_orden_".$cont_orden_campo_erc])."',";
							$cont_orden_campo_erc++;
						}//fin while con los valores de los campos 2463 a insertar en la tabla de reporte obligatorio
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["numero_secuencia"]."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["fila"]."',";				
						$sql_insert_duplicados_rep_oblig_erc.="'".$regimen_almacenado."',";								
						$sql_insert_duplicados_rep_oblig_erc.="'".$fecha_corte_bd."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$fecha_actual."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$tiempo_actual."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["codigo_eapb_a_reportar"]."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["codigo_prestador_reportante"]."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$nombre_zip_sin_consecutivo."'";
						$sql_insert_duplicados_rep_oblig_erc.=" ) ";
						$sql_insert_duplicados_rep_oblig_erc.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_duplicados_rep_oblig_erc, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.=" ERROR Al subir en la tabla DUPLICADOS para reporte obligatorio caso 1: ".$error_bd_seq."<br>";
						}
					}//fin if si hay duplicados
					//fin subiendo a duplicados					
					//FIN CASO 1 SE CONSULTA EN TABLA DUPLICADOS SI HAY COINCIDENCIAS
					
					//CASO 2 NO ENCONTRO NADA EN LA TABLA DUPLICADOS PERO ENCONTRO
					//DUPLICADO EN LA TABLA DE CONSULTA REPORTE OBLIGATORIO
					//tiene los valores traidos de bd
					$registro_ultimo_dejado=array();					
					
					$sql_query_ultimo_dejado="";
					$sql_query_ultimo_dejado.=" SELECT * FROM ";
					$sql_query_ultimo_dejado.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso ";
					$sql_query_ultimo_dejado.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
					$sql_query_ultimo_dejado.=" AND ";
					$sql_query_ultimo_dejado.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
					$sql_query_ultimo_dejado.=" AND ";
					$sql_query_ultimo_dejado.=" nombre_archivo_erc='".$nombre_zip_sin_consecutivo."'  ";
					$sql_query_ultimo_dejado.=" AND ";
					$sql_query_ultimo_dejado.=" campo_erc_de_numero_orden_4='".trim($linea_consulta["campo_erc_de_numero_orden_4"])."'  ";
					$sql_query_ultimo_dejado.=" AND ";
					$sql_query_ultimo_dejado.=" campo_erc_de_numero_orden_5='".trim($linea_consulta["campo_erc_de_numero_orden_5"])."'  ";
					$sql_query_ultimo_dejado.=" AND ";
					$sql_query_ultimo_dejado.=" ( ";
					$sql_query_ultimo_dejado.=" (numero_registro<>'".$linea_consulta["fila"]."' AND numero_secuencia='".$linea_consulta["numero_secuencia"]."' ) ";					
					$sql_query_ultimo_dejado.=" OR ";
					$sql_query_ultimo_dejado.=" (numero_secuencia<>'".$linea_consulta["numero_secuencia"]."')  ";
					$sql_query_ultimo_dejado.=" ) ";
					$sql_query_ultimo_dejado.=" ; ";
					$error_bd_seq="";
					$registro_ultimo_dejado=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_query_ultimo_dejado, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR al consultar ultimo duplicado  dejado: ".$error_bd_seq."<br>";
					}
					
					//subiendo a duplicados
					if(count($registro_ultimo_dejado)>0 && is_array($registro_ultimo_dejado))
					{
						$hubo_duplicado=true;
						$hubo_al_menos_un_duplicado=true;
						
						$sql_insert_duplicados_rep_oblig_erc="";
						$sql_insert_duplicados_rep_oblig_erc.=" INSERT INTO ";
						$sql_insert_duplicados_rep_oblig_erc.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado ";				
						$sql_insert_duplicados_rep_oblig_erc.=" ( ";				
						$cont_orden_campo_erc=0;
						while($cont_orden_campo_erc<=118)
						{
							$sql_insert_duplicados_rep_oblig_erc.=" campo_erc_de_numero_orden_".$cont_orden_campo_erc." , ";
							$cont_orden_campo_erc++;
						}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar				
						$sql_insert_duplicados_rep_oblig_erc.=" numero_secuencia, ";
						$sql_insert_duplicados_rep_oblig_erc.=" numero_registro, ";
						$sql_insert_duplicados_rep_oblig_erc.=" regimen, ";
						$sql_insert_duplicados_rep_oblig_erc.=" fecha_corte_reporte, ";
						$sql_insert_duplicados_rep_oblig_erc.=" fecha_de_generacion, ";
						$sql_insert_duplicados_rep_oblig_erc.=" hora_generacion, ";
						$sql_insert_duplicados_rep_oblig_erc.=" codigo_entidad_eapb_generadora, ";
						$sql_insert_duplicados_rep_oblig_erc.=" codigo_entidad_prestadora, ";
						$sql_insert_duplicados_rep_oblig_erc.=" nombre_archivo_erc ";
						$sql_insert_duplicados_rep_oblig_erc.=" ) ";
						$sql_insert_duplicados_rep_oblig_erc.=" VALUES ";
						$sql_insert_duplicados_rep_oblig_erc.=" ( ";				
						$cont_orden_campo_erc=0;
						while($cont_orden_campo_erc<=118)
						{
							$sql_insert_duplicados_rep_oblig_erc.="'".alphanumericAndSpace($linea_consulta["campo_erc_de_numero_orden_".$cont_orden_campo_erc])."',";
							$cont_orden_campo_erc++;
						}//fin while con los valores de los campos 2463 a insertar en la tabla de reporte obligatorio
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["numero_secuencia"]."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["fila"]."',";	
						$sql_insert_duplicados_rep_oblig_erc.="'".$regimen_almacenado."',";								
						$sql_insert_duplicados_rep_oblig_erc.="'".$fecha_corte_bd."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$fecha_actual."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$tiempo_actual."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["codigo_eapb_a_reportar"]."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$linea_consulta["codigo_prestador_reportante"]."',";
						$sql_insert_duplicados_rep_oblig_erc.="'".$nombre_zip_sin_consecutivo."'";
						$sql_insert_duplicados_rep_oblig_erc.=" ) ";
						$sql_insert_duplicados_rep_oblig_erc.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_duplicados_rep_oblig_erc, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.=" ERROR Al subir en la tabla DUPLICADOS para reporte obligatorio caso 2.1: ".$error_bd_seq."<br>";
						}
						
						foreach($registro_ultimo_dejado as $ultimo_registro)
						{
							$sql_insert_duplicados_rep_oblig_erc="";
							$sql_insert_duplicados_rep_oblig_erc.=" INSERT INTO ";
							$sql_insert_duplicados_rep_oblig_erc.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado ";				
							$sql_insert_duplicados_rep_oblig_erc.=" ( ";				
							$cont_orden_campo_erc=0;
							while($cont_orden_campo_erc<=118)
							{
								$sql_insert_duplicados_rep_oblig_erc.=" campo_erc_de_numero_orden_".$cont_orden_campo_erc." , ";
								$cont_orden_campo_erc++;
							}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar				
							$sql_insert_duplicados_rep_oblig_erc.=" numero_secuencia, ";
							$sql_insert_duplicados_rep_oblig_erc.=" numero_registro, ";
							$sql_insert_duplicados_rep_oblig_erc.=" regimen, ";
							$sql_insert_duplicados_rep_oblig_erc.=" fecha_corte_reporte, ";
							$sql_insert_duplicados_rep_oblig_erc.=" fecha_de_generacion, ";
							$sql_insert_duplicados_rep_oblig_erc.=" hora_generacion, ";
							$sql_insert_duplicados_rep_oblig_erc.=" codigo_entidad_eapb_generadora, ";
							$sql_insert_duplicados_rep_oblig_erc.=" codigo_entidad_prestadora, ";
							$sql_insert_duplicados_rep_oblig_erc.=" nombre_archivo_erc ";
							$sql_insert_duplicados_rep_oblig_erc.=" ) ";
							$sql_insert_duplicados_rep_oblig_erc.=" VALUES ";
							$sql_insert_duplicados_rep_oblig_erc.=" ( ";				
							$cont_orden_campo_erc=0;
							while($cont_orden_campo_erc<=118)
							{
								$sql_insert_duplicados_rep_oblig_erc.="'".alphanumericAndSpace($ultimo_registro["campo_erc_de_numero_orden_".$cont_orden_campo_erc])."',";
								$cont_orden_campo_erc++;
							}//fin while con los valores de los campos 2463 a insertar en la tabla de reporte obligatorio
							$sql_insert_duplicados_rep_oblig_erc.="'".$ultimo_registro["numero_secuencia"]."',";
							$sql_insert_duplicados_rep_oblig_erc.="'".$ultimo_registro["numero_registro"]."',";	
							$sql_insert_duplicados_rep_oblig_erc.="'".$regimen_almacenado."',";								
							$sql_insert_duplicados_rep_oblig_erc.="'".$fecha_corte_bd."',";
							$sql_insert_duplicados_rep_oblig_erc.="'".$fecha_actual."',";
							$sql_insert_duplicados_rep_oblig_erc.="'".$tiempo_actual."',";
							$sql_insert_duplicados_rep_oblig_erc.="'".$ultimo_registro["codigo_entidad_eapb_generadora"]."',";
							$sql_insert_duplicados_rep_oblig_erc.="'".$ultimo_registro["codigo_entidad_prestadora"]."',";
							$sql_insert_duplicados_rep_oblig_erc.="'".$nombre_zip_sin_consecutivo."'";
							$sql_insert_duplicados_rep_oblig_erc.=" ) ";
							$sql_insert_duplicados_rep_oblig_erc.=" ; ";
							$error_bd_seq="";		
							$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_duplicados_rep_oblig_erc, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR Al subir en la tabla DUPLICADOS para reporte obligatorio caso 2.2: ".$error_bd_seq."<br>";
							}
						}//fin foreach
					}//fin if si hay duplicados
					//fin subiendo a duplicados
					
					
					
					$sql_delete_duplicados="";
					$sql_delete_duplicados.="DELETE FROM ";
					$sql_delete_duplicados.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso ";					
					$sql_delete_duplicados.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
					$sql_delete_duplicados.=" AND ";
					$sql_delete_duplicados.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
					$sql_delete_duplicados.=" AND ";
					$sql_delete_duplicados.=" nombre_archivo_erc='".$nombre_zip_sin_consecutivo."'  ";
					$sql_delete_duplicados.=" AND ";
					$sql_delete_duplicados.=" campo_erc_de_numero_orden_4='".trim($linea_consulta["campo_erc_de_numero_orden_4"])."'  ";
					$sql_delete_duplicados.=" AND ";
					$sql_delete_duplicados.=" campo_erc_de_numero_orden_5='".trim($linea_consulta["campo_erc_de_numero_orden_5"])."'  ";
					$sql_delete_duplicados.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_delete_duplicados, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR DUPLICADOS ".$error_bd_seq."<br>";
					}
					//FIN CASO 2 NO ENCONTRO NADA EN LA TABLA DUPLICADOS PERO ENCONTRO
					//DUPLICADO EN LA TABLA DE CONSULTA REPORTE OBLIGATORIO
					
				}//fin if si accion es validada
				
				//FIN SEPARA DUPLICADOS
				
				//PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				if($hubo_duplicado==false)
				{
					$sql_insert_consulta_reporte_obligatorio="";
					$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
					if($accion=="validada")
					{
						$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso ";
					}
					else if($accion=="rechazada")
					{
						$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_erc2463_rechazado ";
					}
					$sql_insert_consulta_reporte_obligatorio.=" ( ";
					$cont_orden_campo_erc=0;
					while($cont_orden_campo_erc<=118)
					{
						$sql_insert_consulta_reporte_obligatorio.=" campo_erc_de_numero_orden_".$cont_orden_campo_erc." , ";
						$cont_orden_campo_erc++;
					}
					$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
					$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
					$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
					$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
					$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
					$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
					$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
					$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
					$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_erc ";
					$sql_insert_consulta_reporte_obligatorio.=" ) ";
					$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
					$sql_insert_consulta_reporte_obligatorio.=" ( ";
					$cont_orden_campo_erc=0;
					while($cont_orden_campo_erc<=118)
					{
						$sql_insert_consulta_reporte_obligatorio.="'".alphanumericAndSpace($linea_consulta["campo_erc_de_numero_orden_".$cont_orden_campo_erc])."',";
						$cont_orden_campo_erc++;
					}
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["numero_secuencia"]."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["fila"]."',";					
					$sql_insert_consulta_reporte_obligatorio.="'".$regimen_almacenado."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$fecha_corte_bd."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_eapb_a_reportar"]."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$linea_consulta["codigo_prestador_reportante"]."',";
					$sql_insert_consulta_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
					$sql_insert_consulta_reporte_obligatorio.=" ) ";
					$sql_insert_consulta_reporte_obligatorio.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL INSERTAR EN LA TABLA DE CONSULTA REPORTE OBLIGATORIO CORRESPONDIENTE ".$error_bd_seq."<br>";
					}
				}//fin si no hubo duplicado
				//FIN PARTE SUBE A BD EN LA TABLA CONSULTA REPORTE OBLIGATORIO
				
				
				
			}//fin foreach
			
			if($fue_cerrada_la_gui==false)
			{
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere subiendo $contador_offset registros recuperados de $numero_registros a la tabla de reportes obligatorios.';</script>";
				ob_flush();
				flush();
			}
			//ACTUALIZA MENSAJE ESTADO EJECUCION
			$mensaje_para_estado_ejecucion="Por favor espere subiendo $contador_offset registros recuperados de $numero_registros a la tabla de reportes obligatorios.";
			
			$query_update_esta_siendo_procesado="";
			$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_consolidando_ro_actualmente ";
			$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_para_estado_ejecucion' ";
			$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
			$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
			$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
			$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
			$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
			$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
			$query_update_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  2463 ');</script>";
				}
			}
			//FIN ACTUALIZA MENSAJE ESTADO EJECUCION
			
			$contador_offset+=$numero_registros_bloque;
		}//fin if
		//FIN SUBIDA A TABLA CONSULTA  ERC Y ESCRIBIENDO EN EL ARCHIVO
		
		//echo "<script>alert('entes de entro correccion un dupl $hubo_al_menos_un_duplicado');</script>";
		
		//ARREGLO DE DUPLICADOS EN UNO SOLO
		$contador_duplicado_para_excluidos=0;
		if($accion=="validada" && $hubo_al_menos_un_duplicado==true)
		{
			//echo "<script>alert('entro correccion un dupl');</script>";
			$sql_vista_duplicados_reporte_obligatorio ="";
			$sql_vista_duplicados_reporte_obligatorio.="CREATE OR REPLACE VIEW vduplerc_".$nick_user."_".$tipo_id."_".$identificacion." ";
			$sql_vista_duplicados_reporte_obligatorio.=" AS  ";					
			$sql_vista_duplicados_reporte_obligatorio .="SELECT * from gioss_consulta_reporte_obligatorio_erc2463_exitoso_duplicado WHERE ";				
			$sql_vista_duplicados_reporte_obligatorio .=" fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual'  ";			
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" nombre_archivo_erc='".$nombre_zip_sin_consecutivo."'  ";
			$sql_vista_duplicados_reporte_obligatorio.=" ORDER BY campo_erc_de_numero_orden_4 asc,campo_erc_de_numero_orden_5 asc,  numero_secuencia asc ";
			$sql_vista_duplicados_reporte_obligatorio.=";";
			$error_bd_seq="";		
			$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_reporte_obligatorio, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.=" ERROR al crear vista de duplicados: ".$error_bd_seq."<br>";
			}
			
			//numero de duplicados
			$sql_numero_de_duplicados="";
			$sql_numero_de_duplicados.=" SELECT count(*) as numero_registros FROM vduplerc_".$nick_user."_".$tipo_id."_".$identificacion."  ; ";
			$error_bd_seq="";
			$array_numero_de_duplicados=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_duplicados, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados  dejado: ".$error_bd_seq."<br>";
			}
			
			$numero_duplicados=0;
			
			if(count($array_numero_de_duplicados)>0 && is_array($array_numero_de_duplicados))
			{
				$numero_duplicados=$array_numero_de_duplicados[0]["numero_registros"];
			}
			//fin numero de duplicados
			
			$limite_duplicados=0;
			$contador_offset_duplicados=0;
			$numero_registros_bloque_duplicados=1;
			if($numero_duplicados>0)
			{
				$fue_cerrada_la_gui_2=false;
				while($contador_offset_duplicados<$numero_duplicados)
				{
					if($fue_cerrada_la_gui_2==false)
					{
					    if(connection_aborted()==true)
					    {
						$fue_cerrada_la_gui_2=true;
					    }
					}//fin if verifica si el usuario cerro la pantalla
					
					//PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
					$verificar_si_ejecucion_fue_cancelada="";
					$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_2463_esta_consolidando_ro_actualmente ";
					$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND cod_eapb='".$cod_eapb."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" ; ";
					$error_bd_seq="";
					$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd_seq);		
					if($error_bd_seq!="")
					{
					    if($fue_cerrada_la_gui_2==false)
					    {
						echo "<script>alert(' error al consultar si se cancelo la ejecucion ');</script>";
					    }
					}
					
					if(count($resultados_si_ejecucion_fue_cancelada)>0 && is_array($resultados_si_ejecucion_fue_cancelada))
					{
					    $esta_ejecutando=$resultados_si_ejecucion_fue_cancelada[0]["esta_ejecutando"];
					    if($esta_ejecutando=="NO")
					    {
						exit(0);
					    }
					}
					//FIN PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
					
					$limite_duplicados=$numero_registros_bloque_duplicados;
						
					if( ($contador_offset_duplicados+$numero_registros_bloque_duplicados)>=$numero_duplicados)
					{
						$limite_duplicados=$numero_registros_bloque_duplicados+($numero_duplicados-$contador_offset_duplicados);
					}
					
					$sql_query_busqueda_duplicados="";
					$sql_query_busqueda_duplicados.="SELECT * FROM vduplerc_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite_duplicados OFFSET $contador_offset_duplicados;  ";
					$error_bd_seq="";
					$resultados_query_erc2463_duplicados=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda_duplicados,$error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.="ERROR AL CONSULTAR de vista de los duplicados: ".$error_bd_seq."<br>";
					}
					
					if(count($resultados_query_erc2463_duplicados)>0 && is_array($resultados_query_erc2463_duplicados))
					{
						foreach($resultados_query_erc2463_duplicados as $duplicado_actual)
						{
							$tipo_id_duplicado_actual=trim($duplicado_actual["campo_erc_de_numero_orden_4"]);
							$numero_id_duplicado_actual=trim($duplicado_actual["campo_erc_de_numero_orden_5"]);
							
							$registro_si_ya_se_proceso=array();
							$bool_ya_se_proceso=false;
					
							$sql_query_ya_se_proceso="";
							$sql_query_ya_se_proceso.=" SELECT * FROM ";
							$sql_query_ya_se_proceso.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso ";
							$sql_query_ya_se_proceso.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
							$sql_query_ya_se_proceso.=" AND ";
							$sql_query_ya_se_proceso.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
							$sql_query_ya_se_proceso.=" AND ";
							$sql_query_ya_se_proceso.=" nombre_archivo_erc='".$nombre_zip_sin_consecutivo."'  ";
							$sql_query_ya_se_proceso.=" AND ";
							$sql_query_ya_se_proceso.=" campo_erc_de_numero_orden_4='$tipo_id_duplicado_actual'  ";
							$sql_query_ya_se_proceso.=" AND ";
							$sql_query_ya_se_proceso.=" campo_erc_de_numero_orden_5='$numero_id_duplicado_actual'  ";
							$sql_query_ya_se_proceso.=" ; ";
							$error_bd_seq="";
							$registro_si_ya_se_proceso=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_query_ya_se_proceso, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR al verificar si ya se proceso duplicado : ".$error_bd_seq."<br>";
							}
							
							if(count($registro_si_ya_se_proceso)>0 && is_array($registro_si_ya_se_proceso))
							{
								if($numero_id_duplicado_actual==trim($registro_si_ya_se_proceso[0]["campo_erc_de_numero_orden_5"]))
								{
									$bool_ya_se_proceso=true;
								}
							}
							
							if($bool_ya_se_proceso==false)
							{
								$nombre_vista_duplicados_del_duplicado_actual="vduplacterc_".$nick_user."_".$tipo_id."_".$identificacion;
								
								$sql_vista_duplicado_actual ="";
								$sql_vista_duplicado_actual.="CREATE OR REPLACE VIEW $nombre_vista_duplicados_del_duplicado_actual ";
								$sql_vista_duplicado_actual.=" AS  ";					
								$sql_vista_duplicado_actual .="SELECT * from vduplerc_".$nick_user."_".$tipo_id."_".$identificacion." WHERE ";				
								$sql_vista_duplicado_actual .=" fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual'  ";			
								$sql_vista_duplicado_actual.=" AND ";
								$sql_vista_duplicado_actual.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
								$sql_vista_duplicado_actual.=" AND ";
								$sql_vista_duplicado_actual.=" nombre_archivo_erc='".$nombre_zip_sin_consecutivo."'  ";
								$sql_vista_duplicado_actual.=" AND ";
								$sql_vista_duplicado_actual.=" campo_erc_de_numero_orden_4='".$tipo_id_duplicado_actual."'  ";
								$sql_vista_duplicado_actual.=" AND ";
								$sql_vista_duplicado_actual.=" campo_erc_de_numero_orden_5='".$numero_id_duplicado_actual."'  ";
								$sql_vista_duplicado_actual.=" ORDER BY campo_erc_de_numero_orden_4 asc,campo_erc_de_numero_orden_5 asc, numero_secuencia asc ";
								$sql_vista_duplicado_actual.=";";
								$error_bd_seq="";		
								$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicado_actual, $error_bd_seq);
								if($error_bd_seq!="")
								{
									$mensajes_error_bd.=" ERROR al crear vista de duplicados del duplicado actual: ".$error_bd_seq."<br>";
								}
								
								//numero de duplicados del duplicado
								$sql_numero_de_duplicados_de_duplicado="";
								$sql_numero_de_duplicados_de_duplicado.=" SELECT count(*) as numero_registros FROM $nombre_vista_duplicados_del_duplicado_actual  ; ";
								$error_bd_seq="";
								$array_numero_de_duplicados_de_duplicado=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_duplicados_de_duplicado, $error_bd_seq);
								if($error_bd_seq!="")
								{
									$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados del duplicado: ".$error_bd_seq."<br>";
								}
								
								$numero_duplicados_de_duplicado=0;
								if(count($array_numero_de_duplicados_de_duplicado)>0 && is_array($array_numero_de_duplicados_de_duplicado))
								{
									$numero_duplicados_de_duplicado=$array_numero_de_duplicados_de_duplicado[0]["numero_registros"];
								}
								//numeros de duplicados del duplicado
								
								//parte donde se procesaran los duplicados								
								$numero_secuencia_para_procesado="";
								$numero_registro_para_procesado="";
								$cod_prestador_para_procesado="";
								//en la funcion se hara falso si no se proceso los duplicados al haber campos vacios
								$bool_fueron_procesados_duplicados_en_un_registro=true;
								
								$array_campos_procesados_de_los_duplicados_del_duplicado=array();
								$array_campos_procesados_de_los_duplicados_del_duplicado=reparacion_campos_duplicados($tipo_id_duplicado_actual,
																		      $numero_id_duplicado_actual,
																		      $fecha_actual,
																		      $tiempo_actual,
																		      $nick_user,
																		      $identificacion,
																		      $tipo_id,
																		      $numero_duplicados_de_duplicado,
																		      $nombre_vista_duplicados_del_duplicado_actual,
																		      $numero_secuencia_para_procesado,
																		      $numero_registro_para_procesado,
																		      $cod_prestador_para_procesado,
																		      $bool_fueron_procesados_duplicados_en_un_registro,
																		      $contador_offset_duplicados,
																		      $contador_duplicado_para_excluidos,
																		      $mensajes_error_bd,
																		      $coneccionBD);
								//fin parte donde se procesaran los duplicados
								
								//insertando registro procesado
								if($bool_fueron_procesados_duplicados_en_un_registro==true)
								{
									$sql_insert_procesado_en_reporte_obligatorio="";
									$sql_insert_procesado_en_reporte_obligatorio.=" INSERT INTO ";
									$sql_insert_procesado_en_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_erc2463_exitoso ";
									$sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
									$cont_orden_campo_erc=0;
									while($cont_orden_campo_erc<=118)
									{
										$sql_insert_procesado_en_reporte_obligatorio.=" campo_erc_de_numero_orden_".$cont_orden_campo_erc." , ";
										$cont_orden_campo_erc++;
									}//fin while para nombres columnas de bd correspondientes a los campos de 2463 a insertar				
									$sql_insert_procesado_en_reporte_obligatorio.=" numero_secuencia, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" numero_registro, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" regimen, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" fecha_corte_reporte, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" fecha_de_generacion, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" hora_generacion, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_prestadora, ";
									$sql_insert_procesado_en_reporte_obligatorio.=" nombre_archivo_erc ";
									$sql_insert_procesado_en_reporte_obligatorio.=" ) ";
									$sql_insert_procesado_en_reporte_obligatorio.=" VALUES ";
									$sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
									$cont_orden_campo_erc=0;
									while($cont_orden_campo_erc<=118)
									{
										$sql_insert_procesado_en_reporte_obligatorio.="'".alphanumericAndSpace($array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_erc])."',";
										$cont_orden_campo_erc++;
									}//fin while con los valores de los campos 2463 a insertar en la tabla de reporte obligatorio
									$sql_insert_procesado_en_reporte_obligatorio.="'".$numero_secuencia_para_procesado."',";
									$sql_insert_procesado_en_reporte_obligatorio.="'".$numero_registro_para_procesado."',";
									$regimen_afiliacion="1";
									$regimen_almacenado=$regimen_afiliacion;				
									$sql_insert_procesado_en_reporte_obligatorio.="'".$regimen_afiliacion."',";								
									$sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_corte_bd."',";
									$sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_actual."',";
									$sql_insert_procesado_en_reporte_obligatorio.="'".$tiempo_actual."',";
									$sql_insert_procesado_en_reporte_obligatorio.="'".$cod_eapb."',";
									$sql_insert_procesado_en_reporte_obligatorio.="'".$cod_prestador_para_procesado."',";
									$sql_insert_procesado_en_reporte_obligatorio.="'".$nombre_zip_sin_consecutivo."'";
									$sql_insert_procesado_en_reporte_obligatorio.=" ) ";
									$sql_insert_procesado_en_reporte_obligatorio.=" ; ";
									$error_bd_seq="";		
									$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_procesado_en_reporte_obligatorio, $error_bd_seq);
									if($error_bd_seq!="")
									{
										$mensajes_error_bd.=" ERROR Al subir en la tabla reporte obligatorio: ".$error_bd_seq."<br>";
									}
								}//fin if si fueron procesados duplicados inserta el porcesado en la tabla de archivos reportados obligatorios exitosos de 2463
								//fin insertando registro procesado
								
								//borrando vistas
								$sql_borrar_vista_duplicados_del_duplicado_actual="";
								$sql_borrar_vista_duplicados_del_duplicado_actual.=" DROP VIEW $nombre_vista_duplicados_del_duplicado_actual ; ";							
								$error_bd="";		
								$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_del_duplicado_actual, $error_bd);		
								if($error_bd!="")
								{
									if(connection_aborted()==false)
									{
										echo "<script>alert('error al borrar la vista duplicados del duplicado actual');</script>";
									}
								}
								//fin borrando vistas
							}//fin if si el duplicado no se ha procesado
							
						}//fin foreach por lo general sera un solo registro a l ves extraido de la base de datos
					}//fin if hay resultados
					
					if($fue_cerrada_la_gui_2==false)
					{
						echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere arreglando $contador_offset_duplicados registros duplicados de $numero_duplicados duplicados encontrados.';</script>";
						ob_flush();
						flush();
					}
					
					//ACTUALIZA MENSAJE ESTADO EJECUCION
					$mensaje_para_estado_ejecucion="Por favor espere arreglando $contador_offset_duplicados registros duplicados de $numero_duplicados duplicados encontrados.";
					
					$query_update_esta_siendo_procesado="";
					$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_consolidando_ro_actualmente ";
					$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_para_estado_ejecucion' ";
					$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
					$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
					$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
					$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
					$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
					$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
					$query_update_esta_siendo_procesado.=" ; ";
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
					if($error_bd!="")
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  2463 ');</script>";
						}
					}
					//FIN ACTUALIZA MENSAJE ESTADO EJECUCION
					
					//incremento contador
					$contador_offset_duplicados+=$numero_registros_bloque_duplicados;
				}//fin while
			}//fin if si hay archivos duplicados
		}//fin if si se genera reporte para un archivo validado
		//FIN ARREGLO DDE DUPLICADOS EN UNO SOLO
		
		//PARTE REGISTRO EN LA TABLA DE REPORTADO
		if($mensajes_error_bd=="" && count($array_rutas_archivos_generados)>0 && $numero_registros>0)
		{
			$sql_insert_verificador_reportados="";
			$sql_insert_verificador_reportados.=" INSERT INTO gioss_archivos_obligatorios_reportados_erc ";
			$sql_insert_verificador_reportados.=" ( ";
			$sql_insert_verificador_reportados.=" usuario_que_genero, ";
			$sql_insert_verificador_reportados.=" nombre_archivo_erc, ";
			$sql_insert_verificador_reportados.=" fecha_de_generacion, ";
			$sql_insert_verificador_reportados.=" hora_generacion, ";
			$sql_insert_verificador_reportados.=" fecha_corte_reporte, ";
			$sql_insert_verificador_reportados.=" regimen, ";
			$sql_insert_verificador_reportados.=" codigo_entidad_eapb_generadora, ";
			$sql_insert_verificador_reportados.=" estado_informacion, ";
			$sql_insert_verificador_reportados.=" cantidad_registros_reportados ";
			$sql_insert_verificador_reportados.=" ) ";
			$sql_insert_verificador_reportados.=" VALUES ";
			$sql_insert_verificador_reportados.=" ( ";
			$sql_insert_verificador_reportados.="'".$nick_user."',";
			$sql_insert_verificador_reportados.="'".$nombre_zip_sin_consecutivo."',";
			$sql_insert_verificador_reportados.="'".$fecha_actual."',";
			$sql_insert_verificador_reportados.="'".$tiempo_actual."',";
			$sql_insert_verificador_reportados.="'".$fecha_corte_bd."',";
			$sql_insert_verificador_reportados.="'".$regimen_almacenado."',";
			$sql_insert_verificador_reportados.="'".$cod_eapb."',";
			if($accion=="validada")
			{
				$sql_insert_verificador_reportados.="'1',";
			}
			else if($accion=="rechazada")
			{
				$sql_insert_verificador_reportados.="'2',";
			}
			$sql_insert_verificador_reportados.="'".$numero_registros."'";
			$sql_insert_verificador_reportados.=" ) ";
			$sql_insert_verificador_reportados.=" ; ";
			$bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($sql_insert_verificador_reportados, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.="ERROR AL SUBIR INFO EN LA TABLA DE REPORTADO ".$error_bd_seq."<br>";
			}
		}
		//FIN PARTE REGISTRO EN LA TABLA DE REPORTADO
		
		//FIN GENERANDO ERC
		
			
		//PARTE ESCRIBE CSV
		$estado_validacion_seleccionado=$accion;
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vcroerc_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_erc2463_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_erc='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc  ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_erc2463_rechazado WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_erc='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc  ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vcroerc_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2_no_crea_cierra($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		//echo "<script>alert('$nombre_zip_sin_consecutivo $numero_registros');</script>";
		
		$cont_linea=1;
		$contador_offset=0;
		$flag_creacion_archivo=false;
		$limite=0;
		$flag_para_salto_linea_inicial=false;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
			
			//echo "<script>alert('$nombre_zip_sin_consecutivo $numero_registros $contador_offset $limite');</script>";
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vcroerc_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_reporte_obligatorio=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);
			if($error_bd_seq!="")
			{
			    $mensajes_error_bd.="ERROR AL CONSULTAR de vista_consulta: ".$error_bd_seq."<br>";
			}
		
			if(count($resultado_query_reporte_obligatorio)>0)
			{
				//echo "<script>alert('$nombre_zip_sin_consecutivo ".count($resultado_query_reporte_obligatorio)."');</script>";
				$ruta_escribir_archivo=$rutaTemporal.$nombre_zip_sin_consecutivo.".txt";
				
				//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				if($flag_creacion_archivo==false)
				{
					if(file_exists($ruta_escribir_archivo))
					{
						unlink($ruta_escribir_archivo);
					}
					$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
					fclose($reporte_obligatorio_file);
				}
				
				$flag_creacion_archivo=true;
				//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				/*
				$titulos="";
				$titulos.="consecutivo,nombre archivo,codigo tipo inconsistencia,tipo inconsistencia,codigo grupo inconsistencia,grupo inconsistencia,";
				$titulos.="codigo detalle inconsistencia,detalle inconsistencia, numero de linea, numero de campo";
				fwrite($reporte_obligatorio_file, $titulos."\n");
				*/
				
				$fue_cerrada_la_gui_3=false;
				foreach($resultado_query_reporte_obligatorio as $resultado)
				{
					if($fue_cerrada_la_gui_3==false)
					{
					    if(connection_aborted()==true)
					    {
						$fue_cerrada_la_gui_3=true;
					    }
					}//fin if verifica si el usuario cerro la pantalla
					
					//PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
					$verificar_si_ejecucion_fue_cancelada="";
					$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_2463_esta_consolidando_ro_actualmente ";
					$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND cod_eapb='".$cod_eapb."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
					$verificar_si_ejecucion_fue_cancelada.=" ; ";
					$error_bd_seq="";
					$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd_seq);		
					if($error_bd_seq!="")
					{
					    if($fue_cerrada_la_gui_3==false)
					    {
						echo "<script>alert(' error al consultar si se cancelo la ejecucion ');</script>";
					    }
					}
					
					if(count($resultados_si_ejecucion_fue_cancelada)>0 && is_array($resultados_si_ejecucion_fue_cancelada))
					{
					    $esta_ejecutando=$resultados_si_ejecucion_fue_cancelada[0]["esta_ejecutando"];
					    if($esta_ejecutando=="NO")
					    {
						exit(0);
					    }
					}
					//FIN PARTE VERIFICA SI FUE CANCELADA LA EJECUCION DEL SCRIPT
					
					$cadena_escribir_linea="";
					$cont_orden_campo_erc=0;
					while($cont_orden_campo_erc<=118)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.="\t";
						}
						$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_erc_de_numero_orden_".$cont_orden_campo_erc]);
						$cont_orden_campo_erc++;
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
					
					//echo "<script>alert('$cadena_escribir_linea');</script>";
					
					if($fue_cerrada_la_gui_3==false)
					{
						
						echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros.';</script>";
						ob_flush();
						flush();
						
					}
					
					//ACTUALIZA MENSAJE ESTADO EJECUCION
					$mensaje_para_estado_ejecucion="Por favor espere, $cont_linea registros recuperados de $numero_registros.";
					
					$query_update_esta_siendo_procesado="";
					$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_consolidando_ro_actualmente ";
					$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_para_estado_ejecucion' ";
					$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
					$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
					$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
					$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
					$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
					$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
					$query_update_esta_siendo_procesado.=" ; ";
					$error_bd="";
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
					if($error_bd!="")
					{
						if(connection_aborted()==false)
						{
							echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  2463 ');</script>";
						}
					}
					//FIN ACTUALIZA MENSAJE ESTADO EJECUCION
					
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hallo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		//FIN PARTE ESCRIBE CSV
		
		//borrando vistas
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW vsroerc_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		$sql_borrar_vistas.=" DROP VIEW vcroerc_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al borrar vistas');</script>";
			}
		}
		//fin borrando vistas
		
		/*
		//obteniendo consecutivo, veces que ha reportado la eapb?
		$consecutivo_num=0;
		$consecutivo="00";
		$query_obtener_consecutivo="SELECT * FROM gioss_numero_consecutivo_por_eapb WHERE cod_eapb='$cod_eapb' ; ";
		$resultados_consecutivo=$conexionbd->consultar2_no_crea_cierra($query_obtener_consecutivo);
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
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_actualizar_consecutivo, $error_bd);
				
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
			$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insertar_consecutivo, $error_bd);
			
			}
			catch (Exception $e) {}
		}//fin else si no existe inserta
		$consecutivo=$consecutivo_num;
		if(strlen($consecutivo)<2)
		{
			$consecutivo="0".$consecutivo;
		}
		//fin obteniendo consecutivo, veces que ha reportado la eapb?
		*/
		
		
		if(count($array_rutas_archivos_generados)>0 && $numero_registros>0)
		{
			//GENERANDO ARCHIVO ZIP
					
			$ruta_zip=$rutaTemporal.$nombre_zip_sin_consecutivo.'.zip';		
			
			$ruta_dat=$rutaTemporal.$nombre_zip_sin_consecutivo.".DAT";
			
			$result_zip = create_zip($array_rutas_archivos_generados,$ruta_zip);
			$result_dat = create_zip($array_rutas_archivos_generados,$ruta_dat);
			$mensaje.="<br>Se genero el consolidado de los archivos ERC 2463 de forma comprimida.";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('grilla').style.display='inline';</script>";
				echo "<script>var ruta_zip= '$ruta_zip'; </script>";
				echo "<script>var ruta_dat= '$ruta_dat'; </script>";
			}
			$resultadoDefinitivo.="<input type='button' value='Descargar Reporte Obligatorio .zip' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_zip);' />  ";
			$resultadoDefinitivo.="<input type='button' value='Descargar Reporte Obligatorio .DAT' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_dat);' />  ";
			
			
			//FIN GENERANDO ARCHIVO ZIP
			
			//YA NO ESTA EN USO EL ARCHIVO	    
		    
		    
			$query_update_esta_siendo_procesado="";
			$query_update_esta_siendo_procesado.=" UPDATE gioss_2463_esta_consolidando_ro_actualmente ";
			$query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
			$query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
			if($ruta_dat!="")
			{
			    //lleva la coma aca por si no esta vacio
			    $query_update_esta_siendo_procesado.=" , ruta_archivo_descarga_dat='$ruta_dat' ";
			}
			$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
			$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
			$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_zip_sin_consecutivo."'  ";
			$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
			$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
			$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
			$query_update_esta_siendo_procesado.=" ; ";
			$error_bd="";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
			if($error_bd!="")
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  2463 ');</script>";
				}
			}
			//FIN YA NO ESTA EN USO EL ARCHIVO
			
			// inicio envio de mail
	
		    $mail = new PHPMailer();
		    //inicio configuracion mail de acuerdoa archivo gloabl de configuracion
			if($USA_SMTP_CONFIGURACION_CORREO==true)
			{
			    
			 $mail->IsSMTP();
			 $mail->SMTPAuth = $SMTPAUTH_CONF_EMAIL_CE;
			 $mail->SMTPSecure = $SMTPSECURE_CONF_EMAIL_CE;
			 $mail->Host = $HOST_CONF_EMAIL;
			 $mail->Port = $PUERTO_CONF_EMAIL;
			 if($REQUIERE_AUTENTIFICACION_EMAIL==true)
			 {
			  $mail->Username = $USUARIO_CONF_EMAIL;
			  $mail->Password = $PASS_CONF_EMAIL;
			 }//fin if da el usuario y password
			}//fin if usa configuracion_global_email.php
		    $mail->From = "sistemagioss@gmail.com";
		    $mail->FromName = "GIOSS";
		    $mail->Subject = "Reporte Obligatorio ERC 2463 ";
		    $mail->AltBody = "Cordial saludo,\n El sistema ha generado el reporte obligatorio para el periodo escogido.";
	
		    $mail->MsgHTML("Cordial saludo,\n El sistema ha determinado que su archivo(s)  contiene diversos errores.<strong>GIOSS</strong>.");
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
				if(connection_aborted()==false)
				{
					echo "<script>alert('Se ha enviado una copia del consolidado a su correo $correo_electronico ')</script>";
				}
		    }
	
		    //fin envio de mail
			
		}
		else
		{
			//$mensaje.="<br>No se encontraron resultados.";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede generar el archivo, no existen datos asociados al a&ntildeo y periodo seleccionados.';</script>";
			}
		}
	
	}//fin if se reporto archivo es false (no se reporto un archivo con ese nombre)
	else
	{
		//$mensaje.="<br>El archivo ya fue reportado.";
		if($esta_validando_actualmente==false)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='El archivo obligatorio que intenta cargar, ya fue generado en la fecha $fecha_generacion_si_se_reporto. Por favor verifique en el \"Menu de consulta reporte obligatorio\" y descarguelo. ';</script>";
			}
		}
		else if($esta_validando_actualmente==true)
		{
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Ya hay un archivo obligatorio para 0247 que esta siendo consolidado actualmente, por favor cancele la ejecucion del anterior o espere a que termine. ';</script>";
			}
		}
	}
	
	if($mensajes_error_bd!="")
	{
		$mensajes_error_bd="<br>".procesar_mensaje($mensajes_error_bd);
	}
	
	if(connection_aborted()==false)
	{
		echo "<script>document.getElementById('mensaje_div').style.textAlign='center';</script>";
		echo "<script>document.getElementById('resultado_definitivo').style.textAlign='center';</script>";
		echo "<script>document.getElementById('mensaje_div').innerHTML=\"$mensaje $mensajes_error_bd\"</script>";
		echo "<script>document.getElementById('resultado_definitivo').innerHTML=\"$resultadoDefinitivo\"</script>";
	}
}//fin if cuando se hizo submit


$coneccionBD->cerrar_conexion();
?>