<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/conf_personalizada.php';

//recibe en dia mes year

function reparacion_campo_en_blanco_HF(&$campos,
					$cod_eapb,
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
	
	$campos[6]=corrector_formato_fecha($campos[6],$fecha_de_corte,true);
	
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

	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters="";
	    $campo_ver_characters=str_replace(array("-","."),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters=ctype_alnum($campo_ver_characters);
	    
	    $campos[$cont_campos]=str_replace(",",".",$campos[$cont_campos]);
	    
	    if($campo_ver_characters==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}		
		//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104099"])[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104099,$cadena_descripcion_inconsistencia ...".$campos[$cont_campos]." ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
		$consecutivo_errores++;
		
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO	
	
	//numero_orden 0 numero campo 1 
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
	    
			
	}//if si existe campo
	
	//numero_orden 1 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="NONE";
	    }//fin if   
		
	}//if si existe campo
	
	//numero_orden 2 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	
	//numero_orden 3 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="NOAP";
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden 4  numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
	

		
	}//if si existe campo
	
	
	//numero_orden 5 numero campo 6 Numero Id
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	
	//numero_orden 6 numero campo 7 fecha nacimiento
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
		
		
	}//if si existe campo
	
	//numero_orden 7 numero campo 8 sexo
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	
	//numero_orden 8  numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="9998";
		}//fin if 
	}//if si existe campo
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="N";
		}//fin if 
	}//if si existe campo

	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]=$cod_eapb;
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="6";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="61";
		}//fin if 
	}//if si existe campo
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{	
	}//if si existe campo
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
		if (trim($campos[$numero_campo]) == "") {
			$campos[$numero_campo] = "0000000000";
		}
	}//if si existe campo
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="3";
	    }//fin if
	}//if si existe campo

	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="4";
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9998";
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden 19  numero campo 20 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="4";
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden 20  numero campo 21 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 21  numero campo 22
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    
	}//if si existe campo
	
	//numero_orden 22  numero campo 23
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 23  numero campo 24 
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 24  numero campo 25 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 25  numero campo 26
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 26  numero campo 27 
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 27  numero campo 28 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 28  numero campo 29 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]='1845-01-01';
	    }//fin if
	}//if si existe campo
	
	//numero_orden 29  numero campo 30 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 30  numero campo 31
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 31  numero campo 32 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 32  numero campo 32.1
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9998";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 33  numero campo 32.2 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 34  numero campo 32.3 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="999999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 35  numero campo 32.4 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9998";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 36  numero campo 33 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 37  numero campo 34 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 38  numero campo 35 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 39  numero campo 36 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 40  numero campo 37 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 38 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 39 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 43  numero campo 40 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 42  numero campo 40.1 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="99";
		}//fin if
	}//if si existe campo
	
	//numero_orden 43  numero campo 40.2 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="99";
		}//fin if
	}//if si existe campo
	
	//numero_orden 44  numero campo 41
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 45  numero campo 42
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 46  numero campo 43 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 47  numero campo 44
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 48  numero campo 45
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 49  numero campo 46
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 50  numero campo 47.1
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 51  numero campo 47.2 
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 52  numero campo 47.3 
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 53  numero campo 48 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 54  numero campo 48.1
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 55  numero campo 48.2 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 56  numero campo 48.3 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 57  numero campo 48.4 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 58  numero campo 49
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 59  numero campo 49.1 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 60  numero campo 50
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 61  numero campo 51 
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 62  numero campo 52 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 63  numero campo 53
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 64  numero campo 54 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 65  numero campo 55 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 66  numero campo 55.1
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo

	//numero_orden 67  numero campo 56 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 56.1
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 69 numero campo 57
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 70  numero campo 57.1
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 71  numero campo 57.2
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 72 numero campo 57.3
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 73 numero campo 57.4
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 74 numero campo 57.5
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 75 numero campo 57.6
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 76 numero campo 57.7
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 77 numero campo 57.8 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 78 numero campo 57.9
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 79 numero campo 57.10
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 80 numero campo 57.11
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 81  numero campo 57.12
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{	    
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="NONE";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 82 numero campo 57.13
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 83  numero campo 57.14 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="NOAP";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 84  numero campo 58
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 59
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 86  numero campo 60 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 87  numero campo 61
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 88  numero campo 62
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 89  numero campo 63
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 90  numero campo 64
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	//numero_orden 91  numero campo 64.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="99";
	    }//fin if
	}//if si existe campo
	
	//numero_orden 92  numero campo 64.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }//fin if
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarHF

