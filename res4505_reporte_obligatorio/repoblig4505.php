<?php
ignore_user_abort(true);
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');
require_once '../utiles/queries_utiles_bd.php';

require_once '../utiles/crear_zip.php';

//require_once 'reparacion_campos_duplicados.php';
require_once '../res4505/reparacion_duplicados_por_txt.php';

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/configuracion_global_email.php';

require_once 'corrector_de_registros.php';

require_once 'subir_a_tablas_pob_riesgo.php';

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
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string));
}

function alphanumericAndSpace_include_br( $string )
{
	$string = str_replace("á","a",$string);
	$string = str_replace("é","e",$string);
	$string = str_replace("í","i",$string);
	$string = str_replace("ó","o",$string);
	$string = str_replace("ú","u",$string);
	$string = str_replace("Á","A",$string);
	$string = str_replace("É","E",$string);
	$string = str_replace("Í","I",$string);
	$string = str_replace("Ó","O",$string);
	$string = str_replace("Ú","U",$string);
	
	$string = str_replace("ñ","n",$string);
	$string = str_replace("Ñ","N",$string);
    return trim(preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/\<\>\_\:]/', '', $string));
}

function alphanumericAndSpace4( $string )
{
    $string = str_replace("á","a",$string);
    $string = str_replace("é","e",$string);
    $string = str_replace("í","i",$string);
    $string = str_replace("ó","o",$string);
    $string = str_replace("ú","u",$string);
    $string = str_replace("Á","A",$string);
    $string = str_replace("É","E",$string);
    $string = str_replace("Í","I",$string);
    $string = str_replace("Ó","O",$string);
    $string = str_replace("Ú","U",$string);
    
    $string = str_replace("ñ","n",$string);
    $string = str_replace("Ñ","N",$string);
    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\.]/', '', $string);
    return $cadena;
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



//para el corrector registros despeus de unificar duplicado
$consecutivo_errores=0;


$selector_fechas_corte="";
$selector_fechas_corte.="<input type='hidden' id='fechas_corte' name='fechas_corte' >";


$query_periodos_rips="SELECT * FROM gioss_periodo_informacion ORDER BY cod_periodo_informacion;";
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
			$eapb.="<option value='".$eapb_prestador_usuario_res['codigo_entidad']."' selected>".$eapb_prestador_usuario_res['codigo_entidad']." ".$eapb_prestador_usuario_res['nombre_de_la_entidad']."</option>";
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
			$eapb.="<option value='".$eapb_entidad['codigo_entidad']."' selected>".$eapb_entidad['codigo_entidad']." ".$eapb_entidad['nombre_de_la_entidad']."</option>";
		}
	}
}//fin else si el usuario es de perfil 3 o 4 y la entidad es de tipo eapb

$eapb.="</select>";
$eapb.="</div>";
//FIN
$mensaje_div="<div id='mensaje_div' style='text-align:left;'></div>";
$res_def_div="<div id='resultado_definitivo' style='text-align:left;'></div>";
$smarty->assign("mensaje_proceso", $mensaje_div, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);
$smarty->assign("resultado_definitivo", $res_def_div, true);
$smarty->assign("campo_eapb", $eapb, true);
//$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('repoblig4505.html.tpl');

