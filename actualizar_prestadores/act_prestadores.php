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
$smarty->display('act_prestadores.html.tpl');

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
	$ruta_1=$rutaTemporal."tabla_prestadores_salud.sql";
	$ruta_2=$rutaTemporal."tabla_prestadores_salud.error.csv";
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
		    cod_tipo_identificacion character varying(2) NOT NULL,
			num_tipo_identificacion character varying(90) NOT NULL,
			cod_registro_especial_pss character varying(12) NOT NULL,
			nom_entidad_prestadora character varying(300),
			des_representante_legal character varying(200),
			cod_municipio character varying(5),
			des_direccion character varying(200),
			des_telefono character varying(200),
			txt_correo_contacto character varying(200),
			clase_prestador character varying(2),
			cod_tipo_entidad character varying(2),
			cod_naturaleza_juridica character varying(2),
			cod_tipo_cobertura character varying(2),
			num_sede_ips character varying(2) NOT NULL,
			digito_verificacion character varying(2) NOT NULL,
			nombre_comercial_prestador character varying(300) NOT NULL,
			zona character varying(2) NOT NULL,
			cod_nivel_atencion character varying(2) NOT NULL,
			cod_depto character varying(320),
			ese character varying(320),
			sede_principal character varying(320),
		*/		
		
		if($linea_res!="")
		{
			//parte solucion excede
			if(count($campos)==22)
			{
				unset($campos[7]);
				$campos_tmp=array_values($campos);
				$campos=$campos_tmp;
			
			}
			//fin solucion cuando se excede
			
			$bool_funciono=false;
			$campo_codigo_pss="";
			$numero_campo_pk=2;
						
			if($numero_campo_pk == count($campos)-1 )
			{
				$campo_codigo_pss = substr($campos[$numero_campo_pk], 0, strlen($campos[$numero_campo_pk])-1);
				$campo_codigo_pss = str_replace("\n","",$campo_codigo_pss);
				$campo_codigo_pss = str_replace("\"","",$campo_codigo_pss);
				$campo_codigo_pss = str_replace("'","",$campo_codigo_pss);
			}
			else
			{
				$campo_codigo_pss = str_replace("\n","",$campos[$numero_campo_pk]);
				$campo_codigo_pss = str_replace("\"","",$campo_codigo_pss);
				$campo_codigo_pss = str_replace("'","",$campo_codigo_pss);
			}
				
			$sql_verificar="SELECT cod_registro_especial_pss FROM  gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campo_codigo_pss."' ";
			
			$existe_en_bd=false;
			$resultado_busqueda=$coneccionBD->consultar2($sql_verificar);
			if(count($resultado_busqueda)>0)
			{
				$existe_en_bd=true;
			}
			
			
			
			//solo introduce en la base de datos si el numero de campos es correcto
			if(count($campos)==21)
			{
				if($existe_en_bd==false)
				{
					$sql_upsert="";
					//INICIA QUERY INSERT gios_prestador_servicios_salud
					$sql_upsert.="insert into gios_prestador_servicios_salud";
					$sql_upsert.="(";
					$sql_upsert.="cod_tipo_identificacion,num_tipo_identificacion,cod_registro_especial_pss,";
					$sql_upsert.="nom_entidad_prestadora,des_representante_legal,cod_municipio,";
					$sql_upsert.="des_direccion,des_telefono,txt_correo_contacto,";
					$sql_upsert.="clase_prestador,cod_tipo_entidad,cod_naturaleza_juridica,";
					$sql_upsert.="cod_tipo_cobertura,num_sede_ips,digito_verificacion,";
					$sql_upsert.="nombre_comercial_prestador,zona,cod_nivel_atencion,cod_depto,ese,sede_principal";
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
					//FIN QUERY INSERT gios_prestador_servicios_salud
					
					
					
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
					//INICIA QUERY UPDATE gios_prestador_servicios_salud
					$sql_upsert.="UPDATE gios_prestador_servicios_salud SET ";
					
					$sql_upsert.="cod_tipo_identificacion='".$array_campos_procesados[0]."',";
					$sql_upsert.="num_tipo_identificacion='".$array_campos_procesados[1]."',";
					$sql_upsert.="cod_registro_especial_pss='".$array_campos_procesados[2]."',";
					$sql_upsert.="nom_entidad_prestadora='".$array_campos_procesados[3]."',";
					$sql_upsert.="des_representante_legal='".$array_campos_procesados[4]."',";
					$sql_upsert.="cod_municipio='".$array_campos_procesados[5]."',";
					$sql_upsert.="des_direccion='".$array_campos_procesados[6]."',";
					$sql_upsert.="des_telefono='".$array_campos_procesados[7]."', ";
					$sql_upsert.="txt_correo_contacto='".$array_campos_procesados[8]."',";
					$sql_upsert.="clase_prestador='".$array_campos_procesados[9]."',";
					$sql_upsert.="cod_tipo_entidad='".$array_campos_procesados[10]."',";
					$sql_upsert.="cod_naturaleza_juridica='".$array_campos_procesados[11]."',";
					$sql_upsert.="cod_tipo_cobertura='".$array_campos_procesados[12]."',";
					$sql_upsert.="num_sede_ips='".$array_campos_procesados[13]."', ";
					$sql_upsert.="digito_verificacion='".$array_campos_procesados[14]."',";
					$sql_upsert.="nombre_comercial_prestador='".$array_campos_procesados[15]."',";
					$sql_upsert.="zona='".$array_campos_procesados[16]."',";
					$sql_upsert.="cod_nivel_atencion='".$array_campos_procesados[17]."',";
					$sql_upsert.="cod_depto='".$array_campos_procesados[18]."', ";
					$sql_upsert.="ese='".$array_campos_procesados[19]."', ";
					$sql_upsert.="sede_principal='".$array_campos_procesados[20]."' ";
					$sql_upsert.=" WHERE ";
					$sql_upsert.=" cod_registro_especial_pss='".$campo_codigo_pss."' ";
					$sql_upsert.=";";
					//FIN QUERY UPDATE gios_prestador_servicios_salud
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
				$mensaje="<p style='color:green;'> Fila para prestadores de salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				
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