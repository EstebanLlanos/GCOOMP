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

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('conversor_excel_plano4505.html.tpl');




//admin sistema
//admin eapb
//usuario normal eapb
//admin ips
//usuario normal ips
/*
INSERT INTO gios_menus_opciones_sistema(id_principal,id_padre,nombre_opcion,descripcion_ayuda,tiene_submenus,ruta_interfaz,prioridad_jerarquica) VALUES ('218','10','Excel Plano 4505','',FALSE,'..|conversor_excel_plano4505|conversor_excel_plano4505.php','33.05');
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('218','5'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('218','4'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('218','3');
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('218','2'); 
INSERT INTO gios_menus_perfiles(id_menu,id_perfil) VALUES ('218','1'); 
*/

date_default_timezone_set ("America/Bogota");
$fecha_actual = date('Y-m-d');
$tiempo_actual = date('H:i:s');
$fecha_para_archivo= date('Y-m-d-H-i-s');

$fecha_para_archivo=str_replace("-", "", $fecha_para_archivo);
$unix_time="".time();

$nombre_carpeta_origen="origcv4505".$fecha_para_archivo;
$nombre_carpeta_destino="destcv4505".$fecha_para_archivo;

$rutaTemporalOrigen="";
$rutaTemporalDestino="";

$ruta_archivos_subidos=array();
$array_nombre_original=array();
$array_nombre_creado=array();
$ruta_archivos_procesados=array();
	
//crea directorio para evitar que se descarguen archivos pasados
$rutaTemporal = '../TEMPORALES/';
$tiempo_actual_string=explode(":",$tiempo_actual)[0].explode(":",$tiempo_actual)[1].explode(":",$tiempo_actual)[2];

if(isset($_FILES['excel_loader'])==true
&& count($_FILES['excel_loader'])>0
)
{

	//CARPETA ORIGEN
	if(!file_exists($rutaTemporal.$nombre_carpeta_origen.$tiempo_actual_string))
	{
		mkdir($rutaTemporal.$nombre_carpeta_origen.$tiempo_actual_string, 0777);
	}
	else
	{
		$files_to_erase = glob($rutaTemporal.$nombre_carpeta_origen.$tiempo_actual_string."/*"); // get all file names
		foreach($files_to_erase as $file_to_be_erased)
		{ // iterate files
		  if(is_file($file_to_be_erased))
		  {
		    unlink($file_to_be_erased); // delete file
		  }//fin if
		}//fin foreach
	}//fin else
	$rutaTemporalOrigen=$rutaTemporal.$nombre_carpeta_origen.$tiempo_actual_string."/";
	//fin crea directorio

	//CARPETA DESTINO
	if(!file_exists($rutaTemporal.$nombre_carpeta_destino.$tiempo_actual_string))
	{
		mkdir($rutaTemporal.$nombre_carpeta_destino.$tiempo_actual_string, 0777);
	}
	else
	{
		$files_to_erase = glob($rutaTemporal.$nombre_carpeta_destino.$tiempo_actual_string."/*"); // get all file names
		foreach($files_to_erase as $file_to_be_erased)
		{ // iterate files
		  if(is_file($file_to_be_erased))
		  {
		    unlink($file_to_be_erased); // delete file
		  }//fin if
		}//fin foreach
	}//fin else
	$rutaTemporalDestino=$rutaTemporal.$nombre_carpeta_destino.$tiempo_actual_string."/";
	//fin crea directorio

	$array_archivos_cargados=$_FILES['excel_loader'];

	echo "Numero de Archivos ".count($array_archivos_cargados['name'])."<br>";
	$cont_archivos=0;
	while($cont_archivos<count($array_archivos_cargados['name']))
	{
		$nombre_archivo=$array_archivos_cargados['name'][$cont_archivos];
		$extension="";
		$array_sep_nombre=explode(".", $nombre_archivo);
		if(isset($array_sep_nombre[count($array_sep_nombre)-1]) && count($array_sep_nombre)>1 )
		{
			$extension=$array_sep_nombre[count($array_sep_nombre)-1];
		}//fin trae extension
		$array_nombre_original[$cont_archivos]=$nombre_archivo;
		$nombre_temporal="arch".$cont_archivos.".".$extension;
		$array_nombre_creado[$cont_archivos]=$nombre_temporal;

		$rutaTemporalSubirArchivoActual = $rutaTemporalOrigen.$array_nombre_creado[$cont_archivos];
		move_uploaded_file($array_archivos_cargados['tmp_name'][$cont_archivos], $rutaTemporalSubirArchivoActual);
		$ruta_archivos_subidos[]=$rutaTemporalSubirArchivoActual;
		echo "nombre_archivo: $nombre_archivo nombre temporal: $nombre_temporal<br>";
		$cont_archivos++;
	}
}//fin if

