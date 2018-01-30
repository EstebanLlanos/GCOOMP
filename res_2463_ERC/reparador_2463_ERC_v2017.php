<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/conf_personalizada.php';



function reparacion_campo_en_blanco_ERC(&$campos,
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
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
	
	
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
	
	//CALCULO TFG
	$formula_cockcroft_gault=0.0;
	$formula_schwartz=0.0;
	
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
	if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01")
	{
		$edad_years_formula=floatval($edad_r27_1);
	}
	else
	{
		$edad_years_formula=floatval($edad);
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
	    else if(is_numeric($peso) && count($array_decimal_check_peso)>1 )
	    {
		$es_formula_valida=false;  
	    }
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
			$formula_schwartz=(0.50*$talla)/($creatinina);
		}
		else if($edad_years_formula<=12)
		{
			$formula_schwartz=(0.55*$talla)/($creatinina);
		}
		else if($edad_years_formula<=17)
		{
			if($sexo=="M")
			{
				$formula_schwartz=(0.70*$talla)/($creatinina);
			}
			else if($sexo=="F")
			{
				$formula_schwartz=(0.57*$talla)/($creatinina);
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
		$TFG=$formula_schwartz;
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
	//FIN CALCULO TFG
	
	//CORRECCION NOTACION CIENTIFICA
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
	//FIN CORRECCION NOTACION CIENTIFICA
	
	
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
		 
		
		
	}//if si existe campo
	
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		 
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]=$cod_eapb;
		}//fin if 
		
	}//if si existe campo
	
	
	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="6";
		}//fin if 
		
	}//if si existe campo
	
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		 
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="99";
		}//fin if 
		
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
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="0000000000";
	    }//fin if
		
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
		if(strlen($campos[$numero_campo])==11
		   && (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8"))
		{
		    $campos[$numero_campo]="0".$campos[$numero_campo];
		}
		else if(strlen($campos[$numero_campo])==10)
		{
		    $campos[$numero_campo]=$campos[$numero_campo]."01";
		}
		
		
		$consulta="";
		$consulta.="SELECT * FROM gios_prestador_servicios_salud WHERE cod_registro_especial_pss='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		if( count($resultado)==0 || $campos[$numero_campo]=="")
		{
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n19=trim($campos[18]);
		//campo obligatorio
		if($campos[$numero_campo]=="" && $campo_n19!="1845-01-01")
		{
			$campos[$numero_campo]="1";
		}//fin if
		else if($campos[$numero_campo]=="" && $campo_n19=="1845-01-01")
		{
			$campos[$numero_campo]="2";
		}//fin if
		else if($campos[$numero_campo]=="" && $campo_n19=="")
		{
			$campos[$numero_campo]="2";
		}//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n18=trim($campos[17]);
	    if($campos[$numero_campo]=="" && $campo_n18=="2")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n18=="1")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n18=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden 19  numero campo 19.1 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n18=trim($campos[17]);
	    if($campos[$numero_campo]=="" && $campo_n18=="2")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n18=="1")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n18=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 20  numero campo 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n21=trim($campos[21]);
		//campo obligatorio
		if($campos[$numero_campo]=="" && $campo_n21!="1845-01-01")
		{
		    $campos[$numero_campo]="1";
		}//fin if
		else if($campos[$numero_campo]=="" && $campo_n21=="1845-01-01")
		{
		    $campos[$numero_campo]="2";
		}//fin if
		else if($campos[$numero_campo]=="" && $campo_n21=="")
		{
		    $campos[$numero_campo]="2";
		}//fin if 
		
		
	}//if si existe campo
	
	
	//numero_orden 21  numero campo 21 
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    if($campos[$numero_campo]=="" && $campo_n20=="2")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n20=="1")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n20=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 22  numero campo 21.1 
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    if($campos[$numero_campo]=="" && $campo_n20=="2")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n20=="1")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n20="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if	
	}//if si existe campo
	
	
	//numero_orden 23  numero campo 22
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="98";
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		
		
	}//if si existe campo
	
	
	//numero_orden 26  numero campo 25
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 27  numero campo 26 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 28  numero campo 27 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n27_1=trim($campos[29]);
	    if($campos[$numero_campo]=="" && $campo_n27_1!="1845-01-01")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n27_1=="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n27_1=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden 29  numero campo 27.1 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n27=trim($campos[28]);
	    if($campos[$numero_campo]=="" && $campo_n27!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n27=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n27=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 30  numero campo 28 
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n20=trim($campos[20]);
	    if($campos[$numero_campo]=="" && $campo_n20=="2")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n20=="1")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n20=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 31  numero campo 28.1 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n28=trim($campos[30]);
	    if($campos[$numero_campo]=="" && $campo_n28=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n28!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n28=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 29 
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n29_1=trim($campos[33]);
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="9999";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 29.1 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n29=trim($campos[32]);
	    if($campos[$numero_campo]==""
	       && $campo_n29!="9999"
	       && $campo_n29!="9888")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 30 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n30_1=trim($campos[35]);
	    
	    $campo_n35=$campos[44];
	    $campo_n39=$campos[48];
	    if($campos[$numero_campo]=="" && intval($campo_n39)==5)
	    {
		    $campos[$numero_campo]="988";
	    }
	    else if($campos[$numero_campo]=="" && intval($campo_n39)!=5)
	    {
		    $campos[$numero_campo]="999";
	    }
	     
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 30.1 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n30=trim($campos[34]);
	    if($campos[$numero_campo]=="" && $campo_n30!="988")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n30=="988")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n30=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 31 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n31_1=trim($campos[37]);
	    if($campos[$numero_campo]=="" && $campo_n31_1!="1845-01-01")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n31_1=="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n31_1=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 31.1 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n31=trim($campos[36]);
	    if($campos[$numero_campo]=="" && $campo_n31!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n31=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n31=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 32 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n32_1=trim($campos[39]);
	    if($campos[$numero_campo]=="" && $campo_n32_1!="1845-01-01")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n32_1=="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n32_1=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 32.1 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n32=trim($campos[38]);
	    if($campos[$numero_campo]=="" && $campo_n32!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n32=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n32=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 33 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n33_1=trim($campos[41]);
	    if($campos[$numero_campo]=="" && $campo_n33_1!="1845-01-01")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n33_1=="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n33_1=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 33.1 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n33=trim($campos[40]);
	    if($campos[$numero_campo]=="" && $campo_n33!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n33=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n33=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 34 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n39=$campos[48];
	    if($campos[$numero_campo]=="" && (intval($campo_n39)==1 || intval($campo_n39)==2 || intval($campo_n39)==3 || intval($campo_n39)==98 )  )
	    {
		    $campos[$numero_campo]="988";
	    }			
	    else if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="999";
	    }
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 34.1 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n34=$campos[43];
	    if(trim($campos[$numero_campo])==""
		    && intval($campo_n34)!=988 && intval($campo_n34)!=999
		    )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	    else if(trim($campos[$numero_campo])==""
	       && (intval($campo_n34)==988 || intval($campo_n34)==999)
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 44  numero campo 35 
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		//echo "<script>alert('campo 35 antes vo ".$campos[$numero_campo]."');</script>";
	    $campo_n27=trim($campos[28]);
	    if($campos[$numero_campo]=="" && $campo_n27=="")
	    {
		$campos[$numero_campo]="999";
	    }//fin if
	    else if($campos[$numero_campo]=="" && ($campo_n27=="98" || $campo_n27=="99"))
	    {
		$campos[$numero_campo]="999";
	    }//fin if
	    else if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]=$TFG;
	    }//fin if
	    //echo "<script>alert('campo 35 despues vo ".$campos[$numero_campo]."');</script>";
	}//if si existe campo
	
	
	//numero_orden 45  numero campo 36 
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="99";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 37 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="99";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 47  numero campo 38
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
		$campo_n35=trim($campos[44]);
		if($campos[$numero_campo]=="" && $campo_n35=="")
		{
		    $campos[$numero_campo]="3";
		}//fin if
		else if($campos[$numero_campo]=="" && $campo_n35=="999")
		{
		    $campos[$numero_campo]="3";
		}//fin if
		else if($campos[$numero_campo]=="")
		{
		    if( floatval($campo_n35)>250 && floatval($campo_n35)!=988 && floatval($campo_n35)!=999 )
		    {
			    $campos[$numero_campo]="0";
		    }
		    else if( (floatval($campo_n35)<60 || intval($campo_n35)==988 )  )
		    {
			    $campos[$numero_campo]="1";
		    }		
		    else if( (floatval($campo_n35)>=60 && floatval($campo_n35)<=250 )  )
		    {
			    $campos[$numero_campo]="2";
		    }
		}
	}//if si existe campo
	
	
	//numero_orden 48  numero campo 39
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n35=trim($campos[44]);
	    		
		if($campos[$numero_campo]=="" && $campo_n35=="")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n35!="")
	    {
			if(intval($campo_n35)>=90 && intval($campo_n35)<=250)
			{
				$campos[$numero_campo]="1";
			}
			else if( intval($campo_n35)>=60 && intval($campo_n35)<90) 
			{
				$campos[$numero_campo]="2";
			}
			else if( intval($campo_n35)>=30 && intval($campo_n35)<60) 
			{
				$campos[$numero_campo]="3";
			}
			else if( intval($campo_n35)>=15 && intval($campo_n35)<30) 
			{
				$campos[$numero_campo]="4";
			}
			else if( intval($campo_n35)<15  ) 
			{
				$campos[$numero_campo]="5";
			}
			else if(intval($campo_n35)==988 )
			{
				$campos[$numero_campo]="98";
			}
			else if(intval($campo_n35)==999 )
			{
				$campos[$numero_campo]="99";
			}
	    }//fin else 
		
	}//if si existe campo
	
	
	//numero_orden 49  numero campo 40 
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n39=trim($campos[48]);
	    if($campos[$numero_campo]=="" && $campo_n39!="5")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 50  numero campo 41
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=trim($campos[47]);
		if($campos[$numero_campo]=="" && ($campo_n38!="1" && $campo_n38!="2"))
		{
		    $campos[$numero_campo]="98";
		}//fin if
		else if($campos[$numero_campo]=="" && ($campo_n38=="1" || $campo_n38=="2"))
		{
		    $campos[$numero_campo]="99";
		}//fin if
		else if($campos[$numero_campo]=="" && ($campo_n38==""))
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 42
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n41=trim($campos[50]);
	    $campo_n35=trim($campos[44]);
	    if($campos[$numero_campo]=="" && $campo_n41!="1")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n41=="1")
	    {
		$campos[$numero_campo]=$campo_n35;
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n41=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 43
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[55]);
	    $campo_n49=trim($campos[58]);
	    $campo_n57=trim($campos[66]);
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]==""
	       && $campo_n46=="98"
	       && $campo_n49=="98"
	       && $campo_n57=="98"
	       && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && ($campo_n46!="98"
	       || $campo_n49!="98"
	       || $campo_n57!="98")
	       && $campo_n64!="5")
	    {
		$campos[$numero_campo]="4";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && $campo_n46==""
	       && $campo_n49==""
	       && $campo_n57==""
	       && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 44 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[55]);
	    $campo_n49=trim($campos[58]);
	    $campo_n57=trim($campos[66]);
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]==""
	       && $campo_n46=="98"
	       && $campo_n49=="98"
	       && $campo_n57=="98"
	       && $campo_n64=="5")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && ($campo_n46!="98"
	       || $campo_n49!="98"
	       || $campo_n57!="98")
	       && $campo_n64!="5")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && $campo_n46==""
	       && $campo_n49==""
	       && $campo_n57==""
	       && $campo_n64=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 45 
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[55]);
	    $campo_n49=trim($campos[58]);
	    if($campos[$numero_campo]==""
	       && $campo_n46=="98"
	       && $campo_n49=="98"
	       )
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && ($campo_n46!="98"
	       || $campo_n49!="98"
	       )
	       )
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && $campo_n46==""
	       && $campo_n49=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 46 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n39=trim($campos[48]);
	    $campo_n47=trim($campos[56]);
	    if($campos[$numero_campo]=="" && $campo_n39!="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5" && $campo_n47!="98")
	    {
		$campos[$numero_campo]="1";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5" && $campo_n47=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5" && $campo_n47=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 47 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[55]);
	    if($campos[$numero_campo]=="" && $campo_n46=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n46=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 57  numero campo 48 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[55]);
	    if($campos[$numero_campo]=="" && $campo_n46=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n46!="98")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n46=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 58  numero campo 49 
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n39=trim($campos[48]);
	    $campo_n50=trim($campos[59]);
	    if($campos[$numero_campo]=="" && $campo_n39!="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5" && $campo_n50!="98")
	    {
		$campos[$numero_campo]="1";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5" && $campo_n50=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5" && $campo_n50=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 50 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n49=trim($campos[58]);
	    if($campos[$numero_campo]=="" && $campo_n49=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n49=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 51 
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[55]);
	    if($campos[$numero_campo]=="" && $campo_n46=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n46=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 52
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n49=trim($campos[58]);
	    if($campos[$numero_campo]=="" && $campo_n49=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n49!="98")
	    {
		$campos[$numero_campo]="0";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n49=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 53 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n49=trim($campos[58]);
	    if($campos[$numero_campo]=="" && $campo_n49=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n49!="98")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n49=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 54
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n44=trim($campos[53]);
	    if($campos[$numero_campo]=="" && $campo_n44=="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n44!="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n44=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if 
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 55 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n44=trim($campos[53]);
	    if($campos[$numero_campo]=="" && $campo_n44=="1845-01-01")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n44=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if 
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 56 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n44=trim($campos[53]);
	    if($campos[$numero_campo]=="" && $campo_n44=="1845-01-01")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n44=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if 
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 57
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n39=trim($campos[48]);
	    if($campos[$numero_campo]=="" && $campo_n39!="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="5")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n39=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 58 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n57=trim($campos[66]);
	    if($campos[$numero_campo]=="" && $campo_n57=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n57!="98")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n57=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 59 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 60
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 61 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 71  numero campo 62
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    //campo en blanco
	    $campo_n38=$campos[47];
		
	    $campo_n39=$campos[48];		
	    
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
	    
	    if($campos[$numero_campo]==""
	       && trim($campo_n39)=="98" )
	    {
		    $campos[$numero_campo]="98";
	    }		
	    else if($campos[$numero_campo]==""
		       && (intval($campo_n39)=="1"
			   || intval($campo_n39)=="2"
			   || intval($campo_n39)=="3"
			   || intval($campo_n39)=="4")
		       )
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if($campos[$numero_campo]==""
		       && trim($campo_n39)=="5"
		       && (trim($campo_n64)=="1" || trim($campo_n64)=="2")
		    )
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if(
		    $campos[$numero_campo]==""
		     && trim($campo_n39)=="5"
		    && (trim($campo_n64)!="1" && trim($campo_n64)!="2")
		    && (trim($campo_n62_1)=="1"
			|| trim($campo_n62_2)=="1"
			|| trim($campo_n62_3)=="1"
			|| trim($campo_n62_4)=="1"
			|| trim($campo_n62_5)=="1"
			|| trim($campo_n62_6)=="1"
			|| trim($campo_n62_7)=="1"
			|| trim($campo_n62_8)=="1"
			|| trim($campo_n62_9)=="1"
			|| trim($campo_n62_10)=="1"
			|| trim($campo_n62_11)=="1"
			)
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(
		    $campos[$numero_campo]==""
		     && trim($campo_n39)=="5"
		    && (trim($campo_n64)!="1" && trim($campo_n64)!="2")
		    && (trim($campo_n62_1)!="1"
			&& trim($campo_n62_2)!="1"
			&& trim($campo_n62_3)!="1"
			&& trim($campo_n62_4)!="1"
			&& trim($campo_n62_5)!="1"
			&& trim($campo_n62_6)!="1"
			&& trim($campo_n62_7)!="1"
			&& trim($campo_n62_8)!="1"
			&& trim($campo_n62_9)!="1"
			&& trim($campo_n62_10)!="1"
			&& trim($campo_n62_11)!="1"
			)
		    )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 62.1
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 62.2
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 62.3
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 62.4
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 62.5
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 62.6
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 62.7
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 62.8
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 62.9 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	//numero_orden 81  numero campo 62.10
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	//numero_orden 82  numero campo 62.11
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]==""
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]==""
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]=="" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 63
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n39=trim($campos[48]);
	    $campo_n63_1=trim($campos[84]);
	    if($campos[$numero_campo]=="" && $campo_n39!="5")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n63_1!="98" && $campo_n39=="5")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n63_1=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 63.1
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{	    
		
	    $campo_n63=trim($campos[83]);
	    if($campos[$numero_campo]==""
	       && $campo_n63=="1845-01-01" )
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && $campo_n63!="1845-01-01" )
	    {
		if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }//fin if
	    else if($campos[$numero_campo]==""
	       && $campo_n63=="" )
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 64
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n66=trim($campos[87]);
	    if($campos[$numero_campo]=="" && ($campo_n66!="98" && $campo_n66!="99"))
	    {
		$campos[$numero_campo]="1";
	    }//fin if 
	    else if($campos[$numero_campo]=="" && ($campo_n66=="98" || $campo_n66=="99"))
	    {
		$campos[$numero_campo]="5";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n66=="")
	    {
		$campos[$numero_campo]="5";
	    }//fin if 
	}//if si existe campo
	
	
	//numero_orden 86  numero campo 65 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]=$cod_eapb;
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 87  numero campo 66
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 88  numero campo 67
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if 
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 68 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 69
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 69.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 92  numero campo 69.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 93  numero campo 69.3
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 94  numero campo 69.4
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 95  numero campo 69.5 
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 96  numero campo 69.6 
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 97  numero campo 69.7 
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 98  numero campo 70 
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="1";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 99  numero campo 70.1 
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n70=trim($campos[98]);
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 100  numero campo 70.2 
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
		$campo_n70=trim($campos[98]);
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 101  numero campo 70.3 
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
		$campo_n70=trim($campos[98]);
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 102  numero campo 70.4 
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
		$campo_n70=trim($campos[98]);
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 103  numero campo 70.5 
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
		$campo_n70=trim($campos[98]);
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 104  numero campo 70.6 
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n70=trim($campos[98]);
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="2";
	    }//fin if
	    
	}//if si existe campo
	
	$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();
	
	//numero_orden 105  numero campo 70.7 
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n70=trim($campos[98]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' OR codigo_cum_2='".$campos[$numero_campo]."' OR codigo_cum_3='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
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
	    
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="97";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 106  numero campo 70.8
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n70=trim($campos[98]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' OR codigo_cum_2='".$campos[$numero_campo]."' OR codigo_cum_3='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
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
	    
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="97";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 107  numero campo 70.9 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n70=trim($campos[98]);
	    
	    //CORRECCION CUM HOMOLOGADO
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum='".$campos[$numero_campo]."' OR codigo_cum_con_guion='".$campos[$numero_campo]."' OR cod_atc='".$campos[$numero_campo]."' OR codigo_cum_2='".$campos[$numero_campo]."' OR codigo_cum_3='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( count($resultado)==0  && (intval($campos[$numero_campo])!=97 && intval($campos[$numero_campo])!=98))
	    {
		$consulta1="";
		$consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE codigo_cum=trim('".$campos[$numero_campo]."') OR codigo_cum_con_guion=trim('".$campos[$numero_campo]."') OR cod_atc=trim('".$campos[$numero_campo]."') OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
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
	    
	    if($campos[$numero_campo]=="" && $campo_n70=="98")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n70!="98")
	    {
		$campos[$numero_campo]="97";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 108  numero campo 71 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="1";//estaba cero deberia ser uno por validacion de valor permitido
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 109  numero campo 72
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n71=trim($campos[108]);
	    if($campos[$numero_campo]=="" && $campo_n71=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n71!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n71=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 110  numero campo 73
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 111  numero campo 74
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="1";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden 112  numero campo 75 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n64=trim($campos[85]);
	    if($campos[$numero_campo]=="" && $campo_n64=="5")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n64!="5")
	    {
		$campos[$numero_campo]="1000";
	    }//fin if
	    if($campos[$numero_campo]=="" && $campo_n64=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden 113  numero campo 76
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="1";
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 114  numero campo 77
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="1000";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 115  numero campo 78 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		$campos[$numero_campo]="99";
	    }//fin if 
	}//if si existe campo
	
	
	//numero_orden 116  numero campo 79
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="98";
		}//fin if 
	}//if si existe campo
	
	
	//numero_orden 117  numero campo 80 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n80_1=trim($campos[118]);
	    if($campos[$numero_campo]=="" && $campo_n80_1!="1845-01-01")
	    {
		$campos[$numero_campo]="99";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n80_1=="1845-01-01")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n80_1=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden 118  numero campo 80.1 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n80=trim($campos[117]);
	    if($campos[$numero_campo]=="" && $campo_n80!="98")
	    {
		$campos[$numero_campo]="1800-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n80=="98")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	    else if($campos[$numero_campo]=="" && $campo_n80=="")
	    {
		$campos[$numero_campo]="1845-01-01";
	    }//fin if
	}//if si existe campo
	
	
	
	
	
	
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarERC


