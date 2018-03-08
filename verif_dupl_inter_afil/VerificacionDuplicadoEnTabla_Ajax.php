<?php
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '2000M');

error_reporting(E_ALL);
ini_set('display_errors', '0');
include_once ('../utiles/clase_coneccion_bd.php');
$coneccionBD = new conexion();
$coneccionBD->crearConexion();


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


$ruta_archivo_leer="";
$pathdirectorverificacion="";
$pathdefinitivos="";
$pathlogproces="";
$pathtempprogreso="";

$arrayRequest=$_REQUEST;

 if(isset($arrayRequest['ruta_archivo_leer'])==true)
 {
 	$pathdirectorverificacion=trim($arrayRequest['pathdirectorverificacion']);
 	$ruta_archivo_leer=trim($_REQUEST['ruta_archivo_leer']);

 	$pathdefinitivos=trim($_REQUEST['pathdefinitivos']);
 	$pathlogproceso=trim($_REQUEST['pathlogproceso']);
 	$pathtempprogreso=trim($_REQUEST['pathtempprogreso']);

 	$archivoDefinitivos=fopen($pathdefinitivos, "w");
 	fclose($archivoDefinitivos);

 	date_default_timezone_set('America/Bogota');
	$fecha_actual=date('Y-m-d');
	$tiempo_actual=date('H:i:s');


	$lineasArchivoLeer=contar_lineas_archivo($ruta_archivo_leer);

	//echo "ruta_archivo_leer: ".$ruta_archivo_leer."<br>";

	$cont_porcentaje=0;

	$contDefinitivosDeDuplicadosEncontrados=0;

	$cont_fila_actual=0;

	$cont_linea_actual_archivo=0;

	$handle1 = fopen($ruta_archivo_leer, "r");		
	while(!feof($handle1))
	{
		$line1 = trim(fgets($handle1) );

		$array_linea_a1=explode("|", $line1);

		//echo "array de linea actual ".print_r($array_linea_a1,true)."<br>";

		

		$numero_identificacion_afiliado="";
		//aca es campo cero, debido a que no es un archivo 4505
		if(isset($array_linea_a1[0])==true)
		{
			$array_linea_a1[0]=preg_replace("/[^a-zA-Z0-9]+/", "", trim($array_linea_a1[0]) );
			$numero_identificacion_afiliado=trim($array_linea_a1[0]);

		}//fin if

		$tipo_id_afiliado_archivo="";
		if(isset($array_linea_a1[1])==true)
		{

			$tipo_id_afiliado_archivo=trim($array_linea_a1[1]);

		}//fin if

		$primer_apellido="";
		if(isset($array_linea_a1[2])==true)
		{

			$primer_apellido=trim($array_linea_a1[2]);

		}//fin if
		$primer_nombre="";
		if(isset($array_linea_a1[3])==true)
		{

			$primer_nombre=trim($array_linea_a1[3]);

		}//fin if

		$segundo_apellido="";
		if(isset($array_linea_a1[4])==true)
		{

			$segundo_apellido=trim($array_linea_a1[4]);

		}//fin if

		$segundo_nombre="";
		if(isset($array_linea_a1[5])==true)
		{

			$segundo_nombre=trim($array_linea_a1[5]);

		}//fin if


		$sexo_archivo="";
		if(isset($array_linea_a1[6])==true)
		{

			$sexo_archivo=trim($array_linea_a1[6]);

		}//fin if

		$fecha_nacimiento_archivo="";
		if(isset($array_linea_a1[7])==true)
		{

			$fecha_nacimiento_archivo=trim($array_linea_a1[7]);

		}//fin if

		$consecutivo_del_archivo=0;
		if(isset($array_linea_a1[8])==true)
		{

			$consecutivo_del_archivo=intval(trim($array_linea_a1[8]));

		}//fin if


		$parte_from="";
		$parte_from.="  gioss_afiliados_eapb_mp ";

		$parte_where="";
		$parte_where.="
				id_afiliado='$numero_identificacion_afiliado'
			 ";
		

	 	$order_by=" ORDER BY id_afiliado asc ";
		$query_a_extraer_resultados_contar="SELECT count(*) as contador_filas FROM $parte_from ";
		if(trim($parte_where)!="")
		{
			$query_a_extraer_resultados_contar.=" WHERE $parte_where  ; ";
		}

		$query_comun="	select * FROM $parte_from   ";
		
		if(trim($parte_where)!="")
		{
			$query_comun.=" WHERE $parte_where  $order_by ";//aca no lleva punto y coma debido a que despues va el limit y offset en el ciclo
		}//fin if
		

		//echo $query_a_extraer_resultados_contar."<br>";
		//echo $query_comun."<br>";
		

		$numero_filas=0;


		

		$error_bd_seq="";
		$resultados_contador=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_a_extraer_resultados_contar, $error_bd_seq);		

		if($error_bd_seq!="")
		{
		   // echo "Error al contar los resultados.<br>";
		}//fin if
		else
		{
			$numero_filas=$resultados_contador[0]['contador_filas'];
		}//fin else 

		$cantidad_registros_bloque=50000;

		$ultima_posicion=0;

		if($numero_filas>0 && isset($array_linea_a1[8])==true)
		{
			//echo "Numero Coincidencias Hayadas en BD ( $numero_filas ) Para la fila actual del archivo leido ( $cont_linea_actual_archivo ) <br>";
		}
		ob_flush();
	  	flush();

	  	
		if($numero_filas>0)
		{

			while($ultima_posicion<$numero_filas)
			{

				$query_a_extraer_resultados="";

				$query_a_extraer_resultados.=$query_comun;

				$query_a_extraer_resultados.=" LIMIT $cantidad_registros_bloque OFFSET $ultima_posicion; ";

				$error_bd_seq="";
				$resultados=$coneccionBD->consultar_no_warning_get_error_no_crea_cierra($query_a_extraer_resultados, $error_bd_seq);		
				if($error_bd_seq!="")
				{
				    //echo "Error al consultar los resultados.<br>";

				}//fin if
				else if(is_array($resultados) && count($resultados)>0 )
				{
					$NV=-5;
					$NI=-4;
					$RN=-3;//recien nacido
					$MS=-2;//menor sin identificacion
					$AS=-1;//adulto sin identificacion
					$PA=0;//pasaporte
					$CE=2.5;//cedula extranjeria
					$RC=1;//registro civil
					$TI=2;//tarjeta de identidad
					$CC=3;//cedula de ciudadania

					//con doble signo $ para traer el valor de la variable indicada por la cadena
					$se_deja_estos_datos_de_afiliado=false;
					$peso_del_tipo_id_archivo_linea_actual=-4;//se inicializa en un valor bajo solo por si no existe
					if(isset($$tipo_id_afiliado_archivo)==true)
					{
						$peso_del_tipo_id_archivo_linea_actual=$$tipo_id_afiliado_archivo;
					}//fin if existe la variable contenida en el string
					$tiene_el_mayor_peso_tipo_identificacion=-3;
					$tiene_el_mayor_peso_tipo_identificacion=$peso_del_tipo_id_archivo_linea_actual;

					$entro_a_dejar=false;

					foreach ($resultados as $key => $fila_actual) 
					{
						$tipo_id_bd=trim($fila_actual['tipo_id_afiliado']);
						if($tipo_id_afiliado_archivo!=$tipo_id_bd)
						{
							$valor_actual_peso_tipo_id_bd=$$tipo_id_bd;
							if($valor_actual_peso_tipo_id_bd>$tiene_el_mayor_peso_tipo_identificacion)
							{
								$tiene_el_mayor_peso_tipo_identificacion=$valor_actual_peso_tipo_id_bd;
							}//fin if
						}//fin if
					}//fin foreach

					$array_fecha_nacimiento=explode("-",$fecha_nacimiento_archivo);
					$fecha_campo_actual=explode("-",$fecha_actual);
					$yearsEdad=-1;
					if(preg_match("/[0-9][0-9][0-9][0-9][\-][0-9][0-9][\-][0-9][0-9]/", $fecha_nacimiento_archivo)===1)
					{
						$array_calc_edad_actual=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_actual[2]."-".$fecha_campo_actual[1]."-".$fecha_campo_actual[0]);
 						$yearsEdad=intval($array_calc_edad_actual['y']);
 					}//fin if
    

					if($tiene_el_mayor_peso_tipo_identificacion==$peso_del_tipo_id_archivo_linea_actual
						&& (
							(
								($tipo_id_afiliado_archivo=="CC" 
									|| $tipo_id_afiliado_archivo=="CE" 
									|| $tipo_id_afiliado_archivo=="PA"
									|| $tipo_id_afiliado_archivo=="SA"  
								)
								&& $yearsEdad>=18 
							)//parte mayor de 18
							||
							(
								($tipo_id_afiliado_archivo=="TI" 
									|| $tipo_id_afiliado_archivo=="RC"
									|| $tipo_id_afiliado_archivo=="PA"
									|| $tipo_id_afiliado_archivo=="MS"
									 )
								&& $yearsEdad<18 
							)//parte mayor de 18
						)//fin sigue del and
					)//fin condicion
					{
						$se_deja_estos_datos_de_afiliado=true;
					}//fin if
					else if(
						( $tipo_id_afiliado_archivo=="TI" && $yearsEdad>=7 && $yearsEdad<18 )
						|| ( $tipo_id_afiliado_archivo=="RC" && $yearsEdad<7 )
						)//fin condicion
					{
						$se_deja_estos_datos_de_afiliado=true;
					}//fin else if
					
					foreach ($resultados as $key => $fila_actual) 
					{
						$linea_a_escribir="";
						$cont_colfila=0;


						if($cont_fila_actual==0)
						{
								
						}//fin if

						

						$cont_fila_actual++;

						//PRESUNTOS IGUALES

						$boolPrimerApellidoCoincideEnAlgunCampo5678=false;
						$boolSegundoApellidoCoincideEnAlgunCampo5678=false;
						$boolPrimerNombreCoincideEnAlgunCampo5678=false;						
						$boolSegundoNombreCoincideEnAlgunCampo5678=false;

						if(trim($fila_actual['primer_apellido'])!="")
						{
							if(strpos($primer_apellido, trim($fila_actual['primer_apellido']) )!==false)
							{
								$boolPrimerApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($primer_nombre, trim($fila_actual['primer_apellido']) )!==false)
							{
								$boolPrimerApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_apellido, trim($fila_actual['primer_apellido']) )!==false)
							{
								$boolPrimerApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_nombre, trim($fila_actual['primer_apellido']) )!==false)
							{
								$boolPrimerApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
						}//fin if p apellido no es cadena vacia

						if(trim($fila_actual['segundo_apellido'])!="")
						{
							if(strpos($primer_apellido, trim($fila_actual['segundo_apellido']) )!==false)
							{
								$boolSegundoApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($primer_nombre, trim($fila_actual['segundo_apellido']) )!==false)
							{
								$boolSegundoApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_apellido, trim($fila_actual['segundo_apellido']) )!==false)
							{
								$boolSegundoApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_nombre, trim($fila_actual['segundo_apellido']) )!==false)
							{
								$boolSegundoApellidoCoincideEnAlgunCampo5678=true;
							}//fin if
						}//fin if s apellido no es cadena vacia

						if(trim($fila_actual['primer_nombre'])!="")
						{
							if(strpos($primer_apellido, trim($fila_actual['primer_nombre']) )!==false)
							{
								$boolPrimerNombreCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($primer_nombre, trim($fila_actual['primer_nombre']) )!==false)
							{
								$boolPrimerNombreCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_apellido, trim($fila_actual['primer_nombre']) )!==false)
							{
								$boolPrimerNombreCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_nombre, trim($fila_actual['primer_nombre']) )!==false)
							{
								$boolPrimerNombreCoincideEnAlgunCampo5678=true;
							}//fin if
						}//fin if p nombre no es cadena vacia

						if(trim($fila_actual['segundo_nombre'])!="")
						{
							if(strpos($primer_apellido, trim($fila_actual['segundo_nombre']) )!==false)
							{
								$boolSegundoNombreCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($primer_nombre, trim($fila_actual['segundo_nombre']) )!==false)
							{
								$boolSegundoNombreCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_apellido, trim($fila_actual['segundo_nombre']) )!==false)
							{
								$boolSegundoNombreCoincideEnAlgunCampo5678=true;
							}//fin if
							if(strpos($segundo_nombre, trim($fila_actual['segundo_nombre']) )!==false)
							{
								$boolSegundoNombreCoincideEnAlgunCampo5678=true;
							}//fin if
						}//fin if s nombre no es cadena vacia

						$stringCoincide="";
						$contadorAprobacion=0;

						$boolSexoCoincide=false;

						if(trim($fila_actual['sexo'])!="")
						{

							if(strpos($sexo_archivo, trim($fila_actual['sexo']) )!==false)
							{
								$boolSexoCoincide=true;
								$contadorAprobacion++;
								$stringCoincide.="sexo coincide, ";
							}//fin if
						}//fin if sexo no es cadena vacia

						$boolFechaNacimientoCoincide=false;

						$stringFechaArchivo= correctorFormatoFechaVersionCorta( $fecha_nacimiento_archivo );
						$stringFechaBd=correctorFormatoFechaVersionCorta( trim($fila_actual['fecha_nacimiento']) );
						if($stringFechaBd!="")
						{
							$dateArchivo=date("Y-m-d", strtotime($stringFechaArchivo) );
							$dateBd=date("Y-m-d", strtotime($stringFechaBd) );

							if($dateArchivo==$dateBd)
							{
								$boolFechaNacimientoCoincide=true;
								$contadorAprobacion++;
								$stringCoincide.="fecha nacimiento coincide, ";
								
							}//fin if
						}//fin if fecha nacimiento no es cadena vacia

						$diasDiferenciasFechas=0;
						$valorAbs=0;
						if($boolFechaNacimientoCoincide==false)
						{
							$diasDiferenciasFechas=diferencia_dias_entre_fechas($stringFechaArchivo,$stringFechaBd);
							if($diasDiferenciasFechas!==false)
							{
								$valorAbs=abs($diasDiferenciasFechas);
							}//fin if
						}//fin if

						
						

						if($boolPrimerApellidoCoincideEnAlgunCampo5678==true)
						{
							$contadorAprobacion++;
							$stringCoincide.="P. apellido coincide, ";
						}//fin if
						if($boolSegundoApellidoCoincideEnAlgunCampo5678==true)
						{
							$contadorAprobacion++;
							$stringCoincide.="S. apellido coincide, ";
						}//fin if
						if($boolPrimerNombreCoincideEnAlgunCampo5678==true)
						{
							$contadorAprobacion++;
							$stringCoincide.="P. nombre coincide, ";
						}//fin if
						if($boolSegundoNombreCoincideEnAlgunCampo5678==true)
						{
							$contadorAprobacion++;
							$stringCoincide.="S. nombre coincide, ";
						}//fin if

						if($diasDiferenciasFechas!==false)
						{
							$stringCoincide.="Diferencia Entre Fechas Dias ($valorAbs) ";
						}
						else
						{
							$stringCoincide.="no se pudo calcular ( $stringFechaArchivo VS $stringFechaBd ) ";
						}//fin else
						

						
						

						//solo 6 para aprobacion
						if( 
							
								$tipo_id_afiliado_archivo!=trim($fila_actual['tipo_id_afiliado'])
							&&	(
									($contadorAprobacion>=4 
										|| ($contadorAprobacion>=3 &&  $diasDiferenciasFechas!==false  && $valorAbs==0	)
									 )						
									&& ($stringFechaBd!="" 
										&&
											( 
												($stringFechaArchivo!="" && $diasDiferenciasFechas!==false && $valorAbs<730)
												|| ($stringFechaArchivo=="" && $diasDiferenciasFechas===false)
												|| ($stringFechaArchivo=="1845-01-01" && $diasDiferenciasFechas!==false)
												|| ($stringFechaArchivo=="1835-01-01" && $diasDiferenciasFechas!==false)
												|| ($stringFechaArchivo=="1830-01-01" && $diasDiferenciasFechas!==false)
												|| ($stringFechaArchivo=="1825-01-01" && $diasDiferenciasFechas!==false)
												|| ($stringFechaArchivo=="1810-01-01" && $diasDiferenciasFechas!==false)
												|| ($stringFechaArchivo=="1805-01-01" && $diasDiferenciasFechas!==false)
												|| ($stringFechaArchivo=="1800-01-01" && $diasDiferenciasFechas!==false)
											)//fin and
										)//fin and
								)//fin and
							&& $se_deja_estos_datos_de_afiliado==true
							&& $entro_a_dejar==false
							
							
						 )//fin condicion
						{
							$mensajeLogProgreso="";
							
							$mensajeLogProgreso.= $stringCoincide."\n";
							$mensajeLogProgreso.= "( $consecutivo_del_archivo )".$contadorAprobacion." ( ".correctorFormatoFechaVersionCorta( $fecha_nacimiento_archivo )." vs ".correctorFormatoFechaVersionCorta( trim($fila_actual['fecha_nacimiento']) )." )\n";
							$mensajeLogProgreso.= "--Numero ID $numero_identificacion_afiliado PERMITIDO, tipo_id_afiliado_archivo $tipo_id_afiliado_archivo , tipo id bd ".trim($fila_actual['tipo_id_afiliado'])." , y cumple con los criterios de equivalencia para los otros campos \n\n";
							
							$archivoLogProceso=fopen($pathlogproceso, "a");
							fwrite($archivoLogProceso, "\n".$mensajeLogProgreso);
							fclose($archivoLogProceso);
							

							$arrayArreglados=array();
							$arrayArreglados=$array_linea_a1;

							$arrayArreglados[1]=$tipo_id_afiliado_archivo;//deja el del archivo
							$arrayArreglados[2]=$primer_apellido;
							$arrayArreglados[3]=$segundo_apellido;
							$arrayArreglados[4]=$primer_nombre;
							$arrayArreglados[5]=$segundo_nombre;
							$arrayArreglados[6]=$fecha_nacimiento_archivo;
							$arrayArreglados[7]=$sexo_archivo;
							if($primer_apellido=="NONE" || $primer_apellido=="")
							{
								$arrayArreglados[2]=trim($fila_actual['primer_apellido']);
							}//fin if
							if($segundo_apellido=="NONE" || $segundo_apellido=="")
							{
								$arrayArreglados[3]=trim($fila_actual['segundo_apellido']);
							}//fin if
							if($primer_nombre=="NONE" || $primer_nombre=="")
							{
								$arrayArreglados[4]=trim($fila_actual['primer_nombre']);
							}//fin if
							if($segundo_nombre=="NONE" || $segundo_nombre=="")
							{
								$arrayArreglados[5]=trim($fila_actual['segundo_nombre']);
							}//fin if
							if($fecha_nacimiento_archivo=="")
							{
								$arrayArreglados[6]=trim($fila_actual['fecha_nacimiento']);
							}//fin if
							if($sexo_archivo=="")
							{
								$arrayArreglados[7]=trim($fila_actual['sexo']);
							}//fin if

							$lineaArreglada=implode("|", $arrayArreglados);
							$archivoDefinitivos=fopen($pathdefinitivos, "a");
							fwrite($archivoDefinitivos, $lineaArreglada."\n");
							fclose($archivoDefinitivos);

							$entro_a_dejar=true;

							$contDefinitivosDeDuplicadosEncontrados++;
						}//fin if
						else
						{
							/*
							$mensajeLogProgreso="";
							
							$mensajeLogProgreso.= $stringCoincide."\n";
							$mensajeLogProgreso.= "( $consecutivo_del_archivo )".$contadorAprobacion." ( ".correctorFormatoFechaVersionCorta( $fecha_nacimiento_archivo )." vs ".correctorFormatoFechaVersionCorta( trim($fila_actual['fecha_nacimiento']) )." )\n";
							$mensajeLogProgreso.= "--Numero ID $numero_identificacion_afiliado NO PERMITIDO, tipo_id_afiliado_archivo $tipo_id_afiliado_archivo , tipo id bd ".trim($fila_actual['tipo_id_afiliado'])." , y cumple con los criterios de equivalencia para los otros campos \n\n";

							$archivoLogProceso=fopen($pathlogproceso, "a");
							fwrite($archivoLogProceso, "\n".$mensajeLogProgreso);
							fclose($archivoLogProceso);
							*/
						}//fin else
						//FIN PRESUNTOS IGUALES

					}//fin foreach resultado

				}//fin else
				else
				{
					//echo "Problema query comun<br>";
				}//fin else

				$ultima_posicion=$ultima_posicion+$cantidad_registros_bloque;

				//echo "ultima_posicion $ultima_posicion , cantidad_registros_bloque $cantidad_registros_bloque<br>";
				//ob_flush();
	  			//flush();

			}//fin while

			
			
		}//fin if numero de filas contadas en bd es mayor de cero
		else
		{
			
			//echo "No encontro el valor $numero_identificacion_afiliado en la tabla.";
		    
		}//fin else


		

		$muestra_mensaje_nuevo=false;
		$valor_antes_de_round=(($cont_linea_actual_archivo+1)*100)/$lineasArchivoLeer;
	    $porcentaje=round($valor_antes_de_round,1,PHP_ROUND_HALF_UP);
	    if($porcentaje!=$cont_porcentaje || ($porcentaje==0 && ($cont_linea_actual_archivo+1)==1) || $porcentaje>=100)
	    {
	     $cont_porcentaje=$porcentaje;
	     $muestra_mensaje_nuevo=true;
	    }//fin if

	    if($muestra_mensaje_nuevo==true)
	    {
	    	$fecha_temp=date('Y-m-d');
			$tiempo_temp=date('H:i:s');
			$mensajeEstadoActualProgreso="Se han encontrado $contDefinitivosDeDuplicadosEncontrados registros definitivos.<br>Linea actual $cont_linea_actual_archivo de $lineasArchivoLeer en total del archivo.<br> $cont_porcentaje % <br>Inicio a las $tiempo_actual de $fecha_actual, tiempo en este momento $tiempo_temp de $fecha_temp <br>";
			$archivoTempProgreso=fopen($pathtempprogreso, "w");
			fwrite($archivoTempProgreso, $mensajeEstadoActualProgreso);
			fclose($archivoTempProgreso);
	  	}//fin if

		$cont_linea_actual_archivo++;
	}//fin while
	
	date_default_timezone_set('America/Bogota');
	$fecha_temp=date('Y-m-d');
	$tiempo_temp=date('H:i:s');
	$mensajeDirector=$pathdefinitivos."|tiempo_temp_".$tiempo_temp."|fecha_temp_".$fecha_temp."|contDefinitivosDeDuplicadosEncontrados_".$contDefinitivosDeDuplicadosEncontrados."|cont_linea_actual_archivo_".$cont_linea_actual_archivo;
	$archivoDirectorVerificacion=fopen($pathdirectorverificacion, "a");
	fwrite($archivoDirectorVerificacion, $mensajeDirector."\n");
	fclose($archivoDirectorVerificacion);

 }//fin if
 $coneccionBD->cerrar_conexion();
?>