<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');


require_once '../utiles/conf_personalizada.php';

function reparacion_campo_en_blanco_formato_vih(&$campos,
						$nlinea,
						&$consecutivo_errores,
						$array_tipo_validacion,
						$array_grupo_validacion,
						$array_detalle_validacion,
						$nombre_archivo_registrado,
						$fecha_de_corte,
						$cod_prestador,
						$cod_eapb,
						&$diccionario_identificacion,
						&$diccionario_identificacion_lineas,
						&$coneccionBD, $array_numero_campo_bd)
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();
	
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='04' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0401' ORDER BY numero_de_orden ";
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
	//CALCULO EDAD
	$campos[10]=corrector_formato_fecha($campos[10],$fecha_de_corte,true);
	$fecha_nacimiento= explode("-",$campos[10]);
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
	//fin calculo de la edad
	//FIN CALCULO EDAD
	
	$cont_corrector_notacion_cientifica=0;
	while($cont_corrector_notacion_cientifica<count($campos))
	{
	    if(!ctype_digit(trim($campos[$cont_corrector_notacion_cientifica]))
	       && is_numeric(trim($campos[$cont_corrector_notacion_cientifica])))
	    {
		    $antes=$campos[$cont_corrector_notacion_cientifica];
		    
		    $campos[$cont_corrector_notacion_cientifica]="".convert_to_standard_notation($campos[$cont_corrector_notacion_cientifica]);
		    
		    $despues=$campos[$cont_corrector_notacion_cientifica];
		    //echo "<script>alert('$antes $despues');</script>";
	    }
	    $cont_corrector_notacion_cientifica++;
	}
	
	//campo 0 aka 0 prestador
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 1 aka 1 EAPB-EPS
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]=$cod_eapb;
	    }//fin if
		
	}//if si existe campo
	
	//campo 2 aka 2 R??gimen 1=gioss_afiliados_eapb_rc,2=gioss_afiliados_regimen_subsidiado,4=gioss_afiliados_eapb_rp
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	//campo 3 aka 3
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="3";
		}//fin if 
		
	}//if si existe campo
	
	//campo 4 aka 4
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica
		
		
	}//if si existe campo
	
	//campo 5 aka 5
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(trim($campos[$numero_campo])=="")
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo
	
	
	//campo 6 aka 6
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica
	}//if si existe campo
	
	
	//campo 7 aka 7
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(trim($campos[$numero_campo])=="")
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo
	
	
	//campo 8 aka 8 TI
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
	}//if si existe campo
	
	//campo 9 aka 9 Numero identificacion Verifica duplicados con la linea duplicada
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		
		//no aplica
		
	}//if si existe campo
	
	//campo 10 aka 10
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
	}//if si existe campo
	
	//campo 11 aka 11
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
	}//if si existe campo
	
	
	//campo 12 aka 12
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="4";
		}//fin if 
		
	}//if si existe campo
	
	//campo 13 aka 13
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
		    $campos[$numero_campo]="SIN DATO";
		}//fin if
		
		
		
	}//if si existe campo
	
	//campo 14 aka 14
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
		    $campos[$numero_campo]="0000000000";
		}//fin if
		
		
		
	}//if si existe campo
	
	//campo 15 aka 15
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
		
		
	}//if si existe campo
	
	//campo 16 aka 16
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1995-01-01";
			
			$fecha_nacimiento=trim($campos[10]);
			$es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
			$inferior_fecha_de_nacimiento=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_nacimiento);
			if($inferior_fecha_de_nacimiento>0
				   && $es_campo_actual_fecha_calendario<0
				   )
			{
			    $campos[$numero_campo]=$fecha_nacimiento;
			}//fin if
	    }//fin if
	}//if si existe campo
	
	//campo 17 aka 17
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="3";
	    }//fin if 
		
		
	}//if si existe campo
	
	//campo 18 aka 18
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
		
		
	}//if si existe campo
	
	//campo 19 aka 19
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }//fin if
		
	}//if si existe campo
	
	//campo 20 aka 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 21 aka 21
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{		
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 22 aka 22
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	//campo 23 aka 23
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{	   
	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="10";
	    }	    
		
	}//if si existe campo
	
	//campo 24 aka 24
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 25 aka 25
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 26 aka 26
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 27 aka 27
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="12";
	    }
	}//if si existe campo
	
	//campo 28 aka 27.1
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="13";
	    }	    		
	}//if si existe campo
	
	
	//campo 29 aka 28
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99005";
	    }
		
	}//if si existe campo
	
	
	//campo 30 aka 29
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99005";
	    }
	}//if si existe campo
	
	//campo 31 aka 30
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }//fin if
		
	}//if si existe campo
	
	
	//campo 32 aka 31
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99005";
	    }//fin if
	}//if si existe campo
	
	
	//campo 33 aka 32
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99005";
	    }
	}//if si existe campo
	
	
	//campo 34 aka 33
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }//fin if
		
	}//if si existe campo
	
	
	//campo 35 aka 34
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="10";
	    }//fin if	    
	}//if si existe campo
	
	
	//campo 36 aka 35
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }//fin if
		
	}//if si existe campo
	
	
	//campo 37 aka 36
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	
	//campo 38 aka 37
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 39 aka 38
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 40 aka 39
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 41 aka 40
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 42 aka 41
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 43 aka 42
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 44 aka 43
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 45 aka 44.1
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 46 aka 44.2
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 47 aka 44.3
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	
	//campo 48 aka 44.4
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 49 aka 44.5
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 50 aka 44.6
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 51 aka 44.7
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	
	
	//campo 52 aka 44.8
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
		
	
	//campo 53 aka 44.9
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 54 aka 44.10
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 55 aka 44.11
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo	
	
	//campo 56 aka 44.12
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
	   if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo	
	
	//campo 57 aka 44.13
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo

	//campo 58 aka 44.14
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo	

	//campo 59 aka 44.15
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo	
	
	//campo 60 aka 44.16
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo	
	
	//campo 61 aka 44.17
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo	
	
	//campo 62 aka 44.18
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo	
	
	//campo 63 aka 44.19
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo	
	
	//campo 64 aka 44.20
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo	
	
	//campo 65 aka 45
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo	
	
	//campo 66 aka 46
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo	
	
	//campo 67 aka 47
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="97";
	    }
	}//if si existe campo	
	
	//campo 68 aka 48
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo

	//campo 69 aka 49
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo

	//campo 70 aka 50
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }	    	    
		
	}//if si existe campo
	
	//campo 71 aka 51
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99";
	    }//fin if
	}//if si existe campo
	
	//campo 72 aka 52
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99";
	    }
	    
	}//if si existe campo
	
	//campo 73 aka 53.1
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 74 aka 53.2
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 75 aka 53.3
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }

	}//if si existe campo
	
	//campo 76 aka 53.4
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 77 aka 53.5
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 78 aka 53.6
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 79 aka 53.7
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 80 aka 53.8
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 81 aka 53.9
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 82 aka 53.10
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 83 aka 53.11
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 84 aka 53.12
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 85 aka 53.13
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 86 aka 53.14
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    } 
	}//if si existe campo
	
	//campo 87 aka 53.15
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 88 aka 53.16
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 89 aka 53.17
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 90 aka 53.18
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 91 aka 53.19
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 92 aka 53.20
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    } 
	}//if si existe campo
	
	//campo 93 aka 54
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	
	//campo 94 aka 55
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }//fin if
	}//if si existe campo
	
	//campo 95 aka 56
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n54=trim($campos[93]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="" && strlen($campo_n54)>=5)
	    {
			$consulta2="";
			$consulta2.="SELECT * FROM gios_mpio WHERE cod_municipio='".substr($campo_n54,0,5)."'; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if(count($resultado2)!=0)
			{
			    $campos[$numero_campo]=substr($campo_n54,0,5);
			}//fin if
	    }//fin if		
		
	}//if si existe campo
	
	//campo 96 aka 57
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 97 aka 58
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 98 aka 59
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 99 aka 60
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 100 aka 61
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 101 aka 62
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 102 aka 63
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	
	}//if si existe campo
	
	//campo 103 aka 64
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 104 aka 65
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 105 aka 66
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 106 aka 67
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 107 aka 68
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 108 aka 69
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 109 aka 70
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 110 aka 71
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 111 aka 72
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 112 aka 73
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 113 aka 74
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="3";
	    }
		
	}//if si existe campo
	
	//campo 114 aka 75
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	//campo 115 aka 76
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99999";
	    }
	}//if si existe campo
	
	//campo 116 aka 77
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	    
	}//if si existe campo
	
	//campo 117 aka 78
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99999";
	    }
	}//if si existe campo
	
	//campo 118 aka 79
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	    
	}//if si existe campo
	
	//campo 119 aka 80
	$numero_campo=119;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }	    
	    
	}//if si existe campo
	
	//campo 120 aka 81
	$numero_campo=120;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 121 aka 81.1
	$numero_campo=121;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }

		
	}//if si existe campo
	
	//campo 122 aka 82
	$numero_campo=122;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }	    
		
	}//if si existe campo
	
	
	//campo 123 aka 83
	$numero_campo=123;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="3";
	    }
		
	}//if si existe campo
	
	
	//campo 124 aka 84
	$numero_campo=124;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	
	//campo 125 aka 85
	$numero_campo=125;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 126 aka 86
	$numero_campo=126;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 127 aka 87
	$numero_campo=127;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99";
	    }
	    
	}//if si existe campo
	
	
	//campo 128 aka 88
	$numero_campo=128;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	
	//campo 129 aka 89
	$numero_campo=129;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 130 aka 90
	$numero_campo=130;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	
	//campo 131 aka 91.1
	$numero_campo=131;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 132 aka 91.2
	$numero_campo=132;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 133 aka 91.3
	$numero_campo=133;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 134 aka 91.4
	$numero_campo=134;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 135 aka 91.5
	$numero_campo=135;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 136 aka 91.6
	$numero_campo=136;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 137 aka 91.7
	$numero_campo=137;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 138 aka 91.8
	$numero_campo=138;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 139 aka 91.9
	$numero_campo=139;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 140 aka 91.10
	$numero_campo=140;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 141 aka 91.11
	$numero_campo=141;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 142 aka 91.12
	$numero_campo=142;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 143 aka 91.13
	$numero_campo=143;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 144 aka 91.14
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 145 aka 91.15
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 146 aka 91.16
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 147 aka 91.17
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 148 aka 91.18
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	
	//campo 149 aka 91.19
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 150 aka 91.20
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 151 aka 92
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	//campo 152 aka 92.1
	$numero_campo=152;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 153 aka 92.2
	$numero_campo=153;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 154 aka 92.3
	$numero_campo=154;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 155 aka 92.4
	$numero_campo=155;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 156 aka 92.5
	$numero_campo=156;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 157 aka 93
	$numero_campo=157;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 158 aka 94
	$numero_campo=158;
	if(isset($campos[$numero_campo]))
	{		
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 159 aka 95.1
	$numero_campo=159;
	if(isset($campos[$numero_campo]))
	{
	   if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	    
	}//if si existe campo
	
	//campo 160 aka 95.2
	$numero_campo=160;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 161 aka 95.3
	$numero_campo=161;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 162 aka 95.4
	$numero_campo=162;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 163 aka 95.5
	$numero_campo=163;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 164 aka 95.6
	$numero_campo=164;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 165 aka 95.7
	$numero_campo=165;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 166 aka 95.8
	$numero_campo=166;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 167 aka 95.9
	$numero_campo=167;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
				
	}//if si existe campo
	
	//campo 168 aka 95.10
	$numero_campo=168;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 169 aka 95.11
	$numero_campo=169;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 170 aka 95.12
	$numero_campo=170;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 171 aka 95.13
	$numero_campo=171;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 172 aka 95.14
	$numero_campo=172;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 173 aka 96
	$numero_campo=173;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
	}//if si existe campo
	
	//campo 174 aka 97
	$numero_campo=174;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 175 aka 98
	$numero_campo=175;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 176 aka 99
	$numero_campo=176;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 177 aka 100
	$numero_campo=177;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	//campo 178 aka 101
	$numero_campo=178;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 179 aka 102
	$numero_campo=179;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	    
	}//if si existe campo
	
	//campo 180 aka 103
	$numero_campo=180;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="0";
	    }//fin if
	    
		
		
	}//if si existe campo
	
	//campo 181 aka 104
	$numero_campo=181;
	if(isset($campos[$numero_campo]))
	{		
		if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 182 aka 105
	$numero_campo=182;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="9";
	    }//fin if
		
		
	}//if si existe campo
	
	//campo 183 aka 106
	$numero_campo=183;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="1799-01-01";
	    }//fin if
	}//if si existe campo
	
	//campo 184 aka 107
	$numero_campo=184;
	
	if(isset($campos[$numero_campo]))
	{
	    $fix_ultimo_campo=preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($campos[$numero_campo]) );
	    
	    if(trim($fix_ultimo_campo)=="")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
		
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarVIH




