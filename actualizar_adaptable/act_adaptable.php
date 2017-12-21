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
    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string);
}


function contar_lineas_archivo($ruta_file)
{
	$linecount = 0;
	$handle = fopen($ruta_file, "r");
	while(!feof($handle))
	{
	  $line = fgets($handle);
	  $linecount++;
	}

	fclose($handle);

	return $linecount;
}//fin funcion

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
$smarty->display('act_adaptable.html.tpl');

if(isset($_FILES["archivo_info"]) && isset($_POST["nombres_columnas_tablas"]) && isset($_POST["nombre_tabla"]) && $_POST["nombres_columnas_tablas"]!="" && $_POST["nombre_tabla"]!=""
&& isset($_POST["llaves"]) && $_POST["llaves"]!="" && trim($_POST["nombre_tabla"])!="gioss_detalle_inconsistecias_4505"
&& trim($_POST["nombre_tabla"])!="gioss_detalle_inconsistencias_rips"
&& trim($_POST["nombre_tabla"])!="gioss_detalle_inconsistencia_r4725_sida_vih")
{
	date_default_timezone_set ("America/Bogota");

	//reinsertando valores
	echo "<script>document.getElementById('nombre_tabla').value='".$_POST["nombre_tabla"]."';</script>";
	echo "<script>document.getElementById('llaves').value='".$_POST["llaves"]."';</script>";
	echo "<script>document.getElementById('nombres_columnas_tablas').value='".$_POST["nombres_columnas_tablas"]."';</script>";
	//fin reinsertando valores
	
	$rutaTemporal = '../TEMPORALES/';
	$mensaje_div="";

	$upsert_select="";

	if(isset($_POST["upsert_select"])==true)
	{
		$upsert_select=trim($_POST["upsert_select"]);
		echo "<script>document.getElementById('upsert_select').value='".$_POST["upsert_select"]."';</script>";
	}//fin if

	$tabla_pre_diligenciada="";
	if(isset($_POST["tabla_pre_diligenciada"])==true )
	{
		$tabla_pre_diligenciada=$_POST["tabla_pre_diligenciada"];
		echo "<script>document.getElementById('tabla_pre_diligenciada').value='".$_POST["tabla_pre_diligenciada"]."';</script>";
	}//fin if
	echo "tabla_pre_diligenciada: ".$tabla_pre_diligenciada."<br>";
	

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

	//parte si es tabla analisis
	$datos_id_tabla_analisis=array();

	
	
	$codigo_prestador_para_insercion="";
	$codigo_eapb_para_insercion="";
	$fecha_inicial_para_analisis="";
	$fecha_de_corte_temp_analisis="";
	$fecha_validacion_para_analisis="";
	$nombre_archivo4505="";
	$secuencia_dependiendo_existencia="";
	if($tabla_pre_diligenciada=="11")
	{
		//echo $_POST["nombres_columnas_tablas"]."<br>";
		$temp_columnas=explode(",",$_POST["nombres_columnas_tablas"]);

		//echo print_r($temp_columnas,true)."<br>";

		$datos_id_tabla_analisis=explode(";",str_replace("\"", "", $temp_columnas[1]));

		

		$codigo_prestador_para_insercion=trim($datos_id_tabla_analisis[0]);
		$codigo_eapb_para_insercion=trim($datos_id_tabla_analisis[1]);
		$fecha_inicial_para_analisis=trim($datos_id_tabla_analisis[2]);
		$fecha_de_corte_temp_analisis=trim($datos_id_tabla_analisis[3]);
		$fecha_validacion_para_analisis=trim($datos_id_tabla_analisis[4]);
		$nombre_archivo4505=trim($datos_id_tabla_analisis[5]);
		$secuencia_dependiendo_existencia=trim($datos_id_tabla_analisis[6]);

		$array_columnas_tablas=array();

		$contCampo4505 = 0;
		while($contCampo4505<119)
		{
			$array_columnas_tablas[]="campo".$contCampo4505;
			$contCampo4505++;
		}//fin while
		/*
		cod_prestador_servicios_salud character varying(12) NOT NULL,
		  codigo_eapb character varying(90) NOT NULL,
		  fecha_inicio_periodo date NOT NULL,
		  fecha_de_corte date NOT NULL,
		  fecha_y_hora_validacion timestamp without time zone NOT NULL,
		  nombre_archivo character varying(320) NOT NULL,
		  numero_fila bigint NOT NULL,
		  edad_years integer,
		  edad_meses integer,
		  edad_dias integer,
		  regional character varying(320),
		  grupo_etareo integer,
		  grupo_edad_quinquenal integer,
		  numero_de_secuencia
		*/
		  $array_columnas_tablas[]="cod_prestador_servicios_salud";
		  $array_columnas_tablas[]="codigo_eapb";
		  $array_columnas_tablas[]="fecha_inicio_periodo";
		  $array_columnas_tablas[]="fecha_de_corte";
		  $array_columnas_tablas[]="fecha_y_hora_validacion";
		  $array_columnas_tablas[]="nombre_archivo";		  
		  $array_columnas_tablas[]="numero_de_secuencia";
		  $array_columnas_tablas[]="numero_fila";
		  $array_columnas_tablas[]="edad_years";
		  $array_columnas_tablas[]="edad_meses";
		  $array_columnas_tablas[]="edad_dias";
		  $array_columnas_tablas[]="grupo_etareo";
		  $array_columnas_tablas[]="grupo_edad_quinquenal";
		  $array_columnas_tablas[]="regional";

		  //echo "datos_id_tabla_analisis: ".print_r($datos_id_tabla_analisis,true)."<br>";
		  //echo print_r($array_columnas_tablas,true)."<br>";

	}//fin fi
	//parte si es tabla analisis
	
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
	$ruta_archivo_para_bd = $rutaTemporal . $archivo_entidades_salud['name'];
	move_uploaded_file($archivo_entidades_salud['tmp_name'], $ruta_archivo_para_bd);
		
	//archivo que se lee
	$lineas_del_archivo = contar_lineas_archivo($ruta_archivo_para_bd); 
	$archivo_cargar = fopen($ruta_archivo_para_bd, 'r') or exit("No se pudo abrir el archivo con los datos");
	
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

	$mensaje="";
	$botones_descarga_html="";
	$var_script_html="";
	$mensaje_aciertos="";
	$mensaje_errores="";

	$cont4505Registro=1;

	$bool_inserto_en_indice=false;
	
	while (!feof($archivo_cargar)) 
	{
		$linea = fgets($archivo_cargar);
		$linea_res = str_replace("\n","",$linea);
		
		if($tabla_pre_diligenciada!="11")
		{
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
			$linea_res = str_replace("\"","",$linea_res);
			$linea_res= alphanumericAndSpace($linea_res);	
			$campos = explode(",",$linea_res);
		}
		else if ($tabla_pre_diligenciada=="11")
		{
			$campos = explode("|",$linea_res);
		}
		
		//print_r($campos);
		
		if($linea_res!="")
		{
			//adicion campos tabla analisis 4505
			if($tabla_pre_diligenciada=="11")
			{
				foreach ($datos_id_tabla_analisis as $key => $valueId) 
				{
					# code...
					$campos[]=$valueId;
				}//fin foreach

				/*

				  $array_columnas_tablas[]="numero_fila";

				*/
				 $campos[]=$cont4505Registro;

				 //PREPARA EDADES Y CAMPOS ADICIONALES

	    		//CALCULO EDAD
				$fecha_nacimiento= explode("-",$campos[9]);
				$bool_fecha_nacimiento_valida=true;
				if(count($fecha_nacimiento)!=3
				   || !(ctype_digit($fecha_nacimiento[0]) && ctype_digit($fecha_nacimiento[1]) && ctype_digit($fecha_nacimiento[2]) )
				   || !checkdate($fecha_nacimiento[1],$fecha_nacimiento[2],$fecha_nacimiento[0]))
				{			
					$bool_fecha_nacimiento_valida=false;
				}
				
				
				$edad= -1;
				$edad_dias =-1;
				$edad_meses =-1;
				$edad_semanas = -1;
				$verificador_edad= -1;

				$edad_quinquenio=0;
				$edad_etarea=0;
				
				if($bool_fecha_nacimiento_valida==true)
				{
				    
				    $string_fecha_nacimiento=date($fecha_nacimiento[0]."-".$fecha_nacimiento[1]."-".$fecha_nacimiento[2]);
				    
				    $fecha_nacimiento_format=new DateTime($string_fecha_nacimiento);
				    $fecha_corte_format=new DateTime($fecha_de_corte_temp_analisis);
				
				    $interval = date_diff($fecha_nacimiento_format,$fecha_corte_format);
				    $edad_dias =(float)($interval->days);
				    
				    //$edad= (float)($interval->days / 365);		    
				    //$edad_meses = (float)($interval->days / 30.4368499);
				    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
				    
				    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
				    $array_fecha_corte=explode("-",$fecha_de_corte_temp_analisis);
				    $array_edad=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte[2]."-".$array_fecha_corte[1]."-".$array_fecha_corte[0]);
				    $edad_meses=(intval($array_edad['y'])*12)+$array_edad['m'];
				    $edad=intval($array_edad['y']);
				    
				    $edad_semanas = (float)($interval->days / 7);
				    $verificador_edad= (float)$interval->format("%r%a");

				    //edad quinquenio
				    if($edad==0)
				    {
				    	$edad_quinquenio=1;							    	
				    }//fin if
				    else if($edad>=1 && $edad<=4)
				    {
				    	$edad_quinquenio=2;
				    }
				    else if($edad>=5 && $edad<=9)
				    {
				    	$edad_quinquenio=3;
				    }
				    else if($edad>=10 && $edad<=14)
				    {
				    	$edad_quinquenio=4;
				    }
				    else if($edad>=15 && $edad<=19)
				    {
				    	$edad_quinquenio=5;
				    }
				    else if($edad>=20 && $edad<=24)
				    {
				    	$edad_quinquenio=6;
				    }
				    else if($edad>=25 && $edad<=29)
				    {
				    	$edad_quinquenio=7;
				    }
				    else if($edad>=30 && $edad<=34)
				    {
				    	$edad_quinquenio=8;
				    }
				    else if($edad>=35 && $edad<=39)
				    {
				    	$edad_quinquenio=9;
				    }
				    else if($edad>=40 && $edad<=44)
				    {
				    	$edad_quinquenio=10;
				    }
				    else if($edad>=45 && $edad<=49)
				    {
				    	$edad_quinquenio=11;
				    }
				    else if($edad>=50 && $edad<=54)
				    {
				    	$edad_quinquenio=12;
				    }
				    else if($edad>=55 && $edad<=59)
				    {
				    	$edad_quinquenio=13;
				    }
				    else if($edad>=60 && $edad<=64)
				    {
				    	$edad_quinquenio=14;
				    }
				    else if($edad>=65 && $edad<=69)
				    {
				    	$edad_quinquenio=15;
				    }
				    else if($edad>=70 && $edad<=74)
				    {
				    	$edad_quinquenio=16;
				    }
				    else if($edad>=75 && $edad<=79)
				    {
				    	$edad_quinquenio=17;
				    }
				    else if($edad>=80 && $edad<=84)
				    {
				    	$edad_quinquenio=18;
				    }
				    else if($edad>=85 && $edad<=89)
				    {
				    	$edad_quinquenio=19;
				    }
				    else if($edad>=90 )
				    {
				    	$edad_quinquenio=20;
				    }
				    //fin edad quinquenio

				    //edad etarea
				    if($edad==0)
				    {
				    	$edad_etarea=1;							    	
				    }//fin if
				    else if($edad>=1 && $edad<=4)
				    {
				    	$edad_etarea=2;
				    }
				    else if($edad>=5 && $edad<=14)
				    {
				    	$edad_etarea=3;
				    }
				    else if($edad>=15 && $edad<=44)
				    {
				    	$edad_etarea=4;
				    }
				    else if($edad>=45 && $edad<=59)
				    {
				    	$edad_etarea=5;
				    }
				    else if($edad>=60 )
				    {
				    	$edad_etarea=6;
				    }
				    //fin edad etarea
				    
				    
				}
				//FIN CALCULO EDAD

	    		//FIN PREPARA EDADES Y CAMPOS ADICIONALES
				/*				
				  $array_columnas_tablas[]="edad_years";
				  $array_columnas_tablas[]="edad_meses";
				  $array_columnas_tablas[]="edad_dias";
				  $array_columnas_tablas[]="grupo_etareo";
				  $array_columnas_tablas[]="grupo_edad_quinquenal";
				  $array_columnas_tablas[]="regional";
	    		*/

				  $campos[]=$edad;
				  $campos[]=$edad_dias;
				  $campos[]=$edad_meses;
				  $campos[]=$edad_etarea;
				  $campos[]=$edad_quinquenio;

				  $query_bd_existe_afiliado_en_tabla_regimen="";
					$resultados_query_existe_afiliado_tablas_regimen=array();
					$nombre_tabla_afiliado_hallado="";
					$numero_id_c4=$campos[4];
					$tipo_id_c3=$campos[3];

					
					$nombre_tabla_afiliado_hallado="gioss_afiliados_eapb_mp";

					$query_bd_existe_afiliado_en_tabla_regimen="SELECT * FROM ".$nombre_tabla_afiliado_hallado." WHERE id_afiliado = '".$numero_id_c4."' AND tipo_id_afiliado = '".$tipo_id_c3."' AND codigo_eapb='".$codigo_eapb_para_insercion."' ;";
					$resultados_query_existe_afiliado_tablas_regimen=$coneccionBD->consultar2_no_crea_cierra($query_bd_existe_afiliado_en_tabla_regimen);
				


					$num_filas_resultado_existe_tablas_regimen=count($resultados_query_existe_afiliado_tablas_regimen);

					//variables booleanas para separar y visibilizar
					//mas facilmente si habia sexos y/o fechas de nacimiento diferentes
					//entre el archivo subido y la base de datos

					$nombre_regional="";

					if($num_filas_resultado_existe_tablas_regimen>0 
						&& is_array($resultados_query_existe_afiliado_tablas_regimen)
						)
					{
						if(isset($resultados_query_existe_afiliado_tablas_regimen[0]['nombre_regional']) )
			 			{
							$nombre_regional=trim($resultados_query_existe_afiliado_tablas_regimen[0]['nombre_regional']);
							
						}//fin if existe 
					}//fin if
					$campos[]=$nombre_regional;

					//echo print_r($campos,true)."<br>";
			}//fin if
			//echo "cantidad actual campos ".count($campos)."<br>";
			//fin adicion campos tabla analisis 4505
			
			$bool_funciono=false;

			$existe_en_bd=false;
			
			if($upsert_select=="" || $upsert_select=="update" || $upsert_select=="upsert")
			{
				$sql_verificar="";
				$sql_verificar.="SELECT * FROM  $nombre_tabla WHERE ";
				
				
				$cont_llave=0;
				if(count($campos)==count($array_columnas_tablas) && $tabla_pre_diligenciada!="11")
				{
				    foreach($array_index_llaves_en_numero_columnas as $nombre_llave=>$posicion_campo)
				    {
					    $campo_llave="";
					    $numero_campo_pk=$posicion_campo;							
					    
					    if($tabla_pre_diligenciada!="11")
					    {
					    	$campo_llave = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campos[intval($numero_campo_pk)] );
						}//fin if
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

				    //echo "sql_verificar : ".$sql_verificar."<br>";
				    
				    $resultado_busqueda=$coneccionBD->consultar2_no_crea_cierra($sql_verificar);
				    if(count($resultado_busqueda)>0)
				    {
					    $existe_en_bd=true;
				    }
				}//fin if numero campsoe s correcto
				else if(count($campos)==count($array_columnas_tablas) && $tabla_pre_diligenciada=="11")
				{
					$sql_verificar.=" cod_prestador_servicios_salud='$codigo_prestador_para_insercion' 
					AND codigo_eapb='$codigo_eapb_para_insercion' 
					AND fecha_inicio_periodo='$fecha_inicial_para_analisis' 
					AND fecha_de_corte='$fecha_de_corte_temp_analisis' 
					AND numero_fila='$cont4505Registro' 
					AND nombre_archivo='$nombre_archivo4505'
					AND fecha_y_hora_validacion='$fecha_validacion_para_analisis' ";
				    
				    $sql_verificar.=";";

				    //echo "sql_verificar : ".$sql_verificar."<br>";
				    
				    $resultado_busqueda=$coneccionBD->consultar2_no_crea_cierra($sql_verificar);
				    if(count($resultado_busqueda)>0)
				    {
					    $existe_en_bd=true;
				    }
				}//fin if numero campsoe s correcto
			}//fin if solo si es upsert o update


			
			//VERIFICA SI TODOS LOS CAMPOS ESTAN VACIOS
			$cont_ver=0;
			$cantidad_campos_vacios=0;
			$todos_los_campos_estan_vacios=false;
			$hay_llaves_vacias=false;
			while($cont_ver<count($campos))
			{
				if(trim($campos[$cont_ver])=="")
				{
					$cantidad_campos_vacios++;
				}//fin if
				foreach($array_index_llaves_en_numero_columnas as $indice_donde_esta_llave)
				{
					if($indice_donde_esta_llave==$cont_ver && trim($campos[$cont_ver])=="")
					{
						$hay_llaves_vacias=true;
					}//fin if
				}//fin foreach
				$cont_ver++;
			}//fin while

			if($cantidad_campos_vacios==count($campos) )
			{
				$todos_los_campos_estan_vacios=true;
			}
			//FIN VERIFICA SI TODOS LOS CAMPOS ESTAN VACIOS



			
			
			
			
			//solo introduce en la base de datos si el numero de campos es correcto
			if(count($campos)==count($array_columnas_tablas) && $todos_los_campos_estan_vacios==false && $hay_llaves_vacias==false)
			{
				if($existe_en_bd==false 
					&& ($upsert_select=="" || $upsert_select=="insert" || $upsert_select=="upsert") 
					)
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
						$campo_procesado = trim($campos[$cont_campos] );
						if($tabla_pre_diligenciada!="11")
						{
							$campo_procesado = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campo_procesado );
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}//fin if
						
						
						if($cont_campos!=0)
						{
							$sql_upsert.=",";
						}
						$sql_upsert.="'".utf8_decode($campo_procesado)."'";
						
						$cont_campos++;
					}
					$sql_upsert.=");";
					//FIN QUERY INSERT "nombre_tabla_aqui"
					
					fwrite($archivo_queries, $sql_upsert."\n");

					//echo "insert: ".$sql_upsert."<br>";
										
					$error_bd="";
					try
					{
						$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert, $error_bd);

						

						
					
					}
					catch (Exception $e) {}


					//tabla indice insert
			    	if($bool_inserto_en_indice==false && $tabla_pre_diligenciada=="11" && $error_bd=="")
			    	{
			    		
				    
			    		$insercion_tabla_indice_exitosa=true;

			    		$sql_insertar_en_tabla_indice_analisis_coherencia="";				    
						$sql_insertar_en_tabla_indice_analisis_coherencia.="insert into gioss_indice_archivo_para_analisis_4505";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="(";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="cod_prestador_servicios_salud,";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="codigo_eapb,";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="fecha_inicio_periodo,";					
						$sql_insertar_en_tabla_indice_analisis_coherencia.="fecha_de_corte,";					
						$sql_insertar_en_tabla_indice_analisis_coherencia.="fecha_y_hora_validacion,";					
						$sql_insertar_en_tabla_indice_analisis_coherencia.="nombre_archivo,";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="numero_de_secuencia";								
						$sql_insertar_en_tabla_indice_analisis_coherencia.=")";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="values";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="(";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$codigo_prestador_para_insercion."',";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$codigo_eapb_para_insercion."',";
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_inicial_para_analisis."',";					
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_de_corte_temp_analisis."',";					
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$fecha_validacion_para_analisis."',";							
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$nombre_archivo4505."',";							
						$sql_insertar_en_tabla_indice_analisis_coherencia.="'".$secuencia_dependiendo_existencia."'";								
						$sql_insertar_en_tabla_indice_analisis_coherencia.=");";
						$error_bd_seq="";
						$bandera = $coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_insertar_en_tabla_indice_analisis_coherencia, $error_bd_seq);
						if($error_bd_seq!="")
						{
						    $error_de_base_de_datos.=" ERROR AL INSERTAR INDICE PARA EL ARCHIVO DE ANALISIS: ".$error_bd_seq."<br>";
						    echo "ERROR ".$error_de_base_de_datos;
						    $insercion_tabla_indice_exitosa=false;
						}
						 //FIN QUERY INSERT gioss_indice_archivo_para_analisis_4505

			    		if($insercion_tabla_indice_exitosa==true)
			    		{
			    			echo "<span style='color:red;'>creo indice</span><br>";
			    			$bool_inserto_en_indice=true;
			    		}
			    	}//fin if
					
				}//fin insert si no existe
				else if($upsert_select=="" || $upsert_select=="update" || $upsert_select=="upsert")
				{
					$array_campos_procesados=array();
					$cont_campos=0;
					while($cont_campos < count($campos))
					{
						$campo_procesado="";
						$campo_procesado = trim($campos[$cont_campos] );
						if($tabla_pre_diligenciada!="11")
						{
							$campo_procesado = preg_replace("/[^A-Za-z0-9: ;\-@.\/]/", "", $campo_procesado );
							$campo_procesado = str_replace("\n","",$campo_procesado);
							$campo_procesado = str_replace("\"","",$campo_procesado);
							$campo_procesado = str_replace("'","",$campo_procesado);
						}//fin if
						
						$array_campos_procesados[]=utf8_decode($campo_procesado);
						
						$cont_campos++;
					}
				
					$sql_upsert="";
					//INICIA QUERY UPDATE "nombre_tabla_aqui"
					$sql_upsert.="UPDATE $nombre_tabla SET ";
					
					$cont_nombres_columnas=0;
					while($cont_nombres_columnas<count($array_columnas_tablas))
					{
						if(trim($array_campos_procesados[$cont_nombres_columnas])!="")
						{
							if($cont_nombres_columnas!=0)
							{
								$sql_upsert.=",";
							}
							$sql_upsert.=$array_columnas_tablas[$cont_nombres_columnas]."='".trim($array_campos_procesados[$cont_nombres_columnas])."'";
						}//fin if realiza update de la columna solo si no esta vacio
						$cont_nombres_columnas++;
					}
					
					$sql_upsert.=" WHERE ";
					
					if($tabla_pre_diligenciada!="11")
					{
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
					}//fin if no es 11
					else
					{
						$sql_verificar.=" cod_prestador_servicios_salud='$codigo_prestador_para_insercion' 
						AND codigo_eapb='$codigo_eapb_para_insercion' 
						AND fecha_inicio_periodo='$fecha_inicial_para_analisis' 
						AND fecha_de_corte='$fecha_de_corte_temp_analisis' 
						AND numero_fila='$cont4505Registro' 
						AND nombre_archivo='$nombre_archivo4505'
						AND fecha_y_hora_validacion='$fecha_validacion_para_analisis' ";
					}//fin else es 11
					
					$sql_upsert.=";";
					//FIN QUERY UPDATE "nombre_tabla_aqui"

					fwrite($archivo_queries, $sql_upsert."\n");

					//echo "update: ".$sql_upsert."<br>";
										
					$error_bd="";
					try
					{
						$bool_funciono=$coneccionBD->insertar_no_warning_get_error_no_crea_cierra($sql_upsert, $error_bd);
						
					}
					catch (Exception $e) {}
				}//fin update si existe
			}//fin if numero de campos  es adecuado
			else
			{
				$error_bd="";
				if(count($campos)==count($array_columnas_tablas) )
				{
					$error_bd.="ERROR: el numero de campos no es correcto. Numero de campos del registro ".count($campos).", numero de columnas indicadas para la tabla ".count($array_columnas_tablas);
				}
				if($todos_los_campos_estan_vacios==true)
				{
					$error_bd.="ERROR: todos los campos estan vacios. ";
				}
				if($hay_llaves_vacias==true)
				{
					$error_bd.="ERROR: alguna de las llaves indicadas tine su campo correspondiente vacio";
				}
				$bool_funciono=true;
			}//error cuando no cumple con el numero de campos

			
			$mensaje="";
			if($bool_funciono==false)
			{
				$mensaje="<p style='color:green;'> Fila para $nombre_tabla de salud insertada en la base de datos. Linea(cuenta desde 0): ".$cont_linea.".  Linea(cuenta desde 1): ".($cont_linea+1).". num columnas leidas ".count($campos).", numero de columnas indicadas para la tabla ".count($array_columnas_tablas)."  para un total de lineas $lineas_del_archivo  que posee el archivo ".$coneccionBD->get_filas_afectadas_update()."</p>";
				
				$aciertos++;
			}
			else
			{
				$mensaje="<p style='color:red'>Error al insertar. Linea(cuenta desde 0): ".$cont_linea.".  Linea(cuenta desde 1): ".($cont_linea+1).". num columnas leidas ".count($campos).", numero de columnas indicadas para la tabla ".count($array_columnas_tablas)." para un total de lineas $lineas_del_archivo  que posee el archivo ".$coneccionBD->get_filas_afectadas_update()."</p>";


				$errores++;

				$error_adicional="";
				if($todos_los_campos_estan_vacios==true)
				{
					$error_adicional.="Todos los campos estan vacios. ";
				}
				if($hay_llaves_vacias==true)
				{
					$error_adicional.=" Alguna de las llaves indicadas tine su campo correspondiente vacio. ";
				}
				$error_para_txt="";
				$error_para_txt.="Error al insertar. Linea(cuenta desde 0): ".$cont_linea.".  Linea(cuenta desde 1): ".($cont_linea+1).". num columnas ".count($campos).", numero de columnas indicadas para la tabla ".count($array_columnas_tablas).". ".$error_adicional."\n".$error_bd."\n".$sql_upsert." \n";	
				fwrite($archivo_error, $error_para_txt);			
			}
			/*
			if($error_para_txt!="")
			{
				$mensaje.="<br>ULTIMO ERROR ENCONTRADO: ".alphanumericAndSpace($error_para_txt)."<br>";
			}
			*/
			
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
			}//fin if
			else
			{
				$final="<p style='color:blue;>Se ha terminado de procesar el archivo.</p>";
				echo $var_script_html;
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$final.$mensaje.$mensaje_aciertos.$mensaje_errores.$botones_descarga_html."\";</script>";
				ob_flush();
				flush();
			}//fin else
				
		}//fin if linea no vacia
		else 
		{
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
			}//fin if
			else
			{
				$final="<p style='color:blue;>Se ha terminado de procesar el archivo.</p>";
				echo $var_script_html;
				echo "<script>document.getElementById('mensaje').innerHTML=\"".$final.$mensaje.$mensaje_aciertos.$mensaje_errores.$botones_descarga_html."\";</script>";
				ob_flush();
				flush();
			}//fin else
		}//fin else linea vacia

		$cont4505Registro++;
		
		$cont_linea++;
	}//fin while lectura archivo
	fclose($archivo_queries);
	fclose($archivo_cargar);

	//fwrite($archivo_error, $error_para_txt); // se pasa mas arriba para que escriba errores mientras avanza, y no al final y asi ahorra memora RAM
	fclose($archivo_error);
	
}//if se subio archivo

$coneccionBD->cerrar_conexion();
?>