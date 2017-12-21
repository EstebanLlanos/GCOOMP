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
$sql_prestadores_asociados_al_archivo="SELECT * FROM gioss_archivo_para_analisis_4505 
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

$sql_query_valores_permitidos="SELECT * FROM valores_permitidos_4505 order by numero_campo_norma::numeric asc; ";
$resultado_query_valores_permitidos=$coneccionBD->consultar2_no_crea_cierra($sql_query_valores_permitidos);

$opciones_selector_campos="";

if(is_array($resultado_query_valores_permitidos) && count($resultado_query_valores_permitidos)>0 )
{
	$cont_c1=0;
	while($cont_c1<count($resultado_query_valores_permitidos) && $cont_c1<119)
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
$query_archivos_subidos_para_analisis.=" SELECT * FROM gioss_indice_archivo_para_analisis_4505 ORDER BY fecha_y_hora_validacion asc ; ";
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
$smarty->display('consulta_res_prest_val4505.html.tpl');

/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('217','91','Resul. Validacion Por IPS','',FALSE,'..|consulta_res_prest_val4505|consulta_res_prest_val4505.php','33.03');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('217','5'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('217','4'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('217','3'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('217','2'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('217','1'); 
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



	
	if($selector_rango_conteo=="clone")
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
		 from gioss_archivo_para_analisis_4505  
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
			 from gioss_archivo_para_analisis_4505  
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
	else if($selector_rango_conteo=="res_allips")
	{
		
		$mensajes_error="";

		if($mensajes_error=="" )
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$query_res_prest_en_archivo="";
			$query_res_prest_en_archivo.="SELECT  *
			 from gioss_indexador_para_reporte_ips  
			WHERE 
			nombre_archivo='$nombre_archivo_identificador'
			AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
			AND fecha_de_corte='$fecha_de_corte_identificador'
			
			 ";
			//echo $query_res_prest_en_archivo."<br>";
			$resultado_query_res_prest_en_archivo=$coneccionBD->consultar2_no_crea_cierra($query_res_prest_en_archivo);

			

			//echo print_r($resultado_query_res_prest_en_archivo,true)."<br>";

			$array_titulos=array();

			if(count($resultado_query_res_prest_en_archivo)>0 
				&& is_array($resultado_query_res_prest_en_archivo)==true
				)
			{
				$array_titulos=array_keys($resultado_query_res_prest_en_archivo[0]);
			}//fin if
			$array_titulos[]="Ratio Calidad";

			//echo print_r($array_titulos,true)."<br>";

			//PARTE TABLA PARA RESULTADO
			$num_columna_desde_la_cual_se_empieza_a_mostrar=6;
			$html_resultado_consulta="";
			
			$html_resultado_consulta.="<table class='table_class' >";
			$html_resultado_consulta.="<tr class='tr_class'><th colspan='100' class='th_class th_class2' style='text-align:center;'>Consulta Resultados Prestadores Reportantes En Validacion Archivo</th></tr>";

			$html_resultado_consulta.="<tr class='tr_class'>";
			$cont_titulos=0;
			foreach ($array_titulos as $key => $titulo_col) 
			{
				if($cont_titulos>$num_columna_desde_la_cual_se_empieza_a_mostrar)
				{
					$titulo_col_procesado=str_replace("_", " ", $titulo_col);
					$titulo_col_procesado=ucwords($titulo_col_procesado);
					$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$titulo_col_procesado</th>";
				}//fin if
				$cont_titulos++;
			}//fin if
			$html_resultado_consulta.="</tr>";
			
			foreach ($resultado_query_res_prest_en_archivo as $key => $fila) 
			{
				$html_resultado_consulta.="<tr class='tr_class'>";
				$cont_cols=0;
				foreach ($fila as $key => $col) 
				{
					if($cont_cols>$num_columna_desde_la_cual_se_empieza_a_mostrar)
					{
						$html_resultado_consulta.="<td class='td_class' style='text-align:center;'>$col</td>";
					}
					$cont_cols++;
				}//fin foreach

				//columna calculada ratio
				$ratio_calidad=0;
				if(intval($fila['cantidad_lineas_en_archivo'])!=0)
				{
					$ratio_calidad=(intval($fila['cantidad_lineas_correctas_en_archivo'])*100 )/intval($fila['cantidad_lineas_en_archivo']);
					$ratio_calidad=round($ratio_calidad, 2, PHP_ROUND_HALF_UP); 
				}//fin if diferente de  cero
				$complemento_style="";
				if($ratio_calidad<50)
				{
					$complemento_style.="background-color:#aa1212 !important;color: white;";
				}//fin if
				else if($ratio_calidad<75)
				{
					$complemento_style.="background-color:#f94343 !important;color: black;";
				}//fin if
				else if($ratio_calidad<80)
				{
					$complemento_style.="background-color:#fcde32 !important;color: black;";
				}//fin if
				else if($ratio_calidad<90)
				{
					$complemento_style.="background-color:#4286f4 !important;color: black;";
				}//fin if
				else if($ratio_calidad<100)
				{
					$complemento_style.="background-color:#c4f441 !important;color: black;";
				}//fin if
				$html_resultado_consulta.="<td class='td_class' style='text-align:center;$complemento_style'><b>$ratio_calidad %</b></td>";
				//fin columna calculada ratio
				
				$html_resultado_consulta.="</tr>";
			}//fin foreach
			

			$html_resultado_consulta.="</table>";
			
			//FIN PARTE TABLA PARA RESULTADO

			

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_resultado_consulta\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

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
	else if($selector_rango_conteo=="res_oneips")
	{
		$prestador_a_filtrar=trim($_REQUEST['prestador']);

		$mensajes_error="";

		if($mensajes_error=="" )
		{
			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"div_mensaje_exito\").style.display=\"block\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();

			$query_res_prest_en_archivo="";
			$query_res_prest_en_archivo.="SELECT  *
			 from gioss_indexador_para_reporte_ips  
			WHERE 
			nombre_archivo='$nombre_archivo_identificador'
			AND fecha_y_hora_validacion='$fecha_y_hora_validacion_identificador'
			AND fecha_de_corte='$fecha_de_corte_identificador'
			AND prestador_en_archivo='$prestador_a_filtrar'
			
			 ";
			//echo $query_res_prest_en_archivo."<br>";
			$resultado_query_res_prest_en_archivo=$coneccionBD->consultar2_no_crea_cierra($query_res_prest_en_archivo);

			

			//echo print_r($resultado_query_res_prest_en_archivo,true)."<br>";

			$array_titulos=array();

			if(count($resultado_query_res_prest_en_archivo)>0 
				&& is_array($resultado_query_res_prest_en_archivo)==true
				)
			{
				$array_titulos=array_keys($resultado_query_res_prest_en_archivo[0]);
			}//fin if
			$array_titulos[]="Ratio Calidad";

			//echo print_r($array_titulos,true)."<br>";

			//PARTE TABLA PARA RESULTADO
			$num_columna_desde_la_cual_se_empieza_a_mostrar=6;
			$html_resultado_consulta="";
			
			$html_resultado_consulta.="<table class='table_class' >";
			$html_resultado_consulta.="<tr class='tr_class'><th colspan='100' class='th_class th_class2' style='text-align:center;'>Consulta Resultados Prestadores Reportantes En Validacion Archivo Para Prestador $prestador_a_filtrar</th></tr>";

			$html_resultado_consulta.="<tr class='tr_class'>";
			$cont_titulos=0;
			foreach ($array_titulos as $key => $titulo_col) 
			{
				if($cont_titulos>$num_columna_desde_la_cual_se_empieza_a_mostrar)
				{
					$titulo_col_procesado=str_replace("_", " ", $titulo_col);
					$titulo_col_procesado=ucwords($titulo_col_procesado);
					$html_resultado_consulta.="<th class='th_class th_class2' style='text-align:center;'>$titulo_col_procesado</th>";
				}//fin if
				$cont_titulos++;
			}//fin if
			$html_resultado_consulta.="</tr>";
			
			foreach ($resultado_query_res_prest_en_archivo as $key => $fila) 
			{
				$html_resultado_consulta.="<tr class='tr_class'>";
				$cont_cols=0;
				foreach ($fila as $key => $col) 
				{
					if($cont_cols>$num_columna_desde_la_cual_se_empieza_a_mostrar)
					{
						$html_resultado_consulta.="<td class='td_class' style='text-align:center;'>$col</td>";
					}
					$cont_cols++;
				}//fin foreach

				//columna calculada ratio
				$ratio_calidad=0;
				if(intval($fila['cantidad_lineas_en_archivo'])!=0)
				{
					$ratio_calidad=(intval($fila['cantidad_lineas_correctas_en_archivo'])*100 )/intval($fila['cantidad_lineas_en_archivo']);
					$ratio_calidad=round($ratio_calidad, 2, PHP_ROUND_HALF_UP); 
				}//fin if diferente de  cero
				$complemento_style="";
				if($ratio_calidad<50)
				{
					$complemento_style.="background-color:#aa1212 !important;color: white;";
				}//fin if
				else if($ratio_calidad<75)
				{
					$complemento_style.="background-color:#f94343 !important;color: black;";
				}//fin if
				else if($ratio_calidad<80)
				{
					$complemento_style.="background-color:#fcde32 !important;color: black;";
				}//fin if
				else if($ratio_calidad<90)
				{
					$complemento_style.="background-color:#4286f4 !important;color: black;";
				}//fin if
				else if($ratio_calidad<100)
				{
					$complemento_style.="background-color:#c4f441 !important;color: black;";
				}//fin if
				$html_resultado_consulta.="<td class='td_class' style='text-align:center;$complemento_style'><b>$ratio_calidad %</b></td>";
				//fin columna calculada ratio
				
				$html_resultado_consulta.="</tr>";
			}//fin foreach
			

			$html_resultado_consulta.="</table>";
			
			//FIN PARTE TABLA PARA RESULTADO

			

			$html_javascript_resultado="";
			$html_javascript_resultado.= "<script>document.getElementById(\"parrafo_mensaje_exito\").innerHTML=\"$html_resultado_consulta\";</script>";
			echo $html_javascript_resultado;
			ob_flush();
			flush();
			


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


}//fin if

$coneccionBD->cerrar_conexion();
?>