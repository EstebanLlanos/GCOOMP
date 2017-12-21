<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

function procesar_tildes_eapb($mensaje)
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
	
	return $mensaje_procesado;
}

//VALIDAR CT
function validar_eapb_ct($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$archivos_subidos,$fecha_remision,$cod_eapb,$numero_de_remision,$ruta_nueva)
{
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//campo0ct aka 1
	$numero_campo=0;
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	$nombre_prestador_ct=$campos[0];//aka campo 1
	$archivo_actual_a_revisar=$campos[2];
	
	$nombres_archivos_a_revisar_existe =array();
	
	foreach($archivos_subidos as $archivo)
	{
		$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
		$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		
	}//fin foreach
	
	if($campos[$numero_campo]!="")
	{
		//hacerlo mejor contrala tabla entidad administradora
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
				
		//relacionamiento
		$campo_entidad_reportadora=$campos[0];//aka campo 1
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		/*
		$existe_entidad_reportadora=false;
		
                
		$ruta_ac=$rutaTemporal.$ruta_nueva."/"."AC".$numero_de_remision.".txt";
		//echo "<script>alert('$ruta_ac')</script>";
		if(isset($nombres_archivos_a_revisar_existe["AC".$numero_de_remision]) && file_exists($ruta_ac))
		{
			//echo "<script>alert('existe AC')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ac, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			
		}//fin if esta ac
		
		$ruta_ap=$rutaTemporal.$ruta_nueva."/"."AP".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AP".$numero_de_remision]) && file_exists($ruta_ap))
		{
			//echo "<script>alert('existe AP')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ap, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			
		}//fin if ap
		
		$ruta_au=$rutaTemporal.$ruta_nueva."/"."AU".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AU".$numero_de_remision]) && file_exists($ruta_au))
		{
			//echo "<script>alert('existe AU')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_au, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if au
		
		$ruta_ah=$rutaTemporal.$ruta_nueva."/"."AH".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AH".$numero_de_remision]) && file_exists($ruta_ah))
		{
			//echo "<script>alert('existe AH')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ah, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if ah
		
		$ruta_an=$rutaTemporal.$ruta_nueva."/"."AN".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AN".$numero_de_remision]) && file_exists($ruta_an))
		{
			//echo "<script>alert('existe AN')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_an, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if an
		
		$ruta_am=$rutaTemporal.$ruta_nueva."/"."AM".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AM".$numero_de_remision]) && file_exists($ruta_am))
		{
			//echo "<script>alert('existe AM')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_am, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if am
		
		$ruta_av=$rutaTemporal.$ruta_nueva."/"."AV".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AV".$numero_de_remision]) && file_exists($ruta_av))
		{
			//echo "<script>alert('existe AV')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_at, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]==$campo_entidad_reportadora)
				{
					$existe_entidad_reportadora=true;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if at
                
		
		
		if($existe_entidad_reportadora==false)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		*/
		
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="CT";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
	}//fin diferente de vacio
	
	//campo1ct aka 2
	$numero_campo=1;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad 1ct aka 2
		
		$array_fecha_remision=explode("/",$fecha_remision);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$date_reportada=$array_fecha[2]."-".$array_fecha[1]."-".$array_fecha[0];
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[0]."-".$array_fecha_remision[1];
		
		$interval = date_diff(date_create($date_reportada),date_create($fecha_actual));
		$tiempo_dif_actual= (float)($interval->format("%r%a"));
		if($tiempo_dif_actual<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010334"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			//$errores_campos.=$array_tipo_validacion_rips["01"].","."grupo 105".","."Campo invalido cuando la fecha reportada es diferente a la fecha de remision"." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$interval = date_diff(date_create($date_reportada),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_reportada t $date_remision s $tiempo ');</script>";
		if($tiempo!=0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010521"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			//$errores_campos.=$array_tipo_validacion_rips["01"].","."grupo 105".","."Campo invalido cuando la fecha reportada es diferente a la fecha de remision"." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	//campo2ct aka 3
	
	
	$numero_campo=2;
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=8)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010112"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		$sigla_archivos_subidos=substr($campos[$numero_campo],0,2);
		$condicion1= "CT"!=$sigla_archivos_subidos &&  "US"!=$sigla_archivos_subidos;
		$condicion2= "AC"!=$sigla_archivos_subidos && "AP"!=$sigla_archivos_subidos && "AU"!=$sigla_archivos_subidos && "AH"!=$sigla_archivos_subidos;
		$condicion3= "AN"!=$sigla_archivos_subidos && "AM"!=$sigla_archivos_subidos && "AV"!=$sigla_archivos_subidos;
		if($condicion1 && $condicion2 && $condicion3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010302"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//calidad y relacionamiento 2ct aka 3
		$nombres_archivos =array();
		
		foreach($archivos_subidos as $archivo)
		{
			$nombres_archivos_subidos[]=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
		}
		
		$nombres_archivos_indicados_en_ct=array();
		$rutaTemporal = '../TEMPORALES/';
		
		$ruta_este_archivo_control=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		$cont_reportados=0;
		$file_ct = fopen($ruta_este_archivo_control, 'r') or exit("No se pudo abrir el archivo");
		while (!feof($file_ct)) 
		{
			$linea_tmp = fgets($file_ct);
			$esta_linea= explode("\n", $linea_tmp)[0];
			$campos_ct = explode(",", $esta_linea);		
			if(!isset($nombres_archivos_indicados_en_ct[$campos_ct[2]]))
			{
				$nombres_archivos_indicados_en_ct[$campos_ct[2]]=1;
			}
			else
			{
				$nombres_archivos_indicados_en_ct[$campos_ct[2]]++;
			}
			$cont_reportados++;
		}
		fclose($file_ct);
		$cont_subidos_sin_ct=count($nombres_archivos_subidos)-1;
		
		if($nombres_archivos_indicados_en_ct[$campos[$numero_campo]]>1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010502"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		if($cont_subidos_sin_ct!=$cont_reportados)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010602"]." ... ".$campos[$numero_campo]." ".$cont_subidos_sin_ct."-".$cont_reportados.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		*/
		
		$existe_en_subidos=false;
		foreach($nombres_archivos_subidos as $nombre_file_subido)
		{
			if($campos[$numero_campo]==$nombre_file_subido)
			{
				$existe_en_subidos=true;
			}
		}
		
		if($existe_en_subidos==false)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010602"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		else
		{
			//echo "<script>alert(' $existe_en_subidos ".$campos[$numero_campo]." ');</script>";
		}
	}//diferente de vacio
	
	
	//campo3ct aka 4
	$numero_campo=3;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
	
	if($campo_fix=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	if($campo_fix!="")
	{
		if(strlen($campo_fix)>10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010113"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campo_fix))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ".$campo_fix." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//relacionamiento 3ct aka 4
		
		$ruta_archivo_reportado=$rutaTemporal.$ruta_nueva."/".$campos[2].".txt";
		$size_archivo_reportado=0;
		if(file_exists($ruta_archivo_reportado))
		{
			$size_archivo_reportado=count(file($ruta_archivo_reportado));
		}
		//echo "<script>alert('".$size_archivo_reportado."_".intval($campo_fix)."');</script>";
		if($size_archivo_reportado!=intval($campo_fix))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010603"]."- Numero lineas registrado ".$campo_fix." - Numero lineas encontrado ".$size_archivo_reportado." indicado para ".$campos_ct[2].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin validar ct
//FIN VALIDAR CT


//VALIDAR US
function validar_eapb_us($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados)
{
	$coneccionBD = new conexion();

	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//campo0us aka 1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="US";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
		
	}//diferente de vacio
	
	//campo1us aka 2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]=="AS" && intval($campos[3])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010538"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(($campos[$numero_campo]=="CC" || $campos[$numero_campo]=="TI" || $campos[$numero_campo]=="AS") && (intval($campos[7])==2 || intval($campos[7])==3))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010501"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(($campos[$numero_campo]=="RC" || $campos[$numero_campo]=="TI" || $campos[$numero_campo]=="MS") && (intval($campos[7])==1 && intval($campos[6])>17 ))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010501"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	if($campos[$numero_campo]!="")
	{
		//relacionamiento
		
		$campo_ti_1=$campos[1];
		$campo_id_2=$campos[2];
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		
		$string_archivos_no_encontrado="";
		
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		$existe_usuario=false;
		
		/*
		$ruta_ac=$rutaTemporal.$ruta_nueva."/"."AC".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AC".$numero_de_remision]) && file_exists($ruta_ac) && $existe_usuario==false)
		{
			//echo "<script>alert('existe AC')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ac, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if(count($campos_tmp)==17)
				{
					if($campos_tmp[3]==$campo_ti_1 && $campos_tmp[4]==$campo_id_2)
					{
						$existe_usuario=true;
					}
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_usuario==false)
			{
				$string_archivos_no_encontrado.=" AC ";
			}
		}//fin if esta ac
		
		$ruta_ap=$rutaTemporal.$ruta_nueva."/"."AP".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AP".$numero_de_remision]) && file_exists($ruta_ap) && $existe_usuario==false)
		{
			//echo "<script>alert('existe AP')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ap, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if(count($campos_tmp)==15)
				{
					if($campos_tmp[3]==$campo_ti_1 && $campos_tmp[4]==$campo_id_2)
					{
						$existe_usuario=true;
					}
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_usuario==false)
			{
				$string_archivos_no_encontrado.=" AP ";
			}
		}//fin if ap
		
		$ruta_au=$rutaTemporal.$ruta_nueva."/"."AU".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AU".$numero_de_remision]) && file_exists($ruta_au) && $existe_usuario==false)
		{
			//echo "<script>alert('existe AU')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_au, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if(count($campos_tmp)==17)
				{
					if($campos_tmp[3]==$campo_ti_1 && $campos_tmp[4]==$campo_id_2)
					{
						$existe_usuario=true;
					}
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_usuario==false)
			{
				$string_archivos_no_encontrado.=" AU ";
			}
		}//fin if au
		
		$ruta_ah=$rutaTemporal.$ruta_nueva."/"."AH".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AH".$numero_de_remision]) && file_exists($ruta_ah) && $existe_usuario==false)
		{
			//echo "<script>alert('existe AH')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ah, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if(count($campos_tmp)==19)
				{
					if($campos_tmp[3]==$campo_ti_1 && $campos_tmp[4]==$campo_id_2)
					{
						$existe_usuario=true;
					}
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_usuario==false)
			{
				$string_archivos_no_encontrado.=" AH ";
			}
		}//fin if ah
		
		$ruta_an=$rutaTemporal.$ruta_nueva."/"."AN".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AN".$numero_de_remision]) && file_exists($ruta_an) && $existe_usuario==false)
		{
			//echo "<script>alert('existe AN')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_an, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if(count($campos_tmp)==14)
				{
					if($campos_tmp[3]==$campo_ti_1 && $campos_tmp[4]==$campo_id_2)
					{
						$existe_usuario=true;
					}
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_usuario==false)
			{
				$string_archivos_no_encontrado.=" AN ";
			}
		}//fin if an
		
		
		$ruta_am=$rutaTemporal.$ruta_nueva."/"."AM".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["AM".$numero_de_remision]) && file_exists($ruta_am) && $existe_usuario==false)
		{
			//echo "<script>alert('existe AM')</script>";
			$cont_reportados=0;
			$file_tmp = fopen($ruta_am, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if(count($campos_tmp)==14)
				{
					if($campos_tmp[3]==$campo_ti_1 && $campos_tmp[4]==$campo_id_2)
					{
						$existe_usuario=true;
					}
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_usuario==false)
			{
				$string_archivos_no_encontrado.=" AM ";
			}
		}//fin if am
		
		
		if($existe_usuario==false)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010608"]." ".$string_archivos_no_encontrado." "." ... ".$campos[$numero_campo].$campos[1].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		*/
		
	}//diferente de vacio
	
	//campo2us aka 3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20 && ($campos[0]!="MS" && $campos[0]!="AS"))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(strlen($campos[$numero_campo])>10 && ($campos[0]=="MS" || $campos[0]=="AS"))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010120"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//S D I A =AS MS 6 posicion
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$coincidencias_numero_identificacion_ms_as=array();
		preg_match("/[0-9][0-9][0-9][0-9][0-9](S|D|I|A)[0-9]*/",$campos[$numero_campo], $coincidencias_numero_identificacion_ms_as);
		
		$coincidencias_numero_identificacion_normal=array();
		preg_match("/[0-9]+/",$campos[$numero_campo], $coincidencias_numero_identificacion_normal);
		
		if((count($coincidencias_numero_identificacion_normal)==0 || !is_array($coincidencias_numero_identificacion_normal) )&& ($campos[0]!="MS" && $campos[0]!="AS"))
		{
			
		}
		
		if((count($coincidencias_numero_identificacion_ms_as)==0 || !is_array($coincidencias_numero_identificacion_ms_as)) && ($campos[0]=="MS" || $campos[0]=="AS"))
		{
			
		}
		
		
		//verifica si el usuario se repite en otras lineas del archivo
		$tipo_id_us_ver=$campos[1];
		$numero_id_us_ver=$campos[2];
		if(array_key_exists($tipo_id_us_ver."_".$numero_id_us_ver,$array_afiliados_duplicados))
		{
			$array_afiliados_duplicados[$tipo_id_us_ver."_".$numero_id_us_ver].="-".($nlinea+1);
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010535"]." ... se repite $tipo_id_us_ver $numero_id_us_ver en las lineas ".$array_afiliados_duplicados[$tipo_id_us_ver."_".$numero_id_us_ver].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		else
		{
			$array_afiliados_duplicados[$tipo_id_us_ver."_".$numero_id_us_ver]="".($nlinea+1);
		}
		
		/*
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="US";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);
				if($tipo_id_us_ver==$campos[1] && $numero_id_us_ver==$campos[2])
				{
					$es_igual=true;
					$linea_es_igual=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010535"]." ... se repite $tipo_id_us_ver $numero_id_us_ver en la linea $linea_es_igual,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el tipo id entidad reportadora  esta diferente al tipo id entidad reportadora otras lineas en su mismo archivo
		*/
		
	}//difernete de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	
	//campo3us aka 4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>8;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010307"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo4us aka 5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010308"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo5us aka 6
	$numero_campo=5;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010115"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo6us aka 7
	$numero_campo=6;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010114"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad edad 8us aka 9
	
		if(intval($campos[$numero_campo])>120 && intval($campos[7])==1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010510"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])>11 && intval($campos[7])==2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010511"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])>29 && intval($campos[7])==3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010512"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>120)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010335"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo7us aka 8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010308"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo8us aka 9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		$condicion1=$campos[$numero_campo]!="M" && $campos[$numero_campo]!="F";
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010309"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo9us aka 10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query11us="select * from gioss_tabla_divi_pola_rips where codigo_departamento::integer='".$campos[$numero_campo]."'; ";
		$res_query11us=$coneccionBD->consultar2($query11us);
		if(count($res_query11us)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010310"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo10us aka 11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010115"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query12us="select * from gioss_tabla_divi_pola_rips where codigo_municipio::integer='".$campos[$numero_campo]."'; ";
		$res_query12us=$coneccionBD->consultar2($query12us);
		if(count($res_query12us)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010311"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="" )
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo11us aka 12
	$numero_campo=11;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	
	if($campo_fix!="")
	{
		if(strlen($campo_fix)!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campo_fix!="U" && $campo_fix!="R";
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010312"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campo_fix=="" )
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//FIN VALIDAR US

//VALIDAR AC
function validar_eapb_ac($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados,&$numeros_de_factura_por_ti_nit_ips,$nombre_largo_rips,$departamento_filtro,$mpio_filtro_bd)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//validacion hacia exitoso para servicios reportados
	//Campo 2	Código Prestador de Servicios
	//Campo 3	Tipo de Identificacion usuario
	//Campo 4	Numero de identificación usuario
	//Campo 5	Fecha de la atencion
	//Campo 7	Código de la consulta
	

	$codigo_prestador_de_servicios_ac=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[1] ));
	$tipo_identificacion_ac=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[2] ));
	$numero_identificacion_ac=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[3] ));
	$fecha_de_atencion_ac=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[4] ));
	$codigo_de_la_consulta_ac=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[6] ));
	
	$fecha_de_atencion_array_ac=explode("/",$fecha_de_atencion_ac);
	
	
	
	if($codigo_prestador_de_servicios_ac!=""
	   && $tipo_identificacion_ac!=""
	   && $numero_identificacion_ac!=""
	   && $fecha_de_atencion_ac!=""
	   && $codigo_de_la_consulta_ac!=""
	   && count($fecha_de_atencion_array_ac)==3
	   && checkdate(intval($fecha_de_atencion_array_ac[1]),intval($fecha_de_atencion_array_ac[0]),intval($fecha_de_atencion_array_ac[2]))
	   )
	{
		$fecha_de_atencion_bd_ac=$fecha_de_atencion_array_ac[2]."-".$fecha_de_atencion_array_ac[1]."-".$fecha_de_atencion_array_ac[0];
		
		$query_servicios_reportados="";
		$query_servicios_reportados.="SELECT * FROM gioss_archivo_cargado_ac WHERE ";
		$query_servicios_reportados.=" codigo_prestador_servicios_salud='$codigo_prestador_de_servicios_ac' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" tipo_identificacion_usuario='$tipo_identificacion_ac' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" numero_identificacion_usuario='$numero_identificacion_ac' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" fecha_atencion='$fecha_de_atencion_ac' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" codigo_cups_consulta='$codigo_de_la_consulta_ac' ";
		$query_servicios_reportados.=" ; ";
		$res_query_servicios_reportados=$coneccionBD->consultar2($query_servicios_reportados);
		
		if(count($res_query_servicios_reportados)>1)
		{
			foreach($res_query_servicios_reportados as $servicio_fue_reportado)
			{
				$fecha_servicio_reportado=$servicio_fue_reportado["fecha_validacion_exito"];
				$numero_remision_servicio_fue_reportado=$servicio_fue_reportado["numero_remision"];
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["04"].",".$array_grupo_validacion_rips["0401"].",".$array_detalle_validacion_rips["04_0401_040101"]." ...  AC".$numero_remision_servicio_fue_reportado." en la fecha ".$fecha_servicio_reportado.",".$nombre_archivo.",4,".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if en caso de que haya resultados	
	}
	//fin validacion hacia exitoso para servicios reportados
	
	
	//campo0ac aka 1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AC";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	//campo1ac aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010102"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query_prestador_en_bd="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."';";
		$res_query_prestador_en_bd=$coneccionBD->consultar2($query_prestador_en_bd);
		if(count($res_query_prestador_en_bd)==0 || !is_array($res_query_prestador_en_bd))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010301"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2ac aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$codigo_habilitacion_prestador=$campos[1];
		$numero_factura_actual=$campos[$numero_campo];
		if(!array_key_exists($campos[$numero_campo]."_".$codigo_habilitacion_prestador,$numeros_de_factura_por_ti_nit_ips))
		{
			$numeros_de_factura_por_ti_nit_ips[$campos[$numero_campo]."_".$codigo_habilitacion_prestador]=1;
		}
		
		$query_numero_factura_en_bd="SELECT * FROM gioss_numero_factura_validacion_eapb WHERE numero_factura='".$campos[$numero_campo]."' AND codigo_eapb='".$campos[0]."'  AND codigo_ips='".$campos[1]."' AND codigo_dpto='$departamento_filtro' AND codigo_mpio='$mpio_filtro_bd' AND ((estado_validacion='1') OR (estado_validacion='2' AND nombre_archivo_rips<>'$nombre_largo_rips' ));";
		$res_query_numero_factura_en_bd=$coneccionBD->consultar2($query_numero_factura_en_bd);
		if(count($res_query_numero_factura_en_bd)!=0 && is_array($res_query_numero_factura_en_bd))
		{
			//ES INFORMATIVA
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["02"].",".$array_grupo_validacion_rips["0205"].",".$array_detalle_validacion_rips["02_0205_020503"]." ... La FACTURA ".$campos[$numero_campo]." para la EAPB ".$campos[0]." de la IPS ".$campos[1]." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo3ac aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//relacionamiento
	$sexo_usuario="";
	$edad_usuario="";
	$unidad_medida_edad_usuario="";
	$campo_regimen="";
	$existencia_usuario_para_edad_sexo=false;
	$tipo_id_usuario=$campos[3];
	$id_usuario=$campos[4];
	
	if($campos[$numero_campo]!="")
	{
		
		
		$campo_ti_1=trim($campos[3]);
		$campo_id_2=trim($campos[4]);
		
		if(array_key_exists($campo_ti_1."_".$campo_id_2,$array_afiliados_duplicados))
		{
			$coincidencias_encontrado=array();
			$int_bool_encontro=preg_match("/(ENCONTRADO)/",$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2], $coincidencias_encontrado);
			if($int_bool_encontro==0)
			{
				$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2].="...ENCONTRADO";
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010612"]." ... ".$campos[$numero_campo]." ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	//campo4ac aka5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo5ac aka6
	$numero_campo=5;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		
		//calidad 4ac aka5
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AC".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
				
		$array_fecha_atencion=explode("/",$campos[$numero_campo]);
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$date_atencion=$array_fecha_atencion[2]."-".$array_fecha_atencion[1]."-".$array_fecha_atencion[0];
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_atencion),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_atencion t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	
	
	//campo6ac aka7
	$numero_campo=6;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		$query="select * from gioss_cups where codigo_procedimiento='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)!=0 && (intval($campos[$numero_campo])<890101 || intval($campos[$numero_campo])>890704 ) )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010313"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo7ac aka8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		if($campos[$numero_campo]!="01"
		   && $campos[$numero_campo]!="02"
		   && $campos[$numero_campo]!="03"
		   && $campos[$numero_campo]!="04"
		   && $campos[$numero_campo]!="05"
		   && $campos[$numero_campo]!="06"
		   && $campos[$numero_campo]!="07"
		   && $campos[$numero_campo]!="08"
		   && $campos[$numero_campo]!="09"
		   && $campos[$numero_campo]!="10")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010316"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo8ac aka9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!="01"
		   && $campos[$numero_campo]!="02"
		   && $campos[$numero_campo]!="03"
		   && $campos[$numero_campo]!="04"
		   && $campos[$numero_campo]!="05"
		   && $campos[$numero_campo]!="06"
		   && $campos[$numero_campo]!="07"
		   && $campos[$numero_campo]!="08"
		   && $campos[$numero_campo]!="09"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   && $campos[$numero_campo]!="13"
		   && $campos[$numero_campo]!="14"
		   && $campos[$numero_campo]!="15")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010315"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo9ac aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		
		//calidad
		
		if(substr($campos[$numero_campo],0,1)=="Z" && intval($campos[7])==10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010517"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(substr($campos[$numero_campo],0,1)!="Z" && intval($campos[7])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010518"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
		
	}//diferente de vacio
	
	//campo10ac aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	
	}//diferente de vacio
	
	//campo11ac aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo12ac aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo13ac aka14
	$numero_campo=13;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010308"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo14ac aka15
	$numero_campo=14;
	
	if($campos[$numero_campo]!="")
	{		
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		
		//calidad
		
		if((float)($campos[$numero_campo])<0 )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo15ac aka16
	$numero_campo=15;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		
		if((float)($campos[$numero_campo])<0 )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo16ac aka17
	$numero_campo=16;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	
	if($campo_fix!="")
	{
		if(strlen($campo_fix)>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campo_fix))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campo_fix=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campo_fix!="")
	{
		//calidad
		
		if((float)($campo_fix)<0 )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//FIN VALIDAR AC

//VALIDAR AH
function validar_eapb_ah($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados,&$numeros_de_factura_por_ti_nit_ips,$nombre_largo_rips,$departamento_filtro,$mpio_filtro_bd)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//validacion hacia exitoso para servicios reportados
	//Campo 2	Código Prestador de Servicios
	//Campo 3	Tipo de Identificacion usuario
	//Campo 4	Numero de identificación usuario
	//Campo 6	Fecha de Ingreso del usuario a observacion
	//Campo 7	Hora de Ingreso del usuario a observacion
	

	$codigo_prestador_de_servicios_ah=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[1] ));
	$tipo_identificacion_ah=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[2] ));
	$numero_identificacion_ah=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[3] ));
	$fecha_de_ingreso_a_observacion_ah=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[5] ));
	$hora_ingreso_a_observacion_ah=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[6] ));
	
	$fecha_de_ingreso_a_observacion_array_ah=explode("/",$fecha_de_ingreso_a_observacion_ah);
	
	
	
	if($codigo_prestador_de_servicios_ah!=""
	   && $tipo_identificacion_ah!=""
	   && $numero_identificacion_ah!=""
	   && $fecha_de_ingreso_a_observacion_ah!=""
	   && $hora_ingreso_a_observacion_ah!=""
	   && count($fecha_de_ingreso_a_observacion_array_ah)==3
	   && checkdate(intval($fecha_de_ingreso_a_observacion_array_ah[1]),intval($fecha_de_ingreso_a_observacion_array_ah[0]),intval($fecha_de_ingreso_a_observacion_array_ah[2]))
	   )
	{
		$fecha_de_ingreso_a_observacion_bd_ah=$fecha_de_ingreso_a_observacion_array_ah[2]."-".$fecha_de_ingreso_a_observacion_array_ah[1]."-".$fecha_de_ingreso_a_observacion_array_ah[0];
		
		$query_servicios_reportados="";
		$query_servicios_reportados.="SELECT * FROM gioss_archivo_cargado_ah WHERE ";
		$query_servicios_reportados.=" codigo_prestador_servicios_salud='$codigo_prestador_de_servicios_ah' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" tipo_identificacion_usuario='$tipo_identificacion_ah' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" numero_identificacion_usuario='$numero_identificacion_ah' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" fecha_ingreso='$fecha_de_ingreso_a_observacion_ah' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" hora_ingreso='$hora_ingreso_a_observacion_ah' ";
		$query_servicios_reportados.=" ; ";
		$res_query_servicios_reportados=$coneccionBD->consultar2($query_servicios_reportados);
		
		if(count($res_query_servicios_reportados)>1)
		{
			foreach($res_query_servicios_reportados as $servicio_fue_reportado)
			{
				$fecha_servicio_reportado=$servicio_fue_reportado["fecha_validacion_exito"];
				$numero_remision_servicio_fue_reportado=$servicio_fue_reportado["numero_remision"];
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["04"].",".$array_grupo_validacion_rips["0401"].",".$array_detalle_validacion_rips["04_0401_040101"]." ...  AH ".$numero_remision_servicio_fue_reportado." en la fecha ".$fecha_servicio_reportado.",".$nombre_archivo.",4,".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if en caso de que haya resultados	
	}
	//fin validacion hacia exitoso para servicios reportados
	
	//campo0ah aka 1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AH";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	
	
	//campo1ah aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010102"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query_prestador_en_bd="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='$campos[$numero_campo]';";
		$res_query_prestador_en_bd=$coneccionBD->consultar2($query_prestador_en_bd);
		if(count($res_query_prestador_en_bd)==0 || !is_array($res_query_prestador_en_bd))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010301"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2ah aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$codigo_habilitacion_prestador=$campos[1];
		$numero_factura_actual=$campos[$numero_campo];
		if(!array_key_exists($campos[$numero_campo]."_".$codigo_habilitacion_prestador,$numeros_de_factura_por_ti_nit_ips))
		{
			$numeros_de_factura_por_ti_nit_ips[$campos[$numero_campo]."_".$codigo_habilitacion_prestador]=1;
		}
		
		$query_numero_factura_en_bd="SELECT * FROM gioss_numero_factura_validacion_eapb WHERE numero_factura='".$campos[$numero_campo]."' AND codigo_eapb='".$campos[0]."'  AND codigo_ips='".$campos[1]."' AND codigo_dpto='$departamento_filtro' AND codigo_mpio='$mpio_filtro_bd' AND ((estado_validacion='1') OR (estado_validacion='2' AND nombre_archivo_rips<>'$nombre_largo_rips' ));";
		$res_query_numero_factura_en_bd=$coneccionBD->consultar2($query_numero_factura_en_bd);
		if(count($res_query_numero_factura_en_bd)!=0 && is_array($res_query_numero_factura_en_bd))
		{
			//ES INFORMATIVA
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["02"].",".$array_grupo_validacion_rips["0205"].",".$array_detalle_validacion_rips["02_0205_020503"]." ... La FACTURA ".$campos[$numero_campo]." para la EAPB ".$campos[0]." de la IPS ".$campos[1]." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo3ah aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//relacionamiento
	$sexo_usuario="";
	$edad_usuario="";
	$unidad_medida_edad_usuario="";
	$campo_regimen="";
	$existencia_usuario_para_edad_sexo=false;
	$tipo_id_usuario=$campos[3];
	$id_usuario=$campos[4];
	
	if($campos[$numero_campo]!="")
	{
		
		
		$campo_ti_1=trim($campos[3]);
		$campo_id_2=trim($campos[4]);
		if(array_key_exists($campo_ti_1."_".$campo_id_2,$array_afiliados_duplicados))
		{
			$coincidencias_encontrado=array();
			$int_bool_encontro=preg_match("/(ENCONTRADO)/",$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2], $coincidencias_encontrado);
			if($int_bool_encontro==0)
			{
				$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2].="...ENCONTRADO";
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010612"]." ... ".$campos[$numero_campo]." ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo4ah aka5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo5ah aka6
	$numero_campo=5;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>4;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010319"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo6ah aka7
	$numero_campo=6;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AH".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_ingreso=explode("/",$campos[$numero_campo]);
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$date_ingreso=$array_fecha_ingreso[2]."-".$array_fecha_ingreso[1]."-".$array_fecha_ingreso[0];
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_ingreso),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_ingreso t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha_egreso=explode("/",$campos[17]);
		
		if(count($array_fecha_egreso)==3)
		{
			if(checkdate(intval($array_fecha_egreso[1]),intval($array_fecha_egreso[0]),intval($array_fecha_egreso[2])))
			{
				$date_egreso=$array_fecha_egreso[2]."-".$array_fecha_egreso[1]."-".$array_fecha_egreso[0];
				
				$interval_ing_egr = date_diff(date_create($date_ingreso),date_create($date_egreso));
				$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
				if($tiempo_ie<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010514"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
			}
		}
	}//diferente de vacio
	
	//campo7ah aka8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=5)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010111"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$hora_array=explode(":",$campos[$numero_campo]);		
		$condicion_hora24=count($hora_array)!=2 && intval($hora_array[0])>23 && intval($hora_array[1])>59;
		if($condicion_hora24)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010202"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		$hora_ing=explode(":",$campos[7]);
		$hora_egr=explode(":",$campos[18]);
		
		if(intval($hora_ing[0])>23)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010525"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($hora_ing[1])>59)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010526"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(trim($campos[6])==trim($campos[17]) &&  (intval($hora_ing[0])>intval($hora_egr[0]) ||  (intval($hora_ing[0])==intval($hora_egr[0]) && intval($hora_ing[1])>=intval($hora_egr[1])) ) ) 
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010527"]." ... ".$campos[$numero_campo]."- fecha ".$campos[5].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	
	//campo8ah aka9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
				
		if($campos[$numero_campo]!="01"
		   && $campos[$numero_campo]!="02"
		   && $campos[$numero_campo]!="03"
		   && $campos[$numero_campo]!="04"
		   && $campos[$numero_campo]!="05"
		   && $campos[$numero_campo]!="06"
		   && $campos[$numero_campo]!="07"
		   && $campos[$numero_campo]!="08"
		   && $campos[$numero_campo]!="09"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   && $campos[$numero_campo]!="13"
		   && $campos[$numero_campo]!="14"
		   && $campos[$numero_campo]!="15")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010315"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo9ah aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		
		if(substr($campos[$numero_campo],0,1)=="Z")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010505"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
		
	}//diferente de vacio
	
	//campo10ah aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(substr($campos[$numero_campo],0,1)=="Z")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010505"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo11ah aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//calidad
		if(count($res_query)!=0 && intval($campos[8])>=1 && intval($campos[8])<=12 &&(substr($campos[$numero_campo],0,1)!="V" && substr($campos[$numero_campo],0,1)!="W" && substr($campos[$numero_campo],0,1)!="X" && substr($campos[$numero_campo],0,1)!="Y") )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010522"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(count($res_query)!=0 && intval($campos[8])>12 && (substr($campos[$numero_campo],0,1)=="V" || substr($campos[$numero_campo],0,1)=="W" || substr($campos[$numero_campo],0,1)=="X" || substr($campos[$numero_campo],0,1)=="Y")  )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010522"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		*/
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	
	
	
	//campo12ah aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo13ah aka14
	$numero_campo=13;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo14ah aka15
	$numero_campo=14;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		if(substr($campos[$numero_campo],0,1)=="Z")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010505"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo15ah aka16
	$numero_campo=15;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2;
		
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010320"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])==2 && $campos[16]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010542"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo16ah aka17
	$numero_campo=16;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if(intval($campos[15])==2 && $campos[$numero_campo]="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010407"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}

	if($campos[$numero_campo]!="")
	{
		//calidad
		
		if(intval($campos[15])==1 && $campos[$numero_campo]!="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010524"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		if(substr($campos[$numero_campo],0,1)=="Z")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010505"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo17ah aka18
	$numero_campo=17;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AH".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_ingreso=explode("/",$campos[6]);
		$array_fecha_egreso=explode("/",$campos[$numero_campo]);
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		
		$date_egreso=$array_fecha_egreso[2]."-".$array_fecha_egreso[1]."-".$array_fecha_egreso[0];
		
		
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_egreso),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_egreso t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(count($array_fecha_ingreso)==3)
		{
			if(checkdate(intval($array_fecha_ingreso[1]),intval($array_fecha_ingreso[0]),intval($array_fecha_ingreso[2])))
			{
				$date_ingreso=$array_fecha_ingreso[2]."-".$array_fecha_ingreso[1]."-".$array_fecha_ingreso[0];
				
				$interval_ing_egr = date_diff(date_create($date_ingreso),date_create($date_egreso));
				$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
				if($tiempo_ie<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010515"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
			}
		}
	}//diferente de vacio
	
	//campo18ah aka19
	$numero_campo=18;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	
	if($campo_fix!="")
	{
		if(strlen($campo_fix)!=5)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010111"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$hora_array=explode(":",$campo_fix);		
		$condicion_hora24=count($hora_array)!=2 && intval($hora_array[0])>23 && intval($hora_array[1])>59;
		if($condicion_hora24)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010202"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campo_fix=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campo_fix!="")
	{
		//calidad
		$hora_ing=explode(":",$campos[7]);
		$hora_egr=explode(":",$campo_fix);
		
		if(intval($hora_egr[0])>23)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010525"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($hora_egr[1])>59)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010526"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[6]==$campos[17] &&  (intval($hora_ing[0])>intval($hora_egr[0]) ||  (intval($hora_ing[0])==intval($hora_egr[0]) && intval($hora_ing[1])>=intval($hora_egr[1])) )  ) 
		{
			//echo "<script>alert('".$hora_ing[0]." ".$hora_ing[1]." ".$hora_egr[0]." ".$hora_egr[1]."');</script>";
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010528"]." ... ".$campo_fix."- fecha ".$campos[17].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}

//FIN VALIDAR AH



//VALIDAR AP
function validar_eapb_ap($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados,&$numeros_de_factura_por_ti_nit_ips,$nombre_largo_rips,$departamento_filtro,$mpio_filtro_bd)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//validacion hacia exitoso para servicios reportados
	//Campo 2	Código Prestador de Servicios
	//Campo 3	Tipo de Identificacion usuario
	//Campo 4	Numero de identificación usuario
	//Campo 5	Fecha de la atencion
	//Campo 7	Código de la consulta
	

	$codigo_prestador_de_servicios_ap=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[1] ));
	$tipo_identificacion_ap=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[2] ));
	$numero_identificacion_ap=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[3] ));
	$fecha_de_procedimiento_ap=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[4] ));
	$codigo_del_procedimiento_ap=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[6] ));
	
	$fecha_de_procedimiento_array_ap=explode("/",$fecha_de_procedimiento_ap);
	
	
	
	if($codigo_prestador_de_servicios_ap!=""
	   && $tipo_identificacion_ap!=""
	   && $numero_identificacion_ap!=""
	   && $fecha_de_procedimiento_ap!=""
	   && $codigo_del_procedimiento_ap!=""
	   && count($fecha_de_procedimiento_array_ap)==3
	   && checkdate(intval($fecha_de_procedimiento_array_ap[1]),intval($fecha_de_procedimiento_array_ap[0]),intval($fecha_de_procedimiento_array_ap[2]))
	   )
	{
		$fecha_de_atencion_bd_ap=$fecha_de_procedimiento_array_ap[2]."-".$fecha_de_procedimiento_array_ap[1]."-".$fecha_de_procedimiento_array_ap[0];
		
		$query_servicios_reportados="";
		$query_servicios_reportados.="SELECT * FROM gioss_archivo_cargado_ap WHERE ";
		$query_servicios_reportados.=" codigo_prestador_servicios_salud='$codigo_prestador_de_servicios_ap' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" tipo_identificacion_usuario='$tipo_identificacion_ap' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" numero_identificacion_usuario='$numero_identificacion_ap' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" fecha_procedimiento='$fecha_de_procedimiento_ap' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" codigo_cups_procedimiento='$codigo_del_procedimiento_ap' ";
		$query_servicios_reportados.=" ; ";
		$res_query_servicios_reportados=$coneccionBD->consultar2($query_servicios_reportados);
		
		if(count($res_query_servicios_reportados)>1)
		{
			foreach($res_query_servicios_reportados as $servicio_fue_reportado)
			{
				$fecha_servicio_reportado=$servicio_fue_reportado["fecha_validacion_exito"];
				$numero_remision_servicio_fue_reportado=$servicio_fue_reportado["numero_remision"];
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["04"].",".$array_grupo_validacion_rips["0401"].",".$array_detalle_validacion_rips["04_0401_040101"]." ...  AP ".$numero_remision_servicio_fue_reportado." en la fecha ".$fecha_servicio_reportado.",".$nombre_archivo.",4,".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if en caso de que haya resultados	
	}
	//fin validacion hacia exitoso para servicios reportados
	
	//campo0ap aka1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AP";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	
	//campo1ap aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010102"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	
	
		$query_prestador_en_bd="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='$campos[$numero_campo]';";
		$res_query_prestador_en_bd=$coneccionBD->consultar2($query_prestador_en_bd);
		if(count($res_query_prestador_en_bd)==0 || !is_array($res_query_prestador_en_bd))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010301"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2ap aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$codigo_habilitacion_prestador=$campos[1];
		$numero_factura_actual=$campos[$numero_campo];
		if(!array_key_exists($campos[$numero_campo]."_".$codigo_habilitacion_prestador,$numeros_de_factura_por_ti_nit_ips))
		{
			$numeros_de_factura_por_ti_nit_ips[$campos[$numero_campo]."_".$codigo_habilitacion_prestador]=1;
		}
		
		$query_numero_factura_en_bd="SELECT * FROM gioss_numero_factura_validacion_eapb WHERE numero_factura='".$campos[$numero_campo]."' AND codigo_eapb='".$campos[0]."'  AND codigo_ips='".$campos[1]."' AND codigo_dpto='$departamento_filtro' AND codigo_mpio='$mpio_filtro_bd' AND ((estado_validacion='1') OR (estado_validacion='2' AND nombre_archivo_rips<>'$nombre_largo_rips' ));";
		$res_query_numero_factura_en_bd=$coneccionBD->consultar2($query_numero_factura_en_bd);
		if(count($res_query_numero_factura_en_bd)!=0 && is_array($res_query_numero_factura_en_bd))
		{
			//ES INFORMATIVA
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["02"].",".$array_grupo_validacion_rips["0205"].",".$array_detalle_validacion_rips["02_0205_020503"]." ... La FACTURA ".$campos[$numero_campo]." para la EAPB ".$campos[0]." de la IPS ".$campos[1]." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	
	
	//campo3ap aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio 
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//relacionamiento
	$sexo_usuario="";
	$edad_usuario="";
	$unidad_medida_edad_usuario="";
	$campo_regimen="";
	$existencia_usuario_para_edad_sexo=false;
	$tipo_id_usuario=$campos[3];
	$id_usuario=$campos[4];
	
	if($campos[$numero_campo]!="")
	{
		
		
		$campo_ti_1=trim($campos[3]);
		$campo_id_2=trim($campos[4]);
		if(array_key_exists($campo_ti_1."_".$campo_id_2,$array_afiliados_duplicados))
		{
			$coincidencias_encontrado=array();
			$int_bool_encontro=preg_match("/(ENCONTRADO)/",$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2], $coincidencias_encontrado);
			if($int_bool_encontro==0)
			{
				$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2].="...ENCONTRADO";
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010612"]." ... ".$campos[$numero_campo]." ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo4ap aka5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo5ap aka6
	$numero_campo=5;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AP".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_procedimiento=explode("/",$campos[$numero_campo]);
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$date_procedimiento=$array_fecha_procedimiento[2]."-".$array_fecha_procedimiento[1]."-".$array_fecha_procedimiento[0];
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_procedimiento),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_procedimiento t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	
	
	
	//campo6ap aka7
	$numero_campo=6;
	
	$array_res_campo6_aka7=array();
	$query="select * from gioss_cups where codigo_procedimiento='".$campos[$numero_campo]."'; ";
	$res_query=$coneccionBD->consultar2($query);
	$array_res_campo6_aka7=$res_query;
		
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010313"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])>=890101 && intval($campos[$numero_campo])<=890704)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010541"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo7ap aka8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010308"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo8ap aka9
	$numero_campo=8;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>5;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010317"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo9ap aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>5;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010317"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//calidad 735300, 735910, 735930, 735931, 735980
	if($campos[$numero_campo]=="" &&  intval($campos[6])>=735300 && intval($campos[6])<=740300 )
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010405"]." ... ".$campos[$numero_campo]."- cod CUPS ".$campos[6].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		if($campos[$numero_campo]!="" &&  intval($campos[6])<735300 || intval($campos[6])>740300 )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010520"]." ... ".$campos[$numero_campo]."- cod CUPS ".$campos[6].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	//campo10ap aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	
	//calidad
		
	if($campos[$numero_campo]=="" && count($array_res_campo6_aka7)!=0)
	{
		if(intval($array_res_campo6_aka7[0]["codigo_grupo_cups"])==11)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010406"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}
	
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		
		
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
		
	}//diferente de vacio
	
	
	//campo11ap aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
	}//diferente de vacio
	
	
	
	//campo12ap aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//calidad
		
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
	}//diferente de vacio
	
	
	
	
	//campo13ap aka14
	$numero_campo=13;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	if($campo_fix!="")
	{
		if(strlen($campo_fix)>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campo_fix))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	if($campo_fix=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	if($campo_fix!="")
	{
		//calidad verificar valor sea positivo no  negativo
		
		if((float)($campo_fix)<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//FIN VALIDAR AP

//VALIDAR AU
function validar_eapb_au($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados,&$numeros_de_factura_por_ti_nit_ips,$nombre_largo_rips,$departamento_filtro,$mpio_filtro_bd)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//validacion hacia exitoso para servicios reportados
	//Campo 2	Código Prestador de Servicios
	//Campo 3	Tipo de Identificacion usuario
	//Campo 4	Numero de identificación usuario
	//Campo 5	Fecha de Ingreso del usuario a observacion
	//Campo 6	Hora de Ingreso del usuario a observacion
	

	$codigo_prestador_de_servicios_au=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[1] ));
	$tipo_identificacion_au=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[2] ));
	$numero_identificacion_au=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[3] ));
	$fecha_de_ingreso_a_observacion_au=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[4] ));
	$hora_ingreso_a_observacion_au=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[5] ));
	
	$fecha_de_ingreso_a_observacion_array_au=explode("/",$fecha_de_ingreso_a_observacion_au);
	
	
	
	if($codigo_prestador_de_servicios_au!=""
	   && $tipo_identificacion_au!=""
	   && $numero_identificacion_au!=""
	   && $fecha_de_ingreso_a_observacion_au!=""
	   && $hora_ingreso_a_observacion_au!=""
	   && count($fecha_de_ingreso_a_observacion_array_au)==3
	   && checkdate(intval($fecha_de_ingreso_a_observacion_array_au[1]),intval($fecha_de_ingreso_a_observacion_array_au[0]),intval($fecha_de_ingreso_a_observacion_array_au[2]))
	   )
	{
		$fecha_de_ingreso_a_observacion_bd_au=$fecha_de_ingreso_a_observacion_array_au[2]."-".$fecha_de_ingreso_a_observacion_array_au[1]."-".$fecha_de_ingreso_a_observacion_array_au[0];
		
		$query_servicios_reportados="";
		$query_servicios_reportados.="SELECT * FROM gioss_archivo_cargado_au WHERE ";
		$query_servicios_reportados.=" codigo_prestador_servicios_salud='$codigo_prestador_de_servicios_au' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" tipo_identificacion_usuario='$tipo_identificacion_au' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" numero_identificacion_usuario='$numero_identificacion_au' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" fecha_ingreso='$fecha_de_ingreso_a_observacion_au' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" hora_ingreso='$hora_ingreso_a_observacion_au' ";
		$query_servicios_reportados.=" ; ";
		$res_query_servicios_reportados=$coneccionBD->consultar2($query_servicios_reportados);
		
		if(count($res_query_servicios_reportados)>1)
		{
			foreach($res_query_servicios_reportados as $servicio_fue_reportado)
			{
				$fecha_servicio_reportado=$servicio_fue_reportado["fecha_validacion_exito"];
				$numero_remision_servicio_fue_reportado=$servicio_fue_reportado["numero_remision"];
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["04"].",".$array_grupo_validacion_rips["0401"].",".$array_detalle_validacion_rips["04_0401_040101"]." ...  AU ".$numero_remision_servicio_fue_reportado." en la fecha ".$fecha_servicio_reportado.",".$nombre_archivo.",4,".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if en caso de que haya resultados	
	}
	//fin validacion hacia exitoso para servicios reportados
	
	//campo0au aka 1
	
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AU";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	
	
	//campo1au aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010102"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query_prestador_en_bd="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='$campos[$numero_campo]';";
		$res_query_prestador_en_bd=$coneccionBD->consultar2($query_prestador_en_bd);
		if(count($res_query_prestador_en_bd)==0 || !is_array($res_query_prestador_en_bd))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010301"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2au aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$codigo_habilitacion_prestador=$campos[1];
		$numero_factura_actual=$campos[$numero_campo];
		if(!array_key_exists($campos[$numero_campo]."_".$codigo_habilitacion_prestador,$numeros_de_factura_por_ti_nit_ips))
		{
			$numeros_de_factura_por_ti_nit_ips[$campos[$numero_campo]."_".$codigo_habilitacion_prestador]=1;
		}
		
		$query_numero_factura_en_bd="SELECT * FROM gioss_numero_factura_validacion_eapb WHERE numero_factura='".$campos[$numero_campo]."' AND codigo_eapb='".$campos[0]."'  AND codigo_ips='".$campos[1]."' AND codigo_dpto='$departamento_filtro' AND codigo_mpio='$mpio_filtro_bd' AND ((estado_validacion='1') OR (estado_validacion='2' AND nombre_archivo_rips<>'$nombre_largo_rips' ));";
		$res_query_numero_factura_en_bd=$coneccionBD->consultar2($query_numero_factura_en_bd);
		if(count($res_query_numero_factura_en_bd)!=0 && is_array($res_query_numero_factura_en_bd))
		{
			//ES INFORMATIVA
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["02"].",".$array_grupo_validacion_rips["0205"].",".$array_detalle_validacion_rips["02_0205_020503"]." ... La FACTURA ".$campos[$numero_campo]." para la EAPB ".$campos[0]." de la IPS ".$campos[1]." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo3au aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//relacionamiento
		
	$sexo_usuario="";
	$edad_usuario="";
	$unidad_medida_edad_usuario="";
	$campo_regimen="";
	$existencia_usuario_para_edad_sexo=false;
	$tipo_id_usuario=$campos[3];
	$id_usuario=$campos[4];
	
	if($campos[$numero_campo]!="")
	{
		
		
		$campo_ti_1=trim($campos[3]);
		$campo_id_2=trim($campos[4]);
		if(array_key_exists($campo_ti_1."_".$campo_id_2,$array_afiliados_duplicados))
		{
			$coincidencias_encontrado=array();
			$int_bool_encontro=preg_match("/(ENCONTRADO)/",$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2], $coincidencias_encontrado);
			if($int_bool_encontro==0)
			{
				$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2].="...ENCONTRADO";
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010612"]." ... ".$campos[$numero_campo]." ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo4au aka5
	$numero_campo=4;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo5au aka6
	$numero_campo=5;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AU".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_ingreso=explode("/",$campos[$numero_campo]);
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$date_ingreso=$array_fecha_ingreso[2]."-".$array_fecha_ingreso[1]."-".$array_fecha_ingreso[0];
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_ingreso),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_ingreso t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha_egreso=explode("/",preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[14] ));
		if(count($array_fecha_egreso)==3)
		{
			if(checkdate(intval($array_fecha_egreso[1]),intval($array_fecha_egreso[0]),intval($array_fecha_egreso[2])))
			{
				$date_egreso=$array_fecha_egreso[2]."-".$array_fecha_egreso[1]."-".$array_fecha_egreso[0];
				
				$interval_ing_egr = date_diff(date_create($date_ingreso),date_create($date_egreso));
				$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
				if($tiempo_ie<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010514"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
			}
		}
	}//diferente de vacio
	
	
	
	
	//campo6au aka7
	$numero_campo=6;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!="01"
		   && $campos[$numero_campo]!="02"
		   && $campos[$numero_campo]!="03"
		   && $campos[$numero_campo]!="04"
		   && $campos[$numero_campo]!="05"
		   && $campos[$numero_campo]!="06"
		   && $campos[$numero_campo]!="07"
		   && $campos[$numero_campo]!="08"
		   && $campos[$numero_campo]!="09"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   && $campos[$numero_campo]!="13"
		   && $campos[$numero_campo]!="14"
		   && $campos[$numero_campo]!="15")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010315"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo7au aka8
	$numero_campo=7;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		
		//calidad
		if((substr($campos[$numero_campo],0,1)=="V" || substr($campos[$numero_campo],0,1)=="W" || substr($campos[$numero_campo],0,1)=="X" || substr($campos[$numero_campo],0,1)=="Y") )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010523"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		
		//condicion verificacion sexo edad usuario para diagnostico
		$query_validacion_sexo_edad="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query_validacion_sexo_edad=$coneccionBD->consultar2($query_validacion_sexo_edad);
		if(count($res_query_validacion_sexo_edad)>0 && is_array($res_query_validacion_sexo_edad))
		{
			$sexo_consultado_del_diagnostico=$res_query_validacion_sexo_edad[0]["cod_sexo"];
			$edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_minima"]);
			$edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["edad_maxima"]);
			$unidad_edad_minima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_minima"]);
			$unidad_edad_maxima_diagnostico=intval($res_query_validacion_sexo_edad[0]["cod_unidad_edad_maxima"]);
			
			//VALIDACION DE SEXO
			if($sexo_usuario!="")
			{
				//caso 1
				if($sexo_consultado_del_diagnostico=="A")
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($sexo_consultado_del_diagnostico=="F")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
				//caso 3
				else if($sexo_consultado_del_diagnostico=="M")
				{
					if($sexo_usuario!=$sexo_consultado_del_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}//fin if
				}
			}//fin if si el sexo del usuario existe
			
			//VALIDACION EDAD
			if($edad_usuario!="")
			{
				if(intval($unidad_medida_edad_usuario)==2
				   || intval($unidad_medida_edad_usuario)==3 )
				{
					$unidad_medida_edad_usuario="1";
					$edad_usuario="0";
				}
				else if(intval($unidad_medida_edad_usuario)!=1
					&& intval($unidad_medida_edad_usuario)!=2
					&& intval($unidad_medida_edad_usuario)!=3)
				{
					$unidad_medida_edad_usuario="1";
				}
			
				//caso 1
				if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico==999)
				{
					//no hay inconsistencia
				}
				//caso 2
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico==999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."   edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 3
				else if($edad_minima_diagnostico==0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($edad_usuario)>$edad_maxima_diagnostico
					   && intval($unidad_medida_edad_usuario)==1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."     edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
				//caso 4
				else if($edad_minima_diagnostico!=0 && $edad_maxima_diagnostico!=999)
				{
					if(intval($unidad_medida_edad_usuario)!=1)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
					else if(intval($edad_usuario)<$edad_minima_diagnostico || intval($edad_usuario)>$edad_maxima_diagnostico)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."    edad usuario $edad_usuario,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}//fin si hay edad usuario
			
		}//fin if
		
		//fin condicion verificacion sexo edad usuario para diagnostico
		
	}//diferente de vacio
	
	//campo8au aka9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//calidad
		if(count($res_query)!=0 && intval($campos[7])>=1 && intval($campos[7])<=12 &&(substr($campos[$numero_campo],0,1)!="V" && substr($campos[$numero_campo],0,1)!="W" && substr($campos[$numero_campo],0,1)!="X" && substr($campos[$numero_campo],0,1)!="Y") )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010522"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		*/
		
		/*
		
		if(count($res_query)!=0 && (intval($campos[7])<1 || intval($campos[7])>12) &&(substr($campos[$numero_campo],0,1)=="V" || substr($campos[$numero_campo],0,1)=="W" || substr($campos[$numero_campo],0,1)=="X" || substr($campos[$numero_campo],0,1)=="Y") )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010522"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		*/
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo9au aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		//calidad
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo10au aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo11au aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010308"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo12au aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010320"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])==2 && $campos[13]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_01542"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo13au aka14
	$numero_campo=13;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//calidad
	if($campos[$numero_campo]=="" && intval($campos[12])==2)
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010407"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		if($campos[$numero_campo]!="" && intval($campos[12])==1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010524"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	//campo14au aka15
	$numero_campo=14;
	$campos[$numero_campo] = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AU".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_ingreso=explode("/",$campos[5]);
		$array_fecha_egreso=explode("/",$campos[$numero_campo]);
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		
		$date_egreso=$array_fecha_egreso[2]."-".$array_fecha_egreso[1]."-".$array_fecha_egreso[0];
		
		
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_egreso),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_egreso t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(count($array_fecha_ingreso)==3)
		{
			if(checkdate(intval($array_fecha_ingreso[1]),intval($array_fecha_ingreso[0]),intval($array_fecha_ingreso[2])))
			{
				$date_ingreso=$array_fecha_ingreso[2]."-".$array_fecha_ingreso[1]."-".$array_fecha_ingreso[0];
		
				$interval_ing_egr = date_diff(date_create($date_ingreso),date_create($date_egreso));
				$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
				if($tiempo_ie<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010515"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
			}
		}
	}//diferente de vacio
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//FIN VALIDAR AU

