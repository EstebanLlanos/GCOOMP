<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

error_reporting(E_ALL);
ini_set('display_errors', '0');

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

if(
	isset($_SESSION['tipo_perfil'])
	&& $_SESSION['tipo_perfil']!='5'
	)
{
	header ("Location: ../index.php?no_tiene_permiso=true");
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

date_default_timezone_set('America/Bogota');

function rename_win($oldfile,$newfile) {
    if (!rename($oldfile,$newfile)) {
        if (copy ($oldfile,$newfile)) {
            unlink($oldfile);
            return TRUE;
        }
        return FALSE;
    }
    return TRUE;
}

function contar_lineas_archivo($ruta_file)
{
	$linecount = 0;
	$handle = fopen($ruta_file, "r");
	while(!feof($handle)){
	  $line = fgets($handle);
	  $linecount++;
	}

	fclose($handle);

	return $linecount;
}//fin function contar_lineas

function mezclar_archivos_planos($filepath,$filepathsArray)
{
	$contLine=0;
	$contFile=0;
    $out = fopen($filepath, "a");
    //Then cycle through the files reading and writing.
    $filesQuantity=count($filepathsArray);
	foreach($filepathsArray as $file)
	{
	    $in = fopen($file, "r");
	    while (!feof($in) )
	    {
	      	$line = fgets($in);
	      	$contLine++;
	       	fwrite($out, $line);
	    }//end while
		if($contFile<($filesQuantity-1) )
	  	{
			fwrite($out, "\n");
		}//fin fi
	    fclose($in);
	    $contFile++;
	}//end foreach

    //Then clean up
    fclose($out);

    return $contLine;
}//fin funcion mezclar

function elemento_seleccionado($valor,$valor_seleccionado)
{
	if($valor==$valor_seleccionado)
	{
		return "selected";
	}//fin if
}//fin function


function correctorFormatoFechaVersionCorta($fecha_actual)
{
	$fecha_corregida=str_replace("/", "-", $fecha_actual);
	$array_fecha_corregida=explode("-", $fecha_corregida);
	if(count($array_fecha_corregida)==3)
    {
		if(ctype_digit($array_fecha_corregida[0]) && ctype_digit($array_fecha_corregida[1]) && ctype_digit($array_fecha_corregida[2]))
		{
		    //checkdate mm-dd-aaaa -> aaaa-mm-dd ?
		    if(checkdate($array_fecha_corregida[1],$array_fecha_corregida[2],$array_fecha_corregida[0])
		       && intval($array_fecha_corregida[0])>=32)
		    {
				//no se cambia
				$caso_al_que_entro="no cambia, caso 0 aaaa-mm-dd";
		    }
		    else
		    {
			
				if(intval($array_fecha_corregida[1])>12 && intval($array_fecha_corregida[1])<=31)
				{
				    //checkdate mm-dd-aaaa -> aaaa-dd-mm ?
				    if(checkdate($array_fecha_corregida[2],$array_fecha_corregida[1],$array_fecha_corregida[0]))
				    {
						$fecha_corregida=$array_fecha_corregida[0]."-".$array_fecha_corregida[2]."-".$array_fecha_corregida[1];
						$caso_al_que_entro="cambia, caso 1 aaaa-dd-mm";
				    }
				    else if(intval($array_fecha_corregida[2])>=32)
				    {
						//checkdate mm-dd-aaaa -> mm-dd-aaaa ?
						if(checkdate($array_fecha_corregida[0],$array_fecha_corregida[1],$array_fecha_corregida[2]))
						{
						    $fecha_corregida=$array_fecha_corregida[2]."-".$array_fecha_corregida[0]."-".$array_fecha_corregida[1];
						    $caso_al_que_entro="cambia, caso 1 mm-dd-aaaa";
						}//fin if
				    }//fin else if
				    
				}//fin if			
				else if(intval($array_fecha_corregida[2])>=32)
				{
				    //checkdate mm-dd-aaaa -> dd-mm-aaaa ?
				    if(checkdate($array_fecha_corregida[1],$array_fecha_corregida[0],$array_fecha_corregida[2]))
				    {
					$fecha_corregida=$array_fecha_corregida[2]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[0];
					$caso_al_que_entro="cambia, caso 1 dd-mm-aaaa";
				    }//fin if
				    
				}//fin else if
			
			
		    }//fin else
		    
		}//fin if
		
    }//fin if array count es 3
    
    return $fecha_corregida;
}//fin function

function diferencia_dias_entre_fechas($fecha_1,$fecha_2)
{
	//las fechas deben ser cadenas de 10 caracteres en el sigueinte formato AAAA-MM-DD, ejemplo: 1989-03-03
	//si la fecha 1 es inferior a la fecha 2, obtendra un valor mayor a 0
	//si la fecha uno excede o es igual a la fecha 2, tendra un valor resultado menor o igual a 0
	date_default_timezone_set("America/Bogota");

	$array_fecha_1=explode("-",$fecha_1);

	$verificar_fecha_para_date_diff=true;

	if(count($array_fecha_1)==3)
	{
		if(!ctype_digit($array_fecha_1[0])
		   || !ctype_digit($array_fecha_1[1]) || !ctype_digit($array_fecha_1[2])
		   || !checkdate(intval($array_fecha_1[1]),intval($array_fecha_1[2]),intval($array_fecha_1[0])) )
		{
			$verificar_fecha_para_date_diff=false;
		}
	}
	else
	{
		$verificar_fecha_para_date_diff=false;	
	}

	$array_fecha_2=explode("-",$fecha_2);			
	if(count($array_fecha_2)==3)
	{
		if(!ctype_digit($array_fecha_2[0])
		   || !ctype_digit($array_fecha_2[1]) || !ctype_digit($array_fecha_2[2])
		   || !checkdate(intval($array_fecha_2[1]),intval($array_fecha_2[2]),intval($array_fecha_2[0])) )
		{
			$verificar_fecha_para_date_diff=false;
		}
	}
	else
	{
		$verificar_fecha_para_date_diff=false;
	}

	if($verificar_fecha_para_date_diff==true)
	{
	    $year1=intval($array_fecha_1[0])."";
	    $mes1=intval($array_fecha_1[1])."";
	    $dia1=intval($array_fecha_1[2])."";

	    $year2=intval($array_fecha_2[0])."";
	    $mes2=intval($array_fecha_2[1])."";
	    $dia2=intval($array_fecha_2[2])."";

	    if(strlen($dia1)==1)
	    {
	    	$dia1="0".$dia1;
	    }//fin if

	    if(strlen($mes1)==1)
	    {
	    	$mes1="0".$mes1;
	    }//fin if

	    if(strlen($dia2)==1)
	    {
	    	$dia2="0".$dia2;
	    }//fin if

	    if(strlen($mes2)==1)
	    {
	    	$mes2="0".$mes2;
	    }//fin if

	    $fecha_1=$year1."-".$mes1."-".$dia1;

	    $fecha_2=$year2."-".$mes2."-".$dia2;
	}//fin if

	$diferencia_dias_entre_fechas=0;
	if($verificar_fecha_para_date_diff==true)
	{
		$date_fecha_1=date($fecha_1);
		$date_fecha_2=date($fecha_2);
		$fecha_1_format=new DateTime($date_fecha_1);
		$fecha_2_format=new DateTime($date_fecha_2);		
		try
		{
		$interval = date_diff($fecha_1_format,$fecha_2_format);
		$diferencia_dias_entre_fechas= (float)$interval->format("%r%a");
		}
		catch(Exception $e)
		{}
	}//fin if funcion date diff
	else
	{
		$diferencia_dias_entre_fechas=false;
	}

	return $diferencia_dias_entre_fechas;

}//fin calculo diferencia entre fechas

function edad_years_months_days($dob, $now = false)
{
	if (!$now) $now = date('d-m-Y');
	$dob = explode('-', $dob);
	$now = explode('-', $now);
	$mnt = array(1 => 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	if (($now[2]%400 == 0) or ($now[2]%4==0 and $now[2]%100!=0)) $mnt[2]=29;
	if($now[0] < $dob[0]){
		$now[0] += $mnt[intval($now[1])];
		$now[1]--;
	}
	if($now[1] < $dob[1]){
		$now[1] += 12;
		$now[2]--;
	}
	if($now[2] < $dob[2]) return false;
	return  array('y' => $now[2] - $dob[2], 'm' => $now[1] - $dob[1], 'd' => $now[0] - $dob[0]);
}//fin function calculo edad

/**
 * Copyright © 2011 Erin Millard
 */
/**
 * Returns the number of available CPU cores
 * 
 *  Should work for Linux, Windows, Mac & BSD
 * 
 * @return integer 
 */
function num_cpus()
{
  $numCpus = 1;
  if (is_file('/proc/cpuinfo'))
  {
    $cpuinfo = file_get_contents('/proc/cpuinfo');
    preg_match_all('/^processor/m', $cpuinfo, $matches);
    $numCpus = count($matches[0]);
  }
  else if ('WIN' == strtoupper(substr(PHP_OS, 0, 3)))
  {
    $process = @popen('wmic cpu get NumberOfCores', 'rb');
    if (false !== $process)
    {
      fgets($process);
      $numCpus = intval(fgets($process));
      pclose($process);
    }
  }
  else
  {
    $process = @popen('sysctl -a', 'rb');
    if (false !== $process)
    {
      $output = stream_get_contents($process);
      preg_match('/hw.ncpu: (\d+)/', $output, $matches);
      if ($matches)
      {
        $numCpus = intval($matches[1][0]);
      }
      pclose($process);
    }
  }
  
  return $numCpus;
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



$ruta_archivo_leer="origen/archivoLeer.txt";

if(isset($_REQUEST['ruta_leer'])==true
	&& trim($_REQUEST['ruta_leer'])!="")
{
	$ruta_archivo_leer=str_replace("\\", "/", trim($_REQUEST['ruta_leer'])) ;
}//fin if


$cantidad_procesos_consulta=4;
$cantidad_procesos_verificacion=4;

if(num_cpus()>0)
{
	$cantidad_procesos_consulta=num_cpus();
	$cantidad_procesos_verificacion=num_cpus();
}

$html_div_procesos="";

$cont_div_proc=0;

while($cont_div_proc<$cantidad_procesos_verificacion)
{
	$html_div_procesos.="<tr><td style='text-align:center;' colspan='100' align='center' ><div id='mensaje_estado_p".$cont_div_proc."'></div></td></tr>";
	$cont_div_proc++;
}//fin while

$mensaje="";
$mostrarResultado="none";

$smarty->assign("ruta_archivo_leer", $ruta_archivo_leer, true);
$smarty->assign("html_div_procesos", $html_div_procesos, true);

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('verif_dupl_inter_afil.html.tpl');

/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('219','72','Ver. Dupl. Inter. Afil. MP','',FALSE,'..|verif_dupl_inter_afil|verif_dupl_inter_afil.php','33.06');

INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('219','5'); --admin sistema
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('219','4'); --admin eapb
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('219','2'); --admin ips
*/

//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('219','3'); //usuario normal eapb
//INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('219','1'); //usuario normal ips


$nombre_tabla="";
if( isset($_REQUEST['nombre_tabla'])
	&& $_REQUEST['nombre_tabla']!=""
)
{
	$nombre_tabla=$_REQUEST['nombre_tabla'];
}
$columnas_a_imprimir="";
if( isset($_REQUEST['columnas_a_imprimir'])
	&& $_REQUEST['columnas_a_imprimir']!=""
)
{
	$columnas_a_imprimir=$_REQUEST['columnas_a_imprimir'];
}

$parte_from="";
$parte_from.="  gioss_afiliados_eapb_mp ";

$parte_where="";


$query_a_extraer_resultados_contar="";
/*
$query_a_extraer_resultados_contar.="
	select count(DISTINCT pa.c12_nitusuar) as contador_filas from poblacion_para_analizar_2016 pa inner join gioss_afiliados_eapb_mp gamp on pa.c12_nitusuar=gamp.id_afiliado::numeric where c32_anoper='2016' and c31_mesper='12'       ;
";
*/


$query_comun="";
/*
$query_comun.="
	select DISTINCT ON (pa.c12_nitusuar) c12_nitusuar, gamp.* , pa.c38_regional, pa.c39_regionaleps, pa.c0_sucursal from poblacion_para_analizar_2016 pa inner join gioss_afiliados_eapb_mp gamp on pa.c12_nitusuar=gamp.id_afiliado::numeric where pa.c32_anoper='2016' and pa.c31_mesper='12'  order by pa.c12_nitusuar
";
*/


ob_flush();
flush();



$mensajes="";
if( isset($_REQUEST['iniciar'])
	&& $_REQUEST['iniciar']!=""
	&& $_REQUEST['iniciar']=="SI"
)
{

	date_default_timezone_set('America/Bogota');
	$fecha_archivo=date('dmYHis');

	$fecha_actual=date('Y-m-d');
	$tiempo_actual=date('H:i:s');

	$carpetaPropia="resAfilDuplEE".$fecha_archivo;

	$pathCarpetaDestino="destino/";
	if(!file_exists($pathCarpetaDestino)==true)
	{
		$pathCarpetaDestino="../TEMPORALES/";
		if(!file_exists($pathCarpetaDestino)==true)
		{
			mkdir($pathCarpetaDestino,0777,true);
		}//fin if
	}//fin if

	mkdir($pathCarpetaDestino.$carpetaPropia,0777,true);

		



	//PARTE CONSULTA AFILIADOS Y ESCRIBE DATOS
	

	$pathArchivoAfiliadosActualNoExt=$pathCarpetaDestino.$carpetaPropia."/"."ListaAfiliadosActual";
	$pathArchivoDirector=$pathCarpetaDestino.$carpetaPropia."/"."director.txt";

	$cantidad_registros_bloque_afil=50000;
	$ultima_posicion_afil=0;
	$IdProceso=0;
	$pathArchivoAfiliadosActual="";
	
	$tablaAfiliados="gioss_afiliados_eapb_mp";
	$query_count_afil="SELECT count(*) as contador_filas FROM $tablaAfiliados  ";
	$error_bd_seq_cont_afil="";
	$resultados_contador_afil=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_count_afil, $error_bd_seq_cont_afil);
	$numero_filas_afil=0;
	if($error_bd_seq_cont_afil!="")
	{
	    echo "Error al contar los resultados.<br>";
	}//fin if
	else
	{
		$numero_filas_afil=$resultados_contador_afil[0]['contador_filas'];
	}//fin else 

	
	$cantidad_registros_bloque_afil=intval(round($numero_filas_afil/$cantidad_procesos_consulta, 0, PHP_ROUND_HALF_UP) );
	$mensaje_inicio_particion="cantidad_procesos_consulta: ( $cantidad_procesos_consulta ) para un total de ( $numero_filas_afil ) registros, se separaran en ( $cantidad_registros_bloque_afil ), inicia $tiempo_actual en $fecha_actual<br>";
	
	sleep(1);
	echo "<script>document.getElementById('mensaje_estado_particion').innerHTML='$mensaje_inicio_particion';</script>";
	ob_flush();
  	flush();

	$contProcesos=0;
	$arrayRutasArchivosResultadosProcesos=array();
	while($contProcesos<$cantidad_procesos_consulta)
	{
		$archivoProcesoActual=$pathArchivoAfiliadosActualNoExt."_".$contProcesos.".txt";
		$arrayRutasArchivosResultadosProcesos[]=$archivoProcesoActual;		

		echo "<script>llamarConsultaAfil('$pathArchivoDirector','$archivoProcesoActual','$ultima_posicion_afil','$cantidad_registros_bloque_afil','$numero_filas_afil');</script>";
		/*
		while(file_exists($archivoProcesoActual)==false)
		{
			sleep(1);
			echo "<script>llamarConsultaAfil('$pathArchivoDirector','$archivoProcesoActual','$ultima_posicion_afil','$cantidad_registros_bloque_afil','$numero_filas_afil');</script>";
		}//fin while
		*/
		$mensaje_inicio_particion.="Inicia en $ultima_posicion_afil <br>";
		$ultima_posicion_afil=$ultima_posicion_afil+$cantidad_registros_bloque_afil;
		$contProcesos++;
	}//fin while

	sleep(1);
	echo "<script>document.getElementById('mensaje_estado_particion').innerHTML='$mensaje_inicio_particion';</script>";
	ob_flush();
  	flush();
	
	

	$pathArchivoAfiliadosActual=$pathCarpetaDestino.$carpetaPropia."/"."ListaAfiliadosActual.txt";

	$boolTerminoParticionParalela=false;

	$arrayArchivosEnDirector=array();

	echo "<script>document.getElementById('mensaje_estado_particion').innerHTML=document.getElementById('mensaje_estado_particion').innerHTML+'Esperando...<br>';</script>";
	ob_flush();
  	flush();	

	while($boolTerminoParticionParalela==false)
	{
			
		sleep(5);

		if(file_exists($pathArchivoDirector)==true)
		{
			
			//echo $pathArchivoDirector."<br>";

			$archivoDirector=fopen($pathArchivoDirector, "r");
			$contLineasDirector=0;
			$arrayArchivosEnDirector=array();
			while(!feof($archivoDirector) )
			{
				$lineaDirector=trim(fgets($archivoDirector) );
				if($lineaDirector!="")
				{
					$arrayLineaActual=explode("|", $lineaDirector);
					$arrayExtraerIndiceDelNombre=explode("_", $arrayLineaActual[0]);
					$indice=intval(str_replace(".txt", "", $arrayExtraerIndiceDelNombre[1]) );
					$arrayArchivosEnDirector[$indice]=$arrayLineaActual[0];
					$contLineasDirector++;
				}//fin if

			}//fin while
			fclose($archivoDirector);

			if($contLineasDirector==$cantidad_procesos_consulta)
			{
				$archivoAfiliadosActual=fopen($pathArchivoAfiliadosActual, "w");
				$encabezado="";
				$encabezado.="Afiliados Registrados A la fecha $fecha_archivo\n";
				$encabezado.="Numero ID|Tipo ID|Primer Apellido|Primer Nombre|Segundo Apellido|Segundo Nombre|Sexo|Fecha Nacimiento\n";
				fwrite($archivoAfiliadosActual, $encabezado);
				fclose($archivoAfiliadosActual);

				$registros_totales=mezclar_archivos_planos($pathArchivoAfiliadosActual, $arrayArchivosEnDirector);
				echo "<script>document.getElementById('mensaje_estado_particion').innerHTML=document.getElementById('mensaje_estado_particion').innerHTML+registros_totales ".$registros_totales."<br>';</script>";

				$boolTerminoParticionParalela=true;
				echo "<script>document.getElementById('mensaje_estado_particion').innerHTML=document.getElementById('mensaje_estado_particion').innerHTML+'Termino Consulta Paralela...<br>';</script>";
				ob_flush();
			  	flush();
			}//fin if

		}//fin if
	}//fin while

	if(file_exists($pathArchivoAfiliadosActual)==true)
	{
		$rutaZipAfiliadosAll=$pathArchivoAfiliadosActual.".zip";
		$boolZip=create_zip(array($pathArchivoAfiliadosActual),$rutaZipAfiliadosAll);
		$mensajes.="<a class=\"btn btn-success color_boton\" href=\"$rutaZipAfiliadosAll\" target=\"blank_\">Afiliados actuales en bd.</a><br>";
		echo "<script>document.getElementById('mensaje').innerHTML='".$mensajes."';</script>";
		ob_flush();
	  	flush();
  	}//fin if

	$ruta_archivo_leer=$pathArchivoAfiliadosActual;

	$fecha_temp=date('Y-m-d');
	$tiempo_temp=date('H:i:s');
	echo "<script>document.getElementById('mensaje_estado_particion').innerHTML=document.getElementById('mensaje_estado_particion').innerHTML+'Termino la parte de consulta de todos los afiliados a las $tiempo_temp de $fecha_temp <br>';</script>";
	//FIN PARTE CONSULTA AFILIADOS Y ESCRIBE DATOS


	$encabezadoDefinitivos="";
	$encabezadoDefinitivos.="Registros Unicos De Los Duplicados Dentro De La Tabla De Afiliados \n";
	$encabezadoDefinitivos.="Numero ID|Tipo ID|Primer Apellido|Segundo Apellido|Primer Nombre|Segundo Nombre|Fecha De Nacimiento|Sexo";

	$pathdefinitivos_sin_ext=$pathCarpetaDestino.$carpetaPropia."/"."definitivosDeDupl";
	$pathlogproceso_sin_ext=$pathCarpetaDestino.$carpetaPropia."/"."logProceso";
	$pathtempprogreso_sin_ext=$pathCarpetaDestino.$carpetaPropia."/"."tempProgreso";

	$pathdirectorverificacion=$pathCarpetaDestino.$carpetaPropia."/"."directorVerificacion.txt";

	$arrayRutasArchivosResultadosProcesosVerificacion=array();
	
	$contProcesos=0;
	$arrayRutasArchivosResultadosProcesos=array();
	while($contProcesos<$cantidad_procesos_verificacion)
	{
		$pathDefinitivosActual=$pathdefinitivos_sin_ext."_".$contProcesos.".txt";
		$pathLogProcesoActual=$pathlogproceso_sin_ext."_".$contProcesos.".txt";
		$pathTempProgresoActual=$pathtempprogreso_sin_ext."_".$contProcesos.".txt";
		$pathArchivoLeerActual=$arrayArchivosEnDirector[$contProcesos];
		$arrayRutasArchivosResultadosProcesosVerificacion[]=$pathDefinitivosActual;		

		echo "<script>llamarVerificacionAfil('$pathdirectorverificacion','$pathArchivoLeerActual','$pathDefinitivosActual','$pathLogProcesoActual','$pathTempProgresoActual');</script>";
		/*
		while(file_exists($pathDefinitivosActual)==false)
		{
			sleep(1);
			echo "<script>llamarVerificacionAfil('$pathdirectorverificacion','$pathArchivoLeerActual','$pathDefinitivosActual','$pathLogProcesoActual','$pathTempProgresoActual');</script>";
		}//fin while
		*/
		$contProcesos++;
	}//fin while

	$pathdefinitivosFinal=$pathdefinitivos_sin_ext.".txt";

	$boolTerminoVerificacionParalela=false;

	$arrayArchivosEnDirectorVerificacion=array();

	echo "<script>document.getElementById('mensaje_estado_particion').innerHTML=document.getElementById('mensaje_estado_particion').innerHTML+'Esperando Verificacion...<br>'</script>";
	ob_flush();
  	flush();

	while($boolTerminoVerificacionParalela==false)
	{			
		sleep(5);

		$contProcesos=0;
		$html_script_div_proc_ver="";
		while($contProcesos<$cantidad_procesos_verificacion)
		{
			$pathTempProgresoActual=$pathtempprogreso_sin_ext."_".$contProcesos.".txt";
			if(file_exists($pathTempProgresoActual)==true)
			{
				$archivoTempProgreso=fopen($pathTempProgresoActual, "r");
				$contenidoTempProgreso="";
				while(!feof($archivoTempProgreso))
				{
					$contenidoTempProgreso=trim(fgets($archivoTempProgreso) );
				}//fin while
				fclose($archivoTempProgreso);
				$html_script_div_proc_ver.="document.getElementById('mensaje_estado_p".$contProcesos."').innerHTML='".$contenidoTempProgreso."';";
			}//fin if
			else
			{
				$html_script_div_proc_ver.="document.getElementById('mensaje_estado_p".$contProcesos."').innerHTML='".$pathTempProgresoActual." No Existe Aun, Espere Un Momento.';";
			}
			$contProcesos++;
		}//fin while
		echo "<script>".$html_script_div_proc_ver."</script>";
		ob_flush();
	  	flush();

		if(file_exists($pathdirectorverificacion)==true)
		{
			
			//echo $pathArchivoDirector."<br>";

			$archivoDirectorVerificacion=fopen($pathdirectorverificacion, "r");
			$contLineasDirectorVerificacion=0;
			$arrayArchivosEnDirectorVerificacion=array();
			while(!feof($archivoDirectorVerificacion) )
			{
				$lineaDirectorVerificacion=trim(fgets($archivoDirectorVerificacion) );
				if($lineaDirector!="")
				{
					$arrayLineaActual=explode("|", $lineaDirectorVerificacion);
					$arrayExtraerIndiceDelNombre=explode("_", $arrayLineaActual[0]);
					$indice=intval(str_replace(".txt", "", $arrayExtraerIndiceDelNombre[1]) );
					$arrayArchivosEnDirectorVerificacion[$indice]=$arrayLineaActual[0];
					$contLineasDirectorVerificacion++;
				}//fin if

			}//fin while
			fclose($archivoDirectorVerificacion);

			if($contLineasDirectorVerificacion==$cantidad_procesos_verificacion)
			{
				$archivoDefinitivosFinal=fopen($pathdefinitivosFinal, "w");
				$encabezado="";
				$encabezado.="Afiliados Registrados Con Tipo ID definitivo de acuerdo a su fecha de nacimiento\n";
				$encabezado.="Numero ID|Tipo ID|Primer Apellido|Primer Nombre|Segundo Apellido|Segundo Nombre|Sexo|Fecha Nacimiento\n";
				fwrite($archivoDefinitivosFinal, $encabezado);
				fclose($archivoDefinitivosFinal);

				$registros_totales=mezclar_archivos_planos($pathdefinitivosFinal, $arrayArchivosEnDirectorVerificacion);
				echo "registros_totales ".$registros_totales."<br>";

				$boolTerminoVerificacionParalela=true;
				echo "Termino Verificacion Paralela...<br>";
				ob_flush();
			  	flush();
			}//fin if

		}//fin if
	}//fin while

	while(file_exists($pathdefinitivosFinal)==false)
	{
		sleep(5);
	}//fin while

	if(file_exists($pathdefinitivosFinal)==true)
	{
		$rutaZipDefinitivos=$pathdefinitivosFinal.".zip";
		$boolZip=create_zip(array($pathdefinitivosFinal),$rutaZipDefinitivos);
		$mensajes.="<a class=\"btn btn-success color_boton\" href=\"$rutaZipDefinitivos\" target=\"blank_\">Definitivos con Tipo Id Adecuado A fecha nacimiento de los duplicados dentro de la tabla afiliados.</a><br>";
		echo "<script>document.getElementById('mensaje').innerHTML='".$mensajes."';</script>";
		ob_flush();
	  	flush();
  	}//fin if

}//fin if
else
{
	/*
	if(isset($_REQUEST['columnas_a_imprimir']) && $_REQUEST['columnas_a_imprimir']=="")
	{
		$mensajes.="Diligencie el nombre de las columnas.<br>";
	}//fin if
	else
	{
		$mensajes.="Diliegencie el nombre de la tabla.<br>";
	}//fin else
	*/
	if(isset($_REQUEST['iniciar'])
	&& ($_REQUEST['iniciar']=="" || $_REQUEST['iniciar']=="NO")
	)
	{
		$mensajes.="Seleccione SI en iniciar para ejecutar la extraccion de datos.<br>";
	}//fin if

	if(file_exists($ruta_archivo_leer)==false && isset($_REQUEST['iniciar']) && $_REQUEST['iniciar']=="SI")
	{
		$mensajes.="La ruta del archivo no existe.<br>";
	}//fin if
}//fin else


	echo "<script>document.getElementById('mensaje').innerHTML='".$mensajes."';</script>";
	ob_flush();
  	flush();

$coneccionBD->cerrar_conexion();
?>