function reparacion_formato_ERC(&$campos,
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
	
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
	
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
	
	//CALCULO TFG
	$formula_cockcroft_gault=0.0;
	$formula_schwartz=0.0;
	
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
	if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01")
	{
		$edad_years_formula=floatval($edad_r27_1);
	}
	else
	{
		$edad_years_formula=floatval($edad);
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
	    else if(is_numeric($peso) && count($array_decimal_check_peso)>1 )
	    {
		$es_formula_valida=false;  
	    }
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
			$formula_schwartz=(0.50*$talla)/($creatinina);
		}
		else if($edad_years_formula<=12)
		{
			$formula_schwartz=(0.55*$talla)/($creatinina);
		}
		else if($edad_years_formula<=17)
		{
			if($sexo=="M")
			{
				$formula_schwartz=(0.70*$talla)/($creatinina);
			}
			else if($sexo=="F")
			{
				$formula_schwartz=(0.57*$talla)/($creatinina);
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
		$TFG=$formula_schwartz;
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
	//FIN CALCULO TFG
	
	//CORRECCION NOTACION CIENTIFICA
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
	//FIN CORRECCION NOTACION CIENTIFICA
	
	
	
	//numero_orden 0 numero campo 1 
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );
		
		
				
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
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );
		
		
		
	}//if si existe campo
	
	
	//numero_orden 8  numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim(strtoupper($campos[$numero_campo])) );
		
		
	}//if si existe campo
	
	
	//numero_orden 9  numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
		
		
	}//if si existe campo
	
	
	//numero_orden 10  numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}		
		
	}//if si existe campo
	
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
		
		
		
	}//if si existe campo
	
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
		
	}//if si existe campo
	
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) ); 
		
		
		
	}//if si existe campo
	
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0); 
		
	}//if si existe campo
	
	
	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
		
	}//if si existe campo
	
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
		
	}//if si existe campo
	
	
	//numero_orden 19  numero campo 19.1 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		 
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
		
	}//if si existe campo
	
	
	//numero_orden 20  numero campo 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
		
	}//if si existe campo
	
	
	//numero_orden 21  numero campo 21 
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 22  numero campo 21.1 
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 23  numero campo 22
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 24  numero campo 23 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		//echo "<script>alert('antes: ".$campos[$numero_campo]." ');</script>";
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						//$longitud_a_dejar=(strlen($array_campo_actual[1])-1);
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>1)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		//echo "<script>alert('despues: ".$campos[$numero_campo]." ');</script>";
	}//if si existe campo
	
	
	//numero_orden 25  numero campo 24 
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
		
		
		str_replace(",",".",$campos[$numero_campo]);
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_24=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_24)>1)
		    {
				if(strlen($array_campo_24[0])==3)
				{
					$campos[$numero_campo]=$array_campo_24[0];
				}
				else if(strlen($array_campo_24[0])==2 && strlen($array_campo_24[1])>=1)
				{
					$campos[$numero_campo]=$array_campo_24[0].".".substr($array_campo_24[1],0,1);
				}
				else if(strlen($array_campo_24[0])==1 && strlen($array_campo_24[1])>1)
				{
					$campos[$numero_campo]=$array_campo_24[0].".".substr($array_campo_24[1],0,2);
				}
				else if(strlen($array_campo_24[0])==1 && strlen($array_campo_24[1])==1)
				{
					$campos[$numero_campo]=$array_campo_24[0].".".substr($array_campo_24[1],0,1)."0";
				}
				$campos[$numero_campo]=intval($campos[$numero_campo]);
			
		    }//fin if
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 26  numero campo 25
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 27  numero campo 26 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 28  numero campo 27 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98
		   || intval($campos[$numero_campo])==99)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 29  numero campo 27.1 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 30  numero campo 28 
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98
		   || intval($campos[$numero_campo])==99)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 31  numero campo 28.1 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 29 
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==9999 || intval($campos[$numero_campo])==9888)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 29.1 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 30 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98
		   || intval($campos[$numero_campo])==99)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 30.1 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 31 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==999)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 31.1 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 32 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==999)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 32.1 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 33 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==999)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 33.1 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 34 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==988
		   || intval($campos[$numero_campo])==999)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 34.1 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 44  numero campo 35 
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		//echo "<script>alert('campo 35 antes vf ".$campos[$numero_campo]."');</script>";
		
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						//$longitud_a_dejar=(strlen($array_campo_actual[1])-1);
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>1)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==988
		   || intval($campos[$numero_campo])==999)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
		
		//echo "<script>alert('campo 35 despues vf ".$campos[$numero_campo]."');</script>";
	}//if si existe campo
	
	
	//numero_orden 45  numero campo 36 
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 37 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 47  numero campo 38
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 48  numero campo 39
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 49  numero campo 40 
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
		
	}//if si existe campo
	
	
	//numero_orden 50  numero campo 41
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 42
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 43
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 44 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 45 
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
		
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 46 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 47 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 57  numero campo 48 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 58  numero campo 49 
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 50 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 51 
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 52
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 53 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 54
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 55 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
				
		//$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 56 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 57
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 58 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 59 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 60
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 61 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
		str_replace(",",".",$campos[$numero_campo]);		
		str_replace(" ","",$campos[$numero_campo]);
		if(is_numeric($campos[$numero_campo]))
		{		
		    $array_campo_actual=explode(".",$campos[$numero_campo]);
			
			if($array_campo_actual[0]=="")
			{
				$array_campo_actual[0]="0";
			}
			
		    if(count($array_campo_actual)>1)
		    {
				if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==1)
				{
					$longitud_a_dejar=1;
					if(strlen($array_campo_actual[1])>1)
					{
						$longitud_a_dejar=2;//ya que el maximo de posiciones decimales es 2
					}
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])==2)
				{
					$longitud_a_dejar=1;
					$campos[$numero_campo]=$array_campo_actual[0].".".substr($array_campo_actual[1],0,$longitud_a_dejar);
				}
				else if(strlen($array_campo_actual[1])>=1
				   && strlen($array_campo_actual[0])>2)
				{
					$campos[$numero_campo]=$array_campo_actual[0];
				}
				
		    }//fin
		}//fin if
		
		if(intval($campos[$numero_campo])==98)
		{
			$campos[$numero_campo]=intval($campos[$numero_campo]);
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden 71  numero campo 62
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 62.1
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 62.2
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 62.3
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 62.4
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 62.5
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 62.6
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 62.7
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 62.8
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 62.9 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	//numero_orden 81  numero campo 62.10
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
		
	}//if si existe campo
	
	//numero_orden 82  numero campo 62.11
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 63
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 63.1
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 64
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 86  numero campo 65 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
	}//if si existe campo
	
	
	//numero_orden 87  numero campo 66
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) ); 
	}//if si existe campo
	
	
	//numero_orden 88  numero campo 67
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 68 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 69
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 69.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
				
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 92  numero campo 69.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 93  numero campo 69.3
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 94  numero campo 69.4
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 95  numero campo 69.5 
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 96  numero campo 69.6 
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 97  numero campo 69.7 
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 98  numero campo 70 
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 99  numero campo 70.1 
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 100  numero campo 70.2 
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 101  numero campo 70.3 
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 102  numero campo 70.4 
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 103  numero campo 70.5 
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 104  numero campo 70.6 
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 105  numero campo 70.7 
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) ); 
	}//if si existe campo
	
	
	//numero_orden 106  numero campo 70.8
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
	}//if si existe campo
	
	
	//numero_orden 107  numero campo 70.9 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
	}//if si existe campo
	
	
	//numero_orden 108  numero campo 71 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 109  numero campo 72
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 110  numero campo 73
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0);
	}//if si existe campo
	
	
	//numero_orden 111  numero campo 74
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 112  numero campo 75 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
		
		
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 113  numero campo 76
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
		
	}//if si existe campo
	
	
	//numero_orden 114  numero campo 77
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 115  numero campo 78 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    $campos[$numero_campo]=trim(strtoupper($campos[$numero_campo]));
		$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
	}//if si existe campo
	
	
	//numero_orden 116  numero campo 79
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);} 
	}//if si existe campo
	
	
	//numero_orden 117  numero campo 80 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!=""){$campos[$numero_campo]=intval($campos[$numero_campo]);}
	}//if si existe campo
	
	
	//numero_orden 118  numero campo 80.1 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,0,801);
	}//if si existe campo
	
	
	
	
	
	
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarERC

