<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

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

function formato_fecha_valida_quick_val($fecha_a_verificar,$separador="-")
{
	$es_fecha_valida=true;

	$fecha_a_verificar_array= explode($separador,$fecha_a_verificar);

	if(count($fecha_a_verificar_array)!=3)
	{			
		$es_fecha_valida=false;
	}//fin if
	else if( !ctype_digit($fecha_a_verificar_array[0]) 
		|| !ctype_digit($fecha_a_verificar_array[1]) 
		|| !ctype_digit($fecha_a_verificar_array[2])  
		)
	{			
		$es_fecha_valida=false;
	}//fin if
	else if( 
		!checkdate($fecha_a_verificar_array[1],$fecha_a_verificar_array[2],$fecha_a_verificar_array[0])
		)
	{			
		$es_fecha_valida=false;
	}//fin if

	return $es_fecha_valida;
}//fin function

function validar_HF($campos,
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
			$array_numero_campo_bd)
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

	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	$longitud_campos=count($campos_ver_characters);
	while($cont_campos<$longitud_campos)
	{
	   /* $campo_ver_characters="";
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
		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
		$consecutivo_errores++;
		
		$hubo_errores=true;
	    }*/
	    
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101014";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101014";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alpha($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 4  numero campo 5 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alpha($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
			if( $campos[$numero_campo]!="RC" &&  $campos[$numero_campo]!="TI" &&  $campos[$numero_campo]!="CC" &&  $campos[$numero_campo]!="CE" &&  $campos[$numero_campo]!="PA" &&  $campos[$numero_campo]!="MS" &&  $campos[$numero_campo]!="AS" &&  $campos[$numero_campo]!="CD")
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103033";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 5 numero campo 6 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101014";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if

		    //validacion de calidad
			//VERIFICACION DUPLICADOS
			$tipo_identificacion=$campos[4];//campo 5 numero orden 4;
			$identificacion=$campos[5];//campo 6 numero orden 5
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
				$var_numero_codigo="0105999";
				$cadena_descripcion_inconsistencia=explode(";;",str_replace(",", " - ", $array_detalle_validacion[$var_numero_codigo]) )[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$tipo_identificacion."_".$identificacion." $lineas_coincidentes_string ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;

				$hubo_errores=true;
			}//cuando ya esta la posicion en el array(los arreglos de php se usan como diccionarios)
			//FIN VERIFICACION DUPLICADOS
		}
	}//if si existe campo
	
	
	//numero_orden 6 numero campo 7 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//Validaciones Formato de Campo
			if( !formato_fecha_valida_quick_val($campos[$numero_campo]) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//Validaciones Valor Permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_1900_01_01="1900-01-01";
			$fecha_1900_12_31="1900-12-31";
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_de_corte);
			$verificacion_con_1900_01_01=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900_01_01);
			$verificacion_con_1900_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900_12_31);
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if(	($excede_fecha_corte<0 && $verificacion_con_1900_12_31<0) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				if(	($verificacion_con_1900_01_01>0 ) )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103035";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				$campo_n16 = trim($campos[15]);
				$verificacion_con_campo_n16=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $campo_n16);

				if(	$verificacion_con_campo_n16 < 0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103036";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			}//fin if
			
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 7 numero campo 8 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
			
		    //Validaciones Formato de Campo
		    if(!ctype_alpha($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if( $campos[$numero_campo]!="M" &&  $campos[$numero_campo]!="F"  )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103030";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gioss_ciou WHERE codigo_ciou_88='".$campos[$numero_campo]."' OR codigo_ciou_08='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if(	count($resultado)==0  
		    	&& intval($campos[$numero_campo])!=9996 
		    	&& intval($campos[$numero_campo])!=9997 
		    	&& intval($campos[$numero_campo])!=9998 
		    	&& intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103026";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alpha($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido regimen
		    if(trim($campos[$numero_campo])!="C" && trim($campos[$numero_campo])!="S" && trim($campos[$numero_campo])!="P" && trim($campos[$numero_campo])!="E" && trim($campos[$numero_campo])!="N")
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103021";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101010";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if( count($resultado)==0  )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103024";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>6)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103018";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>16)
		       && (intval($campos[$numero_campo])<31 || intval($campos[$numero_campo])>39)
		       && (intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>61)
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103019";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101008";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
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
				$var_numero_codigo="0103023";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo, $cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	
	}//if si existe campo
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>21)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101015";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo, $cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		
		    $numeros_telefonicos = explode('-', $campos[$numero_campo]);

		    if (count($numeros_telefonicos)!=2) {
		    	
		    	if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo, $cadena_descripcion_inconsistencia p ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }
		    
			//Validaciones Formato de Campo
		    if(!ctype_alnum(str_replace('-', '', $campos[$numero_campo])))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo, $cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 15 numero campo 16 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//Validaciones Formato de Campo
			if( !formato_fecha_valida_quick_val($campos[$numero_campo]) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//Validaciones Valor Permitido	
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_1900="1900-12-31";
			$fecha_1995="1995-01-01";
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_de_corte);
			$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900);
			$verificacion_con_1995=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1995);
			
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if($excede_fecha_corte<0 && $verificacion_con_1900<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				if($verificacion_con_1995>0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103017";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				$campo_n7 = trim($campos[6]);
				$verificacion_con_campo_n7=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $campo_n7);
				if($verificacion_con_1900<0 && $verificacion_con_campo_n7>0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103028";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
				}//fin if

			}//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if( (intval($campos[$numero_campo])!= 0 && intval($campos[$numero_campo]) != 1 && intval($campos[$numero_campo]) != 3) )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103014";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }else {
		    	
		    	//Validaciones Calidad
		    	$campo_n8 = trim($campos[7]);
		    	if ( intval($campos[$numero_campo]) != 0 && $campo_n8 == 'M' ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103015";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}


		    	if ( intval($campos[$numero_campo]) != 0 && $campo_n8 == 'F' && ($edad < 10 || $edad >= 60) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103015";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 0 && $campo_n8 == 'F' && ($edad >= 10 || $edad < 60) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103015";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if-else

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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if( (intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>4) )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103015";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	//Validaciones Calidad

		    	if ( intval($campos[$numero_campo]) != 3 && ($edad < 10 || $edad >= 60) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105013";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 3 && ($edad >= 10 || $edad < 60) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105014";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>100) && intval($campos[$numero_campo])!=9998)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103027";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones Calidad
		    	if ( intval($campos[$numero_campo]) > $edad ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105014";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 19  numero campo 20 
	$numero_campo=19;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if( (intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>4) )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103015";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 20  numero campo 21 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//Validaciones Formato de Campo
			if(!formato_fecha_valida_quick_val($campos[$numero_campo]) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//Validaciones Valor Permitido		
			
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_1900="1900-12-31";
			$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900);
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_de_corte);
			
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( $verificacion_con_1900<0 && $excede_fecha_corte<0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				$campo_n7 = trim($campos[6]);
				$verificacion_con_campo_n7=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $campo_n7);
				if( $verificacion_con_1900<0 && $verificacion_con_campo_n7>0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103028";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				if( ($verificacion_con_1900>=0 && $verificacion_con_1900!==false)
				   && trim($campos[$numero_campo])!="1811-01-01"
				   && trim($campos[$numero_campo])!="1800-01-01" )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103034";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

			} else {

				// Validaciones Calidad
				$campo_n64 = trim($campos[92]);
		    	if ( ( (trim($campos[$numero_campo]) == '1800-01-01') || (trim($campos[$numero_campo]) == '1822-01-01') ) && $campo_n64 == 2 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105017";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

			}//fin if
			
		}
	}//if si existe campo
	
	//numero_orden 21  numero campo 22 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
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
				$var_numero_codigo="0103038";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 22  numero campo 23
	$numero_campo=22;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>11)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones Calidad
		    	$campo_n24 = trim($campos[23]);
		    	if ( ( intval($campos[$numero_campo]) == 0 || intval($campos[$numero_campo]) == 1 ) && 
		    		 ( intval($campo_n24) != 0 || intval($campo_n24) != 1 || intval($campo_n24) != 2 ) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105017";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( ( intval($campos[$numero_campo]) >= 2 && intval($campos[$numero_campo]) <= 11 ) && ( intval($campo_n24) != 9999) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105018";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	$campo_n40_1 = trim($campos[44]);
		    	if ( ( intval($campos[$numero_campo]) >= 2 && intval($campos[$numero_campo]) <= 11 ) && ( intval($campo_n40_1) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105019";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	$campo_n40_2 = trim($campos[45]);
		    	if ( ( intval($campos[$numero_campo]) >= 2 && intval($campos[$numero_campo]) <= 11 ) && ( intval($campo_n40_2) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105020";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	$campo_n47_1 = trim($campos[52]);
		    	if ( ( intval($campos[$numero_campo]) >= 2 && intval($campos[$numero_campo]) <= 11 ) && ( intval($campo_n47_1) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105021";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				   	$hubo_errores=true;
		    	}

		    	$campo_n47_2 = trim($campos[53]);
		    	if ( ( intval($campos[$numero_campo]) >= 2 && intval($campos[$numero_campo]) <= 11 ) && ( intval($campo_n47_2) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105022";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	$campo_n47_3 = trim($campos[54]);
		    	if ( ( intval($campos[$numero_campo]) >= 2 && intval($campos[$numero_campo]) <= 11 ) && ( intval($campo_n47_3) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105023";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 23  numero campo 24
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>3) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103012";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones Calidad

		    	$campo_n25 = trim($campos[24]);
		    	if ( intval($campos[$numero_campo]) == 1 && intval($campo_n25) != 1) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105024";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 2 && intval($campo_n25) != 0) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105025";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 0 && intval($campo_n25) != 2) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105026";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	$campo_n31 = trim($campos[30]);
		    	if ( intval($campos[$numero_campo]) == 9999 && intval($campo_n31) != 9999) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105027";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 24  numero campo 25 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103012";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones Calidad

		    	$campo_n23 = trim($campos[22]);
		    	if ( intval($campos[$numero_campo]) == 9999 && ( intval($campo_n23) != 0 || intval($campo_n23) != 1) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105028";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( ( intval($campos[$numero_campo]) == 0 || intval($campos[$numero_campo]) == 1 || intval($campos[$numero_campo]) == 2) && 
		    		( intval($campo_n23) != 0 || intval($campo_n23) != 1) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105029";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	$campo_n31 = trim($campos[30]);
		    	if ( intval($campos[$numero_campo]) == 9999 && intval($campo_n31) != 9999 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105027";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 25  numero campo 26 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>24) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103007";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones Calidad

		    	$campo_n23 = trim($campos[22]);
		    	if ( intval($campos[$numero_campo]) == 9999 && ( intval($campo_n23) != 0 || intval($campo_n23) != 1) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105028";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && ( intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105030";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;

				    $hubo_errores=true;
		    	}

		    }//fin if
		}
	}//if si existe campo
	
	//numero_orden 26  numero campo 27
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>5) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103010";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad
		    	$campo_n23 = trim($campos[22]);
		    	if (intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105028";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) >= 0 && intval($campos[$numero_campo]) <= 5) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105030";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 27  numero campo 28 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>6) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103009";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad
		    	$campo_n23 = trim($campos[22]);
		    	if ( intval($campos[$numero_campo]) == 9999 && ($campo_n23 == 0 || $campo_n23 == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105028";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) >= 0 && intval($campos[$numero_campo]) <= 6) && ($campo_n23 >= 2 && $campo_n23 <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105031";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 28  numero campo 29 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//Validaciones Formato de Campo
			if(!formato_fecha_valida_quick_val($campos[$numero_campo]) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//Validaciones Valor Permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_1900="1900-12-31";
			$fecha_1822="1822-01-01";
			$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900);
			$verificacion_con_1822=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1822);
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_de_corte);
			
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( $verificacion_con_1900<0 && $excede_fecha_corte<0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				$campo_n7 = trim($campos[6]);
				$verificacion_con_campo_n7=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $campo_n7);
				
				if( $verificacion_con_1900<0 && $verificacion_con_campo_n7>0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103028";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				if(($verificacion_con_1900>=0 && $verificacion_con_1900!==false)
				   && trim($campos[$numero_campo])!="1800-01-01"
				   && trim($campos[$numero_campo])!="1845-01-01"
				   )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103032";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

			} else {

				//Validaciones de Calidad

				$campo_n21 = trim($campos[20]);
				$verificacion_campo_n21_con_1900 = diferencia_dias_entre_fechas($campo_n21, $fecha_1900);
				$campo_n21_excede_fecha_corte=diferencia_dias_entre_fechas($campo_n21, $fecha_de_corte);

				if ( $verificacion_campo_n21_con_1900<0 && $campo_n21_excede_fecha_corte<0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105015";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n7 = trim($campos[6]);
		    	$verificacion_campo_n21_con_campo_n7 = diferencia_dias_entre_fechas($campo_n21, $campo_n7);
		    	
		    	if ( $verificacion_campo_n21_con_1900<0 && $verificacion_campo_n21_con_campo_n7>0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105016";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n64 = trim($campos[92]);

		    	if ( ($campo_n21 == '1800-01-01' || $campo_n21 == '1822-01-01') && $verificacion_con_campo_n64 == 2 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105017";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (trim($campos[$numero_campo]) == '1800-01-01') && $verificacion_con_campo_n64 == 2 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105032";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( (trim($campos[$numero_campo]) == '1800-01-01' || trim($campos[$numero_campo]) == '1845-01-01') && ($campo_n23 >= 2 && $campo_n23 <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105032";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

			}//fin if
			
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 29  numero campo 30
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>7) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103008";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n31 = trim($campos[30]);

		    	if ( (intval($campos[$numero_campo]) == 7) && ($campo_n31 != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105082";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n35 = trim($campos[38]);

		    	if ( (intval($campos[$numero_campo]) == 7) && ($campo_n35 != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105083";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n36 = trim($campos[39]);

		    	if ( (intval($campos[$numero_campo]) == 7) && ($campo_n36 != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105084";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n38 = trim($campos[41]);

		    	if ( (intval($campos[$numero_campo]) == 7) && ($campo_n38 != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105085";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && ($campo_n23 == 0 || $campo_n23 == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105086";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 9999) && ($campo_n23 >= 2 && $campo_n23 <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105087";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campo_n35."' OR codigo_cum_con_guion='".$campo_n35."' OR cod_atc='".$campo_n35."' ; ";
			    $resultado_campo_n35=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campos[$numero_campo]) == 0) && (count($resultado_campo_n35)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105088";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 0) && (count($resultado) > 0) && (intval($campo_n35) != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105089";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campo_n36."' OR codigo_cum_con_guion='".$campo_n36."' OR cod_atc='".$campo_n36."' ; ";
			    $resultado_campo_n36=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campos[$numero_campo]) == 0) && (count($resultado_campo_n36)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105090";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 0) && (count($resultado) > 0) && (intval($campo_n36) != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105091";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 1) && (count($resultado_campo_n35) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105092";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 1) && (count($resultado_campo_n35) > 0) && (intval($campo_n35) != 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105093";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 2) && (count($resultado_campo_n36) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105094";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 2) && (count($resultado_campo_n36) > 0) && (intval($campo_n36) != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105095";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 3) && (count($resultado_campo_n36) == 0))  {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105096";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 3 && count($resultado_campo_n36) > 0 && intval($campo_n36) != 3 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105097";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 30  numero campo 31 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if
		
		if($campos[$numero_campo]!="")
		{
			//longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if

		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>5) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103010";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n33 = trim($campos[36]);

		    	if ( (intval($campos[$numero_campo]) == 3) && intval($campo_n33) == 9999 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105035";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n34 = trim($campos[37]);

		    	if ( (intval($campos[$numero_campo]) == 3) && intval($campo_n34) == 9999 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105036";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n35 = trim($campos[38]);

		    	if ( (intval($campos[$numero_campo]) == 3) && intval($campo_n35) != 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105037";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n36 = trim($campos[39]);

		    	if ( (intval($campos[$numero_campo]) == 3) && intval($campo_n36) != 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105038";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n37 = trim($campos[40]);

		    	if ( (intval($campos[$numero_campo]) == 3) && intval($campo_n37) != 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105039";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n38 = trim($campos[41]);

		    	if ( (intval($campos[$numero_campo]) == 3) && intval($campo_n38) != 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105040";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n24 = trim($campos[23]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n24) != 0 && intval($campo_n24) != 1 && intval($campo_n24) != 2) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105041";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n25 = trim($campos[24]);

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n25) != 0 && intval($campo_n25) != 1 && intval($campo_n25) != 2) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105042";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) < 2 || intval($campo_n23) > 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105043";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n32_1 = trim($campos[32]);

		    	if ( (intval($campos[$numero_campo]) == 0 || intval($campos[$numero_campo]) == 5) && (intval($campo_n32_1) != 9998) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105044";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 1 || intval($campos[$numero_campo]) == 2 || intval($campos[$numero_campo]) == 4) && (intval($campo_n32_1) > 9000) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105045";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 3) && (intval($campo_n32_1) != 9998) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105046";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n32_2 = trim($campos[33]);

		    	if ( (intval($campos[$numero_campo]) == 0 || intval($campos[$numero_campo]) == 5) && (intval($campo_n32_2) != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105047";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 1 || intval($campos[$numero_campo]) == 2 || intval($campos[$numero_campo]) == 4) && (intval($campo_n32_2) < 0 || intval($campo_n32_2) > 5) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105048";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 3) && (intval($campo_n32_2) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105049";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n32_3 = trim($campos[34]); 

		    	if ( (intval($campos[$numero_campo]) == 0 || intval($campos[$numero_campo]) == 5) && (intval($campo_n32_3) < 0 || intval($campo_n32_3) > 999900) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105050";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 0) && (intval($campo_n32_3) != 999998) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105051";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_32_4=trim($campos[35]);

		    	if ( (intval($campos[$numero_campo]) == 0) && (intval($campo_n32_4) != 9998) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105052";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 0 || intval($campos[$numero_campo]) == 5) && (intval($campo_n32_4) > 500) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105053";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 1 || intval($campos[$numero_campo]) == 2 || intval($campos[$numero_campo]) == 4) && (intval($campo_n32_4) != 9998) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105054";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 3) && (intval($campo_n32_4) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105055";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 31  numero campo 32
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101009";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    $array_campo_decimal=explode(".",$campos[$numero_campo]);
		    $bool_tiene_decimal=false;
		    $len_parte_decimal=0;
		    if(count($array_campo_decimal)==2 && is_numeric($campos[$numero_campo]) )
		    {
			$bool_tiene_decimal=true;
			$len_parte_decimal=strlen($array_campo_decimal[1]);
		    }
		    if(!is_numeric($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    else if(is_numeric($campos[$numero_campo])
			    && $bool_tiene_decimal==true && $len_parte_decimal>2)
		    {
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102006";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
		    }
		    
		    //Validaciones Valor Permitido
		    if(floatval($campos[$numero_campo])<2.0 || floatval($campos[$numero_campo])>250.0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103034";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	if ( (intval($campos[$numero_campo]) < 2) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105056";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) > 250) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105057";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 32.1 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>9000) && intval($campos[$numero_campo])!=9998 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103031";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) < 2 || intval($campo_n23) > 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105058";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105059";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n31 = trim($campos[30]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n31) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105060";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 32.2
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>5) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103010";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad
		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) < 2 && intval($campo_n23) > 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105061";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105062";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n31 = trim($campos[$numero_campo]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n31) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105063";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 32.3 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101010";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>999900) && intval($campos[$numero_campo]) != 999998 && intval($campos[$numero_campo]) != 999999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103031";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n31 = trim($campos[$numero_campo]);

		    	if ( (intval($campos[$numero_campo]) == 999998) && (intval($campo_n31) != 1 && intval($campo_n31) != 2 && intval($campo_n31) != 4) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105064";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 999998) && (intval($campo_n31) == 1 || intval($campo_n31) == 2 || intval($campo_n31) == 4) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105065";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) == 999999) && (intval($campo_n23) < 2 || intval($campo_n23) > 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105066";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 999999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105067";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}


		    	if ( (intval($campos[$numero_campo]) != 999999) && (intval($campo_n31) == 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105068";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 32.4
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>500) && intval($campos[$numero_campo]) != 9998 && intval($campos[$numero_campo]) != 9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103031";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) < 2 || intval($campo_n23) > 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105069";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105070";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n31 = trim($campos[30]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n31) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105071";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 33 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>3) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105072";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) == 0 || intval($campo_n23) == 1 || intval($campo_n23) == 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105073";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105074";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) == 0 || intval($campo_n23) == 1 || intval($campo_n23) == 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105075";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 34
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105074";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n23) == 0 || intval($campo_n23) == 1 || intval($campo_n23) == 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105075";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 35 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if( count($resultado)==0  && (intval($campos[$numero_campo])!=0) )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n34 = trim($campos[37]);

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
			    $resultado_campo_n35=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campo_n34) != 0) && (count($resultado_campo_n35)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105076";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n60 = trim($campos[88]);
				
				if ( (intval($campo_n34) != 0) && (intval($campo_n60)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105077";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 36 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if( count($resultado)==0  && (intval($campos[$numero_campo])!=0 ))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n35 = trim($campos[38]);

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
			    $resultado_campo_n36=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campo_n35) != 0) && (count($resultado_campo_n36)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105078";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n61 = trim($campos[89]);
		    	$campo_n34 = trim($campos[37]);
				
				if ( (intval($campo_n34) != 0) && (intval($campo_n60)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105079";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 37 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if( count($resultado)==0  && (intval($campos[$numero_campo])!=0))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
			    $resultado_campo_n37=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campos[$numero_campo]) != 0) && (count($resultado_campo_n37)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105080";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 38 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if( count($resultado)==0  && (intval($campos[$numero_campo])!=0))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103022";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
			    $resultado_campo_n38=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campos[$numero_campo]) != 0) && (count($resultado_campo_n38)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105080";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 39 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
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
				$var_numero_codigo="0103025";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$consulta="";
			    $consulta.="SELECT * FROM gioss_codigo_cum_hemofilia WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
			    $resultado_campo_n39=$coneccionBD->consultar2_no_crea_cierra($consulta);

		    	if ( (intval($campos[$numero_campo]) != 0) && (count($resultado_campo_n39)==0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105081";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
		
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 40
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n40_1 = trim($campos[44]);
		    	$campo_n40_2 = trim($campos[45]);

		    	if ( intval($campos[$numero_campo]) == 0 && ( (intval($campo_n40_1) >= 1 && intval($campo_n40_1) <= 60) || (intval($campo_n40_2) >= 1 && intval($campo_n40_2) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105098";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && intval($campo_n40_1) == 0 && intval($campo_n40_2) == 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105099";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && ( intval($campo_n23) >= 2 && intval($campo_n23) <= 11 ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && ( intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 44  numero campo 40.1 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo]) > 60)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103041";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n40 = trim($campos[43]);

		    	if ( intval($campos[$numero_campo]) != 0 && intval($campo_n40) == 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105102";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 45  numero campo 40.2
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>60)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	$campo_n40 = trim($campos[43]);

		    	if ( intval($campos[$numero_campo]) != 0 && intval($campo_n40) == 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105102";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 41 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo]) !=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
		    	$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 && 
		    		( (intval($campo_n47_1) >= 1 && intval($campo_n47_1) <= 60) || 
		    		  (intval($campo_n47_2) >= 1 && intval($campo_n47_2) <= 60) || 
		    		  (intval($campo_n47_3) >= 1 && intval($campo_n47_3) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 == trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && ( intval($campo_n23) >= 2 && intval($campo_n23) <= 11 ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && ( intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 47  numero campo 42
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    

		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
		    	$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n47_1) >= 1 && intval($campo_n47_1) <= 60) ||
		    		  (intval($campo_n47_2) >= 1 && intval($campo_n47_2) <= 60) ||
		    		  (intval($campo_n47_3) >= 1 && intval($campo_n47_3) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 48  numero campo 43
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
		    	$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n47_1) >= 1 && intval($campo_n47_1) <= 60) ||
		    		  (intval($campo_n47_2) >= 1 && intval($campo_n47_2) <= 60) ||
		    		  (intval($campo_n47_3) >= 1 && intval($campo_n47_3) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}


		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 49  numero campo 44 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
		    	$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n47_1) >= 1 && intval($campo_n47_1) <= 60) ||
		    		  (intval($campo_n47_2) >= 1 && intval($campo_n47_2) <= 60) ||
		    		  (intval($campo_n47_3) >= 1 && intval($campo_n47_3) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 50  numero campo 45
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
		    	$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n47_1) >= 1 && intval($campo_n47_1) <= 60) ||
		    		  (intval($campo_n47_2) >= 1 && intval($campo_n47_2) <= 60) ||
		    		  (intval($campo_n47_3) >= 1 && intval($campo_n47_3) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 46
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
		    	$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n47_1) >= 1 && intval($campo_n47_1) <= 60) ||
		    		  (intval($campo_n47_2) >= 1 && intval($campo_n47_2) <= 60) ||
		    		  (intval($campo_n47_3) >= 1 && intval($campo_n47_3) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 47.1
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>60)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103041";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	$campo_n41 = trim($campos[46]);
		    	$campo_n42 = trim($campos[47]);
		    	$campo_n43 = trim($campos[48]);
		    	$campo_n44 = trim($campos[49]);
		    	$campo_n45 = trim($campos[50]);
		    	$campo_n46 = trim($campos[51]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n41) >= 1 && intval($campo_n41) <= 60) ||
		    		  (intval($campo_n42) >= 1 && intval($campo_n42) <= 60) ||
		    		  (intval($campo_n43) >= 1 && intval($campo_n43) <= 60) ||
		    		  (intval($campo_n44) >= 1 && intval($campo_n44) <= 60) ||
		    		  (intval($campo_n45) >= 1 && intval($campo_n45) <= 60) ||
		    		  (intval($campo_n46) >= 1 && intval($campo_n46) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 0 && 
		    		(intval($campo_n41) == 0 && 
		    		 intval($campo_n42) == 0 && 
		    		 intval($campo_n43) == 0 && 
		    		 intval($campo_n44) == 0 && 
		    		 intval($campo_n45) == 0 && 
		    		 intval($campo_n46) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}


		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 47.2 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>60)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103041";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n41 = trim($campos[46]);
		    	$campo_n42 = trim($campos[47]);
		    	$campo_n43 = trim($campos[48]);
		    	$campo_n44 = trim($campos[49]);
		    	$campo_n45 = trim($campos[50]);
		    	$campo_n46 = trim($campos[51]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n41) >= 1 && intval($campo_n41) <= 60) ||
		    		  (intval($campo_n42) >= 1 && intval($campo_n42) <= 60) ||
		    		  (intval($campo_n43) >= 1 && intval($campo_n43) <= 60) ||
		    		  (intval($campo_n44) >= 1 && intval($campo_n44) <= 60) ||
		    		  (intval($campo_n45) >= 1 && intval($campo_n45) <= 60) ||
		    		  (intval($campo_n46) >= 1 && intval($campo_n46) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 0 && 
		    		(intval($campo_n41) == 0 && 
		    		 intval($campo_n42) == 0 && 
		    		 intval($campo_n43) == 0 && 
		    		 intval($campo_n44) == 0 && 
		    		 intval($campo_n45) == 0 && 
		    		 intval($campo_n46) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 47.3 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>60)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103041";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n41 = trim($campos[46]);
		    	$campo_n42 = trim($campos[47]);
		    	$campo_n43 = trim($campos[48]);
		    	$campo_n44 = trim($campos[49]);
		    	$campo_n45 = trim($campos[50]);
		    	$campo_n46 = trim($campos[51]);

		    	if ( intval($campos[$numero_campo]) == 0 &&
		    		( (intval($campo_n41) >= 1 && intval($campo_n41) <= 60) ||
		    		  (intval($campo_n42) >= 1 && intval($campo_n42) <= 60) ||
		    		  (intval($campo_n43) >= 1 && intval($campo_n43) <= 60) ||
		    		  (intval($campo_n44) >= 1 && intval($campo_n44) <= 60) ||
		    		  (intval($campo_n45) >= 1 && intval($campo_n45) <= 60) ||
		    		  (intval($campo_n46) >= 1 && intval($campo_n46) <= 60) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105103";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 0 && 
		    		(intval($campo_n41) == 0 && 
		    		 intval($campo_n42) == 0 && 
		    		 intval($campo_n43) == 0 && 
		    		 intval($campo_n44) == 0 && 
		    		 intval($campo_n45) == 0 && 
		    		 intval($campo_n46) == 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105104";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 48 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>4) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103012";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1 ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 48.1 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//Validaciones Formato de Campo
			if(!formato_fecha_valida_quick_val($campos[$numero_campo]) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//Validaciones Valor Permitido		
			$array_campo_fecha=explode("-",$campos[$numero_campo]);
			$fecha_1900="1900-12-31";
			$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900);
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_de_corte);
			
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( $verificacion_con_1900<0 && $excede_fecha_corte<0)
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				$campo_n7=$campos[6];
				$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $campo_n7);
				if( $verificacion_con_1900<0 && $campo_n7>0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103028";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				if( ($verificacion_con_1900>=0 && $verificacion_con_1900!==false)
				   && trim($campos[$numero_campo])!="1800-01-01"
				   && trim($campos[$numero_campo])!="1845-01-01")
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0103032";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			} else {

				// Validaciones de Calidad

				$fecha_1900="1900-12-31";
				$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $fecha_1900);
				$anio_campo = explode('-', $fecha_de_corte)[0];
				$inicio_fecha_de_corte = $anio_campo.'-04-01';
				$verificacion_con_inicio_fecha_corte = diferencia_dias_entre_fechas(trim($campos[$numero_campo]), $inicio_fecha_de_corte);

				if ( ($verificacion_con_1900) < 0 && ( $verificacion_con_inicio_fecha_corte > 0) ) {
					
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105133";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;

				}

				$campo_n21 = trim($campos[20]);
				$verificacion_con_campo_n21 = diferencia_dias_entre_fechas(trim($campos[$numero_campo], $campo_n21));

				if ( ($verificacion_con_1900 < 0) && ( $verificacion_con_inicio_fecha_corte > 0) ) {
					
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105134";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;

				}

				$verificacion_campo_n21_con_inicio_fecha_corte = diferencia_dias_entre_fechas($campo_n21, $inicio_fecha_de_corte);

				if ( trim($campos[$numero_campo]) == '1800-01-01' && ( $verificacion_campo_n21_con_inicio_fecha_corte > 0) ) {
					
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105132";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;

				}

			}//fin if
			
			
		}
	}//if si existe campo
	
	//numero_orden 57  numero campo 48.2 
	$numero_campo=57;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11 ) ) {
		    		if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && ( intval($campo_n23) == 0 || intval($campo_n23) == 1 ) ) {
		    		if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 58  numero campo 48.3 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if( (intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>2) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n48_4 = trim($campos[59]);

		    	if ( intval($campos[$numero_campo]) == 0 && intval($campo_n48_4) != 9998 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105105";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n48 = trim($campos[55]);

		    	if ( intval($campos[$numero_campo]) == 1 && intval($campo_n48) != 0 && intval($campo_n48) != 1 && intval($campo_n48) != 2 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105106";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 1 || intval($campos[$numero_campo]) == 2) && ( intval($campo_n48_4) >= 365 && intval($campo_n48_4) <= 9998 ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105107";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && intval($campo_n48_4) != 9999 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105108";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && ( intval($campo_n23) >= 2 && intval($campo_n23) <= 11 ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105109";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && ( intval($campo_n23) == 0 || intval($campo_n23) == 1 ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105108";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 48.4 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>365) && intval($campos[$numero_campo]) != 9998 && intval($campos[$numero_campo]) != 9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103001";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n48_3 = trim($campos[58]);

		    	if ( (intval($campos[$numero_campo]) >= 1 && intval($campos[$numero_campo]) <= 365)  && intval($campo_n48_3) != 1 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105109";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9998  && intval($campo_n48_3) != 0 ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105110";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999  && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999  && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 49 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n49_1 = trim($campos[61]);

		    	if ( intval($campos[$numero_campo]) == 0  && (intval($campo_n49_1) != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105111";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 1 && (intval($campo_n49_1) < 1 || intval($campo_n49_1) > 100) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105112";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && (intval($campo_n49_1) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105113";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 49.1
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>100) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n49 = trim($campos[60]);

		    	if ( intval($campos[$numero_campo]) == 0 && (intval($campo_n49) != 0) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105114";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) >= 0 && intval($campos[$numero_campo]) <= 100) && (intval($campo_n49) != 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105115";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) ==  9999) && (intval($campo_n49) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105116";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 50 
	$numero_campo=62;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 51
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Caidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 52 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 53 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 54
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 55 
	$numero_campo=67;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n55_1 = trim($campos[68]);

		    	if ( (intval($campos[$numero_campo]) == 0 && (intval($campo_n55_1) != 0) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105117";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999 && (intval($campo_n55_1) != 9999) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105118";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 55.1 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if(count($resultado)==0  && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103043";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n55 = trim($campos[67]);

		    	if ( (intval($campos[$numero_campo]) == 0 && (intval($campo_n55) != 0) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105117";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999 && (intval($campo_n55) != 9999) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105118";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 9999)  && (intval($campo_n55) != 1)  ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105119";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 56
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n56_1 = trim($campos[70]);

		    	if ( (intval($campos[$numero_campo]) == 0 && (intval($campo_n56_1) != 0) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105117";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999 && (intval($campo_n56_1) != 9999) ) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105118";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( (intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 9999)  && (intval($campo_n56_1) != 1)  ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105119";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 56.1 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n56 = trim($campos[69]);

		    	if ( intval($campos[$numero_campo]) == 0 && intval($campo_n56) != 0 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105120";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) >= 1 && intval($campos[$numero_campo]) <= 10) && intval($campo_n56) != 1 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105121";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 71  numero campo 57
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>4) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103011";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n57_1 = trim($campos[72]);

		    	if ( intval($campos[$numero_campo]) == 0 && intval($campo_n57_1) == 0 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105120";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 0) && intval($campo_n57_1) != 0 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105121";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n57_2 = trim($campos[73]);

		    	if ( (intval($campos[$numero_campo]) == 4) && intval($campo_n57_2) == 0 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105122";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 4) && intval($campo_n57_2) != 0 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105123";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n57_3 = trim($campos[74]);
		    	$campo_n57_4 = trim($campos[75]);
		    	$campo_n57_5 = trim($campos[76]);
		    	$campo_n57_6 = trim($campos[77]);
		    	$campo_n57_7 = trim($campos[78]);
		    	$campo_n57_8 = trim($campos[79]);
		    	$campo_n57_9 = trim($campos[80]);
		    	$campo_n57_10 = trim($campos[81]);

		    	if ( (intval($campos[$numero_campo]) == 9999) && 
		    		 (intval($campo_n57_2) != 9999 || 
		    		  intval($campo_n57_3) != 9999 || 
		    		  intval($campo_n57_4) != 9999 || 
		    		  intval($campo_n57_5) != 9999 || 
		    		  intval($campo_n57_6) != 9999 || 
		    		  intval($campo_n57_7) != 9999 || 
		    		  intval($campo_n57_8) != 9999 || 
		    		  intval($campo_n57_9) != 9999 || 
		    		  intval($campo_n57_10) != 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105124";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 9999) && 
		    		 (intval($campo_n57_2) == 9999 || 
		    		  intval($campo_n57_3) == 9999 || 
		    		  intval($campo_n57_4) == 9999 || 
		    		  intval($campo_n57_5) == 9999 || 
		    		  intval($campo_n57_6) == 9999 || 
		    		  intval($campo_n57_7) == 9999 || 
		    		  intval($campo_n57_8) == 9999 || 
		    		  intval($campo_n57_9) == 9999 || 
		    		  intval($campo_n57_10) == 9999) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105125";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 57.1
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n57 = trim($campos[71]);

		    	if ( (intval($campos[$numero_campo]) != 0) && (intval($campo_n57) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105126";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) == 0) && (intval($campo_n57) == 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105127";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 57.2
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n57 = trim($campos[71]);

		    	if ( (intval($campos[$numero_campo]) != 0) && (intval($campo_n57) != 4) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105128";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) == 0) && (intval($campo_n57) == 4) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105129";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) == 9999) && (intval($campo_n57) != 9999) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105130";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 9999) && (intval($campo_n57) == 9999) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105131";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 57.3
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1 && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 57.4
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
		
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 57.5
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
		
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 57.6
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 57.7
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 57.8
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 57.9 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1  && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else
		}
	}//if si existe campo
	
	//numero_orden 81  numero campo 57.10
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101006";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1  && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if
		    }//fin else calidad
		}
		
	}//if si existe campo
	
	//numero_orden 82  numero campo 57.11
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101014";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 57.12
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 57.13
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101014";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 57.14
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101016";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_alnum($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102002";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 86  numero campo 58 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n40_1 = trim($campos[44]);
		    	$campo_n40_2 = trim($campos[45]);

		    	if ( (intval($campos[$numero_campo]) == 0) && (intval($campo_n40_1) != 0 || intval($campo_n40_2) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105135";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 0) && (intval($campo_n40_1) == 0 && intval($campo_n40_2) == 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105136";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n47_1 = trim(intval($campos[52]));
		    	$campo_n47_2 = trim(intval($campos[53]));
		    	$campo_n47_3 = trim(intval($campos[54]));

		    	if ( (intval($campos[$numero_campo]) == 0) && (intval($campo_n47_1) != 0 || intval($campo_n47_2) != 0 || intval($campo_n47_3) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105137";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 0) && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105138";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 87  numero campo 59
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>30) && intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103003";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n40 = trim($campos[43]);

		    	if ( intval($campos[$numero_campo]) != 0 && intval($campo_n40) != 1 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105139";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 0 && intval($campo_n40) != 0 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105140";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && intval($campo_n40) != 9999 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105141";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n47_1 = trim($campos[52]);
		    	$campo_n47_2 = trim($campos[53]);
				$campo_n47_3 = trim($campos[54]);

		    	if ( intval($campos[$numero_campo]) == 0 && (intval($campo_n47_1) != 0 || intval($campo_n47_2) != 0 || intval($campo_n47_3) != 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105142";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) != 0 && (intval($campo_n47_1) == 0 && intval($campo_n47_2) == 0 && intval($campo_n47_3) == 0) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105143";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n23 = trim($campos[22]);

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) >= 2 && intval($campo_n23) <= 11) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105100";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}

		    	if ( intval($campos[$numero_campo]) != 9999 && (intval($campo_n23) == 0 || intval($campo_n23) == 1) ) {
		    		
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105101";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;

		    	}//fin if

		    }//fin if
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 88  numero campo 60
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    $array_campo_decimal=explode(".",$campos[$numero_campo]);
		    $bool_tiene_decimal=false;
		    $len_parte_decimal=0;
		    if(count($array_campo_decimal)==2 && is_numeric($campos[$numero_campo]) )
		    {
			$bool_tiene_decimal=true;
			$len_parte_decimal=strlen($array_campo_decimal[1]);
		    }
		    if(!is_numeric($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    else if(is_numeric($campos[$numero_campo])
			    && $bool_tiene_decimal==true && $len_parte_decimal>2)
		    {
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102006";
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		    }
		    
		    //Validaciones Valor Permitido
		    if(floatval($campos[$numero_campo])<0.0 || floatval($campos[$numero_campo])>9000000000.0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103042";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n62 = trim($campos[90]);

		    	if ( intval($campos[$numero_campo]) > intval($campo_n62) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105143";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 61 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    $array_campo_decimal=explode(".",$campos[$numero_campo]);
		    $bool_tiene_decimal=false;
		    $len_parte_decimal=0;
		    if(count($array_campo_decimal)==2 && is_numeric($campos[$numero_campo]) )
		    {
			$bool_tiene_decimal=true;
			$len_parte_decimal=strlen($array_campo_decimal[1]);
		    }
		    if(!is_numeric($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    else if(is_numeric($campos[$numero_campo])
			    && $bool_tiene_decimal==true && $len_parte_decimal>2)
		    {
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102006";
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		    }
		    
		    //Validaciones Valor Permitido
		    if(floatval($campos[$numero_campo])<0.0 || floatval($campos[$numero_campo])>9000000000.0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103042";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad 

		    	$campo_n62 = trim($campos[90]);

		    	if ( intval($campos[$numero_campo]) > intval($campo_n62) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105143";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 62
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    $array_campo_decimal=explode(".",$campos[$numero_campo]);
		    $bool_tiene_decimal=false;
		    $len_parte_decimal=0;
		    if(count($array_campo_decimal)==2 && is_numeric($campos[$numero_campo]) )
		    {
			$bool_tiene_decimal=true;
			$len_parte_decimal=strlen($array_campo_decimal[1]);
		    }
		    if(!is_numeric($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    else if(is_numeric($campos[$numero_campo])
			    && $bool_tiene_decimal==true && $len_parte_decimal>2)
		    {
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102006";
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		    }
		    
		    //Validaciones Valor Permitido
		    if(floatval($campos[$numero_campo])<0.0 || floatval($campos[$numero_campo])>9000000000.0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103042";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	$campo_n60 = trim($campos[88]);
		    	$campo_n61 = trim($campos[89]);

		    	if ( intval($campos[$numero_campo]) < intval($campo_n60) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105144";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) < intval($campo_n61) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105145";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 63
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101013";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if((floatval($campos[$numero_campo])<0.0 || floatval($campos[$numero_campo])>100000000.0) && $campos[$numero_campo]!="9999")
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103036";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n10 = trim($campos[9]);

		    	if ( intval($campos[$numero_campo]) != 9999 && trim($campo_n10) != 'C' ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105146";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( intval($campos[$numero_campo]) == 9999 && trim($campo_n10) == 'C' ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105147";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 92  numero campo 64
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103005";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n64_1 = trim($campos[93]);

		    	if ( (intval($campos[$numero_campo]) == 4 || intval($campos[$numero_campo]) == 10) && intval($campo_n64_1) == 98 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105148";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 4 && intval($campos[$numero_campo]) != 10) && intval($campo_n64_1) != 98 ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105149";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$campo_n64_2 = trim($campos[94]);

		    	if ( (intval($campos[$numero_campo]) == 4 || intval($campos[$numero_campo]) == 10) && trim($campo_n64_2) == '1845-01-01' ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105150";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 4 && intval($campos[$numero_campo]) != 10) && trim($campo_n64_2) != '1845-01-01' ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105151";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 93  numero campo 64.1
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])!=1 && strlen($campos[$numero_campo])!=2 && strlen($campos[$numero_campo])!=4)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0101007";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Formato de Campo
		    if(!ctype_digit($campos[$numero_campo]))
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102004";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //Validaciones Valor Permitido
		    
		    if((intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>6) 
		    	&& intval($campos[$numero_campo])!=98 
		    	&& intval($campos[$numero_campo])!=99 
		    	&& intval($campos[$numero_campo])!=9999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103020";
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    } else {

		    	// Validaciones de Calidad

		    	$campo_n64 = trim($campos[92]);

		    	if ( (intval($campos[$numero_campo]) == 98) && (intval($campo_n64) == 4 || intval($campo_n64) == 10) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105152";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (intval($campos[$numero_campo]) != 98) && (intval($campo_n64) != 4 && intval($campo_n64) != 10) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105153";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
		}
	}//if si existe campo
	
	//numero_orden 94  numero campo 64.2
	$numero_campo=94;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
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
				$var_numero_codigo="0101011";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if 
			
			//Validaciones Formato de Campo
			if( !formato_fecha_valida_quick_val($campo_fix) )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0102001";
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//Validaciones Valor Permitido		
			$array_campo_fecha=explode("-",$campo_fix);
			$fecha_1900="1900-12-31";
			$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campo_fix), $fecha_1900);
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campo_fix), $fecha_de_corte);
			
			if(count($array_campo_fecha)==3)//pre 0103036
			{
				if( $verificacion_con_1900<0 && $excede_fecha_corte<0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				$campo_n7 = $campos[6];
				$verificacion_con_campo_n7=diferencia_dias_entre_fechas(trim($campo_fix), $campo_n7);
				if( $verificacion_con_1900<0 && $verificacion_con_campo_n7>0 )
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if

				if( ($verificacion_con_1900>=0 && $verificacion_con_1900!==false)
				   && trim($campos[$numero_campo])!="1845-01-01"
				   && trim($campos[$numero_campo])!="1800-01-01")
				{
					if($errores_campos!="")
					{
						$errores_campos.="|";
					}		
					//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$var_numero_codigo="0103029";
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					$consecutivo_errores++;
					
					$hubo_errores=true;
				}//fin if
			} else {

		    	// Validaciones de Calidad

		    	$campo_n64 = trim($campos[92]);

		    	if ( (trim($campos[$numero_campo]) == '1845-01-01') && (intval($campo_n64) == 4 || intval($campo_n64) == 10) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105154";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	if ( (trim($campos[$numero_campo]) != '1845-01-01') && (intval($campo_n64) != 4 && intval($campo_n64) != 10) ) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105155";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    	$fecha_1900="1900-12-31";
				$verificacion_con_1900=diferencia_dias_entre_fechas(trim($campo_fix), $fecha_1900);
				$anio_inicio = explode('-', $fecha_de_corte)[0];
				$fecha_de_inicio_reporte = ($anio_inicio - 1).'-04-01';
				$inferior_a_fecha_inicio=diferencia_dias_entre_fechas(trim($campo_fix), $fecha_de_inicio_reporte);

		    	if ( $verificacion_con_1900 < 0 &&  $inferior_a_fecha_inicio > 0) {
		    		if($errores_campos!="")
				    {
					    $errores_campos.="|";
				    }		
				    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
					$var_numero_codigo="0105156";
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion[$var_numero_codigo])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",$var_numero_codigo,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				    $consecutivo_errores++;
				    
				    $hubo_errores=true;
		    	}

		    }//fin if
			
		}
		
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarVIH
?>
