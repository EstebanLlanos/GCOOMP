<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

require '../librerias_externas/Smarty-3.1.17/libs/Smarty.class.php';
include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

$smarty = new Smarty;
$coneccionBD = new conexion();

function alphanumericAndSpace( $string )
{
    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string);
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
$smarty->display('act_afiliados_rp.html.tpl');

if(isset($_FILES["archivo_info"]) && isset($_POST["nombres_columnas_tablas"]) && isset($_POST["nombre_tabla"]) && $_POST["nombres_columnas_tablas"]!="" && $_POST["nombre_tabla"]!=""
&& isset($_POST["llaves"]) && $_POST["llaves"]!="")
{
	$rutaTemporal = '../TEMPORALES/';
	$mensaje_div="";
	
	//parte preparacion datos info tabla
	
	//quita comas
	$nombre_tabla=preg_replace("/[^A-Za-z0-9:_]/", "", $_POST["nombre_tabla"]);
	
	//no quita comas para hacer explode por la coma
	$string_llaves=preg_replace("/[^A-Za-z0-9:_,.]/", "", $_POST["llaves"] );		
	$array_llaves=explode(",",$string_llaves);
	$array_index_llaves_en_numero_columnas=array();
	
	//no quita comas para hacer explode por la coma
	$string_columnas_tablas=preg_replace("/[^A-Za-z0-9:_,.]/", "", $_POST["nombres_columnas_tablas"] );
	$array_columnas_tablas=explode(",",$string_columnas_tablas);
	
	foreach($array_llaves as $llave)
	{
		foreach($array_columnas_tablas as $indice=>$columna)
		{
			if($llave==$columna)
			{
				$array_index_llaves_en_numero_columnas[$llave]=$indice;
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
	
	//archivos que se crean
	$ruta_1=$rutaTemporal.$nombre_tabla.".sql";
	$ruta_2=$rutaTemporal.$nombre_tabla.".error.csv";
	$archivo_queries = fopen($ruta_1, "w") or die("fallo la creacion del archivo");
	$archivo_error= fopen($ruta_2, "w") or die("fallo la creacion del archivo");
	
	$cont_reset_div=0;
	$cont_linea=0;
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
			$resultado_busqueda=$coneccionBD->consultar2($sql_verificar);
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
						$sql_upsert.=$array_columnas_tablas[$cont_nombres_columnas]."='".$array_campos_procesados[$cont_nombres_columnas]."'";
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
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error($sql_upsert, $error_bd);
				
				}
				catch (Exception $e) {}
			}//fin if numero de campos  es adecuado
			else
			{
				$error_bd="ERROR: el numero de campos no es correcto. ".count($campos);
				$bool_funciono=true;
			}//error cuando no cumple con el numero de campos

			
			$mensaje="";
			if($bool_funciono==false)
			{
				$mensaje="<p style='color:green;'> Fila para $nombre_tabla de salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				
				$aciertos++;
			}
			else
			{
				$mensaje="<p style='color:red'>Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				$errores++;
				$error_para_txt.="Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos).".\n".$error_bd."\n".$sql_upsert." \n";				
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
			}
			else
			{
				$final="<p style='color:blue;>Se ha terminado de procesar el archivo.</p>";
				echo $var_script_html;
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$final.$mensaje.$mensaje_aciertos.$mensaje_errores.$botones_descarga_html."\";</script>";
				
			}
				
		}//fin if linea no vacia
		
		$cont_linea++;
	}//fin while lectura archivo
	fclose($archivo_queries);
	fclose($archivo_cargar);

	fwrite($archivo_error, $error_para_txt);
	fclose($archivo_error);
	
}//if se subio archivo

?>