function reparacion_valor_permitido_ERC(&$campos,
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
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
	
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
	
	//CALCULO TFG
	$formula_cockcroft_gault=0.0;
	$formula_schwartz=0.0;
	
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
	if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01")
	{
		$edad_years_formula=floatval($edad_r27_1);
	}
	else
	{
		$edad_years_formula=floatval($edad);
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
	    else if(is_numeric($peso) && count($array_decimal_check_peso)>1 )
	    {
		$es_formula_valida=false;  
	    }
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
			$formula_schwartz=(0.50*$talla)/($creatinina);
		}
		else if($edad_years_formula<=12)
		{
			$formula_schwartz=(0.55*$talla)/($creatinina);
		}
		else if($edad_years_formula<=17)
		{
			if($sexo=="M")
			{
				$formula_schwartz=(0.70*$talla)/($creatinina);
			}
			else if($sexo=="F")
			{
				$formula_schwartz=(0.57*$talla)/($creatinina);
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
		$TFG=$formula_schwartz;
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
	//FIN CALCULO TFG
	
	//CORRECCION NOTACION CIENTIFICA
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
	//FIN CORRECCION NOTACION CIENTIFICA
	
	
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
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="4"
	       && $campos[$numero_campo]!="5"
	       && $campos[$numero_campo]!="6"
	       )
	    {
		$campos[$numero_campo]="6";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden 11  numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
	    if((intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>16)
	       && (intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>59)
	       && (intval($campos[$numero_campo])<31 || intval($campos[$numero_campo])>39)
	       && $campos[$numero_campo]!="99"
	      )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    
	    if(strlen($campos[$numero_campo])==4
		&& (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8"))
	     {
		 $campos[$numero_campo]="0".$campos[$numero_campo];
	     }
	    else if(strlen($campos[$numero_campo])>5)
	    {
		$campos[$numero_campo]=substr($campos[$numero_campo],0,5);
	    }
		
		
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
	    //$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);
	}//if si existe campo
	
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
	    		
	    //se tuvo que colocar antes de la validacion de campo en blanco debido a que esta verifica si
	    //existe en bd y lo remplaza por $cod_prestador para no cambiar codigos que estubiessen buenos
		
	}//if si existe campo
	
	
	//numero_orden 16  numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);		
	}//if si existe campo
	
	
	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 19  numero campo 19.1 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		 
	    if($campos[$numero_campo]!="")
	    {
		if(intval($campos[$numero_campo])>999999999 && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="999999999";
		}
		else if(intval($campos[$numero_campo])<1000 && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="1000";
		}
		
	    }
		
	}//if si existe campo
	
	
	//numero_orden 20  numero campo 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       )
	    {
		$campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 21  numero campo 21 
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 22  numero campo 21.1 
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="")
	    {
		if(intval($campos[$numero_campo])>999999999 && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="999999999";
		}
		else if(intval($campos[$numero_campo])<1000 && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="1000";
		}
		
	    }
	}//if si existe campo
	
	
	//numero_orden 23  numero campo 22
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="4"
	       && $campos[$numero_campo]!="5"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 24  numero campo 23 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="" && is_numeric($campos[$numero_campo]))
	    {
		if(floatval($campos[$numero_campo])>500 )
		{
		    $campos[$numero_campo]="500";
		}
		else if(floatval($campos[$numero_campo])<1)
		{
		    $campos[$numero_campo]="1";
		}
	    }
	}//if si existe campo
	
	
	//numero_orden 25  numero campo 24 
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="" && is_numeric($campos[$numero_campo]))
	    {
		if(floatval($campos[$numero_campo])>300 )
		{
		    $campos[$numero_campo]="300";
		}
		else if(floatval($campos[$numero_campo])<20)
		{
		    $campos[$numero_campo]="20";
		}
	    }
	}//if si existe campo
	
	
	//numero_orden 26  numero campo 25
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>300 && $campos[$numero_campo]!="999" )
		{
		    $campos[$numero_campo]="300";
		}
		else if(floatval($campos[$numero_campo])<60 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="60";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="60";
		}
	}//if si existe campo
	
	
	//numero_orden 27  numero campo 26 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>150 && $campos[$numero_campo]!="999" )
		{
		    $campos[$numero_campo]="150";
		}
		else if(floatval($campos[$numero_campo])<20 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="20";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="20";
		}
	}//if si existe campo
	
	
	//numero_orden 28  numero campo 27 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>50 && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="50";
		}
		else if(floatval($campos[$numero_campo])<0.01 && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="0.01";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0.1";
		}
		
	}//if si existe campo
	
	
	//numero_orden 29  numero campo 27.1 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 30  numero campo 28 
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>40 && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="40";
		}
		else if(floatval($campos[$numero_campo])<0.01 && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="0.01";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0.01";
		}
	}//if si existe campo
	
	
	//numero_orden 31  numero campo 28.1 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 29 
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>900
		   && $campos[$numero_campo]!="9999"
		   && $campos[$numero_campo]!="9888"
		   )
		{
		    $campos[$numero_campo]="900";
		}
		else if(floatval($campos[$numero_campo])<0.01)
		{
		    $campos[$numero_campo]="0.01";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0.01";
		}
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 29.1 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 30 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>3000
		   && $campos[$numero_campo]!="999"
		   && $campos[$numero_campo]!="988"
		   )
		{
		    $campos[$numero_campo]="3000";
		}
		else if(floatval($campos[$numero_campo])<10)
		{
		    $campos[$numero_campo]="10";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="10";
		}
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 30.1 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 31 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>500 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="500";
		}
		else if(floatval($campos[$numero_campo])<20 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="20";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="20";
		}
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 31.1 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 32 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>200 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="200";
		}
		else if(floatval($campos[$numero_campo])<0 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="0";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0";
		}
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 32.1 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 33 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>600 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="600";
		}
		else if(floatval($campos[$numero_campo])<10 && $campos[$numero_campo]!="999")
		{
		    $campos[$numero_campo]="10";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="10";
		}
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 33.1 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 34 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>5000
		   && $campos[$numero_campo]!="999" && $campos[$numero_campo]!="988" )
		{
		    $campos[$numero_campo]="5000";
		}
		else if(floatval($campos[$numero_campo])<0
			&& $campos[$numero_campo]!="999" && $campos[$numero_campo]!="988")
		{
		    $campos[$numero_campo]="0";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0";
		}
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 34.1 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 44  numero campo 35 
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		//echo "<script>alert('campo 35 antes vp ".$campos[$numero_campo]."');</script>";
		if(floatval($campos[$numero_campo])>250
		   && $campos[$numero_campo]!="999" && $campos[$numero_campo]!="988" )
		{
		    $campos[$numero_campo]="250";
		}
		else if(floatval($campos[$numero_campo])<1
			&& $campos[$numero_campo]!="999" && $campos[$numero_campo]!="988")
		{
		    $campos[$numero_campo]="1";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="1";
		}
		//echo "<script>alert('campo 35 despues vp ".$campos[$numero_campo]."');</script>";
	}//if si existe campo
	
	
	//numero_orden 45  numero campo 36 
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="98"
	       && $campos[$numero_campo]!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 37 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="98"
	       && $campos[$numero_campo]!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 47  numero campo 38
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="98"
	       && $campos[$numero_campo]!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 48  numero campo 39
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_n64=trim($campos[85]);
	    //echo "<script>alert('antes campo 39 ".$campos[$numero_campo]." campo 64 $campo_n64');</script>";
	    if($campos[$numero_campo]=="98"
	       && $campo_n64!="5"
	       )
	    {
		//echo "<script>alert(' campo 39 ".$campos[$numero_campo]." campo 64 $campo_n64');</script>";
		$campos[$numero_campo]="99";
	    }	
		
	}//if si existe campo
	
	
	//numero_orden 49  numero campo 40 
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 50  numero campo 41
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       && $campos[$numero_campo]!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 42
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>20
		   && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="20";
		}
		else if(floatval($campos[$numero_campo])<0
			&& $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="0";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0";
		}
		
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 43
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="4"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 44 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 45 
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 46 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 47 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>4.5
		   && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="4.5";
		}
		else if(floatval($campos[$numero_campo])<0.5
			&& $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="0.5";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0.5";
		}
	}//if si existe campo
	
	
	//numero_orden 57  numero campo 48 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="")
	    {
		if(floatval($campos[$numero_campo])>999999999
		    &&  $campos[$numero_campo]!="98" )
		 {
		     $campos[$numero_campo]="999999999";
		 }
		 else if(floatval($campos[$numero_campo])<1000
			 &&  $campos[$numero_campo]!="98")
		 {
		     $campos[$numero_campo]="1000";
		 }
	    }
	}//if si existe campo
	
	
	//numero_orden 58  numero campo 49 
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	     if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 50 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>4.5
		   && $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="4.5";
		}
		else if(floatval($campos[$numero_campo])<0.5
			&& $campos[$numero_campo]!="99" && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="0.5";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0.5";
		}
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 51 
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>12
		   &&  $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="12";
		}
		else if(floatval($campos[$numero_campo])<1
			&&  $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="1";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="1";
		}
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 52
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>6
		   &&  $campos[$numero_campo]!="98" )
		{
		    $campos[$numero_campo]="6";
		}
		else if(floatval($campos[$numero_campo])<0
			&&  $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="0";
		}
		else if($campos[$numero_campo]=="")
		{
		    $campos[$numero_campo]="0";
		}
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 53 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="")
	    {
		if(floatval($campos[$numero_campo])>999999999
		    &&  $campos[$numero_campo]!="98" )
		 {
		     $campos[$numero_campo]="999999999";
		 }
		 else if(floatval($campos[$numero_campo])<1000
			 &&  $campos[$numero_campo]!="98")
		 {
		     $campos[$numero_campo]="1000";
		 }
	     
	    }
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 54
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	     if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 55 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 56 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 57
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	     if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 58 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="")
	    {
		if(floatval($campos[$numero_campo])>999999999
		    &&  $campos[$numero_campo]!="98" )
		 {
		     $campos[$numero_campo]="999999999";
		 }
		 else if(floatval($campos[$numero_campo])<1000
			 &&  $campos[$numero_campo]!="98")
		 {
		     $campos[$numero_campo]="1000";
		 }
		 
	    }
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 59 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>23
		&&  $campos[$numero_campo]!="98" )
	     {
		 $campos[$numero_campo]="23";
	     }
	     else if(floatval($campos[$numero_campo])<3
		     &&  $campos[$numero_campo]!="98")
	     {
		 $campos[$numero_campo]="3";
	     }
	     else if($campos[$numero_campo]=="")
	     {
		 $campos[$numero_campo]="3";
	     }
		
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 60
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		if(floatval($campos[$numero_campo])>10
		&&  $campos[$numero_campo]!="98" )
	     {
		 $campos[$numero_campo]="10";
	     }
	     else if(floatval($campos[$numero_campo])<0.5
		     &&  $campos[$numero_campo]!="98")
	     {
		 $campos[$numero_campo]="0.5";
	     }
	     else if($campos[$numero_campo]=="")
	     {
		 $campos[$numero_campo]="0.5";
	     }
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 61 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    if(floatval($campos[$numero_campo])>12
		&&  $campos[$numero_campo]!="98" )
	     {
		 $campos[$numero_campo]="12";
	     }
	     else if(floatval($campos[$numero_campo])<0.1
		     &&  $campos[$numero_campo]!="98")
	     {
		 $campos[$numero_campo]="0.1";
	     }
	     else if($campos[$numero_campo]=="")
	     {
		 $campos[$numero_campo]="0.1";
	     }
	}//if si existe campo
	
	
	//numero_orden 71  numero campo 62
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    //valor permitido
	    
	    $campo_n38=$campos[47];
		
	    $campo_n39=$campos[48];		
	    
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
	    
	    if($campos[$numero_campo]!="1"
	   && $campos[$numero_campo]!="2"
	   && $campos[$numero_campo]!="98"
	   && $campos[$numero_campo]!="97"
	   && $campos[$numero_campo]!="99"
	       && trim($campo_n39)=="98" )
	    {
		    $campos[$numero_campo]="98";
	    }		
	    else if($campos[$numero_campo]!="1"
	   && $campos[$numero_campo]!="2"
	   && $campos[$numero_campo]!="98"
	   && $campos[$numero_campo]!="97"
	   && $campos[$numero_campo]!="99"
		       && (intval($campo_n39)=="1"
			   || intval($campo_n39)=="2"
			   || intval($campo_n39)=="3"
			   || intval($campo_n39)=="4")
		       )
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if($campos[$numero_campo]!="1"
	   && $campos[$numero_campo]!="2"
	   && $campos[$numero_campo]!="98"
	   && $campos[$numero_campo]!="97"
	   && $campos[$numero_campo]!="99"
		       && trim($campo_n39)=="5"
		       && (trim($campo_n64)=="1" || trim($campo_n64)=="2")
		    )
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if(
		    $campos[$numero_campo]!="1"
	   && $campos[$numero_campo]!="2"
	   && $campos[$numero_campo]!="98"
	   && $campos[$numero_campo]!="97"
	   && $campos[$numero_campo]!="99"
		     && trim($campo_n39)=="5"
		    && (trim($campo_n64)!="1" && trim($campo_n64)!="2")
		    && (trim($campo_n62_1)=="1"
			|| trim($campo_n62_2)=="1"
			|| trim($campo_n62_3)=="1"
			|| trim($campo_n62_4)=="1"
			|| trim($campo_n62_5)=="1"
			|| trim($campo_n62_6)=="1"
			|| trim($campo_n62_7)=="1"
			|| trim($campo_n62_8)=="1"
			|| trim($campo_n62_9)=="1"
			|| trim($campo_n62_10)=="1"
			|| trim($campo_n62_11)=="1"
			)
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(
		    $campos[$numero_campo]!="1"
	   && $campos[$numero_campo]!="2"
	   && $campos[$numero_campo]!="98"
	   && $campos[$numero_campo]!="97"
	   && $campos[$numero_campo]!="99"
		     && trim($campo_n39)=="5"
		    && (trim($campo_n64)!="1" && trim($campo_n64)!="2")
		    && (trim($campo_n62_1)!="1"
			&& trim($campo_n62_2)!="1"
			&& trim($campo_n62_3)!="1"
			&& trim($campo_n62_4)!="1"
			&& trim($campo_n62_5)!="1"
			&& trim($campo_n62_6)!="1"
			&& trim($campo_n62_7)!="1"
			&& trim($campo_n62_8)!="1"
			&& trim($campo_n62_9)!="1"
			&& trim($campo_n62_10)!="1"
			&& trim($campo_n62_11)!="1"
			)
		    )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]!="1"
		&& $campos[$numero_campo]!="2"
		&& $campos[$numero_campo]!="98"
		&& $campos[$numero_campo]!="97"
		&& $campos[$numero_campo]!="99"
		)
	     {
		 $campos[$numero_campo]="98";
	     }
	    
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 62.1
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	     
	    
	    
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 62.2
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 62.3
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
		
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 62.4
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 62.5
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 62.6
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 62.7
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 62.8
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 62.9 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	//numero_orden 81  numero campo 62.10
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	//numero_orden 82  numero campo 62.11
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 63
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 63.1
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
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
	
	
	//numero_orden 85  numero campo 64
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 86  numero campo 65 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
		 
	}//if si existe campo
	
	
	//numero_orden 87  numero campo 66
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
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
	
	
	//numero_orden 88  numero campo 67
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="98"
	       && $campos[$numero_campo]!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 68 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="")
	    {
		if(floatval($campos[$numero_campo])>999999999
		    &&  $campos[$numero_campo]!="99" &&  $campos[$numero_campo]!="98" &&  $campos[$numero_campo]!="97")
		 {
		     $campos[$numero_campo]="999999999";
		 }
		 else if(floatval($campos[$numero_campo])<1000
			 &&  $campos[$numero_campo]!="99" &&  $campos[$numero_campo]!="98" &&  $campos[$numero_campo]!="97")
		 {
		     $campos[$numero_campo]="1000";
		 }
	    }
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 69
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 69.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 92  numero campo 69.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 93  numero campo 69.3
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 94  numero campo 69.4
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 95  numero campo 69.5 
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 96  numero campo 69.6 
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 97  numero campo 69.7 
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 98  numero campo 70 
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    if(floatval($campos[$numero_campo])>20
		&&  $campos[$numero_campo]!="98" )
	     {
		 $campos[$numero_campo]="20";
	     }
	     else if(floatval($campos[$numero_campo])<1
		     &&  $campos[$numero_campo]!="98")
	     {
		 $campos[$numero_campo]="1";
	     }
	     else if($campos[$numero_campo]=="")
	     {
		 $campos[$numero_campo]="1";
	     }
	}//if si existe campo
	
	
	//numero_orden 99  numero campo 70.1 
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 100  numero campo 70.2 
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 101  numero campo 70.3 
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 102  numero campo 70.4 
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 103  numero campo 70.5 
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 104  numero campo 70.6 
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
		if($campos[$numero_campo]!="1"
		   && $campos[$numero_campo]!="2"
		   && $campos[$numero_campo]!="98")
		{
		    $campos[$numero_campo]="98";
		}//fin if
	}//if si existe campo
	
	
	//numero_orden 105  numero campo 70.7 
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
		if(strlen($campos[$numero_campo])<=2
		   && $campos[$numero_campo]!="97"
		   && $campos[$numero_campo]!="98"
		   )
		{
		    $campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 106  numero campo 70.8
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
		if(strlen($campos[$numero_campo])<=2
		   && $campos[$numero_campo]!="97"
		   && $campos[$numero_campo]!="98"
		   )
		{
		    $campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 107  numero campo 70.9 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
		if(strlen($campos[$numero_campo])<=2
		   && $campos[$numero_campo]!="97"
		   && $campos[$numero_campo]!="98"
		   )
		{
		    $campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 108  numero campo 71 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	   if(is_numeric($campos[$numero_campo]))
	   {
	    if(floatval($campos[$numero_campo])>20
		&&  $campos[$numero_campo]!="98" )
	     {
		 $campos[$numero_campo]="20";
	     }
	     else if(floatval($campos[$numero_campo])<1)
	     {
		 $campos[$numero_campo]="1";//estaba cero deberia ser uno por validacion de valor permitido
	     }
	   }
	}//if si existe campo
	
	
	//numero_orden 109  numero campo 72
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 110  numero campo 73
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1);	
	}//if si existe campo
	
	
	//numero_orden 111  numero campo 74
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    if(floatval($campos[$numero_campo])>98 )
	     {
		 $campos[$numero_campo]="98";
	     }
	    /*
	    if(floatval($campos[$numero_campo])>5
		&&  $campos[$numero_campo]!="98" )
	     {
		 $campos[$numero_campo]="5";
	     }
	     else if(floatval($campos[$numero_campo])<1
		     &&  $campos[$numero_campo]!="98")
	     {
		 $campos[$numero_campo]="1";
	     }
	     else if($campos[$numero_campo]=="")
	     {
		 $campos[$numero_campo]="1";
	     }
	     */
		
	}//if si existe campo
	
	
	//numero_orden 112  numero campo 75 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
		
	    if($campos[$numero_campo]!="")
	    {
		if(floatval($campos[$numero_campo])>999999999
		     &&  $campos[$numero_campo]!="98" )
		 {
		     $campos[$numero_campo]="999999999";
		 }
		 else if(floatval($campos[$numero_campo])<1000
		    &&  $campos[$numero_campo]!="98")
		 {
		     $campos[$numero_campo]="1000";
		 }	    
	    }
		
	}//if si existe campo
	
	
	//numero_orden 113  numero campo 76
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
	    if(floatval($campos[$numero_campo])>12)
	     {
		 $campos[$numero_campo]="12";
	     }
	     else if(floatval($campos[$numero_campo])<1)
	     {
		 $campos[$numero_campo]="1";
	     }
	     else if($campos[$numero_campo]=="")
	     {
		 $campos[$numero_campo]="1";
	     }
		
	}//if si existe campo
	
	
	//numero_orden 114  numero campo 77
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="")
	    {
		if(floatval($campos[$numero_campo])>999999999 )
		 {
		     $campos[$numero_campo]="999999999";
		 }
		 else if(floatval($campos[$numero_campo])<1000)
		 {
		     $campos[$numero_campo]="1000";
		 }
	    }
	}//if si existe campo
	
	
	//numero_orden 115  numero campo 78 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 116  numero campo 79
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="4"
	       && $campos[$numero_campo]!="5"
	       && $campos[$numero_campo]!="6"
	       && $campos[$numero_campo]!="7"
	       && $campos[$numero_campo]!="8"
	       && $campos[$numero_campo]!="9"
	       && $campos[$numero_campo]!="98"
	       )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 117  numero campo 80 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]!="1"
	       && $campos[$numero_campo]!="2"
	       && $campos[$numero_campo]!="3"
	       && $campos[$numero_campo]!="4"
	       && $campos[$numero_campo]!="5"
	       && $campos[$numero_campo]!="98"
	       && $campos[$numero_campo]!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 118  numero campo 80.1 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	   
	    //aqui agrega el 1800-01-01
	    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-1,801);	    
	}//if si existe campo
	
	
	
	
	
	
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarERC


