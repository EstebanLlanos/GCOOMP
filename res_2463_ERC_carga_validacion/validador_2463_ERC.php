<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/conf_personalizada.php';

//recibe en dia mes year
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


function diferencia_dias_entre_fechas($fecha_1,$fecha_2)
{
    //las fechas deben ser cadenas de 10 caracteres en el siguiente formato AAAA-MM-DD, ejemplo: 1989-03-03
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


function validar_ERC($campos,
		     $nlinea,
		     &$consecutivo_errores,
		     $array_tipo_validacion,
		     $array_grupo_validacion,
		     $array_detalle_validacion,
		     $nombre_archivo_registrado,
		     $fecha_de_corte,
		     $fecha_inferior_pv,
		     $cod_prestador,
		     $cod_eapb,
		     &$diccionario_identificacion,
		     &$diccionario_identificacion_lineas,
		     &$coneccionBD,
		     $array_numero_campo_bd
		 )
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();
	
	//CALCULO EDAD
	$fecha_nacimiento= explode("-",$campos[6]);
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
	
	
	if($bool_fecha_nacimiento_valida==true)
	{
	    
	    $string_fecha_nacimiento=date($fecha_nacimiento[0]."-".$fecha_nacimiento[1]."-".$fecha_nacimiento[2]);
	    
	    $fecha_nacimiento_format=new DateTime($string_fecha_nacimiento);
	    $fecha_corte_format=new DateTime($fecha_de_corte);
	
	    $interval = date_diff($fecha_nacimiento_format,$fecha_corte_format);
	    $edad_dias =(float)($interval->days);
	    
	    //$edad= (float)($interval->days / 365);		    
	    //$edad_meses = (float)($interval->days / 30.4368499);
	    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
	    
	    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
	    $array_fecha_corte=explode("-",$fecha_de_corte);
	    $array_edad=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte[2]."-".$array_fecha_corte[1]."-".$array_fecha_corte[0]);
	    $edad_meses=(intval($array_edad['y'])*12)+$array_edad['m'];
	    $edad=intval($array_edad['y']);
	    //echo "<script>alert('total age years $edad , total age months $edad_meses, total days $edad_dias');</script>";
	    
	    $edad_semanas = (float)($interval->days / 7);
	    $verificador_edad= (float)$interval->format("%r%a");
	}
	
	$fecha_campo_27_1=explode("-",$campos[29]);
	$bool_fecha_campo27_1_valida=true;
	if(count($fecha_campo_27_1)!=3
	   || !(ctype_digit($fecha_campo_27_1[0]) && ctype_digit($fecha_campo_27_1[1]) && ctype_digit($fecha_campo_27_1[2]) )
	   || !checkdate($fecha_campo_27_1[1],$fecha_campo_27_1[2],$fecha_campo_27_1[0])
	   || $campos[29]=="1800-01-01"
	   || $campos[29]=="1845-01-01" 
	   )
	{			
		$bool_fecha_campo27_1_valida=false;
	}
	
	
	$edad_r27_1= -1;
	$edad_dias_r27_1 =-1;
	$edad_meses_r27_1 =-1;
	$edad_semanas_r27_1 = -1;
	$verificador_edad_r27_1= -1;
	
	if($bool_fecha_nacimiento_valida==true && $bool_fecha_campo27_1_valida==true)
	{
	    
	    $string_fecha_nacimiento=date($fecha_nacimiento[0]."-".$fecha_nacimiento[1]."-".$fecha_nacimiento[2]);
	    $string_campo27_1_nacimiento=date($fecha_campo_27_1[0]."-".$fecha_campo_27_1[1]."-".$fecha_campo_27_1[2]);
	    $fecha_nacimiento_format=new DateTime($string_fecha_nacimiento);
	    $fecha_campo27_1_format=new DateTime($string_campo27_1_nacimiento);
	
	    $interval = date_diff($fecha_nacimiento_format,$fecha_campo27_1_format);
	    $edad_dias_r27_1 =(float)($interval->days);
	    
	    //$edad= (float)($interval->days / 365);		    
	    //$edad_meses = (float)($interval->days / 30.4368499);
	    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
	    
	    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
	    $array_edad=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$fecha_campo_27_1[2]."-".$fecha_campo_27_1[1]."-".$fecha_campo_27_1[0]);
	    $edad_meses_r27_1=(intval($array_edad['y'])*12)+$array_edad['m'];
	    $edad_r27_1=intval($array_edad['y']);
	    //echo "<script>alert('total age years $edad , total age months $edad_meses, total days $edad_dias');</script>";
	    
	    $edad_semanas_r27_1 = (float)($interval->days / 7);
	    $verificador_edad_r27_1= (float)$interval->format("%r%a");
	}
	//FIN CALCULO EDAD
	
	//CALCULO TFG
	$formula_cockcroft_gault=0.0;
	$formula_swahartz=0.0;
	
	$sexo=$campos[7];//campo 8
	$peso=floatval($campos[24]);//campo 23
	$creatinina=floatval($campos[28]);//campo 27
	$talla=floatval($campos[25]);//campo 24
	$edad_years_formula=0;
	
	if(floatval($creatinina)==0)
	{
		$creatinina="0.1";
	}
	
	//determina si es valid el  calcular la formula 
	$es_formula_valida=true;
	if(intval($creatinina)==98 || intval($creatinina)==99  || $creatinina=="")
	{
	    $es_formula_valida=false;
	}
	
	if($sexo!="M" && $sexo!="F")
	{
	    $es_formula_valida=false;
	}
	//fin determina si es valid el  calcular la formula 
	
	$campo_n27_1=$campos[29];
	$c27_1_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n27_1,"1900-12-31");
	if($campo_n27_1!="1800-01-01"
	   && $campo_n27_1!="1845-01-01"
	   && $c27_1_es_fecha_calendario<0)
	{
		$edad_years_formula=floatval($edad_r27_1);
	}
	else if($campo_n27_1=="1800-01-01")
	{
		$edad_years_formula=floatval($edad);
	}
	else if($c27_1_es_fecha_calendario>=0
			|| $campo_n27_1!="1800-01-01")
	{
		$es_formula_valida=false;
	}
	
	//parte 2 formula valida
	if($edad_years_formula>=18)
	{
	    $array_decimal_check_peso=explode(".",$peso);
	    if($peso=="")
	    {
		$es_formula_valida=false;
	    }
	    else if(!is_numeric($peso))
	    {
		$es_formula_valida=false;   
	    }//fin if
	    
	}
	else
	{
	    $array_decimal_check_tasa=explode(".",$talla);
	    if($talla=="")
	    {
		$es_formula_valida=false;
	    }
	    else if(!is_numeric($talla))
	    {
		$es_formula_valida=false;   
	    }//fin if
	    else if(is_numeric($talla) && count($array_decimal_check_tasa)>1 )
	    {
		$es_formula_valida=false;  
	    }
	}
	//fin parte 2 formula valida
	
	if($edad_years_formula>=18)
	{
		if($sexo=="M")
		{
			$formula_cockcroft_gault=((140-$edad_years_formula)*$peso)/(72*$creatinina);
		}
		else if($sexo=="F")
		{
			$formula_cockcroft_gault=(((140-$edad_years_formula)*$peso)/(72*$creatinina))*0.85;
		}
	}
	else
	{
		if($edad_years_formula<1)
		{
			$formula_swahartz=(0.50*$talla)/($creatinina);
		}
		else if($edad_years_formula<=12)
		{
			$formula_swahartz=(0.55*$talla)/($creatinina);
		}
		else if($edad_years_formula<=17)
		{
			if($sexo=="M")
			{
				$formula_swahartz=(0.70*$talla)/($creatinina);
			}
			else if($sexo=="F")
			{
				$formula_swahartz=(0.57*$talla)/($creatinina);
			}
		}
	}
	
	$TFG=0.0;
	if($edad_years_formula>=18)
	{
		$TFG=$formula_cockcroft_gault;
	}
	else
	{
		$TFG=$formula_swahartz;
	}
	
	$string_tfg="".$TFG;
	$array_str_tfg=explode(".",$string_tfg);
	if(count($array_str_tfg)>1)
	{
	    if(strlen($array_str_tfg[0])<3)
	    {
		if(strlen($array_str_tfg[1])>1)
		{
		    $array_str_tfg[1]=substr($array_str_tfg[1],0,1);
		}
		$string_tfg=$array_str_tfg[0].".".$array_str_tfg[1];
		$TFG=floatval($string_tfg);
	    }
	    else
	    {
		$string_tfg=$array_str_tfg[0];
		$TFG=floatval($string_tfg);
	    }
	}
	
	if($TFG>250)
	{
	    $TFG=250;
	}
	else if($TFG<1)
	{
	    $TFG=1;
	}
	//FIN CALCULO TFG
	
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	/*
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='06' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0601' ORDER BY numero_de_orden ";
	$query_consulta_estructura_numero_campos.=" ; ";
	$resultado_query_estructura_campos=$coneccionBD->consultar2_no_crea_cierra($query_consulta_estructura_numero_campos);
	
	$array_numero_campo_bd=array();
	if(count($resultado_query_estructura_campos)>0)
	{
		foreach($resultado_query_estructura_campos as $estructura_campo)
		{
			$array_numero_campo_bd[intval($estructura_campo["numero_de_orden"])]=$estructura_campo["numero_de_campo"];
		}
	}
	*/
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $longitud_campo_ver_characters=strlen($campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters="";
	    $campo_ver_characters=str_replace(array("-","."),"",$campos_ver_characters[$cont_campos]);
	    
	    $bool_ver_characters=true;
	    if($campo_ver_characters!="")
	    {
		$bool_ver_characters=ctype_alnum($campo_ver_characters);
	    }
	    
	    $campo_fix="";
	    $campo_fix=str_replace(",","(aqui hay una coma)",$campos[$cont_campos]);
	    
	    
	    
	    if($cont_campos==118)
	    {
		$campo_fix=preg_replace("/[^A-Za-z0-9:,.\/\_\|\-\s+]/", "", trim($campo_fix) );
	    }
	    
	    if($bool_ver_characters==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}		
		//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104099";
		$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...-".$campo_fix."- longitud: $longitud_campo_ver_characters ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
		$consecutivo_errores++;
		
		$hubo_errores=true;
	    }
	    
	    $campos[$cont_campos]=str_replace(",",".",$campos[$cont_campos]);
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//numero_orden 0 numero campo 1 
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>20)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
				
	}//if si existe campo
	
	//numero_orden 1 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>30)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101014";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	//numero_orden 2 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>20)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 3 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>30)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101014";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 4  numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if

		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( $campos[$numero_campo]!="RC" &&  $campos[$numero_campo]!="TI" &&  $campos[$numero_campo]!="CC" &&  $campos[$numero_campo]!="CE" &&  $campos[$numero_campo]!="PA" &&  $campos[$numero_campo]!="MS" &&  $campos[$numero_campo]!="AS" &&  $campos[$numero_campo]!="CD")
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103048";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 5 numero campo 6 Numero Id
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>20)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$tipo_id=$campos[4];
			$id_afiliado=$campos[$numero_campo];
			//regimen es numero de orden 8 numero de campo 9
			$regimen_afiliacion=$campos[8];
			$consulta="";
			if($regimen_afiliacion=="C")
			{
				$consulta.="SELECT * FROM gioss_afiliados_eapb_rc WHERE id_afiliado='".$id_afiliado."' AND tipo_id_afiliado='".$tipo_id."' AND codigo_eapb='".$cod_eapb."' ; ";
			}
			if($regimen_afiliacion=="S")
			{
				$consulta.="SELECT * FROM gioss_afiliados_regimen_subsidiado WHERE id_afiliado='".$id_afiliado."' AND tipo_id_afiliado='".$tipo_id."' AND codigo_eapb='".$cod_eapb."' ; ";
			}
			if($regimen_afiliacion=="E")
			{
				$consulta.="SELECT * FROM gioss_afiliados_eapb_mp WHERE id_afiliado='".$id_afiliado."' AND tipo_id_afiliado='".$tipo_id."' AND codigo_eapb='".$cod_eapb."' ; ";
			}
			if($regimen_afiliacion=="P")
			{
				$consulta.="SELECT * FROM gioss_afiliados_eapb_rp WHERE id_afiliado='".$id_afiliado."' AND tipo_id_afiliado='".$tipo_id."' AND codigo_eapb='".$cod_eapb."' ; ";
			}
			if($regimen_afiliacion=="N")
			{
				$consulta.="SELECT * FROM gioss_afiliados_eapb_nv WHERE id_afiliado='".$id_afiliado."' AND tipo_id_afiliado='".$tipo_id."' AND codigo_eapb='".$cod_eapb."' ; ";
			}
			
			$resultado=array();
			if($consulta!="")
			{
			    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			}
			if( count($resultado)==0  )
			{
				//ES INFORMATIVO
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0203001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0203,".$array_grupo_validacion["0203"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
			}//fin if
			
			//validacion de calidad
			//VERIFICACION DUPLICADOS
			$tipo_identificacion=$campos[4];
			$identificacion=$campos[5];
			if(!array_key_exists($tipo_identificacion."_".$identificacion,$diccionario_identificacion))
			{
				//echo "<script>alert('no estaba');</script>";
				$diccionario_identificacion[$tipo_identificacion."_".$identificacion]=1;
				$diccionario_identificacion_lineas[$tipo_identificacion."_".$identificacion]=array();
				$diccionario_identificacion_lineas[$tipo_identificacion."_".$identificacion][]=($nlinea+1);
			}
			else
			{
				//echo "<script>alert('si estaba');</script>";
				$diccionario_identificacion[$tipo_identificacion."_".$identificacion]++;
				$diccionario_identificacion_lineas[$tipo_identificacion."_".$identificacion][]=($nlinea+1);

				$lineas_coincidentes_string=implode(" - ", $diccionario_identificacion_lineas[$tipo_identificacion."_".$identificacion]);
				
				//se pasan los duplicados al interior del documento de inconsistencias
				if($errores_campos!=""){$errores_campos.="|";}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105090";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$tipo_identificacion."_".$identificacion." $lineas_coincidentes_string ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
			}//cuando ya esta la posicion en el array(los arreglos de php se usan como diccionarios)
			//FIN VERIFICACION DUPLICADOS
		}
	}//if si existe campo
	
	
	//numero_orden 6 numero campo 7 fecha nacimiento
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
				
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$fecha_1900="1900-12-31";
			
			$verificador=0;
			$verificador_2=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);
				$fecha_1900_format=new DateTime($fecha_1900);
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				$interval_2 = date_diff($fecha_campo_format,$fecha_1900_format);
				$verificador_2= (float)$interval_2->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if($verificador<0 || $verificador_2>0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103015";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 7 numero campo 8 sexo
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( $campos[$numero_campo]!="M" &&  $campos[$numero_campo]!="F"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103020";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
		}
		
	}//if si existe campo
	
	
	//numero_orden 8  numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido regimen
			if(trim($campos[$numero_campo])!="C" && trim($campos[$numero_campo])!="S" && trim($campos[$numero_campo])!="P" && trim($campos[$numero_campo])!="N" && trim($campos[$numero_campo])!="E")
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103052";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
		}
	}//if si existe campo
	
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>6)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101007";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103017";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>6) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
		}
	}//if si existe campo
	
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>16)
			   && (intval($campos[$numero_campo])<31 || intval($campos[$numero_campo])>39)
			   &&  (intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>59)
			   && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_mpio WHERE cod_municipio='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103051";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
		}
		
	}//if si existe campo
	
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if
		
		if($campos[$numero_campo]!="")
		{
		
			//longitud de campo
			if(strlen($campos[$numero_campo])>30)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101014";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
		}
		
	}//if si existe campo
	
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="")
		{
		
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$fecha_1995="1995-01-01";
			
			$verificador=0;
			$verificador_2=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);
				$fecha_1995_format=new DateTime($fecha_1995);
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				$interval_2 = date_diff($fecha_campo_format,$fecha_1995_format);
				$verificador_2= (float)$interval_2->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if($verificador<0 || $verificador_2>0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=12)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101012";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103050";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
		 
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=intval($campos[47]);
			if($campo_n38==0 && trim($campos[$numero_campo])!="1800-01-01" && trim($campos[$numero_campo])!="1845-01-01")
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105021";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			*/
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if
		
		if($campos[$numero_campo]!="")
		{
			
			//longitud de campo
			if(strlen($campos[$numero_campo])!=1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n20=$campos[20];
			$campo_n38=$campos[47];
			if(intval($campos[$numero_campo])==2 && intval($campo_n20)==2  && intval($campo_n38)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
		}
	}//if si existe campo
	
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
		
			//validacion de calidad
			$campo_n18=$campos[17];
			$campo_n19=$campos[18];
			if(intval($campo_n18)==1 && trim($campo_n19)=="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campo_n18)!=1 && trim($campo_n19)!="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			*/
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 19  numero campo 19.1 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		 
		
		
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>9)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101009";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n18=$campos[17];
			if(intval($campo_n18)==1 && intval($campos[$numero_campo])==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campo_n18)!=1 && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105005";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 20  numero campo 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n18=$campos[17];
			$campo_n38=$campos[47];
			if(intval($campos[$numero_campo])==2 && intval($campo_n18)==2  && intval($campo_n38)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 21  numero campo 21 
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if
		
		if($campos[$numero_campo]!="")
		{
		
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n20=$campos[20];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n20)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105007";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			//validacion de calidad
			$campo_n20=$campos[20];
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n20)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105008";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 22  numero campo 21.1 
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
		
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>9)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101009";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n20=$campos[20];
			if(intval($campos[$numero_campo])==98 && intval($campo_n20)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105009";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n20)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
	}//if si existe campo
	
	
	//numero_orden 23  numero campo 22
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>5) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103007";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n20=$campos[20];
			$campo_n18=$campos[17];
			if(intval($campos[$numero_campo])==1 && intval($campo_n20)==2 && intval($campo_n18)==2 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n38=$campos[47];
			if(intval($campos[$numero_campo])==98 && intval($campo_n38)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105106";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 24  numero campo 23 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>1)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>500)  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 25  numero campo 24 
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		
			//longitud de campo
			if(strlen($campos[$numero_campo])>3)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<10 || intval($campos[$numero_campo])>300)  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103024";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
		}
	}//if si existe campo
	
	
	//numero_orden 26  numero campo 25
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>3)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<60 || intval($campos[$numero_campo])>300) && intval($campos[$numero_campo])!=999)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103028";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 27  numero campo 26 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>3)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<20 || intval($campos[$numero_campo])>150) && intval($campos[$numero_campo])!=999)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103027";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 28  numero campo 27 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if			
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.01 || floatval($campos[$numero_campo])>50) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103039";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n39=$campos[48];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n39)==5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105044";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			if(intval($campos[$numero_campo])==98 && (intval($campo_n39)==1 || intval($campo_n39)==2 || intval($campo_n39)==3 || intval($campo_n39)==4))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105112";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==99 && intval($campo_n39)==5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105044";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 29  numero campo 27.1 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n27=$campos[28];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n27)!=98 && intval($campo_n27)!=99 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105012";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 30  numero campo 28 
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.01 || floatval($campos[$numero_campo])>40) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103038";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n20=$campos[20];
			if(intval($campos[$numero_campo])!=98 &&  intval($campo_n20)==2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105037";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			//validacion de calidad
			$campo_n20=$campos[20];
			if((intval($campos[$numero_campo])==98 ) && intval($campo_n20)==1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105060";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 31  numero campo 28.1 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n28=$campos[30];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n28)!=98 && intval($campo_n28)!=99 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 29 
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if			
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.01 || floatval($campos[$numero_campo])>900)
			   && intval($campos[$numero_campo])!=9999
			   && intval($campos[$numero_campo])!=9888
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103040";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 29.1 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n29=$campos[32];
			if(trim($campos[$numero_campo])=="1845-01-01"
			   && intval($campo_n29)!=9999 && intval($campo_n29)!=9888
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105014";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 30 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
				
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<10 || floatval($campos[$numero_campo])>3000)
			   && intval($campos[$numero_campo])!=988
			   && intval($campos[$numero_campo])!=999
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103043";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n39=$campos[48];
			if(intval($campos[$numero_campo])==999 && intval($campo_n39)==5  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105044";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==988 && (intval($campo_n39)==1 || intval($campo_n39)==2 || intval($campo_n39)==3 || intval($campo_n39)==4))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105112";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 30.1 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n30=$campos[34];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n30)!=988  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105015";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n30)==988  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105107";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 31 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<20 || floatval($campos[$numero_campo])>900) && intval($campos[$numero_campo])!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103045";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 31.1 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n31=$campos[36];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n31)!=999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105016";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n31)==999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105108";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 32 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0 || floatval($campos[$numero_campo])>200) && intval($campos[$numero_campo])!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103036";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 32.1 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n32=$campos[38];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n32)!=999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105017";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n32)==999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105109";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 33 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<10 || floatval($campos[$numero_campo])>600) && intval($campos[$numero_campo])!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103044";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		
		}
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 33.1 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			
			//validacion de calidad
			$campo_n33=$campos[40];
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n33)!=999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105018";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n33)==999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105110";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 34 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0 || floatval($campos[$numero_campo])>5000) && intval($campos[$numero_campo])!=988 && intval($campos[$numero_campo])!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103037";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n39=$campos[48];
			if(intval($campos[$numero_campo])!=988 &&  (intval($campo_n39)==1 || intval($campo_n39)==2 || intval($campo_n39)==3 || intval($campo_n39)==98)  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105042";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])==999 && (intval($campo_n39)==1 || intval($campo_n39)==2 || intval($campo_n39)==3)   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105043";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			if(intval($campos[$numero_campo])==988 && (intval($campo_n39)==4 || intval($campo_n39)==5)   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105132";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 34.1 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n34=$campos[43];
			if(trim($campos[$numero_campo])=="1845-01-01"
			   && intval($campo_n34)!=988 && intval($campo_n34)!=999
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105019";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="1845-01-01"
			   && (intval($campo_n34)==988 || intval($campo_n34)==999)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105111";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 44  numero campo 35 
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=988 && intval($campos[$numero_campo])!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103042";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			
			//validaciones de calidad
			
			//echo "<script>alert('campo 35 entre guiones: -".$campos[$numero_campo]."- TFG entre guiones: -".$TFG."- es formula valida: $es_formula_valida ')</script>";
			
			if(trim($campos[$numero_campo])!="999"
			   && trim($campos[$numero_campo])!="988"
			   && "".floatval($TFG)!="".floatval(trim($campos[$numero_campo]))
			   && $es_formula_valida
			   )
			{
			    //echo "<script>alert('escribio error 0105026 para el campo 35 --- campo 35 entre guiones: -".$campos[$numero_campo]."- TFG entre guiones: -".$TFG."- es formula valida: $es_formula_valida ')</script>";
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105026";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...TFG: $TFG VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==988 && $TFG>=15 && $es_formula_valida)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105058";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			
			if(intval($campos[$numero_campo])!=988 && (intval($creatinina)==98) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105092";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$array_campo_fecha_27_1=explode("-",$campo_n27_1);
			$fecha_campo_temp_27_1=$campo_n27_1;
			
			$verificar_fecha_para_date_diff_27_1=true;
			
			if(count($array_campo_fecha_27_1)==3)
			{
				if(!ctype_digit($array_campo_fecha_27_1[0])
				   || !ctype_digit($array_campo_fecha_27_1[1]) || !ctype_digit($array_campo_fecha_27_1[2])
				   || !checkdate(intval($array_campo_fecha_27_1[1]),intval($array_campo_fecha_27_1[2]),intval($array_campo_fecha_27_1[0])) )
				{
					//$fecha_campo_temp_27_1="0000-00-00";
					$verificar_fecha_para_date_diff_27_1=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff_27_1=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp_27_1="0000-00-00";
				$verificar_fecha_para_date_diff_27_1=false;
			}
			
			$verificador_1_year=0;
			$verificador_3_months=0;
			if($verificar_fecha_para_date_diff_27_1==true)
			{
				$date_campo_27_1=date($fecha_campo_temp_27_1);
				$date_corte=date($fecha_de_corte);
				$date_corte_1_year=date('Y-m-d', strtotime("-12 months", strtotime($fecha_de_corte)));
				$date_corte_3_months=date('Y-m-d', strtotime("-3 months", strtotime($fecha_de_corte)));
				$fecha_campo_format=new DateTime($date_campo_27_1);
				$fecha_corte_format_1_year=new DateTime($date_corte_1_year);
				$fecha_corte_format_3_months=new DateTime($date_corte_3_months);
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format_1_year);
				$verificador_1_year= (float)$interval->format("%r%a");
				$interval = date_diff($fecha_campo_format,$fecha_corte_format_3_months);
				$verificador_3_months= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador_1_year=-1;
				$verificador_3_months=-1;
			}
			
			
			$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();
			if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
			{
				//inferior en 1 year fecha de corte
				if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01" && floatval($campos[$numero_campo])>=60
				   && trim($campos[$numero_campo])!="999"
				   && trim($campos[$numero_campo])!="988"
				   && $verificador_1_year>=0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105028";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...Valor campo 27_1 $campo_n27_1 VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				
				//inferior en 3 meses fecha de corte
				if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01"
				   &&  floatval($campos[$numero_campo])<60
				   && trim($campos[$numero_campo])!="999"
				   && trim($campos[$numero_campo])!="988"
				   && $verificador_3_months>=0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105057";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...Valor campo 27_1 $campo_n27_1  VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if entidad personalizada
			else
			{
				//inferior en 1 year fecha de corte
				if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01" && floatval($campos[$numero_campo])>=60
				   && trim($campos[$numero_campo])!="999"
				   && trim($campos[$numero_campo])!="988"
				   && $verificador_1_year>=0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205028";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...Valor campo 27_1 $campo_n27_1 VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					//$hubo_errores=true;
				}
				
				//inferior en 3 meses fecha de corte
				if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01"
				   &&  floatval($campos[$numero_campo])<60
				   && trim($campos[$numero_campo])!="999"
				   && trim($campos[$numero_campo])!="988"
				   && $verificador_3_months>=0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205057";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...Valor campo 27_1 $campo_n27_1  VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					//$hubo_errores=true;
				}
			}//fin else no es entidad personalizada
			
			
		}//fin if no esta en blanco
		
	}//if si existe campo
	
	
	//numero_orden 45  numero campo 36 
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103009";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 37 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103009";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 47  numero campo 38
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=1)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>3) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n20=$campos[20];
			$campo_n18=$campos[17];
			if(intval($campos[$numero_campo])!=1 && intval($campo_n20)==2 && intval($campo_n18)==2 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105020";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			$campo_n35=$campos[44];
			if(intval($campos[$numero_campo])==0 && (floatval($campo_n35)<=250 || intval($campo_n35)==988 )  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105100";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=0 && floatval($campo_n35)>250   &&  intval($campo_n35)!=988 &&  intval($campo_n35)!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105104";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==2 && (floatval($campo_n35)<60 || floatval($campo_n35)>250 )  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105105";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=2 && (floatval($campo_n35)>=60 && floatval($campo_n35)<=250 )  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105133";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==1 && floatval($campo_n35)>60 &&  intval($campo_n35)!=988 &&  intval($campo_n35)!=999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105134";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=1 && floatval($campo_n35)<60   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105135";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==3 &&  intval($campo_n35)!=999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105136";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=3 &&  intval($campo_n35)==999  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105137";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
	}//if si existe campo
	
	
	//numero_orden 48  numero campo 39
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>5) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103008";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n35=$campos[44];
			
			
			if(trim($campos[$numero_campo])=="99"
			   && intval($campo_n35)!=999 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105027";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1"
			   && (intval($campo_n35)<90  || intval($campo_n35)>250)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105045";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="1"
			   &&  (intval($campo_n35)>=90  && intval($campo_n35)<=250)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105138";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="2"
			   && (intval($campo_n35)<60 || intval($campo_n35)>=90)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105046";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="2"
			   &&  (intval($campo_n35)>=60 && intval($campo_n35)<90)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105139";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="3"
			   && (intval($campo_n35)<30 || intval($campo_n35)>=60)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105047";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="3"
			   && (intval($campo_n35)>=30 && intval($campo_n35)<60)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105140";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="4"
			   && (intval($campo_n35)<15 || intval($campo_n35)>=30)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105048";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="4"
			   &&  (intval($campo_n35)>=15 && intval($campo_n35)<30)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105141";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="5"
			   && intval($campo_n35)!=988
			   && (intval($campo_n35)>=15)
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105049";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="5"
			   && ((intval($campo_n35)<15) || intval($campo_n35)==988 )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105142";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n38=$campos[47];
			
			
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="98"
			   && intval($campo_n38)==0
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 49  numero campo 40 
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			
			$campo_n39=$campos[48];
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n39)!=5  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105022";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n35=$campos[44];
			/*
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n35)==988 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105023";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105024";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 50  numero campo 41
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98  && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103012";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n38)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105029";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			if(intval($campos[$numero_campo])!=98  && (intval($campo_n38)==0 || intval($campo_n38)==3) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && (intval($campo_n38)==1 || intval($campo_n38)==2))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105078";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 42
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n38)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105029";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			if(intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n39=$campos[48];
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n39)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105086";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105082";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 43
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>4) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103049";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n38)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105029";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n39=$campos[48];
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n39)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105086";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}*/
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 44 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(trim($campos[$numero_campo])!="1800-01-01" && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105032";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			$campo_n39=$campos[48];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && (intval($campo_n39)!=5 ) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105084";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105085";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			
			/*
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105126";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 45 
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			$campo_n46=$campos[55];
			$campo_n49=$campos[58];
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n46)==98 && intval($campo_n49)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			} 
			
			
			$campo_n46=$campos[55];
			$campo_n49=$campos[58];
			if(trim($campos[$numero_campo])=="1845-01-01"
			   && (
			    (intval($campo_n46)==1 || intval($campo_n46)==2)
			   || (intval($campo_n49)==1 || intval($campo_n49)==2)
			  )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105143";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			$campo_n39=$campos[48];
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n39)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105084";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105085";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 46 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n39=$campos[48];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n39)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105033";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n39)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105034";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n44=$campos[53];
			
			if(intval($campos[$numero_campo])!=98 && trim($campo_n44)=="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105035";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n35=$campos[44];
			
			$campo_n64=$campos[85];
			
			if(intval($campos[$numero_campo])!=98 && (intval($campo_n64)==1 || intval($campo_n64)==2))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105125";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 47 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.5 || floatval($campos[$numero_campo])>4.5) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103032";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105038";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n46)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105039";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105078";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
	}//if si existe campo
	
	
	//numero_orden 57  numero campo 48 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
		
		
		if($campos[$numero_campo]!="")
		{
		
			//longitud de campo
			if(strlen($campos[$numero_campo])>8)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101008";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n46)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105079";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105080";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 58  numero campo 49 
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n39=$campos[48];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n39)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105033";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n39)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105034";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			$campo_n44=$campos[53];
			
			if(intval($campos[$numero_campo])!=98 && trim($campo_n44)=="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105035";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n35=$campos[44];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n35)>=15 && intval($campo_n35)!=988 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105036";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n46=$campos[55];
			if(intval($campos[$numero_campo])!=98 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105113";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			
			if(intval($campos[$numero_campo])!=98 && (intval($campo_n64)==1 || intval($campo_n64)==2))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105125";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 50 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.5 || floatval($campos[$numero_campo])>4.5) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103032";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105040";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n49)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105041";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 51 
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>12) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103033";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105040";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n49)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105041";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 52
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>6) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103034";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105040";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n49)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105041";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 53 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
		
		
		
		
		if($campos[$numero_campo]!="")
		{
		   
			//longitud de campo
			if(strlen($campos[$numero_campo])>8)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101008";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105040";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n49)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105041";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 54
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>3)  && intval($campos[$numero_campo])!=99 && intval($campos[$numero_campo])!=98)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 55 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01" && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 56 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01" && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 57
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])==1 && intval($campo_n38)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105114";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			
			$campo_n39=$campos[48];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n39)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105115";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n44=$campos[53];
			
			if(intval($campos[$numero_campo])!=98 && trim($campo_n44)=="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105116";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			
			if(intval($campos[$numero_campo])==1 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105117";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])==2 && intval($campo_n46)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105049";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==1 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105050";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])==2 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105051";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n35=$campos[44];
			
			/*
			if(intval($campos[$numero_campo])==1 && (intval($campo_n35)>=15  || intval($campo_n35)==999) && intval($campo_n35)!=988 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105052";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			if(intval($campos[$numero_campo])==1 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105127";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 58 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
		
		
		
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>8)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101008";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n57=$campos[66];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n57)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105053";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n57)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105087";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 59 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<3 || floatval($campos[$numero_campo])>23) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103035";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])!=98 && (intval($campo_n46)==98 || intval($campo_n49)==98) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105054";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105055";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105056";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105057";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
		
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 60
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.5 || floatval($campos[$numero_campo])>10) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])!=98 && (intval($campo_n46)==98 || intval($campo_n49)==98) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105054";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105055";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98  && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105056";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105057";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
		
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 61 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>5)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_decimal_check=explode(".",$campos[$numero_campo]);
			if(!is_numeric($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else if(is_numeric($campos[$numero_campo]) && count($array_decimal_check)>1 )
			{
				if(strlen($array_decimal_check[1])>2)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<0.1 || floatval($campos[$numero_campo])>12) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])!=98 && (intval($campo_n46)==98 || intval($campo_n49)==98) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105054";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105055";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n49=$campos[58];
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n49)!=98)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105056";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 71  numero campo 62
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=99 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103054";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			
			$campo_n39=$campos[48];
			
			$campo_n64=$campos[85];
			
			
			
			
			$campo_n62_1=$campos[72];
			$campo_n62_2=$campos[73];
			$campo_n62_3=$campos[74];
			$campo_n62_4=$campos[75];
			$campo_n62_5=$campos[76];
			$campo_n62_6=$campos[77];
			$campo_n62_7=$campos[78];
			$campo_n62_8=$campos[79];
			$campo_n62_9=$campos[80];
			$campo_n62_10=$campos[81];
			$campo_n62_11=$campos[82];
			
			$campo_n64=$campos[85];
			
			if(trim($campos[$numero_campo])!="97"
			   && (intval($campo_n39)=="1"
			       || intval($campo_n39)=="2"
			       || intval($campo_n39)=="3"
			       || intval($campo_n39)=="4")
			   )
			{
			    if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105094";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="97"
			   && trim($campo_n39)=="5"
			   && (trim($campo_n64)=="1" || trim($campo_n64)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105153";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		    
			if(
			   (trim($campos[$numero_campo])!="1"
			    && trim($campos[$numero_campo])!="2")
			    && trim($campo_n39)=="5"
			   && (trim($campo_n64)!="1" && trim($campo_n64)!="2")
			   )
			{
			    if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105095";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])!="98"  && trim($campo_n39)=="98" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 62.1
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
		}
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 62.2
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 62.3
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 62.4
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 62.5
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 62.6
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 62.7
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 62.8
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 62.9 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
	}//if si existe campo
	
	//numero_orden 81  numero campo 62.10
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	//numero_orden 82  numero campo 62.11
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])!="98"
			   && (trim($campo_n62)=="97"
			       || trim($campo_n62)=="98"
			       || trim($campo_n62)=="99"
			       )
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105059";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="98"
			   && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105123";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 63
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			$campo_n39=$campos[48];
			$campo_n62=$campos[71];
			
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105130";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n39)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105061";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n38=$campos[47];
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105074";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 63.1
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=12 && strlen($campos[$numero_campo])!=2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101015";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=98 ))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103018";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n63=$campos[83];
			
			if(intval($campos[$numero_campo])==98 && trim($campo_n63)!="1845-01-01" && trim($campo_n63)!="1800-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105062";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && (trim($campo_n63)=="1845-01-01") )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105081";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		
		}
		
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 64
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>5) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=5 && intval($campo_n38)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105063";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n39=$campos[48];
			if(intval($campos[$numero_campo])!=5 && (intval($campo_n39)==98 ) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105064";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n46=$campos[55];
			
			if(intval($campos[$numero_campo])!=5 && intval($campo_n46)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105065";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			$campo_n57=$campos[66];
			
			if(intval($campos[$numero_campo])!=5 && intval($campo_n57)==1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105066";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			$campo_n49=$campos[58];
			if(intval($campos[$numero_campo])!=5 && intval($campo_n49)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105067";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			$campo_n44=$campos[53];
			if((intval($campos[$numero_campo])!=5) && trim($campo_n44)=="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105068";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 86  numero campo 65 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>6)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101007";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103017";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			if(intval($campos[$numero_campo])!=98 && (intval($campo_n64)==2 || intval($campo_n64)==4) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105150";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && (intval($campo_n64)==1 || intval($campo_n64)==3) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105149";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])==99 && intval($campo_n64)!=2  && intval($campo_n64)!=4)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105088";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
	}//if si existe campo
	
	
	//numero_orden 87  numero campo 66
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=12 && strlen($campos[$numero_campo])!=2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101015";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 ))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103019";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105071";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105072";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n39=$campos[48];
			/*
			if(intval($campos[$numero_campo])!=99 && intval($campos[$numero_campo])!=98 && intval($campo_n39)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105102";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 88  numero campo 67
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98  && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103012";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validaciones de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105071";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105072";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 68 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
		
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>9)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101009";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 && intval($campos[$numero_campo])!=97  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103023";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validaciones de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			if((intval($campos[$numero_campo])==98  )
			   && (intval($campo_n64)!=5) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105074";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=99 && (intval($campo_n64)==2 || intval($campo_n64)==4))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105082";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			if((intval($campos[$numero_campo])!=98)  && (intval($campo_n64)==5))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105121";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=99
			   && intval($campo_n64)!=1 && intval($campo_n64)!=3)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105086";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 69
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98  && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103012";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105071";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105072";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 69.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 92  numero campo 69.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 93  numero campo 69.3
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 94  numero campo 69.4
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 95  numero campo 69.5 
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 96  numero campo 69.6 
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 97  numero campo 69.7 
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(trim($campos[$numero_campo])!="1800-01-01"
			   && trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n69=$campos[90];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01" && intval($campo_n69)!=1 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105073";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n64=$campos[85];
			if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105120";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 98  numero campo 70 
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>20) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103047";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105072";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105097";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 99  numero campo 70.1 
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105069";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 100  numero campo 70.2 
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105069";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 101  numero campo 70.3 
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105069";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 102  numero campo 70.4 
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105069";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 103  numero campo 70.5 
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105069";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 104  numero campo 70.6 
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105069";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 105  numero campo 70.7 
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>20)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."'OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103053";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105091";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 106  numero campo 70.8
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>20)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."'
OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103053";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105091";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 107  numero campo 70.9 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>20)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."'OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103053";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105025";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n70=$campos[98];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n70)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105091";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n70)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105129";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 108  numero campo 71 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>20) && intval($campos[$numero_campo])!=98  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103041";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105072";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105097";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105089";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
		
	}//if si existe campo
	
	
	//numero_orden 109  numero campo 72
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validaciones de calidad
			$campo_n38=$campos[47];
			
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n38=$campos[47];
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105074";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n71=$campos[108];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n71)==98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105075";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n71)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105099";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 110  numero campo 73
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validaciones de calidad
			$campo_n38=$campos[47];
			
			$campo_n64=$campos[85];
			
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105031";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n38=$campos[47];
			
			$campo_n39=$campos[48];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && (intval($campo_n64)==1 || intval($campo_n64)==2)  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105122";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1845-01-01"  && (intval($campo_n64)==3 || intval($campo_n64)==4)   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105144";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105145";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			/*
			if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105074";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 111  numero campo 74
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido			
			if( intval($campos[$numero_campo])>98)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103026";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105072";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(intval($campos[$numero_campo])==98 && intval($campo_n64)!=5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105097";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 112  numero campo 75 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
		
		
		 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>9)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101009";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103055";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105030";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			/*
			if(intval($campos[$numero_campo])==98  && intval($campo_n38)!=0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105128";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$campo_n64=$campos[85];
			
			
			if(intval($campos[$numero_campo])==98 && (intval($campo_n64)!=5) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105070";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)==5 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105103";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if(intval($campos[$numero_campo])!=98 && intval($campo_n64)!=1  && intval($campo_n64)!=3 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105087";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
	}//if si existe campo
	
	
	//numero_orden 113  numero campo 76
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (floatval($campos[$numero_campo])<1 || floatval($campos[$numero_campo])>12) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103046";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			/*
			if(intval($campos[$numero_campo])!=0 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105083";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
		}
	}//if si existe campo
	
	
	//numero_orden 114  numero campo 77
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1000 || intval($campos[$numero_campo])>999999999) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103021";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			
			if(intval($campos[$numero_campo])!=0 && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105083";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 115  numero campo 78 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>6)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101007";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_alnum($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$campos[$numero_campo]."' ; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && (intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103017";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 116  numero campo 79
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>9) && intval($campos[$numero_campo])!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103005";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n44=$campos[53];
			if((intval($campos[$numero_campo])==5 || intval($campos[$numero_campo])==7) && trim($campo_n44)!="1845-01-01" )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105101";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
	}//if si existe campo
	
	
	//numero_orden 117  numero campo 80 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
			if(strlen($campos[$numero_campo])>2)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101003";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campos[$numero_campo]))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>6) && intval($campos[$numero_campo])!=98 && intval($campos[$numero_campo])!=99)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103006";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//validacion de calidad
			$campo_n38=$campos[47];
			if((intval($campos[$numero_campo])==1) && intval($campo_n38)==0 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105093";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
		}
		
	}//if si existe campo
	
	
	//numero_orden 118  numero campo 80.1 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
		$campo_fix=preg_replace("/[^A-Za-z0-9:,.\/\_\|\-\s+]/", "", trim($campos[$numero_campo]) );
		//campo obligatorio
		if($campo_fix=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0104001";
			$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campo_fix!="")
		{
			//longitud de campo
			if(strlen($campo_fix)!=10)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101010";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//formato de campo
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)!=3 )		
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			else
			{
				if(!ctype_digit($array_campo_fecha[0]) || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2]) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}
				else if (ctype_digit($array_campo_fecha[0]) && ctype_digit($array_campo_fecha[1]) && ctype_digit($array_campo_fecha[2]))
				{
					if(!checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])))
					{
						if($errores_campos!="")
						{
							$errores_campos.="|";
						}		
						//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
						$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_campo_temp=$campos[$numero_campo];
			
			$verificar_fecha_para_date_diff=true;
			
			if(count($array_campo_fecha)==3)
			{
				if(!ctype_digit($array_campo_fecha[0])
				   || !ctype_digit($array_campo_fecha[1]) || !ctype_digit($array_campo_fecha[2])
				   || !checkdate(intval($array_campo_fecha[1]),intval($array_campo_fecha[2]),intval($array_campo_fecha[0])) )
				{
					//$fecha_campo_temp="0000-00-00";
					$verificar_fecha_para_date_diff=false;
				}
			}
			else
			{
				$verificar_fecha_para_date_diff=false;
			}
			
			$array_ver_fecha_corte=explode("-",$fecha_de_corte);
			if(count($array_ver_fecha_corte)!=3 || !ctype_digit($array_ver_fecha_corte[0])
			   || !ctype_digit($array_ver_fecha_corte[1]) || !ctype_digit($array_ver_fecha_corte[2])
			   || !checkdate(intval($array_ver_fecha_corte[1]),intval($array_ver_fecha_corte[2]),intval($array_ver_fecha_corte[0])) )
			{
				//$fecha_campo_temp="0000-00-00";
				$verificar_fecha_para_date_diff=false;
			}
			
			$verificador=0;
			if($verificar_fecha_para_date_diff==true && strlen($campos[$numero_campo])==10)
			{
				$date_campo=date($fecha_campo_temp);
				$date_corte=date($fecha_de_corte);
				$fecha_campo_format=new DateTime($date_campo);
				$fecha_corte_format=new DateTime($fecha_de_corte);		
				try
				{
				$interval = date_diff($fecha_campo_format,$fecha_corte_format);
				$verificador= (float)$interval->format("%r%a");
				}
				catch(Exception $e)
				{}
			}//fin if funcion date diff
			else
			{
				$verificador=-1;
			}			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( (intval($array_campo_fecha[0])!=1845 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && (intval($array_campo_fecha[0])!=1800 && intval($array_campo_fecha[1])!=1 && intval($array_campo_fecha[2])!=1)
				   && $verificador<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
					$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			//validacion de calidad
			$campo_n80=$campos[117];
			
			if(trim($campos[$numero_campo])!="1845-01-01" && (intval($campo_n80)==98))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105076";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n80)!=98 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105077";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			
			/*
			if((trim($campos[$numero_campo])=="1845-01-01" )
			   && intval($campo_n80)>=1 && intval($campo_n80)<=6 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105124";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}
			*/
			
			$es_inferior_a_fecha_inicial_periodo=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_inferior_pv);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_inicial_periodo>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0205151";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",02,".$array_tipo_validacion["02"].",0205,".$array_grupo_validacion["0205"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    //$hubo_errores=true;
			}//fin if
			
			$fecha_nacimiento_c7=trim($campos[6]);
			$es_inferior_a_fecha_nacimiento=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_nacimiento_c7);
			$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
			if($es_inferior_a_fecha_nacimiento>0
			   && $es_fecha_calendario<0)
			{
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0105152";
			    $cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia  ".$fecha_inferior_pv."... VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	
	
	
	
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarVIH
?>