function reparacion_valor_permitido_formato_vih(&$campos,
						$nlinea,
						&$consecutivo_errores,
						$array_tipo_validacion,
						$array_grupo_validacion,
						$array_detalle_validacion,
						$nombre_archivo_registrado,
						$fecha_de_corte,
						$cod_prestador,
						$cod_eapb,
						&$diccionario_identificacion,
						&$diccionario_identificacion_lineas,
						&$coneccionBD, $array_numero_campo_bd)
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();
	
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='04' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0401' ORDER BY numero_de_orden ";
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
	//CALCULO EDAD
	$campos[10]=corrector_formato_fecha($campos[10],$fecha_de_corte,true);
	$fecha_nacimiento= explode("-",$campos[10]);
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
	//fin calculo de la edad
	//FIN CALCULO EDAD
	
	$cont_corrector_notacion_cientifica=0;
	while($cont_corrector_notacion_cientifica<count($campos))
	{
	    if(!ctype_digit(trim($campos[$cont_corrector_notacion_cientifica]))
	       && is_numeric(trim($campos[$cont_corrector_notacion_cientifica])))
	    {
		    $antes=$campos[$cont_corrector_notacion_cientifica];
		    
		    $campos[$cont_corrector_notacion_cientifica]="".convert_to_standard_notation($campos[$cont_corrector_notacion_cientifica]);
		    
		    $despues=$campos[$cont_corrector_notacion_cientifica];
		    //echo "<script>alert('$antes $despues');</script>";
	    }
	    $cont_corrector_notacion_cientifica++;
	}
	
	//campo 0 aka 0 prestador
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
				
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			
		}//fin if
		
	}//if si existe campo
	
	//campo 1 aka 1 EAPB-EPS
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{		
		
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim(strtoupper($campos[$numero_campo])) );
			
		}//fin if
	}//if si existe campo
	
	//campo 2 aka 2 R??gimen 1=gioss_afiliados_eapb_rc,2=gioss_afiliados_regimen_subsidiado,4=gioss_afiliados_eapb_rp
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
				
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			
		}//fin if
	}//if si existe campo
	
	//campo 3 aka 3
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
				
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			
		}//fin if
	}//if si existe campo
	
	//campo 4 aka 4
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
				
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim(strtoupper($campos[$numero_campo])) );
			
		}//fin if
		
	}//if si existe campo
	
	//campo 5 aka 5
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
		
	}//if si existe campo
	
	
	//campo 6 aka 6
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
		
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			
		}//fin if
	}//if si existe campo
	
	
	//campo 7 aka 7
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
		
	}//if si existe campo
	
	
	//campo 8 aka 8 TI
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		
		
		//poner en mayuscualas
		$campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			
		}//fin if
		
	}//if si existe campo
	
	//campo 9 aka 9 Numero identificacion Verifica duplicados con la linea duplicada
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
				
		
	}//if si existe campo
	
	//campo 10 aka 10
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
				
		//correccion formato fecha al formato AAAA-MM-DD year-month-day fecha nacimiento
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,true);
		
	}//if si existe campo
	
	//campo 11 aka 11
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		
		
		//poner en mayuscualas sexo
		$campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		
		//formato de campo
		if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		{
			$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			
		}//fin if
		
	}//if si existe campo
	
	
	//campo 12 aka 12
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		
	    if($campos[$numero_campo]!="1" 
	    	&& $campos[$numero_campo]!="2" 
	    	&& $campos[$numero_campo]!="4"
	    	)
	    {
			$campos[$numero_campo]="4";
	    }
	}//if si existe campo
	
	//campo 13 aka 13
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 14 aka 14
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=str_replace(" ","",$campos[$numero_campo]);
	    $campos[$numero_campo]=str_replace(".",",",$campos[$numero_campo]);
		
	    if(strlen($campos[$numero_campo])>21)
	    {
			$campos[$numero_campo]=substr($campos[$numero_campo],0,21);
	    }
	}//if si existe campo
	
	//campo 15 aka 15
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
	    if(strlen($campos[$numero_campo])==4
	       && (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8") )
	    {
			$campos[$numero_campo]="0".$campos[$numero_campo];
	    }
	    else if(strlen($campos[$numero_campo])>5)
	    {
			$campos[$numero_campo]=substr($campos[$numero_campo],0,5);
	    }
		
		
		
	}//if si existe campo
	
	//campo 16 aka 16
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    //correccion formato fecha al formato AAAA-MM-DD year-month-day 
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
	    
	    $excede_fecha_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    $es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $es_inferior_a_1995_01_01=diferencia_dias_entre_fechas($campos[$numero_campo],"1995-01-01");
	    
	    if($es_campo_actual_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_inferior_a_1995_01_01>0)
	    {
			$campos[$numero_campo]="1995-01-01";
			
			$fecha_nacimiento=trim($campos[10]);
			$es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
			$inferior_fecha_de_nacimiento=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_nacimiento);
			if($inferior_fecha_de_nacimiento>0
				   && $es_campo_actual_fecha_calendario<0
				   )
			{
			    $campos[$numero_campo]=$fecha_nacimiento;
			}//fin if
	    }//fin else
	    
	    
	}//if si existe campo
	
	//campo 17 aka 17
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    
	    if(trim($campos[$numero_campo])!="0"
	       && trim($campos[$numero_campo])!="1"
	       && trim($campos[$numero_campo])!="2"
	       && trim($campos[$numero_campo])!="3"
	       )
	    {
			$campos[$numero_campo]="3";
	    }//fin if
		
	}//if si existe campo
	
	//campo 18 aka 18
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])!="0"
	       && trim($campos[$numero_campo])!="1"
	       && trim($campos[$numero_campo])!="2"
	       && trim($campos[$numero_campo])!="3"
	       )
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
		
		
	}//if si existe campo
	
	//campo 19 aka 19
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		//campo obligatorio
		if($campos[$numero_campo]!="0"
			&& $campos[$numero_campo]!="1"
			)
		{
		    $campos[$numero_campo]="0";
		}//fin if
		
	}//if si existe campo
	
	//campo 20 aka 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    

	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
	       )
	    {
			$campos[$numero_campo]="0";
	    }
	    
		
	}//if si existe campo
	
	//campo 21 aka 21
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
		
		
	    $array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}
	    
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1811-01-01"
		    && trim($campos[$numero_campo])!="1822-02-01"
		    )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 22 aka 22
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
				
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}
		

	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
	       )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }

		
		
	}//if si existe campo
	
	//campo 23 aka 23
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="10"
	       )
	    {
			$campos[$numero_campo]="10";
	    }
	    
		
	}//if si existe campo
	
	//campo 24 aka 24
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 25 aka 25
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
	    
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$campos[$numero_campo]."'; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if(count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		    )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 26 aka 26
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="0"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
		
	}//if si existe campo
	
	
	//campo 27 aka 27
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   )
	    {
			$campos[$numero_campo]="12";
	    }
		
	    
	}//if si existe campo
	
	//campo 28 aka 27.1
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{

	    $campos[$numero_campo]="".intval($campos[$numero_campo]);

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   && $campos[$numero_campo]!="13"
		   && $campos[$numero_campo]!="14"
		   && $campos[$numero_campo]!="15"		   
	       )
	    {
			$campos[$numero_campo]="13";
	    }
		
	    
		
	}//if si existe campo
	
	
	//campo 29 aka 28
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
	    {
			$campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    
	    if(
	    	(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10000)
		   && intval($campos[$numero_campo])!=99001
		   && intval($campos[$numero_campo])!=99002
		   && intval($campos[$numero_campo])!=99003
		   && intval($campos[$numero_campo])!=99004
		   && intval($campos[$numero_campo])!=99005
	       )
	    {
			$campos[$numero_campo]="99005";
	    }
	    
		
	}//if si existe campo
	
	
	//campo 30 aka 29
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
		
		if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>50000)
		   && intval($campos[$numero_campo])!=99001
		   && intval($campos[$numero_campo])!=99002
		   && intval($campos[$numero_campo])!=99003
		   && intval($campos[$numero_campo])!=99004
		   && intval($campos[$numero_campo])!=99005
		   )
		{
		    $campos[$numero_campo]="99005";
		}
	}//if si existe campo
	
	//campo 31 aka 30
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    
	    $array_fecha=explode("-",$campos[$numero_campo]);
	    if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
	    {
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 1800-01-01
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);
	    }//fin if
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1788-01-01"
		    )
	    {
			$campos[$numero_campo]="1800-01-01";
	    }//fin if
		
	}//if si existe campo
	
	
	//campo 32 aka 31
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
				
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
		
		if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10000)
		   && intval($campos[$numero_campo])!=99001
		   && intval($campos[$numero_campo])!=99002
		   && intval($campos[$numero_campo])!=99003
		   && intval($campos[$numero_campo])!=99004
		   && intval($campos[$numero_campo])!=99005
		   )
		{
		    $campos[$numero_campo]="99005";
		}
	}//if si existe campo
	
	
	//campo 33 aka 32
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
		
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
		
		
		if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>50000)
		   && intval($campos[$numero_campo])!=99001
		   && intval($campos[$numero_campo])!=99002
		   && intval($campos[$numero_campo])!=99003
		   && intval($campos[$numero_campo])!=99004
		   && intval($campos[$numero_campo])!=99005
		   )
		{
		    $campos[$numero_campo]="99005";
		}
	}//if si existe campo
	
	
	//campo 34 aka 33
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="0"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	
	//campo 35 aka 34
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="0"
		    )
	    {
			$campos[$numero_campo]="10";
	    }
	    
	}//if si existe campo
	
	
	//campo 36 aka 35
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo
	
	
	//campo 37 aka 36
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
			    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 38 aka 37
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
			    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
		
		
		
	}//if si existe campo
	
	//campo 39 aka 38
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 40 aka 39
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 41 aka 40
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	   		    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 42 aka 41
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 43 aka 42
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="9"
			)
	    {
			$campos[$numero_campo]="9";
	    }
		
		
		
	}//if si existe campo
	
	
	//campo 44 aka 43
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	   
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"	
	       )
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo
	
	
	//campo 45 aka 44.1
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 46 aka 44.2
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);

	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	
	//campo 47 aka 44.3
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
				
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	
	//campo 48 aka 44.4
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 49 aka 44.5
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
				
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 50 aka 44.6
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 51 aka 44.7
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	
	
	//campo 52 aka 44.8
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
		
	
	//campo 53 aka 44.9
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 54 aka 44.10
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo
	
	
	//campo 55 aka 44.11
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
		
	}//if si existe campo	
	
	//campo 56 aka 44.12
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
			    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo	
	
	//campo 57 aka 44.13
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo

	//campo 58 aka 44.14
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo	

	//campo 59 aka 44.15
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo	
	
	//campo 60 aka 44.16
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		//campo obligatorio
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo	
	
	//campo 61 aka 44.17
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
		
		$consulta_medicamento="";
		$consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
		if( count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   && $campo_n30=="1800-01-01"
		   )
		{
		    $campos[$numero_campo]="0";
		}
		
		
	}//if si existe campo	
	
	//campo 62 aka 44.18
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
		
		$consulta_medicamento="";
		$consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
		if( count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo	
	
	//campo 63 aka 44.19
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
		
		$consulta_medicamento="";
		$consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
		if( count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo	
	
	//campo 64 aka 44.20
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
		
		$consulta_medicamento="";
		$consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
		if( count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo	
	
	//campo 65 aka 45
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
			    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo	
	
	//campo 66 aka 46
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		

	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="98"
		   && $campos[$numero_campo]!="99"
		   )
	    {
			$campos[$numero_campo]="0";
	    }
		
		
	}//if si existe campo	
	
	//campo 67 aka 47
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
	    
	    
	    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>50)
		   && intval($campos[$numero_campo])!=97
		   && intval($campos[$numero_campo])!=98
		   && intval($campos[$numero_campo])!=99
	       )
	    {
			$campos[$numero_campo]="97";
	    }
		
	}//if si existe campo	
	
	//campo 68 aka 48
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo

	//campo 69 aka 49
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if

	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"		    
		    && trim($campos[$numero_campo])!="1811-01-01"
		    && trim($campos[$numero_campo])!="1822-02-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1788-01-01"
		    )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	    
		
	}//if si existe campo

	//campo 70 aka 50
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="9")
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
		
	}//if si existe campo
	
	//campo 71 aka 51
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		

	    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20)
		   && intval($campos[$numero_campo])!=99
	       )
	    {
			$campos[$numero_campo]="99";
	    }
	    
	}//if si existe campo
	
	//campo 72 aka 52
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
		
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
	    
	    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20)
		   && intval($campos[$numero_campo])!=99
	       )
	    {
			$campos[$numero_campo]="99";
	    }
		
	}//if si existe campo
	
	//campo 73 aka 53.1
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
		
		
		
		
	}//if si existe campo
	
	//campo 74 aka 53.2
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
		
	}//if si existe campo
	
	//campo 75 aka 53.3
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
		
	}//if si existe campo
	
	//campo 76 aka 53.4
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 77 aka 53.5
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);

	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 78 aka 53.6
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 79 aka 53.7
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 80 aka 53.8
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 81 aka 53.9
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 82 aka 53.10
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 83 aka 53.11
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 84 aka 53.12
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 85 aka 53.13
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 86 aka 53.14
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 87 aka 53.15
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 88 aka 53.16
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 89 aka 53.17
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 90 aka 53.18
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if
		
	}//if si existe campo
	
	//campo 91 aka 53.19
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 92 aka 53.20
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
		    $campos[$numero_campo]="0";
	    }//fin if 
	}//if si existe campo
	
	//campo 93 aka 54
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
		}//fin if 
		else if(strlen($campos[$numero_campo])==11 &&
	       (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8")
	       )
		{
		    $campos[$numero_campo]="0".$campos[$numero_campo];
		}
		else if(strlen($campos[$numero_campo])==10)
		{
		    $campos[$numero_campo]=$campos[$numero_campo]."01";
		}
		
		
		$consulta="";
		$consulta.="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$campos[$numero_campo]."'; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		if(intval($campos[$numero_campo])!=9 && count($resultado)==0)
		{
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
		}
		
		
	}//if si existe campo
	
	
	//campo 94 aka 55
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{	    
	    $array_fecha=explode("-",$campos[$numero_campo]);
	    if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
	    {
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
	    }//fin if
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    
	    
	    $campo_ant_20=$campos[20];
	    
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }	    
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1799-01-01"
		    )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }//fin if
	}//if si existe campo
	
	//campo 95 aka 56
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    if(strlen($campos[$numero_campo])==4
	       && (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8") )
	    {
		$campos[$numero_campo]="0".$campos[$numero_campo];
	    }
	    else if(strlen($campos[$numero_campo])>5)
	    {
		$campos[$numero_campo]=substr($campos[$numero_campo],0,5);
	    }
	    
	    $campo_n54=trim($campos[93]);
	    //campo obligatorio
	    $consulta="";
	    $consulta.="SELECT * FROM gios_mpio WHERE cod_municipio='".$campos[$numero_campo]."'; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if(count($resultado)==0 && strlen($campo_n54)>=5)
	    {
			$consulta2="";
			$consulta2.="SELECT * FROM gios_mpio WHERE cod_municipio='".substr($campo_n54,0,5)."'; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if(count($resultado2)!=0)
			{
			    $campos[$numero_campo]=substr($campo_n54,0,5);
			}//fin if
		
	    }//fin if
		
	}//if si existe campo
	
	//campo 96 aka 57
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	
	}//if si existe campo
	
	//campo 97 aka 58
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 98 aka 59
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="0"
	       && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 99 aka 60
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 100 aka 61
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo
	
	
	//campo 101 aka 62
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="4"
	       && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	    
	}//if si existe campo
	
	//campo 102 aka 63
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{

	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 103 aka 64
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	    
	    
	}//if si existe campo
	
	//campo 104 aka 65
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
	       && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }
	    //fin if
	}//if si existe campo
	
	//campo 105 aka 66
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }
	    //fin if
		
	}//if si existe campo
	
	//campo 106 aka 67
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
	       && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	    
		
	}//if si existe campo
	
	//campo 107 aka 68
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	    
	}//if si existe campo
	
	//campo 108 aka 69
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	    
		
	}//if si existe campo
	
	//campo 109 aka 70
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
		
	}//if si existe campo
	
	//campo 110 aka 71
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
		
	}//if si existe campo
	
	//campo 111 aka 72
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
		
	}//if si existe campo
	
	//campo 112 aka 73
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   )
	    {
		$campos[$numero_campo]="0";   
	    }//fin if
		
	    
		
	}//if si existe campo
	
	//campo 113 aka 74
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    //campo obligatorio
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   )
	    {
			$campos[$numero_campo]="3";
	    }
	    
		
		
	}//if si existe campo
	
	//campo 114 aka 75
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{		
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}
		
		$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }	    
	    else if($es_fecha_calendario>0  
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1800-01-01"
		    )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	//campo 115 aka 76
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
	    
	    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10000)
		   && intval($campos[$numero_campo])!=88888
		   && intval($campos[$numero_campo])!=99999
		   )
	    {
			$campos[$numero_campo]="99999";
	    }
	    
	}//if si existe campo
	
	//campo 116 aka 77
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if
		
		$es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    
	    
	    $campo_ant_20=$campos[20];
	    
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }	    
	    else if($es_fecha_calendario>0  
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1800-01-01"
		    )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	    
	}//if si existe campo
	
	//campo 117 aka 78
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
	    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>5000)
		   && intval($campos[$numero_campo])!=88888
		   && intval($campos[$numero_campo])!=99999
		   )
	    {
			$campos[$numero_campo]="99999";
	    }
	    
	}//if si existe campo
	
	//campo 118 aka 79
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    
	    $array_fecha=explode("-",$campos[$numero_campo]);
	    if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
	    {
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
	    }//fin if
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    
	    
	    
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }	    
	    else if($es_fecha_calendario>0  
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1800-01-01"
		    )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 119 aka 80
	$numero_campo=119;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="0"
	       && $campos[$numero_campo]!="9")
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 120 aka 81
	$numero_campo=120;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 121 aka 81.1
	$numero_campo=121;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9")
	    {
			$campos[$numero_campo]="9";
	    }
	    
	    

		
	}//if si existe campo
	
	//campo 122 aka 82
	$numero_campo=122;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	
	//campo 123 aka 83
	$numero_campo=123;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="3";
	    }
	    
		
		
	}//if si existe campo
	
	
	//campo 124 aka 84
	$numero_campo=124;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		//campo obligatorio
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	
	//campo 125 aka 85
	$numero_campo=125;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		)
	    {
			$campos[$numero_campo]="9";
	    }
	    
	    
		
	}//if si existe campo
	
	
	//campo 126 aka 86
	$numero_campo=126;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	
	//campo 127 aka 87
	$numero_campo=127;
	if(isset($campos[$numero_campo]))
	{
		
		if(is_numeric($campos[$numero_campo]) && !ctype_digit($campos[$numero_campo]))
		{
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
		}
		
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="5"
		   && $campos[$numero_campo]!="6"
		   && $campos[$numero_campo]!="7"
		   && $campos[$numero_campo]!="8"
		   && $campos[$numero_campo]!="9"
		   && $campos[$numero_campo]!="10"
		   && $campos[$numero_campo]!="11"
		   && $campos[$numero_campo]!="12"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="99"
	       )
	    {
			$campos[$numero_campo]="99";
	    }
	    
	}//if si existe campo
	
	
	//campo 128 aka 88
	$numero_campo=128;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 129 aka 89
	$numero_campo=129;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="4"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo
	
	//campo 130 aka 90
	$numero_campo=130;
	if(isset($campos[$numero_campo]))
	{
		
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		
		}//fin if

		
		//campo obligatorio
	    $campo_ant_20=$campos[20];
	    $campo_ant_89=trim($campos[129]);
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		    && trim($campos[$numero_campo])!="1788-01-01"
	       )
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	
	//campo 131 aka 91.1
	$numero_campo=131;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
		
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 132 aka 91.2
	$numero_campo=132;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 133 aka 91.3
	$numero_campo=133;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 134 aka 91.4
	$numero_campo=134;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 135 aka 91.5
	$numero_campo=135;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 136 aka 91.6
	$numero_campo=136;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 137 aka 91.7
	$numero_campo=137;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 138 aka 91.8
	$numero_campo=138;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 139 aka 91.9
	$numero_campo=139;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 140 aka 91.10
	$numero_campo=140;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    

	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 141 aka 91.11
	$numero_campo=141;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 142 aka 91.12
	$numero_campo=142;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 143 aka 91.13
	$numero_campo=143;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 144 aka 91.14
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 145 aka 91.15
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 146 aka 91.16
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		)
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	//campo 147 aka 91.17
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	    
	    if(count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 148 aka 91.18
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	    
	    if(count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	
	//campo 149 aka 91.19
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	    
	    if(count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 150 aka 91.20
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	    
	    if(count($resultado)==0
		   && $campos[$numero_campo]!="0"
		   )
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
	}//if si existe campo
	
	//campo 151 aka 92
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2")
	    {
			$campos[$numero_campo]="2";
	    }
	    
	    
	    
		
	}//if si existe campo
	
	//campo 152 aka 92.1
	$numero_campo=152;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2")
	    {
			$campos[$numero_campo]="0";
	    }
	    
		
		
	}//if si existe campo
	
	//campo 153 aka 92.2
	$numero_campo=153;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   )
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 154 aka 92.3
	$numero_campo=154;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   )
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 155 aka 92.4
	$numero_campo=155;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   )
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 156 aka 92.5
	$numero_campo=156;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]="".intval($campos[$numero_campo]);
	    
	    if($campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   )
	    {
			$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 157 aka 93
	$numero_campo=157;
	if(isset($campos[$numero_campo]))
	{		
	    
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
		   )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 158 aka 94
	$numero_campo=158;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 1800-01-01
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);
		}//fin if
			
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    $campo_ant_18=$campos[18];	    
	    $campo_n93=trim($campos[157]);
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		    )
	    {
			$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 159 aka 95.1
	$numero_campo=159;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
		
	}//if si existe campo
	
	//campo 160 aka 95.2
	$numero_campo=160;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 161 aka 95.3
	$numero_campo=161;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 162 aka 95.4
	$numero_campo=162;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 163 aka 95.5
	$numero_campo=163;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 164 aka 95.6
	$numero_campo=164;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 165 aka 95.7
	$numero_campo=165;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 166 aka 95.8
	$numero_campo=166;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 167 aka 95.9
	$numero_campo=167;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
				
	}//if si existe campo
	
	//campo 168 aka 95.10
	$numero_campo=168;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	       
	    if( count($resultado)==0
	       && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 169 aka 95.11
	$numero_campo=169;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	       
	    if( count($resultado)==0
	       && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 170 aka 95.12
	$numero_campo=170;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	       
	    if( count($resultado)==0
	       && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 171 aka 95.13
	$numero_campo=171;
	if(isset($campos[$numero_campo]))
	{
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	       
	    if( count($resultado)==0
	       && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 172 aka 95.14
	$numero_campo=172;
	if(isset($campos[$numero_campo]))
	{
	    
	    $consulta_medicamento="";
	    $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
	       
	    if( count($resultado)==0
	       && $campos[$numero_campo]!="0")
	    {
			$campos[$numero_campo]="0";   
	    }//fin if
		
	}//if si existe campo
	
	//campo 173 aka 96
	$numero_campo=173;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 174 aka 97
	$numero_campo=174;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
		
	}//if si existe campo
	
	
	//campo 175 aka 98
	$numero_campo=175;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 176 aka 99
	$numero_campo=176;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="3"
		   && $campos[$numero_campo]!="0"
		   && $campos[$numero_campo]!="9"
	       )
	    {
			$campos[$numero_campo]="9";
	    }
	    
		
		
	}//if si existe campo
	
	//campo 177 aka 100
	$numero_campo=177;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if
		
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		)
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	//campo 178 aka 101
	$numero_campo=178;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if
		
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		)
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 179 aka 102
	$numero_campo=179;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if
		
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1800-01-01"
		    && trim($campos[$numero_campo])!="1799-01-01"
		)
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 180 aka 103
	$numero_campo=180;
	if(isset($campos[$numero_campo]))
	{
		
		if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>16)
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo
	
	//campo 181 aka 104
	$numero_campo=181;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day 
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1799-01-01"
		)
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	    
	}//if si existe campo
	
	//campo 182 aka 105
	$numero_campo=182;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_entidades_sector_salud WHERE codigo_entidad='".$campos[$numero_campo]."'; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    //campo obligatorio
	    if(count($resultado)==0
	       && $campos[$numero_campo]!="9"
	       && $campos[$numero_campo]!="8"
	       && $campos[$numero_campo]!="0"
	       )
	    {
			$campos[$numero_campo]="9";
	    }//fin if
		
	}//if si existe campo
	
	//campo 183 aka 106
	$numero_campo=183;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
		if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
		{
			//correccion formato fecha al formato AAAA-MM-DD year-month-day 
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
		}//fin if
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
	    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
	    //campo obligatorio
	    if($es_fecha_calendario<0
	       && $excede_fecha_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && trim($campos[$numero_campo])!="1799-01-01"
		)
	    {
			$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 184 aka 107
	$numero_campo=184;
	
	if(isset($campos[$numero_campo]))
	{
	    $fix_ultimo_campo=preg_replace("/[^A-Za-z0-9:.\-\s+]/", "", trim($campos[$numero_campo]) );
	    
	    
	    if(trim($fix_ultimo_campo)!="1"
		   && trim($fix_ultimo_campo)!="2"
		   && trim($fix_ultimo_campo)!="3"
		   &&trim($fix_ultimo_campo)!="0"
		   )
	    {
		    $campos[$numero_campo]="0";
	    }//fin if
	    
	    
		
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarVIH


function reparacion_calidad_formato_vih(&$campos,
					$nlinea,
					&$consecutivo_errores,
					$array_tipo_validacion,
					$array_grupo_validacion,
					$array_detalle_validacion,
					$nombre_archivo_registrado,
					$fecha_de_corte,
					$cod_prestador,
					$cod_eapb,
					&$diccionario_identificacion,
					&$diccionario_identificacion_lineas,
					&$coneccionBD, $array_numero_campo_bd)
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();
	
	//CALCULO EDAD
	$fecha_nacimiento= explode("-",$campos[10]);
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
	//fin calculo de la edad
	//FIN CALCULO EDAD
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='04' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0401' ORDER BY numero_de_orden ";
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
	
	
	//campo 0 aka 0 prestador
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	//campo 1 aka 1 EAPB-EPS
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 2 aka 2 R??gimen 1=gioss_afiliados_eapb_rc,2=gioss_afiliados_regimen_subsidiado,4=gioss_afiliados_eapb_rp
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	//campo 3 aka 3
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 4 aka 4
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
		
		
		
		
	}//if si existe campo
	
	//campo 5 aka 5
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	
	//campo 6 aka 6
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	
	//campo 7 aka 7
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	
	//campo 8 aka 8 TI
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 9 aka 9 Numero identificacion Verifica duplicados con la linea duplicada
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 10 aka 10
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	//campo 11 aka 11
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//campo 12 aka 12
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
	    
		
	}//if si existe campo
	
	//campo 13 aka 13
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
	    
		
	}//if si existe campo
	
	//campo 14 aka 14
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 15 aka 15
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//campo 16 aka 16
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	//campo 17 aka 17
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_11=$campos[11];
	    if($campos[$numero_campo]!="3" && $campo_ant_11=="M")
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]=="3" && $campo_ant_11=="F")
	    {
		$campos[$numero_campo]="0";
	    }
	    
	}//if si existe campo
	
	//campo 18 aka 18
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	//campo 19 aka 19
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="1" && $edad_meses>18)
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 20 aka 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n17=$campos[17];
	    $campo_n18=$campos[18];
	    $campo_n19=$campos[19];
	    
	    $campo_n22=trim($campos[22]);
	    $c_22_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n22,"1900-12-31");
	    
	    if($campos[$numero_campo]!="0"
	       && $c_22_es_fecha_calendario<0)
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="2"
		&& ($campo_n17!="0" || $campo_n18!="0" || $campo_n19!="0")
		&& $campo_n22=="1800-01-01"
		)
	    {
		$campos[$numero_campo]="2";
	    }
	    else if(($campos[$numero_campo]=="0" || $campos[$numero_campo]=="2")
		&& ($campo_n17!="0" || $campo_n18!="0" || $campo_n19!="0")
		&& $campo_n22=="1799-01-01"
		)
	    {
		$campos[$numero_campo]="3";
	    }
		
	}//if si existe campo
	
	//campo 21 aka 21
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="1799-01-01" && $edad<1)
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1800-01-01" && $edad>=1)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    
	}//if si existe campo
	
	//campo 22 aka 22
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n22=trim($campos[22]);
	    $c22_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n22,"1900-12-31");
				    
	    $campo_n21=trim($campos[21]);
	    $c21_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n21,"1900-12-31");
	    $es_c22_menor_a_c21=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$campo_n21);
	    
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1799-01-01"
	       && ($campo_ant_20=="1" || $campo_ant_20=="3" || $campo_ant_20=="4" )
	       )
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if($campos[$numero_campo]!="1800-01-01" && $campo_ant_20=="2")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	    else if($es_c22_menor_a_c21>0
	       && $c22_es_fecha_calendario<0
	       && $c21_es_fecha_calendario<0
	       )
	    {
		$array_cf=explode("-",$campo_n21);
		if(checkdate($array_cf[1],$array_cf[2],$array_cf[0]))
		{
		    $fecha = date_create(trim($campo_n21));
		    //date sub resta, por lo cual al poner un dia negativo suma
		    date_sub($fecha, date_interval_create_from_date_string('-5 days'));
		    $date_resul="".date_format($fecha, 'Y-m-d');
		    $campos[$numero_campo]=$date_resul;
		    
		    //revisa que no exceda la fecha de corte
		    $es_fecha_calendario=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"1900-12-31");
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    //campo obligatorio
		    if($es_fecha_calendario<0
		       && $excede_fecha_corte<0
		       )
		    {
			$campos[$numero_campo]=$fecha_de_corte;
		    }
		}//fin if
	    }
	    
	}//if si existe campo
	
	//campo 23 aka 23
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_11=$campos[11];
	    $campo_ant_21=$campos[21];
	    $campo_ant_17=$campos[17];
	    $campo_ant_18=$campos[18];
	    
	    $c21r_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_21,"1900-12-31");
	    
	    if($campos[$numero_campo]!="10" && ($campo_ant_21=="1799-01-01" || $campo_ant_21=="1800-01-01"))
	    {
		$campos[$numero_campo]="10";
	    }
	    else if($campos[$numero_campo]!="3"
	       && $campo_ant_11=="F"
	       && $campo_ant_17!=0
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]!="4"
	       && $campo_ant_18!=0
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if($campos[$numero_campo]!="8"
	       && $edad_meses<18
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="8";
	    }
	    else if($campos[$numero_campo]=="3"
	       && $campo_ant_11=="M"
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="4"
	       && $campo_ant_18==0
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="8"
	       && $edad_meses>=18
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="10"
	       && $c21r_es_fecha_calendario<0
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    
	}//if si existe campo
	
	//campo 24 aka 24
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="1";
	    }
		
	}//if si existe campo
	
	//campo 25 aka 25
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    
	    $campo_n22=trim($campos[22]);
	    $c22_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n22,"1900-12-31");
	    $c22_es_fecha_calendario_mayor_2010=diferencia_dias_entre_fechas($campo_n22,"2010-12-31");
			
	    
	    
	    
	    $campo_n24=intval($campos[24]);	
	    if($campos[$numero_campo]!="9"
	       && $campo_n22=="1799-01-01"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="8"
		    && $c22_es_fecha_calendario<0
		    && $c22_es_fecha_calendario_mayor_2010>0
		    )
	    {
		$campos[$numero_campo]="8";
	    }
	    else if($campos[$numero_campo]=="8"
		    && $c22_es_fecha_calendario<0
		    && $c22_es_fecha_calendario_mayor_2010<0
		    && $campo_n24==1
		    )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="0"
		    && $c22_es_fecha_calendario<0
		    && $c22_es_fecha_calendario_mayor_2010<0
		    && ($campo_n24==5 || $campo_n24==6)
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	    
	}//if si existe campo
	
	//campo 26 aka 26
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 27 aka 27
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_27_1=$campos[28];
	    
	    if($campos[$numero_campo]!="10" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="10";
	    }
	    else if($campos[$numero_campo]=="11" && $edad>=13 && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="12";
	    }	    
	    else if($campos[$numero_campo]!="11" && $edad<13)
	    {
		$campos[$numero_campo]="11";
	    }	    
	    else  if($campos[$numero_campo]=="10" && $campo_ant_20=="0" && $edad<13)
	    {
		$campos[$numero_campo]="11";
	    }
	    else  if($campos[$numero_campo]=="10" && $campo_ant_20=="0" && $edad>=13)
	    {
		$campos[$numero_campo]="12";
	    }
	}//if si existe campo
	
	//campo 28 aka 27.1
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_27=$campos[27];
	    
	    if($campos[$numero_campo]!="13"
	       && $campo_ant_20!="0"
	       )
	    {
		$campos[$numero_campo]="13";
	    }
	    else  if($campos[$numero_campo]=="13"
		     && $campo_ant_20=="0"
		     && $edad<13
		     )
	    {
		$campos[$numero_campo]="15";
	    }
	    else  if($campos[$numero_campo]=="13"
		     && $campo_ant_20=="0"
		     && $edad>=13
		     )
	    {
		$campos[$numero_campo]="14";
	    }
	    else  if($campos[$numero_campo]=="14"
		     && $campo_ant_20=="0"
		     && $edad<13
		     )
	    {
		$campos[$numero_campo]="15";
	    }
	    else  if($campos[$numero_campo]!="14"
		     && $campo_ant_20=="0"
		     && $edad>=13
		     )
	    {
		$campos[$numero_campo]="14";
	    }
	    
	    
	}//if si existe campo
	
	
	//campo 29 aka 28
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="99005" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="99005";
	    }
	    else if(($campos[$numero_campo]=="99005") && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="99004";
	    }
	}//if si existe campo
	
	
	//campo 30 aka 29
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="99005" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="99005";
	    }
	    else if(($campos[$numero_campo]=="99005") && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="99004";
	    }
	}//if si existe campo
	
	//campo 31 aka 30
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1800-01-01" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	    /*
	    else if($campos[$numero_campo]=="1800-01-01" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    */
	    
	}//if si existe campo
	
	
	//campo 32 aka 31
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="99005" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="99005";
	    }
	    else if(($campos[$numero_campo]=="99005") && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="99004";
	    }
	}//if si existe campo
	
	
	//campo 33 aka 32
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="99005" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="99005";
	    }
	    else if(($campos[$numero_campo]=="99005") && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="99004";
	    }
	}//if si existe campo
	
	
	//campo 34 aka 33
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="5";
	    }
	    else if($campos[$numero_campo]!="3" && $campo_n30=="1788-01-01")
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]=="3" && $campo_n30!="1788-01-01")
	    {
		$campos[$numero_campo]="5";
	    }
		
	}//if si existe campo
	
	
	//campo 35 aka 34
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    if($campos[$numero_campo]!="10"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="10"
		    && $campo_n30=="1800-01-01"
		    && $campo_ant_20!="0"
		    )
	    {
		$campos[$numero_campo]="10";
	    }
	    else if($campos[$numero_campo]=="10"
		    && $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="7";
	    }
	    else if($campos[$numero_campo]!="10"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="8";
	    }
	    else if($campos[$numero_campo]!="10"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="6";
	    }
	    
	}//if si existe campo
	
	//calculo fecha campo 30 contra year 2000-01-01
	$campo_n30=trim($campos[31]);
	$fecha_campo_temp_30=$campo_n30;
	$array_fecha_30=explode("-",$campo_n30);
	
	$verificar_fecha_para_date_diff=true;
	
	if(count($array_fecha_30)==3)
	{
		if(!ctype_digit($array_fecha_30[0])
		   || !ctype_digit($array_fecha_30[1]) || !ctype_digit($array_fecha_30[2])
		   || !checkdate(intval($array_fecha_30[1]),intval($array_fecha_30[2]),intval($array_fecha_30[0])) )
		{
			//$fecha_campo_temp="0000-00-00";
			$verificar_fecha_para_date_diff=false;
		}
	}
	else
	{
		$verificar_fecha_para_date_diff=false;	
	}
	
	$verificador_fecha_c30=0;
	if($verificar_fecha_para_date_diff==true)
	{
		$date_campo_30=date($fecha_campo_temp_30);
		$date_2000_01_01=date("2000-01-01");
		$fecha_campo_30_format=new DateTime($date_campo_30);
		$fecha_2000_01_01_format=new DateTime($date_2000_01_01);		
		try
		{
		$interval = date_diff($fecha_campo_30_format,$fecha_2000_01_01_format);
		$verificador_fecha_c30= (float)$interval->format("%r%a");
		}
		catch(Exception $e)
		{}
	}//fin if funcion date diff
	else
	{
		$verificador_fecha_c30=-1;
	}
	//fin calculo fecha campo 30 contra year 2000-01-01
			
	//campo 36 aka 35
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1799-01-01"
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	    
	    
	}//if si existe campo
	
	
	//campo 37 aka 36
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	    
	}//if si existe campo
	
	
	//campo 38 aka 37
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	    
	}//if si existe campo
	
	//campo 39 aka 38
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	//campo 40 aka 39
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_18=$campos[18];
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="0"
	       && $campo_ant_18=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	//campo 41 aka 40
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	//campo 42 aka 41
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	//campo 43 aka 42
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{    
	    $campo_ant_20=$campos[20];
	    $campo_ant_17=$campos[17];
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="0"
	       && $campo_ant_17=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	    
	    
	}//if si existe campo
	
	
	//campo 44 aka 43
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    $campo_n30=trim($campos[31]);
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");			
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $campos[$numero_campo]!="2"
	       && $campo_n30=="1800-01-01"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $c30_es_fecha_calendario<0
			&& $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if((intval($campos[$numero_campo])==9 || intval($campos[$numero_campo])==2)
			&& $campo_n30=="1788-01-01"
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	
	//campo 45 aka 44.1
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 46 aka 44.2
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 47 aka 44.3
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 48 aka 44.4
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 49 aka 44.5
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 50 aka 44.6
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 51 aka 44.7
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	
	//campo 52 aka 44.8
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
		
	
	//campo 53 aka 44.9
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 54 aka 44.10
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 55 aka 44.11
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 56 aka 44.12
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 57 aka 44.13
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo

	//campo 58 aka 44.14
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	

	//campo 59 aka 44.15
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 60 aka 44.16
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();
	
	//campo 61 aka 44.17
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="0" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 62 aka 44.18
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="0" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 63 aka 44.19
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="0" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 64 aka 44.20
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    $campo_n30=trim($campos[31]);
	    if($campos[$numero_campo]!="0" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo	
	
	//campo 65 aka 45
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2006_01_01=diferencia_dias_entre_fechas($campo_n30,"2006-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30=="1788-01-01")
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2006_01_01>0
		    )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2006_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	}//if si existe campo	
	
	//campo 66 aka 46
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="0" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]=="0" && $campo_n30=="1788-01-01")
	    {
		$campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]=="0"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]=="0"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	}//if si existe campo	
	
	//campo 67 aka 47
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="97" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="97";
	    }
	    else if($campos[$numero_campo]=="97" && $campo_n30=="1788-01-01")
	    {
		$campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]=="97"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2000_01_01>0
		    )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]=="97"
		    && $c30_es_fecha_calendario<0
		    && $c30_vs_2000_01_01<=0
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	    
	}//if si existe campo	
	
	//campo 68 aka 48
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    $campo_n30=trim($campos[31]);
	    $campo_n17=trim($campos[17]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="9" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]=="3"
		    && $campo_n17=="0"
		    && $campo_ant_20=="0"
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]=="3"
		    && $campo_n17=="0"
		    && $campo_ant_20!="0"
		    )
	    {
		$campos[$numero_campo]="9";
	    }
	}//if si existe campo

	//campo 69 aka 49
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    $campo_n30=trim($campos[31]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="1799-01-01" && $campo_n30=="1800-01-01")
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if($campos[$numero_campo]=="1799-01-01" && $campo_n30!="1800-01-01")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo

	//campo 70 aka 50
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    $campo_n49=trim($campos[69]);
			
	    $c49_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n49,"1900-12-31");
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_20!="0"
	      )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="5"
		    && $campo_ant_20=="0"
	       && ( $campo_n49=="1799-01-01")
	       )
	    {
		$campos[$numero_campo]="5";
	    }
	    else if($campos[$numero_campo]!="4"
		    && $campo_ant_20=="0"
	       && ( $campo_n49=="1777-01-01")
	       )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if($campos[$numero_campo]!="1"
		    && $campos[$numero_campo]!="2"
		    && $campos[$numero_campo]!="3"
		    && $campos[$numero_campo]!="6"
		    && $campo_ant_20=="0"
		    &&  $c49_es_fecha_calendario<0
		    )
	    {
		$campos[$numero_campo]="6";
	    }
	}//if si existe campo
	
	//campo 71 aka 51
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    $campo_n30=trim($campos[31]);
	    $campo_n50=trim($campos[70]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="99"
	       && ($campo_n30=="1800-01-01")
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]=="99"
		    && ($campo_n30!="1800-01-01")
		    &&  $campo_n50=="2"
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]=="99"
		    && ($campo_n30!="1800-01-01")
		    &&  $campo_n50!="2"
		    )
	    {
		$campos[$numero_campo]="0";
	    }	
	}//if si existe campo
	
	//campo 72 aka 52
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_34=$campos[35];
	    
	    $campo_n30=trim($campos[31]);
	    $campo_n50=trim($campos[70]);
	    
	    $c30_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n30,"1900-12-31");
	    $c30_vs_2000_01_01=diferencia_dias_entre_fechas($campo_n30,"2000-01-01");
	    
	    if($campos[$numero_campo]!="99"
	       && ($campo_n30=="1800-01-01")
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]=="99"
		    && ($campo_n30!="1800-01-01")
		    &&  ($campo_n50=="1"
			 || $campo_n50=="2"
			 || $campo_n50=="3"
			 || $campo_n50=="6"
			 )
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]=="99"
		    && ($campo_n30!="1800-01-01")
		    &&  $campo_n50=="4"
		    )
	    {
		$campos[$numero_campo]="0";
	    }			
		
	}//if si existe campo
	
	//campo 73 aka 53.1
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 74 aka 53.2
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 75 aka 53.3
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 76 aka 53.4
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 77 aka 53.5
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 78 aka 53.6
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 79 aka 53.7
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	
	}//if si existe campo
	
	//campo 80 aka 53.8
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 81 aka 53.9
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 82 aka 53.10
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 83 aka 53.11
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 84 aka 53.12
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 85 aka 53.13
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 86 aka 53.14
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 87 aka 53.15
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 88 aka 53.16
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 89 aka 53.17
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 90 aka 53.18
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }		
	}//if si existe campo
	
	//campo 91 aka 53.19
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 92 aka 53.20
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]=="1" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }		
	}//if si existe campo
	
	//campo 93 aka 54
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica calidad
		
		
	}//if si existe campo
	
	
	//campo 94 aka 55
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1799-01-01" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
		
	}//if si existe campo
	
	//campo 95 aka 56
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica calidad
		
		
	}//if si existe campo
	
	//campo 96 aka 57
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="6";
	    }
	
	}//if si existe campo
	
	//campo 97 aka 58
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 98 aka 59
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	//campo 99 aka 60
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_59=$campos[98];
	    //$campo_ant_50=$campos[70];
	    $campo_ant_20=$campos[20];
	    
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_20=="0"
		    && $campo_ant_59=="0"
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_20=="0"
		    && $campo_ant_59=="1"
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]!="9"
		    && $campo_ant_20=="0"
		    && $campo_ant_59=="2"
		    )
	    {
		$campos[$numero_campo]="9";
	    }
	    
		
	}//if si existe campo
	
	//campo 100 aka 61
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 101 aka 62
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	     else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="4";
	    }
	}//if si existe campo
	
	//campo 102 aka 63
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
	}//if si existe campo
	
	//campo 103 aka 64
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
	}//if si existe campo
	
	//campo 104 aka 65
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
		
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
		
	}//if si existe campo
	
	//campo 105 aka 66
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
	}//if si existe campo
	
	//campo 106 aka 67
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
		
	}//if si existe campo
	
	//campo 107 aka 68
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
		
	}//if si existe campo
	
	//campo 108 aka 69
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
	}//if si existe campo
	
	//campo 109 aka 70
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
	}//if si existe campo
	
	//campo 110 aka 71
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
		
	}//if si existe campo
	
	//campo 111 aka 72
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    

		
	}//if si existe campo
	
	//campo 112 aka 73
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	    
		
	}//if si existe campo
	
	//campo 113 aka 74
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
		
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="3" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]=="3" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 114 aka 75
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1799-01-01" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if($campos[$numero_campo]=="1799-01-01" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 115 aka 76
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_62=$campos[101];
	    $campo_ant_20=$campos[20];
	    $campo_ant_75=$campos[114];
	    
	    $campo_n20=trim($campos[20]);
	    
	    if($campos[$numero_campo]!="99999" && $campo_n20!="0")
	    {
		$campos[$numero_campo]="99999";
	    }
	    else if($campos[$numero_campo]!="88888"
		    && $campo_n20=="0"
		    && $campo_ant_75=="1800-01-01"
		    )
	    {
		$campos[$numero_campo]="88888";
	    }
	    
	}//if si existe campo
	
	//campo 116 aka 77
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1799-01-01" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if($campos[$numero_campo]=="1799-01-01" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
		
	}//if si existe campo
	
	//campo 117 aka 78
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_77=$campos[116];
	    if($campos[$numero_campo]!="99999" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="99999";
	    }
	    else if($campos[$numero_campo]=="99999"
		    && $campo_ant_20=="0"
		    && $campo_ant_77=="1800-01-01"
		    )
	    {
		$campos[$numero_campo]="88888";
	    }
		
	}//if si existe campo
	
	//campo 118 aka 79
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="1799-01-01" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if($campos[$numero_campo]=="1799-01-01" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 119 aka 80
	$numero_campo=119;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n79=$campos[118];	    
	    $campo_n20=trim($campos[20]);
	    $c79_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n79,"1900-12-31");
	    
	    if($campos[$numero_campo]!="9"
		&& $campo_n20!="0"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
		&& ($campo_n79=="1799-01-01" || $campo_n79=="1800-01-01")
		&& $campo_n20=="0"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $c79_es_fecha_calendario<0
		    && $campo_n20=="0"
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 120 aka 81
	$numero_campo=120;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_n81=trim($campos[120]);
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_20!="0"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
	       && $campo_ant_20=="0"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	//campo 121 aka 81.1
	$numero_campo=121;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_n81=trim($campos[120]);
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_20!="0"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="1"
		    && $campos[$numero_campo]!="2"
		    && $campos[$numero_campo]!="3"
	       && $campo_ant_20=="0"
	       && $campo_n81=="1"
	       )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]!="4"
	       && $campo_ant_20=="0"
	       && $campo_n81=="2"
	       )
	    {
		$campos[$numero_campo]="4";
	    }
	    else if($campos[$numero_campo]!="0"
	       && $campo_ant_20=="0"
	       && $campo_n81=="0"
	       )
	    {
		$campos[$numero_campo]="0";
	    }

		
	}//if si existe campo
	
	//campo 122 aka 82
	$numero_campo=122;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    $campo_ant_20=$campos[20];
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_20!="0"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9" && $edad<10)
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && $campo_ant_19=="1"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_20=="0"
		    && $campo_ant_19!="1"
		    && $edad>=10
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	    
	}//if si existe campo
	
	
	//campo 123 aka 83
	$numero_campo=123;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="3" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]=="3" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="9";
	    }
		
		
	}//if si existe campo
	
	
	//campo 124 aka 84
	$numero_campo=124;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }

		
	}//if si existe campo
	
	
	//campo 125 aka 85
	$numero_campo=125;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    if($campos[$numero_campo]!="9"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && $campo_ant_19=="0"
	       && $edad_meses<18
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_19=="1"
		    && $edad_meses<18
		    )
	    {
		$campos[$numero_campo]="0";
	    }		
		
	}//if si existe campo
	
	
	//campo 126 aka 86
	$numero_campo=126;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    if($campos[$numero_campo]!="9"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && $campo_ant_19=="0"
	       && $edad_meses<18
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_19=="1"
		    && $edad_meses<18
		    )
	    {
		$campos[$numero_campo]="0";
	    }		
		
	}//if si existe campo
	
	
	//campo 127 aka 87
	$numero_campo=127;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    if($campos[$numero_campo]!="99"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]!="99"
	       && $campo_ant_19=="0"
	       && $edad_meses<18
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]=="99"
		    && $campo_ant_19=="1"
		    && $edad_meses<18
		    )
	    {
		$campos[$numero_campo]="0";
	    }		
		
	}//if si existe campo
	
	
	//campo 128 aka 88
	$numero_campo=128;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    if($campos[$numero_campo]!="9"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && $campo_ant_19=="0"
	       && $edad_meses<18
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_19=="1"
		    && $edad_meses<18
		    )
	    {
		$campos[$numero_campo]="0";
	    }		
		
	}//if si existe campo
	
	
	//campo 129 aka 89
	$numero_campo=129;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 130 aka 90
	$numero_campo=130;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_89=trim($campos[129]);
	    if($campos[$numero_campo]!="1799-01-01"
	       && $campo_ant_20!="0"
	       )
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if($campos[$numero_campo]=="1799-01-01"
		    && $campo_ant_20=="0"
		    && $campo_ant_89!="4" && $campo_ant_89!="9"
		    )
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
		
	}//if si existe campo
	
	
	//campo 131 aka 91.1
	$numero_campo=131;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 132 aka 91.2
	$numero_campo=132;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 133 aka 91.3
	$numero_campo=133;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 134 aka 91.4
	$numero_campo=134;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 135 aka 91.5
	$numero_campo=135;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 136 aka 91.6
	$numero_campo=136;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 137 aka 91.7
	$numero_campo=137;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 138 aka 91.8
	$numero_campo=138;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 139 aka 91.9
	$numero_campo=139;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 140 aka 91.10
	$numero_campo=140;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 141 aka 91.11
	$numero_campo=141;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 142 aka 91.12
	$numero_campo=142;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 143 aka 91.13
	$numero_campo=143;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 144 aka 91.14
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 145 aka 91.15
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 146 aka 91.16
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    if($campos[$numero_campo]!="9" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9" && $campo_ant_20=="0")
	    {
		$campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	//campo 147 aka 91.17
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_90=trim($campos[130]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 148 aka 91.18
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_90=trim($campos[130]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//campo 149 aka 91.19
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_90=trim($campos[130]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }	
	}//if si existe campo
	
	//campo 150 aka 91.20
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[20];
	    $campo_ant_90=trim($campos[130]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_20!="0")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 151 aka 92
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    $campo_ant_20=$campos[20];
	    
	    $campo_92_1=trim($campos[152]);
	    $campo_92_2=trim($campos[153]);
	    $campo_92_3=trim($campos[154]);
	    $campo_92_4=trim($campos[155]);
	    $campo_92_5=trim($campos[156]);
	    
	    if($campos[$numero_campo]!="2"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="2"
		    && $campo_ant_19=="0"
		    )
	    {
		$campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="2"
		    && $campo_ant_19=="1"
		    && (
			$campo_92_1=="1"
			|| $campo_92_2=="1"
			|| $campo_92_3=="1"
			|| $campo_92_4=="1"
			|| $campo_92_5=="1"
			)
		    )
	    {
		$campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]=="2"
		    && $campo_ant_19=="1"
		    && (
			$campo_92_1!="1"
			&& $campo_92_2!="1"
			&& $campo_92_3!="1"
			&& $campo_92_4!="1"
			&& $campo_92_5!="1"
			)
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 152 aka 92.1
	$numero_campo=152;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_19=$campos[19];
	    $campo_ant_92=$campos[151];
	    
	    if($campos[$numero_campo]!="0"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="0"
	       && $campo_ant_19=="0"
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	    
	}//if si existe campo
	
	//campo 153 aka 92.2
	$numero_campo=153;
	if(isset($campos[$numero_campo]))
	{
	    //calidad
	    $campo_ant_19=$campos[19];
	    $campo_ant_92=$campos[151];
	    if($campos[$numero_campo]!="0"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="0"
	       && $campo_ant_19=="0"
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 154 aka 92.3
	$numero_campo=154;
	if(isset($campos[$numero_campo]))
	{
	    //calidad
	    $campo_ant_19=$campos[19];
	    $campo_ant_92=$campos[151];
	    if($campos[$numero_campo]!="0"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="0"
	       && $campo_ant_19=="0"
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 155 aka 92.4
	$numero_campo=155;
	if(isset($campos[$numero_campo]))
	{
	    //calidad
	    $campo_ant_19=$campos[19];
	    $campo_ant_92=$campos[151];
	    if($campos[$numero_campo]!="0"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="0"
	       && $campo_ant_19=="0"
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 156 aka 92.5
	$numero_campo=156;
	if(isset($campos[$numero_campo]))
	{
	    //calidad
	    $campo_ant_19=$campos[19];
	    $campo_ant_92=$campos[151];
	    if($campos[$numero_campo]!="0"
	       && $edad_meses>=18
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	    else if($campos[$numero_campo]!="0"
	       && $campo_ant_19=="0"
	       )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 157 aka 93
	$numero_campo=157;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    
	    $campo_95_1=trim($campos[159]);
	    $campo_95_2=trim($campos[160]);
	    $campo_95_3=trim($campos[161]);
	    $campo_95_4=trim($campos[162]);
	    $campo_95_5=trim($campos[163]);
	    $campo_95_6=trim($campos[164]);
	    $campo_95_7=trim($campos[165]);
	    $campo_95_8=trim($campos[166]);
	    $campo_95_9=trim($campos[167]);

	    
	    if($campos[$numero_campo]!="9" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_18=="1"
		    && (
			$campo_95_1=="1"
			|| $campo_95_2=="1"
			|| $campo_95_3=="1"
			|| $campo_95_4=="1"
			|| $campo_95_5=="1"
			|| $campo_95_6=="1"
			|| $campo_95_7=="1"
			|| $campo_95_8=="1"
			|| $campo_95_9=="1"
			)
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	    else if($campos[$numero_campo]=="9"
		    && $campo_ant_18=="1"
		    && (
			$campo_95_1=="0"
			&& $campo_95_2=="0"
			&& $campo_95_3=="0"
			&& $campo_95_4=="0"
			&& $campo_95_5=="0"
			&& $campo_95_6=="0"
			&& $campo_95_7=="0"
			&& $campo_95_8=="0"
			&& $campo_95_9=="0"
			)
		    )
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 158 aka 94
	$numero_campo=158;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];	    
	    $campo_n93=trim($campos[157]);
	    if($campos[$numero_campo]!="1800-01-01"
	       && $campo_ant_18!="1"
	       )
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	    else if($campos[$numero_campo]=="1800-01-01"
		    && ($campo_n93=="0" || $campo_n93=="1" || $campo_n93=="2")
		    && $campo_ant_18=="1"
		    )
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	}//if si existe campo
	
	//campo 159 aka 95.1
	$numero_campo=159;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 160 aka 95.2
	$numero_campo=160;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 161 aka 95.3
	$numero_campo=161;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 162 aka 95.4
	$numero_campo=162;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }		
	}//if si existe campo
	
	//campo 163 aka 95.5
	$numero_campo=163;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 164 aka 95.6
	$numero_campo=164;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 165 aka 95.7
	$numero_campo=165;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 166 aka 95.8
	$numero_campo=166;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 167 aka 95.9
	$numero_campo=167;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 168 aka 95.10
	$numero_campo=168;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 169 aka 95.11
	$numero_campo=169;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 170 aka 95.12
	$numero_campo=170;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 171 aka 95.13
	$numero_campo=171;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 172 aka 95.14
	$numero_campo=172;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_18=$campos[18];
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') ; ";
		$resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
		if( count($resultado1)>0 && is_array($resultado1) )
		{
		    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
		    
		    if($resultado1[0]["codigo_cum"]!="")
	    	{
	    		$campos[$numero_campo]=$resultado1[0]["codigo_cum"];
	    	}//fin if
		}//fin if
		else if (count($resultado1)==0)
		{
		    //si no existe en el caso anterior
		    //busca si tiene un equivalente en la tabla de homologacion
		    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		    {
			$consulta2="";
			$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
			$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
			if( count($resultado2)>0 && is_array($resultado2))
			{
			    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
			}//fin if
		    }//fin if si entidad es fundacion valle del lili
		}//fin else if
	    }//fin if
	    //FIN CORRECCION CUM HOMOLOGADO
	    
	    //busca si tiene un equivalente en la tabla de homologacion
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$consulta2="";
		$consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE codigo_cfvl=trim('".$campos[$numero_campo]."') ; ";
		$resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
		if( count($resultado2)>0 && is_array($resultado2))
		{
		    $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);
		}//fin if
	    }//fin if si entidad es fundacion valle del lili
	    
	    if($campos[$numero_campo]!="0" && $campo_ant_18!="1")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	//campo 173 aka 96
	$numero_campo=173;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_17=$campos[17];
	    $campo_ant_11=$campos[11];
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_11=="M"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && ($campo_ant_17=="0")
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && ($campo_ant_17=="1" || $campo_ant_17=="2")
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	//campo 174 aka 97
	$numero_campo=174;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_17=$campos[17];
	    $campo_ant_11=$campos[11];
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_11=="M"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && ($campo_ant_17=="0")
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && ($campo_ant_17=="1" || $campo_ant_17=="2")
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	
	//campo 175 aka 98
	$numero_campo=175;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_17=$campos[17];
	    $campo_ant_11=$campos[11];
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_11=="M"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && ($campo_ant_17=="0")
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && ($campo_ant_17=="1" || $campo_ant_17=="2")
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	//campo 176 aka 99
	$numero_campo=176;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_17=$campos[17];
	    $campo_ant_11=$campos[11];
	    
	    if($campos[$numero_campo]!="9"
	       && $campo_ant_11=="M"
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]!="9"
	       && ($campo_ant_17=="0")
	       )
	    {
		$campos[$numero_campo]="9";
	    }
	    else if($campos[$numero_campo]=="9"
		    && ($campo_ant_17=="1" || $campo_ant_17=="2")
		    )
	    {
		$campos[$numero_campo]="3";
	    }
	    
	}//if si existe campo
	
	//campo 177 aka 100
	$numero_campo=177;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n96=intval($campos[173]);
	    $campo_n97=intval($campos[174]);
	    $campo_n98=intval($campos[175]);
	    $campo_n99=intval($campos[176]);
	    
	    $campo_n17=trim($campos[17]);
	    $campo_n11=trim($campos[11]);
	    
	    if(trim($campos[$numero_campo])!="1799-01-01"
		&& $campo_n11=="M"
		)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1799-01-01"
		&& $campo_n11=="F"
		&& $campo_n17=="0"
		)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1799-01-01"
		&& $campo_n11=="F"
		&& ($campo_n17=="1" || $campo_n17=="2")
		)
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 178 aka 101
	$numero_campo=178;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n96=intval($campos[173]);
	    $campo_n97=intval($campos[174]);
	    $campo_n98=intval($campos[175]);
	    $campo_n99=intval($campos[176]);
	    
	    $campo_n17=trim($campos[17]);
	    $campo_n11=trim($campos[11]);
	    
	    if(trim($campos[$numero_campo])!="1799-01-01"
		&& $campo_n11=="M"
		)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1799-01-01"
		&& $campo_n11=="F"
		&& $campo_n17=="0"
		)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1799-01-01"
		&& $campo_n11=="F"
		&& ($campo_n17=="1" || $campo_n17=="2")
		)
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 179 aka 102
	$numero_campo=179;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n96=intval($campos[173]);
	    $campo_n97=intval($campos[174]);
	    $campo_n98=intval($campos[175]);
	    $campo_n99=intval($campos[176]);
	    
	    $campo_n17=trim($campos[17]);
	    $campo_n11=trim($campos[11]);
	    
	    if(trim($campos[$numero_campo])!="1799-01-01"
		&& $campo_n11=="M"
		)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1799-01-01"
		&& $campo_n11=="F"
		&& $campo_n17=="0"
		)
	    {
		$campos[$numero_campo]="1799-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1799-01-01"
		&& $campo_n11=="F"
		&& ($campo_n17=="1" || $campo_n17=="2")
		)
	    {
		$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	//campo 180 aka 103
	$numero_campo=180;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n106=trim($campos[183]);
	    if($campos[$numero_campo]=="4"  && $campo_n106=="1799-01-01" )
	    {
		$campos[$numero_campo]="0";
	    }		
		
	}//if si existe campo
	
	//campo 181 aka 104
	$numero_campo=181;
	if(isset($campos[$numero_campo]))
	{
		
	    //no aplica calidad
		
	}//if si existe campo
	
	//campo 182 aka 105
	$numero_campo=182;
	if(isset($campos[$numero_campo]))
	{
		
	    //no aplica calidad
		
	}//if si existe campo
	
	//campo 183 aka 106
	$numero_campo=183;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica calidad
		
	}//if si existe campo
	
	//campo 184 aka 107
	$numero_campo=184;	
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_106=$campos[183];
	    if($campos[$numero_campo]!="0" && $campo_ant_106=="1799-01-01")
	    {
		$campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarVIH
?>