function reparacion_criterios_de_calidad_ERC(&$campos,
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
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
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
	//FIN PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	*/
	
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
	
	
	//numero_orden 29  numero campo 27.1 
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
		
		$campo_n27=$campos[28];
		if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n27)!=98 && intval($campo_n27)!=99 )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		
	}//if si existe campo
	
	//numero_orden 28  numero campo 27 
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
		$campo_n39=$campos[48];
		if(intval($campos[$numero_campo])==98 && intval($campo_n39)!=5)
		{
			$campos[$numero_campo]="99";
		}
		else if(intval($campos[$numero_campo])==99 && intval($campo_n39)==5)
		{
			$campos[$numero_campo]="98";
		}
		
		
	}//if si existe campo
	
	//CALCULO TFG
	$formula_cockcroft_gault=0.0;
	$formula_schwartz=0.0;
	
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
			$formula_schwartz=(0.50*$talla)/($creatinina);
		}
		else if($edad_years_formula<=12)
		{
			$formula_schwartz=(0.55*$talla)/($creatinina);
		}
		else if($edad_years_formula<=17)
		{
			if($sexo=="M")
			{
				$formula_schwartz=(0.70*$talla)/($creatinina);
			}
			else if($sexo=="F")
			{
				$formula_schwartz=(0.57*$talla)/($creatinina);
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
		$TFG=$formula_schwartz;
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
	
	
	//numero_orden 44  numero campo 35 
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
		$campo_n27_1=$campos[29];
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
		
		
		
		//echo "<script>alert('campo 35 antes vc ".$campos[$numero_campo]."');</script>";
		
		//si la formula se pudo aplicar remplaza el valor del registro por
		//el valor calculado de todas formas
		if($es_formula_valida)
		{
			$campos[$numero_campo]=$TFG;
		}//fin if es valido
		
		$se_cumplio_en_condicion_entidad_personalizada=false;
		$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();
		if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
		{
			//inferior en 1 year fecha de corte
			if($campo_n27_1!="1800-01-01"
			   && $campo_n27_1!="1845-01-01"
			   && floatval($campos[$numero_campo])>=60
			   && trim($campos[$numero_campo])!="999"
			   && trim($campos[$numero_campo])!="988"
			   && $verificador_1_year>=0)
			{
				$campos[$numero_campo]="999";
				$se_cumplio_en_condicion_entidad_personalizada=true;
			}		
			//inferior en 3 meses fecha de corte
			else if($campo_n27_1!="1800-01-01" && $campo_n27_1!="1845-01-01"
					&& floatval($campos[$numero_campo])<60
					&& trim($campos[$numero_campo])!="999"
					&& trim($campos[$numero_campo])!="988"
					&& $verificador_3_months>=0 )
			{
				$campos[$numero_campo]="999";
				$se_cumplio_en_condicion_entidad_personalizada=true;
			}//fin else
		}//fin if entidad personalizada
		
		//validaciones de calidad
		if($se_cumplio_en_condicion_entidad_personalizada==false)
		{
			if(trim($campos[$numero_campo])!="999"
					&& trim($campos[$numero_campo])!="988"
					&& "".floatval($TFG)!="".floatval($campos[$numero_campo])
					&& $es_formula_valida
					)
			{
				$campos[$numero_campo]=$TFG;
			}
			else if(trim($campos[$numero_campo])=="988"
					&& $TFG<15
					&& $es_formula_valida
					)
			{
				$campos[$numero_campo]=$TFG;
			}
			else if(trim($campos[$numero_campo])!="988" && trim($creatinina)=="98" )
			{
				$campos[$numero_campo]="988";
			}
			else if(trim($campos[$numero_campo])!="999" && trim($creatinina)=="99" )
			{
				$campos[$numero_campo]="999";
			}
		}//fin if se cumplio en entidad personalizada
		
		//echo "<script>alert('campo 35 vc despues ".$campos[$numero_campo]."');</script>";
	}//if si existe campo
	
	//numero_orden 47  numero campo 38
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
		
		//validacion de calidad
		$campo_n20=$campos[20];
		$campo_n18=$campos[17];
		$campo_n35=$campos[44];
		$campo_n27=$campos[28];
		
		if( floatval($campo_n35)>250 && floatval($campo_n35)!=988 && floatval($campo_n35)!=999 )
		{
			$campos[$numero_campo]="0";
		}
		else if( (floatval($campo_n35)<60 || intval($campo_n35)==988 )  )
		{
			$campos[$numero_campo]="1";
		}		
		else if( (floatval($campo_n35)>=60 && floatval($campo_n35)<=250 )  )
		{
			$campos[$numero_campo]="2";
		}
		else if(intval($campos[$numero_campo])!=3 &&  floatval($campo_n35)==999   )
		{
			$campos[$numero_campo]="3";
		}
	}//if si existe campo
	
	//numero_orden 48  numero campo 39
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
		
		//validacion de calidad
		$campo_n35=$campos[44];
		$campo_n27_1=$campos[29];
		$campo_n38=$campos[47];
		
		
		if(intval($campo_n35)>=90 && intval($campo_n35)<=250)
		{
		    $campos[$numero_campo]="1";
		}
		else if( intval($campo_n35)>=60 && intval($campo_n35)<90) 
		{
		    $campos[$numero_campo]="2";
		}
		else if( intval($campo_n35)>=30 && intval($campo_n35)<60) 
		{
		    $campos[$numero_campo]="3";
		}
		else if( intval($campo_n35)>=15 && intval($campo_n35)<30) 
		{
		    $campos[$numero_campo]="4";
		}
		else if( intval($campo_n35)<15  ) 
		{
		    $campos[$numero_campo]="5";
		}
		else if(intval($campo_n35)==988 )
		{
		    $campos[$numero_campo]="98";
		}
		else if(intval($campo_n35)==999 )
		{
		    $campos[$numero_campo]="99";
		}
		
		
		
		
		
	}//if si existe campo
	
	
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
		$campo_n38=intval($campos[47]);
		if($campo_n38==0 && trim($campos[$numero_campo])!="1800-01-01" && trim($campos[$numero_campo])!="1845-01-01")
		{
			$campos[$numero_campo]="1845-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 17  numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
		
		$campo_n20=$campos[20];
		$campo_n38=$campos[47];
		if(intval($campos[$numero_campo])==2 && intval($campo_n20)==2  && intval($campo_n38)!=1 )
		{
			$campos[$numero_campo]="1";
		}
	}//if si existe campo
	
	
	//numero_orden 18  numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n18=$campos[17];
		$campo_n19=$campos[18];
		if(intval($campo_n18)==1 && trim($campo_n19)=="1845-01-01" )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		else if(intval($campo_n18)!=1 && trim($campo_n19)!="1845-01-01" )
		{
			$campos[$numero_campo]="1845-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 19  numero campo 19.1 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n18=$campos[17];
		
		
		if(intval($campo_n18)!=1 && intval($campos[$numero_campo])!=98 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])==98 && intval($campo_n18)==1 )
		{
			$campos[$numero_campo]="1000";
		}
	}//if si existe campo
	
	
	//numero_orden 20  numero campo 20 
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
		
		//validacion de calidad
		$campo_n18=$campos[17];
		$campo_n38=$campos[47];
		if(intval($campos[$numero_campo])==2 && intval($campo_n18)==2  && intval($campo_n38)!=1 )
		{
			$campos[$numero_campo]="1";
		}
	}//if si existe campo
	
	
	//numero_orden 21  numero campo 21 
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
		
		
		//validacion de calidad
		$campo_n20=$campos[20];
		if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n20)==1 )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		else if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n20)!=1 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 22  numero campo 21.1 
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
		
		
		//validacion de calidad
		$campo_n20=$campos[20];
		/*
		if(intval($campos[$numero_campo])==98 && intval($campo_n20)==1 )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0105009"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",0105009,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}
		*/
		
		if(intval($campos[$numero_campo])!=98 && intval($campo_n20)!=1 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 23  numero campo 22
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    $campo_n20=$campos[20];
	    $campo_n18=$campos[17];
	    $campo_n38=$campos[47];
	    if($campos[$numero_campo]=="98" && $campo_n38=="1" && ($campo_n18=="1" || $campo_n20=="1"))
	    {
		$campos[$numero_campo]="1";
	    }
	    else if($campos[$numero_campo]=="98" && $campo_n38=="1" && ($campo_n18!="1" && $campo_n20!="1"))
	    {
		$campos[$numero_campo]="5";
	    }
		
		
		
		
	}//if si existe campo
	
	
	//numero_orden 24  numero campo 23 
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		
		
		
	}//if si existe campo
	
	
	//numero_orden 25  numero campo 24 
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 26  numero campo 25
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 27  numero campo 26 
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	
	
	
	
	
	//numero_orden 30  numero campo 28 
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		
		
		//validacion de calidad
		$campo_n20=$campos[20];
		if(intval($campos[$numero_campo])!=98 && intval($campo_n20)==2)
		{
			$campos[$numero_campo]="98";
		}
		else if((intval($campos[$numero_campo])==98 ) && intval($campo_n20)==1)
		{
			$campos[$numero_campo]="99";
		}
		
	}//if si existe campo
	
	
	//numero_orden 31  numero campo 28.1 
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{		
		$campo_n28=$campos[30];
		if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n28)!=98 && intval($campo_n28)!=99 )
		{
			$campos[$numero_campo]="1800-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 29 
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 29.1 
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
		
		$campo_n29=$campos[32];
		if(trim($campos[$numero_campo])=="1845-01-01"
		   && intval($campo_n29)!=9999 && intval($campo_n29)!=9888
		   )
		{
		    $campos[$numero_campo]="1800-01-01";
			
		}
		
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 30 
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
		
				
		$campo_n35=$campos[44];
		$campo_n39=$campos[48];
		if(intval($campos[$numero_campo])==988 && intval($campo_n39)!=5)
		{
			$campos[$numero_campo]="999";
		}
		else if(intval($campos[$numero_campo])==999 && intval($campo_n39)==5)
		{
			$campos[$numero_campo]="988";
		}
		
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 30.1 
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
		
		
		//validacion de calidad
		$campo_n30=$campos[34];
		/*
		if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n30)!=98  )
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0105015"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0105,".$array_grupo_validacion["0105"].",0105015,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}*/
		
		if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n30)==988  )
		{
			 $campos[$numero_campo]="1845-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 31 
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
		
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 31.1 
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n31=$campos[36];
		
		
		if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n31)==999  )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n31)!=999  )
		{
			$campos[$numero_campo]="1800-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 32 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		
		
		
		
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 32.1 
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n32=$campos[38];
		
		
		if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n32)==999  )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n32)!=999  )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 33 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 33.1 
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
		$campo_n33=$campos[40];
		
		
		if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n33)==999  )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n33)!=999  )
		{
			$campos[$numero_campo]="1800-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 34 
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n39=$campos[48];
		if(intval($campos[$numero_campo])!=988 && (intval($campo_n39)==1 || intval($campo_n39)==2 || intval($campo_n39)==3 || intval($campo_n39)==98 )  )
		{
			$campos[$numero_campo]="988";
		}			
		else if(intval($campos[$numero_campo])==988 && (intval($campo_n39)==4 || intval($campo_n39)==5)   )
		{
			$campos[$numero_campo]="999";
		}
		
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 34.1 
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n34=$campos[43];
		if(trim($campos[$numero_campo])!="1845-01-01"
		   && (intval($campo_n34)==988 || intval($campo_n34)==999)
		   )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01"
			&& intval($campo_n34)!=988 && intval($campo_n34)!=999
			)
		{
			$campos[$numero_campo]="1800-01-01";
		}	
	}//if si existe campo
	
	
	
	
	
	//numero_orden 45  numero campo 36 
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 37 
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	
	
	
	
	//numero_orden 49  numero campo 40 
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
		$campo_n39=$campos[48];
		$campo_n35=$campos[44];
		/*
		if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n35)!=988 && intval($campo_n35)>=15 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" && (intval($campo_n35)==988 ||  intval($campo_n35)<15)  )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		*/
		
		if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n39)!=5 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5  )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 50  numero campo 41
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		if(intval($campos[$numero_campo])!=98  && (intval($campo_n38)==0 || intval($campo_n38)==3) )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])==98 && (intval($campo_n38)==1 || intval($campo_n38)==2) )
		{
			$campos[$numero_campo]="99";
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 42
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 43
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		$campo_n35=$campos[44];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98 && intval($campo_n35)!=988 && intval($campo_n35)>=15 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 44 
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		$campo_n35=$campos[44];
		$campo_n64=$campos[85];
		$campo_n39=$campos[48];
		if(trim($campos[$numero_campo])!="1845-01-01" && (intval($campo_n39)!=5) )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n39)==5   )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 45 
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		$campo_n35=$campos[44];
		$campo_n46=$campos[55];
		$campo_n49=$campos[58];
		/*
		if(trim($campos[$numero_campo])!="1845-01-01" && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		else
		*/
		if(trim($campos[$numero_campo])!="1845-01-01"
			   && intval($campo_n46)==98  && intval($campo_n49)==98
			   )
		{
		    $campos[$numero_campo]="1845-01-01";
		}
		else if(trim($campos[$numero_campo])=="1845-01-01" 
			&& (intval($campo_n46)==1 || intval($campo_n46)==2
			   || intval($campo_n49)==1 || intval($campo_n49)==2)
			)
		{
			$campos[$numero_campo]="1800-01-01";
		}
		/*
		else if(trim($campos[$numero_campo])=="1845-01-01" && intval($campo_n35)<15 )
		{
			$campos[$numero_campo]="1800-01-01";
		}
		else if(trim($campos[$numero_campo])!="1845-01-01" && intval($campo_n35)>=15 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
		*/
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 46 
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		$campo_n35=$campos[44];
		$campo_n64=$campos[85];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98 && intval($campo_n35)>=15 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98 && (intval($campo_n64)==1 || intval($campo_n64)==2) )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 47 
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n46=$campos[55];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n46)==98 )
		{
			$campos[$numero_campo]="98";
		}
		
	}//if si existe campo
	
	
	//numero_orden 57  numero campo 48 
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		$campo_n46=$campos[55];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n46)==98 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 58  numero campo 49 
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n35=$campos[44];
		$campo_n38=$campos[47];
		$campo_n46=$campos[55];
		$campo_n64=$campos[85];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n46)!=98 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98 && intval($campo_n35)>=15 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98 && (intval($campo_n64)==1 || intval($campo_n64)==2) )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 50 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n49)==98 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 51 
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n49)==98 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 52
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n49)==98 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 53 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n49)==98 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 54
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 55 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(trim($campos[$numero_campo])!="1845-01-01"  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 56 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
		$campo_n38=$campos[47];
		$campo_n49=$campos[58];
		if(trim($campos[$numero_campo])!="1845-01-01"  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="1845-01-01";
		}
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 57
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		
		
		$campo_n39=$campos[48];
		
		
		$campo_n44=$campos[53];
		
		
		
		$campo_n46=$campos[55];
		
		
		$campo_n49=$campos[58];
		
		
		$campo_n35=$campos[44];
		
		$campo_n64=$campos[85];
		
		if(intval($campos[$numero_campo])!=98  && intval($campo_n39)!=5)
		{
			$campos[$numero_campo]="98";
		}
		if(intval($campos[$numero_campo])!=98  && intval($campo_n44)=="1845-01-01")
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])==1  && intval($campo_n46)!=98 )
		{
			$campos[$numero_campo]="2";
		}
		else if(intval($campos[$numero_campo])==1  && intval($campo_n49)!=98 )
		{
			$campos[$numero_campo]="2";
		}		
		else if(intval($campos[$numero_campo])==1  && intval($campo_n64)!=5 )
		{
			$campos[$numero_campo]="2";
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 58 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
		
		//validacion de calidad
		$campo_n38=$campos[47];
		$campo_n57=$campos[66];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n57)!=1 )
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 59 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    
		$campo_n38=$campos[47];
		
		$campo_n46=$campos[55];
		
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98   && intval($campo_n46)==98 && intval($campo_n49)==98)
		{
			$campos[$numero_campo]="98";
		}
		
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 60
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
		$campo_n38=$campos[47];
		
		$campo_n46=$campos[55];
		
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98  && intval($campo_n46)==98 && intval($campo_n49)==98)
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 61 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
		$campo_n38=$campos[47];
		
		$campo_n46=$campos[55];
		
		$campo_n49=$campos[58];
		if(intval($campos[$numero_campo])!=98  && intval($campo_n38)==0 )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])!=98   && intval($campo_n46)==98 && intval($campo_n49)==98)
		{
			$campos[$numero_campo]="98";
		}
	}//if si existe campo
	
	
	//numero_orden 71  numero campo 62
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
		//validacion de calidad
		$campo_n38=$campos[47];
		
		$campo_n39=$campos[48];		
		
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
		
		if(trim($campos[$numero_campo])!="98"  && trim($campo_n39)=="98" )
		{
			$campos[$numero_campo]="98";
		}		
		else if(trim($campos[$numero_campo])!="97"
			   && (intval($campo_n39)=="1"
			       || intval($campo_n39)=="2"
			       || intval($campo_n39)=="3"
			       || intval($campo_n39)=="4")
			   )
		{
			$campos[$numero_campo]="97";
		}
		else if(trim($campos[$numero_campo])!="97"
			   && trim($campo_n39)=="5"
			   && (trim($campo_n64)=="1" || trim($campo_n64)=="2")
			)
		{
			$campos[$numero_campo]="97";
		}
		else if(
			(trim($campos[$numero_campo])!="1"
			 && trim($campos[$numero_campo])!="2")
			 && trim($campo_n39)=="5"
			&& (trim($campo_n64)!="1" && trim($campo_n64)!="2")
			&& (trim($campo_n62_1)=="1"
			    || trim($campo_n62_2)=="1"
			    || trim($campo_n62_3)=="1"
			    || trim($campo_n62_4)=="1"
			    || trim($campo_n62_5)=="1"
			    || trim($campo_n62_6)=="1"
			    || trim($campo_n62_7)=="1"
			    || trim($campo_n62_8)=="1"
			    || trim($campo_n62_9)=="1"
			    || trim($campo_n62_10)=="1"
			    || trim($campo_n62_11)=="1"
			    )
			)
		{
			$campos[$numero_campo]="2";
		}
		else if(
			(trim($campos[$numero_campo])!="1"
			 && trim($campos[$numero_campo])!="2")
			 && trim($campo_n39)=="5"
			&& (trim($campo_n64)!="1" && trim($campo_n64)!="2")
			&& (trim($campo_n62_1)!="1"
			    && trim($campo_n62_2)!="1"
			    && trim($campo_n62_3)!="1"
			    && trim($campo_n62_4)!="1"
			    && trim($campo_n62_5)!="1"
			    && trim($campo_n62_6)!="1"
			    && trim($campo_n62_7)!="1"
			    && trim($campo_n62_8)!="1"
			    && trim($campo_n62_9)!="1"
			    && trim($campo_n62_10)!="1"
			    && trim($campo_n62_11)!="1"
			    )
			)
		{
			$campos[$numero_campo]="1";
		}
		
			
			
		
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 62.1
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 62.2
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 62.3
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 62.4
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 62.5
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 62.6
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 62.7
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 62.8
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 62.9 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	//numero_orden 81  numero campo 62.10
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	//numero_orden 82  numero campo 62.11
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
		
	    $campo_n62=$campos[71];
	    if(trim($campos[$numero_campo])!="98"
		&& (trim($campo_n62)=="97"
		    || trim($campo_n62)=="98"
		    || trim($campo_n62)=="99"
		    )
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && (trim($campo_n62)=="1" || trim($campo_n62)=="2")
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="1" && trim($campo_n62)=="1"  )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 63
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n39=$campos[48];
	    $campo_n38=$campos[47];
		
	    if( trim($campos[$numero_campo])!="1845-01-01"  && intval($campo_n39)!=5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1845-01-01"  && intval($campo_n39)==5)
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 63.1
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n39=$campos[48];
	    $campo_n63=$campos[83];
	    if(intval($campos[$numero_campo])!=98   && trim($campo_n63)=="1845-01-01")
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 64
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n46=$campos[55];
	    $campo_n49=$campos[58];
	    $campo_n63=$campos[83];
	    /*
	    if(intval($campos[$numero_campo])!=5  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="5";
	    }
	    else if(intval($campos[$numero_campo])!=5  && (intval($campo_n39)==98 || intval($campo_n39)==99))
	    {
		    $campos[$numero_campo]="5";
	    }
	    else if(intval($campos[$numero_campo])!=5  && intval($campo_n46)!=98)
	    {
		    $campos[$numero_campo]="5";
	    }
	    else if(intval($campos[$numero_campo])!=5  && intval($campo_n49)!=98)
	    {
		    $campos[$numero_campo]="5";
	    }
	    */
	}//if si existe campo
	
	
	//numero_orden 86  numero campo 65 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n10=$campos[9];
	    if(intval($campos[$numero_campo])!=98  && (intval($campo_n64)==2 || intval($campo_n64)==4) )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  &&  (intval($campo_n64)==1 || intval($campo_n64)==3))
	    {
		    $campos[$numero_campo]=$campo_n10;
	    }
	    else if(intval($campos[$numero_campo])==98  && (intval($campo_n64)==2 || intval($campo_n64)==4) )
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 87  numero campo 66
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n39=$campos[48];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && (intval($campo_n64)!=5) )
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 88  numero campo 67
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n64)!=5 )
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 68 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    
	    
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n64)!=5 )
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if(intval($campos[$numero_campo])!=99  && (intval($campo_n64)==2  || intval($campo_n64)==4))
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 69
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n64)!=5 )
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 69.1
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 92  numero campo 69.2
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 93  numero campo 69.3
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 94  numero campo 69.4
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 95  numero campo 69.5 
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 96  numero campo 69.6 
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 97  numero campo 69.7 
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    $campo_n69=$campos[90];
	    if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"  && trim($campos[$numero_campo])!="1800-01-01"   && intval($campo_n69)!=1 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 98  numero campo 70 
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden 99  numero campo 70.1 
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 100  numero campo 70.2 
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 101  numero campo 70.3 
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 102  numero campo 70.4 
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 103  numero campo 70.5 
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 104  numero campo 70.6 
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden 105  numero campo 70.7 
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="97";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden 106  numero campo 70.8
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="97";
	    }
	}//if si existe campo
	
	
	//numero_orden 107  numero campo 70.9 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n70=$campos[98];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n70)==98 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n70)!=98 )
	    {
		    $campos[$numero_campo]="97";
	    }
	}//if si existe campo
	
	
	//numero_orden 108  numero campo 71 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n64)!=5 )
	    {
		    $campos[$numero_campo]="1";//estaba cero deberia ser uno por validacion de valor permitido
	    }
	}//if si existe campo
	
	
	//numero_orden 109  numero campo 72
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n71=$campos[108];
	    /*
	    if(trim($campos[$numero_campo])!="1845-01-01"   && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    */
	    if(trim($campos[$numero_campo])!="1845-01-01"   && intval($campo_n71)==98 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1845-01-01"   && intval($campo_n71)!=98 )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 110  numero campo 73
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n39=$campos[48];
	    $campo_n64=$campos[85];
	    /*
	    if(trim($campos[$numero_campo])!="1845-01-01"    && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    */
	    if(trim($campos[$numero_campo])=="1845-01-01"    && (intval($campo_n64)==3 || intval($campo_n64)==4) )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"   && (intval($campo_n64)==1 || intval($campo_n64)==2) )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden 111  numero campo 74
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    if(intval($campos[$numero_campo])!=98  && intval($campo_n64)==5 )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(intval($campos[$numero_campo])==98  && intval($campo_n64)!=5 )
	    {
		    $campos[$numero_campo]="1";
	    }
	}//if si existe campo
	
	
	//numero_orden 112  numero campo 75 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n64=$campos[85];
	    if($campos[$numero_campo]!="")
	    {
		if(intval($campos[$numero_campo])!=98  && (intval($campo_n64)==5) )
		{
			$campos[$numero_campo]="98";
		}
		else if(intval($campos[$numero_campo])==98  && (intval($campo_n64)!=5) )
		{
			$campos[$numero_campo]="1000";
		}
	    }
	}//if si existe campo
	
	
	//numero_orden 113  numero campo 76
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    $campo_n18=$campos[17];
	    $campo_n20=$campos[20];
	    /*
	    if((intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>12)
	       && ($campo_n18=="1" || $campo_n20=="1" || $campo_n38=="1")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    */
	}//if si existe campo
	
	
	//numero_orden 114  numero campo 77
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    if(intval($campos[$numero_campo])!=0  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//numero_orden 115  numero campo 78 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	//numero_orden 116  numero campo 79
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
	}//if si existe campo
	
	
	//numero_orden 117  numero campo 80 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=$campos[47];
	    if(intval($campos[$numero_campo])==1  && intval($campo_n38)==0 )
	    {
		    $campos[$numero_campo]="5";
	    }
	}//if si existe campo
	
	
	//numero_orden 118  numero campo 80.1 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n80=$campos[117];
	    if(trim($campos[$numero_campo])!="1845-01-01"    && intval($campo_n80)==98 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1845-01-01"   && intval($campo_n80)!=98 )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	
	
	
	
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  reparacion criterios calidad ERC



?>