if(isset($_POST["year_de_corte"]) && isset($_POST['eapb']) && $_POST['eapb']!="none" && $_POST["year_de_corte"]!="" && ctype_digit($_POST["year_de_corte"]) )
{

	$fecha_de_corte=$_POST['year_de_corte']."-".$_POST['fechas_corte'];
	$periodo=$_POST['periodo'];
	$accion=$_POST['selector_estado_info'];
	
	$tipo_tiempo_periodo=$_POST['tipo_tiempo_periodo'];
	//echo "<script>alert(\"".$fecha_de_corte." ".$periodo." ".$accion."\");</script>";
	
	
		
	//$cod_pss_IPS = $_POST['prestador'];
	$cod_eapb=$_POST['eapb'];
	//echo "<script>alert(\"".$secuencia_actual."\");</script>";
	//echo "<script>alert(\"".$cod_pss_IPS."\");</script>";
	
	
	$ruta_escribir_archivo="";
	$ruta_escribir_archivo_con_duplicados="";
	
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('H:i:s');
	$fecha_para_archivo= date('Y-m-d-H-i-s');
	
	$fecha_y_hora_para_view=str_replace(":","",$tiempo_actual).str_replace("-","",$fecha_actual);
	$fecha_y_hora_para_view=substr($fecha_y_hora_para_view,0,4);
	
	$mensaje_perm_estado="";
	$mensaje_perm_estado_reg_dupl="";
	$mensaje_perm_estado_reg_recuperados="";
	
	$fecha_array= explode("-",$fecha_de_corte);
	$year=$fecha_array[0];
	
	$fecha_revisar = date('Y-m-d',strtotime($fecha_de_corte));
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";
	
	$regimen_almacenado="";
	//echo "<script>alert(\"".$fecha_revisar."\");</script>";
	
	//PERIODOS PYP
	if($tipo_tiempo_periodo=="trimestral")
	{
		if(intval($periodo)==1)
		{
		   $fecha_ini_bd="01/01/".$year;
		   $fecha_fin_bd="03/31/".$year;
		   $fecha_de_corte_periodo="03/31/".$year;
		}
		if(intval($periodo)==2)
		{
		   $fecha_ini_bd="04/01/".$year;
		   $fecha_fin_bd="06/30/".$year;
		   $fecha_de_corte_periodo="06/30/".$year;
		}
		if(intval($periodo)==3)
		{
		   $fecha_ini_bd="07/01/".$year;
		   $fecha_fin_bd="09/30/".$year;
		   $fecha_de_corte_periodo="09/30/".$year;
		}
		if(intval($periodo)==4)
		{
		   $fecha_ini_bd="10/01/".$year;
		   $fecha_fin_bd="12/31/".$year;
		   $fecha_de_corte_periodo="12/31/".$year;
		}
	}//fin if trimestral
	else if($tipo_tiempo_periodo=="mensual")
	{
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
	}//fin if mensual
	//FIN PERIODOS PYP
	
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
	$query_tipo_entidad="SELECT cod_tipo_ident_entidad_reportadora,cod_tipo_regimen_rips,nit,cod_tipo_regimen_4505 FROM gios_entidad_administradora WHERE cod_entidad_administradora='$cod_eapb';";
	$resultado_query_tipo_entidad_reportadora=$coneccionBD->consultar2_no_crea_cierra($query_tipo_entidad);
	
	$cod_tipo_ident_entidad_reportadora=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_ident_entidad_reportadora"];
	$cod_tipo_regimen_rips=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_regimen_rips"];
	$nit_entidad_reportadora=$resultado_query_tipo_entidad_reportadora[0]["nit"];
	$regimen4505=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_regimen_4505"];
	
	
	$string_nit_entidad_reportadora=$nit_entidad_reportadora;
	while(strlen($string_nit_entidad_reportadora)<12)
	{
		$string_nit_entidad_reportadora="0".$string_nit_entidad_reportadora;
	}
	
	if(trim($nit_entidad_reportadora)=="SD")
	{
		$query_nit_ess_entidad="SELECT numero_identificacion FROM gioss_entidades_sector_salud WHERE codigo_entidad='$cod_eapb';";
		$resultado_query_nit_ess_entidad_reportadora=$coneccionBD->consultar2_no_crea_cierra($query_nit_ess_entidad);
		
		if($resultado_query_nit_ess_entidad_reportadora>0 && is_array($resultado_query_nit_ess_entidad_reportadora))
		{
			$string_nit_entidad_reportadora=$resultado_query_nit_ess_entidad_reportadora[0]["numero_identificacion"];
			while(strlen($string_nit_entidad_reportadora)<12)
			{
				$string_nit_entidad_reportadora="0".$string_nit_entidad_reportadora;
			}
		}
	}//fin if
	
	$string_cod_eapb=$cod_eapb;
	while(strlen($string_cod_eapb)<12)
	{
		$string_cod_eapb="0".$string_cod_eapb;
	}
	//fin queries  para estructura nombre zip
	
	
	
	
	$tiempo_actual_simple=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
	
	$nombre_archivo_para_zip="";
	if($tipo_tiempo_periodo=="trimestral")
	{
		$nombre_archivo_para_zip="SGD280RPED".$array_fcbd[2].$array_fcbd[0].$array_fcbd[1]."NI".$string_nit_entidad_reportadora.$regimen4505."01";
	}
	else if($tipo_tiempo_periodo=="mensual")
	{
		$nombre_archivo_para_zip="SGD280RPED".$array_fcbd[2].$array_fcbd[0].$array_fcbd[1]."NI".$string_nit_entidad_reportadora.$regimen4505."M01";
	}
	
	
	//VERIFICA SI EL ARCHIVO YA ESTA SIENDO CONSOLIDADO ACTUALMENTE
	$esta_validando_actualmente=false;	
					
	$query_verificacion_esta_siendo_procesado="";
	$query_verificacion_esta_siendo_procesado.=" SELECT * FROM gioss_4505_esta_consolidando_ro_actualmente ";
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
	//FIN VERIFICA SI EL ARCHIVO YA ESTA SIENDO CONSOLIDADO ACTUALMENTE
	
	//VERIFICAR QUE EL NOMBRE DE ARCHIVO NO HAYA SIDO REPORTADO
	$fecha_generacion_si_se_reporto="";
	$ya_se_reporto_archivo=false;
	
	$query_verificar_nombre_archivo="SELECT * FROM gioss_archivos_obligatorios_reportados_pyp WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_pyp='$nombre_archivo_para_zip' ;";
	$resultado_verificar_nombre_archivo=$coneccionBD->consultar2_no_crea_cierra($query_verificar_nombre_archivo);
	
	if(count($resultado_verificar_nombre_archivo)>0)
	{
		$ya_se_reporto_archivo=true;
		$fecha_generacion_si_se_reporto=$resultado_verificar_nombre_archivo[0]["fecha_de_generacion"];
	}
	
	//si el archivo esa siendo validado actualmente lo marca como si se hubiese generado
	if($esta_validando_actualmente==true)
	{
		$ya_se_reporto_archivo=true;
	}
	
	//FIN VERIFICA SI ARCHIVO YA SE REPORTO
	
	//VERIFICA SI ES USUARIO ADMINISTRADOR PARA REMPLAZAR UN REPORTE DE EL MISMO PERIODO PARA LA MISMA EPS
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
				
				if(count($resultado_verificar_nombre_archivo)>0 )
				{
					echo "<script>alert('Se eliminara el reporte generado anteriormente para el mismo periodo por orden de usuario admin.');</script>";
					
					//BORRA TABLAS YA QUE SOLO PUEDE HABER UN REPORTE POR PERIODO CONSOLIDADO
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_consulta_reporte_obligatorio_pyp4505_exitoso ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_pyp='$nombre_archivo_para_zip' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_consulta_reporte_obligatorio_pyp4505_exitoso ".$error_bd_seq."<br>";
					}
					
					/*
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_consulta_reporte_obligatorio_pyp4505_exitosos_duplicados ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_pyp='$nombre_archivo_para_zip' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_consulta_reporte_obligatorio_pyp4505_exitosos_duplicados ".$error_bd_seq."<br>";
					}
					*/
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_dupl_incluidos_excluidos_hor_pyp4505 ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_pyp='$nombre_archivo_para_zip' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_dupl_incluidos_excluidos_hor_pyp4505 ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_indexador_duplicados_del_consolidado_4505 ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo_pyp='$nombre_archivo_para_zip' AND codigo_entidad_eapb_generadora='$cod_eapb'; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_indexador_duplicados_del_consolidado_4505 ".$error_bd_seq."<br>";
					}
					
					//BORRA TABLAS YA QUE SOLO PUEDE HABER UN REPORTE POR PERIODO CONSOLIDADO
					
					
					//BORRA TABLAS RIESGO POBLACION SI SE VOLVIO A GENERAR EL MISMO REPORTE
					
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_partos_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_partos_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_atencion_por_psicologia_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_atencion_por_psicologia_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_adulto_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_adulto_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_infeccion_trasmision_sexual_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_infeccion_trasmision_sexual_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_victima_enfermedad_mental_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_victima_enfermedad_mental_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_cancer_seno_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_cancer_seno_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_lepra_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_lepra_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_joven_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_joven_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_vacunacion_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_vacunacion_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_cancer_cervix_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_cancer_cervix_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_obesidad_desnutricion_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_obesidad_desnutricion_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_gestacion_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_gestacion_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_victima_maltrato_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_victima_maltrato_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_violencia_sexual_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_violencia_sexual_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_menor_10anos_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_menor_10anos_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_odontologico_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_odontologico_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_sintomatico_respiratorio_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_sintomatico_respiratorio_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_edad_gestacional_nacer_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_edad_gestacional_nacer_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_enfermedad_leishmaniasis_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_enfermedad_leishmaniasis_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_enfermedad_renal_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_enfermedad_renal_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_control_recien_nacido_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_control_recien_nacido_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_enfermedad_anemica_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_enfermedad_anemica_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_problemas_vision_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_problemas_vision_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_planificacion_familiar_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_planificacion_familiar_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_enfermedad_diabetica_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_enfermedad_diabetica_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_hipotiroidismo_congenito_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_hipotiroidismo_congenito_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					$delete_consolidado_anterior="";
					$delete_consolidado_anterior.="DELETE FROM gioss_poblacion_riesgo_enfermedad_colesterol_res4505_pyp ";
					$delete_consolidado_anterior.=" WHERE nombre_archivo='$nombre_archivo_para_zip' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" codigo_eapb='$cod_eapb' ";
					$delete_consolidado_anterior.=" AND ";
					$delete_consolidado_anterior.=" fecha_de_corte='$fecha_corte_bd' ";
					$delete_consolidado_anterior.=" ; ";
					$error_bd_seq="";		
					$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($delete_consolidado_anterior, $error_bd_seq);
					if($error_bd_seq!="")
					{
						$mensajes_error_bd.=" ERROR AL BORRAR CONSOLIDADO exitoso ANTERIOR gioss_poblacion_riesgo_enfermedad_colesterol_res4505_pyp ".$error_bd_seq."<br>";
					}
					
					//FIN BORRA TABLAS RIESGO POBLACION SI SE VOLVIO A GENERAR EL MISMO REPORTE
					
				}//fin if
				
				//if si no hay mensajes de error
				if($mensajes_error_bd=="")
				{
					//permite generar el archivo
					$ya_se_reporto_archivo=false;
				}
			}
		}
	}
	
	
	//FIN VERIFICA SI ES USUARIO ADMINISTRADOR PARA REMPLAZAR UN REPORTE DE EL MISMO PERIODO PARA LA MISMA EPS
	
	$array_rutas_archivos_generados=array();
	$bool_hubo_error_query=false;
	
	
	if($ya_se_reporto_archivo==false)
	{
		
		
		//crea directorio para evitar que se descarguen archivos pasados
		$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
		if(!file_exists($rutaTemporal.$nombre_archivo_para_zip.$tiempo_actual_string))
		{
			mkdir($rutaTemporal.$nombre_archivo_para_zip.$tiempo_actual_string, 0700);
		}
		else
		{
			$files_to_erase = glob($rutaTemporal.$nombre_archivo_para_zip.$tiempo_actual_string."/*"); // get all file names
			foreach($files_to_erase as $file_to_be_erased)
			{ // iterate files
			  if(is_file($file_to_be_erased))
			  {
			    unlink($file_to_be_erased); // delete file
			  }
			}
		}
		$rutaTemporal=$rutaTemporal.$nombre_archivo_para_zip.$tiempo_actual_string."/";
	
		
		//GENERANDO PYP
		
		//seleccion de las tablas de acuerdo a si es validado o rechazado
		//$NOMBRE_VISTA_PYP_CONSOLIDADO="vsropyp4505_".$nick_user."_".$tipo_id."_".$identificacion;
		$NOMBRE_VISTA_PYP_CONSOLIDADO="vsropyp4505_".$nick_user;
		
		$sql_datos_reporte_obligatorio ="";
		$sql_datos_reporte_obligatorio.="CREATE OR REPLACE VIEW $NOMBRE_VISTA_PYP_CONSOLIDADO ";
		$sql_datos_reporte_obligatorio.=" AS  ";		
		
		//solo se miraran los datos validos
		$sql_datos_reporte_obligatorio .=" ( ";
		$sql_datos_reporte_obligatorio .="SELECT * from gios_datos_validados_exito_r4505 WHERE ";			
		$sql_datos_reporte_obligatorio .=" (fecha_de_corte BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."'  ";
		$sql_datos_reporte_obligatorio .=" UNION ";
		$sql_datos_reporte_obligatorio .="SELECT * from gios_datos_rechazados_r4505 WHERE ";			
		$sql_datos_reporte_obligatorio .=" (fecha_de_corte BETWEEN '".$fecha_ini_bd."'  AND '".$fecha_fin_bd."' )  ";
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" estado_registro='1' ";
		$sql_datos_reporte_obligatorio .=" AND ";
		$sql_datos_reporte_obligatorio .=" codigo_eapb='".$cod_eapb."' ";
		$sql_datos_reporte_obligatorio .=" ) ";
		$sql_datos_reporte_obligatorio .=" ORDER BY numero_de_secuencia asc,numero_fila asc ";
		$sql_datos_reporte_obligatorio .=";";
		//fin solo se miraran los datos validos
		
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_datos_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR al crear vista de pre-subida: ".$error_bd_seq."<br>";
		}
		//echo "<script>alert('".procesar_mensaje($sql_datos_reporte_obligatorio)."');</script>";
		
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM $NOMBRE_VISTA_PYP_CONSOLIDADO ; ";
		$error_bd_seq="";
		$resultado_query_numero_registros=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_numero_registros,$error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR AL CONSULTAR CANTIDAD ELEMENTOS de vista_subiendo: ".$error_bd_seq."<br>";
		}
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		$lineas_del_archivo = intval($resultado_query_numero_registros[0]["contador"]);
		if($numero_registros==0)
		{
			$mensajes_error_bd.="No hay registros a reportar. <br>";
		}
		
		//INDICA QUE INICIO PROCESO SOLO SI HAY REGISTROS
		if($numero_registros>0)
		{
			$query_insert_esta_siendo_procesado="";
			$query_insert_esta_siendo_procesado.=" INSERT INTO gioss_4505_esta_consolidando_ro_actualmente ";
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
			$query_insert_esta_siendo_procesado.=" '".$nombre_archivo_para_zip."',  ";
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
					echo "<script>alert('error al iniciar el estado actual de validacion en tiempo real  4505 ');</script>";
				}
			}
		}
		//FIN INDICA QUE INICIO PROCESO SOLO SI HAY REGISTROS
		
		//CREA ARCHIVO PARA LOS REGISTROS DEFINITIVOS
		$ruta_escribir_archivo=$rutaTemporal.$nombre_archivo_para_zip.".txt";
		$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		fclose($reporte_obligatorio_file);
		//FIN CREA ARCHIVO PARA LOS REGISTROS DEFINITIVOS
		
		//CREA ARCHIVO CON TODOS LOS REGISTROS PARA CORREGIR DUPLICADOS
		$ruta_escribir_archivo_con_duplicados=$rutaTemporal.$nombre_archivo_para_zip."_dupl.txt";
		$reporte_obligatorio_dupl_file= fopen($ruta_escribir_archivo_con_duplicados, "w") or die("fallo la creacion del archivo");
		fclose($reporte_obligatorio_dupl_file);
		//FIN CREA ARCHIVO CON TODOS LOS REGISTROS PARA CORREGIR DUPLICADOS
		
		//CREACION DEL ARCHIVO DE CAMBIOS PARA DUPLICADOS
		$ruta_cambios_duplicados_campos=$rutaTemporal.$nombre_archivo_para_zip."_crpdupl_".$fecha_para_archivo.".txt";
		//se remplaza el archivo si ya existe con modo w		
		$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "w") or die("fallo la creacion del archivo");		    
		fclose($file_cambios_duplicados_registro);		    
		//FIN CREACION DEL ARCHIVO DE CAMBIOS PARA DUPLICADOS
		
		//CREACION DEL ARCHIVO DE CAMBIOS REALIZADOS 2
		$ruta_cambios_real_dupl_campos_2=$rutaTemporal.$nombre_archivo_para_zip."_cambios_real2_dupl_".$fecha_para_archivo.".txt";		    
		//se remplaza el archivo si ya existe con modo w		
		$file_cambios_real_dupl_registro_2 = fopen($ruta_cambios_real_dupl_campos_2, "w") or die("fallo la creacion del archivo");
		fwrite($file_cambios_real_dupl_registro_2, "LOG CAMBIOS CORRECCION CAMPOS UNIFICADOS DE DUPLICADOS");
		fclose($file_cambios_real_dupl_registro_2);		    
		//FIN CREACION DEL ARCHIVO DE CAMBIOS REALIZADOS 2
		
		//archivo temp para numeros de secuencia
		$ruta_temp_numero_secuencia=$rutaTemporal.$nombre_archivo_para_zip."_numsec_".$fecha_para_archivo.".txt";
		//se remplaza el archivo si ya existe con modo w		
		$file_temp_numero_secuencia = fopen($ruta_temp_numero_secuencia, "w") or die("fallo la creacion del archivo");
		//el salto de linea \n ya esta abajo, aca no es necesario
		fwrite($file_temp_numero_secuencia, "primera_linea");
		fclose($file_temp_numero_secuencia);
		//fin archivo temp para numeros de secuencia
		
		
		//SE ADICIONAN LAS RUTAS DE LSO ARCHIVOS A COMPRIMIR
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo_con_duplicados;
		$array_rutas_archivos_generados[]=$ruta_cambios_duplicados_campos;
		$array_rutas_archivos_generados[]=$ruta_cambios_real_dupl_campos_2;//casi
		$array_rutas_archivos_generados[]=$ruta_temp_numero_secuencia;
		//FIN SE ADICIONAN LAS RUTAS DE LSO ARCHIVOS A COMPRIMIR
		
		//PARTE ESCRIBE CONSOLIDADO CON DUPLICADOS E IDENTIFICA REGISTROS DUPLICADOS (EN LA NUEVA FORMA, CON INDEXADOR)		
		
		
		$numero_registros_bloque=1000;		
		$cont_linea=1;
		$contador_offset=0;
		$flag_creacion_archivo=false;
		$limite=0;
		$string_vacia="                ";
		$flag_para_salto_linea_inicial=false;
		
		$cont_resultados=1;
		
		$cont_porcentaje_consolidado_dupl=0;
		
		$acumulador_para_contar_duplicados=0;
		$personas_con_duplicados_hasta_el_momento=0;
		$personas_insertadas_hasta_el_momento=0;
		
		$cont_porcentaje=0;
		$cont_porcentaje_dupl=0;
		$cont_porcentaje_csv=0;
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM $NOMBRE_VISTA_PYP_CONSOLIDADO LIMIT $limite OFFSET $contador_offset;  ";
			$error_bd_seq="";
			$resultado_query_reporte_obligatoria=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);			
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.="ERROR AL CONSULTAR de vista_consulta: ".$error_bd_seq."<br>";
			}
			
			if(count($resultado_query_reporte_obligatoria)>0)
			{
				
				
				//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				if($flag_creacion_archivo==false)
				{
					$reporte_obligatorio_dupl_file= fopen($ruta_escribir_archivo_con_duplicados, "w") or die("fallo la creacion del archivo");
					fclose($reporte_obligatorio_dupl_file);
					
					//ESCRIBE PRIMERA LINEA DE 4505
					$reporte_obligatorio_dupl_file= fopen($ruta_escribir_archivo_con_duplicados, "a") or die("fallo la creacion del archivo");
											
					$primera_linea_4505="";
					$primera_linea_4505.="1|".$cod_eapb."|".$fecha_ini_bd."|".$fecha_fin_bd."|".$string_vacia;
					fwrite($reporte_obligatorio_dupl_file, $primera_linea_4505."\n");		
					fclose($reporte_obligatorio_dupl_file);
					
					//FIN ESCRIBE PRIMERA LINEA DE 4505
				}
				
				$flag_creacion_archivo=true;
				//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				
				
				$reporte_obligatorio_dupl_file= fopen($ruta_escribir_archivo_con_duplicados, "a") or die("fallo la creacion del archivo");
				
				$fue_cerrada_la_gui_3=false;
				foreach($resultado_query_reporte_obligatoria as $resultado)
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
					$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_consolidando_ro_actualmente ";
					$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND cod_eapb='".$cod_eapb."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
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
					
					//PARTE IDENTIFICA DUPLICADOS E INDEXA
					$tipo_id_registro_actual=alphanumericAndSpace($resultado["campo3"]);
					$numero_id_registro_actual=alphanumericAndSpace($resultado["campo4"]);
					
					//INDEXADOR DE DUPLICADOS
					//FASE 1 consulta por el campo 3 y 4 (tipo id y numero id afiliado) si existe duplicado
					$existe_afiliado=false;
					$lista_lineas_duplicados="".$cont_linea;
					$query_consultar_en_indexador="";
					$query_consultar_en_indexador.=" SELECT lista_lineas_donde_hay_duplicados FROM  ";
					$query_consultar_en_indexador.=" gioss_indexador_duplicados_del_consolidado_4505 ";
					$query_consultar_en_indexador.=" WHERE  ";
					$query_consultar_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="id_usuario='".$identificacion."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="nick_usuario='".$nick_user."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="fecha_corte_reporte='".$fecha_corte_bd."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="hora_generacion='".$tiempo_actual."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="codigo_entidad_eapb_generadora='".$cod_eapb."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="nombre_archivo_pyp='".$nombre_archivo_para_zip."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="campo_3_tipo_id='".$tipo_id_registro_actual."'";
					$query_consultar_en_indexador.=" AND ";
					$query_consultar_en_indexador.="campo_4_numero_id='".$numero_id_registro_actual."'";
					$query_consultar_en_indexador.=" ; ";
					$error_bd_seq="";		
					$resultado_esta_afiliado_en_indexador=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_consultar_en_indexador, $error_bd_seq);
					if($error_bd_seq!="")
					{
					    $mensajes_error_bd.=" ERROR Al consultar en la tabla gioss_indexador_duplicados_del_consolidado_4505 ".procesar_mensaje($error_bd_seq).".<br>";
					    
					    if($fue_cerrada_la_gui_3==false)
					    {
						    echo "<script>alert('ERROR Al consultar en la tabla gioss_indexador_duplicados_del_consolidado_4505 ".procesar_mensaje($error_bd_seq)."');</script>";
					    }
						
					}//fin if
					if(is_array($resultado_esta_afiliado_en_indexador) && count($resultado_esta_afiliado_en_indexador)>0)
					{
					    $existe_afiliado=true;
					    if(ctype_digit($resultado_esta_afiliado_en_indexador[0]["lista_lineas_donde_hay_duplicados"]))
					    {
						$acumulador_para_contar_duplicados+=1;
					    }
					    $lista_lineas_duplicados=$resultado_esta_afiliado_en_indexador[0]["lista_lineas_donde_hay_duplicados"].";;".$cont_linea;
					    //si haya duplicaco, suma 1
					    $acumulador_para_contar_duplicados+=1;
					}
					else
					{
					    //si no haya duplicado, suma cero
					    $acumulador_para_contar_duplicados+=0;
					}
					//FIN FASE 1
					
					
					//FASE 2 inserta en indexador de duplicado si no habia
					if($existe_afiliado==false)
					{
					    $query_insert_updt_en_indexador="";
					    $query_insert_updt_en_indexador.=" INSERT INTO ";
					    $query_insert_updt_en_indexador.=" gioss_indexador_duplicados_del_consolidado_4505 ";				
					    $query_insert_updt_en_indexador.=" ( ";	
					    $query_insert_updt_en_indexador.=" tipo_id_usuario, ";
					    $query_insert_updt_en_indexador.=" id_usuario, ";
					    $query_insert_updt_en_indexador.=" nick_usuario, ";
					    $query_insert_updt_en_indexador.=" fecha_corte_reporte, ";
					    $query_insert_updt_en_indexador.=" fecha_de_generacion, ";
					    $query_insert_updt_en_indexador.=" hora_generacion, ";
					    $query_insert_updt_en_indexador.=" codigo_entidad_eapb_generadora, ";
					    $query_insert_updt_en_indexador.=" nombre_archivo_pyp, ";
					    $query_insert_updt_en_indexador.=" campo_3_tipo_id, ";
					    $query_insert_updt_en_indexador.=" campo_4_numero_id, ";
					    $query_insert_updt_en_indexador.=" contiene_filas_coincidentes, ";
					    $query_insert_updt_en_indexador.=" lista_lineas_donde_hay_duplicados ";
					    $query_insert_updt_en_indexador.=" ) ";
					    $query_insert_updt_en_indexador.=" VALUES ";
					    $query_insert_updt_en_indexador.=" ( ";
					    $query_insert_updt_en_indexador.="'".$tipo_id."',";
					    $query_insert_updt_en_indexador.="'".$identificacion."',";
					    $query_insert_updt_en_indexador.="'".$nick_user."',";							
					    $query_insert_updt_en_indexador.="'".$fecha_corte_bd."',";
					    $query_insert_updt_en_indexador.="'".$fecha_actual."',";
					    $query_insert_updt_en_indexador.="'".$tiempo_actual."',";
					    $query_insert_updt_en_indexador.="'".$cod_eapb."',";
					    $query_insert_updt_en_indexador.="'".$nombre_archivo_para_zip."',";
					    $query_insert_updt_en_indexador.="'".$tipo_id_registro_actual."',";
					    $query_insert_updt_en_indexador.="'".$numero_id_registro_actual."',";
					    $query_insert_updt_en_indexador.="'NO',";
					    $query_insert_updt_en_indexador.="'".$cont_linea."'";
					    $query_insert_updt_en_indexador.=" ) ";
					    $query_insert_updt_en_indexador.=" ; ";
					    $error_bd_seq="";		
					    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
					    if($error_bd_seq!="")
					    {
						$mensajes_error_bd.=" ERROR Al subir en la tabla gioss_indexador_duplicados_del_consolidado_4505 ".procesar_mensaje($error_bd_seq).".<br>";
						
						if($fue_cerrada_la_gui_3==false)
						{
							echo "<script>alert('ERROR Al subir en la tabla gioss_indexador_duplicados_del_consolidado_4505  ".procesar_mensaje($error_bd_seq)."');</script>";
						}
					    }
					    else
					    {
						$personas_insertadas_hasta_el_momento+=1;
					    }
					    
					    //SUBE A GIOSS_CONSULTA_REPORTE_OBLIGATORIO_PYP4505_EXITOSO SI ES EL PRIMERO ENCONTRADO
					    
						$sql_insert_consulta_reporte_obligatorio="";
						$sql_insert_consulta_reporte_obligatorio.=" INSERT INTO ";
						$sql_insert_consulta_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_pyp4505_exitoso ";
						$sql_insert_consulta_reporte_obligatorio.=" ( ";				
						$cont_orden_campo_pyp=0;
						while($cont_orden_campo_pyp<=118)
						{
							$sql_insert_consulta_reporte_obligatorio.=" campo_pyp4505_con_numero_orden_".$cont_orden_campo_pyp." , ";
							$cont_orden_campo_pyp++;
						}//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar				
						$sql_insert_consulta_reporte_obligatorio.=" numero_secuencia, ";
						$sql_insert_consulta_reporte_obligatorio.=" numero_registro, ";
						$sql_insert_consulta_reporte_obligatorio.=" regimen, ";
						$sql_insert_consulta_reporte_obligatorio.=" fecha_corte_reporte, ";
						$sql_insert_consulta_reporte_obligatorio.=" fecha_de_generacion, ";
						$sql_insert_consulta_reporte_obligatorio.=" hora_generacion, ";
						$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
						$sql_insert_consulta_reporte_obligatorio.=" codigo_entidad_prestadora, ";
						$sql_insert_consulta_reporte_obligatorio.=" nombre_archivo_pyp ";
						$sql_insert_consulta_reporte_obligatorio.=" ) ";
						$sql_insert_consulta_reporte_obligatorio.=" VALUES ";
						$sql_insert_consulta_reporte_obligatorio.=" ( ";				
						$cont_orden_campo_pyp=0;
						while($cont_orden_campo_pyp<=118)
						{
							//es campo porque proviene de la tabla gios_datos_validados_exito_r4505 o gios_datos_rechazados_r4505
							$sql_insert_consulta_reporte_obligatorio.="'".alphanumericAndSpace($resultado["campo".$cont_orden_campo_pyp])."',";
							$cont_orden_campo_pyp++;
						}//fin while con los valores de los campos 4505 a insertar en la tabla de reporte obligatorio
						$sql_insert_consulta_reporte_obligatorio.="'".$resultado["numero_de_secuencia"]."',";
						$sql_insert_consulta_reporte_obligatorio.="'".$resultado["numero_fila"]."',";				
						$sql_insert_consulta_reporte_obligatorio.="'".$resultado["tipo_de_regimen_de_la_informacion_reportada"]."',";								
						$sql_insert_consulta_reporte_obligatorio.="'".$fecha_corte_bd."',";
						$sql_insert_consulta_reporte_obligatorio.="'".$fecha_actual."',";
						$sql_insert_consulta_reporte_obligatorio.="'".$tiempo_actual."',";
						$sql_insert_consulta_reporte_obligatorio.="'".$resultado["codigo_eapb"]."',";
						$sql_insert_consulta_reporte_obligatorio.="'".$resultado["cod_prestador_servicios_salud"]."',";
						$sql_insert_consulta_reporte_obligatorio.="'".$nombre_archivo_para_zip."'";
						$sql_insert_consulta_reporte_obligatorio.=" ) ";
						$sql_insert_consulta_reporte_obligatorio.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insert_consulta_reporte_obligatorio, $error_bd_seq);
						if($error_bd_seq!="")
						{
							echo "<script>alert('error Al subir en la tabla reporte obligatorio: ".procesar_mensaje($error_bd_seq)." ');</script>";
							$mensajes_error_bd.=" ERROR Al subir en la tabla reporte obligatorio: ".$error_bd_seq."<br>";
						}
					    //echo "<script>alert('cod_prestador_servicios_salud ".$resultado["cod_prestador_servicios_salud"]."');</script>";
					    //FIN SUBE A GIOSS_CONSULTA_REPORTE_OBLIGATORIO_PYP4505_EXITOSO SI ES EL PRIMERO ENCONTRADO
					    
					}//fin if
					//o actualiza si ya habia concatenando a la lista de numero de filas
					else if($existe_afiliado==true)
					{
					    $array_check_tiene_2_filas_coincidentes=explode(";;",$lista_lineas_duplicados);
					    
					    //borra el que estaba en gioss_consulta_reporte_obligatorio_pyp4505_exitoso
					    //entrea si el nuemro de filas es igual a dos
					    if(count($array_check_tiene_2_filas_coincidentes)==2)
					    {
						//BORRANDO el afiliado duplicado de gioss_consulta_reporte_obligatorio_pyp4505_exitoso
						$sql_delete_corregidos_temp="";
						$sql_delete_corregidos_temp.=" DELETE FROM   ";
						$sql_delete_corregidos_temp.=" gioss_consulta_reporte_obligatorio_pyp4505_exitoso ";
						$sql_delete_corregidos_temp.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
						$sql_delete_corregidos_temp.=" AND ";
						$sql_delete_corregidos_temp.=" fecha_corte_reporte='".$fecha_corte_bd."'  ";
						$sql_delete_corregidos_temp.=" AND ";
						$sql_delete_corregidos_temp.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
						$sql_delete_corregidos_temp.=" AND ";
						$sql_delete_corregidos_temp.=" nombre_archivo_pyp='".$nombre_archivo_para_zip."'  ";;
						$sql_delete_corregidos_temp.=" AND ";
						$sql_delete_corregidos_temp.=" campo_pyp4505_con_numero_orden_3='".$tipo_id_registro_actual."' ";
						$sql_delete_corregidos_temp.=" AND ";
						$sql_delete_corregidos_temp.=" campo_pyp4505_con_numero_orden_4='".$numero_id_registro_actual."' ";
						$sql_delete_corregidos_temp.=" ; ";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.=" ERROR Al eliminar en la tabla gioss_consulta_reporte_obligatorio_pyp4505_exitoso ".procesar_mensaje($error_bd_seq).".<br>";
							
						}
						//FIN BORRANDO el afiliado duplicado de gioss_consulta_reporte_obligatorio_pyp4505_exitoso
					    }
					    //fin borra el que estaba en gioss_consulta_reporte_obligatorio_pyp4505_exitoso
					    
					    $query_insert_updt_en_indexador="";
					    $query_insert_updt_en_indexador.=" UPDATE  ";
					    $query_insert_updt_en_indexador.=" gioss_indexador_duplicados_del_consolidado_4505 ";				
					    $query_insert_updt_en_indexador.=" SET ";
					    $query_insert_updt_en_indexador.=" contiene_filas_coincidentes='SI', ";
					    $query_insert_updt_en_indexador.=" lista_lineas_donde_hay_duplicados='".$lista_lineas_duplicados."' ";
					    $query_insert_updt_en_indexador.=" WHERE  ";
					    $query_insert_updt_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="id_usuario='".$identificacion."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="nick_usuario='".$nick_user."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="fecha_corte_reporte='".$fecha_corte_bd."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="hora_generacion='".$tiempo_actual."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="codigo_entidad_eapb_generadora='".$cod_eapb."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="nombre_archivo_pyp='".$nombre_archivo_para_zip."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="campo_3_tipo_id='".$tipo_id_registro_actual."'";
					    $query_insert_updt_en_indexador.=" AND ";
					    $query_insert_updt_en_indexador.="campo_4_numero_id='".$numero_id_registro_actual."'";
					    $query_insert_updt_en_indexador.=" ; ";
					    $error_bd_seq="";		
					    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
					    if($error_bd_seq!="")
					    {
						$mensajes_error_bd.=" ERROR Al actualizar en la tabla gioss_indexador_duplicados_del_consolidado_4505 ".procesar_mensaje($error_bd_seq).".<br>";
						
						if($fue_cerrada_la_gui_3==false)
						{
							echo "<script>alert('ERROR Al actualizar en la tabla gioss_indexador_duplicados_del_consolidado_4505  ".procesar_mensaje($error_bd_seq)."');</script>";
						}
					    }
					    
					    if(count($array_check_tiene_2_filas_coincidentes)==2)
					    {					
						$personas_con_duplicados_hasta_el_momento+=1;
					    }
					}//fin if actualizar
					//FIN FASE 2
					//FIN INDEXADOR DE DUPLICADOS
					
					//FIN PARTE IDENTIFICA DUPLICADOS E INDEXA
					
					//PARTE ESCRIBE EN TXT CONSOLIDADO CON DUPLICADOS
					$cadena_escribir_linea="";
					$cont_orden_campo_pyp=0;
					while($cont_orden_campo_pyp<=118)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.="|";
						}
						if($cont_orden_campo_pyp!=1)
						{
							$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo".$cont_orden_campo_pyp]);
						}
						else
						{
							$cadena_escribir_linea.=$cont_resultados;
						}
						$cont_orden_campo_pyp++;
					}
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_dupl_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_dupl_file, "\n".$cadena_escribir_linea);
					}
					
					
					
					//FIN PARTE ESCRIBE EN TXT CONSOLIDADO CON DUPLICADOS
					
					//escribe el archivo con las variables adicionales necesarias para llenar las tablas
					$file_temp_numero_secuencia = fopen($ruta_temp_numero_secuencia, "a") or die("fallo la creacion del archivo");
						
					$numero_secuencia_para_archivo=$resultado["numero_de_secuencia"]."|".$resultado["cod_prestador_servicios_salud"]."|".$resultado["tipo_de_regimen_de_la_informacion_reportada"];
					
					fwrite($file_temp_numero_secuencia, "\n".$numero_secuencia_para_archivo);
					
					//fin
					
					$regimen_almacenado=$resultado["tipo_de_regimen_de_la_informacion_reportada"];
					
					//cierra el archivo del log reparacion de duplicados
					fclose($file_temp_numero_secuencia);
					
					//porcentaje
					$muestra_mensaje_nuevo=false;
					$porcentaje=intval((($cont_linea)*100)/($numero_registros));
					if($porcentaje!=$cont_porcentaje_consolidado_dupl || ($porcentaje==0 && ($cont_linea)==1) || $porcentaje==100)
					{
					 $cont_porcentaje_consolidado_dupl=$porcentaje;
					 $muestra_mensaje_nuevo=true;
					}
					//fin porcentaje
					
					//$mensaje_para_estado_ejecucion="Por favor espere, $cont_linea registros recuperados de $numero_registros.";
					
					$mensaje_estado_registros="";
					$mensaje_estado_registros.="<table style=text-align:center;width:60%;left:0%;position:relative;border-style:solid;border-width:5px; id=tabla_estado_1>";
					$mensaje_estado_registros.="<tr style=background-color:#80bfff><th colspan=2 style=text-align:center;width:60%><span style=\"color:white;text-shadow:2px 2px 8px #0000ff;\">Inicio a las $tiempo_actual del $fecha_actual para $nombre_archivo_para_zip</span></th></tr>";
					$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros:</td><td style=text-align:left>".$numero_registros."</td></tr>";
					$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Registros consolidados analizados:</td><td style=text-align:left>".$cont_linea."</td></tr>";
					$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de personas unicas:</td><td style=text-align:left>".$personas_insertadas_hasta_el_momento."</td></tr>";
					$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Numero de personas con registros duplicados:</td><td style=text-align:left>".$personas_con_duplicados_hasta_el_momento."</td></tr>";
					$mensaje_estado_registros.="<tr><td style=text-align:left;width:60%>Numero de registros duplicados:</td><td style=text-align:left>".$acumulador_para_contar_duplicados.".</tr>";
					$mensaje_estado_registros.="<tr style=background-color:#80bfff><td style=text-align:left;width:60%>Porcentaje actual:</td><td style=text-align:left>$porcentaje %</td></tr>";
					$mensaje_estado_registros.="</table><br>";
					
					$mensaje_perm_estado=$mensaje_estado_registros;
										
					//ACTUALIZA MENSAJE ESTADO EJECUCION
					if($muestra_mensaje_nuevo)
					{
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_consolidando_ro_actualmente ";
						$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_estado_registros' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
						$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
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
								echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
							}
						}
					}//fin if muestra mensaje nuevo
					//FIN ACTUALIZA MENSAJE ESTADO EJECUCION
					
					if($fue_cerrada_la_gui_3==false  && $muestra_mensaje_nuevo)
					{						
						//PASAR A PORCENTAJE
						echo "<script>document.getElementById('mensaje_div').innerHTML='$mensaje_estado_registros';</script>";
						//echo "<script>document.getElementById('tabla_estado_1').style.left='25%';</script>";
						ob_flush();
						flush();
						
					}
					
					$cont_resultados++;
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_dupl_file);
				
				
				
			}//fin if hayo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		
		//RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS
		$reporte_obligatorio_dupl_file= fopen($ruta_escribir_archivo_con_duplicados, "c") or die("fallo la creacion del archivo");
					
		$string_cont_linea="".($cont_linea-1);
		$string_vacia="                ";
		
		while(strlen($string_cont_linea)<strlen($string_vacia))
		{
			$string_cont_linea.=" ";					
		}
		
		$primera_linea_4505="";
		$primera_linea_4505.="1|".$cod_eapb."|".$fecha_ini_bd."|".$fecha_fin_bd."|".$string_cont_linea;
		fwrite($reporte_obligatorio_dupl_file, $primera_linea_4505."\n");		
		fclose($reporte_obligatorio_dupl_file);
		
		//FIN RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS
		
		//FIN PARTE ESCRIBE CONSOLIDADO CON DUPLICADOS E IDENTIFICA REGISTROS DUPLICADOS (EN LA NUEVA FORMA, CON INDEXADOR)	
		
		
		
		//ARREGLO DE DUPLICADOS EN UNO SOLO
		$nombre_vista_index_duplicados="inddc4505".$nombre_archivo_para_zip.$nick_user.$fecha_y_hora_para_view;
		$hubo_al_menos_un_duplicado=true;//se pone por defecto true para mantener el bloque organizado ya que esta variable no es relevante
		$contador_duplicado_para_excluidos=0;
		if($hubo_al_menos_un_duplicado==true)
		{
			$sql_vista_duplicados_reporte_obligatorio ="";
			$sql_vista_duplicados_reporte_obligatorio.="CREATE OR REPLACE VIEW $nombre_vista_index_duplicados ";
			$sql_vista_duplicados_reporte_obligatorio.=" AS  ";					
			$sql_vista_duplicados_reporte_obligatorio .="SELECT * from gioss_indexador_duplicados_del_consolidado_4505  ";	
			$sql_vista_duplicados_reporte_obligatorio.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" nombre_archivo_pyp='".$nombre_archivo_para_zip."'  ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" tipo_id_usuario='$tipo_id' ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" id_usuario='$identificacion' ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" nick_usuario='$nick_user' ";
			$sql_vista_duplicados_reporte_obligatorio.=" AND ";
			$sql_vista_duplicados_reporte_obligatorio.=" contiene_filas_coincidentes='SI' ";
			$sql_vista_duplicados_reporte_obligatorio.=" ORDER BY campo_3_tipo_id asc,campo_4_numero_id asc ";
			$sql_vista_duplicados_reporte_obligatorio.=";";
			$error_bd_seq="";		
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_reporte_obligatorio, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.=" ERROR al crear vista de duplicados: ".$error_bd_seq."<br>";
			}
			
			//numero de duplicados
			$sql_numero_de_personas="";
			$sql_numero_de_personas.=" SELECT count(*) as numero_registros FROM $nombre_vista_index_duplicados  ; ";
			$error_bd_seq="";
			$array_numero_de_personas=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_personas, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados  dejado: ".$error_bd_seq."<br>";
			}
			
			$numero_personas=0;
			
			if(count($array_numero_de_personas)>0 && is_array($array_numero_de_personas))
			{
				$numero_personas=$array_numero_de_personas[0]["numero_registros"];
			}
			//fin numero de duplicados
						
			$limite_personas=0;
			$contador_offset_personas=0;
			//a diferencia de los otros bloques donde eran bloques
			//de registros delarchivo aqui es un bloque de mil personas
			$numero_registros_bloque_personas=150;
			$fue_cerrada_la_gui2=false;
			$numero_filas_donde_esta_afiliado_actual=0;
			$numero_duplicados_procesados_hasta_el_momento=0;
			if($numero_personas>0)
			{								
			    while($contador_offset_personas<$numero_personas)
			    {
				if($fue_cerrada_la_gui2==false)
				{
				    if(connection_aborted()==true)
				    {
					$fue_cerrada_la_gui2=true;
				    }
				}//fin if verifica si el usuario cerro la pantalla
				
				
					    
				$limite_personas=$numero_registros_bloque_personas;
					
				if( ($contador_offset_personas+$numero_registros_bloque_personas)>=$numero_personas)
				{
					$limite_personas=$numero_registros_bloque_personas+($numero_personas-$contador_offset_personas);
				}
				
				$sql_query_busqueda_personas_bloques="";
				$sql_query_busqueda_personas_bloques.="SELECT * FROM $nombre_vista_index_duplicados LIMIT $limite_personas OFFSET $contador_offset_personas;  ";
				$error_bd_seq="";
				$resultados_query_pyp4505_duplicados=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda_personas_bloques,$error_bd_seq);
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.="ERROR AL CONSULTAR de vista de las personas: ".$error_bd_seq."<br>";
				}
				
				if(count($resultados_query_pyp4505_duplicados)>0 && is_array($resultados_query_pyp4505_duplicados))
				{
					foreach($resultados_query_pyp4505_duplicados as $duplicado_actual)
					{
						//CANCELA EJECUCION DEL ARCHIVO			    
						$verificar_si_ejecucion_fue_cancelada="";
						$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_consolidando_ro_actualmente ";
						$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
						$verificar_si_ejecucion_fue_cancelada.=" AND cod_eapb='".$cod_eapb."' ";	    
						$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" AND nick_usuario='".$nick_user."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" AND fecha_validacion='".$fecha_actual."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" AND hora_validacion='".$tiempo_actual."'  ";
						$verificar_si_ejecucion_fue_cancelada.=" ; ";
						$error_bd="";
						$resultados_si_ejecucion_fue_cancelada=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($verificar_si_ejecucion_fue_cancelada, $error_bd);
						if($error_bd!="")
						{
							if($fue_cerrada_la_gui2==false)
							{
								echo "<script>alert('error al consultar si se cancelo la ejecucion ');</script>";
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
						//FIN CANCELA EJECUCION DEL ARCHIVO
				
					    //TOMA LOS DATOS DEL DUPLICADO ACTUAL DE LA VISTA DE LA TABLA DEL INDEXADOR
					    $tipo_id_duplicado_actual=trim($duplicado_actual["campo_3_tipo_id"]);
					    $numero_id_duplicado_actual=trim($duplicado_actual["campo_4_numero_id"]);
					    $lista_string_filas_donde_esta_duplicado=trim($duplicado_actual["lista_lineas_donde_hay_duplicados"]);
					    $array_filas_correspondientes_al_duplicado_actual=explode(";;",$lista_string_filas_donde_esta_duplicado);
					    $numero_filas_donde_esta_afiliado_actual=count($array_filas_correspondientes_al_duplicado_actual);
					    if($numero_filas_donde_esta_afiliado_actual>1)
					    {						    
						$numero_duplicados_procesados_hasta_el_momento+=$numero_filas_donde_esta_afiliado_actual;
					    }//fin if
					    //FIN TOMA LOS DATOS DEL DUPLICADO ACTUAL DE LA VISTA DE LA TABLA DEL INDEXADOR
					    
					    
					    $bool_ya_se_proceso=false;
					    
					    //LEE EL ARCHIVO CORREGIDO PARA CADA LINEA Y LO SUBE A BD
					    
						//CREACION DEL ARCHIVO DE REGISTROS DUPLICADOS DEL AFILIADO ACTUAL
						$primera_linea_duplicados_afiliado_actual=true;
						$ruta_temporal_duplicados_afiliado_actual=$rutaTemporal.$nombre_archivo_para_zip."tmpdupl".$fecha_para_archivo.".txt";
						//se remplaza el archivo si ya existe con modo w		
						$file_temporal_duplicados_afiliado_actual = fopen($ruta_temporal_duplicados_afiliado_actual, "w") or die("fallo la creacion del archivo");		    
						fclose($file_temporal_duplicados_afiliado_actual);
						
						$ruta_temporal_nsecuencia_duplicados_afiliado_actual=$rutaTemporal.$nombre_archivo_para_zip."secdupl".$fecha_para_archivo.".txt";
						//se remplaza el archivo si ya existe con modo w		
						$file_temporal_nsecuencia_duplicados_afiliado_actual = fopen($ruta_temporal_nsecuencia_duplicados_afiliado_actual, "w") or die("fallo la creacion del archivo");		    
						fclose($file_temporal_nsecuencia_duplicados_afiliado_actual);	
						//FIN CREACION DEL ARCHIVO DE REGISTROS DUPLICADOS DEL AFILIADO ACTUAL
						
					    foreach($array_filas_correspondientes_al_duplicado_actual as $numero_linea_dupl)
					    {						
						
						//lee el archivo de texto en la linea especifica
						$linea_act = intval($numero_linea_dupl) ; 
						$fileHandler = new SplFileObject($ruta_escribir_archivo_con_duplicados);		
						$fileHandler->seek($linea_act);
						$linea_duplicada_del_afiliado=$fileHandler->current();
						$array_campos_del_duplicado_del_afiliado=explode("|",$linea_duplicada_del_afiliado);
						//fin lee el archivo de texto en la linea especifica
						
						//lee el archivo con el numero de secuencia del registro
						$fileHandler_2 = new SplFileObject($ruta_temp_numero_secuencia);		
						$fileHandler_2->seek($linea_act);
						$linea_posee_secuencia_prestador_desde_txt=$fileHandler_2->current();
						$array_posee_secuencia_prestador_desde_txt=explode("|",$linea_posee_secuencia_prestador_desde_txt);
						$numero_secuencia_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[0]);
						$prestador_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[1]);
						$regimen_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[2]);
						//fin lee el archivo con el numero de secuencia del registro
						
						
						//PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 1
						//se abre con modo a para que adicione que no subio
						$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
						
						$identificadores_de_cambios_duplicados_registro="";
						$identificadores_de_cambios_duplicados_registro.=$nombre_archivo_para_zip."||";//nombre del archivo
						$identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
						$identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
						$identificadores_de_cambios_duplicados_registro.="DUPLICADO"."||";//identificador si es duplicado, unico, final
						$identificadores_de_cambios_duplicados_registro.=$fecha_corte_bd."||";//fecha de corte
						$identificadores_de_cambios_duplicados_registro.="PYP"."||";//tipo reporte
						$identificadores_de_cambios_duplicados_registro.=$cod_eapb."||";
						$identificadores_de_cambios_duplicados_registro.=$array_campos_del_duplicado_del_afiliado[2]."||";//codigo prestador del registro en el archivo
						$identificadores_de_cambios_duplicados_registro.="CONSOLIDADO"."||";//reparacion o consolidado
						if($tipo_tiempo_periodo=="trimestral")
						{
						    $identificadores_de_cambios_duplicados_registro.="TRIMESTRAL"."||";
						}
						else if($tipo_tiempo_periodo=="mensual")
						{
						    $identificadores_de_cambios_duplicados_registro.="MENSUAL"."||";
						}
						$identificadores_de_cambios_duplicados_registro.=$array_campos_del_duplicado_del_afiliado[1]."||";//numero registro
						fwrite($file_cambios_duplicados_registro, $identificadores_de_cambios_duplicados_registro.$linea_duplicada_del_afiliado);
						
						//cierra el archivo del log reparacion de duplicados
						fclose($file_cambios_duplicados_registro);
						//FIN PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 1
						
						
						
						//if mira que la linea contega los 119 campos
						if(count($array_campos_del_duplicado_del_afiliado)==119)
						{
						    //if en caso de que solo haya un elemento en la lista de filas de duplicados
						    //se sube a corregidos sin duplicados debido a que si hay solo una fila
						    //es porque no posee duplicados
						    if($numero_filas_donde_esta_afiliado_actual==1)
						    {
							
							//se coloca que ya se proceso en verdadero debido a que no es un duplicado
							$bool_ya_se_proceso=true;
						    }//fin if solo habia una fila en la lista por ende no tenia duplicados
						    else if($numero_filas_donde_esta_afiliado_actual>1)
						    {
							/*
							//sube a gioss_temp_dupl_afiliado_actual_consolidado_pyp4505
							//para agrupar solo los registros duplicados para dicho afiliado
							$query_subir_registro_corregido="";
							$query_subir_registro_corregido.=" INSERT INTO ";
							$query_subir_registro_corregido.=" gioss_temp_dupl_afiliado_actual_consolidado_pyp4505 ";				
							$query_subir_registro_corregido.=" ( ";				
							$numero_actual_campo_registro_corregido=0;
							while($numero_actual_campo_registro_corregido<=118)
							{
								$query_subir_registro_corregido.=" campo_pyp4505_con_numero_orden_".$numero_actual_campo_registro_corregido." , ";
								$numero_actual_campo_registro_corregido++;
							}//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
							$query_subir_registro_corregido.=" numero_secuencia, ";
							$query_subir_registro_corregido.=" regimen, ";
							$query_subir_registro_corregido.=" tipo_id_usuario, ";
							$query_subir_registro_corregido.=" id_usuario, ";
							$query_subir_registro_corregido.=" nick_usuario, ";
							$query_subir_registro_corregido.=" numero_registro, ";
							$query_subir_registro_corregido.=" fecha_corte_reporte, ";
							$query_subir_registro_corregido.=" fecha_de_generacion, ";
							$query_subir_registro_corregido.=" hora_generacion, ";
							$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
							$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
							$query_subir_registro_corregido.=" nombre_archivo_pyp ";
							$query_subir_registro_corregido.=" ) ";
							$query_subir_registro_corregido.=" VALUES ";
							$query_subir_registro_corregido.=" ( ";				
							$numero_actual_campo_registro_corregido=0;
							while($numero_actual_campo_registro_corregido<=118)
							{
							    if($numero_actual_campo_registro_corregido!=3 &&  $numero_actual_campo_registro_corregido!=4)
							    {
								$query_subir_registro_corregido.="'".trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]))."',";								
							    }
							    else if($numero_actual_campo_registro_corregido==3)
							    {
								$query_subir_registro_corregido.="'".$tipo_id_duplicado_actual."',";
							    
							    }
							    else if($numero_actual_campo_registro_corregido==4)
							    {
								$query_subir_registro_corregido.="'".$numero_id_duplicado_actual."',";
								
								
							    }//fin else if
							    
							    //verifica si los campos indentificadores de duplicados TI y numero ID Corresponden
							    if($numero_actual_campo_registro_corregido==3
							       || $numero_actual_campo_registro_corregido==4
							       )
							    {
								$tipo_id_temp_del_array=trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[3]));
								$num_id_temp_del_array=trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[4]));
								if($num_id_temp_del_array!=$numero_id_duplicado_actual
								   || $tipo_id_temp_del_array!=$tipo_id_duplicado_actual
								   )
								{
									$mensaje_verificador_id_dupl="";
									$mensaje_verificador_id_dupl.="numero registro: ".$array_campos_del_duplicado_del_afiliado[1];
									$mensaje_verificador_id_dupl.=" los id son diferentes del array: $tipo_id_temp_del_array $num_id_temp_del_array ";
									$mensaje_verificador_id_dupl.="de la tabla indexador: $tipo_id_duplicado_actual $numero_id_duplicado_actual ";
									$mensaje_verificador_id_dupl.=" lista: $lista_string_filas_donde_esta_duplicado .";
									echo "<script>alert('$mensaje_verificador_id_dupl');</script>";
								}
							    }//fin if
							    //FIN verifica si los campos indentificadores de duplicados TI y numero ID Corresponden
							    
							    $numero_actual_campo_registro_corregido++;
							}//fin while con los valores de los campos 4505 a insertar en la tabla
							$query_subir_registro_corregido.="'".$numero_secuencia_desde_txt."',";
							$query_subir_registro_corregido.="'".$regimen_desde_txt."',";
							$query_subir_registro_corregido.="'".$tipo_id."',";
							$query_subir_registro_corregido.="'".$identificacion."',";
							$query_subir_registro_corregido.="'".$nick_user."',";	
							$query_subir_registro_corregido.="'".$array_campos_del_duplicado_del_afiliado[1]."',";							
							$query_subir_registro_corregido.="'".$fecha_corte_bd."',";
							$query_subir_registro_corregido.="'".$fecha_actual."',";
							$query_subir_registro_corregido.="'".$tiempo_actual."',";
							$query_subir_registro_corregido.="'".$cod_eapb."',";
							$query_subir_registro_corregido.="'".$prestador_desde_txt."',";
							$query_subir_registro_corregido.="'".$nombre_archivo_para_zip."'";
							$query_subir_registro_corregido.=" ) ";
							$query_subir_registro_corregido.=" ; ";
							$error_bd_seq="";		
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR Al subir en la tabla gioss_temp_dupl_afiliado_actual_consolidado_pyp4505 ".procesar_mensaje($error_bd_seq).".<br>";
								
							}
							//fin sube a gioss_temp_dupl_afiliado_actual_consolidado_pyp4505
							*/
							
							//PARTE CREA TXT TEMPORAL PARA DUPLICADOS DEL COINCIDENTE ACTUAL							
							$file_temporal_duplicados_afiliado_actual = fopen($ruta_temporal_duplicados_afiliado_actual, "a") or die("fallo la creacion del archivo");							
							$registro_para_txt="";								
							//corresponden los id de el registro con los id del coincidente actual
							$tipo_id_temp_del_array=trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[3]));
							$num_id_temp_del_array=trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[4]));						
							if($num_id_temp_del_array==$numero_id_duplicado_actual
							&& $tipo_id_temp_del_array==$tipo_id_duplicado_actual
							)//fin parentesis condicion
							{
								$registro_para_txt.=trim($linea_duplicada_del_afiliado);
							}//fin if
							else 
							{
								$mensaje_verificador_id_dupl="";
								$mensaje_verificador_id_dupl.="(para txt) numero registro: ".$array_campos_del_duplicado_del_afiliado[1];
								$mensaje_verificador_id_dupl.=" los id son diferentes del array: $tipo_id_temp_del_array $num_id_temp_del_array ";
								$mensaje_verificador_id_dupl.="de la tabla indexador: $tipo_id_duplicado_actual $numero_id_duplicado_actual ";
								$mensaje_verificador_id_dupl.=" lista: $lista_string_filas_donde_esta_duplicado .";
								echo "<script>alert('$mensaje_verificador_id_dupl');</script>";
							}
							//fin corresponden los id de el registro con los id del coincidente actual							
							if($primera_linea_duplicados_afiliado_actual==true)
							{
								$primera_linea_duplicados_afiliado_actual=false;
								fwrite($file_temporal_duplicados_afiliado_actual, $registro_para_txt);								
								
							}//fin if
							else
							{
								fwrite($file_temporal_duplicados_afiliado_actual, "\n".$registro_para_txt);
							}//fin else
							fclose($file_temporal_duplicados_afiliado_actual);
							
							$file_temporal_nsecuencia_duplicados_afiliado_actual = fopen($ruta_temporal_nsecuencia_duplicados_afiliado_actual, "a") or die("fallo la creacion del archivo");							
							$secuencia_prestador_para_txt="";								
							//corresponden los id de el registro con los id del coincidente actual
							$tipo_id_temp_del_array=trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[3]));
							$num_id_temp_del_array=trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[4]));						
							if($num_id_temp_del_array==$numero_id_duplicado_actual
							&& $tipo_id_temp_del_array==$tipo_id_duplicado_actual
							)//fin parentesis condicion
							{
								$secuencia_prestador_para_txt.=trim($linea_posee_secuencia_prestador_desde_txt);
							}//fin if
							else 
							{
								$mensaje_verificador_id_dupl="";
								$mensaje_verificador_id_dupl.="(para txt) numero registro: ".$array_campos_del_duplicado_del_afiliado[1];
								$mensaje_verificador_id_dupl.=" los id son diferentes del array: $tipo_id_temp_del_array $num_id_temp_del_array ";
								$mensaje_verificador_id_dupl.="de la tabla indexador: $tipo_id_duplicado_actual $numero_id_duplicado_actual ";
								$mensaje_verificador_id_dupl.=" lista: $lista_string_filas_donde_esta_duplicado .";
								echo "<script>alert('$mensaje_verificador_id_dupl');</script>";
							}
							//fin corresponden los id de el registro con los id del coincidente actual							
							if($primera_linea_duplicados_afiliado_actual==true)
							{
								$primera_linea_duplicados_afiliado_actual=false;
								fwrite($file_temporal_nsecuencia_duplicados_afiliado_actual, $secuencia_prestador_para_txt);								
								
							}//fin if
							else
							{
								fwrite($file_temporal_nsecuencia_duplicados_afiliado_actual, "\n".$secuencia_prestador_para_txt);
							}//fin else
							fclose($file_temporal_nsecuencia_duplicados_afiliado_actual);	
							//FIN PARTE CREA TXT TEMPORAL PARA DUPLICADOS DEL COINCIDENTE ACTUAL
							
							//para agrupar solo los registros duplicados para dicho afiliado							
							//sube a gioss_consulta_reporte_obligatorio_pyp4505_solo_duplicados para reportes futuros
							$query_subir_registro_corregido="";
							$query_subir_registro_corregido.=" INSERT INTO ";
							$query_subir_registro_corregido.=" gioss_consulta_reporte_obligatorio_pyp4505_solo_duplicados ";				
							$query_subir_registro_corregido.=" ( ";				
							$numero_actual_campo_registro_corregido=0;
							while($numero_actual_campo_registro_corregido<=118)
							{
								$query_subir_registro_corregido.=" campo_pyp4505_con_numero_orden_".$numero_actual_campo_registro_corregido." , ";
								$numero_actual_campo_registro_corregido++;
							}//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
							$query_subir_registro_corregido.=" numero_secuencia, ";
							$query_subir_registro_corregido.=" regimen, ";
							$query_subir_registro_corregido.=" numero_registro, ";
							$query_subir_registro_corregido.=" fecha_corte_reporte, ";
							$query_subir_registro_corregido.=" fecha_de_generacion, ";
							$query_subir_registro_corregido.=" hora_generacion, ";
							$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";
							$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
							$query_subir_registro_corregido.=" nombre_archivo_pyp ";
							$query_subir_registro_corregido.=" ) ";
							$query_subir_registro_corregido.=" VALUES ";
							$query_subir_registro_corregido.=" ( ";				
							$numero_actual_campo_registro_corregido=0;
							while($numero_actual_campo_registro_corregido<=118)
							{
								$query_subir_registro_corregido.="'".trim(alphanumericAndSpace4($array_campos_del_duplicado_del_afiliado[$numero_actual_campo_registro_corregido]))."',";
								$numero_actual_campo_registro_corregido++;
							}//fin while con los valores de los campos 4505 a insertar en la tabla
							$query_subir_registro_corregido.="'".$numero_secuencia_desde_txt."',";
							$query_subir_registro_corregido.="'".$regimen_desde_txt."',";
							$query_subir_registro_corregido.="'".$array_campos_del_duplicado_del_afiliado[1]."',";							
							$query_subir_registro_corregido.="'".$fecha_corte_bd."',";
							$query_subir_registro_corregido.="'".$fecha_actual."',";
							$query_subir_registro_corregido.="'".$tiempo_actual."',";
							$query_subir_registro_corregido.="'".$cod_eapb."',";
							$query_subir_registro_corregido.="'".$prestador_desde_txt."',";
							$query_subir_registro_corregido.="'".$nombre_archivo_para_zip."'";
							$query_subir_registro_corregido.=" ) ";
							$query_subir_registro_corregido.=" ; ";
							$error_bd_seq="";		
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR Al subir en la tabla gioss_consulta_reporte_obligatorio_pyp4505_solo_duplicados ".procesar_mensaje($error_bd_seq).".<br>";
								
							}
							//fin sube a gioss_consulta_reporte_obligatorio_pyp4505_solo_duplicados para reportes futuros
							
							
						    }//fin else if si habian varias filas en la lista por ende tiene duplicados el afiliado 
						}//fin if si la linea posee 119 campos
						
					    }//fin foreach							
					    //FIN LEE EL ARCHIVO CORREGIDO PARA CADA LINEA Y LO SUBE A BD
					    
					    if($bool_ya_se_proceso==false)
					    {
						/*
						$nombre_vista_con_los_duplicados_del_afiliado_actual="rodpa4505".$nombre_archivo_para_zip.$nick_user.$fecha_y_hora_para_view;
						
						$sql_vista_duplicados_de_la_persona_actual ="";
						$sql_vista_duplicados_de_la_persona_actual.="CREATE OR REPLACE VIEW $nombre_vista_con_los_duplicados_del_afiliado_actual ";
						$sql_vista_duplicados_de_la_persona_actual.=" AS  ";					
						$sql_vista_duplicados_de_la_persona_actual .="SELECT * from gioss_temp_dupl_afiliado_actual_consolidado_pyp4505  ";	
						$sql_vista_duplicados_de_la_persona_actual.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" nombre_archivo_pyp='".$nombre_archivo_para_zip."'  ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" tipo_id_usuario='$tipo_id' ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" id_usuario='$identificacion' ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" nick_usuario='$nick_user' ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" campo_pyp4505_con_numero_orden_3='$tipo_id_duplicado_actual' ";
						$sql_vista_duplicados_de_la_persona_actual.=" AND ";
						$sql_vista_duplicados_de_la_persona_actual.=" campo_pyp4505_con_numero_orden_4='$numero_id_duplicado_actual' ";			    
						$sql_vista_duplicados_de_la_persona_actual.=" ORDER BY numero_registro asc ";
						$sql_vista_duplicados_de_la_persona_actual.=";";
						$error_bd_seq="";		
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_duplicados_de_la_persona_actual, $error_bd_seq);
						if($error_bd_seq!="")
						{
						    $mensajes_error_bd.=" ERROR al crear vista de duplicados de la persona actual para corregir en uno solo por persona: ".$error_bd_seq."<br>";
						}
						
						//numero de duplicados del duplicado
						$sql_numero_de_personas_de_duplicado="";
						$sql_numero_de_personas_de_duplicado.=" SELECT count(*) as numero_registros FROM $nombre_vista_con_los_duplicados_del_afiliado_actual  ; ";
						$error_bd_seq="";
						$array_numero_de_personas_de_duplicado=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_numero_de_personas_de_duplicado, $error_bd_seq);
						if($error_bd_seq!="")
						{
							$mensajes_error_bd.=" ERROR al consultar numero de registros del total de los duplicados del duplicado: ".$error_bd_seq."<br>";
						}
						
						$numero_personas_de_duplicado=0;
						if(count($array_numero_de_personas_de_duplicado)>0 && is_array($array_numero_de_personas_de_duplicado))
						{
							$numero_personas_de_duplicado=$array_numero_de_personas_de_duplicado[0]["numero_registros"];
						}
						//numeros de duplicados del duplicado
						*/
						    
						    //PARTE DONDE LLAMA A LA FUNCION QUE CONTIENE LOS CRITERIOS PARA PROCESAR LOS DUPLICADOS
						    //enves del numero de secuencia se usara el ultimo numero de registro(fila)
						    $numero_registro_para_procesado="";
						    $cod_prestador_para_procesado="";
						    $numero_secuencia_para_procesado="";
						    $regimen_para_procesado="";
						    //en la funcion se hara falso si no se proceso los duplicados al haber campos vacios
						    $bool_fueron_procesados_duplicados_en_un_registro=true;
						    
						    $array_campos_procesados_de_los_duplicados_del_duplicado=array();
						    
						    //FUNCION QUE REPARA DUPLICADOS POR MEDIO BD
						    /*
						    $array_campos_procesados_de_los_duplicados_del_duplicado=reparacion_campos_duplicados($tipo_id_duplicado_actual,
																	$numero_id_duplicado_actual,
																	$fecha_actual,
																	$tiempo_actual,
																	$nick_user,
																	$identificacion,
																	$tipo_id,
																	$numero_personas_de_duplicado, //cambio
																	$nombre_vista_con_los_duplicados_del_afiliado_actual,//cambio
																	$numero_secuencia_para_procesado,
																	$numero_registro_para_procesado,
																	$cod_prestador_para_procesado,
																	$regimen_para_procesado,
																	$bool_fueron_procesados_duplicados_en_un_registro,
																	$contador_offset_personas,
																	$contador_duplicado_para_excluidos,
																	$mensajes_error_bd,
																	$coneccionBD);
						    */
						    //FIN FUNCION QUE REPARA DUPLICADOS POR MEDIO BD
						    
						    //FUNCION QUE REPARA DUPLICADOS POR MEDIO TXT
						    $array_campos_procesados_de_los_duplicados_del_duplicado=reparacion_duplicados_por_txt($tipo_id_duplicado_actual,
																	$numero_id_duplicado_actual,
																	$fecha_actual,
																	$tiempo_actual,
																	$fecha_corte_bd,
																	$nick_user,
																	$identificacion,
																	$tipo_id,
																	$numero_filas_donde_esta_afiliado_actual, //nuevo
																	$ruta_temporal_duplicados_afiliado_actual, //nuevo
																	$ruta_temporal_nsecuencia_duplicados_afiliado_actual,//adicionado
																	$numero_secuencia_para_procesado,
																	$numero_registro_para_procesado,
																	$cod_prestador_para_procesado,
																	$regimen_para_procesado,
																	$bool_fueron_procesados_duplicados_en_un_registro,
																	$contador_offset_personas,
																	$contador_duplicado_para_excluidos,
																	$mensajes_error_bd,
																	$coneccionBD);
						    //FIN FUNCION QUE REPARA DUPLICADOS POR MEDIO TXT
						    //fin parte donde se procesaran los duplicados
						    
						    //insertando registro procesado
						    if($bool_fueron_procesados_duplicados_en_un_registro==true)
						    {
							$nlinea_que_tomo_duplicado="";
							if(isset($array_campos_procesados_de_los_duplicados_del_duplicado[1]))
							{
							    $nlinea_que_tomo_duplicado=$array_campos_procesados_de_los_duplicados_del_duplicado[1];
							}
							
							//campos unificado del duplicado pre correccion
							$array_pre_correccion_unificado_dupl=array();
							$cont_asign=0;
							while($cont_asign<count($array_campos_procesados_de_los_duplicados_del_duplicado))
							{
							    $array_pre_correccion_unificado_dupl[]=$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_asign];
							    $cont_asign++;
							}
							//fin campos unificado del duplicado pre correccion
							
							//PARTE CORRECCION DE CAMPOS PARA EL DUPLICADO CORREGIDO
							$fixer= new corrector_registros_para_duplicados_en_consolidado();
							
							$cont_total_registros_del_duplicado_corregido_antes=count($array_campos_procesados_de_los_duplicados_del_duplicado);
							
							$linea_duplicado_corregido_a_reparar="";
							
							$cont_orden_campo_pyp=0;									
							while($cont_orden_campo_pyp<=118)
							{
							    if($linea_duplicado_corregido_a_reparar!=""){$linea_duplicado_corregido_a_reparar.="|";}
							    $linea_duplicado_corregido_a_reparar.=$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_pyp];
							    $cont_orden_campo_pyp++;
							}//fin while
							$string_campos_procesados_de_los_duplicados_del_duplicado=$fixer->correccion_errores_campos_PyP_4505($linea_duplicado_corregido_a_reparar,
																			   $array_campos_procesados_de_los_duplicados_del_duplicado[1],
																			   $consecutivo_errores,
																			   $fecha_corte_bd,
																			   $coneccionBD
																			   )["registro_corregido"];
							
							$array_campos_procesados_de_los_duplicados_del_duplicado=explode("|",$string_campos_procesados_de_los_duplicados_del_duplicado);
							
							$cont_total_registros_del_duplicado_corregido_despues=count($array_campos_procesados_de_los_duplicados_del_duplicado);
							//echo "<script>alert('antes: $cont_total_registros_del_duplicado_corregido_antes, despues: $cont_total_registros_del_duplicado_corregido_despues');</script>";
							//FIN PARTE CORRECCION DE CAMPOS PARA EL DUPLICADO CORREGIDO
							
							//PARTE ESCRIBE LOG CAMBIOS CORRECCION UNIFICADO DEL DUPLICADO
							if(count($array_pre_correccion_unificado_dupl)==119
							   && count($array_campos_procesados_de_los_duplicados_del_duplicado)==119
							   )
							{
							    //se abre con modo a para que adicione que no subio
							    
							    $file_cambios_real_dupl_registro_2 = fopen($ruta_cambios_real_dupl_campos_2, "a") or die("fallo la creacion del archivo");
							    
							    $cont_log_cambios=0;
							    while($cont_log_cambios<119)
							    {
								if(trim($array_pre_correccion_unificado_dupl[$cont_log_cambios])!=trim($array_campos_procesados_de_los_duplicados_del_duplicado[$cont_log_cambios])
								   && $cont_log_cambios!=1//no interesa el consecutivo aca
								   )
								{
								    $linea_log_cambos_realizados_correccion="";
								    $linea_log_cambos_realizados_correccion.="La persona  TI: ".trim($array_pre_correccion_unificado_dupl[3])." ".trim($array_pre_correccion_unificado_dupl[4])." ";
								    $linea_log_cambos_realizados_correccion.=" reparo el campo numero $cont_log_cambios ";
								    $linea_log_cambos_realizados_correccion.=" con un valor inicial de ".trim($array_pre_correccion_unificado_dupl[$cont_log_cambios]);
								    $linea_log_cambos_realizados_correccion.=" transformado en ";
								    $linea_log_cambos_realizados_correccion.=" el valor final de ".trim($array_campos_procesados_de_los_duplicados_del_duplicado[$cont_log_cambios]);
								    $linea_log_cambos_realizados_correccion.=" de acuerdo a los criterios de correccion ";
								    $linea_log_cambos_realizados_correccion.="";
								    
								    fwrite($file_cambios_real_dupl_registro_2, "\n".$linea_log_cambos_realizados_correccion);
								}//fin if solo escribe si hubo cambios en el campo al corregir
								
								$cont_log_cambios++;
							    }//fin while
							    
							    //cierra el archivo del log reparacion de duplicados
							    fclose($file_cambios_real_dupl_registro_2);
							}//fin if
							//FIN PARTE ESCRIBE LOG CAMBIOS CORRECCION UNIFICADO DEL DUPLICADO
							
							//PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 2
							//se abre con modo a para que adicione que no subio
							$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
							
							$identificadores_de_cambios_duplicados_registro="";
							$identificadores_de_cambios_duplicados_registro.=$nombre_archivo_para_zip."||";//nombre del archivo
							$identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
							$identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
							$identificadores_de_cambios_duplicados_registro.="--UNICO--"."||";//identificador si es duplicado, unico, final
							$identificadores_de_cambios_duplicados_registro.=$fecha_corte_bd."||";//fecha de corte
							$identificadores_de_cambios_duplicados_registro.="PYP"."||";//tipo reporte
							$identificadores_de_cambios_duplicados_registro.=$cod_eapb."||";							    
							$identificadores_de_cambios_duplicados_registro.=$array_campos_procesados_de_los_duplicados_del_duplicado[2]."||";//codigo prestador del registro en el archivo
							$identificadores_de_cambios_duplicados_registro.="CONSOLIDADO"."||";//reparacion o consolidado
							if($tipo_tiempo_periodo=="trimestral")
							{
							    $identificadores_de_cambios_duplicados_registro.="TRIMESTRAL"."||";
							}
							else if($tipo_tiempo_periodo=="mensual")
							{
							    $identificadores_de_cambios_duplicados_registro.="MENSUAL"."||";
							}
							$identificadores_de_cambios_duplicados_registro.="U".$nlinea_que_tomo_duplicado."||";
							fwrite($file_cambios_duplicados_registro, $identificadores_de_cambios_duplicados_registro.$linea_duplicado_corregido_a_reparar);
							
							
							//se abre con modo a para que adicione que no subio
							$file_cambios_duplicados_registro = fopen($ruta_cambios_duplicados_campos, "a") or die("fallo la creacion del archivo");
							
							$identificadores_de_cambios_duplicados_registro="";
							$identificadores_de_cambios_duplicados_registro.=$nombre_archivo_para_zip."||";//nombre del archivo
							$identificadores_de_cambios_duplicados_registro.=$fecha_actual."||";//fecha correccion
							$identificadores_de_cambios_duplicados_registro.=$tiempo_actual."||";//hora correcion
							$identificadores_de_cambios_duplicados_registro.="--FINAL--"."||";//identificador si es duplicado, unico, final
							$identificadores_de_cambios_duplicados_registro.=$fecha_corte_bd."||";//fecha de corte
							$identificadores_de_cambios_duplicados_registro.="PYP"."||";//tipo reporte
							$identificadores_de_cambios_duplicados_registro.=$cod_eapb."||";							    
							$identificadores_de_cambios_duplicados_registro.=$array_campos_procesados_de_los_duplicados_del_duplicado[2]."||";//codigo prestador del registro en el archivo
							$identificadores_de_cambios_duplicados_registro.="CONSOLIDADO"."||";//reparacion o consolidado
							if($tipo_tiempo_periodo=="trimestral")
							{
							    $identificadores_de_cambios_duplicados_registro.="TRIMESTRAL"."||";
							}
							else if($tipo_tiempo_periodo=="mensual")
							{
							    $identificadores_de_cambios_duplicados_registro.="MENSUAL"."||";
							}
							$identificadores_de_cambios_duplicados_registro.="F".$nlinea_que_tomo_duplicado."||";
							fwrite($file_cambios_duplicados_registro, "\n".$identificadores_de_cambios_duplicados_registro.$string_campos_procesados_de_los_duplicados_del_duplicado."\n");
							
							/*
							if($string_campos_procesados_de_los_duplicados_del_duplicado=="")
							{
							    echo "<script>alert('esta vacio');</script>";
							}
							else
							{
							    echo "<script>alert('$string_campos_procesados_de_los_duplicados_del_duplicado');</script>";
							}							    
							*/
							
							//cierra el archivo del log reparacion de duplicados
							fclose($file_cambios_duplicados_registro);
							//FIN PARTE ESCRIBE LOG REPARACION DE DUPLICADOS PARTE 2
							
							$sql_insert_procesado_en_reporte_obligatorio="";
							$sql_insert_procesado_en_reporte_obligatorio.=" INSERT INTO ";
							$sql_insert_procesado_en_reporte_obligatorio.=" gioss_consulta_reporte_obligatorio_pyp4505_exitoso ";									    
							$sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
							$cont_orden_campo_pyp=0;
							while($cont_orden_campo_pyp<=118)
							{
								$sql_insert_procesado_en_reporte_obligatorio.=" campo_pyp4505_con_numero_orden_".$cont_orden_campo_pyp." , ";
								$cont_orden_campo_pyp++;
							}//fin while para nombres columnas de bd correspondientes a los campos de 4505 a insertar
							$sql_insert_procesado_en_reporte_obligatorio.=" numero_secuencia, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" regimen, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" numero_registro, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" fecha_corte_reporte, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" fecha_de_generacion, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" hora_generacion, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_eapb_generadora, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" codigo_entidad_prestadora, ";
							$sql_insert_procesado_en_reporte_obligatorio.=" nombre_archivo_pyp ";
							$sql_insert_procesado_en_reporte_obligatorio.=" ) ";
							$sql_insert_procesado_en_reporte_obligatorio.=" VALUES ";
							$sql_insert_procesado_en_reporte_obligatorio.=" ( ";				
							$cont_orden_campo_pyp=0;
							while($cont_orden_campo_pyp<=118)
							{
								$sql_insert_procesado_en_reporte_obligatorio.="'".$array_campos_procesados_de_los_duplicados_del_duplicado[$cont_orden_campo_pyp]."',";
								$cont_orden_campo_pyp++;
							}//fin while con los valores de los campos 4505 a insertar en la tabla de reporte obligatorio
							$sql_insert_procesado_en_reporte_obligatorio.="'".$numero_secuencia_para_procesado."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$regimen_para_procesado."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$numero_registro_para_procesado."',";								
							$sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_corte_bd."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$fecha_actual."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$tiempo_actual."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$cod_eapb."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$cod_prestador_para_procesado."',";
							$sql_insert_procesado_en_reporte_obligatorio.="'".$nombre_archivo_para_zip."'";
							$sql_insert_procesado_en_reporte_obligatorio.=" ) ";
							$sql_insert_procesado_en_reporte_obligatorio.=" ; ";
							$error_bd_seq="";		
							$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insert_procesado_en_reporte_obligatorio, $error_bd_seq);
							if($error_bd_seq!="")
							{
								$mensajes_error_bd.=" ERROR Al subir en la tabla gioss_consulta_reporte_obligatorio_pyp4505_exitoso despues de reparar duplicados en un unico registro: ".$error_bd_seq."<br>";
							}
						    }//fin if si fueron procesados duplicados inserta el porcesado en la tabla de archivos reportados obligatorios exitosos de 4505
						    //fin insertando registro procesado
						    
						    //BORRANDO VISTA DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
						    /*
						    $sql_borrar_vista_duplicados_en_uno_solo="";
						    $sql_borrar_vista_duplicados_en_uno_solo.=" DROP VIEW $nombre_vista_con_los_duplicados_del_afiliado_actual ; ";							
						    $error_bd="";		
						    $bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_en_uno_solo, $error_bd);		
						    if($error_bd!="")
						    {
							if(connection_aborted()==false)
							{
							    echo "<script>alert('error al borrar la vista duplicados del afiliado actual');</script>";
							}
							    $mensajes_error_bd.=" ERROR Al al borrar la vista duplicados en uno solo: ".$error_bd."<br>";
						    }
						    */
						    //FIN BORRANDO VISTA DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
						    
						    //BORRANDO INFORMACION DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
						    /*
						    $sql_delete_corregidos_temp="";
						    $sql_delete_corregidos_temp.=" DELETE FROM gioss_temp_dupl_afiliado_actual_consolidado_pyp4505  ";
						    $sql_delete_corregidos_temp.=" WHERE fecha_de_generacion='$fecha_actual'  AND hora_generacion='$tiempo_actual' ";
						    $sql_delete_corregidos_temp.=" AND ";
						    $sql_delete_corregidos_temp.=" codigo_entidad_eapb_generadora='".$cod_eapb."'  ";
						    $sql_delete_corregidos_temp.=" AND ";
						    $sql_delete_corregidos_temp.=" nombre_archivo_pyp='".$nombre_archivo_para_zip."'  ";
						    $sql_delete_corregidos_temp.=" AND ";
						    $sql_delete_corregidos_temp.=" tipo_id_usuario='$tipo_id' ";
						    $sql_delete_corregidos_temp.=" AND ";
						    $sql_delete_corregidos_temp.=" id_usuario='$identificacion' ";
						    $sql_delete_corregidos_temp.=" AND ";
						    $sql_delete_corregidos_temp.=" nick_usuario='$nick_user' ";
						    $sql_delete_corregidos_temp.=" ; ";
						    $error_bd_seq="";		
						    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_delete_corregidos_temp, $error_bd_seq);
						    if($error_bd_seq!="")
						    {
							    $mensajes_error_bd.=" ERROR Al eliminar en la tabla temporal de registros corregidos pre correccion duplicados  para corrector ".procesar_mensaje($error_bd_seq).".<br>";
							    
						    }
						    */
						    //FIN BORRANDO INFORMACION DE LA TABLA TEMPORAL CON LOS DUPLICADOS DEL AFILIADO
					    }//fin if si el duplicado no se ha procesado
					    
					    //porcentaje
					    $muestra_mensaje_nuevo_dupl=false;
					    $porcentaje_dupl=intval((($numero_duplicados_procesados_hasta_el_momento)*100)/($acumulador_para_contar_duplicados));
					    if($porcentaje_dupl!=$cont_porcentaje_dupl || ($porcentaje_dupl==0 && ($numero_duplicados_procesados_hasta_el_momento)==1) || $porcentaje_dupl==100)
					    {
					     $cont_porcentaje_dupl=$porcentaje_dupl;
					     $muestra_mensaje_nuevo_dupl=true;
					    }
					    //fin porcentaje
					    
					    
					    //ACTUALIZA ESTADO DEL ARCHIVO
					    $mensaje_estado_registros_temp_dupl="<span style=color:red>Por favor espere, se han arreglado $numero_duplicados_procesados_hasta_el_momento duplicados para un total de $acumulador_para_contar_duplicados duplicados. $porcentaje_dupl %.</span><br>";
					    
					    $mensaje_perm_estado_reg_dupl=$mensaje_estado_registros_temp_dupl;
						
					    $msg_a_bd="";
					    $msg_a_bd=$mensaje_perm_estado." ".$mensaje_perm_estado_reg_dupl;
					    
					    if($muestra_mensaje_nuevo_dupl)
					    {
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_consolidando_ro_actualmente ";
						$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$msg_a_bd' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
						$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";		    
						$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
						$query_update_esta_siendo_procesado.=" AND nick_usuario='".$nick_user."'  ";
						$query_update_esta_siendo_procesado.=" AND fecha_validacion='".$fecha_actual."'  ";
						$query_update_esta_siendo_procesado.=" AND hora_validacion='".$tiempo_actual."'  ";
						$query_update_esta_siendo_procesado.=" ; ";
						$error_bd="";
						$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_esta_siendo_procesado, $error_bd);
						if($error_bd!="")
						{
							if($fue_cerrada_la_gui2==false)
							{
								echo "<script>alert('error al actualizar el estado actual de reparacion en tiempo real  4505 ');</script>";
							}
						}
					    }//fin if
					    //FIN ACTUALIZA ESTADO DEL ARCHIVO
					    
					    if($fue_cerrada_la_gui2==false && $muestra_mensaje_nuevo_dupl)
					    {
						echo "<script>document.getElementById('mensaje_div').innerHTML='$mensaje_perm_estado $mensaje_perm_estado_reg_dupl';</script>";
						//echo "<script>document.getElementById('tabla_estado_1').style.left='25%';</script>";
						ob_flush();
						flush();
					    }
					}//fin foreach trae bloques de personas
				}//fin if hay resultados
				
				
				
				//incremento contador
				$contador_offset_personas+=$numero_registros_bloque_personas;
			    }//fin while
			}//fin if si hay archivos duplicados
			
			//BORRANDO VISTAS
			$sql_borrar_vista_duplicados_en_uno_solo="";
			$sql_borrar_vista_duplicados_en_uno_solo.=" DROP VIEW $nombre_vista_index_duplicados ; ";							
			$error_bd="";		
			$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vista_duplicados_en_uno_solo, $error_bd);		
			if($error_bd!="")
			{
			    if(connection_aborted()==false)
			    {
				echo "<script>alert('error al borrar la vista duplicados en uno solo');</script>";
			    }
				$mensajes_error_bd.=" ERROR Al al borrar la vista duplicados en uno solo: ".$error_bd."<br>";
			}
			//FIN BORRANDO VISTAS
			
		}//fin if si se genera reporte para un archivo validado
		//FIN ARREGLO DE DUPLICADOS EN UNO SOLO
		
		//PARTE REGISTRO EN LA TABLA DE REPORTADO
		if($mensajes_error_bd=="" && $numero_registros>0)
		{
			
			$sql_insert_verificador_reportados="";
			$sql_insert_verificador_reportados.=" INSERT INTO gioss_archivos_obligatorios_reportados_pyp ";
			$sql_insert_verificador_reportados.=" ( ";
			$sql_insert_verificador_reportados.=" usuario_que_genero, ";
			$sql_insert_verificador_reportados.=" nombre_archivo_pyp, ";
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
			$sql_insert_verificador_reportados.="'".$nombre_archivo_para_zip."',";
			$sql_insert_verificador_reportados.="'".$fecha_actual."',";
			$sql_insert_verificador_reportados.="'".$tiempo_actual."',";
			$sql_insert_verificador_reportados.="'".$fecha_corte_bd."',";
			$sql_insert_verificador_reportados.="'".$regimen_almacenado."',";
			$sql_insert_verificador_reportados.="'".$cod_eapb."',";
			$sql_insert_verificador_reportados.="'1',";
			$sql_insert_verificador_reportados.="'".$numero_registros."'";
			$sql_insert_verificador_reportados.=" ) ";
			$sql_insert_verificador_reportados.=" ; ";
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insert_verificador_reportados, $error_bd_seq);
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.="ERROR EN la tabla resumen archivos obligatorios reportados: ".$error_bd_seq."<br>";
			}
		}
		//FIN PARTE REGISTRO EN LA TABLA DE REPORTADO
		
		
		
		//PARTE ESCRIBE CSV		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion." ";
		$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_pyp4505_exitoso ";
		$sql_vista_consulta_reporte_obligatorio.=" WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_pyp='$nombre_archivo_para_zip' ";
		$sql_vista_consulta_reporte_obligatorio.=" AND fecha_de_generacion='$fecha_actual'  AND  hora_generacion='$tiempo_actual'";
		$sql_vista_consulta_reporte_obligatorio.=" ORDER BY numero_secuencia asc, numero_registro asc ; ";		
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=$error_bd_seq."<br>";
		}
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion.";  ";		
		$error_bd_seq="";
		$resultado_query_numero_registros=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_numero_registros,$error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR AL CONSULTAR numero registros de vista_consulta: ".$error_bd_seq."<br>";
		}
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		if($numero_registros==0)
		{
			$mensajes_error_bd.="No hay registros a consultar. <br> ";
		}
		
		//echo "<script>alert('".$numero_registros."');</script>";
		
		$cont_linea=1;
		$contador_offset=0;
		$flag_creacion_archivo=false;
		$limite=0;
		$string_vacia="                ";
		$flag_para_salto_linea_inicial=false;
		
		$cont_resultados=1;
		
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$error_bd_seq="";
			$resultado_query_reporte_obligatoria=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);			
			if($error_bd_seq!="")
			{
				$mensajes_error_bd.="ERROR AL CONSULTAR de vista_consulta: ".$error_bd_seq."<br>";
			}
			
			if(count($resultado_query_reporte_obligatoria)>0)
			{
				
				
				//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				if($flag_creacion_archivo==false)
				{
					$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
					fclose($reporte_obligatorio_file);
					
					//ESCRIBE PRIMERA LINEA DE 4505
					$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
											
					$primera_linea_4505="";
					$primera_linea_4505.="1|".$cod_eapb."|".$fecha_ini_bd."|".$fecha_fin_bd."|".$string_vacia;
					fwrite($reporte_obligatorio_file, $primera_linea_4505."\n");		
					fclose($reporte_obligatorio_file);
					
					//FIN ESCRIBE PRIMERA LINEA DE 4505
				}
				
				$flag_creacion_archivo=true;
				//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				
				$fue_cerrada_la_gui_3=false;
				foreach($resultado_query_reporte_obligatoria as $resultado)
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
					$verificar_si_ejecucion_fue_cancelada.=" SELECT esta_ejecutando FROM gioss_4505_esta_consolidando_ro_actualmente ";
					$verificar_si_ejecucion_fue_cancelada.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND cod_eapb='".$cod_eapb."' ";
					$verificar_si_ejecucion_fue_cancelada.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
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
					
					//array que almacena cada registro
					$array_campos4505_registro_perfecto=array();
					
					$cadena_escribir_linea="";
					$cont_orden_campo_pyp=0;
					while($cont_orden_campo_pyp<=118)
					{
						if($cadena_escribir_linea!="")
						{
							$cadena_escribir_linea.="|";
						}
						if($cont_orden_campo_pyp!=1)
						{
							$cadena_escribir_linea.=alphanumericAndSpace($resultado["campo_pyp4505_con_numero_orden_".$cont_orden_campo_pyp]);
							//campos diferentes del consecutivo
							$array_campos4505_registro_perfecto[$cont_orden_campo_pyp]=alphanumericAndSpace($resultado["campo_pyp4505_con_numero_orden_".$cont_orden_campo_pyp]);
						}
						else
						{
							$cadena_escribir_linea.=$cont_resultados;
							//para el consecutivo
							$array_campos4505_registro_perfecto[$cont_orden_campo_pyp]="".$cont_resultados;
						}
						$cont_orden_campo_pyp++;
					}
					if($flag_para_salto_linea_inicial==false)
					{
						fwrite($reporte_obligatorio_file, $cadena_escribir_linea);
						$flag_para_salto_linea_inicial=true;
					}
					else
					{
						fwrite($reporte_obligatorio_file, "\n".$cadena_escribir_linea);
					}
					
					//PARTE SUBE TABLAS RIESGOS
					$mensaje_subida_pob_riesgo="";
					$numero_secuencia_para_subida_riesgo="".$resultado["numero_secuencia"];
					$regimen_para_subida_riesgo="".trim($resultado["regimen"]);
					subir_a_tablas_poblacion_riesgo($array_campos4505_registro_perfecto,
									$nombre_archivo_para_zip,
									$cod_eapb,
									$fecha_corte_bd,
									$fecha_actual,
									$tiempo_actual,
									$nick_user,
									$identificacion,
									$tipo_id,
									$numero_secuencia_para_subida_riesgo,
									$regimen_para_subida_riesgo,
									$mensaje_subida_pob_riesgo,
									$coneccionBD
									);
					//FIN PARTE SUBE A TABLAS RIESGOS
					
					//porcentaje
					$muestra_mensaje_nuevo_csv=false;
					$porcentaje_csv=intval((($cont_linea)*100)/($numero_registros));
					if($porcentaje_csv!=$cont_porcentaje_csv || ($porcentaje_csv==0 && ($cont_linea)==1) || $porcentaje_csv==100)
					{
					 $cont_porcentaje_csv=$porcentaje_csv;
					 $muestra_mensaje_nuevo_csv=true;
					}
					//fin porcentaje
					
					if($fue_cerrada_la_gui_3==false && $muestra_mensaje_nuevo_csv)
					{
						$mensaje_perm_estado_reg_recuperados="";
						$mensaje_perm_estado_reg_recuperados.="Por favor espere, $cont_linea registros recuperados de $numero_registros. $porcentaje_csv %<br>";
						$mensaje_perm_estado_reg_recuperados.=" Y subiendo informaci&oacuten del registro en las tablas de poblacion riesgo<br>";
						$mensaje_perm_estado_reg_recuperados.=$mensaje_subida_pob_riesgo;
						echo "<script>document.getElementById('mensaje_div').innerHTML='$mensaje_perm_estado $mensaje_perm_estado_reg_dupl $mensaje_perm_estado_reg_recuperados ';</script>";
						//echo "<script>document.getElementById('tabla_estado_1').style.left='25%';</script>";
						ob_flush();
						flush();						
					
					
						//ACTUALIZA MENSAJE ESTADO EJECUCION
						$mensaje_para_estado_ejecucion="$mensaje_perm_estado $mensaje_perm_estado_reg_dupl $mensaje_perm_estado_reg_recuperados ";
						
						$query_update_esta_siendo_procesado="";
						$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_consolidando_ro_actualmente ";
						$query_update_esta_siendo_procesado.=" SET mensaje_estado_registros='$mensaje_para_estado_ejecucion' ";
						$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
						$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
						$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
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
								echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
							}
						}
						//FIN ACTUALIZA MENSAJE ESTADO EJECUCION
					}//fin if
					
					$cont_resultados++;
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hayo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		
		//RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS
		$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "c") or die("fallo la creacion del archivo");
					
		$string_cont_linea="".($cont_linea-1);
		$string_vacia="                ";
		
		while(strlen($string_cont_linea)<strlen($string_vacia))
		{
			$string_cont_linea.=" ";					
		}
		
		$primera_linea_4505="";
		$primera_linea_4505.="1|".$cod_eapb."|".$fecha_ini_bd."|".$fecha_fin_bd."|".$string_cont_linea;
		fwrite($reporte_obligatorio_file, $primera_linea_4505."\n");		
		fclose($reporte_obligatorio_file);
		
		//FIN RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS
		
		//FIN PARTE ESCRIBE CSV
		
		//FIN GENERANDO PYP
		
			
		//borrando vistas
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $NOMBRE_VISTA_PYP_CONSOLIDADO ; ";
		$sql_borrar_vistas.=" DROP VIEW vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		
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
		
		
		
		
		if(count($array_rutas_archivos_generados)>0 && $numero_registros>0)
		{
			//GENERANDO ARCHIVO ZIP
					
			$ruta_zip=$rutaTemporal.$nombre_archivo_para_zip.'.zip';		
			
			$ruta_dat=$rutaTemporal.$nombre_archivo_para_zip.".DAT";
			
			
			$result_zip = create_zip($array_rutas_archivos_generados,$ruta_zip);
			$result_dat = create_zip($array_rutas_archivos_generados,$ruta_dat);
			$mensaje.=" $mensaje_perm_estado $mensaje_perm_estado_reg_dupl $mensaje_perm_estado_reg_recuperados Se genero el consolidado de los archivos 4505 de forma comprimida.";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('grilla').style.display='inline';</script>";
				echo "<script>var ruta_zip= '$ruta_zip'; </script>";
				echo "<script>var ruta_dat= '$ruta_dat'; </script>";
			}
			$resultadoDefinitivo.="<input type='button' value='Descargar Reporte Obligatorio .zip' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_zip);' />  ";
			$resultadoDefinitivo.="<input type='button' value='Descargar Reporte Obligatorio .DAT' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_dat);' />  ";
			
			
			//FIN GENERANDO ARCHIVO ZIP
			
			
			
			if($mensajes_error_bd!="")
			{
				$mensajes_error_bd="<br>".procesar_mensaje($mensajes_error_bd);
			}
			
			if(connection_aborted()==false)
			{
				//echo "<script>document.getElementById('mensaje_div').style.textAlign='center';</script>";
				//echo "<script>document.getElementById('resultado_definitivo').style.textAlign='center';</script>";
				echo "<script>document.getElementById('mensaje_div').innerHTML='$mensaje $mensajes_error_bd'</script>";
				echo "<script>document.getElementById('resultado_definitivo').innerHTML=\"$resultadoDefinitivo\"</script>";
				//echo "<script>document.getElementById('tabla_estado_1').style.position='relative';</script>";
				//echo "<script>document.getElementById('tabla_estado_1').style.left='25%';</script>";
			}
			
			//SUBE A GIOSS_LOG_DUPL PARA REPORTES FUTUROS
		    
			//borra el anterior
			$query_delete_log_dupl_anterior="";
			$query_delete_log_dupl_anterior.=" DELETE FROM ";
			$query_delete_log_dupl_anterior.=" gioss_log_dupl ";				
			$query_delete_log_dupl_anterior.=" WHERE ";
			$query_delete_log_dupl_anterior.=" tipo_id_usuario='".$tipo_id."'  ";
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" id_usuario='".$identificacion."' ";
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" nick_usuario='".$nick_user."' ";    
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" fecha_corte_reporte='".$fecha_corte_bd."' ";    
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" codigo_entidad_eapb_generadora='".$cod_eapb."' ";
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" tipo_reporte='PYP' ";
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" reparacion_o_consolidado='CONSOLIDADO' ";
			if($tipo_tiempo_periodo=="trimestral")
			{
			    $query_delete_log_dupl_anterior.=" AND ";
			    $query_delete_log_dupl_anterior.=" agrupado_o_prestador='TRIMESTRAL' ";
			}
			else if($tipo_tiempo_periodo=="mensual")
			{
			    $query_delete_log_dupl_anterior.=" AND ";
			    $query_delete_log_dupl_anterior.=" agrupado_o_prestador='MENSUAL' ";
			}
			$query_delete_log_dupl_anterior.=" AND ";
			$query_delete_log_dupl_anterior.=" nombre_archivo='".$nombre_archivo_para_zip."' ";
			$query_delete_log_dupl_anterior.=" ; ";
			$error_bd_seq="";		
			$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_delete_log_dupl_anterior, $error_bd_seq);
			if($error_bd_seq!="")
			{
				echo "<script>alert('ERROR Al borrar de tabla gioss_log_dupl ".$this->procesar_mensaje($error_bd_seq)." ');</script>";
				
			}
			//fin borra el anterior
			
			$cont_lineas_log_dupl = 0;
			$lectura_archivo_log_dupl = fopen($ruta_cambios_duplicados_campos, "r");
			while(!feof($lectura_archivo_log_dupl))
			{
			    $linea_log_dupl = fgets($lectura_archivo_log_dupl);
			    
			    //se separa en un array por medio del caracter || como separador
			    //no se usa | ya que separaria campos de mas y los campos del registro
			    $separadores_linea_log_dupl=explode("||",$linea_log_dupl);
			    
			    if(count($separadores_linea_log_dupl)>=12)
			    {
				$nombre_archivo_log_dupl=$separadores_linea_log_dupl[0];
				$fecha_actual_log_dupl=$separadores_linea_log_dupl[1];
				$hora_actual_log_dupl=$separadores_linea_log_dupl[2];
				$ident_dupl_unico_final=$separadores_linea_log_dupl[3];
				$fecha_corte_log_dupl=$separadores_linea_log_dupl[4];
				$tipo_reporte_log_dupl=$separadores_linea_log_dupl[5];
				$eapb_log_dupl=$separadores_linea_log_dupl[6];			
				$prestador_log_dupl=$separadores_linea_log_dupl[7];
				$reparacion_o_consolidado_log_dupl=$separadores_linea_log_dupl[8];
				
				$agrupado_o_prestador=$separadores_linea_log_dupl[count($separadores_linea_log_dupl)-3];			
				$nlinea_correspondiente_en_log=$separadores_linea_log_dupl[count($separadores_linea_log_dupl)-2];			
				$registro_con_campos = $separadores_linea_log_dupl[count($separadores_linea_log_dupl)-1];
			    
			    
				$query_subir_registro_corregido="";
				$query_subir_registro_corregido.=" INSERT INTO ";
				$query_subir_registro_corregido.=" gioss_log_dupl ";				
				$query_subir_registro_corregido.=" ( ";
				$query_subir_registro_corregido.=" tipo_id_usuario, ";
				$query_subir_registro_corregido.=" id_usuario, ";
				$query_subir_registro_corregido.=" nick_usuario, ";
				$query_subir_registro_corregido.=" numero_registro, ";
				$query_subir_registro_corregido.=" fecha_corte_reporte, ";
				$query_subir_registro_corregido.=" fecha_de_generacion, ";
				$query_subir_registro_corregido.=" hora_generacion, ";
				$query_subir_registro_corregido.=" codigo_entidad_eapb_generadora, ";			
				$query_subir_registro_corregido.=" codigo_entidad_prestadora, ";
				$query_subir_registro_corregido.=" identificador_dupl, ";
				$query_subir_registro_corregido.=" tipo_reporte, ";
				$query_subir_registro_corregido.=" reparacion_o_consolidado, ";			
				$query_subir_registro_corregido.=" agrupado_o_prestador, ";
				$query_subir_registro_corregido.=" registro_con_campos, ";
				$query_subir_registro_corregido.=" nombre_archivo ";
				$query_subir_registro_corregido.=" ) ";
				$query_subir_registro_corregido.=" VALUES ";
				$query_subir_registro_corregido.=" ( ";
				$query_subir_registro_corregido.="'".$tipo_id."',";
				$query_subir_registro_corregido.="'".$identificacion."',";
				$query_subir_registro_corregido.="'".$nick_user."',";	
				$query_subir_registro_corregido.="'".$nlinea_correspondiente_en_log."',";							
				$query_subir_registro_corregido.="'".$fecha_corte_log_dupl."',";
				$query_subir_registro_corregido.="'".$fecha_actual_log_dupl."',";
				$query_subir_registro_corregido.="'".$hora_actual_log_dupl."',";
				$query_subir_registro_corregido.="'".$eapb_log_dupl."',";			
				$query_subir_registro_corregido.="'".$prestador_log_dupl."',";
				$query_subir_registro_corregido.="'".$ident_dupl_unico_final."',";
				$query_subir_registro_corregido.="'".$tipo_reporte_log_dupl."',";
				$query_subir_registro_corregido.="'".$reparacion_o_consolidado_log_dupl."',";
				$query_subir_registro_corregido.="'".$agrupado_o_prestador."',";
				$query_subir_registro_corregido.="'".$registro_con_campos."',";
				$query_subir_registro_corregido.="'".$nombre_archivo_log_dupl."'";
				$query_subir_registro_corregido.=" ) ";
				$query_subir_registro_corregido.=" ; ";
				$error_bd_seq="";		
				$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_subir_registro_corregido, $error_bd_seq);
				if($error_bd_seq!="")
				{
					echo "<script>alert('ERROR Al subir en la tabla gioss_log_dupl ".$this->procesar_mensaje($error_bd_seq)." ');</script>";
					
				}
			    }//fin if longitud es correcta
			    
			    $cont_lineas_log_dupl++;
			}		    
			fclose($lectura_archivo_log_dupl);		    
			
			//FIN SUBE A GIOSS_LOG_DUPL PARA REPORTES FUTUROS
			
			//YA NO ESTA EN USO EL ARCHIVO	    
		    
		    
			$query_update_esta_siendo_procesado="";
			$query_update_esta_siendo_procesado.=" UPDATE gioss_4505_esta_consolidando_ro_actualmente ";
			$query_update_esta_siendo_procesado.=" SET esta_ejecutando='NO',";
			$query_update_esta_siendo_procesado.=" ruta_archivo_descarga='$ruta_zip' ";
			if($ruta_dat!="")
			{
			    //lleva la coma aca por si no esta vacio
			    $query_update_esta_siendo_procesado.=" , ruta_archivo_descarga_dat='$ruta_dat' ";
			}
			$query_update_esta_siendo_procesado.=" WHERE fecha_corte_periodo_consolidar='".$fecha_corte_bd."' ";
			$query_update_esta_siendo_procesado.=" AND cod_eapb='".$cod_eapb."' ";
			$query_update_esta_siendo_procesado.=" AND nombre_archivo='".$nombre_archivo_para_zip."'  ";
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
					echo "<script>alert('error al actualizar el estado actual de validacion en tiempo real  4505 ');</script>";
				}
			}
			//FIN YA NO ESTA EN USO EL ARCHIVO

			//limpieza tabla indexador al final
			$query_insert_updt_en_indexador="";
		    $query_insert_updt_en_indexador.=" DELETE FROM  ";
		    $query_insert_updt_en_indexador.=" gioss_indexador_duplicados_del_consolidado_4505 ";
		    $query_insert_updt_en_indexador.=" WHERE  ";
		    $query_insert_updt_en_indexador.="tipo_id_usuario='".$tipo_id."'";				
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="id_usuario='".$identificacion."'";
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="nick_usuario='".$nick_user."'";
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="fecha_corte_reporte='".$fecha_corte_bd."'";
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="fecha_de_generacion='".$fecha_actual."'";
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="hora_generacion='".$tiempo_actual."'";
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="codigo_entidad_eapb_generadora='".$cod_eapb."'";
		    $query_insert_updt_en_indexador.=" AND ";
		    $query_insert_updt_en_indexador.="nombre_archivo_pyp='".$nombre_archivo_para_zip."'";
		    $query_insert_updt_en_indexador.=" ; ";
		    $error_bd_seq="";		
		    $bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_updt_en_indexador, $error_bd_seq);
		    if($error_bd_seq!="")
		    {
				$mensajes_error_bd.=" ERROR Al limpiar la tabla gioss_indexador_duplicados_del_consolidado_4505 ".procesar_mensaje($error_bd_seq).".<br>";
				
				if($fue_cerrada_la_gui_3==false)
				{
					echo "<script>alert('ERROR Al limpiar la tabla gioss_indexador_duplicados_del_consolidado_4505  ".procesar_mensaje($error_bd_seq)."');</script>";
				}
		    }//fin if
		    //fin if limpieza tabla indexador al final
			
			// INICIO ENVIO DE MAIL
			
			$size_adjunto_ruta_zip=filesize($ruta_zip);			
			$factor_ruta_zip = floor((strlen($size_adjunto_ruta_zip) - 1) / 3);
			$size_ruta_zip_readable_KB=round(($size_adjunto_ruta_zip / pow(1024, $factor_ruta_zip)),2,PHP_ROUND_HALF_UP);
			$size_ruta_zip_readable_MB=round(($size_ruta_zip_readable_KB / 1024),2,PHP_ROUND_HALF_UP);
			$size_adjunto_ruta_dat=filesize($ruta_dat);
			
			//echo "<script>alert('size ruta zip $size_ruta_zip_readable_KB KB $size_ruta_zip_readable_MB MB , size ruta dat $size_adjunto_ruta_dat ');</script>";
			
			if($size_ruta_zip_readable_MB<25)
			{
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
				$mail->Subject = "Reporte Obligatorio PYP 4505 ";
				$mail->AltBody = "Cordial saludo,\n El sistema ha generado el reporte obligatorio para el periodo escogido.";
		    
				$mail->MsgHTML("Cordial saludo,\n El sistema ha generado el reporte obligatorio de 4505 .<strong>GIOSS</strong>.");
				$mail->AddAttachment($ruta_zip);
				if($size_ruta_zip_readable_MB<12)
				{
					$mail->AddAttachment($ruta_dat);	
				}//fin if si el archivo zip es menor de 12 MB inclye el archivo DAT
				$mail->AddAddress($correo_electronico, "Destinatario");
		    
				$mail->IsHTML(true);
		    
				if (!$mail->Send()) 
				{
					//echo "Error: " . $mail->ErrorInfo;
				}
				else 
				{
					// echo "Mensaje enviado.";
					if(connection_aborted()==false)
					{
						if($size_ruta_zip_readable_MB<12)
						{
							echo "<script>alert('Se ha enviado una copia del consolidado a su correo $correo_electronico , con un archivo adjunto .ZIP y .DAT de $size_ruta_zip_readable_MB MB cada uno. ')</script>";
						}//fin if
						else
						{
							echo "<script>alert('Se ha enviado una copia del consolidado a su correo $correo_electronico , con un archivo adjunto .ZIP de $size_ruta_zip_readable_MB MB , descargue el archivo .DAT de la interfaz ya que solo se pudo colocar uno por el limite de espacio en servidores de email ')</script>";
						}//fin else
						
					}//fin if
				}//fin else
		    
				//fin envio de mail
			}//si posee el size permitido
			else
			{
				if(connection_aborted()==false)
				{
					echo "<script>alert('El archivo es demasiado grande( $size_ruta_zip_readable_MB MB ) para ser enviado por correo, descarguelo desde la interfaz o el adminsitrador de tareas. ');</script>";
					echo "<h1 style='text-align:center'>El archivo es demasiado grande( $size_ruta_zip_readable_MB MB ) para ser enviado por correo, descarguelo desde la interfaz o el adminsitrador de tareas.<h1>";
				}//fin if
			}//fin else
		}//fin if
		else
		{
			//$mensaje.="<br>No se encontraron resultados.";
			if(connection_aborted()==false)
			{
				echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede generar el archivo, no existen datos asociados al a&ntildeo y periodo seleccionados.';</script>";
			}//fin if
		}//fin else
	
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
				echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Ya hay un archivo obligatorio para 4505 que esta siendo consolidado actualmente, por favor cancele la ejecucion del anterior o espere a que termine. ';</script>";
			}
		}
	}
	
	if($mensajes_error_bd!="")
	{
		$mensajes_error_bd="<br>".procesar_mensaje($mensajes_error_bd);
		
		echo "<script>alert('$mensajes_error_bd');</script>";
	}
	
}//fin if cuando se hizo submit


$coneccionBD->cerrar_conexion();
?>