function reparacion_formato_HF(&$campos,
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
	
	$campos[6]=corrector_formato_fecha($campos[6],$fecha_de_corte,true);
	
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

	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters="";
	    $campo_ver_characters=str_replace(array("-","."),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters=ctype_alnum($campo_ver_characters);
	    
	    $campos[$cont_campos]=str_replace(",",".",$campos[$cont_campos]);
	    
	    if($campo_ver_characters==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}		
		//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104099"])[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104099,$cadena_descripcion_inconsistencia ...".$campos[$cont_campos]." ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
		$consecutivo_errores++;
		
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//numero_orden 0 numero campo 1 
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
		
		
				
	}//if si existe campo
	
	//numero_orden 1 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );
		
		
	}//if si existe campo
	
	//numero_orden 2 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );
		
		
	}//if si existe campo
	
	
	//numero_orden 3 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );
		
		
	}//if si existe campo
	
	
	//numero_orden 4  numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );

		
	}//if si existe campo
	
	
	//numero_orden 5 numero campo 6 Numero Id
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
		
		
	}//if si existe campo
	
	
	//numero_orden 6 numero campo 7 fecha nacimiento
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
			
		//se corrigio desde el comienzo antes de calcular la edad
		
	}//if si existe campo
	
	//numero_orden 7 numero campo 8 sexo
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 8  numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]));
		}//fin if 
	}//if si existe campo
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo

	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9\-]/", "", trim($campos[$numero_campo]));

		if (trim($campos[$numero_campo]) == "") {
			$campos[$numero_campo] = "0000000000";
		}
	}//if si existe campo
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		$array_fecha=explode("-",$campos[$numero_campo]);
        if(count($array_fecha)!=3 || !checkdate(intval($array_fecha[1]),intval($array_fecha[2]),intval($array_fecha[0])))
        {
            //correccion formato fecha al formato AAAA-MM-DD year-month-day 1799-01-01
            $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-2);
        }
	}//if si existe campo
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo

	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 19  numero campo 20 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 20  numero campo 21 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	//numero_orden 21  numero campo 22
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));

		if(strlen($campos[$numero_campo])==11
           && (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8"))
        {
            $campos[$numero_campo]="0".$campos[$numero_campo];
        }
        else if(strlen($campos[$numero_campo])==10)
        {
            $campos[$numero_campo]=$campos[$numero_campo]."01";
        }

	}//if si existe campo
	
	//numero_orden 22  numero campo 23
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 23  numero campo 24 
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 24  numero campo 25 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 25  numero campo 26
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 26  numero campo 27 
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 27  numero campo 28 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 28  numero campo 29 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	//numero_orden 29  numero campo 30 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 30  numero campo 31
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 31  numero campo 32 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=str_replace(",", ".", trim($campos[$numero_campo]));
		$campos[$numero_campo]=preg_replace("/[^0-9\.]/", "", trim($campos[$numero_campo]));

		if (is_numeric($campos[$numero_campo])) {
			if (!ctype_digit($campos[$numero_campo])) {
				$campos[$numero_campo] = round(floatval($campos[$numero_campo]), 1, PHP_ROUND_HALF_UP);
			}
		}
	}//if si existe campo
	
	//numero_orden 32  numero campo 32.1
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 33  numero campo 32.2 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 34  numero campo 32.3 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 35  numero campo 32.4 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 36  numero campo 33 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 37  numero campo 34 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 38  numero campo 35 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 39  numero campo 36 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 40  numero campo 37 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 41  numero campo 38 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 42  numero campo 39 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo
	
	//numero_orden 43  numero campo 40 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 42  numero campo 40.1 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 43  numero campo 40.2 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 44  numero campo 41
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 45  numero campo 42
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 46  numero campo 43 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 47  numero campo 44
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 48  numero campo 45
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 49  numero campo 46
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 50  numero campo 47.1
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 51  numero campo 47.2 
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 52  numero campo 47.3 
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 53  numero campo 48 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 54  numero campo 48.1
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo] = corrector_formato_fecha($campos[$numero_campo], $fecha_de_corte, false, 0);
	}//if si existe campo
	
	//numero_orden 55  numero campo 48.2 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 56  numero campo 48.3 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 57  numero campo 48.4 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 58  numero campo 49
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 59  numero campo 49.1 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 60  numero campo 50
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 61  numero campo 51 
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 62  numero campo 52 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 63  numero campo 53
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 64  numero campo 54 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 65  numero campo 55 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 66  numero campo 55.1
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])));
	}//if si existe campo

	//numero_orden 67  numero campo 56 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 68  numero campo 56.1
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 69 numero campo 57
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 70  numero campo 57.1
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 71  numero campo 57.2
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 72 numero campo 57.3
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 73 numero campo 57.4
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 74 numero campo 57.5
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 75 numero campo 57.6
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	
	//numero_orden 76 numero campo 57.7
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	
	//numero_orden 77 numero campo 57.8 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 78 numero campo 57.9
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 79 numero campo 57.10
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 80 numero campo 57.11
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 81  numero campo 57.12
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 82 numero campo 57.13
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 83  numero campo 57.14 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 84  numero campo 58
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 85  numero campo 59
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 86  numero campo 60 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 87  numero campo 61
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 88  numero campo 62
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 89  numero campo 63
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 90  numero campo 64
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 91  numero campo 64.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^0-9]/", "", trim($campos[$numero_campo]));
	}//if si existe campo
	
	//numero_orden 92  numero campo 64.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo], $fecha_de_corte, false, 0);
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarHF

