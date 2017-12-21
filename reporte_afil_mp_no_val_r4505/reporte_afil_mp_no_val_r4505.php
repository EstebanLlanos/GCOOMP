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


$mensaje_perm_estado_reg_recuperados="";
$mensaje_perm_est_final_reg_recuperado="";
$mensaje_perm_tablas_recuperadas="";
$cont_porcentaje_file=0;
$numero_total_registros_tablas=0;

$resultadoDefinitivo="";
$mensajes_error_bd="";

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('reporte_afil_mp_no_val_r4505.html.tpl');

//INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('212','10','cambiar_aqui','',FALSE,'..|cambiar_aqui|cambiar_aqui.php','33.02');

//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','5');
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','4');
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('212','2');

if(isset($_POST["accion"])
   && $_POST["accion"]!=""
   )
{
	//esta query permite consultar los afiliados que no fueron validados  como registros exitosos o no existen en ninguna validacion
	$query_afiliados_no_validados=" 
	(
		(
			SELECT gafmp.* as identificacion FROM gioss_afiliados_eapb_mp gafmp
			LEFT JOIN gios_datos_validados_exito_r4505 vb ON (gafmp.tipo_id_afiliado || ' ' || gafmp.id_afiliado) =  (vb.campo3 || ' ' || vb.campo4)
			WHERE  (vb.campo3 || ' ' || vb.campo4) IS NULL
		)

	intersect 

		(
			SELECT gafmp.* as identificacion FROM gioss_afiliados_eapb_mp gafmp 
			LEFT JOIN gios_datos_rechazados_r4505 rb ON (gafmp.tipo_id_afiliado || ' ' || gafmp.id_afiliado) = (rb.campo3 || ' ' || rb.campo4)
			WHERE (rb.campo3 || ' ' || rb.campo4) IS NULL
		)
	)

	union 
	(
		SELECT gafmp.* as identificacion FROM gioss_afiliados_eapb_mp gafmp 
		INNER JOIN gios_datos_rechazados_r4505 rb ON (gafmp.tipo_id_afiliado || ' ' || gafmp.id_afiliado) = (rb.campo3 || ' ' || rb.campo4)
		where rb.estado_registro='2'
	)
	
	";
	


	
	
	
	$array_rutas_archivos_generados=array();
	date_default_timezone_set ("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$tiempo_actual = date('H:i:s');
	$fecha_para_archivo= date('Y-m-d-H-i-s');
	
	$fecha_y_hora_para_view=str_replace(":","",$tiempo_actual).str_replace("-","",$fecha_actual);
	
	$nombre_archivo_para_descarga_validados_zip="descarga_afmp_novalidados_".$fecha_para_archivo;
	
	//crea directorio para evitar que se descarguen archivos pasados
	$rutaTemporal = '../TEMPORALES/';
	$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];
	if(!file_exists($rutaTemporal.$nombre_archivo_para_descarga_validados_zip.$tiempo_actual_string))
	{
		mkdir($rutaTemporal.$nombre_archivo_para_descarga_validados_zip.$tiempo_actual_string, 0700);
	}
	else
	{
		$files_to_erase = glob($rutaTemporal.$nombre_archivo_para_descarga_validados_zip.$tiempo_actual_string."/*"); // get all file names
		foreach($files_to_erase as $file_to_be_erased)
		{ // iterate files
		  if(is_file($file_to_be_erased))
		  {
		    unlink($file_to_be_erased); // delete file
		  }
		}
	}
	$rutaTemporal=$rutaTemporal.$nombre_archivo_para_descarga_validados_zip.$tiempo_actual_string."/";
	//fin crea directorio para evitar que se descarguen archivos pasados
	
	
	//TABLA RESUMEN
	$style_titulos="";
	$style_titulos.=" style=\"color:white;text-shadow:2px 2px 8px #d9d9d9;\" ";
	
	$cuadro_resumen_estado_cons_descarga_validacion_i="";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<table style=text-align:center;width:60%;left:18%;border-style:solid;border-width:5px;position:relative; id=tabla_estado_riego_poblacion>";
	//fila titulos
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<tr style=\"background-color:black;\">";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<th colspan=100 style=text-align:center;><span $style_titulos ></span></th>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="</tr>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<tr style=\"background-color:black;\">";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<th style=text-align:left;><span $style_titulos >Nombre archivo: </span></th>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<th style=text-align:left;><span $style_titulos >Estado:</span></th>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<th style=text-align:left;><span $style_titulos >Numero registros hasta el momento:</span></th>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<th style=text-align:left;><span $style_titulos >Numero registros totales:</span></th>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="<th style=text-align:left;><span $style_titulos >Porcentaje:</span></th>";
	$cuadro_resumen_estado_cons_descarga_validacion_i.="</tr>";
	//fin fila titulos
	$cuadro_resumen_estado_cons_descarga_validacion_m="";
	$cuadro_resumen_estado_cons_descarga_validacion_f="";
	$cuadro_resumen_estado_cons_descarga_validacion_f.="</table><br>";
	//FIN TABLA RESUMEN
	
	$contador_secuencia=0;
	
		$contador_secuencia++;
		$NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS="afmp_novalidados_".$nick_user.$fecha_y_hora_para_view;

		$mensajes_info="";
			
		$sql_datos_descarga_validacion ="";
		$sql_datos_descarga_validacion.="CREATE OR REPLACE VIEW $NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS AS ";
		$sql_datos_descarga_validacion.="( ".$query_afiliados_no_validados." ) ";
		$sql_datos_descarga_validacion .=";";
		$error_bd_seq="";		
		$bool_hubo_error_query=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_datos_descarga_validacion, $error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.=" ERROR al crear vista de consulta afmp_novalidados: ".$error_bd_seq."<br>";
			echo "<script>alert('ERROR al crear vista de consulta afmp_novalidados')</script>";
			echo " ERROR al crear vista de consulta afmp_novalidados: ".$error_bd_seq."<br>";
		}
		else
		{
			$mensajes_info.="Se ha creado la vista $NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS<br>";
			echo "<script>document.getElementById('mensaje_div').innerHTML=' $mensajes_info ';</script>";
			ob_flush();
			flush();
		}
		
		//PARTE DONDE INDICA NUMERO DE REGISTROS
		$numero_registros=0;
		$sql_query_numero_registros="";
		$sql_query_numero_registros.="SELECT count(*) as contador FROM $NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS ; ";
		$error_bd_seq="";
		$resultado_query_numero_registros=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_numero_registros,$error_bd_seq);
		if($error_bd_seq!="")
		{
			$mensajes_error_bd.="ERROR AL CONSULTAR CANTIDAD ELEMENTOS de vista_subiendo: ".$error_bd_seq."<br>";
		}
		else
		{
			$mensajes_info.=" ".$resultado_query_numero_registros[0]["contador"]." <br>";
			echo "<script>document.getElementById('mensaje_div').innerHTML=' $mensajes_info  ';</script>";
			ob_flush();
			flush();
		}
		

		$numero_registros=intval($resultado_query_numero_registros[0]["contador"]);
		$lineas_del_archivo = intval($resultado_query_numero_registros[0]["contador"]);
		if($numero_registros==0)
		{
			//$mensajes_error_bd.="No hay registros a reportar. <br>";
		}
		//FIN PARTE DONDE INDICA NUMERO DE REGISTROS
		
		$numero_total_registros_tablas+=$numero_registros;
		if($numero_registros>0)
		{
			$ruta_escribir_archivo="";
			
			$titulo_tabla_consultada="afmp_novalidados";
			
			$cont_linea=1;
			$contador_offset=0;
			$limite=0;
			
			$es_primera_linea=true;
			$numero_registros_bloque=50000;	
			
			while($contador_offset<$numero_registros)
			{
				$limite=$numero_registros_bloque;
				
				if( ($contador_offset+$numero_registros_bloque)>=$numero_registros)
				{
					$limite=$numero_registros_bloque+($numero_registros-$contador_offset);
				}
				
				//PARTE QUERY FILAS POR LIMITE
				$sql_query_busqueda="";
				$sql_query_busqueda.="SELECT * FROM $NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS LIMIT $limite OFFSET $contador_offset;  ";
				$error_bd_seq="";
				$resultado_query_consulta_vista=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($sql_query_busqueda,$error_bd_seq);			
				if($error_bd_seq!="")
				{
					$mensajes_error_bd.="ERROR AL CONSULTAR de $NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS: ".$error_bd_seq."<br>";
				}
				
				//FIN PARTE QUERY FILAS POR LIMITE
				
				if(count($resultado_query_consulta_vista)>0)
				{
					
					$fue_cerrada_la_gui=false;
					foreach($resultado_query_consulta_vista as $resultado)
					{
						if($fue_cerrada_la_gui==false)
						{
						    if(connection_aborted()==true)
						    {
							$fue_cerrada_la_gui=true;
						    }
						}//fin if verifica si el usuario cerro la pantalla
						
						
						
						//PARTE ESCRIBE TITULOS COLUMNAS EN EL TXT
						if($es_primera_linea)
						{
							//CREA ARCHIVO PARA LOS REGISTROS DEFINITIVOS
							
							
							$ruta_escribir_archivo=$rutaTemporal.$titulo_tabla_consultada.".txt";//en la base de datos ya esta la extension si es 4505
							$descarga_validacion_file= fopen($ruta_escribir_archivo, "w") or die("fallo la creacion del archivo");
							fclose($descarga_validacion_file);
							//FIN CREA ARCHIVO PARA LOS REGISTROS DEFINITIVOS
							
							
							$array_rutas_archivos_generados[]=$ruta_escribir_archivo;
							
														
							
							
							
							
							$descarga_validacion_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
							
							fwrite($descarga_validacion_file, "cambiar_aqui_primera_linea");
							
							fclose($descarga_validacion_file);
							$es_primera_linea=false;
						}//fin if es primera linea
						//FIN PARTE ESCRIBE TITULOS COLUMNAS EN EL TXT
						
						$descarga_validacion_file= fopen($ruta_escribir_archivo, "a") or die("fallo la creacion del archivo");
						$linea_impresion=$resultado['tipo_id_afiliado'].",".$resultado['id_afiliado'].",".$resultado['primer_nombre'].",".$resultado['segundo_nombre'].",".$resultado['primer_apellido'].",".$resultado['segundo_apellido'].",".$resultado['sexo'].",".$resultado['fecha_nacimiento'];
						fwrite($descarga_validacion_file, "\n".$linea_impresion);
						fclose($descarga_validacion_file);
						
						//PORCENTAJE
						$muestra_mensaje_nuevo_file=false;
						$porcentaje_file=intval((($cont_linea)*100)/($numero_registros));
						if($porcentaje_file!=$cont_porcentaje_file || ($porcentaje_file==0 && ($cont_linea)==1) || $porcentaje_file==100)
						{
							$cont_porcentaje_file=$porcentaje_file;
							$muestra_mensaje_nuevo_file=true;
						}
						//FIN PORCENTAJE
						
						if($fue_cerrada_la_gui==false && $muestra_mensaje_nuevo_file)
						{
							$style="";
							if(($contador_secuencia%2)==0)
							{
								$style.="style=background-color:#ffffff";
							}//fin if
							else
							{
								$style.="style=background-color:#d9d9d9";
							}
							
							//fila de  la tabla de riesgo actual en proceso
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp="";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<tr $style>";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$titulo_tabla_consultada</td>";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>En proceso</td>";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$cont_linea</td>";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$numero_registros</td>";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$porcentaje_file % </td>";
							$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="</tr>";
							//fin fila de  la tabla de riesgo actual en proceso
							
							if($porcentaje_file==100)
							{
								//fila de  la tabla de riesgo actual terminada
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp="";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<tr $style>";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$titulo_tabla_consultada</td>";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>Recuperada</td>";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$cont_linea</td>";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$numero_registros</td>";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$porcentaje_file % </td>";
								$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="</tr>";
								//fin fila de  la tabla de riesgo actual terminada
								//se adiciona la fila terminada a la parte definitiva del medio de la tabla
								$cuadro_resumen_estado_cons_descarga_validacion_m.=$cuadro_resumen_estado_cons_descarga_validacion_mtmp;
								
							}
							
							$msg_innerHTML_mensaje_div="";
							//union cuadro resumen
							$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_i;
							$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_m;
							if($porcentaje_file<100)
							{
								$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_mtmp;
							}
							$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_f;
							//fin union cuadro resumen
							echo "<script>document.getElementById('mensaje_div').innerHTML=' $msg_innerHTML_mensaje_div ';</script>";
							
							ob_flush();
							flush();
						}//fin if
						
						$cont_linea++;
					}//fin foreach
					
					
				}//fin if
				
				$contador_offset+=$numero_registros_bloque;
				
			}//fin while
		}//fin if encontro registros
		else
		{
			$style="";
			if(($contador_secuencia%2)==0)
			{
				$style.="style=background-color:#ffffff";
			}//fin if
			else
			{
				$style.="style=background-color:#d9d9d9";
			}
			
			$titulo_tabla_consultada="S".$contador_secuencia."_"."no_hay_archivo";
			
			//fila de  la tabla de riesgo actual terminada
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp="";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<tr $style>";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>$titulo_tabla_consultada</td>";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>No se recupero</td>";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>---</td>";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>---</td>";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="<td style=text-align:left;>---</td>";
			$cuadro_resumen_estado_cons_descarga_validacion_mtmp.="</tr>";
			//fin fila de  la tabla de riesgo actual terminada
			//se adiciona la fila terminada a la parte definitiva del medio de la tabla
			$cuadro_resumen_estado_cons_descarga_validacion_m.=$cuadro_resumen_estado_cons_descarga_validacion_mtmp;
			
		}//fin else no hay resultados
		
		//BORRANDO VISTAS
		$sql_borrar_vistas="";
		$sql_borrar_vistas.=" DROP VIEW $NOMBRE_VISTA_AFILIADOS_NO_VALIDADOS ; ";
		
		$error_bd="";		
		$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_borrar_vistas, $error_bd);		
		if($error_bd!="")
		{
			if(connection_aborted()==false)
			{
				echo "<script>alert('error al borrar vistas');</script>";
			}
		}
		//FIN BORRANDO VISTAS
		
	
	
	if($numero_total_registros_tablas>0)
	{
		//GENERANDO ARCHIVO ZIP		
		$ruta_zip=$rutaTemporal.$nombre_archivo_para_descarga_validados_zip.'.zip';
		$result_zip = create_zip($array_rutas_archivos_generados,$ruta_zip);
		
		$msg_innerHTML_mensaje_div="";
		//union cuadro resumen
		$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_i;
		$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_m;
		//$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_mtmp; como aca es la parte final no se muestra ya que ya esta contenida en $cuadro_resumen_estado_cons_descarga_validacion_m
		$msg_innerHTML_mensaje_div.=$cuadro_resumen_estado_cons_descarga_validacion_f;
		//fin union cuadro resumen
		
		$mensaje.="$msg_innerHTML_mensaje_div  Se genero el reporte de forma comprimida. <br> numero total de registros recuperados <b>$numero_total_registros_tablas</b> .";
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('grilla').style.display='inline';</script>";
			echo "<script>var ruta_zip= '$ruta_zip'; </script>";
			ob_flush();
			flush();
		}//fin if
		$resultadoDefinitivo.="<input type='button' value='Descargar archivos validados .zip' id='Descargar' class='btn btn-success color_boton' onclick='download_reporte(ruta_zip);' />  ";
		//FIN GENERANDO ARCHIVO ZIP
		
		if($mensajes_error_bd!="")
		{
			$mensajes_error_bd="<br>".procesar_mensaje($mensajes_error_bd);
		}//fin if
		
		if(connection_aborted()==false)
		{
			//echo "<script>document.getElementById('mensaje_div').style.textAlign='center';</script>";
			//echo "<script>document.getElementById('resultado_definitivo').style.textAlign='center';</script>";
			echo "<script>document.getElementById('mensaje_div').innerHTML='$mensaje $mensajes_error_bd'</script>";
			echo "<script>document.getElementById('resultado_definitivo').innerHTML=\"$resultadoDefinitivo\"</script>";
			ob_flush();
			flush();
		}//fin if
	}//fin if si hay registros en al menos una tabla
	else
	{
		//$mensaje.="<br>No se encontraron resultados.";
		if(connection_aborted()==false)
		{
			echo "<script>document.getElementById('div_mensaje_error').style.display='inline';</script>";
			echo "<script>document.getElementById('parrafo_mensaje_error').innerHTML='No se puede generar el archivo, no existen datos asociados a los filtros seleccionados.';</script>";
			ob_flush();
			flush();
		}//fin if hay coneccion
	}//fin else no hay resultados
	
	
}//fin if

$coneccionBD->cerrar_conexion();
?>