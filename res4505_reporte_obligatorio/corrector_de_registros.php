<?php
ignore_user_abort(true);
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '2000M');

error_reporting(E_ALL);
ini_set('display_errors', '1');

/*
ini_set('display_errors',1); 
 error_reporting(E_ALL);
 */


require_once '../res4505/criterios_reparacion_4505.php';
 

class corrector_registros_para_duplicados_en_consolidado extends criterios_reparacion_4505
{

    var $delimitador = ",";
    var $i = 0;
    var $seq;
    var $codEntidad;
    var $fechaRemision;
	
    var $nit_prestador="0000";
    var $modulo_de_informacion="";
    var $tema_de_informacion="";
    var $tipo_de_identificacion_entidad_reportadora="";
    var $tipo_de_regimen_de_la_informacion_reportada="";
    var $consecutivo_de_archivo="";
    var $identificacion_entidad_administradora="";
    var $cod_registro_especial_pss="";
    var $cadena_fecha_corte="";
    
    var $nombre_archivo_4505="";
    
    var $codigo_periodo="";
    
    var $fecha_inicio_periodo="";
    var $fecha_de_corte_periodo="";
    
    var $secuencia_actual_para_email="";
    var $ruta_archivos_inconsistencias_para_email="";
    
    var $nombre_archivo_generado="";
    
    var $verificacion_inicial_global=true;
    
    var $cod_eapb_global="";
    
    var $consecutivo_fixer=1;
    
    var $tipo_periodo_tiempo_global;

    

    function __construct() 
    {
	
	
    }
    
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
    
        
    //recibe en dia mes year
    // las fechas deben estar en este formato
    // fecha_nacimiento dd-mm-aaaa, fecha_actual dd-mm-aaaa
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

    	
	var $diccionario_identificacion=array();
	var $diccionario_identificacion_lineas=array();
	
	var $diccionario_identificacion_para_bool=array();
	
	
	function corrige_longitud_fecha($array_fecha_corregida,$fecha_corte,$fase=0)
	{
	    if(is_array($array_fecha_corregida) && is_array($fecha_corte))
	    {    
		if(count($array_fecha_corregida)==3)
		{
		    //PARTE YEAR
		    //accede a corregir year si la longitud de dia y mes son iguales o menores a dos	    
		    if(ctype_digit($array_fecha_corregida[0])
			&& strlen($array_fecha_corregida[0])<4
			&& strlen($array_fecha_corregida[1])<=2
			&& strlen($array_fecha_corregida[2])<=2
		       )
		    {
			//accede a corregir year si la longitud de dia y mes son iguales o menores a dos para cuando el year es menor de 4 caracteres
			if(intval($array_fecha_corregida[0])>200)
			{
			    $longitud_year_corte=strlen($fecha_corte[0]);
			    $ultimo_digito_year_corte=substr($fecha_corte[0],($longitud_year_corte-1),$longitud_year_corte);
			    $array_fecha_corregida[0]=$array_fecha_corregida[0].$ultimo_digito_year_corte;
			}//fin if
			else if(intval($array_fecha_corregida[0])>190)
			{
			    $array_fecha_corregida[0]=$array_fecha_corregida[0]."0";
			}//fin else
			
		    }//fin if
		    else if(ctype_digit($array_fecha_corregida[0])
		       && strlen($array_fecha_corregida[0])>4
		       && strlen($array_fecha_corregida[0])>strlen($array_fecha_corregida[1])
			&& strlen($array_fecha_corregida[0])>strlen($array_fecha_corregida[2])
		       )
		    {
			$array_fecha_corregida[0]=intval($array_fecha_corregida[0]);
			if(strlen($array_fecha_corregida[0])>4)
			{
			    $array_fecha_corregida[0]=substr($array_fecha_corregida[0],0,4);
			}
			else if(strlen($array_fecha_corregida[0])<4)
			{
			    if(intval($array_fecha_corregida[0])>200)
			    {
				$longitud_year_corte=strlen($fecha_corte[0]);
				$ultimo_digito_year_corte=substr($fecha_corte[0],($longitud_year_corte-1),$longitud_year_corte);
				$array_fecha_corregida[0]=$array_fecha_corregida[0].$ultimo_digito_year_corte;
			    }//fin if
			    else if(strlen($array_fecha_corregida[0])==3
				    && intval($array_fecha_corregida[0])>190
				    )
			    {
				$array_fecha_corregida[0]=$array_fecha_corregida[0]."0";
			    }//fin else
			}//fin else
		    }//fin else if
		    else if(ctype_digit($array_fecha_corregida[0])
			    && ctype_digit($array_fecha_corregida[1])
			    && ctype_digit($array_fecha_corregida[2])
			    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[1])
			    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[2])
			    )
		    {
			$array_fecha_corregida[0]="".intval($array_fecha_corregida[0]);
			$array_fecha_corregida[1]="".intval($array_fecha_corregida[1]);
			$array_fecha_corregida[2]="".intval($array_fecha_corregida[2]);
			if(ctype_digit($array_fecha_corregida[0])
			    && ctype_digit($array_fecha_corregida[1])
			    && ctype_digit($array_fecha_corregida[2])
			    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[1])
			    && strlen($array_fecha_corregida[0])==strlen($array_fecha_corregida[2])
			    )
			{
			    $diferencia_0=0;
			    $diferencia_0=intval($fecha_corte[0])-intval($array_fecha_corregida[0]);
			    $diferencia_1=0;
			    $diferencia_1=intval($fecha_corte[0])-intval($array_fecha_corregida[1]);
			    $diferencia_2=0;
			    $diferencia_2=intval($fecha_corte[0])-intval($array_fecha_corregida[2]);
			    
			    
			    if(intval($array_fecha_corregida[0])<(intval($fecha_corte[0])-5) || intval($array_fecha_corregida[0])>intval($fecha_corte[0]))
			    {
				//caso en el que el valor del campo 0 no esta remotamente cercano a el year de la fecha de corte en 5 years o es superior
				if($diferencia_1>=0 && $diferencia_2>=0)
				{
				    $temp=$array_fecha_corregida[0];
				    if($diferencia_1<$diferencia_2)
				    {				
					$array_fecha_corregida[0]=$array_fecha_corregida[1];
					$array_fecha_corregida[1]=$temp;
					
				    }//fin if
				    else if($diferencia_2<$diferencia_1)
				    {
					$array_fecha_corregida[0]=$array_fecha_corregida[2];
					$array_fecha_corregida[2]=$temp;
				    }//fin else if
				}//fin if
				
			    }//fin if
			    else
			    {
				$diferencia_seleccionada="none";
				if($diferencia_1>=0 && $diferencia_2>=0)
				{
				    if($diferencia_1<$diferencia_2)
				    {				
					$diferencia_seleccionada="dif_1";
					
				    }//fin if
				    else if($diferencia_2<$diferencia_1)
				    {
					$diferencia_seleccionada="dif_2";
				    }//fin else if
				}//fin if
				
				if($diferencia_seleccionada=="dif_1")
				{
				    $temp=$array_fecha_corregida[0];
				    if($diferencia_1<$diferencia_0)
				    {				
					$array_fecha_corregida[0]=$array_fecha_corregida[1];
					$array_fecha_corregida[1]=$temp;
					
				    }//fin if
				}//fin if
				else if($diferencia_seleccionada=="dif_2")
				{
				    $temp=$array_fecha_corregida[0];
				    if($diferencia_2<$diferencia_0)
				    {				
					$array_fecha_corregida[0]=$array_fecha_corregida[2];
					$array_fecha_corregida[2]=$temp;
					
				    }//fin if
				}//fin else
			    }//fin else
			}//fin if
			
		    }//fin else if
		    //FIN PARTE YEAR
		    
