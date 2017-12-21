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
    return preg_replace('/[^a-zA-Z0-9\s,@]/', '', $string);
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
$smarty->display('act_tipo_ent.html.tpl');

if(isset($_FILES["archivo_info"]) && $_FILES["archivo_info"]['tmp_name']!="")
{
	$rutaTemporal = '../TEMPORALES/';
	$mensaje_div="";
	
	$archivo_subido=$_FILES["archivo_info"];
	$ruta_archivo_subido = $rutaTemporal . $archivo_subido['name'];
    move_uploaded_file($archivo_subido['tmp_name'], $ruta_archivo_subido);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_subido)); 
	$archivo_cargar = fopen($ruta_archivo_subido, 'r') or exit("No se pudo abrir el archivo con las entidades de salud");
	
	//archivos que se crean
	$ruta_1=$rutaTemporal."tabla_tipo_entidad.sql";
	$ruta_2=$rutaTemporal."tabla_tipo_entidad.error.csv";
	$archivo_queries = fopen($ruta_1, "w") or die("fallo la creacion del archivo");
	$archivo_error= fopen($ruta_2, "w") or die("fallo la creacion del archivo");
	
	$cont_reset_div=0;
	$cont_linea=0;
	$aciertos=0;
	$errores=0;
	$error_para_txt="";
	
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
		
		/*
		  codigo_tipo_entidad character varying(320) NOT NULL,
		  descripcion character varying(320) NOT NULL,
		*/		
		
		if($linea_res!="")
		{
			$bool_funciono=false;
			$campo_llave_principal="";
			$numero_campo_pk=0;
						
			if($numero_campo_pk == count($campos)-1 )
			{
				$campo_llave_principal = substr($campos[$numero_campo_pk], 0, strlen($campos[$numero_campo_pk])-1);
				$campo_llave_principal = str_replace("\n","",$campo_llave_principal);
				$campo_llave_principal = str_replace("\"","",$campo_llave_principal);
				$campo_llave_principal = str_replace("'","",$campo_llave_principal);
			}
			else
			{
				$campo_llave_principal = str_replace("\n","",$campos[$numero_campo_pk]);
				$campo_llave_principal = str_replace("\"","",$campo_llave_principal);
				$campo_llave_principal = str_replace("'","",$campo_llave_principal);
			}
				
			$sql_verificar="SELECT codigo_tipo_entidad FROM  gioss_tipo_entidad WHERE codigo_tipo_entidad='".$campo_llave_principal."' ";
			
			$existe_en_bd=false;
			$resultado_busqueda=$coneccionBD->consultar2($sql_verificar);
			if(count($resultado_busqueda)>0)
			{
				$existe_en_bd=true;
			}
			
			//entra si el numero de campos es correcto
			if(count($campos)==2)
			{
				if($existe_en_bd==false)
				{
					$sql_upsert="";
					//INICIA QUERY INSERT gioss_tipo_entidad
					$sql_upsert.="insert into gioss_tipo_entidad";
					$sql_upsert.="(";
					$sql_upsert.="codigo_tipo_entidad, ";
					$sql_upsert.="descripcion ";
					$sql_upsert.=")";
					$sql_upsert.="values";
					$sql_upsert.="(";
					$cont_campos=0;
					while($cont_campos < count($campos))
					{
						$campo_procesado="";
								
						if($cont_campos == count($campos)-1)
						{
							$campo_procesado = substr($campos[$cont_campos], 0, strlen($campos[$cont_campos])-1);
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}
						else
						{
							$campo_procesado = str_replace("\n","",$campos[$cont_campos]);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}
						
						if($cont_campos < count($campos)-1)
						{
							$sql_upsert.="'".utf8_decode($campo_procesado)."',";
						}
						else
						{
							$sql_upsert.="'".utf8_decode($campo_procesado)."'";
						}
						$cont_campos++;
					}
					$sql_upsert.=");";
					//FIN QUERY INSERT gioss_tipo_entidad
					
					
					
				}//fin insert si no existe
				else
				{
					$array_campos_procesados=array();
					$cont_campos=0;
					while($cont_campos < count($campos))
					{
						$campo_procesado="";
								
						if($cont_campos == count($campos)-1)
						{
							$campo_procesado = substr($campos[$cont_campos], 0, strlen($campos[$cont_campos])-1);
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}
						else
						{
							$campo_procesado = str_replace("\n","",$campos[$cont_campos]);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}
						
						$array_campos_procesados[]=utf8_decode($campo_procesado);
						
						$cont_campos++;
					}
				
					$sql_upsert="";
					//INICIA QUERY UPDATE gioss_tipo_entidad
					$sql_upsert.="UPDATE gioss_tipo_entidad SET ";
					
					$sql_upsert.="codigo_tipo_entidad='".$array_campos_procesados[0]."',";
					$sql_upsert.="descripcion='".$array_campos_procesados[1]."' ";
					$sql_upsert.=" WHERE ";
					$sql_upsert.=" codigo_tipo_entidad='".$campo_llave_principal."' ";
					$sql_upsert.=";";
					//FIN QUERY UPDATE gioss_tipo_entidad
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
				$mensaje="<p style='color:green;'> Fila para tipo entidad sector salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				
				$aciertos++;
			}
			else
			{
				$mensaje="<p style='color:red'>Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				$errores++;
				$error_para_txt.="Error al insertar. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos).".\n".$error_bd."\n".$sql_upsert." \n";				
			}
			
			$mensaje_aciertos ="<p style='color:green;'>Numero de lineas insertadas correctamente: ".$aciertos."  </p>";
			$mensaje_errores ="<p style='color:red;'>Numero de lineas que no pudieron insertarse en la base de datos: ".$errores."  </p>";
			
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