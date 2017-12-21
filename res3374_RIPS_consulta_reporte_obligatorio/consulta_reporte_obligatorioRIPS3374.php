<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/crear_zip.php';

include ("../librerias_externas/PHPMailer/class.phpmailer.php");
include ("../librerias_externas/PHPMailer/class.smtp.php");

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

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$correo_electronico=$_SESSION['correo'];

session_write_close();

$mostrarResultado = "<div id='mostrar_resultado_div'></div>";
$mensaje="<div id='mensaje_div'></div>";


$selector_fechas_corte="";
$selector_fechas_corte.="<input type='hidden' id='fechas_corte' name='fechas_corte' >";


$query_periodos_rips="SELECT * FROM gioss_periodos_reporte_rips;";
$resultado_query_periodos=$coneccionBD->consultar2($query_periodos_rips);

$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='seleccionar_fecha_de_corte();'>";
$selector_periodo.="<option value='none'>Seleccione un periodo</option>";
foreach($resultado_query_periodos as $key=>$periodo_bd)
{
	$cod_periodo=$periodo_bd["cod_periodo"];
	$nombre_periodo=$periodo_bd["nombre_periodo"];
	$fecha_inicio=$periodo_bd["fecha_inicio_periodo"];
	$fecha_final=$periodo_bd["fecha_final_periodo"];
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




$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado",$mostrarResultado,true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_fechas_corte", $selector_fechas_corte, true);
$smarty->assign("campo_eapb", $eapb, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('consulta_reporte_obligatorioRIPS3374.html.tpl');

$numero_registros=0;
$contador_offset=0;
$flag_creacion_archivo=false;

$nombre_archivo_inconsistencias="";

$numero_registros_bloque=1000;

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$esta_validado_exitoso=false;
$existe_validacion=false;

if(isset($_POST["selector_estado_info"]) && isset($_POST["eapb"]) && isset($_POST["periodo"]) && isset($_POST["year_de_corte"])
   && $_POST["selector_estado_info"]!="none" && $_POST["eapb"]!="none"
   && (($_POST["periodo"]!="none" && $_POST["tipo_consulta_radio"]=="detallado")  || $_POST["tipo_consulta_radio"]=="consolidado")
   && $_POST["year_de_corte"]!=""
   && ctype_digit($_POST["year_de_corte"])
   )
{
	$cod_eapb=$_POST["eapb"];
	$estado_validacion_seleccionado=$_POST["selector_estado_info"];
	$periodo=$_POST["periodo"];
	
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('H:i:s');
	
	$year=$_POST["year_de_corte"];
	
	
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";
	
	
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
	
	if($periodo=="none")
	{
	   $fecha_ini_bd="01/01/".$year;
	   $fecha_fin_bd="12/31/".$year;
	   $fecha_de_corte_periodo="12/31/".$year;
	}
	
	$array_fecha_de_corte_simple=explode("/",$fecha_de_corte_periodo);
	$fecha_de_corte_simple=$array_fecha_de_corte_simple[2].$array_fecha_de_corte_simple[0].$array_fecha_de_corte_simple[1];
	
	
	$array_fibd=explode("/",$fecha_ini_bd);
	$fecha_ini_bd=$array_fibd[2]."-".$array_fibd[0]."-".$array_fibd[1];
	
	$array_ffbd=explode("/",$fecha_fin_bd);
	$fecha_fin_bd=$array_ffbd[2]."-".$array_ffbd[0]."-".$array_ffbd[1];
	
	$array_fcbd=explode("/",$fecha_de_corte_periodo);
	$fecha_corte_bd=$array_fcbd[2]."-".$array_fcbd[0]."-".$array_fcbd[1];
	
	$numero_remision_de_bd="";
	
	$string_periodo= "".$periodo;
	
	if(strlen($string_periodo)!=2)
	{
		$string_periodo="0".$string_periodo;
	}
	
	if($_POST["tipo_consulta_radio"]=="detallado")
	{
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
		
		//PARTE QUE VERIFICA LA EXISTENCIA DEL ARCHIVO REPORTADO
		
		//verificar que el nombre de archivo no haya sido reportado
		$consecutivo_de_bd="";
		$ya_se_reporto_archivo=false;
		$query_verificar_nombre_archivo="";
		$query_verificar_nombre_archivo.="SELECT * FROM gioss_archivos_obligatorios_reportados_rips WHERE codigo_entidad_eapb_generadora='$cod_eapb' ";
		$query_verificar_nombre_archivo.=" AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ";
		if($estado_validacion_seleccionado=="validada")
		{
			$query_verificar_nombre_archivo.=" AND estado_informacion='1' ;";
		}
		else if($estado_validacion_seleccionado=="rechazada")
		{
			$query_verificar_nombre_archivo.=" AND estado_informacion='2' ;";
		}
		$resultado_verificar_nombre_archivo=$coneccionBD->consultar2($query_verificar_nombre_archivo);
		if(count($resultado_verificar_nombre_archivo)>0)
		{
			$ya_se_reporto_archivo=true;
			$consecutivo_de_bd=$resultado_verificar_nombre_archivo[0]["consecutivo"];
			$nombre_ct=$resultado_verificar_nombre_archivo[0]["archivo_ct"];
			$numero_remision_de_bd=explode("T",$nombre_ct)[1];
		}
		//fin verificar
			
		if($ya_se_reporto_archivo)
		{
			$existe_validacion=true;
		}
		else
		{
			$existe_validacion=false;
			//echo "<script>document.getElementById('mensaje_div').innerHTML='No se hallaron archivos 3374 reportados.';</script>";
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se encontrar&oacuten reportes obligatorios asociados.';</script>";
		}
			
		//FIN PARTE QUE VERIFICA LA EXISTENCIA  DEL ARCHIVO REPORTADO
	}//solo si se escogio consulta detallado
	
	$valor_radio="";
	if(isset($_POST["tipo_consulta_radio"]))
	{
		$valor_radio=$_POST["tipo_consulta_radio"];
	}
	
	//echo "<script>alert('$valor_radio');</script>";
	
	//IF SI EXISTE VALIDACION
	if($existe_validacion==true && $valor_radio=="detallado")
	{
		$array_rutas_archivos_generados=array();		
		
		$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
		
		$rutaTemporal = '../TEMPORALES/';
		//mkdir($rutaTemporal.$nombre_zip_sin_consecutivo.$tiempo_actual_string, 0700);
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
		
		//PARTE ESCRIBE CSV CT
		
		$ruta_escribir_archivo=$rutaTemporal."CT".$string_periodo.intval($year).".txt";
		$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
		
		//echo "<script>alert('$ruta_escribir_archivo')</script>";
		
		$numero_registros_bloque=1000;
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vconsror3374_ct_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ct_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
				if($flag_creacion_archivo==false)
				{
					$flag_creacion_archivo=true;
				}
				
				
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_us_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ac_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ah_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_ap_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_au_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_an_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_rips3374_am_exitoso WHERE codigo_entidad_eapb_generadora='$cod_eapb' AND nombre_archivo_rips='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado=="rechazada")
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
		if($estado_validacion_seleccionado=="validada")
		{
			$consecutivo=trim($consecutivo_de_bd);
		}
		else if($estado_validacion_seleccionado=="rechazada")
		{
			$consecutivo=trim($consecutivo_de_bd)."R";
		}
			
		//fin obteniendo consecutivo
		
		
		if($flag_creacion_archivo)
		{
			//CREAR ZIP en generacion se usa el consecutivo por eapb pero como aca es consulta se usa el tiempo del sistema
			
			$ruta_zip=$rutaTemporal.$nombre_zip_sin_consecutivo.$consecutivo.".zip";
			$result_zip = create_zip($array_rutas_archivos_generados,$ruta_zip);
			$ruta_dat=$rutaTemporal.$nombre_zip_sin_consecutivo.$consecutivo.".DAT";
			$result_dat = create_zip($array_rutas_archivos_generados,$ruta_dat);
			ob_flush();
			flush();
			//FIN CREAR ZIP
			echo "<script>document.getElementById('grilla').style.display='inline';</script>";
			$boton_descarga="";	
			$boton_descarga.=" <input type=\'button\' value=\'Descargar el reporte obligatorio almacenado para  rips 3374 .zip\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
			$boton_descarga.=" <input type=\'button\' value=\'Descargar el reporte obligatorio almacenado para  rips 3374 .DATS\'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_dat\');\"/> ";
			echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_descarga';</script>";
			ob_flush();
			flush();
			
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
			$mail->AltBody = "Cordial saludo,\n El sistema ha consultado el reporte obligatorio para el periodo escogido.";
	    
			$mail->MsgHTML("Cordial saludo,\n El sistema ha consultado el reporte obligatorio de 3374.<strong>GIOSS</strong>.");
			$mail->AddAttachment($ruta_zip);
			$mail->AddAddress($correo_electronico, "Destinatario");
	    
			$mail->IsHTML(true);
	    
			if (!$mail->Send()) 
				    {
			    //echo "Error: " . $mail->ErrorInfo;
			} else 
				    {
			    // echo "Mensaje enviado.";
					    echo "<script>alert('Se ha enviado una copia del comprimido del reporte obligatorio de 3374 a su correo $correo_electronico ')</script>";
			}
	    
			//fin envio de mail
			
		}
		else
		{
			//$mensaje.="<br>No se encontraron resultados.";
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se encontrar&oacuten reportes obligatorios asociados.';</script>";
			ob_flush();
			flush();
		}	
		
	}//FIN IF SI EXISTE VALIDACION
	
	//PARTE CONSOLIDADO
	if($valor_radio=="consolidado" && $estado_validacion_seleccionado=="validada")
	{
		//echo "<script>alert('$valor_radio validada');</script>";
		
		$bool_hay_datos_para_consolidado=false;
		$query_consolidado_rep_oblig_rips="";
		$query_consolidado_rep_oblig_rips.="SELECT * FROM gioss_archivos_obligatorios_reportados_rips WHERE codigo_entidad_eapb_generadora='$cod_eapb' ";
		$query_consolidado_rep_oblig_rips.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
		$query_consolidado_rep_oblig_rips.=" AND estado_informacion='1' ;";
		
		$resultado_consolidado_rep_oblig_rips=$coneccionBD->consultar2($query_consolidado_rep_oblig_rips);
		
		if(count($resultado_consolidado_rep_oblig_rips)>0)
		{
			$nombre_consolidado_rep_oblig_rips=$cod_eapb."_".$fecha_ini_bd."_".$fecha_fin_bd."_consulta_cons_rep_oblig_rips.csv";
			$ruta_consolidado_rep_oblig_rips=$rutaTemporal.$nombre_consolidado_rep_oblig_rips;
			
			
			$file_consolidado_rep_oblig= fopen($ruta_consolidado_rep_oblig_rips, "w") or die("fallo la creacion del archivo");
			fclose($file_consolidado_rep_oblig);
			
			
			$html_abrir_ventana="";
			$html_abrir_ventana.="<script>";
			$html_abrir_ventana.="ventana_detalle=window.open ('','ventana_consolidado_rep_oblig', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
			$html_abrir_ventana.="</script>";
			echo $html_abrir_ventana;
			
			$html_nueva_ventana="";
			$html_nueva_ventana.="<html>";
			
			$html_nueva_ventana.="<head>";
			$html_nueva_ventana.="<title>Consolidado Reporte Obligatorio</title>";
			$html_nueva_ventana.="<style>";
			$html_nueva_ventana.="table, th, td";
			$html_nueva_ventana.="{";
			$html_nueva_ventana.="    border: 1px solid black;";
			$html_nueva_ventana.="}";
			$html_nueva_ventana.="</style>";
			$html_nueva_ventana.="</head>";
			$html_nueva_ventana.="<body>";
			
			$html_nueva_ventana.="<table id='tabla_ventana_consolidado_rep_oblig' >";
			
			$file_consolidado_rep_oblig= fopen($ruta_consolidado_rep_oblig_rips, "a") or die("fallo la creacion del archivo");
			
			$titulos="";
			$titulos.="N. Orden,Cod. EAPB,Nombre EAPB,Nombre Archivo Reportado,Fecha Generacion,Periodo Reportado,";
			$titulos.="N. Reg CT,N. Reg US,N. Reg. AC,N. Reg. AP,";
			$titulos.="N. Reg. AH,N. Reg AU,N. Reg. AN,N. Reg. AM";
			fwrite($file_consolidado_rep_oblig, $titulos."\n");
			
			$html_nueva_ventana.="<tr>";
			$array_titulos=explode(",",$titulos);
			$cont_titulos=0;
			foreach($array_titulos as $titulo_columna)
			{
				
				$html_nueva_ventana.="<td>$titulo_columna</td>";
				
				$cont_titulos++;
			}
			$html_nueva_ventana.="</tr>";
			
			$cont_linea_consolidado_rep_oblig=0;
			foreach($resultado_consolidado_rep_oblig_rips as $resultado)
			{
				$linea_estado_informacion="";
				$linea_estado_informacion.=$cont_linea_consolidado_rep_oblig.",";
				$linea_estado_informacion.=$resultado["codigo_entidad_eapb_generadora"].",";
				//para nombre eapb
				$query_nombre_entidad="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$resultado["codigo_entidad_eapb_generadora"]."';";
				$resultado_para_nombre_eapb=$coneccionBD->consultar2($query_nombre_entidad);
				$linea_estado_informacion.=$resultado_para_nombre_eapb[0]["nombre_de_la_entidad"].",";
				//fin para nombre eapb
				$linea_estado_informacion.=$resultado["nombre_archivo_rips"].$resultado["consecutivo"].",";
				$linea_estado_informacion.=$resultado["fecha_de_generacion"].",";
				$linea_estado_informacion.=$resultado["fecha_corte_reporte"].",";
				//registros rep oblig ct
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_ct_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig us
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_us_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig ac
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_ac_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig ap
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_ap_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig ah
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_ah_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig au
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_au_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig an
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_an_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"].",";
				//registros rep oblig am
				$query_contador_archivo_rep_oblig="";
				$query_contador_archivo_rep_oblig.="SELECT count(*) as contador FROM ";
				$query_contador_archivo_rep_oblig.=" gioss_consulta_reporte_obligatorio_rips3374_am_exitoso ";
				$query_contador_archivo_rep_oblig.=" WHERE ";
				$query_contador_archivo_rep_oblig.=" codigo_entidad_eapb_generadora='$cod_eapb' ";
				$query_contador_archivo_rep_oblig.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
				$query_contador_archivo_rep_oblig.=" ; ";
				$resultado_contador_archivo_rep_oblig=$coneccionBD->consultar2($query_contador_archivo_rep_oblig);
				$linea_estado_informacion.=$resultado_contador_archivo_rep_oblig[0]["contador"];
				
				fwrite($file_consolidado_rep_oblig, $linea_estado_informacion."\n");
				
				echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, ".($cont_linea_consolidado_rep_oblig+1)." registros recuperados .';</script>";
				
				$html_nueva_ventana.="<tr>";
				$array_linea_estado_informacion=explode(",",$linea_estado_informacion);
				$cont_columnas_estado_informacion=0;
				foreach($array_linea_estado_informacion as $columna_estado_informacion)
				{
					
					$html_nueva_ventana.="<td>$columna_estado_informacion</td>";
					
					$cont_columnas_estado_informacion++;
				}
				$html_nueva_ventana.="</tr>";
				
				$cont_linea_consolidado_rep_oblig++;
			}//fin foreach de lso resultados
			fclose($file_consolidado_rep_oblig);
			
			$html_nueva_ventana.="</table>";
			
			$html_nueva_ventana.="</body>";
			$html_nueva_ventana.="</html>";
		
			$insertar_html_nueva_ventana="";
			$insertar_html_nueva_ventana.="<script>ventana_detalle.document.write(\"$html_nueva_ventana\");</script>";
			echo $insertar_html_nueva_ventana;
			
			echo "<script>document.getElementById('grilla').style.display='inline';</script>";
		
			$html_reabrir_ventana="";
			$html_reabrir_ventana.="<script>";
			$html_reabrir_ventana.="function re_abrir_nueva_ventana()";
			$html_reabrir_ventana.="{";
			$html_reabrir_ventana.="ventana_detalle=window.open ('','ventana_consolidado_rep_oblig', config='height=600,width=1280, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, directories=no, status=no');";
			$html_reabrir_ventana.="ventana_detalle.document.write(\"$html_nueva_ventana\");";	
			$html_reabrir_ventana.="}";
			$html_reabrir_ventana.="</script>";
			echo $html_reabrir_ventana;
			
			$boton_ventana=" <input type=\'button\' value=\'Ver el consolidado de reporte obligatorio para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
			
			$boton_descarga=" <input type=\'button\' value=\'Descargar el consolidado de reporte obligatorio para RIPS\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_consolidado_rep_oblig_rips\');\"/> ";
		
			echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
			
		}//hay datos
		else
		{
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se encontraron datos para formar el consolidado de reportes obligatorios para los criterios de busqueda diligenciados.';</script>";
			ob_flush();
			flush();
		}
		
	}
	if($valor_radio=="consolidado" && $estado_validacion_seleccionado=="rechazada")
	{
		//echo "<script>alert('$valor_radio rechazada');</script>";
		
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Solo se pueden consultar consolidados de los archivos reportados validados con exito.';</script>";
		ob_flush();
		flush();
	}
	//FIN PARTE CONSOLIDADO
}//fin if se selecciono
//FIN PARTE BUSQUEDA


?>