//echo print_r($_FILES,true)."<br>";
$ruta_archivo_final="";
$ruta_archivo_excluido="";
$ruta_archivo_lista_problemas_excel="";

$primera_linea=true;

$mensajes="";
if( count($ruta_archivos_subidos)>0 )
{
	$existen_y_son_rutas=true;
	$activarExcel=true;

	if($existen_y_son_rutas==true)
	{
		echo "<script>document.getElementById('mensaje').innerHTML='Se procedera a listar los archivos de texto al interior'</script>";		
		ob_flush();
		flush();

		date_default_timezone_set('America/Bogota');
		$fecha_archivo=date('dmYHis');

		$ruta_archivo_final=$rutaTemporalDestino."/"."consolidado".$fecha_archivo.".txt";
		$archivo_final=fopen($ruta_archivo_final, "w");
		//fwrite($archivo_final, "poner aqui primera linea");
		fclose($archivo_final);

		$ruta_archivo_excluido=$rutaTemporalDestino."/"."excluido".$fecha_archivo.".txt";
		$archivo_excluido=fopen($ruta_archivo_excluido, "w");
		fwrite($archivo_excluido, "REGISTROS EXCLUIDOS");
		fclose($archivo_excluido);

		$ruta_archivo_lista_problemas_excel=$rutaTemporalDestino."/"."listaFilesProblemaExcel".$fecha_archivo.".txt";
		$archivo_lista_problemas_excel=fopen($ruta_archivo_lista_problemas_excel, "w");
		fwrite($archivo_lista_problemas_excel, "Lista Archivo Con Problemas Excel");
		fclose($archivo_lista_problemas_excel);

		$array_lista_archivos_en_origen=array();

		if ($gestor = opendir($rutaTemporalOrigen)) 
		{
		    		 
		    /* Esta es la forma correcta de iterar sobre el directorio. */
		    while (false !== ($entrada = readdir($gestor))) 
		    {
		    	$patron ='';
		    	if($activarExcel==false)
		    	{
		    		$patron = '/(\.[t|T][x|X][t|T])|(\.[c|C][s|S][v|V])/';
		    	}//fin if
		    	else if($activarExcel==true)
		    	{
		    		$patron = '/(\.[t|T][x|X][t|T])|(\.[c|C][s|S][v|V])|(\.[x|X][l|L][s|S])|(\.[x|X][l|L][s|S][x|X])/';
		    	}//fin else
		    	
				if( preg_match($patron, $entrada) > 0)
				{
		        	$array_lista_archivos_en_origen[]=$rutaTemporalOrigen.$entrada;
		    	}//fin if

		    	$ruta_subdirectorio=$rutaTemporalOrigen.$entrada;
				if(is_dir($ruta_subdirectorio) && $entrada!=".." && $entrada!="." )
		    	{
		    		echo "es sub-directorio: \"$entrada\"<br>";
		    		$lista_archivos_subdirectorios=array();
					$lista_archivos_subdirectorios=scandir($ruta_subdirectorio);
					echo "RUTA: $ruta_subdirectorio , ".print_r($lista_archivos_subdirectorios,true)."<br>";
					ob_flush();
					flush();
					foreach ($lista_archivos_subdirectorios as $key => $archivo_subdirectorio_actual) 
					{
						$ruta_archivo_subdirectorio=$ruta_subdirectorio."/".$archivo_subdirectorio_actual;
						if(is_dir($ruta_archivo_subdirectorio) )
				    	{
				    		//echo "es sub-sub-directorio: \"$archivo_subdirectorio_actual\"<br>";
				    	}//fin if
				    	else
				    	{
				    		$array_lista_archivos_en_origen[]=$ruta_archivo_subdirectorio;
				    	}
					}//fin foreach
		    	}//fin if
		    	
		    }//fin while
		}//fin if gestor
		
		

		$contador_archivos=0;
		while($contador_archivos<count($array_lista_archivos_en_origen) )
		{
			$archivoActual=$array_lista_archivos_en_origen[$contador_archivos];

			echo "<h2><b>Procesando Archivo ".($contador_archivos+1)." : ".$archivoActual."<br></b></h2>";
			ob_flush();
			flush();

			$total_lineas_archivo_actual=0;
			$lineas_que_no_pudieron_procesar=0;
			$es_excel_archivo_actual=false;
			$patronSoloExcel = '/(\.[x|X][l|L][s|S])|(\.[x|X][l|L][s|S][x|X])/';
			if( preg_match($patronSoloExcel, $archivoActual) > 0)
			{
	        	$es_excel_archivo_actual=true;

	        	echo "Es Excel<br>";
				ob_flush();
				flush();
	    	}//fin if
	    	else
	    	{
	    		$total_lineas_archivo_actual=contar_lineas_archivo($archivoActual);
	    		echo "No Es Excel, numero total lineas: $total_lineas_archivo_actual .<br>";
				ob_flush();
				flush();
	    	}

	    	if($es_excel_archivo_actual==false)
	    	{
				$lectorArchivo = @fopen($archivoActual, "r");
				if ($lectorArchivo) 
				{
					$linea_actual=0;
				    while (($buffer = fgets($lectorArchivo, 9999)) !== false) 
				    {
				    	$linea_original=trim($buffer);

				    	$array_campos_por_tubo=explode("|", trim($buffer));
				    	$array_campos_por_punto_y_coma=explode(";", trim($buffer));
				    	$array_campos_por_tabulacion=explode("\t", trim($buffer));
				    	$array_campos_por_solo_coma=explode(",", trim($buffer));

				    	$se_arreglo_linea=false;
				    	
				    	$cumplio_con_119_campos=false;
				    	$linea_resultado="";
				    	if(count($array_campos_por_tubo)==119 )
				    	{
				    		$cont=0;
				    		while ($cont<count($array_campos_por_tubo) ) 
				    		{
				    			if($linea_resultado!=""){$linea_resultado.="|";}
				    			$linea_resultado.=trim($array_campos_por_tubo[$cont]);
				    			$cont++;
				    		}//fin while
				    		$cumplio_con_119_campos=true;
				    		$se_arreglo_linea=true;
				    	}//fin if
				    	else if(count($array_campos_por_punto_y_coma)==119)
				    	{
				    		$cont=0;
				    		while ($cont<count($array_campos_por_punto_y_coma) ) 
				    		{
				    			if($linea_resultado!=""){$linea_resultado.="|";}
				    			$linea_resultado.=trim($array_campos_por_punto_y_coma[$cont]);
				    			$cont++;
				    		}//fin while
				    		$cumplio_con_119_campos=true;
				    		$se_arreglo_linea=true;
				    	}//fin if
				    	else if(count($array_campos_por_tabulacion)==119)
				    	{
				    		$cont=0;
				    		while ($cont<count($array_campos_por_tabulacion) ) 
				    		{
				    			if($linea_resultado!=""){$linea_resultado.="|";}
				    			$linea_resultado.=trim($array_campos_por_tabulacion[$cont]);
				    			$cont++;
				    		}//fin while
				    		$cumplio_con_119_campos=true;
				    		$se_arreglo_linea=true;
				    	}//fin if
				    	else if(count($array_campos_por_solo_coma)==119)
				    	{
				    		$cont=0;
				    		while ($cont<count($array_campos_por_solo_coma) ) 
				    		{
				    			if($linea_resultado!=""){$linea_resultado.="|";}
				    			$linea_resultado.=trim($array_campos_por_solo_coma[$cont]);
				    			$cont++;
				    		}//fin while
				    		$cumplio_con_119_campos=true;
				    		$se_arreglo_linea=true;
				    	}//fin if
				    	else if(count($array_campos_por_tubo)>=120 )
				    	{
				    		
							if(  trim($array_campos_por_tubo[119])=="" )
							{
					    		$cont=0;
					    		while ($cont<119 ) 
					    		{
					    			if($linea_resultado!=""){$linea_resultado.="|";}
					    			$linea_resultado.=trim($array_campos_por_tubo[$cont]);
					    			$cont++;
					    		}//fin while
					    		$cumplio_con_119_campos=true;
					    		$se_arreglo_linea=true;
					    		echo "arreglando 120 o mas campos a 119 tubo<br>";
					    		ob_flush();
								flush();
				    		}
				    		else
				    		{
				    			echo "El campo 120 no es vacio para tubo --empieza--".trim($array_campos_por_tubo[119])."--termina--<br>";
					    		ob_flush();
								flush();
				    		}
				    	}//fin if
				    	else if(count($array_campos_por_punto_y_coma)>=120 )
				    	{
				    		
							if( trim($array_campos_por_punto_y_coma[119])=="")
							{
					    		$cont=0;
					    		while ($cont<119 ) 
					    		{
					    			if($linea_resultado!=""){$linea_resultado.="|";}
					    			$linea_resultado.=trim($array_campos_por_punto_y_coma[$cont]);
					    			$cont++;
					    		}//fin while
					    		$cumplio_con_119_campos=true;
					    		$se_arreglo_linea=true;
					    		echo "arreglando 120 o mas campos a 119 punto y coma<br>";
					    		ob_flush();
								flush();
				    		}//fin if
				    		else
				    		{
				    			echo "El campo 120 no es vacio para punto y coma --empieza--".trim($array_campos_por_punto_y_coma[119])."--termina--<br>";
					    		ob_flush();
								flush();
				    		}
				    	}//fin else if
				    	else if(count($array_campos_por_tabulacion)>=120 )
				    	{
				    		
							if( trim($array_campos_por_tabulacion[119])=="")
							{
					    		$cont=0;
					    		while ($cont<119 ) 
					    		{
					    			if($linea_resultado!=""){$linea_resultado.="|";}
					    			$linea_resultado.=trim($array_campos_por_tabulacion[$cont]);
					    			$cont++;
					    		}//fin while
					    		$cumplio_con_119_campos=true;
					    		$se_arreglo_linea=true;
					    		echo "arreglando 120 o mas campos a 119 tabulacion<br>";
					    		ob_flush();
								flush();
				    		}//fin if
				    		else
				    		{
				    			echo "El campo 120 no es vacio para tabulacion --empieza--".trim($array_campos_por_tabulacion[119])."--termina--<br>";
					    		ob_flush();
								flush();
				    		}
				    	}//fin else if
				    	else if(count($array_campos_por_solo_coma)>=120 )
				    	{
				    		
							if( trim($array_campos_por_solo_coma[119])=="")
							{
					    		$cont=0;
					    		while ($cont<119 ) 
					    		{
					    			if($linea_resultado!=""){$linea_resultado.="|";}
					    			$linea_resultado.=trim($array_campos_por_solo_coma[$cont]);
					    			$cont++;
					    		}//fin while
					    		$cumplio_con_119_campos=true;
					    		$se_arreglo_linea=true;
					    		echo "arreglando 120 o mas campos a 119 tabulacion<br>";
					    		ob_flush();
								flush();
				    		}//fin if
				    		else
				    		{
				    			//el 119 es el campo 120 contado desde cero
				    			echo "El campo 120 no es vacio para tabulacion --empieza--".trim($array_campos_por_solo_coma[119])."--termina--<br>";
					    		ob_flush();
								flush();
				    		}
				    	}//fin else if
				    	
				    	if($se_arreglo_linea==false)
				    	{				    		
				    		$lineas_que_no_pudieron_procesar++;
				    		$mensaje_error_campos="";
				    		$mensaje_error_campos.="Error: no se pudo procesar la linea $linea_actual (contando desde cero), ";
				    		$mensaje_error_campos.="La cantidad lineas que no se pudieron procesar $lineas_que_no_pudieron_procesar,";
				    		$mensaje_error_campos.="La cantidad campos separados por tubo ".count($array_campos_por_tubo).", ";
				    		$mensaje_error_campos.="La cantidad campos separados por punto y coma ".count($array_campos_por_punto_y_coma).", ";
				    		$mensaje_error_campos.="La cantidad campos separados por tabulacion ".count($array_campos_por_tabulacion).", ";
				    		$mensaje_error_campos.=".<br>";
				    		$mensaje_error_campos.="Linea Actual es: --empieza--".trim($buffer)."--termina--<br>";
				    		echo $mensaje_error_campos;
					        ob_flush();
							flush();
				    	}//fin else

				    	if($cumplio_con_119_campos)
				    	{
					        $archivo_final=fopen($ruta_archivo_final, "a");
					        if($primera_linea==true)
					        {
					        	if(trim($linea_resultado)!="")
					        	{
					        		fwrite($archivo_final, trim($linea_resultado) );
					        	}//fin if
					        	$primera_linea=false;
					        }
					        else
					        {
					        	if(trim($linea_resultado)!="")
					        	{
					        		fwrite($archivo_final, "\r\n".trim($linea_resultado) );
					        	}//fin if
					    	}//fin else
							fclose($archivo_final);
						}//fin if
						else
						{
							$array_ruta_archivo_actual=explode("/", $archivoActual);
							$nombreArchivoSinRutaCompleta=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];
							$archivo_excluido=fopen($ruta_archivo_excluido, "a");
					        fwrite($archivo_excluido, "\r\n"."Proviene del archivo: ".$nombreArchivoSinRutaCompleta."|".$linea_original);
							fclose($archivo_excluido);
						}//fin else

						$linea_actual++;
				    }//fin while

				    echo "Numero Lineas que no se pudieron procesar $lineas_que_no_pudieron_procesar .<br>";
			        ob_flush();
					flush();

				    if (!feof($lectorArchivo)) 
				    {
				        echo "Error: fallo inesperado de fgets() en el archivo $archivoActual .<br>";
				        ob_flush();
						flush();
				    }
				    fclose($lectorArchivo);
				}//fin if
			}//fin if no es archivo excel
			else if($es_excel_archivo_actual==true)
			{
				require_once('spreadsheet-reader-master/php-excel-reader/excel_reader2.php');
				require_once('spreadsheet-reader-master/SpreadsheetReader.php');
				$rutaExcelActual=$archivoActual;

				$Reader = new SpreadsheetReader($rutaExcelActual);
				$Sheets = $Reader -> Sheets();

				foreach ($Sheets as $Index => $Name)
				{
					echo 'Sheet #'.$Index.': '.$Name.' de la ruta '.$rutaExcelActual.'<br>';

					$Reader -> ChangeSheet($Index);

					echo "realizo ChangeSheet con parametro $Index <br>";

					$linea_actual=0;
					foreach ($Reader as $Row)
					{
						

				    	$se_arreglo_linea=false;
				    	
				    	$cumplio_con_119_campos=false;
				    	$linea_resultado="";
				    	if(count($Row)==119 )
				    	{
				    		$cont=0;
				    		while ($cont<count($Row) ) 
				    		{
				    			if($linea_resultado!=""){$linea_resultado.="|";}
				    			$celda_procesada=preg_replace( "/\r|\n/", "", trim($Row[$cont]) );
				    			$celda_procesada=str_replace(array("\r","\n"), "", $celda_procesada);
				    			$linea_resultado.=$celda_procesada;
				    			$cont++;
				    		}//fin while
				    		$cumplio_con_119_campos=true;
				    		$se_arreglo_linea=true;
				    	}//fin if				    	
				    	else if(count($Row)>=120 )
				    	{
				    		
							if(  trim($Row[119])=="" )
							{
					    		$cont=0;
					    		while ($cont<119 ) 
					    		{
					    			if($linea_resultado!=""){$linea_resultado.="|";}
					    			$celda_procesada=preg_replace( "/\r|\n/", "", trim($Row[$cont]) );
					    			$celda_procesada=str_replace(array("\r","\n"), "", $celda_procesada);
				    				$linea_resultado.=$celda_procesada;
					    			$cont++;
					    		}//fin while
					    		$cumplio_con_119_campos=true;
					    		$se_arreglo_linea=true;
					    		echo "arreglando 120 o mas campos a 119 tubo<br>";
					    		ob_flush();
								flush();
				    		}
				    		else
				    		{
				    			echo "El campo 120 no es vacio para fila excel --empieza--".trim($Row[119])."--termina--<br>";
					    		ob_flush();
								flush();

								$cont=0;
					    		while ($cont<count($Row) ) 
					    		{
					    			if($linea_resultado!=""){$linea_resultado.="|";}
					    			$celda_procesada=preg_replace( "/\r|\n/", "", trim($Row[$cont]) );
					    			$celda_procesada=str_replace(array("\r","\n"), "", $celda_procesada);
				    				$linea_resultado.=$celda_procesada;
					    			$cont++;
					    		}//fin while
				    		}//fin else
				    	}//fin if
				    	
				    	
				    	if($se_arreglo_linea==false)
				    	{				    		
				    		$array_ruta_archivo_actual=explode("/", $archivoActual);
							$nombreArchivoSinRutaCompleta=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];

				    		$lineas_que_no_pudieron_procesar++;
				    		$mensaje_error_campos="";
				    		$mensaje_error_campos.="Error: no se pudo procesar la linea $linea_actual (contando desde cero), ";
				    		$mensaje_error_campos.="La cantidad lineas que no se pudieron procesar $lineas_que_no_pudieron_procesar,";
				    		$mensaje_error_campos.="La cantidad campos del excel ".count($Row)." $nombreArchivoSinRutaCompleta, ";
				    		$mensaje_error_campos.=".<br>";
				    		$mensaje_error_campos.="Linea Actual es: --empieza--".$linea_resultado."--termina--<br>";
				    		echo $mensaje_error_campos;
					        ob_flush();
							flush();
				    	}//fin else

				    	if($cumplio_con_119_campos)
				    	{
					        $archivo_final=fopen($ruta_archivo_final, "a");
					        if($primera_linea==true)
					        {
					        	if(trim($linea_resultado)!="")
					        	{
					        		fwrite($archivo_final, trim($linea_resultado) );
					        	}//fin if
					        	$primera_linea=false;
					        }
					        else
					        {
					        	if(trim($linea_resultado)!="")
					        	{
					        		fwrite($archivo_final, "\r\n".trim($linea_resultado) );
					        	}//fin if
					    	}//fin else
							fclose($archivo_final);
						}//fin if
						else
						{
							$array_ruta_archivo_actual=explode("/", $archivoActual);
							$nombreArchivoSinRutaCompleta=$array_ruta_archivo_actual[count($array_ruta_archivo_actual)-1];
							$archivo_excluido=fopen($ruta_archivo_excluido, "a");
					        fwrite($archivo_excluido, "\r\n"."Proviene del archivo: ".$nombreArchivoSinRutaCompleta."|".$linea_resultado);//debido a que la linea original es un arreglo
							fclose($archivo_excluido);

							if(in_array($nombreArchivoSinRutaCompleta, $array_archivos_con_problemas)==false)
							{
								$array_archivos_con_problemas[]=$nombreArchivoSinRutaCompleta;
							}//fin if
						}//fin else

						$linea_actual++;
					}//fin foreach
				}//fin foreach
			}//fin if
			else if($es_excel_archivo_actual==true && false)//se esta mirando como remplazarlo
			{
				$rutaExcelActual=$archivoActual;
				$objPHPExcel = PHPExcel_IOFactory::load($rutaExcelActual);

				$numeroFila=0;
				$numeroColumna=0;

				$numeroHojaDeCalculo=0;
				foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) 
				{
					echo " NHC $numeroHojaDeCalculo <br>";
			        ob_flush();
					flush();

					$numeroFila=0;
				    foreach ($worksheet->getRowIterator() as $row) 
				    {
				    	$numeroColumna=0;
				        $linea_resultado="";
						$iniciaPor2=false;

				        $cellIterator = $row->getCellIterator();
				        $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if it is not set
				        foreach ($cellIterator as $cell) 
				        {
				            if (!is_null($cell)) 
				            {
				                //echo '        Cell - ' , $cell->getCoordinate() , ' - ' , $cell->getCalculatedValue() , EOL;
				                $valorCelda=trim("".$cell->getFormattedValue() );
				                if($numeroColumna==0 && $valorCelda=="2")
				                {
				                	$iniciaPor2=true;									
				                }//fin if 

				            }//fin if

				            if($numeroColumna<=118 && $iniciaPor2==true)
				            {
				            	if($linea_resultado!=""){$linea_resultado.="|";}
				            	if (!is_null($cell)) 
				            	{
				            		$valorCelda=trim("".$cell->getFormattedValue() );
				            		$linea_resultado.=$valorCelda;
				            	}//if celda no es nual
				            	else
				            	{
				            		$linea_resultado.="";
				            	}//else es nula

				            }//fin if

				            if($iniciaPor2==true && $numeroColumna>=118)
				            {
				            	$archivo_final=fopen($ruta_archivo_final, "a");
						        if($primera_linea==true)
						        {
						        	if(trim($linea_resultado)!="")
						        	{
						        		fwrite($archivo_final, trim($linea_resultado) );
						        	}//fin if
						        	$primera_linea=false;
						        }
						        else
						        {
						        	if(trim($linea_resultado)!="")
						        	{
						        		fwrite($archivo_final, "\r\n".trim($linea_resultado) );
						        	}//fin if
						    	}//fin else
								fclose($archivo_final);

								
								break;//sale del ciclo que recorre las columnas
				            }//fin if

				            if($iniciaPor2==false)
				            {
				            	//no necesita revisar esta fila si no cumple que inicio por 2 en la primera columna
				            	break;//sale del ciclo que recorre las columnas
				            }//fin if

				            /*
				            echo " NHC $numeroHojaDeCalculo NF $numeroFila NC $numeroColumna.<br>";
					        ob_flush();
							flush();
							*/

				            $numeroColumna++;
				        }//fin foreach celda aka columna

				        $stringIniciaPor2="";
				        if($iniciaPor2==true)
				        {
				        	$stringIniciaPor2=" |Si Inicio Por 2| ";
				            echo "  NF $numeroFila NTC ".count($cellIterator)."  $stringIniciaPor2 ";
					        ob_flush();
							flush();
						}
						
				        $numeroFila++;
				    }//fin foreach fila

				    echo " <br> ";
			        ob_flush();
					flush();

				    $numeroHojaDeCalculo++;
				}//fin foreach hoja de calculo
			}//fin else es excel

			$contador_archivos++;
		}//fin while

	}//fin if son validas

	if(count($array_archivos_con_problemas)>0)
	{
		$archivo_lista_problemas_excel=fopen($ruta_archivo_lista_problemas_excel, "a");
		foreach ($array_archivos_con_problemas as $key => $archivoConProblemas) 
		{
			fwrite($archivo_lista_problemas_excel, "\r\n".$archivoConProblemas);//debido a que la linea original es un arreglo		
		}
        fclose($archivo_lista_problemas_excel);
	}
}//fin if
else
{
	$mensajes.="Diliegencie las rutas y/o seleccione un rango, no deben estar vacias.<br>";
}


	echo "<script>document.getElementById('mensaje').innerHTML='".$mensajes."'</script>";

	$mensaje_descarga="";

	// $mensaje_descarga .= "<input type=\'button\' value=\'Haga clic aqui para descargar el archivo consolidado a partir de los excel\' class=\'btn btn-success color_boton\' onclick=\'download_file(\"$ruta_archivo_final\");\'/><br>";
	// $mensaje_descarga .= "<input type=\'button\' value=\'Haga clic aqui para descargar el archivo excluido a partir de los excel\' class=\'btn btn-success color_boton\' onclick=\'download_file(\"$ruta_archivo_excluido\");\'/><br>";
	// $mensaje_descarga .= "<input type=\'button\' value=\'Haga clic aqui para descargar el archivo de errores a partir de los excel\' class=\'btn btn-success color_boton\' onclick=\'download_file(\"$ruta_archivo_lista_problemas_excel\");\'/><br>";

if( count($ruta_archivos_subidos)>0 )
{

	$mensaje_descarga .= "<a  class=\'btn btn-success color_boton\' href=\'$ruta_archivo_final\' download>Haga clic aqui para descargar el archivo consolidado a partir de los excel</a><br>";
	$mensaje_descarga .= "<a  class=\'btn btn-success color_boton\' href=\'$ruta_archivo_excluido\' download>Haga clic aqui para descargar el archivo excluido a partir de los excel</a><br>";
	$mensaje_descarga .= "<a  class=\'btn btn-success color_boton\' href=\'$ruta_archivo_lista_problemas_excel\' download>Haga clic aqui para descargar el archivo de errores a partir de los excel</a><br>";

	echo "<script>document.getElementById('div_mensaje_exito').style.display='inline';</script>";
	echo "<script>document.getElementById('parrafo_mensaje_exito').innerHTML='$mensaje_descarga';</script>";
}//fin if

	// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , "<br >";


$coneccionBD->cerrar_conexion();
?>