function reparacion_valor_permitido_HF(&$campos,
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
	
	$campos[6]=corrector_formato_fecha($campos[6],$fecha_de_corte,true);
	
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
	
	//FIN CALCULO EDAD

	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters="";
	    $campo_ver_characters=str_replace(array("-","."),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters=ctype_alnum($campo_ver_characters);
	    
	    $campos[$cont_campos]=str_replace(",",".",$campos[$cont_campos]);
	    
	    if($campo_ver_characters==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}		
		//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104099"])[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104099,$cadena_descripcion_inconsistencia ...".$campos[$cont_campos]." ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
		$consecutivo_errores++;
		
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//numero_orden 0 numero campo 1 
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 1 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 2 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 3 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 4  numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 5 numero campo 6 Numero Id
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 6 numero campo 7 fecha nacimiento
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 7 numero campo 8 sexo
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 8  numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		$consulta="";
        $consulta.="SELECT * FROM gioss_ciou WHERE codigo_ciou_08='".$campos[$numero_campo]."' OR codigo_ciou_88='".$campos[$numero_campo]."'; ";
        $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
        if( count($resultado)==0  && ($campos[$numero_campo]!="9998" && $campos[$numero_campo]!="9999") )
        {
            $campos[$numero_campo]="9999";
        }//fin if
	}//if si existe campo
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo

	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		if( intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>6)
		{
			$campos[$numero_campo]="6";
		} else {
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
	  if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>16) && (intval($campos[$numero_campo])<31 || intval($campos[$numero_campo])>39) && (intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>63) )
        {
            $campos[$numero_campo]="61";
        }//fin if
        else if(strlen($campos[$numero_campo])>2)
        {
            $campos[$numero_campo]=intval($campos[$numero_campo]);
        }
	}//if si existe campo
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
		if(strlen($campos[$numero_campo])==4)
	    {
			$campos[$numero_campo]="0".$campos[$numero_campo];
	    }
	    else if(strlen($campos[$numero_campo])>5)
	    {
			$campos[$numero_campo]=substr($campos[$numero_campo],0,5);
	    }
	}//if si existe campo
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
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
            
            $fecha_de_nacmiento = trim($campos[6]);
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
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
		if (intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1 
			&& intval($campos[$numero_campo]) != 3) {
			$campos[$numero_campo] == "3";
		}
	}//if si existe campo

	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
		if (intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 4) {
			$campos[$numero_campo] == "4";
		}
	}//if si existe campo
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		if (intval($campos[$numero_campo]) != $edad) {
			$campos[$numero_campo] = $edad;
		}
	}//if si existe campo
	
	//numero_orden 19  numero campo 20 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		if (intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 4) {
			$campos[$numero_campo] == "4";
		}
	}//if si existe campo
	
	//numero_orden 20  numero campo 21 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
		$excede_fecha_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
        $es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
        
        if($es_campo_actual_fecha_calendario<0
           && $excede_fecha_corte<0
           )
        {
            $campos[$numero_campo]=$fecha_de_corte;
        }
        else if($es_campo_actual_fecha_calendario>0
           		&& $campos[$numero_campo] != "1800-01-01"
           		&& $campos[$numero_campo] != "1811-01-01")
        {
            $campos[$numero_campo] = "1800-01-01";
        }//fin else
	}//if si existe campo
	
	//numero_orden 21  numero campo 22
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
		$consulta="";
        $consulta.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."' ; ";
        $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
        
        if( count($resultado)==0 || $campos[$numero_campo]=="")
        {
            if($cod_prestador!="AGRUP_EAPB"){
            	$campos[$numero_campo]=$cod_prestador;
            }
        }
	}//if si existe campo
	
	//numero_orden 22  numero campo 23
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
		if ( (intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 11) ) {
			
		}
	}//if si existe campo
	
	//numero_orden 23  numero campo 24 
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 3) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 24  numero campo 25 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 2) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 25  numero campo 26
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 24) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 26  numero campo 27 
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 5) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 27  numero campo 28 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 6) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 28  numero campo 29 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
		$excede_fecha_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
        $es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
        
        if($es_campo_actual_fecha_calendario<0
           && $excede_fecha_corte<0
           )
        {
            $campos[$numero_campo]=$fecha_de_corte;
        }
        else if($es_campo_actual_fecha_calendario>0
           		&& $campos[$numero_campo] != "1800-01-01"
           		&& $campos[$numero_campo] != "1845-01-01")
        {
            $campos[$numero_campo] = "1800-01-01";
        }//fin else
	}//if si existe campo
	
	//numero_orden 29  numero campo 30 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 7) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 30  numero campo 31
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 5) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 31  numero campo 32 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 32  numero campo 32.1
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 33  numero campo 32.2 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 5) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 34  numero campo 32.3 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "999999";
		}
	}//if si existe campo
	
	//numero_orden 35  numero campo 32.4 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 36  numero campo 33 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 3) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 37  numero campo 34 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 38  numero campo 35 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		$bool_normalizo=false;
        //CUM NORMALIZA
        $consulta1="";
        $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
        $consulta1.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
        $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
        if( count($resultado1)>0 && is_array($resultado1) )
        {
            if($resultado1[0]["codigo_cum"]!="")
            {
                $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                $bool_normalizo=true;
            }//fin if
        }//fin if
        //FIN NORMALIZA
        
        //CORRECCION CUM HOMOLOGADO
        //si es fundacion
        if($bool_normalizo==false 
            && $NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili"
        )//fin condicion
        {
            $consulta2="";
            $consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE trim(codigo_cfvl)='".trim($campos[$numero_campo])."' ; ";
            $resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
            if( count($resultado2)>0 && is_array($resultado2))
            {
                //echo "<script>alert('alert ".$campos[$numero_campo]." ".$resultado2[0]["codigo_cum_homologo"]."');</script>";
                $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);


                //NORMALIZA despues de buscar homologado
                $cum_res_hom_array=explode("-", $campos[$numero_campo]);
                $cum_res_hom_parte_antes_guion=$cum_res_hom_array[0];
                
                $consulta1="";
                $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".$cum_res_hom_parte_antes_guion."' ";
                $consulta1.=" OR trim(codigo_cum_con_guion)='".$cum_res_hom_parte_antes_guion."' OR trim(cod_atc)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' ; ";

                $resultado1=array();
                $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);

                if( count($resultado1)>0 
                    && is_array($resultado1)==true )
                {
                    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
                    if($resultado1[0]["codigo_cum"]!="")
                    {
                        $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                    }//fin if
                }//fin if
                //FIN NORMALIZA despues de buscar homologado
            }//fin if
        }//fin if
        //FIN CORRECCION CUM HOMOLOGADO
        
        //VERIFICACION POSTERIOR
        $resultado=array();
        if($bool_normalizo==false)
        {
            $consulta_medicamento="";
            $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
            $consulta_medicamento.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
            $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
        }//fin if
        else
        {
            $resultado=$resultado1;
        }
        //FIN VERIFICACION POSTERIOR
	}//if si existe campo
	
	//numero_orden 39  numero campo 36 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		$bool_normalizo=false;
        //CUM NORMALIZA
        $consulta1="";
        $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
        $consulta1.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
        $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
        if( count($resultado1)>0 && is_array($resultado1) )
        {
            if($resultado1[0]["codigo_cum"]!="")
            {
                $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                $bool_normalizo=true;
            }//fin if
        }//fin if
        //FIN NORMALIZA
        
        //CORRECCION CUM HOMOLOGADO
        //si es fundacion
        if($bool_normalizo==false 
            && $NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili"
        )//fin condicion
        {
            $consulta2="";
            $consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE trim(codigo_cfvl)='".trim($campos[$numero_campo])."' ; ";
            $resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
            if( count($resultado2)>0 && is_array($resultado2))
            {
                //echo "<script>alert('alert ".$campos[$numero_campo]." ".$resultado2[0]["codigo_cum_homologo"]."');</script>";
                $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);


                //NORMALIZA despues de buscar homologado
                $cum_res_hom_array=explode("-", $campos[$numero_campo]);
                $cum_res_hom_parte_antes_guion=$cum_res_hom_array[0];
                
                $consulta1="";
                $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".$cum_res_hom_parte_antes_guion."' ";
                $consulta1.=" OR trim(codigo_cum_con_guion)='".$cum_res_hom_parte_antes_guion."' OR trim(cod_atc)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' ; ";

                $resultado1=array();
                $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);

                if( count($resultado1)>0 
                    && is_array($resultado1)==true )
                {
                    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
                    if($resultado1[0]["codigo_cum"]!="")
                    {
                        $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                    }//fin if
                }//fin if
                //FIN NORMALIZA despues de buscar homologado
            }//fin if
        }//fin if
        //FIN CORRECCION CUM HOMOLOGADO
        
        //VERIFICACION POSTERIOR
        $resultado=array();
        if($bool_normalizo==false)
        {
            $consulta_medicamento="";
            $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
            $consulta_medicamento.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
            $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
        }//fin if
        else
        {
            $resultado=$resultado1;
        }
	}//if si existe campo
	
	//numero_orden 40  numero campo 37 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		$bool_normalizo=false;
        //CUM NORMALIZA
        $consulta1="";
        $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
        $consulta1.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
        $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
        if( count($resultado1)>0 && is_array($resultado1) )
        {
            if($resultado1[0]["codigo_cum"]!="")
            {
                $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                $bool_normalizo=true;
            }//fin if
        }//fin if
        //FIN NORMALIZA
        
        //CORRECCION CUM HOMOLOGADO
        //si es fundacion
        if($bool_normalizo==false 
            && $NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili"
        )//fin condicion
        {
            $consulta2="";
            $consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE trim(codigo_cfvl)='".trim($campos[$numero_campo])."' ; ";
            $resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
            if( count($resultado2)>0 && is_array($resultado2))
            {
                //echo "<script>alert('alert ".$campos[$numero_campo]." ".$resultado2[0]["codigo_cum_homologo"]."');</script>";
                $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);


                //NORMALIZA despues de buscar homologado
                $cum_res_hom_array=explode("-", $campos[$numero_campo]);
                $cum_res_hom_parte_antes_guion=$cum_res_hom_array[0];
                
                $consulta1="";
                $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".$cum_res_hom_parte_antes_guion."' ";
                $consulta1.=" OR trim(codigo_cum_con_guion)='".$cum_res_hom_parte_antes_guion."' OR trim(cod_atc)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' ; ";

                $resultado1=array();
                $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);

                if( count($resultado1)>0 
                    && is_array($resultado1)==true )
                {
                    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
                    if($resultado1[0]["codigo_cum"]!="")
                    {
                        $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                    }//fin if
                }//fin if
                //FIN NORMALIZA despues de buscar homologado
            }//fin if
        }//fin if
        //FIN CORRECCION CUM HOMOLOGADO
        
        //VERIFICACION POSTERIOR
        $resultado=array();
        if($bool_normalizo==false)
        {
            $consulta_medicamento="";
            $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
            $consulta_medicamento.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
            $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
        }//fin if
        else
        {
            $resultado=$resultado1;
        }
	}//if si existe campo
	
	//numero_orden 41  numero campo 38 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
		$bool_normalizo=false;
        //CUM NORMALIZA
        $consulta1="";
        $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
        $consulta1.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
        $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
        if( count($resultado1)>0 && is_array($resultado1) )
        {
            if($resultado1[0]["codigo_cum"]!="")
            {
                $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                $bool_normalizo=true;
            }//fin if
        }//fin if
        //FIN NORMALIZA
        
        //CORRECCION CUM HOMOLOGADO
        //si es fundacion
        if($bool_normalizo==false 
            && $NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili"
        )//fin condicion
        {
            $consulta2="";
            $consulta2.="SELECT * FROM gioss_homologos_cum_cfvl WHERE trim(codigo_cfvl)='".trim($campos[$numero_campo])."' ; ";
            $resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
            if( count($resultado2)>0 && is_array($resultado2))
            {
                //echo "<script>alert('alert ".$campos[$numero_campo]." ".$resultado2[0]["codigo_cum_homologo"]."');</script>";
                $campos[$numero_campo]=trim($resultado2[0]["codigo_cum_homologo"]);


                //NORMALIZA despues de buscar homologado
                $cum_res_hom_array=explode("-", $campos[$numero_campo]);
                $cum_res_hom_parte_antes_guion=$cum_res_hom_array[0];
                
                $consulta1="";
                $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".$cum_res_hom_parte_antes_guion."' ";
                $consulta1.=" OR trim(codigo_cum_con_guion)='".$cum_res_hom_parte_antes_guion."' OR trim(cod_atc)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_2)='".$cum_res_hom_parte_antes_guion."' OR trim(codigo_cum_3)='".$cum_res_hom_parte_antes_guion."' ; ";

                $resultado1=array();
                $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);

                if( count($resultado1)>0 
                    && is_array($resultado1)==true )
                {
                    //si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
                    if($resultado1[0]["codigo_cum"]!="")
                    {
                        $campos[$numero_campo]=$resultado1[0]["codigo_cum"];
                    }//fin if
                }//fin if
                //FIN NORMALIZA despues de buscar homologado
            }//fin if
        }//fin if
        //FIN CORRECCION CUM HOMOLOGADO
        
        //VERIFICACION POSTERIOR
        $resultado=array();
        if($bool_normalizo==false)
        {
            $consulta_medicamento="";
            $consulta_medicamento.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
            $consulta_medicamento.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
            $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta_medicamento);
        }//fin if
        else
        {
            $resultado=$resultado1;
        }
	}//if si existe campo
	
	//numero_orden 42  numero campo 39 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		$consulta="";
        $consulta.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."' ; ";
        $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
        
        if( count($resultado)==0 || $campos[$numero_campo]=="")
        {
            if($cod_prestador!="AGRUP_EAPB"){
            	$campos[$numero_campo]=$cod_prestador;
            }
        }
	}//if si existe campo
	
	//numero_orden 43  numero campo 40 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 42  numero campo 40.1 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 43  numero campo 40.2 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 44  numero campo 41
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 45  numero campo 42
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 46  numero campo 43 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 47  numero campo 44
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 48  numero campo 45
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 49  numero campo 46
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 50  numero campo 47.1
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 51  numero campo 47.2 
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 52  numero campo 47.3 
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 53  numero campo 48 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 3) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 54  numero campo 48.1
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
		$excede_fecha_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
        $es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
        
        if($es_campo_actual_fecha_calendario<0
           && $excede_fecha_corte<0
           )
        {
            $campos[$numero_campo]=$fecha_de_corte;
        }
        else if($es_campo_actual_fecha_calendario>0
           		&& $campos[$numero_campo] != "1800-01-01"
           		&& $campos[$numero_campo] != "1845-01-01")
        {
            $campos[$numero_campo] = "1845-01-01";
        }//fin else
	}//if si existe campo
	
	//numero_orden 55  numero campo 48.2 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 56  numero campo 48.3 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 2) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 57  numero campo 48.4 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 58  numero campo 49
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 59  numero campo 49.1 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 60  numero campo 50
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 61  numero campo 51 
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 62  numero campo 52 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 63  numero campo 53
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 64  numero campo 54 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 65  numero campo 55 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 66  numero campo 55.1
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo

	//numero_orden 67  numero campo 56 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 68  numero campo 56.1
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 69 numero campo 57
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 4) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 70  numero campo 57.1
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 71  numero campo 57.2
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 72 numero campo 57.3
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 73 numero campo 57.4
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 74 numero campo 57.5
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 75 numero campo 57.6
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 76 numero campo 57.7
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	
	//numero_orden 77 numero campo 57.8 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 78 numero campo 57.9
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 79 numero campo 57.10
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
		if ((intval($campos[$numero_campo]) != 0 && intval($campos[$numero_campo]) != 1) && intval($campos[$numero_campo]) != 9999 ) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 80 numero campo 57.11
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 81  numero campo 57.12
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
		if (!ctype_alpha($campos[$numero_campo]) {
			$campos[$numero_campo] = "NONE";
		}
	}//if si existe campo
	
	//numero_orden 82 numero campo 57.13
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 83  numero campo 57.14 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
		if (!ctype_alpha($campos[$numero_campo]) {
			$campos[$numero_campo] = "NOAP";
		}
	}//if si existe campo
	
	//numero_orden 84  numero campo 58
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 85  numero campo 59
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
		if (!is_numeric($campos[$numero_campo])) {
			$campos[$numero_campo] = "9999";
		}
	}//if si existe campo
	
	//numero_orden 86  numero campo 60 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 87  numero campo 61
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 88  numero campo 62
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 89  numero campo 63
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 90  numero campo 64
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
		if ( (intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 10) ) {
			
		}
	}//if si existe campo
	
	//numero_orden 91  numero campo 64.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
		if ( (intval($campos[$numero_campo]) < 0 || intval($campos[$numero_campo]) > 6) && intval($campos[$numero_campo]) != 9999 
			&& intval($campos[$numero_campo]) != 99 ) {
			$campos[$numero_campo] = "99";
		}
	}//if si existe campo
	
	//numero_orden 92  numero campo 64.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
		$excede_fecha_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
        $es_campo_actual_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
        
        if($es_campo_actual_fecha_calendario<0
           && $excede_fecha_corte<0
           )
        {
            $campos[$numero_campo]=$fecha_de_corte;
        }
        else if($es_campo_actual_fecha_calendario>0
           		&& $campos[$numero_campo] != "1800-01-01"
           		&& $campos[$numero_campo] != "1845-01-01")
        {
            $campos[$numero_campo] = "180-01-01";
        }//fin else
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarHF

