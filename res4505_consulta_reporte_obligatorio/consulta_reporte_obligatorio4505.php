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
require_once ("../librerias_externas/PHPExcel/PHPExcel.php");
require_once ("../librerias_externas/PHPExcel/PHPExcel/Writer/Excel2007.php");

require_once '../utiles/configuracion_global_email.php';

$smarty = new Smarty;
$coneccionBD = new conexion();

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

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$correo_electronico=$_SESSION['correo'];

session_write_close();

$mostrarResultado = "<div id='mostrar_resultado_div'></div>";
$mensaje="<div id='mensaje_div'></div>";


$selector_fechas_corte="";
$selector_fechas_corte.="<input type='hidden' id='fechas_corte' name='fechas_corte' >";


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
$smarty->display('consulta_reporte_obligatorio4505.html.tpl');

$numero_registros=0;
$contador_offset=0;
$flag_creacion_archivo=false;


$numero_registros_bloque=1000;

//PARTE BUSQUEDA
$rutaTemporal = '../TEMPORALES/';

$esta_validado_exitoso=false;
$existe_validacion=false;

if(isset($_POST["selector_estado_info"]) && isset($_POST["eapb"]) && isset($_POST["periodo"]) && isset($_POST["year_de_corte"])
   && $_POST["selector_estado_info"]!="none" && $_POST["eapb"]!="none"
   && (($_POST["periodo"]!="none" && $_POST["tipo_consulta_radio"]=="detallado")  || $_POST["tipo_consulta_radio"]=="consolidado")
   && (($_POST["year_de_corte"]!="" && ctype_digit($_POST["year_de_corte"]) && $_POST["tipo_consulta_radio"]=="detallado")  || $_POST["tipo_consulta_radio"]=="consolidado")
  )
{
	
	$codigo_eapb=$_POST["eapb"];
	$estado_validacion_seleccionado=$_POST["selector_estado_info"];
	$periodo=$_POST["periodo"];
	
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('h:i:s');
	
	$year=$_POST["year_de_corte"];
	
	
	
	$fecha_ini_bd ="";
	$fecha_fin_bd ="";
	$fecha_de_corte_periodo="";
	
	
	//PERIODOS PYP
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
	//FIN PERIODOS PYP
	
	if($periodo=="none" && $year!="" && ctype_digit($year))
	{
	   $fecha_ini_bd="01/01/".$year;
	   $fecha_fin_bd="12/31/".$year;
	   $fecha_de_corte_periodo="12/31/".$year;
	}
	
	if($year=="")
	{
		$fecha_ini_bd="01/01/1900";
		$usando_fecha_actual_para_final_array=explode("-",$fecha_actual);
		$fecha_fin_bd=$usando_fecha_actual_para_final_array[1]."/".$usando_fecha_actual_para_final_array[2]."/".$usando_fecha_actual_para_final_array[0];
		$fecha_de_corte_periodo=$fecha_fin_bd;
	}
	
	$fecha_de_corte=$_POST['year_de_corte']."-".explode("/",$fecha_de_corte_periodo)[0]."-".explode("/",$fecha_de_corte_periodo)[1];
	
	$fecha_revisar = date('Y-m-d',strtotime($fecha_de_corte));
	
	$array_fecha_de_corte_simple=explode("/",$fecha_de_corte_periodo);
	$fecha_de_corte_simple=$array_fecha_de_corte_simple[2].$array_fecha_de_corte_simple[0].$array_fecha_de_corte_simple[1];
	
	
	$array_fibd=explode("/",$fecha_ini_bd);
	$fecha_ini_bd=$array_fibd[2]."-".$array_fibd[0]."-".$array_fibd[1];
	
	$array_ffbd=explode("/",$fecha_fin_bd);
	$fecha_fin_bd=$array_ffbd[2]."-".$array_ffbd[0]."-".$array_ffbd[1];
	
	$array_fcbd=explode("/",$fecha_de_corte_periodo);
	$fecha_corte_bd=$array_fcbd[2]."-".$array_fcbd[0]."-".$array_fcbd[1];
	
	
	$string_periodo= "".$periodo;
	
	if(strlen($string_periodo)!=2)
	{
		$string_periodo="0".$string_periodo;
	}
	
	if($_POST["tipo_consulta_radio"]=="detallado")
	{
		//queries para estructura nombre zip
		$query_tipo_entidad="SELECT cod_tipo_ident_entidad_reportadora,cod_tipo_regimen_rips,nit,cod_tipo_regimen_4505 FROM gios_entidad_administradora WHERE cod_entidad_administradora='$codigo_eapb';";
		$resultado_query_tipo_entidad_reportadora=$coneccionBD->consultar2($query_tipo_entidad);
		
		$cod_tipo_ident_entidad_reportadora=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_ident_entidad_reportadora"];
		$cod_tipo_regimen_rips=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_regimen_rips"];
		$nit_entidad_reportadora=$resultado_query_tipo_entidad_reportadora[0]["nit"];
		$regimen4505=$resultado_query_tipo_entidad_reportadora[0]["cod_tipo_regimen_4505"];
		
		$string_nit_entidad_reportadora=$nit_entidad_reportadora;
		while(strlen($string_nit_entidad_reportadora)<12)
		{
			$string_nit_entidad_reportadora="0".$string_nit_entidad_reportadora;
		}
		
		$string_cod_eapb=$codigo_eapb;
		while(strlen($string_cod_eapb)<12)
		{
			$string_cod_eapb="0".$string_cod_eapb;
		}
		//fin queries  para estructura nombre zip
		
		$nombre_zip_sin_consecutivo="SGD280RPED".$array_fcbd[2].$array_fcbd[0].$array_fcbd[1]."NI".$string_nit_entidad_reportadora.$regimen4505."01";
		
		//PARTE QUE VERIFICA LA EXISTENCIA DEL ARCHIVO REPORTADO
		
		//verificar que el nombre de archivo no haya sido reportado
		$ya_se_reporto_archivo=false;
		$query_verificar_nombre_archivo="SELECT * FROM gioss_archivos_obligatorios_reportados_pyp WHERE codigo_entidad_eapb_generadora='$codigo_eapb' AND nombre_archivo_pyp='$nombre_zip_sin_consecutivo' ;";
		$resultado_verificar_nombre_archivo=$coneccionBD->consultar2($query_verificar_nombre_archivo);
		if(count($resultado_verificar_nombre_archivo)>0)
		{
			$ya_se_reporto_archivo=true;
		}
		//fin verificar
			
		if($ya_se_reporto_archivo)
		{
			$existe_validacion=true;
		}
		else
		{
			$existe_validacion=false;
			//echo "<script>document.getElementById('mensaje_div').innerHTML='No se hallaron archivos 4505 reportados.';</script>";
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
		
	//IF SI EXISTE VALIDACION
	if($existe_validacion==true && $valor_radio=="detallado")
	{
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
		
		/*
		if (DIRECTORY_SEPARATOR == '/')
		{
			// linux
			echo "<script>alert('es linux');</script>";
		}
		    
		if (DIRECTORY_SEPARATOR == '\\')
		{
			// windows
			echo "<script>alert('es windows');</script>";
		}
		
		if (strncasecmp(PHP_OS, 'WIN', 3) == 0)
		{
			echo "<script>alert('es windows');</script>";
		}
		else
		{
			echo "<script>alert('no es windows');</script>";
		}
		*/
		
		
		$sql_vista_consulta_reporte_obligatorio="";
		$sql_vista_consulta_reporte_obligatorio.="CREATE OR REPLACE VIEW vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion." ";
		if($estado_validacion_seleccionado="validada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_pyp4505_exitoso WHERE codigo_entidad_eapb_generadora='$codigo_eapb' AND nombre_archivo_pyp='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		else if($estado_validacion_seleccionado="rechazada")
		{
			$sql_vista_consulta_reporte_obligatorio.=" AS SELECT * FROM gioss_consulta_reporte_obligatorio_pyp4505_rechazado WHERE codigo_entidad_eapb_generadora='$codigo_eapb' AND nombre_archivo_pyp='$nombre_zip_sin_consecutivo' ORDER BY numero_secuencia asc, numero_registro asc ; ";
		}
		$error_bd_seq="";
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error($sql_vista_consulta_reporte_obligatorio, $error_bd_seq);
		
		$sql_numero_registros="";
		$sql_numero_registros.="SELECT count(*) as contador FROM vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion.";  ";
		$resultado_query_numero_registros=$coneccionBD->consultar2($sql_numero_registros);
		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		
		
		$cont_linea=1;		
		$string_vacia="                ";		
		$flag_para_salto_linea_inicial=false;
		while($contador_offset<$numero_registros)
		{
			$limite=$numero_registros_bloque;
			
			if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
			{
				$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
			}
		
			$sql_query_busqueda="";
			$sql_query_busqueda.="SELECT * FROM vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion." LIMIT $limite OFFSET $contador_offset;  ";
			$resultado_query_reporte_obligatoria=$coneccionBD->consultar2($sql_query_busqueda);
		
			if(count($resultado_query_reporte_obligatoria)>0)
			{
				$ruta_escribir_archivo=$rutaTemporal.$nombre_zip_sin_consecutivo.".txt";
				
				//PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				if($flag_creacion_archivo==false)
				{
					$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
					fclose($reporte_obligatorio_file);
					
					//ESCRIBE PRIMERA LINEA DE 4505
					$ruta_escribir_archivo=$rutaTemporal.$nombre_zip_sin_consecutivo.".txt";
					$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
											
					$primera_linea_4505="";
					$primera_linea_4505.="1|".$codigo_eapb."|".$fecha_ini_bd."|".$fecha_fin_bd."|"."                ";
					fwrite($reporte_obligatorio_file, $primera_linea_4505."\n");		
					fclose($reporte_obligatorio_file);
					
					//FIN ESCRIBE PRIMERA LINEA DE 4505
				}
				
				$flag_creacion_archivo=true;
				//FIN PARTE INDICAR HUBO RESULTADOS UNA SOLA VEZ Y CREAR EL ARCHIVO EN BLANCO
				
				
				
				$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							
				$cont_resultados=1;	
				foreach($resultado_query_reporte_obligatoria as $resultado)
				{
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
						}
						else
						{
							$cadena_escribir_linea.=$cont_resultados;
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
					
					echo "<script>document.getElementById('mensaje_div').innerHTML='Por favor espere, $cont_linea registros recuperados de $numero_registros.';</script>";
					ob_flush();
					flush();
					$cont_resultados++;
					$cont_linea++;
				}//fin foreach
				fclose($reporte_obligatorio_file);
				
				
				
			}//fin if hayo resultados
			
			$contador_offset+=$numero_registros_bloque;
		
		}//fin while
		
		//RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS
		$ruta_escribir_archivo=$rutaTemporal.$nombre_zip_sin_consecutivo.".txt";
		$reporte_obligatorio_file= fopen($ruta_escribir_archivo, "c") or die("fallo la creacion del archivo");
		
		$string_cont_linea="".($cont_linea-1);
		
		while(strlen($string_cont_linea)<strlen($string_vacia))
		{
			$string_cont_linea.=" ";					
		}
		$primera_linea_4505="";
		$primera_linea_4505.="1|".$codigo_eapb."|".$fecha_ini_bd."|".$fecha_fin_bd."|".$string_cont_linea;
		fwrite($reporte_obligatorio_file, $primera_linea_4505."\n");		
		fclose($reporte_obligatorio_file);
		
		//FIN RE-ESCRIBE PRIMERA LINEA DE 4505 PARA ADICIONAR EL NUMERO DE REGISTROS
		
		//borrando vistas
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW vcropyp4505_".$nick_user."_".$tipo_id."_".$identificacion." ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			echo "<script>alert('error al borrar vistas');</script>";
		}
		//fin borrando vistas
		
		if($flag_creacion_archivo)
		{
			//CREAR ZIP
			$archivos_a_comprimir=array();
			$archivos_a_comprimir[0]=$ruta_escribir_archivo;
			$ruta_zip=$rutaTemporal.$nombre_zip_sin_consecutivo.'.zip';
			$result_zip = create_zip($archivos_a_comprimir,$ruta_zip);
			ob_flush();
			flush();
			//FIN CREAR ZIP
			echo "<script>document.getElementById('grilla').style.display='inline';</script>";
				
			$boton_descarga=" <input type=\'button\' value=\'Descargar el reporte obligatorio almacenado para  pyp 4505. \'  class=\'btn btn-success color_boton\' onclick=\"download_inconsistencias_campos(\'$ruta_zip\');\"/> ";
		
			echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_descarga';</script>";
			ob_flush();
			flush();
			
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
			}//fin if usa ../utiles/configuracion_global_email.php
			$mail->From = "sistemagioss@gmail.com";
			$mail->FromName = "GIOSS";
			$mail->Subject = "Reporte Obligatorio PYP 4505 ";
			$mail->AltBody = "Cordial saludo,\n El sistema ha consultado el reporte obligatorio para el periodo escogido.";
	    
			$mail->MsgHTML("Cordial saludo,\n El sistema ha consultado el reporte obligatorio de 4505.<strong>GIOSS</strong>.");
			$mail->AddAttachment($ruta_zip);
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
			//echo "<script>document.getElementById('mensaje_div').innerHTML='No se hallaron reportes obligatorios archivos 4505 .';</script>";
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
		$query_consolidado_rep_oblig_pyp="";
		$query_consolidado_rep_oblig_pyp.="SELECT * FROM gioss_archivos_obligatorios_reportados_pyp WHERE codigo_entidad_eapb_generadora='$codigo_eapb' ";
		$query_consolidado_rep_oblig_pyp.=" AND (fecha_de_generacion BETWEEN '$fecha_ini_bd' AND '$fecha_fin_bd') ";
		$query_consolidado_rep_oblig_pyp.=" AND estado_informacion='1' ;";
		
		$resultado_consolidado_rep_oblig_pyp=$coneccionBD->consultar2($query_consolidado_rep_oblig_pyp);
		
		if(count($resultado_consolidado_rep_oblig_pyp)>0)
		{
			$nombre_consolidado_rep_oblig_pyp=$codigo_eapb."_".$fecha_ini_bd."_".$fecha_fin_bd."_consulta_cons_rep_oblig_pyp.csv";
			$ruta_consolidado_rep_oblig_pyp=$rutaTemporal.$nombre_consolidado_rep_oblig_pyp;
			
			$file_consolidado_rep_oblig= fopen($ruta_consolidado_rep_oblig_pyp, "w") or die("fallo la creacion del archivo");
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
			
			$html_nueva_ventana.="<table id='tabla_ventana_consolidado_rep_oblig' >";
			
			$file_consolidado_rep_oblig= fopen($ruta_consolidado_rep_oblig_pyp, "a") or die("fallo la creacion del archivo");
			
			$titulos="";
			$titulos.="N. Orden,Cod. EAPB,Nombre EAPB,";
			$titulos.="Nombre Archivo Reportado,Fecha Generacion,Fecha de corte del periodo,";
			$titulos.="N. Reg";
			fwrite($file_consolidado_rep_oblig, $titulos."\n");
			
			$html_nueva_ventana.="<tr>";
			$array_titulos=explode(",",$titulos);
			$cont_titulos=0;
			foreach($array_titulos as $titulo_columna)
			{
				
				$html_nueva_ventana.="<th>$titulo_columna</th>";
				
				$cont_titulos++;
			}
			$html_nueva_ventana.="</tr>";
			
			$cont_linea_consolidado_rep_oblig=0;
			foreach($resultado_consolidado_rep_oblig_pyp as $resultado)
			{
				$linea_estado_informacion="";
				$linea_estado_informacion.=$cont_linea_consolidado_rep_oblig.",";
				$linea_estado_informacion.=$resultado["codigo_entidad_eapb_generadora"].",";
				//para nombre eapb
				$query_nombre_entidad="SELECT nombre_de_la_entidad FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$resultado["codigo_entidad_eapb_generadora"]."';";
				$resultado_para_nombre_eapb=$coneccionBD->consultar2($query_nombre_entidad);
				$linea_estado_informacion.=$resultado_para_nombre_eapb[0]["nombre_de_la_entidad"].",";
				//fin para nombre eapb
				$linea_estado_informacion.=$resultado["nombre_archivo_pyp"].",";
				$linea_estado_informacion.=$resultado["fecha_de_generacion"].",";
				$linea_estado_informacion.=$resultado["fecha_corte_reporte"].",";
				$linea_estado_informacion.=$resultado["cantidad_registros_reportados"];
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
			}//fin foreach resultados
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
			
			$boton_ventana=" <input type=\'button\' value=\'Ver el consolidado de reporte obligatorio para PyP\'  class=\'btn btn-success color_boton\' onclick=\"re_abrir_nueva_ventana();\"/> ";	
			
			$boton_descarga=" <input type=\'button\' value=\'Descargar el consolidado de reporte obligatorio para PyP\'  class=\'btn btn-success color_boton\' onclick=\"download_archivo(\'$ruta_consolidado_rep_oblig_pyp\');\"/> ";
		
			echo "<script>document.getElementById('mostrar_resultado_div').innerHTML='$boton_ventana $boton_descarga';</script>";
			
		}//fin if si hay resultados para consolidados
		else
		{
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se encontraron datos para formar el consolidado de reportes obligatorios para los criterios de busqueda diligenciados.';</script>";
			ob_flush();
			flush();
		}
	}//fin if
	if($valor_radio=="consolidado" && $estado_validacion_seleccionado=="rechazada")
	{
		//echo "<script>alert('$valor_radio rechazada');</script>";
		
		echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
		echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='Solo se pueden consultar consolidados de los archivos reportados validados con exito.';</script>";
		ob_flush();
		flush();
	}
}//fin if se selecciono
//FIN PRTE BUSQUEDA


?>