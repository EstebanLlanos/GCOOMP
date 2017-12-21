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

$msg_actualizo_tabla_eapb="";
$msg_inserto_tabla_eapb="";
$msg_actualizo_tabla_ips="";
$msg_inserto_tabla_ips="";

$smarty->assign("mensaje_proceso", $mensaje, true);
$smarty->assign("mostrarResultado", $mostrarResultado, true);
$smarty->assign("nombre", $nombre, true);
$smarty->assign("menu", $menu, true);
$smarty->display('act_ent_salud.html.tpl');

if(isset($_FILES["archivo_info"]))
{
    $orden_columnas="orden_1";
    if(isset($_POST["orden"]) && $_POST["orden"]!="")
    {
	$orden_columnas=$_POST["orden"];
    }
    
    $bool_act_otras_tablas=false;
    $msg_sel_act_tablas_otras="";
    if(isset($_POST["check_llenar_otras_tablas"]) && $_POST["check_llenar_otras_tablas"]=="act_yes")
    {
	$bool_act_otras_tablas=true;
	//echo "<script>alert('Sincronizara la informacion con otras tablas de entidades');</script>";
	$msg_sel_act_tablas_otras="<span style=\'color:green;\'><b>Se selecciono sincronizara la informacion con otras tablas de entidades</b></span><br>";
    }
    else
    {
	$msg_sel_act_tablas_otras="<span style=\'color:red;\'><b>Se selecciono no sincronizara la informacion con otras tablas de entidades</b></span><br>";
    }
    
	$rutaTemporal = '../TEMPORALES/';
	$mensaje_div="";
	
	$archivo_entidades_salud=$_FILES["archivo_info"];
	$ruta_archivo_entidades_salud = $rutaTemporal . $archivo_entidades_salud['name'];
	move_uploaded_file($archivo_entidades_salud['tmp_name'], $ruta_archivo_entidades_salud);
		
	//archivo que se lee
	$lineas_del_archivo = count(file($ruta_archivo_entidades_salud)); 
	$archivo_cargar = fopen($ruta_archivo_entidades_salud, 'r') or exit("No se pudo abrir el archivo con las entidades de salud");
	
	//archivos que se crean
	$ruta_1=$rutaTemporal."tabla_entidades_salud.sql";
	$ruta_2=$rutaTemporal."tabla_entidades_salud.error.csv";
	$archivo_queries = fopen($ruta_1, "w") or die("fallo la creacion del archivo");
	$archivo_error= fopen($ruta_2, "w") or die("fallo la creacion del archivo");
	
	$cont_reset_div=0;
	$cont_linea=0;
	$aciertos=0;
	$errores=0;
	$error_para_txt="";
	
	
	$cont_act_tabla_eapb=0;
	$cod_ent_eapb_act="";
	
	$cont_inserto_tabla_eapb=0;
	$cod_ent_eapb_inserto="";
	
	$cont_act_tabla_ips=0;
	$cod_ent_ips_act="";
	
	$cont_inserto_tabla_ips=0;
	$cod_ent_ips_inserto="";
	
	
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
		  cod_tipo_entidad character varying(320),
		  codigo_entidad character varying(320) NOT NULL,
		  nombre_de_la_entidad character varying(320),
		  codigo_dpto character varying(320),
		  cod_mpio character varying(320),
		  des_tipo_entidad_salud character varying(320),
		  numero_identificacion character varying(320),
		  digito_verificacion character varying(320),
		*/		
		
		if($linea_res!="")
		{
			$bool_funciono=false;
			$campo_cod_entidad="";
			if($orden_columnas=="orden_1")
			{
			    $numero_campo_pk=2;
			}
			else if($orden_columnas=="orden_2")
			{
			    $numero_campo_pk=1;
			}
						
			if($numero_campo_pk == count($campos)-1 )
			{
			    //$campo_cod_entidad = substr($campos[$numero_campo_pk], 0, strlen($campos[$numero_campo_pk])-1);
			    $campo_cod_entidad = trim($campos[$numero_campo_pk]);
			    $campo_cod_entidad = str_replace("\n","",$campo_cod_entidad);
			    $campo_cod_entidad = str_replace("\"","",$campo_cod_entidad);
			    $campo_cod_entidad = str_replace("'","",$campo_cod_entidad);
			}
			else
			{
			    $campo_cod_entidad = trim($campos[$numero_campo_pk]);
			    $campo_cod_entidad = str_replace("\n","",$campo_cod_entidad);
			    $campo_cod_entidad = str_replace("\"","",$campo_cod_entidad);
			    $campo_cod_entidad = str_replace("'","",$campo_cod_entidad);
			}
				
			$sql_verificar="SELECT codigo_entidad FROM  gioss_entidades_sector_salud WHERE codigo_entidad='".$campo_cod_entidad."' ";
			
			$existe_en_bd=false;
			$resultado_busqueda=$coneccionBD->consultar2_no_crea_cierra($sql_verificar);
			if(count($resultado_busqueda)>0)
			{
				$existe_en_bd=true;
			}
			
			//entra si el numero de campos es correcto
			if(count($campos)==8)
			{
				if($existe_en_bd==false)
				{
					$sql_upsert="";
					//INICIA QUERY INSERT gioss_entidades_sector_salud
					$sql_upsert.="insert into gioss_entidades_sector_salud";
					$sql_upsert.="(";
					if($orden_columnas=="orden_1")
					{
					    $sql_upsert.="cod_tipo_entidad,";
					    $sql_upsert.="des_tipo_entidad_salud,";
					    $sql_upsert.="codigo_entidad,";
					    $sql_upsert.="nombre_de_la_entidad,";
					    $sql_upsert.="numero_identificacion,";
					    $sql_upsert.="digito_verificacion,";
					    $sql_upsert.="codigo_dpto,";
					    $sql_upsert.="cod_mpio";
					}
					else if($orden_columnas=="orden_2")
					{
					    $sql_upsert.="cod_tipo_entidad,";
					    $sql_upsert.="codigo_entidad,";
					    $sql_upsert.="nombre_de_la_entidad,";
					    $sql_upsert.="codigo_dpto,";
					    $sql_upsert.="cod_mpio,";
					    $sql_upsert.="des_tipo_entidad_salud,";
					    $sql_upsert.="numero_identificacion,";
					    $sql_upsert.="digito_verificacion";
					}
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
						    $campo_procesado = trim($campos[$cont_campos]);
						    $campo_procesado = str_replace("\n","",$campo_procesado);
						    $campo_procesado = str_replace("\"","",$campo_procesado);
						    $campo_procesado = str_replace("'","",$campo_procesado);
						}
						else
						{
						    $campo_procesado = trim($campos[$cont_campos]);
						    $campo_procesado = str_replace("\n","",$campo_procesado);
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
					//FIN QUERY INSERT gioss_entidades_sector_salud
					
					
					
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
							$campo_procesado = trim($campos[$cont_campos]);
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}
						else
						{
							$campo_procesado = trim($campos[$cont_campos]);
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}
						
						$array_campos_procesados[]=utf8_decode($campo_procesado);
						
						$cont_campos++;
					}
				
					$sql_upsert="";
					//INICIA QUERY UPDATE gioss_entidades_sector_salud
					$sql_upsert.="UPDATE gioss_entidades_sector_salud SET ";
					if($orden_columnas=="orden_1")
					{
					    $sql_upsert.="cod_tipo_entidad='".$array_campos_procesados[0]."',";
					    $sql_upsert.="des_tipo_entidad_salud='".$array_campos_procesados[1]."',";
					    $sql_upsert.="codigo_entidad='".$array_campos_procesados[2]."',";
					    $sql_upsert.="nombre_de_la_entidad='".$array_campos_procesados[3]."',";
					    $sql_upsert.="numero_identificacion='".$array_campos_procesados[4]."',";
					    $sql_upsert.="digito_verificacion='".$array_campos_procesados[5]."',";
					    $sql_upsert.="codigo_dpto='".$array_campos_procesados[6]."',";
					    $sql_upsert.="cod_mpio='".$array_campos_procesados[7]."' ";
					}
					else if($orden_columnas=="orden_2")
					{
					    $sql_upsert.="cod_tipo_entidad='".$array_campos_procesados[0]."',";					    
					    $sql_upsert.="codigo_entidad='".$array_campos_procesados[1]."',";					    
					    $sql_upsert.="nombre_de_la_entidad='".$array_campos_procesados[2]."',";
					    $sql_upsert.="codigo_dpto='".$array_campos_procesados[3]."',";
					    $sql_upsert.="cod_mpio='".$array_campos_procesados[4]."', "; 
					    $sql_upsert.="des_tipo_entidad_salud='".$array_campos_procesados[5]."',";
					    $sql_upsert.="numero_identificacion='".$array_campos_procesados[6]."',";
					    $sql_upsert.="digito_verificacion='".$array_campos_procesados[7]."' ";
					    
					}
					$sql_upsert.=" WHERE ";
					$sql_upsert.=" codigo_entidad='".$campo_cod_entidad."' ";
					$sql_upsert.=";";
					//FIN QUERY UPDATE gioss_entidades_sector_salud
				}//fin update si existe
				
				fwrite($archivo_queries, $sql_upsert."\n");
									
				$error_bd="";
				try
				{
				$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert, $error_bd);
				
				}
				catch (Exception $e) {}
				
				//INSERTA EN LAS TABLAS DE EAPB O PRESTADOR 
				//confuso pero significa que realizo la insercion o actualizacion
				if($bool_funciono==false && $bool_act_otras_tablas==true)
				{
				    //if procesando campos
				    $array_campos_procesados=array();
				    $cont_campos=0;
				    while($cont_campos < count($campos))
				    {
					    $campo_procesado="";
							    
					    if($cont_campos == count($campos)-1)
					    {
						    //$campo_procesado = substr($campos[$cont_campos], 0, strlen($campos[$cont_campos])-1);
						    $campo_procesado = trim($campos[$cont_campos]);
						    $campo_procesado = str_replace("\n","",$campo_procesado);
						    $campo_procesado = str_replace("\"","",$campo_procesado);
						    $campo_procesado = str_replace("'","",$campo_procesado);
					    }
					    else
					    {
						    $campo_procesado = trim($campos[$cont_campos]);
						    $campo_procesado = str_replace("\n","",$campo_procesado);
						    $campo_procesado = str_replace("\"","",$campo_procesado);
						    $campo_procesado = str_replace("'","",$campo_procesado);
					    }
					    
					    $array_campos_procesados[]=utf8_decode($campo_procesado);
					    
					    $cont_campos++;
				    }//fin if procesando campos
				    
				    $tipo_entidad_cp="";
				    $codigo_entidad_cp="";
				    $nombre_entidad_cp="";
				    $dpto_cp="";
				    $mpio_cp="";
				    $desc_entidad_cp="";
				    $nit_cp="";
				    $digito_verificacion_cp="";
				    
				    if($orden_columnas=="orden_1")
				    {
					$tipo_entidad_cp=$array_campos_procesados[0];
					$desc_entidad_cp=$array_campos_procesados[1];
					$codigo_entidad_cp=$array_campos_procesados[2];
					$nombre_entidad_cp=$array_campos_procesados[3];
					$nit_cp=$array_campos_procesados[4];
					$digito_verificacion_cp=$array_campos_procesados[5];
					$dpto_cp=$array_campos_procesados[6];
					$mpio_cp=$array_campos_procesados[7];
				    }
				    else if($orden_columnas=="orden_2")
				    {
					$tipo_entidad_cp=$array_campos_procesados[0];
					$codigo_entidad_cp=$array_campos_procesados[1];
					$nombre_entidad_cp=$array_campos_procesados[2];
					$dpto_cp=$array_campos_procesados[3];
					$mpio_cp=$array_campos_procesados[4];
					$desc_entidad_cp=$array_campos_procesados[5];
					$nit_cp=$array_campos_procesados[6];
					$digito_verificacion_cp=$array_campos_procesados[7];
					
				    }
				    
				    //dependiendo si es eapb
				    if(intval($tipo_entidad_cp)!=6 && intval($tipo_entidad_cp)!=7 && intval($tipo_entidad_cp)!=8 && intval($tipo_entidad_cp)!=10)
				    {
					$existe_en_eapb=false;
					$query_consulta_en_eapb="";
					$query_consulta_en_eapb.="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora=trim('$codigo_entidad_cp')";
					$resultado_busqueda_en_eapb=$coneccionBD->consultar2_no_crea_cierra($query_consulta_en_eapb);
					if(count($resultado_busqueda_en_eapb)>0 && is_array($resultado_busqueda_en_eapb))
					{
					    $existe_en_eapb=true;
					}//fin if verifica si ya existe
					
					if($existe_en_eapb==false)
					{
					    $sql_upsert_eapb="";
					    $sql_upsert_eapb.="INSERT INTO gios_entidad_administradora ";
					    $sql_upsert_eapb.="(";					    
					    $sql_upsert_eapb.="cod_entidad_administradora,";
					    $sql_upsert_eapb.="codigo_tipo_entidad,";
					    $sql_upsert_eapb.="nom_entidad_administradora,";
					    $sql_upsert_eapb.="nit,";
					    $sql_upsert_eapb.="dv,";
					    $sql_upsert_eapb.="des_tipo_entidad_salud,";
					    $sql_upsert_eapb.="dpto,";
					    $sql_upsert_eapb.="mpio";
					    $sql_upsert_eapb.=")";
					    $sql_upsert_eapb.="values";
					    $sql_upsert_eapb.="(";
					    $sql_upsert_eapb.="'".$codigo_entidad_cp."',";
					    $sql_upsert_eapb.="'".$tipo_entidad_cp."',";
					    $sql_upsert_eapb.="'".$nombre_entidad_cp."',";
					    $sql_upsert_eapb.="'".$nit_cp."',";
					    $sql_upsert_eapb.="'".$digito_verificacion_cp."',";
					    $sql_upsert_eapb.="'".$desc_entidad_cp."',";
					    $sql_upsert_eapb.="'".$dpto_cp."',";
					    $sql_upsert_eapb.="'".$mpio_cp."'";
					    $sql_upsert_eapb.=")";
					    $sql_upsert_eapb.=";";
					    $error_bd="";
					    $bool_funciono_en_eapb=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert_eapb, $error_bd);
					    if($error_bd!="")
					    {
						echo "<script>alert('Error en query insertar eapb sinc ".alphanumericAndSpace($error_bd)." ');</script>";
						
					    }
					    else
					    {
						$cod_ent_eapb_inserto=$codigo_entidad_cp;
						$cont_inserto_tabla_eapb++;
					    }
					}//fin if se inserta
					else if($existe_en_eapb==true)
					{
					    
					    
					    $sql_upsert_eapb="";
					    $sql_upsert_eapb.="UPDATE gios_entidad_administradora SET ";
					    $query_parcial_para_update="";
					    if(trim($nit_cp)!="SD" && trim($nit_cp)!="")
					    {
						if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						$query_parcial_para_update.="nit='".$nit_cp."' ";
					    }
					    if(trim($dpto_cp)!="SD" && trim($dpto_cp)!="")
					    {
						if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						$query_parcial_para_update.="dpto='".$dpto_cp."' ";
					    }
					    if(trim($mpio_cp)!="SD" && trim($mpio_cp)!="")
					    {
						if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						$query_parcial_para_update.="mpio='".$mpio_cp."' ";
					    }
					    if(trim($digito_verificacion_cp)!="SD" && trim($digito_verificacion_cp)!="")
					    {
						if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						$query_parcial_para_update.="dv='".$digito_verificacion_cp."' ";
					    }
					    if(trim($desc_entidad_cp)!="SD" && trim($desc_entidad_cp)!="")
					    {
						if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						$query_parcial_para_update.="des_tipo_entidad_salud='".$desc_entidad_cp."' ";
					    }
					    $sql_upsert_eapb.=$query_parcial_para_update;
					    $sql_upsert_eapb.=" WHERE ";
					    $sql_upsert_eapb.=" trim(cod_entidad_administradora)=trim('".$codigo_entidad_cp."') ";
					    $sql_upsert_eapb.=";";
					    $error_bd="";
					    $bool_funciono_en_eapb=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert_eapb, $error_bd);
					    if($error_bd!="")
					    {
						echo "<script>alert('Error en query actualizar eapb sinc ".alphanumericAndSpace($error_bd)." ');</script>";
						
					    }
					    else
					    {
						if($codigo_entidad_cp=="EPS016")
						{
						    //echo "<script>alert('nit: $nit_cp desc: $desc_entidad_cp dv: $digito_verificacion_cp dpto: $dpto_cp mpio: $mpio_cp ".alphanumericAndSpace($sql_upsert_eapb)." ');</script>";
						}
						$cod_ent_eapb_act=$codigo_entidad_cp;
						$cont_act_tabla_eapb++;
					    }
					    
					}//fin if se actualiza
					
				    }
				    //dependiendo si es prestador
				    else if(intval($tipo_entidad_cp)==6 || intval($tipo_entidad_cp)==7 || intval($tipo_entidad_cp)==8 || intval($tipo_entidad_cp)==10)
				    {
					$existe_en_ips=false;
					$query_consulta_en_ips="";
					$query_consulta_en_ips.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss=trim('$codigo_entidad_cp')";
					$resultado_busqueda_en_ips=$coneccionBD->consultar2_no_crea_cierra($query_consulta_en_ips);
					if(count($resultado_busqueda_en_ips)>0 && is_array($resultado_busqueda_en_ips))
					{
					    $existe_en_ips=true;
					}//fin if verifica si ya existe
					
					if($existe_en_ips==false)
					{
					    $sql_upsert_ips="";
					    $sql_upsert_ips.="INSERT INTO gios_prestador_servicios_salud ";
					    $sql_upsert_ips.="(";					    
					    $sql_upsert_ips.="cod_tipo_identificacion,";					    
					    $sql_upsert_ips.="num_tipo_identificacion,";
					    $sql_upsert_ips.="cod_registro_especial_pss,";
					    $sql_upsert_ips.="nom_entidad_prestadora,";					    
					    $sql_upsert_ips.="cod_municipio,";
					    $sql_upsert_ips.="num_sede_ips,";
					    $sql_upsert_ips.="digito_verificacion,";
					    $sql_upsert_ips.="nombre_comercial_prestador,";					    
					    $sql_upsert_ips.="zona,";
					    $sql_upsert_ips.="cod_nivel_atencion,";
					    $sql_upsert_ips.="cod_depto";
					    $sql_upsert_ips.=")";
					    $sql_upsert_ips.="values";
					    $sql_upsert_ips.="(";					    
					    $sql_upsert_ips.="'SD',";					    
					    $sql_upsert_ips.="'".$nit_cp."',";
					    $sql_upsert_ips.="'".$codigo_entidad_cp."',";
					    $sql_upsert_ips.="'".$nombre_entidad_cp."',";					    
					    $sql_upsert_ips.="'".$mpio_cp."',";
					    $sql_upsert_ips.="'01',";
					    $sql_upsert_ips.="'".$digito_verificacion_cp."',";
					    $sql_upsert_ips.="'".$nombre_entidad_cp."',";
					    $sql_upsert_ips.="'SD',";
					    $sql_upsert_ips.="'SD',";
					    $sql_upsert_ips.="'".$dpto_cp."'";
					    $sql_upsert_ips.=")";
					    $sql_upsert_ips.=";";
					    $error_bd="";
					    $bool_funciono_en_ips=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert_ips, $error_bd);
					    if($error_bd!="")
					    {
						echo "<script>alert('Error en query insertar ips sinc ".alphanumericAndSpace($error_bd)." ');</script>";
						
					    }
					    else
					    {
						$cod_ent_ips_inserto=$codigo_entidad_cp;
						$cont_inserto_tabla_ips++;
					    }
					}//fin if se inserta
					else if($existe_en_ips==true)
					{
						
						
						$sql_upsert_ips="";
						$sql_upsert_ips.="UPDATE gios_prestador_servicios_salud SET ";
						$query_parcial_para_update="";
						if(trim($nit_cp)!="SD" && trim($nit_cp)!="")
						{
						    if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						    $query_parcial_para_update.="num_tipo_identificacion='".$nit_cp."' ";
						}
						if(trim($dpto_cp)!="SD" && trim($dpto_cp)!="")
						{
						    if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						    $query_parcial_para_update.="cod_depto='".$dpto_cp."' ";
						}
						if(trim($mpio_cp)!="SD" && trim($mpio_cp)!="")
						{
						    if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						    $query_parcial_para_update.="cod_municipio='".$mpio_cp."' ";
						}
						if(trim($digito_verificacion_cp)!="SD" && trim($digito_verificacion_cp)!="")
						{
						    if($query_parcial_para_update!=""){$query_parcial_para_update.=",";}
						    $query_parcial_para_update.="digito_verificacion='".$digito_verificacion_cp."' ";
						}
						$sql_upsert_ips.=$query_parcial_para_update;
						$sql_upsert_ips.=" WHERE ";
						$sql_upsert_ips.=" trim(cod_registro_especial_pss)=trim('".$codigo_entidad_cp."') ";
						$sql_upsert_ips.=";";
						$error_bd="";
						$bool_funciono_en_ips=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert_ips, $error_bd);
						if($error_bd!="")
						{
						    echo "<script>alert('Error en query actualizar ips sinc ".alphanumericAndSpace($error_bd)." ');</script>";
						    
						}
						else
						{
						    $cod_ent_ips_act=$codigo_entidad_cp;
						    $cont_act_tabla_ips++;
						}
					}
				    //fin if correspondia a prestador
				    }
				    
				}//fin if si inserto o actualizo
				//FIN INSERTA EN LAS TABLAS DE EAPB O PRESTADOR 
			}//fin if numero de campos  es adecuado
			else
			{
				$error_bd="ERROR: el numero de campos no es correcto. ".count($campos);
				$bool_funciono=true;
			}//error cuando no cumple con el numero de campos

			
			$mensaje="";
			if($bool_funciono==false)
			{
				$mensaje="<p style='color:green;'> Fila para entidades salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.". num columnas ".count($campos)."  </p>";
				
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
			
			$msg_actualizo_tabla_eapb="<span style=\'color:green\'>Ha actualizado en gios_entidad_administradora ".$cont_act_tabla_eapb." con codigo ".$cod_ent_eapb_act."</span><br>";
			$msg_inserto_tabla_eapb="<span style=\'color:green\'>Ha insertado en gios_entidad_administradora ".$cont_inserto_tabla_eapb." con codigo ".$cod_ent_eapb_inserto."</span><br>";
			
			$msg_actualizo_tabla_ips="<span style=\'color:green\'>Ha actualizado en gios_prestador_servicios_salud ".$cont_act_tabla_ips."</span><br>";
			$msg_inserto_tabla_ips="<span style=\'color:green\'>Ha insertado en gios_prestador_servicios_salud ".$cont_inserto_tabla_ips."</span><br>";
			
			$botones_descarga_html="";
			$var_script_html="";
			$var_script_html.="<script>var ruta1='".$ruta_1."'; </script>";
			$var_script_html.="<script>var ruta2='".$ruta_2."'; </script>";
			
			$botones_descarga_html.="<p><input type='button' class='btn btn-success color_boton' value='Descargar archivo SQL queries' onclick='download_archivo(ruta1);' /></p>";
			$botones_descarga_html.="<p><input type='button' class='btn btn-success color_boton' value='Descargar archivo errores BD' onclick='download_archivo(ruta2);' /></p>";
			
			if($cont_linea < $lineas_del_archivo-1)
			{
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$mensaje.$mensaje_aciertos.$mensaje_errores.$msg_sel_act_tablas_otras.$msg_inserto_tabla_eapb.$msg_actualizo_tabla_eapb.$msg_inserto_tabla_ips.$msg_actualizo_tabla_ips."\";</script>";
				ob_flush();
				flush();
			}
			else
			{
				$final="<p style='color:blue;>Se ha terminado de procesar el archivo.</p>";
				echo $var_script_html;
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$final.$mensaje.$mensaje_aciertos.$mensaje_errores.$msg_sel_act_tablas_otras.$msg_inserto_tabla_eapb.$msg_actualizo_tabla_eapb.$msg_inserto_tabla_ips.$msg_actualizo_tabla_ips.$botones_descarga_html."\";</script>";
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
$coneccionBD->cerrar_conexion();
?>