function reparacion_criterios_de_calidad_HF(&$campos,
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
	
	$campos[6]=corrector_formato_fecha($campos[6],$fecha_de_corte,true);
	
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

	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
	    $campo_ver_characters="";
	    $campo_ver_characters=str_replace(array("-","."),"",$campos_ver_characters[$cont_campos]);
	    
	    $campo_ver_characters=ctype_alnum($campo_ver_characters);
	    
	    $campos[$cont_campos]=str_replace(",",".",$campos[$cont_campos]);
	    
	    if($campo_ver_characters==false)
	    {
		if($errores_campos!="")
		{
			$errores_campos.="|";
		}		
		//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104099"])[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104099,$cadena_descripcion_inconsistencia ...".$campos[$cont_campos]." ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
		$consecutivo_errores++;
		
		$hubo_errores=true;
	    }
	    
	    $cont_campos++;
	    
	}//fin while
	//FIN VALIDACION CARACTERES POR CAMPO
	
	//numero_orden 0 numero campo 1 
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 1 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 2 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	
	//numero_orden 3 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	
	//numero_orden 4  numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	
	//numero_orden 5 numero campo 6 Numero Id
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 6 numero campo 7 fecha nacimiento
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 7 numero campo 8 sexo
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 8  numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo

	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo

	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 20  numero campo 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 20  numero campo 21 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 21  numero campo 22
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 22  numero campo 23
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 23  numero campo 24 
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 24  numero campo 25 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 25  numero campo 26
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 26  numero campo 27 
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 27  numero campo 28 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 28  numero campo 29 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 29  numero campo 30 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 30  numero campo 31
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 31  numero campo 32 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 32  numero campo 32.1
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 33  numero campo 32.2 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 34  numero campo 32.3 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 35  numero campo 32.4 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 36  numero campo 33 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 37  numero campo 34 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 38  numero campo 35 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 39  numero campo 36 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 40  numero campo 37 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 41  numero campo 38 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 42  numero campo 39 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 43  numero campo 40 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 42  numero campo 40.1 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 43  numero campo 40.2 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 44  numero campo 41
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 45  numero campo 42
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 46  numero campo 43 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 47  numero campo 44
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 48  numero campo 45
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 49  numero campo 46
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 50  numero campo 47.1
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 51  numero campo 47.2 
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 52  numero campo 47.3 
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 53  numero campo 48 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 54  numero campo 48.1
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 55  numero campo 48.2 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 56  numero campo 48.3 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 57  numero campo 48.4 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 58  numero campo 49
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 59  numero campo 49.1 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 60  numero campo 50
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 61  numero campo 51 
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 62  numero campo 52 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 63  numero campo 53
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 64  numero campo 54 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 65  numero campo 55 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 66  numero campo 55.1
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo

	//numero_orden 67  numero campo 56 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 68  numero campo 56.1
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 69 numero campo 57
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 70  numero campo 57.1
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 71  numero campo 57.2
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 72 numero campo 57.3
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 73 numero campo 57.4
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 74 numero campo 57.5
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 75 numero campo 57.6
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	
	//numero_orden 76 numero campo 57.7
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	
	//numero_orden 77 numero campo 57.8 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 78 numero campo 57.9
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 79 numero campo 57.10
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 80 numero campo 57.11
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 81  numero campo 57.12
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 82 numero campo 57.13
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 83  numero campo 57.14 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 84  numero campo 58
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 85  numero campo 59
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 86  numero campo 60 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 87  numero campo 61
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 88  numero campo 62
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 89  numero campo 63
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 90  numero campo 64
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 91  numero campo 64.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	//numero_orden 92  numero campo 64.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{

	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  reparacion criterios calidad HF

?>