//VALIDAR AN
function validar_eapb_an($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados,&$numeros_de_factura_por_ti_nit_ips,$nombre_largo_rips,$departamento_filtro,$mpio_filtro_bd)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//validacion hacia exitoso para servicios reportados
	//Campo 2	Código Prestador de Servicios
	//Campo 3	Tipo de Identificacion madre
	//Campo 4	Numero de identificación madre
	//Campo 5	Fecha de nacimiento
	//Campo 6	Hora de nacimeinto
	

	$codigo_prestador_de_servicios_an=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[1] ));
	$tipo_identificacion_an=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[2] ));
	$numero_identificacion_an=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[3] ));
	$fecha_de_nacimiento_an=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[4] ));
	$hora_nacimiento_an=trim(preg_replace("/[^A-Za-z0-9:\-\/]/", "", $campos[5] ));
	
	$fecha_nacimiento_array_an=explode("/",$fecha_de_nacimiento_an);
	
	
	
	if($codigo_prestador_de_servicios_an!=""
	   && $tipo_identificacion_an!=""
	   && $numero_identificacion_an!=""
	   && $fecha_de_nacimiento_an!=""
	   && $hora_nacimiento_an!=""
	   && count($fecha_nacimiento_array_an)==3
	   && checkdate(intval($fecha_nacimiento_array_an[1]),intval($fecha_nacimiento_array_an[0]),intval($fecha_nacimiento_array_an[2]))
	   )
	{
		$fecha_de_ingreso_a_observacion_bd_an=$fecha_nacimiento_array_an[2]."-".$fecha_nacimiento_array_an[1]."-".$fecha_nacimiento_array_an[0];
		
		$query_servicios_reportados="";
		$query_servicios_reportados.="SELECT * FROM gioss_archivo_cargado_an WHERE ";
		$query_servicios_reportados.=" codigo_prestador_servicios_salud='$codigo_prestador_de_servicios_an' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" tipo_identificacion_madre='$tipo_identificacion_an' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" numero_identificacion_madre='$numero_identificacion_an' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" fecha_ingreso='$fecha_de_nacimiento_an' ";
		$query_servicios_reportados.=" AND ";
		$query_servicios_reportados.=" hora_ingreso='$hora_nacimiento_an' ";
		$query_servicios_reportados.=" ; ";
		$res_query_servicios_reportados=$coneccionBD->consultar2($query_servicios_reportados);
		
		if(count($res_query_servicios_reportados)>1)
		{
			foreach($res_query_servicios_reportados as $servicio_fue_reportado)
			{
				$fecha_servicio_reportado=$servicio_fue_reportado["fecha_validacion_exito"];
				$numero_remision_servicio_fue_reportado=$servicio_fue_reportado["numero_remision"];
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["04"].",".$array_grupo_validacion_rips["0401"].",".$array_detalle_validacion_rips["04_0401_040101"]." ...  AN ".$numero_remision_servicio_fue_reportado." en la fecha ".$fecha_servicio_reportado.",".$nombre_archivo.",4,".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if en caso de que haya resultados	
	}
	//fin validacion hacia exitoso para servicios reportados	
	
	//campo0an aka1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AN";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	
	
	//campo1an aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010102"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query_prestador_en_bd="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='$campos[$numero_campo]';";
		$res_query_prestador_en_bd=$coneccionBD->consultar2($query_prestador_en_bd);
		if(count($res_query_prestador_en_bd)==0 || !is_array($res_query_prestador_en_bd))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010301"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2an aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$codigo_habilitacion_prestador=$campos[1];
		$numero_factura_actual=$campos[$numero_campo];
		if(!array_key_exists($campos[$numero_campo]."_".$codigo_habilitacion_prestador,$numeros_de_factura_por_ti_nit_ips))
		{
			$numeros_de_factura_por_ti_nit_ips[$campos[$numero_campo]."_".$codigo_habilitacion_prestador]=1;
		}
		
		$query_numero_factura_en_bd="SELECT * FROM gioss_numero_factura_validacion_eapb WHERE numero_factura='".$campos[$numero_campo]."' AND codigo_eapb='".$campos[0]."'  AND codigo_ips='".$campos[1]."' AND codigo_dpto='$departamento_filtro' AND codigo_mpio='$mpio_filtro_bd' AND ((estado_validacion='1') OR (estado_validacion='2' AND nombre_archivo_rips<>'$nombre_largo_rips' ));";
		$res_query_numero_factura_en_bd=$coneccionBD->consultar2($query_numero_factura_en_bd);
		if(count($res_query_numero_factura_en_bd)!=0 && is_array($res_query_numero_factura_en_bd))
		{
			//ES INFORMATIVA
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["02"].",".$array_grupo_validacion_rips["0205"].",".$array_detalle_validacion_rips["02_0205_020503"]." ... La FACTURA ".$campos[$numero_campo]." para la EAPB ".$campos[0]." de la IPS ".$campos[1]." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo3an aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//relacionamiento
		
	$sexo_usuario="";
	$edad_usuario="";
	$unidad_medida_edad_usuario="";
	$campo_regimen="";
	$existencia_usuario_para_edad_sexo=false;
	$tipo_id_usuario=$campos[3];
	$id_usuario=$campos[4];
	
	if($campos[$numero_campo]!="")
	{
		//relacionamiento
		
		$campo_ti_1=trim($campos[3]);
		$campo_id_2=trim($campos[4]);
		if(array_key_exists($campo_ti_1."_".$campo_id_2,$array_afiliados_duplicados))
		{
			$coincidencias_encontrado=array();
			$int_bool_encontro=preg_match("/(ENCONTRADO)/",$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2], $coincidencias_encontrado);
			if($int_bool_encontro==0)
			{
				$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2].="...ENCONTRADO";
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010612"]." ... ".$campos[$numero_campo]." ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	
	}//diferente de vacio
	
	//campo4an aka5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo5an aka6
	$numero_campo=5;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
	
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AN".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_nacimiento=explode("/",$campos[$numero_campo]);
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		$date_nacimiento=$array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0];
		
		$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
		
		$interval = date_diff(date_create($date_nacimiento),date_create($date_remision));
		$tiempo= (float)($interval->format("%r%a"));
		//echo "<script>alert('$date_nacimiento t $date_remision s $tiempo ');</script>";
		if($tiempo<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha_muerte=explode("/",$campos[13]);
		
		if(count($array_fecha_muerte)==3)
		{
			if(checkdate(intval($array_fecha_muerte[1]),intval($array_fecha_muerte[0]),intval($array_fecha_muerte[2])))
			{
				$date_muerte=$array_fecha_muerte[2]."-".$array_fecha_muerte[1]."-".$array_fecha_muerte[0];
			
				$interval_ing_egr = date_diff(date_create($date_nacimiento),date_create($date_muerte));
				$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
				if($tiempo_ie<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010543"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
			}
		}
	
	}//diferente de vacio
	
	//campo6an aka7
	$numero_campo=6;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=5)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010111"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$hora_array=explode(":",$campos[$numero_campo]);		
		$condicion_hora24=count($hora_array)!=2 && intval($hora_array[0])>23 && intval($hora_array[1])>59;
		if($condicion_hora24)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010202"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		$hora_nacimiento=explode(":",$campos[6]);
		$hora_muerte=explode(":",$campos[14]);
		
		if(intval($hora_nacimiento[0])>23)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010525"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($hora_nacimiento[1])>59)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010526"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		if(count($hora_muerte)==2)
		{
			if($campos[5]==$campos[13] &&  (intval($hora_nacimiento[0])>intval($hora_muerte[0]) || (intval($hora_nacimiento[0])==intval($hora_muerte[0]) && intval($hora_nacimiento[1])>=intval($hora_muerte[1])) ) ) 
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010527"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//si hay muerte
	}//diferente de vacio
	
	//campo7an aka8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010116"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		
		if(intval($campos[$numero_campo])>45)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010529"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo8an aka9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010320"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo9an aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=2;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010320"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo10an aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010117"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo11an aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo12an aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(count($res_query)==0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010314"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		/*
		//condicion verificacion sexo edad usuario para diagnostico
		$entro_a_consultar_sexo_usuario=false;
		if($sexo_usuario=="" && $existencia_usuario_para_edad_sexo=true)
		{
			$num_filas_resultado=0;
			$resultados_query_usuarios=array();
			if($campo_regimen==1 || $campo_regimen==6)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==2 || $campo_regimen==7)
			{
				$query_bd="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			if($campo_regimen==5)
			{
				$query_bd="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado = '".$id_usuario."' AND tipo_id_afiliado = '".$tipo_id_usuario."' ;";
				$resultados_query_usuarios=$coneccionBD->consultar2($query_bd);
				$num_filas_resultado=count($resultados_query_usuarios);
			}
			
			if($num_filas_resultado>0)
			{
				$sexo_usuario=$resultados_query_usuarios[0]["sexo"];
			}
			
			$entro_a_consultar_sexo_usuario=true;
		}
		
		if($edad_usuario!="" && $unidad_medida_edad_usuario!="")
		{
			if($unidad_medida_edad_usuario!=1)
			{
				$edad_usuario="0";
			}
		}
		
		$query="select * from gioss_diagnostico_ciex where codigo_ciex='".$campos[$numero_campo]."'; ";
		$res_query=$coneccionBD->consultar2($query);
		if(
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==true
		    )//condicion para cuando le toco buscar el sexo en la BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario=="" && $unidad_medida_edad_usuario==""
		    )//condicion para cuando no hay edad ni unidad medida edad y no busco el sexo en BD
		   ||
		   (count($res_query)!=0 && $sexo_usuario!="" && $res_query[0]["cod_sexo"]!=$sexo_usuario && $res_query[0]["cod_sexo"]!="A"
		    && $entro_a_consultar_sexo_usuario==false && $edad_usuario!="" && $unidad_medida_edad_usuario!=""
		    && (intval($edad_usuario)<intval($res_query[0]["edad_minima"]) || intval($edad_usuario)>intval($res_query[0]["edad_maxima"]) )
		    )//condicion para cuando hay edad y unidad de medida edad en el archivo de usuarios y no busco el sexo en la BD
		   )
		{
			
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010519"]." ... codigo diagnostico ".$campos[$numero_campo]."  sexo usuario $sexo_usuario ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
			
		}
		*/
	}//diferente de vacio
	
	
	//calidad
	if($campos[$numero_campo]=="" && $campos[13]!="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010410"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo13an aka14
	$numero_campo=13;
	$fecha_formato_valida=false;
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=10)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010104"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$array_fecha=explode("/",$campos[$numero_campo]);
		if(count($array_fecha)==3)
		{
			if(!checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			else
			{
				$fecha_formato_valida=true;
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010201"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="" && $campos[12]!="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010408"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="" && $fecha_formato_valida)
	{
		//calidad
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AN".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		if($campos[12]!="" && checkdate(intval($array_fecha[1]),intval($array_fecha[0]),intval($array_fecha[2])))
		{
			$array_fecha_nacimiento=explode("/",$campos[5]);
			$array_fecha_muerte=explode("/",$campos[$numero_campo]);
			$array_fecha_remision=explode("/",$fecha_remision_ct);
			
			date_default_timezone_set ("America/Bogota");
			$fecha_actual = date('Y-m-d');
			
			
			$date_muerte=$array_fecha_muerte[2]."-".$array_fecha_muerte[1]."-".$array_fecha_muerte[0];
			
			
			
			$date_remision=$array_fecha_remision[2]."-".$array_fecha_remision[1]."-".$array_fecha_remision[0];
			
			$interval = date_diff(date_create($date_muerte),date_create($date_remision));
			$tiempo= (float)($interval->format("%r%a"));
			//echo "<script>alert('$date_egreso t $date_remision s $tiempo ');</script>";
			if($tiempo<0)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010614"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			
			if(count($array_fecha_nacimiento)==3)
			{
				if(checkdate(intval($array_fecha_nacimiento[1]),intval($array_fecha_nacimiento[0]),intval($array_fecha_nacimiento[2])))
				{
					$date_nacimiento=$array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0];
				
					$interval_ing_egr = date_diff(date_create($date_nacimiento),date_create($date_muerte));
					$tiempo_ie= (float)($interval_ing_egr->format("%r%a"));
					if($tiempo_ie<0)
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}
						$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010522"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
						$hubo_errores=true;
					}
				}
			}
		}//si la fecha muerte no es vacia
	}//diferente de vacio
	
	//campo14an aka15
	$numero_campo=14;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	
	if($campo_fix!="")
	{
		if(strlen($campo_fix)!=5)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010111"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$hora_array=explode(":",$campo_fix);		
		$condicion_hora24=count($hora_array)!=2 && intval($hora_array[0])>23 && intval($hora_array[1])>59;
		if($condicion_hora24)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010202"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}///diferente de vacio
	
	if($campo_fix=="" && $campos[13]!="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010409"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campo_fix!="")
	{
		//calidad
		$hora_nacimiento=explode(":",$campos[6]);
		$hora_muerte=explode(":",$campo_fix);
		
		if(count($hora_muerte)==2)
		{
			if(intval($hora_muerte[0])>23)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010525"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
			
			if(intval($hora_muerte[1])>59)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010526"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		
			if($campos[5]==$campos[14] &&  (intval($hora_nacimiento[0])>intval($hora_muerte[0]) || (intval($hora_nacimiento[0])==intval($hora_muerte[0]) && intval($hora_nacimiento[1])>=intval($hora_muerte[1])) ) ) 
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010532"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//si hay muerte
	}//diferente de vacio
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//VALIDAR AN

//VALIDAR AM
function validar_eapb_am($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva,&$array_afiliados_duplicados,&$numeros_de_factura_por_ti_nit_ips,$nombre_largo_rips,$departamento_filtro,$mpio_filtro_bd)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	//campo0am aka1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AM";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	
	
	//campo1am aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010102"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$query_prestador_en_bd="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='$campos[$numero_campo]';";
		$res_query_prestador_en_bd=$coneccionBD->consultar2($query_prestador_en_bd);
		if(count($res_query_prestador_en_bd)==0 || !is_array($res_query_prestador_en_bd))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010301"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2am aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$codigo_habilitacion_prestador=$campos[1];
		$numero_factura_actual=$campos[$numero_campo];
		if(!array_key_exists($campos[$numero_campo]."_".$codigo_habilitacion_prestador,$numeros_de_factura_por_ti_nit_ips))
		{
			$numeros_de_factura_por_ti_nit_ips[$campos[$numero_campo]."_".$codigo_habilitacion_prestador]=1;
		}
		
		$query_numero_factura_en_bd="SELECT * FROM gioss_numero_factura_validacion_eapb WHERE numero_factura='".$campos[$numero_campo]."' AND codigo_eapb='".$campos[0]."'  AND codigo_ips='".$campos[1]."' AND codigo_dpto='$departamento_filtro' AND codigo_mpio='$mpio_filtro_bd' AND ((estado_validacion='1') OR (estado_validacion='2' AND nombre_archivo_rips<>'$nombre_largo_rips' ));";
		$res_query_numero_factura_en_bd=$coneccionBD->consultar2($query_numero_factura_en_bd);
		if(count($res_query_numero_factura_en_bd)!=0 && is_array($res_query_numero_factura_en_bd))
		{
			//ES INFORMATIVA
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["02"].",".$array_grupo_validacion_rips["0205"].",".$array_detalle_validacion_rips["02_0205_020503"]." ... La FACTURA ".$campos[$numero_campo]." para la EAPB ".$campos[0]." de la IPS ".$campos[1]." ,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo3am aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=$campos[$numero_campo]!="CC" && $campos[$numero_campo]!="CE" && $campos[$numero_campo]!="PA" && $campos[$numero_campo]!="RC";
		$condicion2=$campos[$numero_campo]!="TI" && $campos[$numero_campo]!="AS" && $campos[$numero_campo]!="MS"  && $campos[$numero_campo]!="NU";
		if($condicion1 && $condicion2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010306"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//relacionamiento
		
		$campo_ti_1=trim($campos[3]);
		$campo_id_2=trim($campos[4]);
		if(array_key_exists($campo_ti_1."_".$campo_id_2,$array_afiliados_duplicados))
		{
			$coincidencias_encontrado=array();
			$int_bool_encontro=preg_match("/(ENCONTRADO)/",$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2], $coincidencias_encontrado);
			if($int_bool_encontro==0)
			{
				$array_afiliados_duplicados[$campo_ti_1."_".$campo_id_2].="...ENCONTRADO";
			}
		}
		else
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010612"]." ... ".$campos[$numero_campo]." ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo4am aka5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo5am aka6 edad
	$numero_campo=5;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010114"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad edad 
	
		if(intval($campos[$numero_campo])>120 && intval($campos[6])==1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010510"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])>11 && intval($campos[6])==2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010511"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])>29 && intval($campos[6])==3)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010512"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>120)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010335"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo6am aka7 unidad medida edad
	$numero_campo=6;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010308"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo]."- Tipo us ".$campos[3].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	
	//campo7am aka8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>30)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010110"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="" && intval($campos[8])==2)
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010403"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	
	//campo8am aka9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010107"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$condicion1=intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2;
		if($condicion1)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010320"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	
	//campo9am aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="" && intval($campos[9])==2)
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010403"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	
	//campo10am aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="" && intval($campos[8])==2)
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010403"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	
	//campo11am aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>20)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010101"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="" && intval($campos[8])==2)
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010403"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	/*
	//calidad
	if($campos[$numero_campo]=="" && intval($campos[6])==2)
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010532"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	*/
	
	//campo12am aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>5)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010118"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo13am aka14
	$numero_campo=13;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo14am aka15
	$numero_campo=14;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	
	if($campo_fix!="")
	{
		if(strlen($campo_fix)>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campo_fix))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campo_fix=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campo_fix!="")
	{
		//calidad
		if((float)($campo_fix)<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//VALIDAR AM

//VALIDAR AV
function validar_eapb_av($campos,$nlinea,$array_tipo_validacion_rips,$array_grupo_validacion_rips,$array_detalle_validacion_rips,$cod_eapb_reportante,$nombre_archivo,$cod_eapb,$numero_de_remision,$fecha_remision,$archivos_subidos,$ruta_nueva)
{
	$coneccionBD = new conexion();
	$hubo_errores=false;
	$errores_campos="";
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters_actual="";
	    $campo_ver_characters_actual=str_replace(array("/",".","-",":"," "),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters_bool=false;
	    
	    $campo_ver_characters_actual=preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim(procesar_tildes_eapb($campo_ver_characters_actual)) );
	    $campo_ver_characters_bool=ctype_alnum($campo_ver_characters_actual);
	   
	    
	    if($campo_ver_characters_actual=="")
	    {
		$campo_ver_characters_bool=true;
	    }
	    
	    $campo_temporal="";
	    $campo_temporal= preg_replace("/[^A-Za-z0-9:.\-\/]/", "", trim($campos[$cont_campos]) );
	    
	    if($campo_ver_characters_bool==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["03"].",".$array_grupo_validacion_rips["0301"].",".$array_detalle_validacion_rips["03_0301_030102"]." ... ".$campo_temporal.",".$nombre_archivo.",".($cont_campos+1).",".($nlinea+1);
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//campo0av aka1
	$numero_campo=0;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=6)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010105"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_alnum($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010205"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if($campos[$numero_campo]!=$cod_eapb_reportante)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010304"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad cargue eapb
		
		
		
		//relacionamiento
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			$existe_eapb=false;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				if($cont_reportados==0)
				{
					$linea_tmp = fgets($file_tmp);
					$esta_linea= explode("\n", $linea_tmp)[0];
					$campos_tmp = explode(",", $esta_linea);		
					if($campos_tmp[0]==$campos[$numero_campo])
					{
						$existe_eapb=true;
					}
					break;
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
			if($existe_eapb==false)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0106"].",".$array_detalle_validacion_rips["01_0106_010601"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
				$hubo_errores=true;
			}
		}//fin if us existe eapb
		
		/*
		//verifica si el prestador esta diferente al prestador en otras lineas del archivo
		$rutaTemporal = '../TEMPORALES/';
		$sigla_archivo_actual="AV";
		$ruta_mismo_archivo=$rutaTemporal.$ruta_nueva."/".$sigla_archivo_actual.$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe[$sigla_archivo_actual.$numero_de_remision]) && file_exists($ruta_mismo_archivo))
		{
			$es_diferente=false;
			$linea_diferente=1;
			$cont_reportados=0;
			$file_tmp = fopen($ruta_mismo_archivo, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[0]!=$campos[$numero_campo])
				{
					$es_diferente=true;
					$linea_diferente=$cont_reportados+1;
					//se pone aca para que diga las lineas en las que  es diferente
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010534"]." ... es diferente ".$campos[$numero_campo]." al prestador en la linea $linea_diferente,".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
					$hubo_errores=true;
				}
				$cont_reportados++;
				
			}
			fclose($file_tmp);
			
		}//fin if
		//fin si el prestador esta diferente al prestador otras lineas en su mismo archivo
		*/
	}//diferente de vacio
	
	
	//campo1av aka2
	$numero_campo=1;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=4)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010106"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}		
		
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AV".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		if($campos[$numero_campo]!=$array_fecha_remision[2])
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010329"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo2av aka3
	$numero_campo=2;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])!=2)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010103"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!ctype_digit($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010203"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(intval($campos[$numero_campo])>12)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010331"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		$nombres_archivos_a_revisar_existe =array();
		$rutaTemporal = '../TEMPORALES/';
		foreach($archivos_subidos as $archivo)
		{
			$nombre_sin_ext=explode(".",explode("/",$archivo)[count(explode("/",$archivo))-1])[0];
			$nombres_archivos_a_revisar_existe[$nombre_sin_ext]=$nombre_sin_ext;
		}
		
		$rutaTemporal = '../TEMPORALES/';
		$fecha_remision_ct=$fecha_remision;
		$ruta_ct=$rutaTemporal.$ruta_nueva."/"."CT".$numero_de_remision.".txt";
		if(isset($nombres_archivos_a_revisar_existe["CT".$numero_de_remision]) && file_exists($ruta_ct))
		{
			//echo "<script>alert('existe CT')</script>";
			
			$cont_reportados=0;
			$file_tmp = fopen($ruta_ct, 'r') or exit("No se pudo abrir el archivo");
			while (!feof($file_tmp)) 
			{
				$linea_tmp = fgets($file_tmp);
				$esta_linea= explode("\n", $linea_tmp)[0];
				$campos_tmp = explode(",", $esta_linea);		
				if($campos_tmp[2]=="AV".$numero_de_remision)
				{
					$fecha_remision_ct=$campos_tmp[1];
				}
				$cont_reportados++;
			}
			fclose($file_tmp);
			
		}//fin if esta ct
		
		$array_fecha_remision=explode("/",$fecha_remision_ct);
		if(intval($campos[$numero_campo])>$array_fecha_remision[1])
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0103"].",".$array_detalle_validacion_rips["01_0103_010330"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	//campo3av aka4
	$numero_campo=3;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	//campo4av aka5
	$numero_campo=4;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo6av aka7
	$numero_campo=6;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	//campo7av aka8
	$numero_campo=7;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	//campo8av aka9
	$numero_campo=8;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	//campo9av aka10
	$numero_campo=9;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo10av aka11
	$numero_campo=10;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo11av aka12
	$numero_campo=11;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo12av aka13
	$numero_campo=12;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo13av aka14
	$numero_campo=13;
	
	if($campos[$numero_campo]!="")
	{
		if(strlen($campos[$numero_campo])>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
		
		if(!is_numeric($campos[$numero_campo]))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campos[$numero_campo]=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campos[$numero_campo]!="")
	{
		//calidad
		if((float)($campos[$numero_campo])<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campos[$numero_campo].",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	//campo14av aka15
	$numero_campo=14;
	
	$campo_fix = preg_replace("/[^A-Za-z0-9:.\-\/]/", "", $campos[$numero_campo] );
	
	if($campo_fix!="")
	{
		if(strlen($campo_fix)>15)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0101"].",".$array_detalle_validacion_rips["01_0101_010108"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	
	
		if(!is_numeric($campo_fix))
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0102"].",".$array_detalle_validacion_rips["01_0102_010204"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	if($campo_fix=="")
	{
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}
		$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0104"].",".$array_detalle_validacion_rips["01_0104_010401"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
		$hubo_errores=true;
	}
	
	if($campo_fix!="")
	{
		//calidad
		if((float)($campo_fix)<0)
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}
			$errores_campos.=$array_tipo_validacion_rips["01"].",".$array_grupo_validacion_rips["0105"].",".$array_detalle_validacion_rips["01_0105_010508"]." ... ".$campo_fix.",".$nombre_archivo.",".($numero_campo+1).",".($nlinea+1);
			$hubo_errores=true;
		}
	}//diferente de vacio
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}
//VALIDAR AV
?>