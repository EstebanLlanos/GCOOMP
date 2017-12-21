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
$smarty->display('act_eapb.html.tpl');

if(isset($_FILES["archivo_info"]))
{
	$rutaTemporal = '../TEMPORALES/';
	$mensaje_div="";
	
	$archivo_entidades_salud=$_FILES["archivo_info"];
	$ruta_archivo_entidades_salud = $rutaTemporal . $archivo_entidades_salud['name'];
    move_uploaded_file($archivo_entidades_salud['tmp_name'], $ruta_archivo_entidades_salud);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_entidades_salud)); 
	$archivo_cargar = fopen($ruta_archivo_entidades_salud, 'r') or exit("No se pudo abrir el archivo con las entidades de salud");
	
	//archivos que se crean
	$ruta_1=$rutaTemporal."tabla_eapb_salud.sql";
	$ruta_2=$rutaTemporal."tabla_eapb_salud.error.csv";
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
			/*
			//parte solucion excede
			if(count($campos)==20)
			{
				unset($campos[7]);
				$campos_tmp=array_values($campos);
				$campos=$campos_tmp;
			
			}
			//fin solucion cuando se excede
			*/
			
			$bool_funciono=false;
			$campo_codigo_eapb="";
			$numero_campo_pk=0;
						
			if($numero_campo_pk == count($campos)-1 )
			{
				//$campo_codigo_eapb = substr($campos[$numero_campo_pk], 0, strlen($campos[$numero_campo_pk])-1);
				$campo_codigo_eapb = preg_replace("/[^A-Za-z0-9:]/", "", $campos[$numero_campo_pk] );
				$campo_codigo_eapb = str_replace("\n","",$campo_codigo_eapb);
				$campo_codigo_eapb = str_replace("\"","",$campo_codigo_eapb);
				$campo_codigo_eapb = str_replace("'","",$campo_codigo_eapb);
			}
			else
			{
				$campo_codigo_eapb = str_replace("\n","",$campos[$numero_campo_pk]);
				$campo_codigo_eapb = str_replace("\"","",$campo_codigo_eapb);
				$campo_codigo_eapb = str_replace("'","",$campo_codigo_eapb);
			}
				
			$sql_verificar="SELECT cod_entidad_administradora FROM  gios_entidad_administradora WHERE cod_entidad_administradora='".$campo_codigo_eapb."' ";
			
			$existe_en_bd=false;
			$resultado_busqueda=$coneccionBD->consultar2($sql_verificar);
			if(count($resultado_busqueda)>0)
			{
				$existe_en_bd=true;
			}
			
			
			
			//solo introduce en la base de datos si el numero de campos es correcto
			if(count($campos)==11)
			{
				if($existe_en_bd==false)
				{
					$sql_upsert="";
					//INICIA QUERY INSERT gios_entidad_administradora
					$sql_upsert.="insert into gios_entidad_administradora";
					$sql_upsert.="(";
					$sql_upsert.="cod_entidad_administradora,";
					$sql_upsert.="nom_entidad_administradora,";
					$sql_upsert.="des_tipo_entidad_salud,";
					$sql_upsert.="codigo_tipo_entidad,";
					$sql_upsert.="cod_tipo_regimen_4505,";
					$sql_upsert.="cod_tipo_regimen_RIPS,";
					$sql_upsert.="cod_tipo_ident_entidad_reportadora,";
					$sql_upsert.="nit,";
					$sql_upsert.="dv,";
					$sql_upsert.="dpto,";
					$sql_upsert.="mpio";
					$sql_upsert.=")";
					$sql_upsert.="values";
					$sql_upsert.="(";
					$cont_campos=0;
					while($cont_campos < count($campos))
					{
						$campo_procesado="";
								
						if($cont_campos == count($campos)-1)
						{
							//$campo_procesado = substr($campos[$cont_campos], 0, strlen($campos[$cont_campos])-1);
							$campo_procesado = preg_replace("/[^A-Za-z0-9:]/", "", $campos[$cont_campos] );
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
					//FIN QUERY INSERT gios_entidad_administradora
					
					
					
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
							//$campo_procesado = substr($campos[$cont_campos], 0, strlen($campos[$cont_campos])-1);
							$campo_procesado = preg_replace("/[^A-Za-z0-9:]/", "", $campos[$cont_campos] );
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
					//INICIA QUERY UPDATE gios_entidad_administradora
					$sql_upsert.="UPDATE gios_entidad_administradora SET ";
					
					$sql_upsert.="cod_entidad_administradora='".$array_campos_procesados[0]."',";
					$sql_upsert.="nom_entidad_administradora='".$array_campos_procesados[1]."',";
					$sql_upsert.="des_tipo_entidad_salud='".$array_campos_procesados[2]."',";
					$sql_upsert.="codigo_tipo_entidad='".$array_campos_procesados[3]."',";
					$sql_upsert.="cod_tipo_regimen_4505='".$array_campos_procesados[4]."',";
					$sql_upsert.="cod_tipo_regimen_RIPS='".$array_campos_procesados[5]."',";
					$sql_upsert.="cod_tipo_ident_entidad_reportadora='".$array_campos_procesados[6]."',";
					$sql_upsert.="nit='".$array_campos_procesados[7]."',";
					$sql_upsert.="dv='".$array_campos_procesados[8]."',";
					$sql_upsert.="dpto='".$array_campos_procesados[9]."', ";
					$sql_upsert.="mpio='".$array_campos_procesados[10]."' ";
					$sql_upsert.=" WHERE ";
					$sql_upsert.=" cod_entidad_administradora='".$campo_codigo_eapb."' ";
					$sql_upsert.=";";
					//FIN QUERY UPDATE gios_entidad_administradora
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
				$mensaje="<p style='color:green;'> Fila para EAPB de salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				
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