		    //PARTE MES
		    if(ctype_digit($array_fecha_corregida[1])
		       && strlen($array_fecha_corregida[1])==1
		       )
		    {
			$array_fecha_corregida[1]="0".$array_fecha_corregida[1];
		    }//fin if
		    else if(ctype_digit($array_fecha_corregida[1])
		       && strlen($array_fecha_corregida[1])>2
		       && strlen($array_fecha_corregida[1])<4
		       )
		    {
			$array_fecha_corregida[1]=substr($array_fecha_corregida[1],0,2);
		    }//fin else if
		    //FIN PARTE MES
		    
		    //PARTE DIA
		    if(ctype_digit($array_fecha_corregida[2])
		       && strlen($array_fecha_corregida[2])==1
		       )
		    {
			$array_fecha_corregida[2]="0".$array_fecha_corregida[2];
		    }//fin if
		    else if(ctype_digit($array_fecha_corregida[2])
		       && strlen($array_fecha_corregida[2])>2
		       && strlen($array_fecha_corregida[2])<4
		       )
		    {
			$array_fecha_corregida[2]=substr($array_fecha_corregida[2],0,2);
		    }//fin else if
		    //FIN PARTE DIA
		    
		    $fecha_corregida="";
		    $fecha_corregida=$array_fecha_corregida[0]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[2];
		    return $fecha_corregida;
		}//fin if
		else 
		{
		    echo 'Alguno de los parametros no es un arreglo. ';
		    return false;
		}
	    }//fin if son array los parametros
	}//fin funcion corrige_longitud_fecha
	
	function alterar_mes($mes, $cantidad, $signo)
	{
	    $mes_alterado_int=intval($mes);
	    if($signo="+"){$mes_alterado_int=intval($mes)+intval($cantidad);}
	    if($signo="-"){$mes_alterado_int=intval($mes)-intval($cantidad);}
	    
	    if($mes_alterado_int<=0 || $mes_alterado_int>12)
	    {
	        return false;
	    }
	    else
	    {
	        $string_mes_alt="".$mes_alterado_int;
	        if(strlen($mes_alterado_int)==1)
	        {
	            $string_mes_alt="0".$mes_alterado_int;
	        }
	        
	        return $string_mes_alt;
	        
	    }
	}//fin funcion alterar mes
	
	function corrector_formato_fecha($campo_fecha,$es_fecha_nacimiento=false,$campo_especial=-1,$campo_debug=0)
	{
	    date_default_timezone_set ("America/Bogota");
	    
	    $fecha_corte=explode("-",$this->fecha_de_corte_periodo);
	    $date_de_corte=date($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2]);
	 
	    $fecha_corregida="";
	    $fecha_corregida=trim($campo_fecha);
	    $array_fecha_corregida=array();
	    if(strlen($fecha_corregida)==10)
	    {	
	    	$fecha_corregida=str_replace("/","-",$fecha_corregida);
	    	$array_fecha_corregida=explode("-",$fecha_corregida);
		}
		else if(strlen($fecha_corregida)==8)
		{
			//parte year
			$posicion_year=strpos($fecha_corregida, $fecha_corte[0]);
			while($posicion_year!==0 && ($posicion_year+4)!==8)
			{
			    $posicion_year=strpos($fecha_corregida, $fecha_corte[0]);
			}
			$esta_al_final_el_year=false;
			if(($posicion_year+4)===8)
			{
			    $esta_al_final_el_year=true;
			}//fin if
			$yearfc=substr($fecha_corregida,$posicion_year,4);
			//fin parte year
			

			//parte mes
			$cont_mes=1;
			$posicion_mes=0;
			while($cont_mes<=12)
			{
			    
			    $mes_fix="".$cont_mes;
			    if(strlen($mes_fix)==1)
			    {
			        $mes_fix="0".$mes_fix;
			    }
			    
			    $posicion_mes=strpos($fecha_corregida, $mes_fix );
			    //se coloca !== para que identifique tipo
			    if($posicion_mes!==false 
			    && ($posicion_mes<$posicion_year || $posicion_mes>($posicion_year+3) )
			    && ( ($esta_al_final_el_year==true && $posicion_mes==($posicion_year-2) )
			     || ($esta_al_final_el_year==false && $posicion_mes==($posicion_year+4) )
			    )
			    )
			    {
			        break;
			    }
			    $cont_mes++;
			}//fin while mes
			if($posicion_mes===false)
			{
			    //echo "no esta en la mitad \n";
			    if($esta_al_final_el_year==true)
			    {
			        $posicion_mes=0;
			    }
			    else
			    {
			        $posicion_mes=$posicion_year+4;
			        $mesTemp=substr($fecha_corregida,$posicion_mes,2);
			        if(intval($mesTemp)>12)
			        {
			            $posicion_mes=$posicion_year+6;
			        }//fin if
			    }//fin else
			}
			$mesfc=substr($fecha_corregida,$posicion_mes,2);
			//fin parte mes
			
			//echo "pm ".$posicion_mes."\n";


			//parte dia
			$cont_dia=1;
			$posicion_dia=0;
			while($cont_dia<=31)
			{
			    
			    $dia_fix="".$cont_dia;
			    if(strlen($dia_fix)==1)
			    {
			        $dia_fix="0".$dia_fix;
			    }
			    
			    $posicion_dia=strpos($fecha_corregida, $dia_fix );
			    if($posicion_dia!==false
			    && ($posicion_dia<$posicion_year || $posicion_dia>($posicion_year+3) )
			    )
			    {
			    //echo "dia ".$dia_fix." pos ".$posicion_dia." pm ".$posicion_mes." py ".$posicion_year."\n";
			    }
			    //se coloca !== para que identifique tipo
			    if($posicion_dia!==false 
			    && ($posicion_dia<$posicion_year || $posicion_dia>($posicion_year+3) )
			    && ($posicion_dia<$posicion_mes || $posicion_dia==($posicion_mes+2) )
			    )
			    {
			        //echo "entro dia ".$dia_fix." pos ".$posicion_dia."\n";
			        break;
			    }
			    $cont_dia++;
			}//fin while dia			
			$diafc=substr($fecha_corregida,$posicion_dia,2);
			//fin parte dia

			if(intval($mesfc)<=12 && intval($diafc)<=12)
			{
			    $diferencia_meses_con_mes=intval($fecha_corte[1])-intval($mesfc);
			    $diferencia_meses_con_dia=intval($fecha_corte[1])-intval($diafc);
			    if($diferencia_meses_con_mes<0 && $yearfc==$fecha_corte[0] )
			    {
			        $temp=$mesfc;
			        $mesfc=$diafc;
			        $diafc=$temp;
			    }//mes es mayor que la fecha de corte lo cual no es posible si es el mismo year
			    else if($diferencia_meses_con_dia>=0 && $yearfc==$fecha_corte[0] )
			    {
			        if($diferencia_meses_con_mes>$diferencia_meses_con_dia)
			        {
    			        $temp=$mesfc;
    			        $mesfc=$diafc;
    			        $diafc=$temp;
    			    }
			    }//fin else if cuando mes y dia son menores  al mes de la fehca de corte
			}//fin if


			$fecha_corregida=$yearfc."-".$mesfc."-".$diafc;			
	    	$array_fecha_corregida=explode("-",$fecha_corregida);

		}//fin else if corrigiendo fechas de menos de 8 
		else if(strlen($fecha_corregida)>10)
		{
			$fecha_corregida=str_replace("/","-",$fecha_corregida);		
	    	$array_fecha_corregida=explode("-",$fecha_corregida);
	    	if(count($array_fecha_corregida)==3)
	    	{
	    		$array_fecha_corregida[0]=intval($array_fecha_corregida[0])."";
	    		$array_fecha_corregida[1]=intval($array_fecha_corregida[1])."";
	    		$array_fecha_corregida[2]=intval($array_fecha_corregida[2])."";
	    	}//fin if
			$fecha_corregida=$array_fecha_corregida[0]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[2];	
			$array_fecha_corregida=explode("-",$fecha_corregida);
		}//fin if es mayor a 10
	    
	    if(is_array($array_fecha_corregida)
		&& count($array_fecha_corregida)==3
		)
	     {
		 	$fecha_corregida=$this->corrige_longitud_fecha($array_fecha_corregida,$fecha_corte);
	     }//fin if
	     
	     $fecha_corregida=substr($fecha_corregida,0,10);
	     $array_fecha_corregida=explode("-",$fecha_corregida);
	    
	    $caso_al_que_entro="";
	    
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
						else
						{
						   if($es_fecha_nacimiento==false)
						   {
								if($campo_especial==-1 || $campo_especial==33)
								{
								    $fecha_corregida="1800-01-01";
								}
								else if($campo_especial==-2)
								{
								    $fecha_corregida="1845-01-01";
								}
						   }
						   else
						   {
						     //$fecha_corregida=$date_de_corte;
						   }
						}//fin else
				    }//fin else if
				    else
				    {
						if($es_fecha_nacimiento==false)
						{
						    if($campo_especial==-1 || $campo_especial==33)
						    {
								$fecha_corregida="1800-01-01";
						    }
						    else if($campo_especial==-2)
						    {
								$fecha_corregida="1845-01-01";
						    }
						}
						else
						{
						 	//$fecha_corregida=$date_de_corte;
						}
				    }
				}//fin if			
				else if(intval($array_fecha_corregida[2])>=32)
				{
				    //checkdate mm-dd-aaaa -> dd-mm-aaaa ?
				    if(checkdate($array_fecha_corregida[1],$array_fecha_corregida[0],$array_fecha_corregida[2]))
				    {
					$fecha_corregida=$array_fecha_corregida[2]."-".$array_fecha_corregida[1]."-".$array_fecha_corregida[0];
					$caso_al_que_entro="cambia, caso 1 dd-mm-aaaa";
				    }//fin if
				    else
				    {
						if($es_fecha_nacimiento==false)
						{
						    if($campo_especial==-1 || $campo_especial==33)
						    {
								$fecha_corregida="1800-01-01";
						    }
						    else if($campo_especial==-2)
						    {
								$fecha_corregida="1845-01-01";
						    }
						}
						else
						{
						 	//$fecha_corregida=$date_de_corte;
						}
				    }//fin else
				}//fin else if
				else
				{
				    if($es_fecha_nacimiento==false)
				    {
						if($campo_especial==-1 || $campo_especial==33)
						{
						    $fecha_corregida="1800-01-01";
						}
						else if($campo_especial==-2)
						{
						    $fecha_corregida="1845-01-01";
						}
				    }
				    else
				    {
				     	//$fecha_corregida=$date_de_corte;
				    }
				}//fin else
				
			    }//fin else
			    
			}//fin if
			else
			{
			    if($es_fecha_nacimiento==false)
			    {
					if($campo_especial==-1 || $campo_especial==33)
					{
					    $fecha_corregida="1800-01-01";
					}
					else if($campo_especial==-2)
					{
					    $fecha_corregida="1845-01-01";
					}
				    }
				    else
				    {
				     	//$fecha_corregida=$date_de_corte;
				    }
			}//fin else
	    }//fin if array count es 3
	    else
	    {
			if($es_fecha_nacimiento==false)
			{
			    if($campo_especial==-1 || $campo_especial==33)
			    {
					$fecha_corregida="1800-01-01";
			    }
			    else if($campo_especial==-2)
			    {
					$fecha_corregida="1845-01-01";
			    }
			}
			else
			{
			 	//$fecha_corregida=$date_de_corte;
			}
	    }//fin else
	    
	    $nuevo_array_fecha_corregida=explode("-",$fecha_corregida);
	    if(is_array($nuevo_array_fecha_corregida)
	       && count($nuevo_array_fecha_corregida)==3
	       && $es_fecha_nacimiento==false
	       )
	    {
			$fecha_corregida=$this->corrige_longitud_fecha($nuevo_array_fecha_corregida,$fecha_corte,1);
	    }
	    
	    if($es_fecha_nacimiento==false)
	    {	    
			$date_de_corte_12_meses_menos=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' -24 months'));
			$date_de_corte_posterior_10_meses=date('Y-m-d',strtotime($fecha_corte[0]."-".$fecha_corte[1]."-".$fecha_corte[2] . ' +10 months'));
			$diferencia_de_1900=-1;
			$interval = date_diff(new DateTime($fecha_corregida),new DateTime("1900-01-01"));
			$diferencia_de_1900 =(float)($interval->format("%r%a"));
			if($diferencia_de_1900<0)//si excede 1900 entonces no es codigo
			{
			  $interval = date_diff(new DateTime($fecha_corregida),new DateTime($date_de_corte));
			  $verificacion_fecha_corte =(float)($interval->format("%r%a"));
			  $interval = date_diff(new DateTime($fecha_corregida),new DateTime($date_de_corte_12_meses_menos));
			  $verificacion_fecha_corte_12_meses_menos=(float)($interval->format("%r%a"));
			  $interval = date_diff(new DateTime($fecha_corregida),new DateTime($date_de_corte_posterior_10_meses));
			  $verificacion_fecha_corte_pos_10_meses =(float)($interval->format("%r%a"));
			  
			  
			  if($verificacion_fecha_corte<0 && ($campo_especial==-1 || $campo_especial==-2))//excede la fecha de corete, diferencia de dias es inferior
			  {
			    //$fecha_corregida=$date_de_corte;
			  }
			  if($verificacion_fecha_corte_pos_10_meses<0 && $campo_especial==33)//excede la fecha de corete, diferencia de dias es inferior
			  {
			    $fecha_corregida=$date_de_corte_posterior_10_meses;
			  }
			  else if($verificacion_fecha_corte_12_meses_menos>0)//es inferior, por eso la diferencia de dias es mayor de cero
			  {
			   $fecha_corregida="1800-01-01";
			  }
			  
			}//fin si excede 1900 entonces no es codigo
	    }//fin if si no es fecha de nacimiento
	    
	    if($es_fecha_nacimiento==true)
	    {	    
	      //echo "<script>alert('pre $campo_fecha pos $fecha_corregida $caso_al_que_entro');</script>";
	    }
	    
	    /*
	    if($campo_debug==50)
	    {
	      echo "<script>alert('pre $campo_fecha pos $fecha_corregida $caso_al_que_entro');</script>";
	    }
	    */

	    
	    return $fecha_corregida;
	}//fin funcion
	
	function corrector_valor_permitido_numerico_criterio_025($valor_campo)
	{
	    $valor_permitido_corregido=$valor_campo;
	    
	    if($valor_campo!="1"
	       && $valor_campo!="2"
	       && $valor_campo!="3"
	       && $valor_campo!="4"
	       && $valor_campo!="5"
	       && $valor_campo!="6")
	    {
		$valor_permitido_corregido="6";
	    }
	    
	    return $valor_permitido_corregido;
	}//fin funcion criterio 025
	
	function corrector_valor_permitido_numerico_criterio_006($valor_campo)
	{
	    $valor_permitido_corregido=$valor_campo;
	    
	    if($valor_campo!="1"
	       && $valor_campo!="2"
	       && $valor_campo!="3"
	       && $valor_campo!="4"
	       && $valor_campo!="5"
	       && $valor_campo!="6"
	       && $valor_campo!="7"
	       && $valor_campo!="8"
	       && $valor_campo!="9"
	       && $valor_campo!="10"
	       && $valor_campo!="11"
	       && $valor_campo!="12"
	       && $valor_campo!="13")
	    {
		$valor_permitido_corregido="13";
	    }
	    
	    return $valor_permitido_corregido;
	}//fin funcion criterio 006
        
    
	
	
	function convert_to_standard_notation($floatAsString)
	{
	    $norm = strval(floatval($floatAsString));
	
	    if (($e = strrchr($norm, 'E')) === false) {
		return $norm;
	    }
	
	    return number_format($norm, -intval(substr($e, 1)));
	}
	
	//FUNCION QUE VALIDA Y CORRIGE LOS 118 CAMPOS DE 4505
	public function correccion_campos_registro_de_archivo_4505($array_fields, $numLinea,  &$consecutivo_errores,$fecha_corte_bd,&$conexion_bd_validar_campos)
	{
		$fallo_sexo=false;
		$fallo_fecha_nacimiento=false;
		
		$validador_boolean=true;
		$mensajes_error_campos ="";
		$registro_corregido="";
		
		$nombre_archivo4505="";
		$nombre_archivo4505=$this->nombre_archivo_4505;
		
		//CONEXION BASE DE DATOS
		//$conexion_bd_validar_campos = new conexion();
		
		//parte que trae las descripciones de las inconsistencias
		$array_tipo_inconsistencia=array();
		$array_grupo_inconsistencia=array();
		$array_detalle_inconsistencia=array();

		$query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
		$resultado_query1_tipo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query1_tipo_validacion);
		foreach($resultado_query1_tipo_validacion as $tipo_validacion)
		{
			$array_tipo_inconsistencia[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
		}
		$query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
		$resultado_query2_grupo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query2_grupo_validacion);
		foreach($resultado_query2_grupo_validacion as $grupo_validacion)
		{
			$array_grupo_inconsistencia[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
		}
		$query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistecias_4505;";
		$resultado_query3_detalle_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query3_detalle_validacion);
		foreach($resultado_query3_detalle_validacion as $detalle_validacion)
		{
			$array_detalle_inconsistencia[$detalle_validacion["codigo_detalle_inconsistencia"]]=$detalle_validacion["descripcion_inconsistencia"];
		}	
		//fin parte que trae las descripciones de las inconsistencias
		
		//echo "<script>alert('".$array_detalle_inconsistencia["0103034"]." nombre ".$nombre_archivo4505." ".$resultado_query3_detalle_validacion[0]["codigo_detalle_inconsistencia"]." ".$resultado_query3_detalle_validacion[0]["descripcion_inconsistencia"]."');</script>";
		
		//calculo de la edad con la fecha de nacimiento
		date_default_timezone_set ("America/Bogota");
		$fecha_actual = date('Y-m-d');
		
		//$fecha_ini=explode("-",$this->fecha_inicio_periodo);
		$this->fecha_de_corte_periodo=$fecha_corte_bd;
		$fecha_fin=explode("-",$this->fecha_de_corte_periodo);
		$date_fin_reporte=date($fecha_fin[0]."-".$fecha_fin[1]."-".$fecha_fin[2]);
		
		$year_corte=intval($fecha_fin[0]);
		
		//PARTE ANALISIS FECHA DE CORTE ES MENSUAL A TRIMESTRAL
		$old_date_fin_reporte=$date_fin_reporte;
		
		
		//si corresponde al periodo 1 aaaa-01-01 hasta aaaa-03-31
		$corresponde_primer_periodo=true;
		$primer_periodo_li=$year_corte."-01-01";
		$primer_periodo_ls=$year_corte."-03-31";
		$fecha_de_corte_del_archivo_es_menor_primer_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$primer_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_primer_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$primer_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_primer_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_primer_periodo_ls<0)
		{
		    $corresponde_primer_periodo=false;
		}
		
		if($corresponde_primer_periodo==true)
		{
		    $date_fin_reporte=date($primer_periodo_ls);
		}
		//fin si corresponde al periodo 1
		
		//si corresponde al periodo 2 aaaa-04-01 hasta aaaa-06-30
		$corresponde_segundo_periodo=true;
		$segundo_periodo_li=$year_corte."-04-01";
		$segundo_periodo_ls=$year_corte."-06-30";
		$fecha_de_corte_del_archivo_es_menor_segundo_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$segundo_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_segundo_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$segundo_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_segundo_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_segundo_periodo_ls<0)
		{
		    $corresponde_segundo_periodo=false;
		}
		
		if($corresponde_segundo_periodo==true)
		{
		    $date_fin_reporte=date($segundo_periodo_ls);
		}
		//fin si corresponde al periodo 2
		
		//si corresponde al periodo 3 aaaa-07-01 hasta aaaa-09-30
		$corresponde_tercer_periodo=true;
		$tercer_periodo_li=$year_corte."-07-01";
		$tercer_periodo_ls=$year_corte."-09-30";
		$fecha_de_corte_del_archivo_es_menor_tercer_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$tercer_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_tercer_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$tercer_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_tercer_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_tercer_periodo_ls<0)
		{
		    $corresponde_tercer_periodo=false;
		}
		
		if($corresponde_tercer_periodo==true)
		{
		    $date_fin_reporte=date($tercer_periodo_ls);
		}
		//fin si corresponde al periodo 3
		
		//si corresponde al periodo 4 aaaa-10-01 hasta aaaa-12-31
		$corresponde_cuarto_periodo=true;
		$cuarto_periodo_li=$year_corte."-10-01";
		$cuarto_periodo_ls=$year_corte."-12-31";
		$fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li=$this->diferencia_dias_entre_fechas($date_fin_reporte,$cuarto_periodo_li);
		$fecha_de_corte_del_archivo_es_mayor_cuarto_periodo_ls=$this->diferencia_dias_entre_fechas($date_fin_reporte,$cuarto_periodo_ls);
		if($fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li>0
		   || $fecha_de_corte_del_archivo_es_mayor_cuarto_periodo_ls<0)
		{
		    //echo "<script>alert(' $fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li $fecha_de_corte_del_archivo_es_menor_cuarto_periodo_li');</script>";
		    $corresponde_cuarto_periodo=false;
		}
		
		if($corresponde_cuarto_periodo==true)
		{
		    $date_fin_reporte=date($cuarto_periodo_ls);
		}
		//fin si corresponde al periodo 4
		
		//echo "<script>alert(' ANTES: $old_date_fin_reporte, DESPUES: $date_fin_reporte');</script>";
		//FIN PARTE DECHA DE CORTE ES MENSUAL A TRIMESTRAL
		
		$array_fields[9]=$this->corrector_formato_fecha($array_fields[9],true);
		
		$fecha_nacimiento= explode("-",$array_fields[9]);
		$bool_fecha_nacimiento_valida=true;
		if(count($fecha_nacimiento)!=3
		   || !(ctype_digit($fecha_nacimiento[0]) && ctype_digit($fecha_nacimiento[1]) && ctype_digit($fecha_nacimiento[2]) )
		   || !checkdate($fecha_nacimiento[1],$fecha_nacimiento[2],$fecha_nacimiento[0]))
		{			
			$bool_fecha_nacimiento_valida=false;
			$validador_boolean=false;
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
		    $fecha_corte_format=new DateTime($date_fin_reporte);
		
		    $interval = date_diff($fecha_nacimiento_format,$fecha_corte_format);
		    $edad_dias =(float)($interval->days);
		    
		    //$edad= (float)($interval->days / 365);		    
		    //$edad_meses = (float)($interval->days / 30.4368499);
		    //$edad_meses_2 = (float)($interval->format('%m')+ 12 * $interval->format('%y'));
		    
		    //echo "<script>alert('$string_fecha_nacimiento $date_fin_reporte');</script>";
		    
		    $array_fecha_nacimiento=explode("-",$string_fecha_nacimiento);
		    $array_fecha_corte=explode("-",$date_fin_reporte);
		    $array_edad=$this->edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte[2]."-".$array_fecha_corte[1]."-".$array_fecha_corte[0]);
		    $edad_meses=(intval($array_edad['y'])*12)+$array_edad['m'];
		    $edad=intval($array_edad['y']);
		    
		    //echo "<script>alert('total age years $edad , total age months $edad_meses, total days $edad_dias fecha nacimiento $string_fecha_nacimiento fecha de corte $date_fin_reporte');</script>";
		    
		    $edad_semanas = (float)($interval->days / 7);
		    $verificador_edad= (float)$interval->format("%r%a");
		    
		    //echo "<script>alert('$edad_semanas $verificador_edad');</script>";
		}
		//fin calculo de la edad
		
		//corrige sexos indicados en minuscula
		$bool_sexo_es_valido=true;
		$array_fields[10]=trim($this->alphanumericAndSpace4($array_fields[10]));
		if($array_fields[10]=="m")
		{
		    $array_fields[10]="M";
		}
		else if($array_fields[10]=="f")
		{
		    $array_fields[10]="F";
		}
		
		//valida si el sexo indicado es valido
		if($array_fields[10]!="F" && $array_fields[10]!="M")
		{
			$bool_sexo_es_valido=false;
		    
			$validador_boolean=false;
			
		}
		//fin campo 10
		
		
		//echo "<script>alert('entro $numLinea ".$array_fields[10]."');</script>";
		
		//NOTA: el parametro $array_fields pasa por referencia debido a que se altera dentro de la funcion correccion_campo
		    
		$cont_corrector_notacion_cientifica=0;
		while($cont_corrector_notacion_cientifica<count($array_fields))
		{
		    if(!ctype_digit(trim($array_fields[$cont_corrector_notacion_cientifica]))
		       && is_numeric(trim($array_fields[$cont_corrector_notacion_cientifica])))
		    {
			    $antes=$array_fields[$cont_corrector_notacion_cientifica];
			    
			    $array_fields[$cont_corrector_notacion_cientifica]="".$this->convert_to_standard_notation($array_fields[$cont_corrector_notacion_cientifica]);
			    
			    $despues=$array_fields[$cont_corrector_notacion_cientifica];
			    //echo "<script>alert('$antes $despues');</script>";
		    }
		    $cont_corrector_notacion_cientifica++;
		}
		
		
		
		
		
		//CORRIGE LOS CAMPOS SOLO SI EL SEXO Y LA FECHA DE NACIMIENTO SON VALIDAS
		if($bool_fecha_nacimiento_valida==true && $bool_sexo_es_valido==true)
		{
		    $this->orden_para_correccion_campos_y_correccion($array_fields,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,0,$conexion_bd_validar_campos);
		    $this->orden_para_correccion_campos_y_correccion($array_fields,$edad,$edad_dias,$edad_meses,$edad_semanas,$numLinea,1,$conexion_bd_validar_campos);
		    		    
		    //FASE ESCRITURA
		    $cont_para_escribir_campos=0;
		    while($cont_para_escribir_campos<count($array_fields))
		    {
		     if($registro_corregido!=""){$registro_corregido.="|";}
		     $registro_corregido.=$array_fields[$cont_para_escribir_campos];
		     $cont_para_escribir_campos++;
		    }//fin while
		      
		    
		}//fin if solo valida si fecha de nacimiento y sexo es valid apartir del campo 11 contando desde cero
		else
		{
		    if($bool_sexo_es_valido==false)
		    {
			$fallo_sexo=true;
			//echo "<script>alert('fallo registro sexo $numLinea');</script>";
		    }
		    if($bool_fecha_nacimiento_valida==false)
		    {
			$fallo_fecha_nacimiento=true;
			//echo "<script>alert('fallo registro fecha de nacimiento $numLinea');</script>";
		    }
		}//fin else para cuando no es valida la fecha de nacimiento o el sexo
		
		$array_retorno=array();
		$array_retorno["booleano"]=$validador_boolean;
		$array_retorno["detalle_correccion_del_error"]=$mensajes_error_campos;
		$array_retorno["registro_corregido"]=$registro_corregido;
		$array_retorno["fallo_sexo"]=$fallo_sexo;
		$array_retorno["fallo_fecha_nacimiento"]=$fallo_fecha_nacimiento;
		
		return $array_retorno;
	}
	//FIN FUNCION QUE VALIDA LOS 118 CAMPOS DE 4505
	
	public function correccion_errores_campos_PyP_4505($linea_con_campos_de_archivo_pyp,$numLinea,&$consecutivo_errores,$fecha_corte_bd,&$conexion_bd_validar_campos)
	{
	    $registro_corregido="";
	    $error_linea = "";
	    $validador_boolean=true;
	    
	    $fallo_sexo=false;
	    $fallo_fecha_nacimiento=false;
	    $fallo_numero_campos=false;
	    
	    $array_campos = explode("|", $linea_con_campos_de_archivo_pyp);
	    
	    //echo "<script>alert('$numLinea ".count($array_campos)."');</script>";
	    
	    $nombre_archivo4505="";
	    $nombre_archivo4505=$this->nombre_archivo_4505;
	    
	    //CONEXION BASE DE DATOS
	    //$conexion_bd_validar_campos = new conexion();
	    
	    //parte que trae las descripciones de las inconsistencias
	    $array_tipo_inconsistencia=array();
	    $array_grupo_inconsistencia=array();
	    $array_detalle_inconsistencia=array();

	    $query1_tipo_validacion="SELECT * FROM gioss_tipo_inconsistencias;";
	    $resultado_query1_tipo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query1_tipo_validacion);
	    foreach($resultado_query1_tipo_validacion as $tipo_validacion)
	    {
		    $array_tipo_inconsistencia[$tipo_validacion["tipo_validacion"]]=$tipo_validacion["descripcion_tipo_validacion"];
	    }
	    $query2_grupo_validacion="SELECT * FROM gioss_grupo_inconsistencias;";
	    $resultado_query2_grupo_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query2_grupo_validacion);
	    foreach($resultado_query2_grupo_validacion as $grupo_validacion)
	    {
		    $array_grupo_inconsistencia[$grupo_validacion["grupo_validacion"]]=$grupo_validacion["descripcion_grupo_validacion"];
	    }
	    $query3_detalle_validacion="SELECT * FROM gioss_detalle_inconsistecias_4505;";
	    $resultado_query3_detalle_validacion=$conexion_bd_validar_campos->consultar2_no_crea_cierra($query3_detalle_validacion);
	    foreach($resultado_query3_detalle_validacion as $detalle_validacion)
	    {
		    $array_detalle_inconsistencia[$detalle_validacion["codigo_detalle_inconsistencia"]]=$detalle_validacion["descripcion_inconsistencia"];
	    }	
	    //fin parte que trae las descripciones de las inconsistencias
	    
	    //se valida la linea con los 119 campos directamente, no se valida la primera linea debido a que es un consolidado
	    if(count($array_campos)==119)
	    {
		
		//parte para reparar cada uno de los 119 campos
		$array_resultados=array();
		$array_resultados= $this->correccion_campos_registro_de_archivo_4505($array_campos,$numLinea,$consecutivo_errores,$fecha_corte_bd,$conexion_bd_validar_campos);
		
		$error_linea = $array_resultados["detalle_correccion_del_error"];
		$validador_boolean= $array_resultados["booleano"];
		$registro_corregido= $array_resultados["registro_corregido"];
		$fallo_sexo=$array_resultados["fallo_sexo"];
		$fallo_fecha_nacimiento=$array_resultados["fallo_fecha_nacimiento"];
	    }
	    else
	    {
		     if($numLinea==0)
		     {
			$validador_boolean=false;
			if($error_linea!=""){$error_linea.="|";}
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia["0301001"])[1];
			$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",03,".$array_tipo_inconsistencia["03"].",0301,".$array_grupo_inconsistencia["0301"].",0301001,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_a_corregir;
			$consecutivo_errores++;
		     }
		     else
		     {
			$validador_boolean=false;
			if($error_linea!=""){$error_linea.="|";}
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_inconsistencia["0301002"])[1];
			$error_linea.=$consecutivo_errores.",".$nombre_archivo4505.",03,".$array_tipo_inconsistencia["03"].",0301,".$array_grupo_inconsistencia["0301"].",0301002,$cadena_descripcion_inconsistencia ...VR:".count($array_campos)." ,".$numLinea.",".$numero_campo_a_corregir;
			$consecutivo_errores++;
			
			$fallo_numero_campos=true;
		     }
		     
		     
	    }//fin else numero de campos incorrecto
	    	    
	    $array_return=array();
	    $array_return["booleano"]=$validador_boolean;
	    $array_return["detalle_correccion_del_error"]=$error_linea;
	    $array_return["registro_corregido"]=$registro_corregido;
	    $array_return["fallo_sexo"]=$fallo_sexo;
	    $array_return["fallo_fecha_nacimiento"]=$fallo_fecha_nacimiento;
	    $array_return["fallo_numero_campos"]=$fallo_numero_campos;
	    return $array_return;
	}//fin funcion
	
	public function alphanumericAndSpace( $string )
	{
	    $string = str_replace("á","a",$string);
	    $string = str_replace("é","e",$string);
	    $string = str_replace("í","i",$string);
	    $string = str_replace("ó","o",$string);
	    $string = str_replace("ú","u",$string);
	    $string = str_replace("Á","A",$string);
	    $string = str_replace("É","E",$string);
	    $string = str_replace("Í","I",$string);
	    $string = str_replace("Ó","O",$string);
	    $string = str_replace("Ú","U",$string);
	    
	    $string = str_replace("ñ","n",$string);
	    $string = str_replace("Ñ","N",$string);
	    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string);
	}
	
	public function alphanumericAndSpace2( $string )
	{
	    $string = str_replace("á","a",$string);
	    $string = str_replace("é","e",$string);
	    $string = str_replace("í","i",$string);
	    $string = str_replace("ó","o",$string);
	    $string = str_replace("ú","u",$string);
	    $string = str_replace("Á","A",$string);
	    $string = str_replace("É","E",$string);
	    $string = str_replace("Í","I",$string);
	    $string = str_replace("Ó","O",$string);
	    $string = str_replace("Ú","U",$string);
    
	    $string = str_replace("ñ","n",$string);
	    $string = str_replace("Ñ","N",$string);
	    return preg_replace('/[^a-zA-Z0-9\s, ;\-@.\/]/', '', $string);
	}
	
	public function alphanumericAndSpace3( $string )
	{
	    $string = str_replace("á","a",$string);
	    $string = str_replace("é","e",$string);
	    $string = str_replace("í","i",$string);
	    $string = str_replace("ó","o",$string);
	    $string = str_replace("ú","u",$string);
	    $string = str_replace("Á","A",$string);
	    $string = str_replace("É","E",$string);
	    $string = str_replace("Í","I",$string);
	    $string = str_replace("Ó","O",$string);
	    $string = str_replace("Ú","U",$string);
	    
	    $string = str_replace("ñ","n",$string);
	    $string = str_replace("Ñ","N",$string);
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-]/', '', $string);
	    $cadena = substr($cadena,0,10);
	    return $cadena;
	}
	
	public function alphanumericAndSpace4( $string )
	{
	    $string = str_replace("á","a",$string);
	    $string = str_replace("é","e",$string);
	    $string = str_replace("í","i",$string);
	    $string = str_replace("ó","o",$string);
	    $string = str_replace("ú","u",$string);
	    $string = str_replace("Á","A",$string);
	    $string = str_replace("É","E",$string);
	    $string = str_replace("Í","I",$string);
	    $string = str_replace("Ó","O",$string);
	    $string = str_replace("Ú","U",$string);
	    
	    $string = str_replace("ñ","n",$string);
	    $string = str_replace("Ñ","N",$string);
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\.]/', '', $string);
	    return $cadena;
	}
	
	public function correccion_query( $string )
	{
	    $string = str_replace("á","a",$string);
	    $string = str_replace("é","e",$string);
	    $string = str_replace("í","i",$string);
	    $string = str_replace("ó","o",$string);
	    $string = str_replace("ú","u",$string);
	    $string = str_replace("Á","A",$string);
	    $string = str_replace("É","E",$string);
	    $string = str_replace("Í","I",$string);
	    $string = str_replace("Ó","O",$string);
	    $string = str_replace("Ú","U",$string);
	    
	    $string = str_replace("ñ","n",$string);
	    $string = str_replace("Ñ","N",$string);
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\/\=\*\_\<\>\(\)\.]/', '', $string);
	    return $cadena;
	}
	
	public function correccion_caracteres_linea( $string )
	{
	    $string = str_replace("á","a",$string);
	    $string = str_replace("é","e",$string);
	    $string = str_replace("í","i",$string);
	    $string = str_replace("ó","o",$string);
	    $string = str_replace("ú","u",$string);
	    $string = str_replace("Á","A",$string);
	    $string = str_replace("É","E",$string);
	    $string = str_replace("Í","I",$string);
	    $string = str_replace("Ó","O",$string);
	    $string = str_replace("Ú","U",$string);
	    
	    $string = str_replace("ñ","n",$string);
	    $string = str_replace("Ñ","N",$string);
	    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\|\.]/', '', $string);
	    return $cadena;
	}
	
	public function procesar_mensaje($mensaje)
	{
		$mensaje_procesado = str_replace("á","a",$mensaje);
		$mensaje_procesado = str_replace("é","e",$mensaje_procesado);
		$mensaje_procesado = str_replace("í","i",$mensaje_procesado);
		$mensaje_procesado = str_replace("ó","o",$mensaje_procesado);
		$mensaje_procesado = str_replace("ú","u",$mensaje_procesado);
		$mensaje_procesado = str_replace("ñ","n",$mensaje_procesado);
		$mensaje_procesado = str_replace("Á","A",$mensaje_procesado);
		$mensaje_procesado = str_replace("É","E",$mensaje_procesado);
		$mensaje_procesado = str_replace("Í","I",$mensaje_procesado);
		$mensaje_procesado = str_replace("Ó","O",$mensaje_procesado);
		$mensaje_procesado = str_replace("Ú","U",$mensaje_procesado);
		$mensaje_procesado = str_replace("Ñ","N",$mensaje_procesado);
		$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
		$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
		$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
		$mensaje_procesado = $this->alphanumericAndSpace($mensaje_procesado);
		
		return $mensaje_procesado;
	}
	
	public function procesar_mensaje_query($mensaje)
	{
		$mensaje_procesado = str_replace("á","a",$mensaje);
		$mensaje_procesado = str_replace("é","e",$mensaje_procesado);
		$mensaje_procesado = str_replace("í","i",$mensaje_procesado);
		$mensaje_procesado = str_replace("ó","o",$mensaje_procesado);
		$mensaje_procesado = str_replace("ú","u",$mensaje_procesado);
		$mensaje_procesado = str_replace("ñ","n",$mensaje_procesado);
		$mensaje_procesado = str_replace("Á","A",$mensaje_procesado);
		$mensaje_procesado = str_replace("É","E",$mensaje_procesado);
		$mensaje_procesado = str_replace("Í","I",$mensaje_procesado);
		$mensaje_procesado = str_replace("Ó","O",$mensaje_procesado);
		$mensaje_procesado = str_replace("Ú","U",$mensaje_procesado);
		$mensaje_procesado = str_replace("Ñ","N",$mensaje_procesado);
		$mensaje_procesado = str_replace(" "," ",$mensaje_procesado);
		$mensaje_procesado = str_replace("'"," ",$mensaje_procesado);
		$mensaje_procesado = str_replace("\n"," ",$mensaje_procesado);
		$mensaje_procesado = $this->correccion_query($mensaje_procesado);
		
		return $mensaje_procesado;
	}
}//fin clase
?>