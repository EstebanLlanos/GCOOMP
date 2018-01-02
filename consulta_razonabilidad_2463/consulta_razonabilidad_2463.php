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


require_once ("funcion_array_valores_permitidos_campo.php");

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();
session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

if(
	isset($_SESSION['tipo_perfil'])
	&& $_SESSION['tipo_perfil']!='5'
	)
{
	//header ("Location: ../index.php?no_tiene_permiso=true");
}//fin if


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

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];



$nick_user=$_SESSION['usuario'];

$correo_electronico=$_SESSION['correo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

session_write_close();

$mensaje="";
$mostrarResultado="none";


//consultar el tipo de entidad
$sql_query_tipo_entidad_asociada="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='$entidad_salud_usuario_actual'; ";
$resultado_query_tipo_entidad=$coneccionBD->consultar2_no_crea_cierra($sql_query_tipo_entidad_asociada);
$tipo_entidad=$resultado_query_tipo_entidad[0]["cod_tipo_entidad"];
//fin consultar el tipo de entidad

//archivos subidos

//fin archivos subidos

//SELECTOR PRESTADOR
$prestador="";
$prestador.="<select id='prestador' name='prestador' class='campo_azul' onchange=''>";
$prestador.="<option value='none'>...</option>";
$prestador.="</select>";
//FIN SELECTOR PRESTADOR

/*
$sql_prestadores_asociados_al_archivo="SELECT * FROM gioss_archivo_para_analisis_2463 
 WHERE nombre_archivo='' AND fecha_y_hora_validacion='' AND fecha_de_corte=''
 ;
 ";
$resultado_query_prestadores_asociados_al_archivo=$coneccionBD->consultar2_no_crea_cierra($sql_prestadores_asociados_al_archivo);
*/


$selector_periodo="";
$selector_periodo.="<select id='periodo' name='periodo' class='campo_azul' onchange='consultar_archivos_subidos_para_periodo_year();' style='width:230px;'>";
/*
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
*/
$selector_periodo.="<option value='none'>Seleccione un Periodo</option>";
$selector_periodo.="<option value='1'>Periodo 1</option>";
$selector_periodo.="<option value='2'>Periodo 2</option>";
$selector_periodo.="<option value='3'>Periodo 3</option>";
$selector_periodo.="<option value='4'>Periodo 4</option>";
$selector_periodo.="<option value='5'>Periodo 5</option>";
$selector_periodo.="<option value='6'>Periodo 6</option>";
$selector_periodo.="<option value='7'>Periodo 7</option>";
$selector_periodo.="<option value='8'>Periodo 8</option>";
$selector_periodo.="<option value='9'>Periodo 9</option>";
$selector_periodo.="<option value='10'>Periodo 10</option>";
$selector_periodo.="<option value='11'>Periodo 11</option>";
$selector_periodo.="<option value='12'>Periodo 12</option>";
$selector_periodo.="</select>";


$prestador.="</select>";
//FIN PRESTADOR-ASOCIADO-USUARIO

$sql_query_valores_permitidos="SELECT * FROM valores_permitidos_2463 order by numero_campo_norma::numeric asc; ";
$resultado_query_valores_permitidos=$coneccionBD->consultar2_no_crea_cierra($sql_query_valores_permitidos);

$opciones_selector_campos="";

if(is_array($resultado_query_valores_permitidos) && count($resultado_query_valores_permitidos)>0 )
{
	$cont_c1=0;
	while($cont_c1<count($resultado_query_valores_permitidos) )
	{
		$nombre_campo=trim($resultado_query_valores_permitidos[$cont_c1]['nombre_campo']);
		$numero_campo_norma=intval(trim($resultado_query_valores_permitidos[$cont_c1]['numero_campo_norma']) );
		$opciones_selector_campos.="<option value='$numero_campo_norma'>Campo Numero $numero_campo_norma $nombre_campo </option>";
		$cont_c1++;

	}//fin while
}//fin if
else
{
	$cont_c1=0;
	while($cont_c1<119)
	{
		$opciones_selector_campos.="<option value='$cont_c1'>Campo Numero $cont_c1 </option>";
		$cont_c1++;

	}//fin while
}//else

$selector_archivos_subidos="";

$selector_archivos_subidos.="<select id='selector_archivos_subidos' name='selector_archivos_subidos' class='campo_azul' onchange='consultar_ips_archivo(this.value)'>";
$selector_archivos_subidos.="<option value='none'>...</option>";

/*
$query_archivos_subidos_para_analisis="";
$query_archivos_subidos_para_analisis.=" SELECT * FROM gioss_indice_archivo_para_analisis_2463 ORDER BY fecha_y_hora_validacion asc ; ";
$resultado_query_archivos_subidos_para_analisis=$coneccionBD->consultar2_no_crea_cierra($query_archivos_subidos_para_analisis);
if(is_array($resultado_query_archivos_subidos_para_analisis) && count($resultado_query_archivos_subidos_para_analisis)>0 )
{
	foreach ($resultado_query_archivos_subidos_para_analisis as $key => $archivo_subido_actual) 
	{		
		$identificador_archivo=$archivo_subido_actual['nombre_archivo']."_".$archivo_subido_actual['fecha_y_hora_validacion']."_".$archivo_subido_actual['fecha_de_corte'];

		$numero_secuencia_si_lo_hay="";
		if(trim($archivo_subido_actual['fecha_de_corte'])!="")
		{
			$numero_secuencia_si_lo_hay=", y con numero de secuencia:".$archivo_subido_actual['numero_de_secuencia'];
		}
		
		$descripcion_archivo=$archivo_subido_actual['nombre_archivo']." , validado en el: ".$archivo_subido_actual['fecha_y_hora_validacion'].", con la fecha de corte: ".$archivo_subido_actual['fecha_de_corte'].$numero_secuencia_si_lo_hay;
		$selector_archivos_subidos.="<option value='$identificador_archivo'>$descripcion_archivo</option>";
	}//fin foreach
}//fin if
*/

$selector_archivos_subidos.="</select>";


$smarty->assign("campo_archivo_analizar", $selector_archivos_subidos, true);
$smarty->assign("campo_selector_campos", $opciones_selector_campos, true);
$smarty->assign("campo_periodo", $selector_periodo, true);
$smarty->assign("campo_prestador", $prestador, true);
$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('consulta_razonabilidad_2463.html.tpl');

/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('220','113','Consulta Razonabilidad','',FALSE,'..|consulta_razonabilidad_2463|consulta_razonabilidad_2463.php','50.02');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('220','5'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('220','4'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('220','3'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('220','2'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('220','1'); 
*/
//admin sistema
//admin eapb
//usuario normal eapb
//admin ips
//usuario normal ips

if(isset($_REQUEST['comprobante_submit'])
&& trim($_REQUEST['comprobante_submit'])=="enviado"

&& isset($_REQUEST['selector_rango_conteo'])
&& trim($_REQUEST['selector_rango_conteo'])!=""
&& trim($_REQUEST['selector_rango_conteo'])!="none"

&& isset($_REQUEST['year_de_corte'])
&& trim($_REQUEST['year_de_corte'])!=""

&& isset($_REQUEST['tipo_periodo_tiempo'])
&& trim($_REQUEST['tipo_periodo_tiempo'])!=""
&& trim($_REQUEST['tipo_periodo_tiempo'])!="none"

&& isset($_REQUEST['tipo_periodo_tiempo'])
&& trim($_REQUEST['tipo_periodo_tiempo'])!=""
&& trim($_REQUEST['tipo_periodo_tiempo'])!="none"

&& isset($_REQUEST['periodo'])
&& trim($_REQUEST['periodo'])!=""
&& trim($_REQUEST['periodo'])!="none"

&& isset($_REQUEST['selector_archivos_subidos'])
&& trim($_REQUEST['selector_archivos_subidos'])!=""
&& trim($_REQUEST['selector_archivos_subidos'])!="none"

 )//cierra condicional
{

	$selector_rango_conteo=trim($_REQUEST['selector_rango_conteo']);

	$year_de_corte=trim($_REQUEST['year_de_corte']);
	$codigo_periodo=trim($_REQUEST['periodo']);
	$tipo_periodo_tiempo=trim($_REQUEST['tipo_periodo_tiempo']);

	$identificador_archivo=trim($_REQUEST['selector_archivos_subidos']);
	$array_identificador=explode("_", trim($identificador_archivo) );

	//parte re diligencia los campos con la información seleccionada
	$html_script_re_diligencia="";
	$html_script_re_diligencia.="
	<script>
	reasignar_valores('$selector_rango_conteo','$tipo_periodo_tiempo','$codigo_periodo','$year_de_corte','$identificador_archivo');
	</script>
	";
	echo $html_script_re_diligencia;
	ob_flush();
	flush();
	//fin parte re diligencia los campos con la información seleccionada

	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('H:i:s');
	$fecha_para_archivo= date('Y-m-d-H-i-s');

	$nombre_archivo_resultado_consulta="consulta_razonabilidad_".$fecha_para_archivo;
	
	//crea directorio para evitar que se descarguen archivos pasados
	$rutaTemporal = '../TEMPORALES/';
	$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
	if(!file_exists($rutaTemporal.$nombre_archivo_resultado_consulta.$tiempo_actual_string))
	{
		mkdir($rutaTemporal.$nombre_archivo_resultado_consulta.$tiempo_actual_string, 0777);
	}
	else
	{
		$files_to_erase = glob($rutaTemporal.$nombre_archivo_resultado_consulta.$tiempo_actual_string."/*"); // get all file names
		foreach($files_to_erase as $file_to_be_erased)
		{ // iterate files
		  if(is_file($file_to_be_erased))
		  {
		    unlink($file_to_be_erased); // delete file
		  }
		}
	}
	$rutaTemporal=$rutaTemporal.$nombre_archivo_resultado_consulta.$tiempo_actual_string."/";
	//fin crea directorio para evitar que se descarguen archivos pasados

	

	

	

	$nombre_archivo_identificador="";
	$fecha_y_hora_validacion_identificador="";
	$fecha_de_corte_identificador="";

	if(count($array_identificador)>=3)
	{
		$nombre_archivo_identificador=$array_identificador[0];
		$fecha_y_hora_validacion_identificador=$array_identificador[1];
		$fecha_de_corte_identificador=$array_identificador[2];
	}//fin if

	$fecha_inicio_bd="";
	$fecha_corte_bd="";

	$resultados_consulta_periodo_informacion_4505=array();
	if($tipo_periodo_tiempo=="trimestral")
	{
	    $consultar_periodo_informacion_4505="";
	    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion WHERE cod_periodo_informacion='".$codigo_periodo."'; ";
	    $resultados_consulta_periodo_informacion_4505=$coneccionBD->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
	}//fin if
	else if($tipo_periodo_tiempo=="mensual")
	{
	    $consultar_periodo_informacion_4505="";
	    $consultar_periodo_informacion_4505.=" SELECT * FROM gioss_periodo_informacion_4505_mensual WHERE cod_periodo_informacion='".$codigo_periodo."'; ";
	    $resultados_consulta_periodo_informacion_4505=$coneccionBD->consultar2_no_crea_cierra($consultar_periodo_informacion_4505);
	}//fin if

	if(count($resultados_consulta_periodo_informacion_4505)>0
	   && is_array($resultados_consulta_periodo_informacion_4505)
	   )
	{				    
	    $fecha_inicio_periodo_bd=$resultados_consulta_periodo_informacion_4505[0]["fec_inicio_periodo"];
	    $array_fecha_inicio_periodo_bd=explode("-",$fecha_inicio_periodo_bd);
	    $fecha_inicio_bd=$year_de_corte."-".$array_fecha_inicio_periodo_bd[1]."-".$array_fecha_inicio_periodo_bd[2];

	    $fecha_final_periodo_bd=$resultados_consulta_periodo_informacion_4505[0]["fec_final_periodo"];
	    $array_fecha_final_periodo_bd=explode("-",$fecha_final_periodo_bd);
	    $fecha_corte_bd=$year_de_corte."-".$array_fecha_final_periodo_bd[1]."-".$array_fecha_final_periodo_bd[2];

	}//fin if verificacion fecha inicial con fecha inicial del periodo




	if($selector_rango_conteo=='all_allips')
	{
		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

		$mensaje_inicial="Se realizo la consulta para el archivo $nombre_archivo_identificador , con fecha de corte $fecha_de_corte_identificador, validado el $fecha_y_hora_validacion_identificador , y para todos los prestadores contenidos ";


		//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
		$ruta_escribir_archivo="";
		$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
		$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		fwrite($handler_file, $mensaje_inicial);
		fclose($handler_file);
		//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

		//titulo_mensaje_exito
		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

		$cont_campos=0;
		while($cont_campos<119)
		{
			$query_conteo_campo="";
			$query_conteo_campo.="SELECT campo".$cont_campos.", COUNT(*) as ocurrencias_c".$cont_campos."
			 from gioss_archivo_para_analisis_2463  
			WHERE 
			nombre_archivo='$nombre_archivo_identificador'
			AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
			AND fecha_de_corte='$fecha_de_corte_identificador'
			group by campo".$cont_campos." order by campo".$cont_campos."
			 ";
			$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

			$html_tabla="";
			if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
			{
				$array_resultado=lista_valores_permitidos_campo($cont_campos,$coneccionBD);
				$descripcion_campo=$array_resultado['descripcion_campo'];
				$array_valores_permitidos=$array_resultado['valores_permitidos'];
				$keys_valores_permitidos=array_keys($array_valores_permitidos);

				$array_descripcion_usada=array();

				$acumulado_otros=0;

				

				
				$html_tabla.="<table class='table_class'>";
				$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $cont_campos $descripcion_campo</th></tr>";

				$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
				$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				fwrite($handler_file, "\n".$linea_a_escribir);
				fclose($handler_file);

				$es_formato_fecha=false;
				foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
				{
					$valor_campo=trim($value_conteo_campo['campo'.$cont_campos]);
					$cantidad_campo=trim($value_conteo_campo['ocurrencias_c'.$cont_campos]);

					
					if(in_array($valor_campo, $keys_valores_permitidos) )
					{
						$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];

						if(in_array($descripcion_valor_permitido, $array_descripcion_usada)==false)
						{
							$array_descripcion_usada[]=$descripcion_valor_permitido;
						}//fin if
						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);
						
					}//fin if
					else
					{
						$array_es_fecha=explode("-", $valor_campo);
						if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
						{
							$es_formato_fecha=true;
						}//fin if
						$acumulado_otros+=intval($cantidad_campo);
					}//fin else

				}//fin foreach
				if($acumulado_otros>0)
				{
					$titulo_alternativo="Otros";
					if($es_formato_fecha==true)
					{
						$titulo_alternativo="AAAA-MM-DD";
					}//fin if
					else
					{
						$array_desc_valor_permitido_sin_usar=arraY();
						$array_desc_valor_permitido_sin_usar=array_diff($array_valores_permitidos , $array_descripcion_usada);
						$array_desc_valor_permitido_sin_usar=array_values($array_desc_valor_permitido_sin_usar);
						if(isset($array_desc_valor_permitido_sin_usar[0]))
						{
							$titulo_alternativo=$array_desc_valor_permitido_sin_usar[0];
						}//fin if

					}//fin else

					$html_tabla.="<tr class='tr_class'>";
					$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
					$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
					$html_tabla.="</tr>";

					$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

				}//fin if
				$html_tabla.="</table>";

			}//fin if
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$cont_campos++;
		}//fin while

		

		$html_script_variable="";
		$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
		echo $html_script_variable;
		ob_flush();
		flush();

		$html_boton_descarga="";		
		$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_boton_descarga\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();
		

	}//fin if all_allips
	else if($selector_rango_conteo=='specific_allips')
	{
		$agrupado_o_detallado=trim($_REQUEST['selector_general_o_detallado']);
		$campo_seleccionado=$_REQUEST['selector_campo_especifico'];

		if($agrupado_o_detallado=="conteo")
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$mensaje_inicial="Se realizo la consulta para el archivo $nombre_archivo_identificador , con fecha de corte $fecha_de_corte_identificador, validado el $fecha_y_hora_validacion_identificador ,  para todos los prestadores contenidos , y solo para el campo $campo_seleccionado ";


			//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
			$ruta_escribir_archivo="";
			$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
			$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
			fwrite($handler_file, $mensaje_inicial);
			fclose($handler_file);
			//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

			//titulo_mensaje_exito
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$cont_campos=intval($campo_seleccionado);
			if($cont_campos<119)
			{
				$query_conteo_campo="";
				$query_conteo_campo.="SELECT campo".$cont_campos.", COUNT(*) as ocurrencias_c".$cont_campos."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				group by campo".$cont_campos."
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($cont_campos,$coneccionBD);
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$array_descripcion_usada=array();

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $cont_campos $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);


					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo['campo'.$cont_campos]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_c'.$cont_campos]);

						
						if(in_array($valor_campo, $keys_valores_permitidos) )
						{
							$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];

							if(in_array($descripcion_valor_permitido, $array_descripcion_usada)==false)
							{
								$array_descripcion_usada[]=$descripcion_valor_permitido;
							}//fin if

							$html_tabla.="<tr class='tr_class'>";
							$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
							$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
							$html_tabla.="</tr>";

							$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
							
						}//fin if
						else
						{
							$array_es_fecha=explode("-", $valor_campo);
							if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
							{
								$es_formato_fecha=true;
							}//fin if
							$acumulado_otros+=intval($cantidad_campo);
						}//fin else

					}//fin foreach
					if($acumulado_otros>0)
					{
						$titulo_alternativo="Otros";
						if($es_formato_fecha==true)
						{
							$titulo_alternativo="AAAA-MM-DD";
						}//fin if
						else
						{
							$array_desc_valor_permitido_sin_usar=arraY();
							$array_desc_valor_permitido_sin_usar=array_diff($array_valores_permitidos , $array_descripcion_usada);
							$array_desc_valor_permitido_sin_usar=array_values($array_desc_valor_permitido_sin_usar);
							if(isset($array_desc_valor_permitido_sin_usar[0]))
							{
								$titulo_alternativo=$array_desc_valor_permitido_sin_usar[0];
							}//fin if

						}//fin else

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin if
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();

				//$cont_campos++; // no es un while
			}//fin if

			if($cont_campos==119)
			{
				//parte campos extra edad_years
				$nombre_campo='edad_years';
				$numero_campo=119;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);


					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						if($valor_campo=="")
						{
							$valor_campo="No Encontrado";
						}

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$valor_campo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin foreach
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==120)
			{
				//parte campos extra grupo_edad_quinquenal
				$nombre_campo='grupo_edad_quinquenal';
				$numero_campo=120;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						
						if(in_array($valor_campo, $keys_valores_permitidos) )
						{
							$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];
							$html_tabla.="<tr class='tr_class'>";
							$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
							$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
							$html_tabla.="</tr>";

							$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
							
						}//fin if
						else
						{
							$array_es_fecha=explode("-", $valor_campo);
							if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
							{
								$es_formato_fecha=true;
							}//fin if
							$acumulado_otros+=intval($cantidad_campo);
						}//fin else

					}//fin foreach
					if($acumulado_otros>0)
					{
						$titulo_alternativo="Otros";
						if($es_formato_fecha==true)
						{
							$titulo_alternativo="AAAA-MM-DD";
						}//fin if

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin if
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==121)
			{
				//parte campos extra grupo_etareo
				$nombre_campo='grupo_etareo';
				$numero_campo=121;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						
						if(in_array($valor_campo, $keys_valores_permitidos) )
						{
							$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];
							$html_tabla.="<tr class='tr_class'>";
							$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
							$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
							$html_tabla.="</tr>";

							$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
							
						}//fin if
						else
						{
							$array_es_fecha=explode("-", $valor_campo);
							if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
							{
								$es_formato_fecha=true;
							}//fin if
							$acumulado_otros+=intval($cantidad_campo);
						}//fin else

					}//fin foreach
					if($acumulado_otros>0)
					{
						$titulo_alternativo="Otros";
						if($es_formato_fecha==true)
						{
							$titulo_alternativo="AAAA-MM-DD";
						}//fin if

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin if
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==122)
			{
				//parte campos extra edad_dias
				$nombre_campo='edad_dias';
				$numero_campo=122;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						if($valor_campo=="")
						{
							$valor_campo="No Encontrado";
						}

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$valor_campo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin foreach			
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==123)
			{
				//parte campos extra regional
				$nombre_campo='regional';
				$numero_campo=123;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				group by $nombre_campo order by $nombre_campo
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						if($valor_campo=="")
						{
							$valor_campo="No Encontrado";
						}

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$valor_campo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

						
						

					}//fin foreach
					
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if 123

			$html_script_variable="";
			$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
			echo $html_script_variable;
			ob_flush();
			flush();

			$html_boton_descarga="";		
			$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_boton_descarga\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
		}//fin if
		else if($agrupado_o_detallado=="detallado")
		{
			

			$selector_valor_permitido_campo=trim($_REQUEST['selector_campo_valor_permitido_1']);

			$array_valor_permitido_campo_seleccionado=explode("-", $selector_valor_permitido_campo);
			$es_un_rango=false;
			$es_una_fecha=false;
			if(count($array_valor_permitido_campo_seleccionado)==2)
			{
				$es_un_rango=true;
			}//fin if
			else if(count($array_valor_permitido_campo_seleccionado)==3)
			{
				$es_una_fecha=true;
			}//fin else if

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$mensaje_inicial="Se realizo la consulta para el archivo $nombre_archivo_identificador , con fecha de corte $fecha_de_corte_identificador, validado el $fecha_y_hora_validacion_identificador ,  para todos los prestadores contenidos , y solo para el campo $campo_seleccionado con el valor permitido ( $selector_valor_permitido_campo )";

			//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
			$ruta_escribir_archivo="";
			$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
			$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
			fwrite($handler_file, $mensaje_inicial);
			fclose($handler_file);
			//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

			//titulo_mensaje_exito
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$cont_campos=intval($campo_seleccionado);
			if($cont_campos<119)
			{
				$complemento_where_query="";

				if($es_un_rango==false && $es_una_fecha==false)
				{
					$complemento_where_query.=" AND campo".$cont_campos."='$selector_valor_permitido_campo' ";
				}//fin if
				else if($es_un_rango==true)
				{
					$complemento_where_query.=" AND ( campo".$cont_campos."::numeric BETWEEN '".trim($array_valor_permitido_campo_seleccionado[0])."' AND '".trim($array_valor_permitido_campo_seleccionado[1])."') ";
					//echo "TEST ".$query_conteo_campo;
				}//fin else if
				else if($es_una_fecha==true)
				{
					$complemento_where_query.=" AND (campo".$cont_campos."::date BETWEEN '1900-12-31' AND '$fecha_de_corte_identificador') ";					
					//echo "TEST ".$query_conteo_campo;
				}//fin else if

				$query_numero_registros="";
				$query_numero_registros.="SELECT COUNT(*) as numero_registros 
					from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					$complemento_where_query
					 ";
				$resultado_numero_registros=$coneccionBD->consultar2_no_crea_cierra($query_numero_registros);

				$numero_registros_archivo=0;

				if(is_array($resultado_numero_registros) && count($resultado_numero_registros)>0)
				{
					$numero_registros_archivo=intval(trim($resultado_numero_registros[0]['numero_registros']));
				}//fin if

				echo "<span style='color:white'>".$numero_registros_archivo."</span>";

				$cantidad_registros_bloque=5000;
				$offset=0;
				$cantidad_bloques_consultados=0;
				$mensaje_estado="";

				while($offset<$numero_registros_archivo)
				{

					$titulos_campos=array();
					$query_conteo_campo="";
					$query_conteo_campo.="SELECT ";
					$cont_campos_identificadores=0;
					while($cont_campos_identificadores<11)
					{
						$query_conteo_campo.=" campo".$cont_campos_identificadores.",";
						$titulos_campos[]="Campo ".$cont_campos_identificadores;
						$cont_campos_identificadores++;
					}//fin while
					$query_conteo_campo.=" campo".$cont_campos;
					$titulos_campos[]="Campo ".$cont_campos;
					$query_conteo_campo.=" from gioss_archivo_para_analisis_2463  
						WHERE 
						nombre_archivo='$nombre_archivo_identificador'
						AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
						AND fecha_de_corte='$fecha_de_corte_identificador'
						$complemento_where_query
						order by campo1::int
						LIMIT $cantidad_registros_bloque OFFSET $offset
						 ";
					
					
					$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

					if($offset==0)
					{
						$titulos_a_escribir=implode("|", $titulos_campos);
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$titulos_a_escribir);
						fclose($handler_file);
					}//fin if

					$html_tabla="";
					if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
					{
						foreach ($resultado_query_conteo_campo as $key => $registro_actual) 
						{
							$linea_a_escribir=implode("|", $registro_actual);
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
						}//fin foreach
										

					}//fin if

					$offset+=$cantidad_registros_bloque;

					$cantidad_bloques_consultados++;

					if($offset>=$numero_registros_archivo)
					{
						$cantidad_registros_ultimo_bloque=count($resultado_query_conteo_campo);
					}//fin if
					else
					{
						$cantidad_registros_ultimo_bloque=$cantidad_registros_bloque;
					}

					$mensaje_estado.="Se han consultado el bloque de registros numero $cantidad_bloques_consultados de una cantidad aproximada  de $cantidad_registros_ultimo_bloque registros de un total de $numero_registros_archivo <br>";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$mensaje_estado\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();

				}//fin while

				$html_script_variable="";
				$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
				echo $html_script_variable;
				ob_flush();
				flush();

				$html_boton_descarga="";
				$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";
				
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$mensaje_estado $html_boton_descarga\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();

				//$cont_campos++; // no es un while
			}//fin if

		}//fin else if

	}//fin if specific_allips
	else if($selector_rango_conteo=='all_oneips')
	{
		$prestador_a_filtrar=trim($_REQUEST['prestador']);

		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

		$mensaje_inicial="Se realizo la consulta para el archivo $nombre_archivo_identificador , con fecha de corte $fecha_de_corte_identificador, validado el $fecha_y_hora_validacion_identificador , y para el prestador $prestador_a_filtrar ";


		//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
		$ruta_escribir_archivo="";
		$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
		$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		fwrite($handler_file, $mensaje_inicial);
		fclose($handler_file);
		//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

		//titulo_mensaje_exito
		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

		$cont_campos=0;
		while($cont_campos<119)
		{
			$query_conteo_campo="";
			$query_conteo_campo.="SELECT campo".$cont_campos.", COUNT(*) as ocurrencias_c".$cont_campos."
			 from gioss_archivo_para_analisis_2463  
			WHERE 
			nombre_archivo='$nombre_archivo_identificador'
			AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
			AND fecha_de_corte='$fecha_de_corte_identificador'
			AND campo2='$prestador_a_filtrar'
			group by campo".$cont_campos."
			 ";
			$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

			$html_tabla="";
			if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
			{
				$array_resultado=lista_valores_permitidos_campo($cont_campos,$coneccionBD);
				$descripcion_campo=$array_resultado['descripcion_campo'];
				$array_valores_permitidos=$array_resultado['valores_permitidos'];
				$keys_valores_permitidos=array_keys($array_valores_permitidos);

				$array_descripcion_usada=array();

				$acumulado_otros=0;

				

				
				$html_tabla.="<table class='table_class'>";
				$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $cont_campos $descripcion_campo</th></tr>";

				$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
				$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				fwrite($handler_file, "\n".$linea_a_escribir);
				fclose($handler_file);

				$es_formato_fecha=false;
				foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
				{
					$valor_campo=trim($value_conteo_campo['campo'.$cont_campos]);
					$cantidad_campo=trim($value_conteo_campo['ocurrencias_c'.$cont_campos]);

					
					if(in_array($valor_campo, $keys_valores_permitidos) )
					{
						$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];

						if(in_array($descripcion_valor_permitido, $array_descripcion_usada)==false)
						{
							$array_descripcion_usada[]=$descripcion_valor_permitido;
						}//fin if

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);
						
					}//fin if
					else
					{
						$array_es_fecha=explode("-", $valor_campo);
						if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
						{
							$es_formato_fecha=true;
						}//fin if
						$acumulado_otros+=intval($cantidad_campo);
					}//fin else

				}//fin foreach
				if($acumulado_otros>0)
				{
					$titulo_alternativo="Otros";
					if($es_formato_fecha==true)
					{
						$titulo_alternativo="AAAA-MM-DD";
					}//fin if
					else
					{
						$array_desc_valor_permitido_sin_usar=arraY();
						$array_desc_valor_permitido_sin_usar=array_diff($array_valores_permitidos , $array_descripcion_usada);
						$array_desc_valor_permitido_sin_usar=array_values($array_desc_valor_permitido_sin_usar);
						if(isset($array_desc_valor_permitido_sin_usar[0]))
						{
							$titulo_alternativo=$array_desc_valor_permitido_sin_usar[0];
						}//fin if

					}//fin else

					$html_tabla.="<tr class='tr_class'>";
					$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
					$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
					$html_tabla.="</tr>";

					$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

				}//fin if
				$html_tabla.="</table>";

			}//fin if
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$cont_campos++;
		}//fin while

		$html_script_variable="";
		$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
		echo $html_script_variable;
		ob_flush();
		flush();

		$html_boton_descarga="";		
		$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_boton_descarga\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

	}//fin if all_oneips
	else if($selector_rango_conteo=='specific_oneips')
	{
		$agrupado_o_detallado=$_REQUEST['selector_general_o_detallado'];
		$campo_seleccionado=$_REQUEST['selector_campo_especifico'];
		$prestador_a_filtrar=trim($_REQUEST['prestador']);

		if($agrupado_o_detallado=="conteo")
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$mensaje_inicial="Se realizo la consulta para el archivo $nombre_archivo_identificador , con fecha de corte $fecha_de_corte_identificador, validado el $fecha_y_hora_validacion_identificador ,  para el prestador $prestador_a_filtrar , y solo para el campo $campo_seleccionado ";


			//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
			$ruta_escribir_archivo="";
			$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
			$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
			fwrite($handler_file, $mensaje_inicial);
			fclose($handler_file);
			//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

			//titulo_mensaje_exito
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$cont_campos=intval($campo_seleccionado);
			if($cont_campos<119)
			{
				$query_conteo_campo="";
				$query_conteo_campo.="SELECT campo".$cont_campos.", COUNT(*) as ocurrencias_c".$cont_campos."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				AND campo2='$prestador_a_filtrar'
				group by campo".$cont_campos."
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($cont_campos,$coneccionBD);
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$array_descripcion_usada=array();

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $cont_campos $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);


					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo['campo'.$cont_campos]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_c'.$cont_campos]);

						
						if(in_array($valor_campo, $keys_valores_permitidos) )
						{
							$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];

							if(in_array($descripcion_valor_permitido, $array_descripcion_usada)==false)
							{
								$array_descripcion_usada[]=$descripcion_valor_permitido;
							}//fin if

							$html_tabla.="<tr class='tr_class'>";
							$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
							$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
							$html_tabla.="</tr>";

							$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
							
						}//fin if
						else
						{
							$array_es_fecha=explode("-", $valor_campo);
							if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
							{
								$es_formato_fecha=true;
							}//fin if
							$acumulado_otros+=intval($cantidad_campo);
						}//fin else

					}//fin foreach
					if($acumulado_otros>0)
					{
						$titulo_alternativo="Otros";
						if($es_formato_fecha==true)
						{
							$titulo_alternativo="AAAA-MM-DD";
						}//fin if
						else
						{
							$array_desc_valor_permitido_sin_usar=arraY();
							$array_desc_valor_permitido_sin_usar=array_diff($array_valores_permitidos , $array_descripcion_usada);
							$array_desc_valor_permitido_sin_usar=array_values($array_desc_valor_permitido_sin_usar);
							if(isset($array_desc_valor_permitido_sin_usar[0]))
							{
								$titulo_alternativo=$array_desc_valor_permitido_sin_usar[0];
							}//fin if

						}//fin else

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin if
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();

				//$cont_campos++; // no es while
			}//fin if

			if($cont_campos==119)
			{
				//parte campos extra edad_years
				$nombre_campo='edad_years';
				$numero_campo=119;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				AND campo2='$prestador_a_filtrar'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						if($valor_campo=="")
						{
							$valor_campo="No Encontrado";
						}

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$valor_campo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin foreach
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==120)
			{
				//parte campos extra grupo_edad_quinquenal
				$nombre_campo='grupo_edad_quinquenal';
				$numero_campo=120;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				AND campo2='$prestador_a_filtrar'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						
						if(in_array($valor_campo, $keys_valores_permitidos) )
						{
							$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];
							$html_tabla.="<tr class='tr_class'>";
							$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
							$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
							$html_tabla.="</tr>";

							$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
							
						}//fin if
						else
						{
							$array_es_fecha=explode("-", $valor_campo);
							if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
							{
								$es_formato_fecha=true;
							}//fin if
							$acumulado_otros+=intval($cantidad_campo);
						}//fin else

					}//fin foreach
					if($acumulado_otros>0)
					{
						$titulo_alternativo="Otros";
						if($es_formato_fecha==true)
						{
							$titulo_alternativo="AAAA-MM-DD";
						}//fin if

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin if
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==121)
			{
				//parte campos extra grupo_etareo
				$nombre_campo='grupo_etareo';
				$numero_campo=121;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				AND campo2='$prestador_a_filtrar'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						
						if(in_array($valor_campo, $keys_valores_permitidos) )
						{
							$descripcion_valor_permitido=$array_valores_permitidos[$valor_campo];
							$html_tabla.="<tr class='tr_class'>";
							$html_tabla.="<th class='td_class'>$descripcion_valor_permitido</th>";
							$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
							$html_tabla.="</tr>";

							$linea_a_escribir="\"$valor_campo\";\"$descripcion_valor_permitido\";\"$cantidad_campo\"";
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
							
						}//fin if
						else
						{
							$array_es_fecha=explode("-", $valor_campo);
							if(count($array_es_fecha)==3 && strlen($valor_campo)==10)
							{
								$es_formato_fecha=true;
							}//fin if
							$acumulado_otros+=intval($cantidad_campo);
						}//fin else

					}//fin foreach
					if($acumulado_otros>0)
					{
						$titulo_alternativo="Otros";
						if($es_formato_fecha==true)
						{
							$titulo_alternativo="AAAA-MM-DD";
						}//fin if

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$titulo_alternativo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$acumulado_otros</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$titulo_alternativo\";\"$titulo_alternativo\";\"$acumulado_otros\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin if
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==122)
			{
				//parte campos extra edad_dias
				$nombre_campo='edad_dias';
				$numero_campo=122;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				AND campo2='$prestador_a_filtrar'
				group by $nombre_campo order by $nombre_campo::int
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						if($valor_campo=="")
						{
							$valor_campo="No Encontrado";
						}

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$valor_campo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

					}//fin foreach			
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if

			if($cont_campos==123)
			{
				//parte campos extra regional
				$nombre_campo='regional';
				$numero_campo=123;

				$query_conteo_campo="";
				$query_conteo_campo.="SELECT ".$nombre_campo.", COUNT(*) as ocurrencias_".$nombre_campo."
				 from gioss_archivo_para_analisis_2463  
				WHERE 
				nombre_archivo='$nombre_archivo_identificador'
				AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
				AND fecha_de_corte='$fecha_de_corte_identificador'
				AND campo2='$prestador_a_filtrar'
				group by $nombre_campo order by $nombre_campo
				 ";
				$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

				$html_tabla="";
				if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
				{
					$array_resultado=lista_valores_permitidos_campo($numero_campo,$coneccionBD);//119 edad years
					$descripcion_campo=$array_resultado['descripcion_campo'];
					$array_valores_permitidos=$array_resultado['valores_permitidos'];
					$keys_valores_permitidos=array_keys($array_valores_permitidos);

					$acumulado_otros=0;

					

					
					$html_tabla.="<table class='table_class'>";
					$html_tabla.="<tr class='tr_class'><th colspan='100' class='th_class th_class2'>Campo Numero $numero_campo $descripcion_campo</th></tr>";

					$linea_a_escribir="Campo Numero $cont_campos $descripcion_campo";
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$linea_a_escribir);
					fclose($handler_file);

					$es_formato_fecha=false;
					foreach ($resultado_query_conteo_campo as $key => $value_conteo_campo) 
					{
						$valor_campo=trim($value_conteo_campo[$nombre_campo]);
						$cantidad_campo=trim($value_conteo_campo['ocurrencias_'.$nombre_campo]);

						if($valor_campo=="")
						{
							$valor_campo="No Encontrado";
						}

						$html_tabla.="<tr class='tr_class'>";
						$html_tabla.="<th class='td_class'>$valor_campo</th>";
						$html_tabla.="<td class='td_class' style='text-align:right;'>$cantidad_campo</td>";
						$html_tabla.="</tr>";

						$linea_a_escribir="\"$valor_campo\";\"$cantidad_campo\"";
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$linea_a_escribir);
						fclose($handler_file);

						
						

					}//fin foreach
					
					$html_tabla.="</table>";

				}//fin if
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_tabla\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();
				//fin parte campos extra
			}//fin if 123

			$html_script_variable="";
			$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
			echo $html_script_variable;
			ob_flush();
			flush();

			$html_boton_descarga="";		
			$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=document.getElementById(\"parrafo_mensaje_exito\").innerHTML+\"<br>\"+\"$html_boton_descarga\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
		}//fin if conteo agrupado
		else if($agrupado_o_detallado=="detallado")
		{

			$prestador_a_filtrar=trim($_REQUEST['prestador']);
			$selector_valor_permitido_campo=trim($_REQUEST['selector_campo_valor_permitido_1']);

			$array_valor_permitido_campo_seleccionado=explode("-", $selector_valor_permitido_campo);
			$es_un_rango=false;
			$es_una_fecha=false;
			if(count($array_valor_permitido_campo_seleccionado)==2)
			{
				$es_un_rango=true;
			}//fin if
			else if(count($array_valor_permitido_campo_seleccionado)==3)
			{
				$es_una_fecha=true;
			}//fin else if

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$mensaje_inicial="Se realizo la consulta para el archivo $nombre_archivo_identificador , con fecha de corte $fecha_de_corte_identificador, validado el $fecha_y_hora_validacion_identificador ,  para el prestador $prestador_a_filtrar, y solo para el campo $campo_seleccionado con el valor permitido ( $selector_valor_permitido_campo )";

			//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
			$ruta_escribir_archivo="";
			$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
			$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
			fwrite($handler_file, $mensaje_inicial);
			fclose($handler_file);
			//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

			//titulo_mensaje_exito
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$cont_campos=intval($campo_seleccionado);
			if($cont_campos<119)
			{
				$complemento_where_query="";

				if($es_un_rango==false && $es_una_fecha==false)
				{
					$complemento_where_query.=" AND campo".$cont_campos."='$selector_valor_permitido_campo' ";
				}//fin if
				else if($es_un_rango==true)
				{
					$complemento_where_query.=" AND ( campo".$cont_campos."::numeric BETWEEN '".trim($array_valor_permitido_campo_seleccionado[0])."' AND '".trim($array_valor_permitido_campo_seleccionado[1])."') ";
					//echo "TEST ".$query_conteo_campo;
				}//fin else if
				else if($es_una_fecha==true)
				{
					$complemento_where_query.=" AND (campo".$cont_campos."::date BETWEEN '1900-12-31' AND '$fecha_de_corte_identificador') ";					
					//echo "TEST ".$query_conteo_campo;
				}//fin else if

				$query_numero_registros="";
				$query_numero_registros.="SELECT COUNT(*) as numero_registros 
					from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					AND campo2='$prestador_a_filtrar'
					$complemento_where_query
					 ";
				$resultado_numero_registros=$coneccionBD->consultar2_no_crea_cierra($query_numero_registros);

				$numero_registros_archivo=0;

				if(is_array($resultado_numero_registros) && count($resultado_numero_registros)>0)
				{
					$numero_registros_archivo=intval(trim($resultado_numero_registros[0]['numero_registros']));
				}//fin if

				echo "<span style='color:white'>".$numero_registros_archivo."</span>";

				$cantidad_registros_bloque=5000;
				$offset=0;
				$cantidad_bloques_consultados=0;
				$mensaje_estado="";

				while($offset<$numero_registros_archivo)
				{

					$titulos_campos=array();
					$query_conteo_campo="";
					$query_conteo_campo.="SELECT ";
					$cont_campos_identificadores=0;
					while($cont_campos_identificadores<11)
					{
						$query_conteo_campo.=" campo".$cont_campos_identificadores.",";
						$titulos_campos[]="Campo ".$cont_campos_identificadores;
						$cont_campos_identificadores++;
					}//fin while
					$query_conteo_campo.=" campo".$cont_campos;
					$titulos_campos[]="Campo ".$cont_campos;
					$query_conteo_campo.=" from gioss_archivo_para_analisis_2463  
						WHERE 
						nombre_archivo='$nombre_archivo_identificador'
						AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
						AND fecha_de_corte='$fecha_de_corte_identificador'
						AND campo2='$prestador_a_filtrar'
						$complemento_where_query
						order by campo1::int
						LIMIT $cantidad_registros_bloque OFFSET $offset
						 ";
					
					$resultado_query_conteo_campo=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campo);

					if($offset==0)
					{
						$titulos_a_escribir=implode("|", $titulos_campos);
						$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						fwrite($handler_file, "\n".$titulos_a_escribir);
						fclose($handler_file);
					}//fin if

					$html_tabla="";
					if(is_array($resultado_query_conteo_campo) && count($resultado_query_conteo_campo) )
					{
						foreach ($resultado_query_conteo_campo as $key => $registro_actual) 
						{
							$linea_a_escribir=implode("|", $registro_actual);
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
						}//fin foreach
										

					}//fin if

					$offset+=$cantidad_registros_bloque;

					$cantidad_bloques_consultados++;

					if($offset>=$numero_registros_archivo)
					{
						$cantidad_registros_ultimo_bloque=count($resultado_query_conteo_campo);
					}//fin if
					else
					{
						$cantidad_registros_ultimo_bloque=$cantidad_registros_bloque;
					}

					$mensaje_estado.="Se han consultado el bloque de registros numero $cantidad_bloques_consultados de una cantidad aproximada  de $cantidad_registros_ultimo_bloque registros de un total de $numero_registros_archivo <br>";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$mensaje_estado\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();

				}//fin while

				$html_script_variable="";
				$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
				echo $html_script_variable;
				ob_flush();
				flush();

				$html_boton_descarga="";
				$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";
				
				$html_javascript_resultado="";
				$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$mensaje_estado $html_boton_descarga\";</script>";
				echo $html_javascript_resultado;
				ob_flush();
				flush();

				//$cont_campos++; // no es un while
			}//fin if

		}//fin else if detallado
	}//fin if specific_oneips
	else if($selector_rango_conteo=="clone")
	{
		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"Escribiendo La copia del archivo seleccionado...\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();

		$mensaje_inicial="Se construira una copia del archivo con los datos almacenados ";


		//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
		$ruta_escribir_archivo="";
		$ruta_escribir_archivo=$rutaTemporal.$nombre_archivo_identificador.".res";
		$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
		//fwrite($handler_file, $mensaje_inicial);
		fclose($handler_file);
		//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS

		$query_conteo_registros="";
		$query_conteo_registros.="SELECT COUNT(*) as numero_registros
		 from gioss_archivo_para_analisis_2463  
		WHERE 
		nombre_archivo='$nombre_archivo_identificador'
		AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
		AND fecha_de_corte='$fecha_de_corte_identificador'
		 ";
		$resultado_query_conteo_registros=$coneccionBD->consultar2_no_crea_cierra($query_conteo_registros);

		$numero_registros_archivo=0;

		if(is_array($resultado_query_conteo_registros) && count($resultado_query_conteo_registros)>0)
		{
			$numero_registros_archivo=intval(trim($resultado_query_conteo_registros[0]['numero_registros']));
		}//fin if

		echo "<span style='color:white'>".$numero_registros_archivo."</span>";
		$cantidad_registros_bloque=5000;
		$offset=0;
		$cantidad_bloques_consultados=0;
		$mensaje_estado="";
		while($offset<$numero_registros_archivo)
		{
			$string_campos="";
			$cont_campos=0;
			while($cont_campos<119)
			{
				if($string_campos!=""){$string_campos.=",";}
				$string_campos.="campo".$cont_campos;
				$cont_campos++;
			}//fin while

			$query_registros="";
			$query_registros.="SELECT $string_campos
			 from gioss_archivo_para_analisis_2463  
			WHERE 
			nombre_archivo='$nombre_archivo_identificador'
			AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
			AND fecha_de_corte='$fecha_de_corte_identificador'
			order by campo1::int
			LIMIT $cantidad_registros_bloque OFFSET $offset

			 ";
			$resultado_query_registros=$coneccionBD->consultar2_no_crea_cierra($query_registros);

			foreach ($resultado_query_registros as $key => $registro) 
			{
				$linea_a_escribir=implode("|", $registro);
				$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
				fwrite($handler_file, "\n".$linea_a_escribir);
				fclose($handler_file);
			}//fin foreach

			$offset+=$cantidad_registros_bloque;

			$cantidad_bloques_consultados++;

			if($offset>=$numero_registros_archivo)
			{
				$cantidad_registros_ultimo_bloque=count($resultado_query_registros);
			}//fin if
			else
			{
				$cantidad_registros_ultimo_bloque=$cantidad_registros_bloque;
			}

			$mensaje_estado.="Se han consultado el bloque de registros numero $cantidad_bloques_consultados de una cantidad aproximada  de $cantidad_registros_ultimo_bloque registros de un total de $numero_registros_archivo <br>";

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$mensaje_estado\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
		}//fin while

		$html_script_variable="";
		$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
		echo $html_script_variable;
		ob_flush();
		flush();

		$html_boton_descarga="";
		$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"Proceso Completado\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();
		
		$html_javascript_resultado="";
		$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$mensaje_estado $html_boton_descarga\";</script>";
		echo $html_javascript_resultado;
		ob_flush();
		flush();
	}//fin if clonar, descargar archivo almacenado
	/*
	else if($selector_rango_conteo=="cross_allips")
	{
		$selector_all_or_one_vp=trim($_REQUEST['selector_all_or_one_vp']);
		$selector_general_o_detallado_cross=trim($_REQUEST['selector_general_o_detallado_cross']);

		$cont_campos_cross=1;//ya que no existe el campo cross cero
		$array_campos_cruzados_seleccionados=array();
		$array_valores_permitidos_de_los_campos_seleccionados=array();
		$mensajes_error="";

		$numero_campo_1_sel_para_reasignar="";
		$numero_campo_2_sel_para_reasignar="";
		$vp_campo_1_sel_para_reasignar="";
		$vp_campo_2_sel_para_reasignar="";
		if(isset($_REQUEST['campocross_1'])==true)
		{
			$numero_campo_1_sel_para_reasignar=trim($_REQUEST['campocross_1']);
		}//fin if
		if(isset($_REQUEST['campocross_2'])==true)
		{
			$numero_campo_2_sel_para_reasignar=trim($_REQUEST['campocross_2']);
		}//fin if
		if(isset($_REQUEST['campocrossvp_1'])==true)
		{
			$vp_campo_1_sel_para_reasignar=trim($_REQUEST['campocrossvp_1']);
		}//fin if
		if(isset($_REQUEST['campocrossvp_2'])==true)
		{
			$vp_campo_2_sel_para_reasignar=trim($_REQUEST['campocrossvp_2']);
		}//fin if

		//parte re diligencia los campos con la información seleccionada
		$html_script_re_diligencia="";
		$html_script_re_diligencia.="
		<script>
		reasignar_valores_consulta_cruzada('$selector_all_or_one_vp','$selector_general_o_detallado_cross','$numero_campo_1_sel_para_reasignar','$numero_campo_2_sel_para_reasignar','$vp_campo_1_sel_para_reasignar','$vp_campo_2_sel_para_reasignar');
		</script>
		";
		echo $html_script_re_diligencia;
		ob_flush();
		flush();
		//fin parte re diligencia los campos con la información seleccionada


		while(isset($_REQUEST['campocross_'.$cont_campos_cross])==true )
		{
			$campo_actual=trim($_REQUEST['campocross_'.$cont_campos_cross]);
			$valor_permitido_campo_actual="";
			
			if($campo_actual=="none" || $campo_actual=="")
			{
				$mensajes_error.="No se selecciono un valor para el selector de campo numero $cont_campos_cross .<br>";
			}//fin if

			if(
				in_array($campo_actual, $array_campos_cruzados_seleccionados)
				&& $campo_actual!="none" && $campo_actual!="" 
			)
			{
				$mensajes_error.="El valor  $campo_actual del selector de campo $cont_campos_cross numero fue seleccionado por el usuario mas de una vez.<br>";
			}//fin if

			$array_campos_cruzados_seleccionados[]=$campo_actual;
			
			if(isset($_REQUEST['campocrossvp_'.$cont_campos_cross])==true)
			{
				$valor_permitido_campo_actual=trim($_REQUEST['campocrossvp_'.$cont_campos_cross]);

				if($selector_all_or_one_vp=="specificvp" 
					&& ($valor_permitido_campo_actual=="" || $valor_permitido_campo_actual=="none") 
					)
				{
					$mensajes_error.="Debe seleccionar un valor permitido para el selector de campo $cont_campos_cross.<br>";
				}//fin if

				$array_valores_permitidos_de_los_campos_seleccionados[]=$valor_permitido_campo_actual;
			}//fin if agrega los valores permitidos encontrados
			
			$cont_campos_cross++;
		}//fin while

		//echo print_r($array_campos_cruzados_seleccionados,true)."<br>".print_r($array_valores_permitidos_de_los_campos_seleccionados,true)."<br>";

		if($mensajes_error=="")
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			//si no hay errores se procede a realizar la consulta cruzada entre los campos seleccionados
			//y dependiendo si se selecciono si se especifica valor permitido para los campos , es conteo, o detallado
			if($selector_all_or_one_vp=="allvp")
			{
				if($selector_general_o_detallado_cross=="conteo")
				{
					$numero_campo_base=$array_campos_cruzados_seleccionados[0];
					$complemento_query_campos_seleccionados_p1="";
					$complemento_query_campos_seleccionados_p2="";

					$listado_vp_para_los_campos_cruzados=array();

					foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
					{
						if($complemento_query_campos_seleccionados_p1!=""){$complemento_query_campos_seleccionados_p1.=",";}
						$complemento_query_campos_seleccionados_p1.="campo".$numero_campo_cruzado;

						

						$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]=lista_valores_permitidos_campo($numero_campo_cruzado,$coneccionBD);

						if($complemento_query_campos_seleccionados_p2!=""){$complemento_query_campos_seleccionados_p2.=", ";}
						$complemento_query_campos_seleccionados_p2.="Campo ".$numero_campo_cruzado." ".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['descripcion_campo'];
					}//fin foreach
									

					$query_conteo_campos_cruzados="";
					$query_conteo_campos_cruzados.="SELECT $complemento_query_campos_seleccionados_p1 , COUNT(*) as ocurrencias
					 from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					group by $complemento_query_campos_seleccionados_p1 ORDER BY campo".$numero_campo_base."
					 ";
					$resultado_query_conteo_campos_cruzados=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campos_cruzados);

					$matriz_organizadora=array();//esta matriz se encargara de organizar los valores apra la comparacion correspondiente

					$titulo_esquina_cruce="";

					foreach ($listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'] as $key => $valor_permitido_puro_campo_base) 
					{
						$array_ocurrencias_valor_permitido_campo_base=array();
						$acumulado_si_es_fecha_secundario=0;
						$acumulado_si_es_rango_secundario=0;
						$acumulado_si_es_fecha_ambos=0;
						$acumulado_si_es_rango_ambos=0;
						$acumulado_si_es_fecha_mixed=0;
						$acumulado_si_es_rango_mixed=0;
						foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
						{
							if($numero_campo_base!=$numero_campo_cruzado)
							{
								$titulo_esquina_cruce=$numero_campo_base."-".$numero_campo_cruzado;
								foreach ($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'] as $key => $valor_permitido_puro_campo_secundario) 
								{
									$se_encontro_numero_ocurrencias=false;
									$numero_ocurrencias="";

									//PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO
									//BASE
									$es_formato_fecha_campo_cruzado_base=false;
									$es_rango_campo_cruzado_base=false;
									
									$valor_campo=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if( $valor_campo=="AAAA-MM-DD"
										&& $valor_campo2!="AAAA-MM-DD"
										)
									{
										$es_formato_fecha_campo_cruzado_base=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2
										&& count($array_es_fecha_o_rango2)!=2
									 )
									{
										$es_rango_campo_cruzado_base=true;
									}//fin else if
									//FIN BASE

									//SECUNDARIO
									$es_formato_fecha_campo_cruzado_secundario=false;
									$es_rango_campo_cruzado_secundario=false;

									$valor_campo=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if($valor_campo=="AAAA-MM-DD"
										&& $valor_campo2!="AAAA-MM-DD"
										)
									{
										$es_formato_fecha_campo_cruzado_secundario=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2
										&& count($array_es_fecha_o_rango2)!=2
										)
									{
										$es_rango_campo_cruzado_secundario=true;
									}//fin else if
									//FIN SECUNDARIO


									//AMBOS
									$es_formato_fecha_campo_cruzado_ambos=false;
									$es_rango_campo_cruzado_ambos=false;
									
									$valor_campo=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if($valor_campo=="AAAA-MM-DD"
										&& $valor_campo2=="AAAA-MM-DD"
										)
									{
										$es_formato_fecha_campo_cruzado_ambos=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2 
										&& count($array_es_fecha_o_rango2)==2 
										)
									{
										$es_rango_campo_cruzado_ambos=true;
									}//fin else if
									//FIN AMBOS

									//AMBOS MIXED
									$es_formato_fecha_campo_cruzado_mixed=false;
									$es_rango_campo_cruzado_mixed=false;
									
									$valor_campo=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if($valor_campo=="AAAA-MM-DD"
										&& count($array_es_fecha_o_rango2)==2
										
										)
									{
										$es_formato_fecha_campo_cruzado_mixed=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2 
										&& $valor_campo2=="AAAA-MM-DD"
										)
									{
										$es_rango_campo_cruzado_mixed=true;
									}//fin else if
									//FIN AMBOS MIXED

									
									//FIN PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO

									foreach ($resultado_query_conteo_campos_cruzados as $key => $resultado_cruce) 
									{
										//PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO
										//BASE
										$es_formato_fecha_campo_cruzado_base_res=false;
										$es_rango_campo_cruzado_base_res=false;										
										
										$valor_campo=$resultado_cruce['campo'.$numero_campo_base];
										$array_es_fecha_o_rango=explode("-", $valor_campo);
										
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10 
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false
											&& $resultado_cruce['campo'.$numero_campo_cruzado]==$valor_permitido_puro_campo_secundario
											)
										{											
											$es_formato_fecha_campo_cruzado_base_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false 
											&& $resultado_cruce['campo'.$numero_campo_cruzado]==$valor_permitido_puro_campo_secundario
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											$es_rango_campo_cruzado_base_res=true;
										}//fin else if
										//FIN BASE
										
										//SECUNDARIO
										$es_formato_fecha_campo_cruzado_secundario_res=false;
										$es_rango_campo_cruzado_secundario_res=false;

										$valor_campo=$resultado_cruce['campo'.$numero_campo_cruzado];
										$array_es_fecha_o_rango=explode("-", $valor_campo);										
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											&& $resultado_cruce['campo'.$numero_campo_base]==$valor_permitido_puro_campo_base
											)
										{
											$es_formato_fecha_campo_cruzado_secundario_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											&& $resultado_cruce['campo'.$numero_campo_base]==$valor_permitido_puro_campo_base
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											$es_rango_campo_cruzado_secundario_res=true;
										}//fin else if
										//FIN SECUNDARIO

										//AMBOS
										$es_formato_fecha_campo_cruzado_ambos_res=false;
										$es_rango_campo_cruzado_ambos_res=false;										
										
										$valor_campo=$resultado_cruce['campo'.$numero_campo_base];
										$array_es_fecha_o_rango=explode("-", $valor_campo);

										$valor_campo2=$resultado_cruce['campo'.$numero_campo_cruzado];
										$array_es_fecha_o_rango2=explode("-", $valor_campo2);

																			
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10 
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false
											
											&& count($array_es_fecha_o_rango2)==3 && strlen($valor_campo2)==10
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											)
										{	
											//echo "pre fecha v1 $valor_campo v2 $valor_campo2<br>";									
											$es_formato_fecha_campo_cruzado_ambos_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false 
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											&& count($array_es_fecha_o_rango)!=3
											&& count($array_es_fecha_o_rango2)!=3
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											//echo "pre rango v1 $valor_campo v2 $valor_campo2<br>";
											$es_rango_campo_cruzado_ambos_res=true;
										}//fin else if
										//FIN AMBOS

										//MIXED
										$es_formato_fecha_campo_cruzado_mixed_res=false;
										$es_rango_campo_cruzado_mixed_res=false;										
										
										$valor_campo=$resultado_cruce['campo'.$numero_campo_base];
										$array_es_fecha_o_rango=explode("-", $valor_campo);

										$valor_campo2=$resultado_cruce['campo'.$numero_campo_cruzado];
										$array_es_fecha_o_rango2=explode("-", $valor_campo2);

																			
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10 
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false
											
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 											
											)
										{	
											//echo "pre fecha v1 $valor_campo v2 $valor_campo2<br>";									
											$es_formato_fecha_campo_cruzado_mixed_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false 
											
											&& count($array_es_fecha_o_rango2)==3 && strlen($valor_campo2)==10
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											//echo "pre rango v1 $valor_campo v2 $valor_campo2<br>";
											$es_rango_campo_cruzado_mixed_res=true;
										}//fin else if
										//FIN MIXED

										//FIN PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO

										if($resultado_cruce['campo'.$numero_campo_base]==$valor_permitido_puro_campo_base
											&& $resultado_cruce['campo'.$numero_campo_cruzado]==$valor_permitido_puro_campo_secundario
											)
										{
											$se_encontro_numero_ocurrencias=true;
											$numero_ocurrencias=$resultado_cruce['ocurrencias'];
										}//fin if										
										else if(
											($es_formato_fecha_campo_cruzado_secundario_res==true 
											|| $es_formato_fecha_campo_cruzado_base_res==true)
											&& $es_formato_fecha_campo_cruzado_ambos_res==false 
											&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO FECHA C res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario ".print_r($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'],true)."<br>";
											$acumulado_si_es_fecha_secundario+=intval($resultado_cruce['ocurrencias']);
										}//fin else if
										else if(
											($es_rango_campo_cruzado_secundario_res==true 
											|| $es_rango_campo_cruzado_base_res==true)
											&& $es_rango_campo_cruzado_ambos_res==false
											&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO RANGO C res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario<br>";
											$acumulado_si_es_rango_secundario+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_rango_secundario ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										else if($es_formato_fecha_campo_cruzado_ambos_res==true 
											//&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO FECHA A res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario ".print_r($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'],true)."<br>";
											$acumulado_si_es_fecha_ambos+=intval($resultado_cruce['ocurrencias']);
										}//fin else if
										else if($es_rango_campo_cruzado_ambos_res==true 
											//&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO RANGO A res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario<br>";
											$acumulado_si_es_rango_ambos+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_rango_ambos ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										else if($es_formato_fecha_campo_cruzado_mixed_res==true )
										{
											//echo "ENTRO FECHA res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario ".print_r($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'],true)."<br>";
											$acumulado_si_es_fecha_mixed+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_fecha_mixed ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										else if($es_rango_campo_cruzado_mixed_res==true )
										{
											//echo "ENTRO RANGO res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario<br>";
											$acumulado_si_es_rango_mixed+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_rango_mixed ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										
									}//fin foreach
									if($se_encontro_numero_ocurrencias==false)
									{
										$numero_ocurrencias=0;
									}//fin if

									$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$numero_ocurrencias;
										
														
									if(
										($es_formato_fecha_campo_cruzado_secundario==true 
										|| $es_formato_fecha_campo_cruzado_base==true )
										&& $es_formato_fecha_campo_cruzado_ambos==false
										&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de fechas
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_fecha_secundario;
										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila fcc $acumulado_si_es_fecha_secundario<br>";

										$acumulado_si_es_fecha_secundario=0;
										
									}//fin if
									else if(
										($es_rango_campo_cruzado_secundario==true 
										|| $es_rango_campo_cruzado_base==true)
										&& $es_rango_campo_cruzado_ambos==false
										&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de rangos
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_rango_secundario;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila rcc $acumulado_si_es_rango_secundario<br>";

										$acumulado_si_es_rango_secundario=0;
										
									}//fin if
									else if(
										$es_formato_fecha_campo_cruzado_ambos==true
										//&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de fechas
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_fecha_ambos;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila fa $acumulado_si_es_fecha_ambos<br>";

										$acumulado_si_es_fecha_ambos=0;
										
									}//fin if
									else if(
										$es_rango_campo_cruzado_ambos==true
										//&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de rangos
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_rango_ambos;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila ra $acumulado_si_es_rango_ambos<br>";

										$acumulado_si_es_rango_ambos=0;
										
									}//fin if
									else if(
										$es_formato_fecha_campo_cruzado_mixed==true
										)
									{
										//se reasigna con el acumulado de fechas
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_fecha_mixed;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila fm $acumulado_si_es_fecha_mixed<br>";

										$acumulado_si_es_fecha_mixed=0;
									}//fin if
									else if(
										$es_rango_campo_cruzado_mixed==true
										)
									{
										//se reasigna con el acumulado de rangos
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_rango_mixed;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila rm $acumulado_si_es_rango_mixed<br>";

										$acumulado_si_es_rango_mixed=0;
										
									}//fin if
									else
									{
										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila defecto $numero_ocurrencias<br>";
									}
									
									
									
								}//fin foreach
							}//fin if
						}//fin foreach 
						$matriz_organizadora[$numero_campo_base."_".$valor_permitido_puro_campo_base]=$array_ocurrencias_valor_permitido_campo_base;
					}//fin foreach

					

					foreach ($resultado_query_conteo_campos_cruzados as $key => $resultado_cruce) 
					{
						//echo print_r($resultado_cruce,true)."<br>";

					}//fin imprime array resultado  para debug

					$html_resultado_consulta="";

					$html_resultado_consulta.="<table class='table_class' >";
					$html_resultado_consulta.="<tr class='tr_class'><th colspan='100' class='th_class th_class2' style='text-align:center;'>Consulta Cruzada, Campos Involucrados: $complemento_query_campos_seleccionados_p2</th></tr>";

					$nombres_filas=array_keys($matriz_organizadora);
					$escribe_titulos_columnas=true;

					$cont_filas=0;

					foreach ($matriz_organizadora as $key => $filas_vp) 
					{
						$nombres_columnas=array_keys($filas_vp);
						if($escribe_titulos_columnas==true)
						{
							$html_resultado_consulta.="<tr class='tr_class'>";
							$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$titulo_esquina_cruce</th>";
							foreach ($nombres_columnas as $key => $titulo_columna)
							{
								$array_titulo_columna=explode("_", $titulo_columna);
								$campo_titulo_col=$array_titulo_columna[0];
								$valor_permitido_campo_titulo_col=$array_titulo_columna[1];
								$descripcion=$listado_vp_para_los_campos_cruzados[$campo_titulo_col]['valores_permitidos'][$valor_permitido_campo_titulo_col];
								$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>Campo $campo_titulo_col: $descripcion</th>";
							}//fin foreach
							$html_resultado_consulta.="</tr>";
							$escribe_titulos_columnas=false;
						}//fin if escribe titulos columnas


						$html_resultado_consulta.="<tr class='tr_class'>";

						$array_titulo_fila=explode("_", $nombres_filas[$cont_filas]);
						$campo_titulo_fila=$array_titulo_fila[0];
						$valor_permitido_campo_titulo_fila=$array_titulo_fila[1];
						$descripcion=$listado_vp_para_los_campos_cruzados[$campo_titulo_fila]['valores_permitidos'][$valor_permitido_campo_titulo_fila];
						$html_resultado_consulta.="<td class='th_class th_class2' style='text-align:center;'><b>Campo $campo_titulo_fila: $descripcion </b></td>";
						//escribe columnas
						foreach ($filas_vp as $key => $columnas_vp) 
						{
							$html_resultado_consulta.="<td class='td_class' style='text-align:center;'>$columnas_vp</td>";
						}//fin foreach

						$html_resultado_consulta.="</tr>";

						$cont_filas++;
					}//fin foreach					
					$html_resultado_consulta.="</table>";

					
					//echo print_r($listado_vp_para_los_campos_cruzados,true)."<br>";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_resultado_consulta\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();
					

				}//fin if
				else if($selector_general_o_detallado_cross=="detallado")
				{
					//no hay para cuando son todos los valores permitidos
				}//fin else if

			}//fin if
			else if($selector_all_or_one_vp=="specificvp")
			{
				if($selector_general_o_detallado_cross=="conteo")
				{
					$numero_campo_base=$array_campos_cruzados_seleccionados[0];
					$complemento_query_campos_seleccionados_p1="";
					$complemento_query_campos_seleccionados_p2="";

					$complemento_query_where="";

					$listado_vp_para_los_campos_cruzados=array();

					$cont_vp_campos=0;

					$nombre_fila="";
					$nombre_columna="";

					$titulo_esquina_cruce="";

					foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
					{
						if($titulo_esquina_cruce!=""){$titulo_esquina_cruce.="-";}
						$titulo_esquina_cruce.=$numero_campo_cruzado;

						if($complemento_query_campos_seleccionados_p1!=""){$complemento_query_campos_seleccionados_p1.=",";}
						$complemento_query_campos_seleccionados_p1.="campo".$numero_campo_cruzado;

						

						$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]=lista_valores_permitidos_campo($numero_campo_cruzado,$coneccionBD);

						if($complemento_query_campos_seleccionados_p2!=""){$complemento_query_campos_seleccionados_p2.=", ";}
						$complemento_query_campos_seleccionados_p2.="Campo ".$numero_campo_cruzado." ".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['descripcion_campo'];

						$vp_campo_actual=$array_valores_permitidos_de_los_campos_seleccionados[$cont_vp_campos];
						$array_vp_campo_actual=explode("-", $vp_campo_actual);
						
						if($vp_campo_actual=="AAAA-MM-DD")
						{
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::date BETWEEN '1900-12-31' AND '$fecha_actual') ";
							
						}//fin if
						else if(count($array_vp_campo_actual)==2)
						{
							$vp1=$array_vp_campo_actual[0];
							$vp2=$array_vp_campo_actual[1];
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::numeric BETWEEN '$vp1' AND '$vp2') ";
						}//fin else if
						else
						{
							$complemento_query_where.=" AND campo".$numero_campo_cruzado."='".$vp_campo_actual."' ";
						}//fin else defecto

						if($key==0)
						{
							$nombre_fila="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}
						else
						{
							if($nombre_columna!=""){$nombre_columna.=", ";}
							$nombre_columna.="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}//fin else

						$cont_vp_campos++;
					}//fin foreach
									

					$query_conteo_campos_cruzados="";
					$query_conteo_campos_cruzados.="SELECT  COUNT(*) as ocurrencias
					 from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					$complemento_query_where
					
					 ";
					//echo $query_conteo_campos_cruzados;
					$resultado_query_conteo_campos_cruzados=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campos_cruzados);

					$ocurrencias_encontradas=$resultado_query_conteo_campos_cruzados[0]['ocurrencias'];

					//echo print_r($resultado_query_conteo_campos_cruzados,true);

					$html_resultado_consulta="";

					$html_resultado_consulta.="<table class='table_class' >";
					$html_resultado_consulta.="<tr class='tr_class'><th colspan='100' class='th_class th_class2' style='text-align:center;'>Consulta Cruzada, Campos Involucrados: $complemento_query_campos_seleccionados_p2</th></tr>";

					$html_resultado_consulta.="<tr class='tr_class'>";
					$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$titulo_esquina_cruce</th>";
					$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$nombre_columna</th>";
					$html_resultado_consulta.="</tr>";

					$html_resultado_consulta.="<tr class='tr_class'>";
					$html_resultado_consulta.="<td class='th_class th_class2' style='text-align:center;'><b>$nombre_fila</b></td>";
					$html_resultado_consulta.="<td class='td_class' style='text-align:center;'>$ocurrencias_encontradas</td>";
					$html_resultado_consulta.="</tr>";

					$html_resultado_consulta.="</table>";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_resultado_consulta\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();

				}//fin if
				else if($selector_general_o_detallado_cross=="detallado")
				{
					$mensaje_inicial="Detallado con los registros que cumplan las condiciones de los campos cruzados con su valor permitido correspondiente. ";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();

					//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
					$ruta_escribir_archivo="";
					$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
					$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
					fwrite($handler_file, $mensaje_inicial);
					fclose($handler_file);
					//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS


					$numero_campo_base=$array_campos_cruzados_seleccionados[0];
					$numero_campo_secundario=$array_campos_cruzados_seleccionados[1];

					$complemento_query_campos_seleccionados_p1="";
					$complemento_query_campos_seleccionados_p2="";

					$complemento_query_where="";

					$listado_vp_para_los_campos_cruzados=array();

					$cont_vp_campos=0;

					$nombre_fila="";
					$nombre_columna="";

					$titulo_esquina_cruce="";

					foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
					{
						if($titulo_esquina_cruce!=""){$titulo_esquina_cruce.="-";}
						$titulo_esquina_cruce.=$numero_campo_cruzado;

						if($complemento_query_campos_seleccionados_p1!=""){$complemento_query_campos_seleccionados_p1.=",";}
						$complemento_query_campos_seleccionados_p1.="campo".$numero_campo_cruzado;

						

						$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]=lista_valores_permitidos_campo($numero_campo_cruzado,$coneccionBD);

						if($complemento_query_campos_seleccionados_p2!=""){$complemento_query_campos_seleccionados_p2.=", ";}
						$complemento_query_campos_seleccionados_p2.="Campo ".$numero_campo_cruzado." ".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['descripcion_campo'];

						$vp_campo_actual=$array_valores_permitidos_de_los_campos_seleccionados[$cont_vp_campos];
						$array_vp_campo_actual=explode("-", $vp_campo_actual);
						
						if($vp_campo_actual=="AAAA-MM-DD")
						{
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::date BETWEEN '1900-12-31' AND '$fecha_actual') ";
							
						}//fin if
						else if(count($array_vp_campo_actual)==2)
						{
							$vp1=$array_vp_campo_actual[0];
							$vp2=$array_vp_campo_actual[1];
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::numeric BETWEEN '$vp1' AND '$vp2') ";
						}//fin else if
						else
						{
							$complemento_query_where.=" AND campo".$numero_campo_cruzado."='".$vp_campo_actual."' ";
						}//fin else defecto

						if($key==0)
						{
							$nombre_fila="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}
						else
						{
							if($nombre_columna!=""){$nombre_columna.=", ";}
							$nombre_columna.="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}//fin else

						$cont_vp_campos++;
					}//fin foreach
									

					$query_conteo_campos_cruzados="";
					$query_conteo_campos_cruzados.="SELECT ";
					$cont_campos_identificadores=0;
					while($cont_campos_identificadores<11)
					{
						$query_conteo_campos_cruzados.=" campo".$cont_campos_identificadores.",";
						$titulos_campos[]="Campo ".$cont_campos_identificadores;
						$cont_campos_identificadores++;
					}//fin while
					$titulos_campos[]="Campo ".$numero_campo_base;
					$titulos_campos[]="Campo ".$numero_campo_secundario;
					$query_conteo_campos_cruzados.="  $complemento_query_campos_seleccionados_p1
					 from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					$complemento_query_where
					
					 ";
					//echo $query_conteo_campos_cruzados;
					$resultado_query_conteo_campos_cruzados=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campos_cruzados);

					$titulos_a_escribir=implode("|", $titulos_campos);
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$titulos_a_escribir);
					fclose($handler_file);

					$html_tabla="";
					if(is_array($resultado_query_conteo_campos_cruzados) && count($resultado_query_conteo_campos_cruzados) )
					{
						foreach ($resultado_query_conteo_campos_cruzados as $key => $registro_actual) 
						{
							$linea_a_escribir=implode("|", $registro_actual);
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
						}//fin foreach
										

					}//fin if
					$html_script_variable="";
					$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
					echo $html_script_variable;
					ob_flush();
					flush();

					$html_boton_descarga="";
					$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"Descargue el archivo con los registros detallados sobre el cruce.\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();
					
					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_boton_descarga\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();
					
					
				}//fin else if
			}//fin else if

		}//fin if no hay errores previos

		if($mensajes_error!="")
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_error\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_error\").innerHTML=\"Ocurrieron Errores En El Proceso\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
			
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_error\").innerHTML=\"$mensajes_error\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
		}//fin if mensajes error
		

	}//fin cruzada todas las ips
	else if($selector_rango_conteo=="cross_oneips")
	{
		$prestador_a_filtrar=trim($_REQUEST['prestador']);

		$selector_all_or_one_vp=trim($_REQUEST['selector_all_or_one_vp']);
		$selector_general_o_detallado_cross=trim($_REQUEST['selector_general_o_detallado_cross']);

		$cont_campos_cross=1;//ya que no existe el campo cross cero
		$array_campos_cruzados_seleccionados=array();
		$array_valores_permitidos_de_los_campos_seleccionados=array();
		$mensajes_error="";

		$numero_campo_1_sel_para_reasignar="";
		$numero_campo_2_sel_para_reasignar="";
		$vp_campo_1_sel_para_reasignar="";
		$vp_campo_2_sel_para_reasignar="";
		if(isset($_REQUEST['campocross_1'])==true)
		{
			$numero_campo_1_sel_para_reasignar=trim($_REQUEST['campocross_1']);
		}//fin if
		if(isset($_REQUEST['campocross_2'])==true)
		{
			$numero_campo_2_sel_para_reasignar=trim($_REQUEST['campocross_2']);
		}//fin if
		if(isset($_REQUEST['campocrossvp_1'])==true)
		{
			$vp_campo_1_sel_para_reasignar=trim($_REQUEST['campocrossvp_1']);
		}//fin if
		if(isset($_REQUEST['campocrossvp_2'])==true)
		{
			$vp_campo_2_sel_para_reasignar=trim($_REQUEST['campocrossvp_2']);
		}//fin if


		//parte re diligencia los campos con la información seleccionada
		$html_script_re_diligencia="";
		$html_script_re_diligencia.="
		<script>
		reasignar_valores_consulta_cruzada('$selector_all_or_one_vp','$selector_general_o_detallado_cross','$numero_campo_1_sel_para_reasignar','$numero_campo_2_sel_para_reasignar','$vp_campo_1_sel_para_reasignar','$vp_campo_2_sel_para_reasignar');
		</script>
		";
		echo $html_script_re_diligencia;
		ob_flush();
		flush();
		//fin parte re diligencia los campos con la información seleccionada

		if($prestador_a_filtrar=="" || $prestador_a_filtrar=="none")
		{
			$mensajes_error.="No se selecciono un prestador .<br>";
		}//fin if


		while(isset($_REQUEST['campocross_'.$cont_campos_cross])==true )
		{
			$campo_actual=trim($_REQUEST['campocross_'.$cont_campos_cross]);
			$valor_permitido_campo_actual="";

			if($campo_actual=="none" || $campo_actual=="")
			{
				$mensajes_error.="No se selecciono un valor para el selector de campo numero $cont_campos_cross .<br>";
			}//fin if

			if(
				in_array($campo_actual, $array_campos_cruzados_seleccionados)
				&& $campo_actual!="none" && $campo_actual!="" 
			)
			{
				$mensajes_error.="El valor  $campo_actual del selector de campo $cont_campos_cross numero fue seleccionado por el usuario mas de una vez.<br>";
			}//fin if

			$array_campos_cruzados_seleccionados[]=$campo_actual;
			
			if(isset($_REQUEST['campocrossvp_'.$cont_campos_cross])==true)
			{
				$valor_permitido_campo_actual=trim($_REQUEST['campocrossvp_'.$cont_campos_cross]);

				if($selector_all_or_one_vp=="specificvp" 
					&& ($valor_permitido_campo_actual=="" || $valor_permitido_campo_actual=="none") 
					)
				{
					$mensajes_error.="Debe seleccionar un valor permitido para el selector de campo $cont_campos_cross.<br>";
				}

				$array_valores_permitidos_de_los_campos_seleccionados[]=$valor_permitido_campo_actual;
			}//fin if agrega los valores permitidos encontrados
			
			$cont_campos_cross++;
		}//fin while

		//echo print_r($array_campos_cruzados_seleccionados,true)."<br>".print_r($array_valores_permitidos_de_los_campos_seleccionados,true)."<br>";

		if($mensajes_error=="")
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			//si no hay errores se procede a realizar la consulta cruzada entre los campos seleccionados
			//y dependiendo si se selecciono si se especifica valor permitido para los campos , es conteo, o detallado
			if($selector_all_or_one_vp=="allvp")
			{
				if($selector_general_o_detallado_cross=="conteo")
				{
					$numero_campo_base=$array_campos_cruzados_seleccionados[0];
					$complemento_query_campos_seleccionados_p1="";
					$complemento_query_campos_seleccionados_p2="";

					$listado_vp_para_los_campos_cruzados=array();

					foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
					{
						if($complemento_query_campos_seleccionados_p1!=""){$complemento_query_campos_seleccionados_p1.=",";}
						$complemento_query_campos_seleccionados_p1.="campo".$numero_campo_cruzado;

						

						$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]=lista_valores_permitidos_campo($numero_campo_cruzado,$coneccionBD);

						if($complemento_query_campos_seleccionados_p2!=""){$complemento_query_campos_seleccionados_p2.=", ";}
						$complemento_query_campos_seleccionados_p2.="Campo ".$numero_campo_cruzado." ".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['descripcion_campo'];
					}//fin foreach
									

					$query_conteo_campos_cruzados="";
					$query_conteo_campos_cruzados.="SELECT $complemento_query_campos_seleccionados_p1 , COUNT(*) as ocurrencias
					 from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					AND campo2='$prestador_a_filtrar'
					group by $complemento_query_campos_seleccionados_p1 ORDER BY campo".$numero_campo_base."
					 ";
					$resultado_query_conteo_campos_cruzados=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campos_cruzados);

					$matriz_organizadora=array();//esta matriz se encargara de organizar los valores apra la comparacion correspondiente

					$titulo_esquina_cruce="";

					foreach ($listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'] as $key => $valor_permitido_puro_campo_base) 
					{
						$array_ocurrencias_valor_permitido_campo_base=array();
						$acumulado_si_es_fecha_secundario=0;
						$acumulado_si_es_rango_secundario=0;
						$acumulado_si_es_fecha_ambos=0;
						$acumulado_si_es_rango_ambos=0;
						$acumulado_si_es_fecha_mixed=0;
						$acumulado_si_es_rango_mixed=0;
						foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
						{
							if($numero_campo_base!=$numero_campo_cruzado)
							{
								$titulo_esquina_cruce=$numero_campo_base."-".$numero_campo_cruzado;
								foreach ($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'] as $key => $valor_permitido_puro_campo_secundario) 
								{
									$se_encontro_numero_ocurrencias=false;
									$numero_ocurrencias="";

									//PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO
									//BASE
									$es_formato_fecha_campo_cruzado_base=false;
									$es_rango_campo_cruzado_base=false;
									
									$valor_campo=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if( $valor_campo=="AAAA-MM-DD"
										&& $valor_campo2!="AAAA-MM-DD"
										)
									{
										$es_formato_fecha_campo_cruzado_base=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2
										&& count($array_es_fecha_o_rango2)!=2
									 )
									{
										$es_rango_campo_cruzado_base=true;
									}//fin else if
									//FIN BASE

									//SECUNDARIO
									$es_formato_fecha_campo_cruzado_secundario=false;
									$es_rango_campo_cruzado_secundario=false;

									$valor_campo=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if($valor_campo=="AAAA-MM-DD"
										&& $valor_campo2!="AAAA-MM-DD"
										)
									{
										$es_formato_fecha_campo_cruzado_secundario=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2
										&& count($array_es_fecha_o_rango2)!=2
										)
									{
										$es_rango_campo_cruzado_secundario=true;
									}//fin else if
									//FIN SECUNDARIO


									//AMBOS
									$es_formato_fecha_campo_cruzado_ambos=false;
									$es_rango_campo_cruzado_ambos=false;
									
									$valor_campo=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if($valor_campo=="AAAA-MM-DD"
										&& $valor_campo2=="AAAA-MM-DD"
										)
									{
										$es_formato_fecha_campo_cruzado_ambos=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2 
										&& count($array_es_fecha_o_rango2)==2 
										)
									{
										$es_rango_campo_cruzado_ambos=true;
									}//fin else if
									//FIN AMBOS

									//AMBOS MIXED
									$es_formato_fecha_campo_cruzado_mixed=false;
									$es_rango_campo_cruzado_mixed=false;
									
									$valor_campo=$valor_permitido_puro_campo_base;
									$array_es_fecha_o_rango=explode("-", $valor_campo);
									$valor_campo2=$valor_permitido_puro_campo_secundario;
									$array_es_fecha_o_rango2=explode("-", $valor_campo2);
									if($valor_campo=="AAAA-MM-DD"
										&& count($array_es_fecha_o_rango2)==2
										
										)
									{
										$es_formato_fecha_campo_cruzado_mixed=true;
									}//fin if
									else if(count($array_es_fecha_o_rango)==2 
										&& $valor_campo2=="AAAA-MM-DD"
										)
									{
										$es_rango_campo_cruzado_mixed=true;
									}//fin else if
									//FIN AMBOS MIXED

									
									//FIN PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO

									foreach ($resultado_query_conteo_campos_cruzados as $key => $resultado_cruce) 
									{
										//PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO
										//BASE
										$es_formato_fecha_campo_cruzado_base_res=false;
										$es_rango_campo_cruzado_base_res=false;										
										
										$valor_campo=$resultado_cruce['campo'.$numero_campo_base];
										$array_es_fecha_o_rango=explode("-", $valor_campo);
										
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10 
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false
											&& $resultado_cruce['campo'.$numero_campo_cruzado]==$valor_permitido_puro_campo_secundario
											)
										{											
											$es_formato_fecha_campo_cruzado_base_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false 
											&& $resultado_cruce['campo'.$numero_campo_cruzado]==$valor_permitido_puro_campo_secundario
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											$es_rango_campo_cruzado_base_res=true;
										}//fin else if
										//FIN BASE
										
										//SECUNDARIO
										$es_formato_fecha_campo_cruzado_secundario_res=false;
										$es_rango_campo_cruzado_secundario_res=false;

										$valor_campo=$resultado_cruce['campo'.$numero_campo_cruzado];
										$array_es_fecha_o_rango=explode("-", $valor_campo);										
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											&& $resultado_cruce['campo'.$numero_campo_base]==$valor_permitido_puro_campo_base
											)
										{
											$es_formato_fecha_campo_cruzado_secundario_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											&& $resultado_cruce['campo'.$numero_campo_base]==$valor_permitido_puro_campo_base
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											$es_rango_campo_cruzado_secundario_res=true;
										}//fin else if
										//FIN SECUNDARIO

										//AMBOS
										$es_formato_fecha_campo_cruzado_ambos_res=false;
										$es_rango_campo_cruzado_ambos_res=false;										
										
										$valor_campo=$resultado_cruce['campo'.$numero_campo_base];
										$array_es_fecha_o_rango=explode("-", $valor_campo);

										$valor_campo2=$resultado_cruce['campo'.$numero_campo_cruzado];
										$array_es_fecha_o_rango2=explode("-", $valor_campo2);

																			
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10 
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false
											
											&& count($array_es_fecha_o_rango2)==3 && strlen($valor_campo2)==10
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											)
										{	
											//echo "pre fecha v1 $valor_campo v2 $valor_campo2<br>";									
											$es_formato_fecha_campo_cruzado_ambos_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false 
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											&& count($array_es_fecha_o_rango)!=3
											&& count($array_es_fecha_o_rango2)!=3
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											//echo "pre rango v1 $valor_campo v2 $valor_campo2<br>";
											$es_rango_campo_cruzado_ambos_res=true;
										}//fin else if
										//FIN AMBOS

										//MIXED
										$es_formato_fecha_campo_cruzado_mixed_res=false;
										$es_rango_campo_cruzado_mixed_res=false;										
										
										$valor_campo=$resultado_cruce['campo'.$numero_campo_base];
										$array_es_fecha_o_rango=explode("-", $valor_campo);

										$valor_campo2=$resultado_cruce['campo'.$numero_campo_cruzado];
										$array_es_fecha_o_rango2=explode("-", $valor_campo2);

																			
										if(count($array_es_fecha_o_rango)==3 && strlen($valor_campo)==10 
											&& in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false
											
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 											
											)
										{	
											//echo "pre fecha v1 $valor_campo v2 $valor_campo2<br>";									
											$es_formato_fecha_campo_cruzado_mixed_res=true;
										}//fin if
										else if(in_array($valor_campo, $listado_vp_para_los_campos_cruzados[$numero_campo_base]['valores_permitidos_puros'])==false 
											
											&& count($array_es_fecha_o_rango2)==3 && strlen($valor_campo2)==10
											&& in_array($valor_campo2, $listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'])==false 
											)//aqui no puede ir count($array_es_fecha_o_rango)==2 debido a que el valor que esta almacenado es un numero
										{
											//echo "pre rango v1 $valor_campo v2 $valor_campo2<br>";
											$es_rango_campo_cruzado_mixed_res=true;
										}//fin else if
										//FIN MIXED

										//FIN PARTE VERIFICA SI EL VALOR PERMITIDO ES FECHA CALENDARIO MAYOR A 1900 O UN RANGO

										if($resultado_cruce['campo'.$numero_campo_base]==$valor_permitido_puro_campo_base
											&& $resultado_cruce['campo'.$numero_campo_cruzado]==$valor_permitido_puro_campo_secundario
											)
										{
											$se_encontro_numero_ocurrencias=true;
											$numero_ocurrencias=$resultado_cruce['ocurrencias'];
										}//fin if										
										else if(
											($es_formato_fecha_campo_cruzado_secundario_res==true 
											|| $es_formato_fecha_campo_cruzado_base_res==true)
											&& $es_formato_fecha_campo_cruzado_ambos_res==false 
											&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO FECHA C res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario ".print_r($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'],true)."<br>";
											$acumulado_si_es_fecha_secundario+=intval($resultado_cruce['ocurrencias']);
										}//fin else if
										else if(
											($es_rango_campo_cruzado_secundario_res==true 
											|| $es_rango_campo_cruzado_base_res==true)
											&& $es_rango_campo_cruzado_ambos_res==false
											&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO RANGO C res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario<br>";
											$acumulado_si_es_rango_secundario+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_rango_secundario ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										else if($es_formato_fecha_campo_cruzado_ambos_res==true 
											//&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO FECHA A res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario ".print_r($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'],true)."<br>";
											$acumulado_si_es_fecha_ambos+=intval($resultado_cruce['ocurrencias']);
										}//fin else if
										else if($es_rango_campo_cruzado_ambos_res==true 
											//&& ($es_formato_fecha_campo_cruzado_mixed_res==false && $es_rango_campo_cruzado_mixed_res==false )
											)
										{
											//echo "ENTRO RANGO A res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario<br>";
											$acumulado_si_es_rango_ambos+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_rango_ambos ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										else if($es_formato_fecha_campo_cruzado_mixed_res==true )
										{
											//echo "ENTRO FECHA res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario ".print_r($listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos_puros'],true)."<br>";
											$acumulado_si_es_fecha_mixed+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_fecha_mixed ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										else if($es_rango_campo_cruzado_mixed_res==true )
										{
											//echo "ENTRO RANGO res_cb: ".$resultado_cruce['campo'.$numero_campo_base]."  res_cc: ".$resultado_cruce['campo'.$numero_campo_cruzado]." vpp_cb: $valor_permitido_puro_campo_base vpp_cc: $valor_permitido_puro_campo_secundario<br>";
											$acumulado_si_es_rango_mixed+=intval($resultado_cruce['ocurrencias']);
											//echo "acumulado $acumulado_si_es_rango_mixed ".intval($resultado_cruce['ocurrencias'])."<br>";
										}//fin else if
										
									}//fin foreach
									if($se_encontro_numero_ocurrencias==false)
									{
										$numero_ocurrencias=0;
									}//fin if

									$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$numero_ocurrencias;
										
														
									if(
										($es_formato_fecha_campo_cruzado_secundario==true 
										|| $es_formato_fecha_campo_cruzado_base==true )
										&& $es_formato_fecha_campo_cruzado_ambos==false
										&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de fechas
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_fecha_secundario;
										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila fcc $acumulado_si_es_fecha_secundario<br>";

										$acumulado_si_es_fecha_secundario=0;
										
									}//fin if
									else if(
										($es_rango_campo_cruzado_secundario==true 
										|| $es_rango_campo_cruzado_base==true)
										&& $es_rango_campo_cruzado_ambos==false
										&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de rangos
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_rango_secundario;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila rcc $acumulado_si_es_rango_secundario<br>";

										$acumulado_si_es_rango_secundario=0;
										
									}//fin if
									else if(
										$es_formato_fecha_campo_cruzado_ambos==true
										//&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de fechas
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_fecha_ambos;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila fa $acumulado_si_es_fecha_ambos<br>";

										$acumulado_si_es_fecha_ambos=0;
										
									}//fin if
									else if(
										$es_rango_campo_cruzado_ambos==true
										//&& ($es_formato_fecha_campo_cruzado_mixed==false && $es_rango_campo_cruzado_mixed==false)
										)
									{
										//se reasigna con el acumulado de rangos
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_rango_ambos;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila ra $acumulado_si_es_rango_ambos<br>";

										$acumulado_si_es_rango_ambos=0;
										
									}//fin if
									else if(
										$es_formato_fecha_campo_cruzado_mixed==true
										)
									{
										//se reasigna con el acumulado de fechas
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_fecha_mixed;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila fm $acumulado_si_es_fecha_mixed<br>";

										$acumulado_si_es_fecha_mixed=0;
									}//fin if
									else if(
										$es_rango_campo_cruzado_mixed==true
										)
									{
										//se reasigna con el acumulado de rangos
										$array_ocurrencias_valor_permitido_campo_base[$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario]=$acumulado_si_es_rango_mixed;

										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila rm $acumulado_si_es_rango_mixed<br>";

										$acumulado_si_es_rango_mixed=0;
										
									}//fin if
									else
									{
										//echo "asigno a posicion ".$numero_campo_cruzado."_".$valor_permitido_puro_campo_secundario." de array fila defecto $numero_ocurrencias<br>";
									}
									
									
									
								}//fin foreach
							}//fin if
						}//fin foreach 
						$matriz_organizadora[$numero_campo_base."_".$valor_permitido_puro_campo_base]=$array_ocurrencias_valor_permitido_campo_base;
					}//fin foreach

					

					foreach ($resultado_query_conteo_campos_cruzados as $key => $resultado_cruce) 
					{
						//echo print_r($resultado_cruce,true)."<br>";

					}//fin imprime array resultado  para debug

					$html_resultado_consulta="";

					$html_resultado_consulta.="<table class='table_class' >";
					$html_resultado_consulta.="<tr class='tr_class'><th colspan='100' class='th_class th_class2' style='text-align:center;'>Consulta Cruzada, Campos Involucrados: $complemento_query_campos_seleccionados_p2</th></tr>";

					$nombres_filas=array_keys($matriz_organizadora);
					$escribe_titulos_columnas=true;

					$cont_filas=0;

					foreach ($matriz_organizadora as $key => $filas_vp) 
					{
						$nombres_columnas=array_keys($filas_vp);
						if($escribe_titulos_columnas==true)
						{
							$html_resultado_consulta.="<tr class='tr_class'>";
							$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$titulo_esquina_cruce</th>";
							foreach ($nombres_columnas as $key => $titulo_columna)
							{
								$array_titulo_columna=explode("_", $titulo_columna);
								$campo_titulo_col=$array_titulo_columna[0];
								$valor_permitido_campo_titulo_col=$array_titulo_columna[1];
								$descripcion=$listado_vp_para_los_campos_cruzados[$campo_titulo_col]['valores_permitidos'][$valor_permitido_campo_titulo_col];
								$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>Campo $campo_titulo_col: $descripcion</th>";
							}//fin foreach
							$html_resultado_consulta.="</tr>";
							$escribe_titulos_columnas=false;
						}//fin if escribe titulos columnas


						$html_resultado_consulta.="<tr class='tr_class'>";

						$array_titulo_fila=explode("_", $nombres_filas[$cont_filas]);
						$campo_titulo_fila=$array_titulo_fila[0];
						$valor_permitido_campo_titulo_fila=$array_titulo_fila[1];
						$descripcion=$listado_vp_para_los_campos_cruzados[$campo_titulo_fila]['valores_permitidos'][$valor_permitido_campo_titulo_fila];
						$html_resultado_consulta.="<td class='th_class th_class2' style='text-align:center;'><b>Campo $campo_titulo_fila: $descripcion </b></td>";
						//escribe columnas
						foreach ($filas_vp as $key => $columnas_vp) 
						{
							$html_resultado_consulta.="<td class='td_class' style='text-align:center;'>$columnas_vp</td>";
						}//fin foreach

						$html_resultado_consulta.="</tr>";

						$cont_filas++;
					}//fin foreach					
					$html_resultado_consulta.="</table>";

					
					//echo print_r($listado_vp_para_los_campos_cruzados,true)."<br>";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_resultado_consulta\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();
					

				}//fin if
				else if($selector_general_o_detallado_cross=="detallado")
				{
					//no hay para cuando son todos los valores permitidos
				}//fin else if

			}//fin if
			else if($selector_all_or_one_vp=="specificvp")
			{
				if($selector_general_o_detallado_cross=="conteo")
				{
					$numero_campo_base=$array_campos_cruzados_seleccionados[0];
					$complemento_query_campos_seleccionados_p1="";
					$complemento_query_campos_seleccionados_p2="";

					$complemento_query_where="";

					$listado_vp_para_los_campos_cruzados=array();

					$cont_vp_campos=0;

					$nombre_fila="";
					$nombre_columna="";

					$titulo_esquina_cruce="";

					foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
					{
						if($titulo_esquina_cruce!=""){$titulo_esquina_cruce.="-";}
						$titulo_esquina_cruce.=$numero_campo_cruzado;

						if($complemento_query_campos_seleccionados_p1!=""){$complemento_query_campos_seleccionados_p1.=",";}
						$complemento_query_campos_seleccionados_p1.="campo".$numero_campo_cruzado;

						

						$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]=lista_valores_permitidos_campo($numero_campo_cruzado,$coneccionBD);

						if($complemento_query_campos_seleccionados_p2!=""){$complemento_query_campos_seleccionados_p2.=", ";}
						$complemento_query_campos_seleccionados_p2.="Campo ".$numero_campo_cruzado." ".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['descripcion_campo'];

						$vp_campo_actual=$array_valores_permitidos_de_los_campos_seleccionados[$cont_vp_campos];
						$array_vp_campo_actual=explode("-", $vp_campo_actual);
						
						if($vp_campo_actual=="AAAA-MM-DD")
						{
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::date BETWEEN '1900-12-31' AND '$fecha_actual') ";
							
						}//fin if
						else if(count($array_vp_campo_actual)==2)
						{
							$vp1=$array_vp_campo_actual[0];
							$vp2=$array_vp_campo_actual[1];
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::numeric BETWEEN '$vp1' AND '$vp2') ";
						}//fin else if
						else
						{
							$complemento_query_where.=" AND campo".$numero_campo_cruzado."='".$vp_campo_actual."' ";
						}//fin else defecto

						if($key==0)
						{
							$nombre_fila="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}
						else
						{
							if($nombre_columna!=""){$nombre_columna.=", ";}
							$nombre_columna.="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}//fin else

						$cont_vp_campos++;
					}//fin foreach
									

					$query_conteo_campos_cruzados="";
					$query_conteo_campos_cruzados.="SELECT  COUNT(*) as ocurrencias
					 from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					AND campo2='$prestador_a_filtrar'
					$complemento_query_where
					
					 ";
					//echo $query_conteo_campos_cruzados;
					$resultado_query_conteo_campos_cruzados=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campos_cruzados);

					$ocurrencias_encontradas=$resultado_query_conteo_campos_cruzados[0]['ocurrencias'];

					//echo print_r($resultado_query_conteo_campos_cruzados,true);

					$html_resultado_consulta="";

					$html_resultado_consulta.="<table class='table_class' >";
					$html_resultado_consulta.="<tr class='tr_class'><th colspan='100' class='th_class th_class2' style='text-align:center;'>Consulta Cruzada para el prestador $prestador_a_filtrar, Campos Involucrados: $complemento_query_campos_seleccionados_p2</th></tr>";

					$html_resultado_consulta.="<tr class='tr_class'>";
					$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$titulo_esquina_cruce</th>";
					$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$nombre_columna</th>";
					$html_resultado_consulta.="</tr>";

					$html_resultado_consulta.="<tr class='tr_class'>";
					$html_resultado_consulta.="<td class='th_class th_class2' style='text-align:center;'><b>$nombre_fila</b></td>";
					$html_resultado_consulta.="<td class='td_class' style='text-align:center;'>$ocurrencias_encontradas</td>";
					$html_resultado_consulta.="</tr>";

					$html_resultado_consulta.="</table>";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_resultado_consulta\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();

				}//fin if
				else if($selector_general_o_detallado_cross=="detallado")
				{
					

					$mensaje_inicial="Detallado con los registros que cumplan las condiciones de los campos cruzados con su valor permitido correspondiente. ";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"$mensaje_inicial\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();

					//CREA ARCHIVO PARA LOS REGISTROS DETALLADOS
					$ruta_escribir_archivo="";
					$ruta_escribir_archivo=$rutaTemporal."resultado_detallado"."_".$fecha_para_archivo.".csv";
					$handler_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
					fwrite($handler_file, $mensaje_inicial);
					fclose($handler_file);
					//FIN CREA ARCHIVO PARA LOS REGISTROS DETALLADOS


					$numero_campo_base=$array_campos_cruzados_seleccionados[0];
					$numero_campo_secundario=$array_campos_cruzados_seleccionados[1];

					$complemento_query_campos_seleccionados_p1="";
					$complemento_query_campos_seleccionados_p2="";

					$complemento_query_where="";

					$listado_vp_para_los_campos_cruzados=array();

					$cont_vp_campos=0;

					$nombre_fila="";
					$nombre_columna="";

					$titulo_esquina_cruce="";

					foreach ($array_campos_cruzados_seleccionados as $key => $numero_campo_cruzado) 
					{
						if($titulo_esquina_cruce!=""){$titulo_esquina_cruce.="-";}
						$titulo_esquina_cruce.=$numero_campo_cruzado;

						if($complemento_query_campos_seleccionados_p1!=""){$complemento_query_campos_seleccionados_p1.=",";}
						$complemento_query_campos_seleccionados_p1.="campo".$numero_campo_cruzado;

						

						$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]=lista_valores_permitidos_campo($numero_campo_cruzado,$coneccionBD);

						if($complemento_query_campos_seleccionados_p2!=""){$complemento_query_campos_seleccionados_p2.=", ";}
						$complemento_query_campos_seleccionados_p2.="Campo ".$numero_campo_cruzado." ".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['descripcion_campo'];

						$vp_campo_actual=$array_valores_permitidos_de_los_campos_seleccionados[$cont_vp_campos];
						$array_vp_campo_actual=explode("-", $vp_campo_actual);
						
						if($vp_campo_actual=="AAAA-MM-DD")
						{
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::date BETWEEN '1900-12-31' AND '$fecha_actual') ";
							
						}//fin if
						else if(count($array_vp_campo_actual)==2)
						{
							$vp1=$array_vp_campo_actual[0];
							$vp2=$array_vp_campo_actual[1];
							$complemento_query_where.=" AND (campo".$numero_campo_cruzado."::numeric BETWEEN '$vp1' AND '$vp2') ";
						}//fin else if
						else
						{
							$complemento_query_where.=" AND campo".$numero_campo_cruzado."='".$vp_campo_actual."' ";
						}//fin else defecto

						if($key==0)
						{
							$nombre_fila="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}
						else
						{
							if($nombre_columna!=""){$nombre_columna.=", ";}
							$nombre_columna.="Campo $numero_campo_cruzado (".$listado_vp_para_los_campos_cruzados[$numero_campo_cruzado]['valores_permitidos'][$vp_campo_actual].")";
						}//fin else

						$cont_vp_campos++;
					}//fin foreach
									

					$query_conteo_campos_cruzados="";
					$query_conteo_campos_cruzados.="SELECT ";
					$cont_campos_identificadores=0;
					while($cont_campos_identificadores<11)
					{
						$query_conteo_campos_cruzados.=" campo".$cont_campos_identificadores.",";
						$titulos_campos[]="Campo ".$cont_campos_identificadores;
						$cont_campos_identificadores++;
					}//fin while
					$titulos_campos[]="Campo ".$numero_campo_base;
					$titulos_campos[]="Campo ".$numero_campo_secundario;
					$query_conteo_campos_cruzados.="  $complemento_query_campos_seleccionados_p1
					 from gioss_archivo_para_analisis_2463  
					WHERE 
					nombre_archivo='$nombre_archivo_identificador'
					AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
					AND fecha_de_corte='$fecha_de_corte_identificador'
					AND campo2='$prestador_a_filtrar'
					$complemento_query_where
					
					 ";
					//echo $query_conteo_campos_cruzados;
					$resultado_query_conteo_campos_cruzados=$coneccionBD->consultar2_no_crea_cierra($query_conteo_campos_cruzados);

					$titulos_a_escribir=implode("|", $titulos_campos);
					$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
					fwrite($handler_file, "\n".$titulos_a_escribir);
					fclose($handler_file);

					$html_tabla="";
					if(is_array($resultado_query_conteo_campos_cruzados) && count($resultado_query_conteo_campos_cruzados) )
					{
						foreach ($resultado_query_conteo_campos_cruzados as $key => $registro_actual) 
						{
							$linea_a_escribir=implode("|", $registro_actual);
							$handler_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							fwrite($handler_file, "\n".$linea_a_escribir);
							fclose($handler_file);
						}//fin foreach
										

					}//fin if
					$html_script_variable="";
					$html_script_variable="<script>var ruta_archivo_descarga='$ruta_escribir_archivo';</script>";
					echo $html_script_variable;
					ob_flush();
					flush();

					$html_boton_descarga="";
					$html_boton_descarga.="<input type='button' value='Descargar resultado consulta' id='Descargar' class='btn btn-success color_boton' onclick='download(ruta_archivo_descarga);' />  ";

					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_exito\").innerHTML=\"Descargue el archivo con los registros detallados sobre el cruce para el prestador $prestador_a_filtrar.\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();
					
					$html_javascript_resultado="";
					$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_boton_descarga\";</script>";
					echo $html_javascript_resultado;
					ob_flush();
					flush();
					
				}//fin else if

			}//fin else if

		}//fin if no hay errores previos

		if($mensajes_error!="")
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_error\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"titulo_mensaje_error\").innerHTML=\"Ocurrieron Errores En El Proceso\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
			
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_error\").innerHTML=\"$mensajes_error\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
		}//fin if mensajes error

	}//fin cruzada ips especifica
	*/

}//fin if

$coneccionBD->cerrar_conexion();
?>