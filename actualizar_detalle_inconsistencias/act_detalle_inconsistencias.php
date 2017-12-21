<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$smarty = new Smarty;
$coneccionBD = new conexion();
$coneccionBD->crearConexion();

function alphanumericAndSpace( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s,: ;\-@.\/]/', '', $string);
}

session_start();

if (!isset($_SESSION['usuario']) && !isset($_SESSION['tipo_perfil']))
{
	
	header ("Location: ../index.php");
}

$menu= $_SESSION['menu_logueo_html'];
$nombre= $_SESSION['nombre_completo'];

$perfil_usuario_actual= $_SESSION['tipo_perfil'];
$entidad_salud_usuario_actual= $_SESSION['entidad_asociada_al_nick'];

$tipo_id=$_SESSION['tipo_id'];
$identificacion=$_SESSION['identificacion'];

session_write_close();

$mensaje="";
$mostrarResultado="none";

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('act_detalle_inconsistencias.html.tpl');

//VERIFICADOR NOMBRE PARA NO CONFUNDIR ARCHIVOS
$CORRESPONDE_AL_TIPO_DE_ARCHIVO=false;
if(isset($_FILES["archivo_info"])
   && isset($_POST["tipo_archivo"])
   )
{
    $tipo_archivo=$_POST["tipo_archivo"];
    $nombre_fichero = $_FILES['archivo_info']['name'];
    
    if($tipo_archivo=="3374"
       && (strripos($nombre_fichero, 'rips') !== false
	   || strripos($nombre_fichero, '3374') !== false
	   )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
    if($tipo_archivo=="4505"
       && (strripos($nombre_fichero, 'pyp') !== false
	   || strripos($nombre_fichero, '4505') !== false
	   )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
    if($tipo_archivo=="4725"
        && (strripos($nombre_fichero, 'vih') !== false
	    || strripos($nombre_fichero, '4725') !== false
	    )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
    if($tipo_archivo=="0247"
       && (strripos($nombre_fichero, 'cancer') !== false
	   || strripos($nombre_fichero, '0247') !== false
	   )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
    if($tipo_archivo=="2463"
       && (strripos($nombre_fichero, 'erc') !== false
	   || strripos($nombre_fichero, '2463') !== false
	   )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
    if($tipo_archivo=="0123"
       && (strripos($nombre_fichero, 'hemofilia') !== false
	   || strripos($nombre_fichero, '0123') !== false
	   )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
    
    if($tipo_archivo=="1393"
       && (strripos($nombre_fichero, 'artritis') !== false
	   || strripos($nombre_fichero, '1393') !== false
	   )
       )
    {
	    $CORRESPONDE_AL_TIPO_DE_ARCHIVO=true;
    }
}
//FIN VERIFICADOR NOMBRE PARA NO CONFUNDIR ARCHIVOS

$cont_linea=0;
if(isset($_FILES["archivo_info"])
   && isset($_POST["tipo_archivo"])
   && ($_POST["tipo_archivo"]=="3374"
       || $_POST["tipo_archivo"]=="4505"
       || $_POST["tipo_archivo"]=="4725"
       || $_POST["tipo_archivo"]=="0247"
       || $_POST["tipo_archivo"]=="2463"
       || $_POST["tipo_archivo"]=="0123"
       || $_POST["tipo_archivo"]=="1393"
       )
   && $_FILES["archivo_info"]["error"]==0
   && $CORRESPONDE_AL_TIPO_DE_ARCHIVO==true
   )
{
	$rutaTemporal = '../TEMPORALES/';
	$mensaje_div="";
	
	//parte preparacion datos info tabla
	$tipo_archivo=$_POST["tipo_archivo"];
	//quita comas
	//$nombre_tabla=preg_replace("/[^A-Za-z0-9:_]/", "", $_POST["nombre_tabla"]);
	$nombre_tabla="";
	if($tipo_archivo=="3374")
	{
		$nombre_tabla="gioss_detalle_inconsistencias_rips";
	}
	if($tipo_archivo=="4505")
	{
		$nombre_tabla="gioss_detalle_inconsistecias_4505";
	}
	if($tipo_archivo=="4725")
	{
		$nombre_tabla="gioss_detalle_inconsistencia_r4725_sida_vih";
	}
	if($tipo_archivo=="0247")
	{
		$nombre_tabla="gioss_detalle_inconsistencia_0247_CANCER";
	}
	if($tipo_archivo=="2463")
	{
		$nombre_tabla="gioss_detalle_inconsistencia_2463_erc";
	}
	if($tipo_archivo=="0123")
	{
		$nombre_tabla="gioss_detalle_inconsistencia_0123_hf";
	}
	
	if($tipo_archivo=="1393")
	{
		$nombre_tabla="gioss_detalle_inconsistencia_1393_arte";
	}
	
	//no quita comas para hacer explode por la coma
	//$string_llaves=preg_replace("/[^A-Za-z0-9:_,.]/", "", $_POST["llaves"] );	
	$string_llaves="";
	if($tipo_archivo=="3374")
	{
		$string_llaves="cod_tipo_validacion,codigo_grupo_inconsistencia,cod_detalle_inconsistencia";
	}
	if($tipo_archivo=="4505")
	{
		$string_llaves="codigo_tipo_inconsistencia,codigo_grupo_inconsistencia,codigo_detalle_inconsistencia";
	}
	//cancer,erc,vih,hemofilia, suscolumnas tienen nombre similar, aunque puede cambiar, revisar antes de modificar
	if($tipo_archivo=="4725")
	{
		$string_llaves="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia";
	}
	if($tipo_archivo=="0247")
	{
		$string_llaves="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia";
	}
	if($tipo_archivo=="2463")
	{
		$string_llaves="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia";
	}
	if($tipo_archivo=="0123")
	{
		$string_llaves="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia";
	}
	if($tipo_archivo=="1393")
	{
		$string_llaves="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia";
	}
	$array_llaves=explode(",",$string_llaves);
	$array_index_llaves_en_numero_columnas=array();
	
	//no quita comas para hacer explode por la coma
	//$string_columnas_tablas=preg_replace("/[^A-Za-z0-9:_,.]/", "", $_POST["nombres_columnas_tablas"] );
	$string_columnas_tablas="";
	if($tipo_archivo=="3374")
	{
		$string_columnas_tablas="cod_tipo_validacion,codigo_grupo_inconsistencia,cod_detalle_inconsistencia,descripcion_detalle_inconsistencia";
	}
	if($tipo_archivo=="4505")
	{
		$string_columnas_tablas="codigo_tipo_inconsistencia,codigo_grupo_inconsistencia,codigo_detalle_inconsistencia,descripcion_inconsistencia";
	}
	if($tipo_archivo=="4725")
	{
		$string_columnas_tablas="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia,descripcion_detalle_inconsistencia";
	}
	if($tipo_archivo=="0247")
	{
		$string_columnas_tablas="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia,descripcion_detalle_inconsistencia";
	}
	if($tipo_archivo=="2463")
	{
		$string_columnas_tablas="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia,descripcion_detalle_inconsistencia";
	}
	if($tipo_archivo=="0123")
	{
		$string_columnas_tablas="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia,descripcion_detalle_inconsistencia";
	}
	if($tipo_archivo=="1393")
	{
		$string_columnas_tablas="codigo_tipo_validacion,codigo_grupo_validacion,codigo_detalle_inconsistencia,descripcion_detalle_inconsistencia";
	}
	$array_columnas_tablas=explode(",",$string_columnas_tablas);
	
	//indice llaves
	foreach($array_llaves as $llave)
	{
		foreach($array_columnas_tablas as $indice=>$columna)
		{
			if(trim($llave)==trim($columna))
			{
				$array_index_llaves_en_numero_columnas[trim($llave)]=$indice;
			}
		}//fin columnas
	}//fin foreach llave
	
	//fin preparacion datos info tabla
	
	$archivo_entidades_salud=$_FILES["archivo_info"];
	$ruta_archivo_entidades_salud = $rutaTemporal . $archivo_entidades_salud['name'];
	move_uploaded_file($archivo_entidades_salud['tmp_name'], $ruta_archivo_entidades_salud);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_entidades_salud)); 
	$archivo_cargar = fopen($ruta_archivo_entidades_salud, 'r') or exit("No se pudo abrir el archivo con los datos");
	
	if($lineas_del_archivo==0)
	{
	    echo "<script>alert('el archivo esta vacio');</script>";
	}
	
	//archivos que se crean
	$ruta_1=$rutaTemporal.$nombre_tabla.".sql";
	$ruta_2=$rutaTemporal.$nombre_tabla.".error.csv";
	$archivo_queries = fopen($ruta_1, "w") or die("fallo la creacion del archivo");
	$archivo_error= fopen($ruta_2, "w") or die("fallo la creacion del archivo");
	
	$cont_reset_div=0;
	$aciertos=0;
	$errores=0;
	$error_para_txt="";
	$sql_upsert="";
	
	while (!feof($archivo_cargar)) 
	{
		$linea = fgets($archivo_cargar);
		$linea_res = str_replace("\n","",$linea);
		
		$linea_res = str_replace("á","a",$linea_res);
		$linea_res = str_replace("é","e",$linea_res);
		$linea_res = str_replace("í","i",$linea_res);
		$linea_res = str_replace("ó","o",$linea_res);
		$linea_res = str_replace("ú","u",$linea_res);
		$linea_res = str_replace("ñ","n",$linea_res);
		$linea_res = str_replace("Á","A",$linea_res);
		$linea_res = str_replace("É","E",$linea_res);
		$linea_res = str_replace("Í","I",$linea_res);
		$linea_res = str_replace("Ó","O",$linea_res);
		$linea_res = str_replace("Ú","U",$linea_res);
		$linea_res = str_replace("Ñ","N",$linea_res);
		$linea_res = str_replace(" "," ",$linea_res);
		$linea_res = str_replace("'","",$linea_res);
		$linea_res = str_replace('"',"",$linea_res);
		$linea_res= alphanumericAndSpace($linea_res);	
		$campos = explode(",",$linea_res);
		
		
		if($linea_res!="")
		{
			
			
			$bool_funciono=false;
			
			
			$sql_verificar="";
			$sql_verificar.="SELECT * FROM  $nombre_tabla WHERE ";
			
			$cont_llave=0;
			foreach($array_index_llaves_en_numero_columnas as $nombre_llave=>$posicion_campo)
			{
				$campo_llave="";
				$numero_campo_pk=$posicion_campo;							
				
				$campo_llave = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[$numero_campo_pk] );
				$campo_llave = str_replace("\n","",$campo_llave);
				$campo_llave = str_replace("\"","",$campo_llave);
				$campo_llave = str_replace("'","",$campo_llave);
				$campo_llave = trim($campo_llave);				
				
				if($cont_llave!=0)
				{
					$sql_verificar.=" AND ";
				}
				$sql_verificar.=" $nombre_llave='".$campo_llave."' ";
				
				$cont_llave++;
			}//fin foreach para las llaves
			$sql_verificar.=";";
			
			$existe_en_bd=false;
			$resultado_busqueda=$coneccionBD->consultar2_no_crea_cierra($sql_verificar);
			if(count($resultado_busqueda)>0)
			{
				$existe_en_bd=true;
			}
			
			
			
			//solo introduce en la base de datos si el numero de campos es correcto
			if(count($campos)==count($array_columnas_tablas))
			{
				if($existe_en_bd==false)
				{
					$sql_upsert="";
					//INICIA QUERY INSERT "nombre_tabla_aqui"
					$sql_upsert.="insert into ".$nombre_tabla;
					$sql_upsert.="(";
					$cont_nombres_columnas=0;
					while($cont_nombres_columnas<count($array_columnas_tablas))
					{
						if($cont_nombres_columnas!=0)
						{
							$sql_upsert.=",";
						}
						$sql_upsert.=$array_columnas_tablas[$cont_nombres_columnas];
						$cont_nombres_columnas++;
					}
					$sql_upsert.=")";
					$sql_upsert.="values";
					$sql_upsert.="(";
					$cont_campos=0;
					while($cont_campos < count($campos))
					{
						if($cont_campos!=3)
						{
							$campo_procesado="";
									
							$campo_procesado = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[$cont_campos] );
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
							$campo_procesado = trim($campo_procesado);
							
							if($cont_campos!=0)
							{
								$sql_upsert.=",";
							}
							$sql_upsert.="'".utf8_decode($campo_procesado)."'";
						}//fin if
						else
						{
							$cod_tipo_inconsistencia = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[0] );
							$cod_tipo_inconsistencia = str_replace("\n","",$cod_tipo_inconsistencia);
							$cod_tipo_inconsistencia = str_replace("\"","",$cod_tipo_inconsistencia);
							$cod_tipo_inconsistencia = str_replace("'","",$cod_tipo_inconsistencia);
							$cod_tipo_inconsistencia = trim($cod_tipo_inconsistencia);
							
							$cod_grupo_inconsistencia = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[1] );
							$cod_grupo_inconsistencia = str_replace("\n","",$cod_grupo_inconsistencia);
							$cod_grupo_inconsistencia = str_replace("\"","",$cod_grupo_inconsistencia);
							$cod_grupo_inconsistencia = str_replace("'","",$cod_grupo_inconsistencia);
							$cod_grupo_inconsistencia = trim($cod_grupo_inconsistencia);
							
							$cod_detalle_inconsistencia = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[2] );
							$cod_detalle_inconsistencia = str_replace("\n","",$cod_detalle_inconsistencia);
							$cod_detalle_inconsistencia = str_replace("\"","",$cod_detalle_inconsistencia);
							$cod_detalle_inconsistencia = str_replace("'","",$cod_detalle_inconsistencia);
							$cod_detalle_inconsistencia = trim($cod_detalle_inconsistencia);
							
							$string_codigo_para_desc=$cod_tipo_inconsistencia."_".$cod_grupo_inconsistencia."_".$cod_detalle_inconsistencia.";;";
							
							$campo_procesado="";
									
							$campo_procesado = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[$cont_campos] );
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
							$campo_procesado = trim($campo_procesado);
							
							if($cont_campos!=0)
							{
								$sql_upsert.=",";
							}
							$sql_upsert.="'$string_codigo_para_desc".utf8_decode($campo_procesado)."'";
						}//fin else
						
						$cont_campos++;
					}
					$sql_upsert.=");";
					//FIN QUERY INSERT "nombre_tabla_aqui"
					
					
					
				}//fin insert si no existe
				else
				{
					$array_campos_procesados=array();
					$cont_campos=0;
					while($cont_campos < count($campos))
					{
						$campo_procesado="";
								
						$campo_procesado = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[$cont_campos] );
						$campo_procesado = str_replace("\n","",$campo_procesado);
						$campo_procesado = str_replace("\"","",$campo_procesado);
						$campo_procesado = str_replace("'","",$campo_procesado);
						$campo_procesado = trim($campo_procesado);
						
						$array_campos_procesados[]=utf8_decode($campo_procesado);
						
						$cont_campos++;
					}
				
					$sql_upsert="";
					//INICIA QUERY UPDATE "nombre_tabla_aqui"
					$sql_upsert.="UPDATE $nombre_tabla SET ";
					
					$cont_nombres_columnas=0;
					while($cont_nombres_columnas<count($array_columnas_tablas))
					{
						if($cont_nombres_columnas!=0)
						{
							$sql_upsert.=",";
						}
						
						if($cont_nombres_columnas!=3)
						{
							$sql_upsert.=$array_columnas_tablas[$cont_nombres_columnas]."='".$array_campos_procesados[$cont_nombres_columnas]."'";
						}
						else
						{
							$cod_tipo_inconsistencia = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[0] );
							$cod_tipo_inconsistencia = str_replace("\n","",$cod_tipo_inconsistencia);
							$cod_tipo_inconsistencia = str_replace("\"","",$cod_tipo_inconsistencia);
							$cod_tipo_inconsistencia = str_replace("'","",$cod_tipo_inconsistencia);
							$cod_tipo_inconsistencia = trim($cod_tipo_inconsistencia);
							
							$cod_grupo_inconsistencia = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[1] );
							$cod_grupo_inconsistencia = str_replace("\n","",$cod_grupo_inconsistencia);
							$cod_grupo_inconsistencia = str_replace("\"","",$cod_grupo_inconsistencia);
							$cod_grupo_inconsistencia = str_replace("'","",$cod_grupo_inconsistencia);
							$cod_grupo_inconsistencia = trim($cod_grupo_inconsistencia);
							
							$cod_detalle_inconsistencia = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[2] );
							$cod_detalle_inconsistencia = str_replace("\n","",$cod_detalle_inconsistencia);
							$cod_detalle_inconsistencia = str_replace("\"","",$cod_detalle_inconsistencia);
							$cod_detalle_inconsistencia = str_replace("'","",$cod_detalle_inconsistencia);
							$cod_detalle_inconsistencia = trim($cod_detalle_inconsistencia);
							
							$string_codigo_para_desc=$cod_tipo_inconsistencia."_".$cod_grupo_inconsistencia."_".$cod_detalle_inconsistencia.";;";
							
							$sql_upsert.=$array_columnas_tablas[$cont_nombres_columnas]."='$string_codigo_para_desc".$array_campos_procesados[$cont_nombres_columnas]."'";
						}
						$cont_nombres_columnas++;
					}
					
					$sql_upsert.=" WHERE ";
					
					$cont_llave=0;
					foreach($array_index_llaves_en_numero_columnas as $nombre_llave=>$posicion_campo)
					{
						$campo_llave="";
						$numero_campo_pk=$posicion_campo;							
						
						$campo_llave = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[$numero_campo_pk] );
						$campo_llave = str_replace("\n","",$campo_llave);
						$campo_llave = str_replace("\"","",$campo_llave);
						$campo_llave = str_replace("'","",$campo_llave);
						$campo_llave = trim($campo_llave);
						
						if($cont_llave!=0)
						{
							$sql_upsert.=" AND ";
						}
						$sql_upsert.=" $nombre_llave='".$campo_llave."' ";
						
						$cont_llave++;
					}//fin foreach para las llaves
					
					$sql_upsert.=";";
					//FIN QUERY UPDATE "nombre_tabla_aqui"
				}//fin update si existe
				
				fwrite($archivo_queries, $sql_upsert."\n");
									
				$error_bd="";
				try
				{
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert, $error_bd);
				
				}
				catch (Exception $e) {}
			}//fin if numero de campos  es adecuado
			else
			{
				$sql_upsert="no se genero query";
				$error_bd="ERROR: el numero de campos no es correcto. ".count($campos);
				$bool_funciono=true;
			}//error cuando no cumple con el numero de campos

			
			$mensaje="";
			if($bool_funciono==false)
			{
				$mensaje="<p style='color:green;'> Fila para $nombre_tabla de salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				if($error_bd!="")
				{
					$error_para_txt.="WTF: Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos).".\n".$error_bd."\n".$sql_upsert."\n\n";
				}
				$error_para_txt.=$sql_verificar."\n\n";
				
				$aciertos++;
			}
			else
			{
				$mensaje="<p style='color:red'>Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				$errores++;
				$error_para_txt.="Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos).".\n".$error_bd."\n".$sql_upsert."\n\n";				
			}
			
			$mensaje_aciertos ="<p style='color:green;'>Numero de lineas insertadas correctamente: ".$aciertos."  </p>";
			$mensaje_errores ="<p style='color:red;'>Numero de lineas que no pudieron insertarse en la tabla $nombre_tabla: ".$errores."  </p>";
			
			$botones_descarga_html="";
			$var_script_html="";
			$var_script_html.="<script>var ruta1='".$ruta_1."'; </script>";
			$var_script_html.="<script>var ruta2='".$ruta_2."'; </script>";
			
			$botones_descarga_html.="<p><input type='button' class='btn btn-success color_boton' value='Descargar archivo SQL queries' onclick='download_archivo(ruta1);' /></p>";
			$botones_descarga_html.="<p><input type='button' class='btn btn-success color_boton' value='Descargar archivo errores BD' onclick='download_archivo(ruta2);' /></p>";
			
			if($cont_linea < $lineas_del_archivo-1)
			{
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$mensaje.$mensaje_aciertos.$mensaje_errores."\";</script>";
				ob_flush();
			    flush();
			}
			else
			{
				$final="<p style='color:blue;>Se ha terminado de procesar el archivo.</p>";
				echo $var_script_html;
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$final.$mensaje.$mensaje_aciertos.$mensaje_errores.$botones_descarga_html."\";</script>";
				ob_flush();
			    flush();
				
			}
				
		}//fin if linea no vacia
		
		$cont_linea++;
	}//fin while lectura archivo
	fclose($archivo_queries);
	fclose($archivo_cargar);

	fwrite($archivo_error, $error_para_txt);
	fclose($archivo_error);
	
	
	
}//if se subio archivo
else if(isset($_FILES["archivo_info"]) &&  $CORRESPONDE_AL_TIPO_DE_ARCHIVO==false )
{
	echo "<script>document.getElementById('mensaje').innerHTML=\"el archivo debe contener el nombre de la norma  en su nombre usando<br> cancer, vih, pyp, rips, erc, hemofilia, artritis o su numero  \";</script>";
}
else if(isset($_FILES["archivo_info"]) && $_FILES["archivo_info"]["error"]==0 )
{
	echo "<script>document.getElementById('mensaje').innerHTML=\"seleccione el tipo de archivo al cual subira las descripciones de los detalles de inconsistencias\";</script>";
}
else if(isset($_FILES["archivo_info"]) && $_FILES["archivo_info"]["error"]>0)
{
	echo "<script>document.getElementById('mensaje').innerHTML=\"seleccione el archivo a subir\";</script>";
}


$coneccionBD->cerrar_conexion();

?>