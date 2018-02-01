<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

require_once '../utiles/conf_personalizada.php';


function reparador_campo_obligatorio_CANCER(&$campos,$cod_eapb,$nlinea,&$consecutivo_errores,
						  $array_tipo_validacion,$array_grupo_validacion,
						  $array_detalle_validacion,$nombre_archivo_registrado,
						  $fecha_de_corte,$cod_prestador,
						  $cod_eapb,&$diccionario_identificacion,
						  &$diccionario_identificacion_lineas,
						  &$coneccionBD, $array_numero_campo_bd)
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();

	$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='03' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0301' ORDER BY numero_de_orden ";
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
	
	
	
	$cont_corrector_notacion_cientifica=0;
	while($cont_corrector_notacion_cientifica<count($campos))
	{
	    if(!ctype_digit(trim($campos[$cont_corrector_notacion_cientifica]))
	       && is_numeric(trim($campos[$cont_corrector_notacion_cientifica])))
	    {
		    $antes=$campos[$cont_corrector_notacion_cientifica];
		    
		    $campos[$cont_corrector_notacion_cientifica]="".convert_to_standard_notation($campos[$cont_corrector_notacion_cientifica]);
		    
		    $campos[$cont_corrector_notacion_cientifica]=str_replace(",","",$campos[$cont_corrector_notacion_cientifica]);
		    
		    $despues=$campos[$cont_corrector_notacion_cientifica];
		    //echo "<script>alert('$antes $despues');</script>";
	    }
	    $cont_corrector_notacion_cientifica++;
	}
	
	//CAMPO 20 CONTRA FECHA DE CORTE
	$campo_n20=$campos[19];
	
	//solo year
			
	$array_fecha_campo_20=explode("-",$campo_n20);
	$verificar_validez_para_comparar_fecha_anterior=true;
	
	
	$year_campo_20=$array_fecha_campo_20[0];
	$fecha_de_corte_array=explode("-",$fecha_de_corte);
	$year_fecha_de_corte=$fecha_de_corte_array[0];
	$corresponde_year_campo20_con_year_fecha_corte=false;
	if(ctype_digit($year_campo_20) && ctype_digit($year_fecha_de_corte))
	{
	    if(trim($year_campo_20)==trim($year_fecha_de_corte))
	    {
		$corresponde_year_campo20_con_year_fecha_corte=true;
	    }
	}
	//fin solo year
	
	$campo20_contra_fecha_corte_global=0;
	$campo20_contra_fecha_corte_global=diferencia_dias_entre_fechas($campo_n20,$fecha_de_corte);
	$campo20_es_fecha_calendario=0;
	$campo20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n20,"1900-12-31");
	//FIN CAMPO 20 CONTRA FECHADE CORTE
	
	
	//numero_orden_desde_cero 0 numero orden 1 numero campo 1 vcampoenblanco
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		//no se coloca debe salir error
				
	}//if si existe campo
	
	//numero_orden_desde_cero 1 numero orden 2 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="NONE";
		}
		
	}//if si existe campo
	
	//numero_orden_desde_cero 2 numero orden 3 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		//no se coloca debe salir error
	}//if si existe campo
	
	
	//numero_orden_desde_cero 3 numero orden 4 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="NOAP";
		}
	}//if si existe campo
	
	
	//numero_orden_desde_cero 4 numero orden 5 numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
		//no se coloca debe salir error
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 5 numero orden 6 numero campo 6 Numero ID
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		//no se coloca debe salir error
	}//if si existe campo
	
	
	//numero_orden_desde_cero 6 numero orden 7 numero campo 7 
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
		//no se coloca debe salir error
	}//if si existe campo
	
	
	//numero_orden_desde_cero 7 numero orden 8 numero campo 8 
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		//no se coloca debe salir error
	}//if si existe campo
	
	
	//numero_orden_desde_cero 8 numero orden 9 numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="9999";
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 9 numero orden 10 numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			//corrige previamente intentando buscar el regimen que le corresponde si no tiene
			//en base de datos
			$campos[$numero_campo]="N";
		}
	}//if si existe campo
	
	
	//numero_orden_desde_cero 10 numero orden 11 numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]=$cod_eapb;
		}
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 11 numero orden 12 numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="6";
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 12 numero orden 13 numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="61";
		}
	}//if si existe campo
	
	
	//numero_orden_desde_cero 13 numero orden 14 numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="76001";
		}
	}//if si existe campo
	
	
	//numero_orden_desde_cero 14 numero orden 15 numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		//echo "<script>alert('campo 15 num ord 14 vacio');</script>";
		$campos[$numero_campo]="0000000000";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 15 numero orden 16 numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{		
		//solo acepta fecha
	}//if si existe campo
	
	
	//numero_orden_desde_cero 16 numero orden 17 numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    //se aplico correccion por fuera debido a que debia crear archivo con los registros separados
	}//if si existe campo
	
	
	//numero_orden_desde_cero 17 numero orden 18 numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 18 numero orden 19 numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 19 numero orden 20 numero campo 20 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	$campo_ant_20=$campos[19];
	$c20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_20,"1900-12-31");
	////$fecha_primer_dia_corte=$year_fecha_de_corte."-01-01";
	$mes_fecha_de_corte=$fecha_de_corte_array[1];
	$fecha_primer_dia_corte=$year_fecha_de_corte."-".$mes_fecha_de_corte."-01";
	$c20_es_mayor_primer_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_20,$fecha_primer_dia_corte);
	$c20_es_menor_ultimo_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_20,$fecha_de_corte);
	
	//numero_orden_desde_cero 20 numero orden 21 numero campo 21 vcampoenblanco
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 21 numero orden 22 numero campo 22 vcampoenblanco
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 22 numero orden 23 numero campo 23 vcampoenblanco
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }//FIN IF
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 23 numero orden 24 numero campo 24 vcampoenblanco
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="1845-01-01";
	    }//campo no esta en blanco
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 24 numero orden 25 numero campo 25 vcampoenblanco
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
	    
	    //campo obligatorio
	    
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }//campo no esta en blanco
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 25 numero orden 26 numero campo 26 vcampoenblanco
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 26 numero orden 27 numero campo 27 vcampoenblanco
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 27 numero orden 28 numero campo 28 vcampoenblanco
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 28 numero orden 29 numero campo 29 vcampoenblanco
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 29 numero orden 30 numero campo 30 vcampoenblanco
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }    
	       
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 30 numero orden 31 numero campo 31 vcampoenblanco
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 31 numero orden 32 numero campo 32 vcampoenblanco
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 32 numero orden 33 numero campo 33 vcampoenblanco
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }		

	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 33 numero orden 34 numero campo 34 vcampoenblanco
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 34 numero orden 35 numero campo 35 vcampoenblanco
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="" )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 35 numero orden 36 numero campo 36 vcampoenblanco
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 36 numero orden 37 numero campo 37 vcampoenblanco
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	$campo_n7=trim($campos[6]);
	$array_fecha_nacimiento=explode("-",$campo_n7);
	$array_fecha_corte_para_nacimiento=explode("-",$fecha_de_corte);
	$array_edad_fc=array();
	$edad_en_year_calc_fc=0;
	if(count($array_fecha_nacimiento)==3)
	{
	    $array_edad_fc=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte_para_nacimiento[2]."-".$array_fecha_corte_para_nacimiento[1]."-".$array_fecha_corte_para_nacimiento[0]);
	    $edad_en_year_calc_fc=intval($array_edad_fc['y']);
	}

	//numero_orden_desde_cero 37 numero orden 38 numero campo 38 vcampoenblanco
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 38 numero orden 39 numero campo 39 vcampoenblanco
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 39 numero orden 40 numero campo 40 vcampoenblanco
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="99";
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 40 numero orden 41 numero campo 41 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="99";
		}
		
	}//if si existe campo
	
	//orden 44, 42,43
	
	//numero_orden_desde_cero 43 numero orden 44 numero campo 44 vcampoenblanco
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="99";
		}
	}//if si existe campo
	
	//numero_orden_desde_cero 41 numero orden 42 numero campo 42 vcampoenblanco
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 42 numero orden 43 numero campo 43 vcampoenblanco
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    
	    
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 44 numero orden 45 numero campo 45 vcampoenblanco
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{	    
	    if($campos[$numero_campo]=="")
	    {
	    	//c3
			$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	//CONSULTA EN TABLA DE CODIGO HEMOLINFATICO
	$campo_n17=trim($campos[16]);
	$consulta_hemolinfatico="";
	$consulta_hemolinfatico.="SELECT * FROM gioss_ca_hematolinfatico WHERE codigo='".$campo_n17."' ; ";
	$resultado_hemolinfatico=$coneccionBD->consultar2_no_crea_cierra($consulta_hemolinfatico);
	//FIN CONSULTA EN TABLA DE CODIGO HEMOLINFATICO
	
	//numero_orden_desde_cero 45 numero orden 46 numero campo 46 vcampoenblanco 
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	//fincampo 45 numero orden 46 numero campo 46 vcampoenblanco 
	
	
	//numero_orden_desde_cero 46 numero orden 47 numero campo 46.1 vcampoenblanco
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 47 numero orden 48 numero campo 46.2 vcampoenblanco
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 48 numero orden 49 numero campo 46.3 vcampoenblanco
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 49 numero orden 50 numero campo 46.4 vcampoenblanco
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 50 numero orden 51 numero campo 46.5 vcampoenblanco
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 51 numero orden 52 numero campo 46.6 vcampoenblanco
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 52 numero orden 53 numero campo 46.7 vcampoenblanco
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 53 numero orden 54 numero campo 46.8 vcampoenblanco
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
	    	//c4
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 54 numero orden 55 numero campo 47 vcampoenblanco
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 55 numero orden 56 numero campo 48 vcampoenblanco
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{		
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if

	}//if si existe campo
	
	
	//numero_orden_desde_cero 56 numero orden 57 numero campo 49 vcampoenblanco
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 57 numero orden 58 numero campo 50 vcampoenblanco
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 58 numero orden 59 numero campo 51 vcampoenblanco
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 59 numero orden 60 numero campo 52 vcampoenblanco
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 60 numero orden 61 numero campo 53 vcampoenblanco
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 61 numero orden 62 numero campo 53.1 vcampoenblanco
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 62 numero orden 63 numero campo 53.2 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 63 numero orden 64 numero campo 53.3 
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 64 numero orden 65 numero campo 53.4 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 65 numero orden 66 numero campo 53.5 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 66 numero orden 67 numero campo 53.6 
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 67 numero orden 68 numero campo 53.7 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 68 numero orden 69 numero campo 53.8 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 69 numero orden 70 numero campo 53.9 
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 70 numero orden 71 numero campo 53.10 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 71 numero orden 72 numero campo 53.11 
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 72 numero orden 73 numero campo 53.12 
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 73 numero orden 74 numero campo 53.13 
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 74 numero orden 75 numero campo 53.14 
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 75 numero orden 76 numero campo 53.15 
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 76 numero orden 77 numero campo 53.16 
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 77 numero orden 78 numero campo 53.17 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 78 numero orden 79 numero campo 53.18 
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 79 numero orden 80 numero campo 53.19
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 80 numero orden 81 numero campo 53.20 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 81 numero orden 82 numero campo 53.21 
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 82 numero orden 83 numero campo 53.22 
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 83 numero orden 84 numero campo 53.23 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 84 numero orden 85 numero campo 53.24 
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 85 numero orden 86 numero campo 53.25 
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 86 numero orden 87 numero campo 53.26 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 87 numero orden 88 numero campo 53.27 
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 88 numero orden 89 numero campo 53.28
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 89 numero orden 90 numero campo 53.29 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 90 numero orden 91 numero campo 53.30 
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 91 numero orden 92 numero campo 53.31 
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 92 numero orden 93 numero campo 53.32 vcampoenblanco
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 93 numero orden 94 numero campo 54 vcampoenblanco
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	     
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 94 numero orden 95 numero campo 55 vcampoenblanco
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 95 numero orden 96 numero campo 56 vcampoenblanco
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 96 numero orden 97 numero campo 57 vcampoenblanco
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 97 numero orden 98 numero campo 58 vcampoenblanco
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 98 numero orden 99 numero campo 59 vcampoenblanco
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 99 numero orden 100 numero campo 60 vcampoenblanco
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	$campo20_contra_2015=diferencia_dias_entre_fechas($campo_n20,"2015-01-01");
	
	//numero_orden_desde_cero 100 numero orden 101 numero campo 61 vcampoenblanco
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 101 numero orden 102 numero campo 62 vcampoenblanco
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 102 numero orden 103 numero campo 63 vcampoenblanco
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 103 numero orden 104 numero campo 64 vcampoenblanco
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 104 numero orden 105 numero campo 65 vcamoenblanco
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 105 numero orden 106 numero campo 66 vcampoenblanco
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
			$campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 106 numero orden 107 numero campo 66.1 vcampoenblanco
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 107 numero orden 108 numero campo 66.2 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 108 numero orden 109 numero campo 66.3 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 109 numero orden 110 numero campo 66.4 
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 110 numero orden 111 numero campo 66.5 
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 111 numero orden 112 numero campo 66.6 
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 112 numero orden 113 numero campo 66.7 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 113 numero orden 114 numero campo 66.8 
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 114 numero orden 115 numero campo 66.9 
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 115 numero orden 116 numero campo 66.10 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 116 numero orden 117 numero campo 66.11 
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 117 numero orden 118 numero campo 66.12 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 118 numero orden 119 numero campo 66.13 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 119 numero orden 120 numero campo 66.14 
	$numero_campo=119;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 120 numero orden 121 numero campo 66.15 
	$numero_campo=120;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 121 numero orden 122 numero campo 66.16 
	$numero_campo=121;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 122 numero orden 123 numero campo 66.17 
	$numero_campo=122;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campos
	
	
	//numero_orden_desde_cero 123 numero orden 124 numero campo 66.18 
	$numero_campo=123;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 124 numero orden 125 numero campo 66.19 
	$numero_campo=124;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 125 numero orden 126 numero campo 66.20 
	$numero_campo=125;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 126 numero orden 127 numero campo 66.21 
	$numero_campo=126;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 127 numero orden 128 numero campo 66.22 
	$numero_campo=127;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 128 numero orden 129 numero campo 66.23 
	$numero_campo=128;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 129 numero orden 130 numero campo 66.24 
	$numero_campo=129;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 130 numero orden 131 numero campo 66.25 
	$numero_campo=130;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 131 numero orden 132 numero campo 66.26 
	$numero_campo=131;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 132 numero orden 133 numero campo 66.27 
	$numero_campo=132;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 133 numero orden 134 numero campo 66.28 
	$numero_campo=133;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 134 numero orden 135 numero campo 66.29 
	$numero_campo=134;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 135 numero orden 136 numero campo 66.30 
	$numero_campo=135;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 136 numero orden 137 numero campo 66.31 
	$numero_campo=136;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 137 numero orden 138 numero campo 66.32 
	$numero_campo=137;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 138 numero orden 139 numero campo 67 vcampoenblanco
	$numero_campo=138;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 139 numero orden 140 numero campo 68 vcampoenblanco
	$numero_campo=139;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 140 numero orden 141 numero campo 69 vcampoenblanco
	$numero_campo=140;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 141 numero orden 142 numero campo 70 vcampoenblanco
	$numero_campo=141;
	if(isset($campos[$numero_campo]))
	{
	    
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 142 numero orden 143 numero campo 71 vcampoenblanco
	$numero_campo=142;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 143 numero orden 144 numero campo 72 vcampoenblanco
	$numero_campo=143;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 144 numero orden 145 numero campo 73 vcampoenblanco
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo

	//numero_orden_desde_cero 145 numero orden 146 numero campo 74 vcampoenblanco
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	//numero_orden_desde_cero 149 numero orden 150 numero campo 78  vcampoenblanco
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }//fin if
	    		
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 146 numero orden 147 numero campo 75 vcampoenblanco
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="" )
	    {
		    $campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 147 numero orden 148 numero campo 76 vcampoenblanco
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    if(trim($campos[$numero_campo])=="" )
	    {
			$campos[$numero_campo]="1845-01-01";		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 148 numero orden 149 numero campo 77 vcampoenblanco
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="" )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	
	
	
	//numero_orden_desde_cero 150 numero orden 151 numero campo 79 vcampoenblanco
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
	    if($campos[$numero_campo]=="" )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if	
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 154 numero orden 155 numero campo 83 vcampoenblanco
	$numero_campo=154;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
		{
		    $campos[$numero_campo]="98";
		}//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 151 numero orden 152 numero campo 80 vcampoenblanco
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="" )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 152 numero orden 153 numero campo 81 vcampoenblanco
	$numero_campo=152;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }

	}//if si existe campo
	
	
	//numero_orden_desde_cero 153 numero orden 154 numero campo 82 
	$numero_campo=153;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="" )
	    {
			$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 155 numero orden 156 numero campo 84 vcampoenblanco
	$numero_campo=155;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])==""  )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 156 numero orden 157 numero campo 85 vcampoenblanco
	$numero_campo=156;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])==""  )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 157 numero orden 158 numero campo 86 vcampoenblanco
	$numero_campo=157;
	if(isset($campos[$numero_campo]))
	{   
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 158 numero orden 159 numero campo 87 vcampoenblanco
	$numero_campo=158;
	if(isset($campos[$numero_campo]))
	{	       
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 159 numero orden 160 numero campo 88 vcampoenblanco
	$numero_campo=159;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 160 numero orden 161 numero campo 89 vcampoenblanco
	$numero_campo=160;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 161 numero orden 162 numero campo 90 vcampoenblanco
	$numero_campo=161;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 162 numero orden 163 numero campo 91 vcampoenblanco
	$numero_campo=162;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 163 numero orden 164 numero campo 92 vcampoenblanco
	$numero_campo=163;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 164 numero orden 165 numero campo 93 vcampoenblanco
	$numero_campo=164;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 165 numero orden 166 numero campo 94 vcampoenblanco
	$numero_campo=165;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 166 numero orden 167 numero campo 95 vcampoenblanco
	$numero_campo=166;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    $campo_n94=trim($campos[165]);
	    $c94_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n94,"1900-12-31");
	    //campo obligatorio

	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 167 numero orden 168 numero campo 96 vcampoenblanco
	$numero_campo=167;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
	    {
			$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 168 numero orden 169 numero campo 97 vcampoenblanco
	$numero_campo=168;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 169 numero orden 170 numero campo 98 vcampoenblanco
	$numero_campo=169;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	}//if si existe campos
	
	
	//numero_orden_desde_cero 170 numero orden 171 numero campo 99 vcampoenblanco
	$numero_campo=170;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 171 numero orden 172 numero campo 100 vcampoenblanco
	$numero_campo=171;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 172 numero orden 173 numero campo 101 vcampoenblanco
	$numero_campo=172;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 173 numero orden 174 numero campo 102 vcampoenblanco
	$numero_campo=173;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 174 numero orden 175 numero campo 103 vcampoenblaco
	$numero_campo=174;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 175 numero orden 176 numero campo 104 vcampoenblanco
	$numero_campo=175;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 176 numero orden 177 numero campo 105 vcampoenblanco
	$numero_campo=176;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 177 numero orden 178 numero campo 106 vcampoenblanco
	$numero_campo=177;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 178 numero orden 179 numero campo 107 vcampoenblanco
	$numero_campo=178;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="" )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 179 numero orden 180 numero campo 108 
	$numero_campo=179;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 180 numero orden 181 numero campo 109 vcampoenblanco
	$numero_campo=180;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="" )	       
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 181 numero orden 182 numero campo 110 vcampoenblanco
	$numero_campo=181;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
			$campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 182 numero orden 183 numero campo 111 
	$numero_campo=182;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 183 numero orden 184 numero campo 112 vcampoenblanco
	$numero_campo=183;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 184 numero orden 185 numero campo 113 vcampoenblanco
	$numero_campo=184;
	if(isset($campos[$numero_campo]))
	{
		if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 185 numero orden 186 numero campo 114 
	$numero_campo=185;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="" )
	    {
		    $campos[$numero_campo]="3";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 186 numero orden 187 numero campo 114.1 vcampoenblanco
	$numero_campo=186;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 187 numero orden 188 numero campo 114.2 
	$numero_campo=187;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 188 numero orden 189 numero campo 114.3 
	$numero_campo=188;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 189 numero orden 190 numero campo 114.4 
	$numero_campo=189;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campos
	
	
	//numero_orden_desde_cero 190 numero orden 191 numero campo 114.5 
	$numero_campo=190;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 191 numero orden 192 numero campo 114.6 
	$numero_campo=191;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="2";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 192 numero orden 193 numero campo 115 vcampoenblanco
	$numero_campo=192;
	if(isset($campos[$numero_campo]))
	{	    
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 193 numero orden 194 numero campo 116 vcampoenblanco
	$numero_campo=193;
	if(isset($campos[$numero_campo]))
	{		
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 194 numero orden 195 numero campo 117 vcampoenblanco
	$numero_campo=194;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 195 numero orden 196 numero campo 118 vcampoenblanco
	$numero_campo=195;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")	       
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 196 numero orden 197 numero campo 119 vcampoenblanco
	$numero_campo=196;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 197 numero orden 198 numero campo 120 vcampoenblanco
	$numero_campo=197;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 198 numero orden 199 numero campo 121 vcampoenblanco
	$numero_campo=198;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 199 numero orden 200 numero campo 122 vcampoenblanco
	$numero_campo=199;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 200 numero orden 201 numero campo 123 vcampoenblanco
	$numero_campo=200;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="4";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 201 numero orden 202 numero campo 124 vcampoenblanco
	$numero_campo=201;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 202 numero orden 203 numero campo 125 
	$numero_campo=202;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 203 numero orden 204 numero campo 126 
	$numero_campo=203;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="97";
	    }
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 204 numero orden 205 numero campo 127 
	$numero_campo=204;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		if(trim($campos[$numero_campo])=="")
		{
			$campos[$numero_campo]="99";
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 205 numero orden 206 numero campo 128 
	$numero_campo=205;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n127=trim($campos[204]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="0";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 206 numero orden 207 numero campo 129 
	$numero_campo=206;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="7";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 207 numero orden 208 numero campo 130
	$numero_campo=207;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 208 numero orden 209 numero campo 131 
	$numero_campo=208;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 209 numero orden 210 numero campo 132 
	$numero_campo=209;
	if(isset($campos[$numero_campo]))
	{
	    if(trim($campos[$numero_campo])=="")
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  reparar Cancer





function reparador_formato_valor_permitido_CANCER(&$campos,$nlinea,&$consecutivo_errores,
						  $array_tipo_validacion,$array_grupo_validacion,
						  $array_detalle_validacion,$nombre_archivo_registrado,
						  $fecha_de_corte,$cod_prestador,
						  $cod_eapb,&$diccionario_identificacion,
						  &$diccionario_identificacion_lineas,
						  &$coneccionBD, $array_numero_campo_bd)
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();

	$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();

	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='03' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0301' ORDER BY numero_de_orden ";
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
	
	
	
	$cont_corrector_notacion_cientifica=0;
	while($cont_corrector_notacion_cientifica<count($campos))
	{
	    if(!ctype_digit(trim($campos[$cont_corrector_notacion_cientifica]))
	       && is_numeric(trim($campos[$cont_corrector_notacion_cientifica])))
	    {
		    $antes=$campos[$cont_corrector_notacion_cientifica];
		    
		    $campos[$cont_corrector_notacion_cientifica]="".convert_to_standard_notation($campos[$cont_corrector_notacion_cientifica]);
		    
		    $campos[$cont_corrector_notacion_cientifica]=str_replace(",","",$campos[$cont_corrector_notacion_cientifica]);
		    
		    $despues=$campos[$cont_corrector_notacion_cientifica];
		    //echo "<script>alert('$antes $despues');</script>";
	    }
	    $cont_corrector_notacion_cientifica++;
	}
	
	//numero_orden_desde_cero 0 numero orden 1 numero campo 1 vvalorperimitido
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
		    $campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		    
		    //formato de campo
		    if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		    {
			    $campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			    
		    }//fin if
		}//fin campo no es en blanco		
	}//if si existe campo
	
	//numero_orden_desde_cero 1 numero orden 2 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
		
		
		
		if(true)
		{
		    $campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		    
		    if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		    {
			    $campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			    
		    }//fin if
		    
		}//fin no esta en blanco
	}//if si existe campo
	
	//numero_orden_desde_cero 2 numero orden 3 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
		    $campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		    
		    if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		    {
			    $campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			    
		    }//fin if
		}//fin no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 3 numero orden 4 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
		    $campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		    
		    if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
		    {
			    $campos[$numero_campo]=preg_replace("/[^A-Za-z0-9\s+]/", "", trim($campos[$numero_campo]) );
			    
		    }//fin if
		    
		}//fin no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 4 numero orden 5 numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
		

		if(true)
		{
			$campos[$numero_campo]=strtoupper($campos[$numero_campo]);
			
			//valor permitido
			/*
			if( $campos[$numero_campo]!="RC" &&  $campos[$numero_campo]!="TI" &&  $campos[$numero_campo]!="CC" &&  $campos[$numero_campo]!="CE" &&  $campos[$numero_campo]!="PA" &&  $campos[$numero_campo]!="MS" &&  $campos[$numero_campo]!="AS" &&  $campos[$numero_campo]!="CD" &&  $campos[$numero_campo]!="SC" &&  $campos[$numero_campo]!="PE")
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103048"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103048,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			*/
		}//fin if no esta en blanco
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 5 numero orden 6 numero campo 6 Numero ID
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		/*
		if(trim($campos[$numero_campo])=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		*/
		
		if(true)
		{
			if(!ctype_alnum(str_replace(" ","",$campos[$numero_campo])))
			{
				$campos[$numero_campo]=preg_replace("/[^A-Za-z0-9]/", "", trim($campos[$numero_campo]) );
				
			}//fin if			
			
		}//fin if no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 6 numero orden 7 numero campo 7 
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
		//campo obligatorio
		/*
		if(trim($campos[$numero_campo])=="")
		{
			if($errores_campos!="")
			{
				$errores_campos.="|";
			}		
			//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			$consecutivo_errores++;
			
			$hubo_errores=true;
		}//fin if 
		*/
			
		if(true)
		{		
			//correccion formato fecha al formato AAAA-MM-DD year-month-day
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,true);
		}//fin if no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 7 numero orden 8 numero campo 8 
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
			$campos[$numero_campo]=strtoupper($campos[$numero_campo]);
			
			//valor permitido
			/*
			if( $campos[$numero_campo]!="M" &&  $campos[$numero_campo]!="F"  )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103046"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103046,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			*/
		}//fin if no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 8 numero orden 9 numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
			
			//valor permitido
			$consulta="";
			$consulta.="SELECT * FROM gioss_ciou WHERE codigo_ciou_08='".$campos[$numero_campo]."' OR codigo_ciou_88='".$campos[$numero_campo]."'; ";
			$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
			if( count($resultado)==0  && ($campos[$numero_campo]!="9998" && $campos[$numero_campo]!="9999") )
			{
				$campos[$numero_campo]="9999";
			}//fin if
		}//fin if no esta en blanco
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 9 numero orden 10 numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
			$campos[$numero_campo]=strtoupper($campos[$numero_campo]);
			
			//valor permitido
			/*
			if( $campos[$numero_campo]!="C" && $campos[$numero_campo]!="S" && $campos[$numero_campo]!="P" && $campos[$numero_campo]!="E" && $campos[$numero_campo]!="N" && $campos[$numero_campo]!="O")
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103034"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103034,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			*/
		}//fin if no esta en blanco
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 10 numero orden 11 numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
		
		$campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		if($campos[$numero_campo]!=$cod_eapb)
		{
		    $campos[$numero_campo]=$cod_eapb;
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 11 numero orden 12 numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
			//valor permitido
			if( intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>6)
			{
				$campos[$numero_campo]="6";
			}//fin if
			else if(strlen($campos[$numero_campo])!=1)
			{
				$campos[$numero_campo]=intval($campos[$numero_campo]);
			}
		}//fin if no esta en blanco
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 12 numero orden 13 numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{	
			//valor permitido
			if( (intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>16) && (intval($campos[$numero_campo])<31 || intval($campos[$numero_campo])>39) && (intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>63) )
			{
				$campos[$numero_campo]="61";
			}//fin if
			else if(strlen($campos[$numero_campo])>2)
			{
				$campos[$numero_campo]=intval($campos[$numero_campo]);
			}
		}//fin if no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 13 numero orden 14 numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
	    if(strlen($campos[$numero_campo])==4 &&
	   (substr($campos[$numero_campo],0,1)=="5" || substr($campos[$numero_campo],0,1)=="8")
	   )
	    {
		$campos[$numero_campo]="0".$campos[$numero_campo];
	    }
	    else if(strlen($campos[$numero_campo])>5)
	    {
		$campos[$numero_campo]=substr($campos[$numero_campo],0,5);
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 14 numero orden 15 numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{		
		
		if(!ctype_digit(str_replace(array(".",","),"",$campos[$numero_campo])))
		{
		    $campos[$numero_campo]=preg_replace("/[^0-9,.]/", "", trim($campos[$numero_campo]) );
		    if(trim($campos[$numero_campo])=="")
		    {
			//echo "<script>alert('campo 15 num ord 14 vacio');</script>";
			$campos[$numero_campo]="0000000000";
		    }//fin if
		}//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 15 numero orden 16 numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{		
		
		if(true)
		{
			//correccion formato fecha al formato AAAA-MM-DD year-month-day
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
		}//fin campo no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 16 numero orden 17 numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    //se aplico correccion por fuera debido a que debia crear archivo con los registros separados
	    $campos[$numero_campo]=strtoupper($campos[$numero_campo]);
		
	}//if si existe campo
	
	//CAMPO 20 CONTRA FECHA DE CORTE
	$campo_n20=$campos[19];
	
	//solo year
			
	$array_fecha_campo_20=explode("-",$campo_n20);
	$verificar_validez_para_comparar_fecha_anterior=true;
	
	
	$year_campo_20=$array_fecha_campo_20[0];
	$fecha_de_corte_array=explode("-",$fecha_de_corte);
	$year_fecha_de_corte=$fecha_de_corte_array[0];
	$corresponde_year_campo20_con_year_fecha_corte=false;
	if(ctype_digit($year_campo_20) && ctype_digit($year_fecha_de_corte))
	{
	    if(trim($year_campo_20)==trim($year_fecha_de_corte))
	    {
		$corresponde_year_campo20_con_year_fecha_corte=true;
	    }
	}
	//fin solo year
	
	$campo20_contra_fecha_corte_global=0;
	$campo20_contra_fecha_corte_global=diferencia_dias_entre_fechas($campo_n20,$fecha_de_corte);
	$campo20_es_fecha_calendario=0;
	$campo20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n20,"1900-12-31");
	//FIN CAMPO 20 CONTRA FECHADE CORTE
	
	//numero_orden_desde_cero 17 numero orden 18 numero campo 18 vvalorpermitido
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
		
		if(true)
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
		}//fin if campo no esta en blanco
			
		$es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
		$excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
		//valor permitido
		if($es_fecha_calendario<0
		   && $excede_fecha_de_corte<0
		   )
		{
		    $campos[$numero_campo]=$fecha_primer_dia_corte;
		}
		else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
		    )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }//campo no esta en blanco
	}//if si existe campo
	
	
	//numero_orden_desde_cero 18 numero orden 19 numero campo 19 vvalorpermitido
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
		}//fin if campo no esta en blanco
		
		$es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
		$excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
		//valor permitido
		if($es_fecha_calendario<0
		   && $excede_fecha_de_corte<0
		   )
		{
		    $campos[$numero_campo]=$fecha_primer_dia_corte;
		}
		else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
		    )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }//campo no esta en blanco
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 19 numero orden 20 numero campo 20 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
		
		
		if(true)
		{				
			//correccion formato fecha al formato AAAA-MM-DD year-month-day
			$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
		}//fin if campo no esta en blanco
		
		$es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
		$excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
		//valor permitido
		if($es_fecha_calendario<0
		   && $excede_fecha_de_corte<0
		   )
		{
		    $campos[$numero_campo]=$fecha_de_corte;
		}
		else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
		    )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }//campo no esta en blanco
		
	}//if si existe campo
	
	
	$campo_ant_20=$campos[19];
	$c20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_20,"1900-12-31");
	//$fecha_primer_dia_corte=$year_fecha_de_corte."-01-01";
	$mes_fecha_de_corte=$fecha_de_corte_array[1];
	$fecha_primer_dia_corte=$year_fecha_de_corte."-".$mes_fecha_de_corte."-01";
	$c20_es_mayor_primer_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_20,$fecha_primer_dia_corte);
	$c20_es_menor_ultimo_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_20,$fecha_de_corte);
	
	//numero_orden_desde_cero 20 numero orden 21 numero campo 21 vvalorpermitido
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="99"
	    	);
	    
	    if($valores_permitidos_para_condicion_actual)
	    {
			$campos[$numero_campo]="8";
	    }
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 21 numero orden 22 numero campo 22 vvalorpermitido
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }	    
	    
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"				
		&& trim($campos[$numero_campo])!="98"				
		&& trim($campos[$numero_campo])!="99"
	    	);

	    if( $valores_permitidos_para_condicion_actual
	       )
	    {
		    $campos[$numero_campo]="98";
	    }//fin if
	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 22 numero orden 23 numero campo 23 vvalorpermitido
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//campo no esta en blanco
	    
	    //valor permitido
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    
	    
	    
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
	       && $campos[$numero_campo]!="1845-01-01"
		    )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }//campo no esta en blanco
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 23 numero orden 24 numero campo 24 vvalorpermitido
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin campo esta en blanco
	    

	    //valor permitido
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    
	    
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
	       && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
			$campos[$numero_campo]="1845-01-01";
	    }//campo no esta en blanco
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 24 numero orden 25 numero campo 25 vvalopermitido
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
		
		//valor permitido
		
		$consulta="";
		$consulta.="SELECT * FROM gioss_cancer_ips_conformacion_dx WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);

		if( (count($resultado)==0 || !is_array($resultado))
		   && $campos[$numero_campo]!="96"
		   && $campos[$numero_campo]!="98"
		   && $campos[$numero_campo]!="99"
		   )
		{
		    $campos[$numero_campo]="99";
		}
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 25 numero orden 26 numero campo 26 vvalorpermitido
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if campo no esta en blanoc
	    
	    //valor permitido
	    
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);

	    
	    if($es_fecha_calendario<0
	    && $excede_fecha_de_corte<0
	    )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    //no es fecha calendario
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
	       )
	    {
			$campos[$numero_campo]="1800-01-01";
	    }
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 26 numero orden 27 numero campo 27 vvalorpermitido
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
			     && trim($campos[$numero_campo])!="2"
			     && trim($campos[$numero_campo])!="3"
			     && trim($campos[$numero_campo])!="4"
			     && trim($campos[$numero_campo])!="5"
			     && trim($campos[$numero_campo])!="6"
			     && trim($campos[$numero_campo])!="7"
			     && trim($campos[$numero_campo])!="8"
			     && trim($campos[$numero_campo])!="9"
			     && trim($campos[$numero_campo])!="10"
			     && trim($campos[$numero_campo])!="11"
			     && trim($campos[$numero_campo])!="12"
			     && trim($campos[$numero_campo])!="13"
			     && trim($campos[$numero_campo])!="14"
			     && trim($campos[$numero_campo])!="15"
			     && trim($campos[$numero_campo])!="16"
			     && trim($campos[$numero_campo])!="17"
			     && trim($campos[$numero_campo])!="18"
			     && trim($campos[$numero_campo])!="19"
			     && trim($campos[$numero_campo])!="20"
			     && trim($campos[$numero_campo])!="98"				
			     && trim($campos[$numero_campo])!="99"
	    	);

	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 27 numero orden 28 numero campo 28 vvalorpermitido
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="98"				
		&& trim($campos[$numero_campo])!="99"
	    	);

	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }

	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 28 numero orden 29 numero campo 29 vvalorpermitido
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    
	    //valor permitido
	    
	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="0"
			   && trim($campos[$numero_campo])!="1"
			     && trim($campos[$numero_campo])!="2"
			     && trim($campos[$numero_campo])!="3"
			     && trim($campos[$numero_campo])!="4"
			     && trim($campos[$numero_campo])!="5"
			     && trim($campos[$numero_campo])!="6"
			     && trim($campos[$numero_campo])!="7"
			     && trim($campos[$numero_campo])!="8"
			     && trim($campos[$numero_campo])!="9"
			     && trim($campos[$numero_campo])!="10"
			     && trim($campos[$numero_campo])!="11"
			     && trim($campos[$numero_campo])!="12"
			     && trim($campos[$numero_campo])!="13"
			     && trim($campos[$numero_campo])!="14"
			     && trim($campos[$numero_campo])!="15"
			     && trim($campos[$numero_campo])!="16"
			     && trim($campos[$numero_campo])!="17"
			     && trim($campos[$numero_campo])!="18"
			     && trim($campos[$numero_campo])!="19"
			     && trim($campos[$numero_campo])!="20"
			     && trim($campos[$numero_campo])!="21"			     
			     && trim($campos[$numero_campo])!="22"
			     && trim($campos[$numero_campo])!="23"
			     && trim($campos[$numero_campo])!="24"			     
			     && trim($campos[$numero_campo])!="25"
			     && trim($campos[$numero_campo])!="98"				
			     && trim($campos[$numero_campo])!="99"
	    	);



	    
	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if este campo no esta en blanco

	    
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 29 numero orden 30 numero campo 30 vvalorpermitido
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin este campo no esta en blanco
	    
	    //valor permitido

	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    if($es_fecha_calendario<0
	    && $excede_fecha_de_corte<0
	    )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1800-01-01"
	       && $campos[$numero_campo]!="1845-01-01")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }

	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 30 numero orden 31 numero campo 31 valorpermitido
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		$campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    
	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="97"
		&& trim($campos[$numero_campo])!="98"
		&& trim($campos[$numero_campo])!="99"
	    	);
	    
	    if($valores_permitidos_para_condicion_actual )
	    {
		    $campos[$numero_campo]="98";
	    }//fin 

	   
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 31 numero orden 32 numero campo 32 valorpermitido
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
		
	    if(true)
	    {
		//correccion formato fecha al formato AAAA-MM-DD year-month-day
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin este campo no esta en blanco
	    
	    //valor permitido
	    
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);


	    
	    //campo obligatorio
	    /*
	    if($es_fecha_calendario<0
	    && $excede_fecha_de_corte<0
	    )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }	    
	    else
	    */
	    if($es_fecha_calendario>0
	       && trim($campos[$numero_campo])!="1800-01-01"
	       && trim($campos[$numero_campo])!="1840-01-01"
	       && trim($campos[$numero_campo])!="1845-01-01" )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 32 numero orden 33 numero campo 33 vvalorpermitido
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
			
	    //valor permitido
	    

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="3"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="98"
		    && trim($campos[$numero_campo])!="99" 
	    	);
			
	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }

	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 33 numero orden 34 numero campo 34 vvalorpermitido
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
				&& trim($campos[$numero_campo])!="2"
				&& trim($campos[$numero_campo])!="3"
				&& trim($campos[$numero_campo])!="4"
				&& trim($campos[$numero_campo])!="98"
				&& trim($campos[$numero_campo])!="99"	   
	    	);

	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }


	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 34 numero orden 35 numero campo 35 vvalorpermitido
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
	    if(true)
	    {		
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if esta en blanco
	    
	    
	    // valor permitido

	    /*
	    //y la fecha de corte
	    if($es_fecha_calendario<0
	    && $excede_fecha_de_corte<0
	    )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    */
	    if($es_fecha_calendario>0
		&& $campos[$numero_campo]!="1800-01-01"
		&& $campos[$numero_campo]!="1845-01-01" )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    
	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 35 numero orden 36 numero campo 36 vvalorpermitdo
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //validado
	    
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
			&& trim($campos[$numero_campo])!="4"
			&& trim($campos[$numero_campo])!="98"
			&& trim($campos[$numero_campo])!="99"
			)
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 36 numero orden 37 numero campo 37 vvalorpermitido
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="8"
	    && trim($campos[$numero_campo])!="9"
	    && trim($campos[$numero_campo])!="10"
	    && trim($campos[$numero_campo])!="98"
	    && trim($campos[$numero_campo])!="99")
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	    
		
	}//if si existe campo
	
	$campo_n7=trim($campos[6]);
	$array_fecha_nacimiento=explode("-",$campo_n7);
	$array_fecha_corte_para_nacimiento=explode("-",$fecha_de_corte);
	$array_edad_fc=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte_para_nacimiento[2]."-".$array_fecha_corte_para_nacimiento[1]."-".$array_fecha_corte_para_nacimiento[0]);
	$edad_en_year_calc_fc=intval($array_edad_fc['y']);
	
	//numero_orden_desde_cero 37 numero orden 38 numero campo 38 vvalorpermitido
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    

		$valores_permitidos_para_condicion_actual=(
			trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="3"
		    && trim($campos[$numero_campo])!="4"
		    && trim($campos[$numero_campo])!="5"
		    && trim($campos[$numero_campo])!="6"
		    && trim($campos[$numero_campo])!="7"
		    && trim($campos[$numero_campo])!="8"
		    && trim($campos[$numero_campo])!="9"
		    && trim($campos[$numero_campo])!="10"
		    && trim($campos[$numero_campo])!="11"
		    && trim($campos[$numero_campo])!="12"
		    && trim($campos[$numero_campo])!="13"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="98"
		    && trim($campos[$numero_campo])!="99"
			);
	    
		
	    
	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 38 numero orden 39 numero campo 39 vvalorpermitido 
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//no esta en blanco
	    
	    //valor permitido
	    
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    
	    
		if($es_fecha_calendario>0
		&& $campos[$numero_campo]!="1800-01-01"
		&& $campos[$numero_campo]!="1845-01-01")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }

	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 39 numero orden 40 numero campo 40 vvalorpermitido
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="99"
	    	);

	    if(
			$valores_permitidos_para_condicion_actual
			
		)
		{
			$campos[$numero_campo]="99";
		}
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 40 numero orden 41 numero campo 41 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="99"
	    )
	    {
		    $campos[$numero_campo]="1";
	    }//fin if
		
		
	}//if si existe campo
	
	//orden 44, 42,43
	
	//numero_orden_desde_cero 43 numero orden 44 numero campo 44 vvalorpermitido
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    $campos[$numero_campo]=strtoupper($campos[$numero_campo]);
	    
		
	    //valor permitido
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_diagnostico_ciex_cancer_tumor_maligno WHERE codigo_diagnostico='".$campos[$numero_campo]."' ; ";
	    $resultado_ciex=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if($campos[$numero_campo]!="99"
	       && (count($resultado_ciex)==0
		    || !is_array($resultado_ciex)
		    )
	       )
	    {
			$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	//numero_orden_desde_cero 41 numero orden 42 numero campo 42 vvalorpermitido
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
				&& trim($campos[$numero_campo])!="2"
				&& trim($campos[$numero_campo])!="99" 
	    	);
	    
	    if(
	    	$valores_permitidos_para_condicion_actual
	    	
		    )
	    {
		    $campos[$numero_campo]="99";
		    
	    }//fin if

	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 42 numero orden 43 numero campo 43 vvalorpermitido
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{	
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//campo no esta en blanco
	    
	    //valor permitido
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);

	    
	    if($es_fecha_calendario>0
		    && $campos[$numero_campo]!="1800-01-01"
		    && $campos[$numero_campo]!="1845-01-01")
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    
	    
	    /*
	    //se comento 
	    if($es_fecha_calendario<0
	    && $excede_fecha_de_corte<0
	    )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    */
	    
	    
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 44 numero orden 45 numero campo 45 vvalorpermitido
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="98"
	    	);
	    
	    if(
	    	$valores_permitidos_para_condicion_actual
			)
	    {
	    	//c3
			$campos[$numero_campo]="98";
	    }
	    

	}//if si existe campo

	//CONSULTA EN TABLA DE CODIGO HEMOLINFATICO
	$campo_n17=trim($campos[16]);
	$consulta_hemolinfatico="";
	$consulta_hemolinfatico.="SELECT * FROM gioss_ca_hematolinfatico WHERE codigo='".$campo_n17."' ; ";
	$resultado_hemolinfatico=$coneccionBD->consultar2_no_crea_cierra($consulta_hemolinfatico);
	//FIN CONSULTA EN TABLA DE CODIGO HEMOLINFATICO
	
	
	//numero_orden_desde_cero 45 numero orden 46 numero campo 46 vvalorpermitido
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		$campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=( 
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="11"
		&& trim($campos[$numero_campo])!="12"
		&& trim($campos[$numero_campo])!="13"
		&& trim($campos[$numero_campo])!="14"
		&& trim($campos[$numero_campo])!="15"
		&& trim($campos[$numero_campo])!="16"
		&& trim($campos[$numero_campo])!="17"
		&& trim($campos[$numero_campo])!="18"
		&& trim($campos[$numero_campo])!="19"
		&& trim($campos[$numero_campo])!="20"
		&& trim($campos[$numero_campo])!="21"
		&& trim($campos[$numero_campo])!="22"
		&& trim($campos[$numero_campo])!="23"
		&& trim($campos[$numero_campo])!="24"
		&& trim($campos[$numero_campo])!="25"
		&& trim($campos[$numero_campo])!="26"
		&& trim($campos[$numero_campo])!="27"
		&& trim($campos[$numero_campo])!="28"
		&& trim($campos[$numero_campo])!="29"
		&& trim($campos[$numero_campo])!="30"
		&& trim($campos[$numero_campo])!="31"
		&& trim($campos[$numero_campo])!="32"
		&& trim($campos[$numero_campo])!="33"
		&& trim($campos[$numero_campo])!="34"
		&& trim($campos[$numero_campo])!="35"
		&& trim($campos[$numero_campo])!="36"
		&& trim($campos[$numero_campo])!="37"
		&& trim($campos[$numero_campo])!="38"
		&& trim($campos[$numero_campo])!="39"
		&& trim($campos[$numero_campo])!="40"
		&& trim($campos[$numero_campo])!="41"
		&& trim($campos[$numero_campo])!="42"
		&& trim($campos[$numero_campo])!="43"
		&& trim($campos[$numero_campo])!="44"
		&& trim($campos[$numero_campo])!="45"
		&& trim($campos[$numero_campo])!="46"
		&& trim($campos[$numero_campo])!="47"
		&& trim($campos[$numero_campo])!="48"
		&& trim($campos[$numero_campo])!="49"
		&& trim($campos[$numero_campo])!="50"
		&& trim($campos[$numero_campo])!="51"
		&& trim($campos[$numero_campo])!="52"
		&& trim($campos[$numero_campo])!="53"
		&& trim($campos[$numero_campo])!="54"
		&& trim($campos[$numero_campo])!="55"
		&& trim($campos[$numero_campo])!="56"
		&& trim($campos[$numero_campo])!="57"
		&& trim($campos[$numero_campo])!="58"
		&& trim($campos[$numero_campo])!="59"
		&& trim($campos[$numero_campo])!="60"
		&& trim($campos[$numero_campo])!="61"
		&& trim($campos[$numero_campo])!="62"
		&& trim($campos[$numero_campo])!="63"
		&& trim($campos[$numero_campo])!="64"
		&& trim($campos[$numero_campo])!="65"
		&& trim($campos[$numero_campo])!="66"
		&& trim($campos[$numero_campo])!="67"
		&& trim($campos[$numero_campo])!="68"
		&& trim($campos[$numero_campo])!="69"
		&& trim($campos[$numero_campo])!="70"
		&& trim($campos[$numero_campo])!="71"
		&& trim($campos[$numero_campo])!="72"
		&& trim($campos[$numero_campo])!="73"
		&& trim($campos[$numero_campo])!="74"
		&& trim($campos[$numero_campo])!="75"
		&& trim($campos[$numero_campo])!="76"
		&& trim($campos[$numero_campo])!="77"
		&& trim($campos[$numero_campo])!="78"
		&& trim($campos[$numero_campo])!="79"
		&& trim($campos[$numero_campo])!="80"
		&& trim($campos[$numero_campo])!="81"
		&& trim($campos[$numero_campo])!="82"
		&& trim($campos[$numero_campo])!="83"
		&& trim($campos[$numero_campo])!="84"
		&& trim($campos[$numero_campo])!="85"
		&& trim($campos[$numero_campo])!="86"
		&& trim($campos[$numero_campo])!="87"
		&& trim($campos[$numero_campo])!="88"
		&& trim($campos[$numero_campo])!="89"
		&& trim($campos[$numero_campo])!="90"
		&& trim($campos[$numero_campo])!="91"
		&& trim($campos[$numero_campo])!="92"
		&& trim($campos[$numero_campo])!="93"
		&& trim($campos[$numero_campo])!="94"
		&& trim($campos[$numero_campo])!="95"
		&& trim($campos[$numero_campo])!="96"
		&& trim($campos[$numero_campo])!="97"
		&& trim($campos[$numero_campo])!="98"
	    	);
	    
	    if(
	    	$valores_permitidos_para_condicion_actual
		 )
	    {
	    	//c4
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    
		
	}//if si existe campo
	//fincampo 45 numero orden 46 numero campo 46 vvalorpermitido
	
	
	//numero_orden_desde_cero 46 numero orden 47 numero campo 46.1 vvalorpermitido
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 47 numero orden 48 numero campo 46.2 vvalorpermitido
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 48 numero orden 49 numero campo 46.3 vvalorpermitido
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 49 numero orden 50 numero campo 46.4 vvalorpermitido
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 50 numero orden 51 numero campo 46.5 vvalorpermitido
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	
	    

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 51 numero orden 52 numero campo 46.6 vvalorpermitido
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 52 numero orden 53 numero campo 46.7 vvalorpermitido
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	
	    

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 53 numero orden 54 numero campo 46.8 vvalorpermitido
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido	
	    

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="97"
		    && trim($campos[$numero_campo])!="99"
	    	);

	    if(
	    	$valores_permitidos_para_condicion_actual
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 54 numero orden 55 numero campo 47 vvalorpermitido
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
			    && trim($campos[$numero_campo])!="2"
			    && trim($campos[$numero_campo])!="3"
			    && trim($campos[$numero_campo])!="4"
			    && trim($campos[$numero_campo])!="5"
			    && trim($campos[$numero_campo])!="6"
			    && trim($campos[$numero_campo])!="7"
			    && trim($campos[$numero_campo])!="8"
			    && trim($campos[$numero_campo])!="9"
			    && trim($campos[$numero_campo])!="10"
			    && trim($campos[$numero_campo])!="11"
			    && trim($campos[$numero_campo])!="12"
			    && trim($campos[$numero_campo])!="13"
			    && trim($campos[$numero_campo])!="14"
			    && trim($campos[$numero_campo])!="15"
			    && trim($campos[$numero_campo])!="16"
			    && trim($campos[$numero_campo])!="17"
			    && trim($campos[$numero_campo])!="18"
			    && trim($campos[$numero_campo])!="19"
			    && trim($campos[$numero_campo])!="20"
			    && trim($campos[$numero_campo])!="21"
			    && trim($campos[$numero_campo])!="22"
			    && trim($campos[$numero_campo])!="23"
			    && trim($campos[$numero_campo])!="24"
			    && trim($campos[$numero_campo])!="25"
			    && trim($campos[$numero_campo])!="26"
			    && trim($campos[$numero_campo])!="27"
			    && trim($campos[$numero_campo])!="28"
			    && trim($campos[$numero_campo])!="29"
			    && trim($campos[$numero_campo])!="30"
			    && trim($campos[$numero_campo])!="31"
			    && trim($campos[$numero_campo])!="32"
			    && trim($campos[$numero_campo])!="33"
			    && trim($campos[$numero_campo])!="34"
			    && trim($campos[$numero_campo])!="35"
			    && trim($campos[$numero_campo])!="36"
			    && trim($campos[$numero_campo])!="37"
			    && trim($campos[$numero_campo])!="38"
			    && trim($campos[$numero_campo])!="39"
			    && trim($campos[$numero_campo])!="40"
			    && trim($campos[$numero_campo])!="41"
			    && trim($campos[$numero_campo])!="42"
			    && trim($campos[$numero_campo])!="43"
			    && trim($campos[$numero_campo])!="44"
			    && trim($campos[$numero_campo])!="45"
			    && trim($campos[$numero_campo])!="46"
			    && trim($campos[$numero_campo])!="47"
			    && trim($campos[$numero_campo])!="48"
			    && trim($campos[$numero_campo])!="49"
			    && trim($campos[$numero_campo])!="50"
			    && trim($campos[$numero_campo])!="51"
			    && trim($campos[$numero_campo])!="52"
			    && trim($campos[$numero_campo])!="53"
			    && trim($campos[$numero_campo])!="54"
			    && trim($campos[$numero_campo])!="55"
			    && trim($campos[$numero_campo])!="56"
			    && trim($campos[$numero_campo])!="57"
			    && trim($campos[$numero_campo])!="58"
			    && trim($campos[$numero_campo])!="59"
			    && trim($campos[$numero_campo])!="60"
			    && trim($campos[$numero_campo])!="61"
			    && trim($campos[$numero_campo])!="62"
			    && trim($campos[$numero_campo])!="63"
			    && trim($campos[$numero_campo])!="64"
			    && trim($campos[$numero_campo])!="65"
			    && trim($campos[$numero_campo])!="66"
			    && trim($campos[$numero_campo])!="67"
			    && trim($campos[$numero_campo])!="68"
			    && trim($campos[$numero_campo])!="69"
			    && trim($campos[$numero_campo])!="70"
			    && trim($campos[$numero_campo])!="71"
			    && trim($campos[$numero_campo])!="72"
			    && trim($campos[$numero_campo])!="73"
			    && trim($campos[$numero_campo])!="74"
			    && trim($campos[$numero_campo])!="75"
			    && trim($campos[$numero_campo])!="76"
			    && trim($campos[$numero_campo])!="77"
			    && trim($campos[$numero_campo])!="78"
			    && trim($campos[$numero_campo])!="79"
			    && trim($campos[$numero_campo])!="80"
			    && trim($campos[$numero_campo])!="81"
			    && trim($campos[$numero_campo])!="82"
			    && trim($campos[$numero_campo])!="83"
			    && trim($campos[$numero_campo])!="84"
			    && trim($campos[$numero_campo])!="85"
			    && trim($campos[$numero_campo])!="86"
			    && trim($campos[$numero_campo])!="87"
			    && trim($campos[$numero_campo])!="88"
			    && trim($campos[$numero_campo])!="89"
			    && trim($campos[$numero_campo])!="90"
			    && trim($campos[$numero_campo])!="91"
			    && trim($campos[$numero_campo])!="92"
			    && trim($campos[$numero_campo])!="93"
			    && trim($campos[$numero_campo])!="94"
			    && trim($campos[$numero_campo])!="95"
			    && trim($campos[$numero_campo])!="96"
			    && trim($campos[$numero_campo])!="97"
			    && trim($campos[$numero_campo])!="98"
	    	);
	    
	    if(

	    	$valores_permitidos_para_condicion_actual
		
		)
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 55 numero orden 56 numero campo 48 vvalorpermitido
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
			&& trim($campos[$numero_campo])!="4"
			&& trim($campos[$numero_campo])!="5"
			&& trim($campos[$numero_campo])!="6"
			&& trim($campos[$numero_campo])!="7"
			&& trim($campos[$numero_campo])!="8"
			&& trim($campos[$numero_campo])!="9"
			&& trim($campos[$numero_campo])!="10"
			&& trim($campos[$numero_campo])!="98"
			&& trim($campos[$numero_campo])!="99"
	    	);
	    //valor permitido
	    if(
	    	$valores_permitidos_para_condicion_actual	    	
	       )
	    {
		    $campos[$numero_campo]="98";		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 56 numero orden 57 numero campo 49 vvalorpermitido
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    //valor permitido 

	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		


	    if($es_fecha_calendario<0
		&& $excede_fecha_de_corte<0
		)
     	{
		 	$campos[$numero_campo]=$fecha_primer_dia_corte;
     	}
	    else if($es_fecha_calendario>0
		   && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 57 numero orden 58 numero campo 50 vvalorpermitido
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="98"
	    	);
	    
	    if(
	    	$valores_permitidos_para_condicion_actual
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 58 numero orden 59 numero campo 51 vvalorpermitido
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    
	    //formato
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" )
	    {
		$campos[$numero_campo]="98";
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
	    
	    
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    $campo_ant_49=$campos[56];
	    
	    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_quimioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    	    	    
	    if((count($resultado)==0 || !is_array($resultado))
	       && $campos[$numero_campo]!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 59 numero orden 60 numero campo 52 vvalorpermitido
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //valor permitido
		
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_quimioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if((count($resultado)==0 || !is_array($resultado))
		&& $campos[$numero_campo]!="98"
		)
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if

	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 60 numero orden 61 numero campo 53 vvalorpermitido
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
			&& trim($campos[$numero_campo])!="4"
			&& trim($campos[$numero_campo])!="5"
			&& trim($campos[$numero_campo])!="6"
			&& trim($campos[$numero_campo])!="7"
			&& trim($campos[$numero_campo])!="8"
			&& trim($campos[$numero_campo])!="9"
			&& trim($campos[$numero_campo])!="10"
			&& trim($campos[$numero_campo])!="11"
			&& trim($campos[$numero_campo])!="12"
			&& trim($campos[$numero_campo])!="13"
			&& trim($campos[$numero_campo])!="14"
			&& trim($campos[$numero_campo])!="15"
			&& trim($campos[$numero_campo])!="16"
			&& trim($campos[$numero_campo])!="17"
			&& trim($campos[$numero_campo])!="18"
			&& trim($campos[$numero_campo])!="19"
			&& trim($campos[$numero_campo])!="20"
			&& trim($campos[$numero_campo])!="21"
			&& trim($campos[$numero_campo])!="22"
			&& trim($campos[$numero_campo])!="23"
			&& trim($campos[$numero_campo])!="24"
			&& trim($campos[$numero_campo])!="25"
			&& trim($campos[$numero_campo])!="26"
			&& trim($campos[$numero_campo])!="27"
			&& trim($campos[$numero_campo])!="28"
			&& trim($campos[$numero_campo])!="29"
			&& trim($campos[$numero_campo])!="30"
			&& trim($campos[$numero_campo])!="31"
			&& trim($campos[$numero_campo])!="32"
			&& trim($campos[$numero_campo])!="33"
			&& trim($campos[$numero_campo])!="34"
			&& trim($campos[$numero_campo])!="35"
			&& trim($campos[$numero_campo])!="36"
			&& trim($campos[$numero_campo])!="37"
			&& trim($campos[$numero_campo])!="38"
			&& trim($campos[$numero_campo])!="39"
			&& trim($campos[$numero_campo])!="40"
			&& trim($campos[$numero_campo])!="41"
			&& trim($campos[$numero_campo])!="42"
			&& trim($campos[$numero_campo])!="43"
			&& trim($campos[$numero_campo])!="44"
			&& trim($campos[$numero_campo])!="45"
			&& trim($campos[$numero_campo])!="46"
			&& trim($campos[$numero_campo])!="47"
			&& trim($campos[$numero_campo])!="48"
			&& trim($campos[$numero_campo])!="49"
			&& trim($campos[$numero_campo])!="50"
			&& trim($campos[$numero_campo])!="51"
			&& trim($campos[$numero_campo])!="52"
			&& trim($campos[$numero_campo])!="53"
			&& trim($campos[$numero_campo])!="54"
			&& trim($campos[$numero_campo])!="55"
			&& trim($campos[$numero_campo])!="56"
			&& trim($campos[$numero_campo])!="57"
			&& trim($campos[$numero_campo])!="58"
			&& trim($campos[$numero_campo])!="59"
			&& trim($campos[$numero_campo])!="60"
			&& trim($campos[$numero_campo])!="61"
			&& trim($campos[$numero_campo])!="62"
			&& trim($campos[$numero_campo])!="63"
			&& trim($campos[$numero_campo])!="64"
			&& trim($campos[$numero_campo])!="65"
			&& trim($campos[$numero_campo])!="66"
			&& trim($campos[$numero_campo])!="67"
			&& trim($campos[$numero_campo])!="68"
			&& trim($campos[$numero_campo])!="69"
			&& trim($campos[$numero_campo])!="70"
			&& trim($campos[$numero_campo])!="71"
			&& trim($campos[$numero_campo])!="72"
			&& trim($campos[$numero_campo])!="73"
			&& trim($campos[$numero_campo])!="74"
			&& trim($campos[$numero_campo])!="75"
			&& trim($campos[$numero_campo])!="76"
			&& trim($campos[$numero_campo])!="77"
			&& trim($campos[$numero_campo])!="78"
			&& trim($campos[$numero_campo])!="79"
			&& trim($campos[$numero_campo])!="80"
			&& trim($campos[$numero_campo])!="81"
			&& trim($campos[$numero_campo])!="82"
			&& trim($campos[$numero_campo])!="83"
			&& trim($campos[$numero_campo])!="84"
			&& trim($campos[$numero_campo])!="85"
			&& trim($campos[$numero_campo])!="86"
			&& trim($campos[$numero_campo])!="87"
			&& trim($campos[$numero_campo])!="88"
			&& trim($campos[$numero_campo])!="89"
			&& trim($campos[$numero_campo])!="90"
			&& trim($campos[$numero_campo])!="91"
			&& trim($campos[$numero_campo])!="92"
			&& trim($campos[$numero_campo])!="93"
			&& trim($campos[$numero_campo])!="94"
			&& trim($campos[$numero_campo])!="95"
			&& trim($campos[$numero_campo])!="96"
			&& trim($campos[$numero_campo])!="97"
			&& trim($campos[$numero_campo])!="98"
	    	);
	    if(
	    	$valores_permitidos_para_condicion_actual
			)
	    {
		$campos[$numero_campo]="98";
	    }
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 61 numero orden 62 numero campo 53.1 vvalorpermitido
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 62 numero orden 63 numero campo 53.2 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 63 numero orden 64 numero campo 53.3 
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 64 numero orden 65 numero campo 53.4 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 65 numero orden 66 numero campo 53.5 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 66 numero orden 67 numero campo 53.6 
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 67 numero orden 68 numero campo 53.7 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 68 numero orden 69 numero campo 53.8 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 69 numero orden 70 numero campo 53.9 
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 70 numero orden 71 numero campo 53.10 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 71 numero orden 72 numero campo 53.11 
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 72 numero orden 73 numero campo 53.12 
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 73 numero orden 74 numero campo 53.13 
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 74 numero orden 75 numero campo 53.14 
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 75 numero orden 76 numero campo 53.15 
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 76 numero orden 77 numero campo 53.16 
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 77 numero orden 78 numero campo 53.17 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 78 numero orden 79 numero campo 53.18 
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 79 numero orden 80 numero campo 53.19
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 80 numero orden 81 numero campo 53.20 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 81 numero orden 82 numero campo 53.21 
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 82 numero orden 83 numero campo 53.22 
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 83 numero orden 84 numero campo 53.23 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 84 numero orden 85 numero campo 53.24 
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 85 numero orden 86 numero campo 53.25 
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 86 numero orden 87 numero campo 53.26 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 87 numero orden 88 numero campo 53.27 
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 88 numero orden 89 numero campo 53.28
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 89 numero orden 90 numero campo 53.29 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 90 numero orden 91 numero campo 53.30 
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 91 numero orden 92 numero campo 53.31 
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 92 numero orden 93 numero campo 53.32 vvalorpermitido
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(intval($campos[$numero_campo])!=1
		    && intval($campos[$numero_campo])!=2
		    && intval($campos[$numero_campo])!=3
		    && intval($campos[$numero_campo])!=98
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 93 numero orden 94 numero campo 54 vvalorermitido
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])==2 &&  $campos[$numero_campo]!=97 &&  $campos[$numero_campo]!=98)
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    
	    $bool_normalizo=false;
	    //CUM NORMALIZA
	    $consulta1="";
	    $consulta1.="SELECT * FROM gioss_codigo_medicamentos WHERE trim(codigo_cum)='".trim($campos[$numero_campo])."' ";
	    $consulta1.=" OR trim(codigo_cum_con_guion)='".trim($campos[$numero_campo])."' OR trim(cod_atc)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_2)='".trim($campos[$numero_campo])."' OR trim(codigo_cum_3)='".trim($campos[$numero_campo])."' ; ";
	    $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
	    if( count($resultado1)>0 && is_array($resultado1) )
	    {
		//si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
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
	    	    
	    //valor permitido
		if((count($resultado)==0 || !is_array($resultado))
	       && trim($campos[$numero_campo])!="97"
	       && trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 94 numero orden 95 numero campo 55 vvalorpermitido
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])==2 &&  $campos[$numero_campo]!=97 &&  $campos[$numero_campo]!=98)
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    
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
	    	    
	    //valor permitido
		if((count($resultado)==0 || !is_array($resultado))
	       && trim($campos[$numero_campo])!="97"
	       && trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 95 numero orden 96 numero campo 56 vvalorpermitido
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])==2 &&  $campos[$numero_campo]!=97 &&  $campos[$numero_campo]!=98)
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    
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
	    	    
	    //valor permitido
		if((count($resultado)==0 || !is_array($resultado))
	       && trim($campos[$numero_campo])!="97"
	       && trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 96 numero orden 97 numero campo 57 vvalorpermitido
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 97 numero orden 98 numero campo 58 vvalorpermitido
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    
	    //valor permitido
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);


		
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
		$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
		   && $campos[$numero_campo]!="1845-01-01"
		   //&& $campos[$numero_campo]!="1800-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 98 numero orden 99 numero campo 59 vvalorpermitido
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 99 numero orden 100 numero campo 60 vvalorpermitido
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="98"
		)
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	    
		
	}//if si existe campo
	
	$campo20_contra_2015=diferencia_dias_entre_fechas($campo_n20,"2015-01-01");	

	//numero_orden_desde_cero 100 numero orden 101 numero campo 61 vvalorpermitido
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="97"
		&& trim($campos[$numero_campo])!="98"
		&& trim($campos[$numero_campo])!="99"
	    	);
		if(
	    	$valores_permitidos_para_condicion_actual
	    	)
	    {
			$campos[$numero_campo]="98";
	    }
	    
		
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 101 numero orden 102 numero campo 62 vvalorpermitido
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    //valor permititdo
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
		$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
		   && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 102 numero orden 103 numero campo 63 vvalorpermitido
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 103 numero orden 104 numero campo 64 vvalorpermitido
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" )
	    {
		$campos[$numero_campo]="98";
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
	    
	    
	    //valor permitido
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_quimioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    
	    
	    if((count($resultado)==0 || !is_array($resultado))
	       && $campos[$numero_campo]!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    //si no cumple se conserva el valor
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 104 numero orden 105 numero campo 65 vvalorpermitido
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //valor permitido
		
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_quimioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if((count($resultado)==0 || !is_array($resultado))
		&& $campos[$numero_campo]!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    //si no cumple se conserva el valor
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 105 numero orden 106 numero campo 66 vvalorpermitido
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido

	    $valores_permitidos_para_condicion_actual=
	    (
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="11"
		&& trim($campos[$numero_campo])!="12"
		&& trim($campos[$numero_campo])!="13"
		&& trim($campos[$numero_campo])!="14"
		&& trim($campos[$numero_campo])!="15"
		&& trim($campos[$numero_campo])!="16"
		&& trim($campos[$numero_campo])!="17"
		&& trim($campos[$numero_campo])!="18"
		&& trim($campos[$numero_campo])!="19"
		&& trim($campos[$numero_campo])!="20"
		&& trim($campos[$numero_campo])!="21"
		&& trim($campos[$numero_campo])!="22"
		&& trim($campos[$numero_campo])!="23"
		&& trim($campos[$numero_campo])!="24"
		&& trim($campos[$numero_campo])!="25"
		&& trim($campos[$numero_campo])!="26"
		&& trim($campos[$numero_campo])!="27"
		&& trim($campos[$numero_campo])!="28"
		&& trim($campos[$numero_campo])!="29"
		&& trim($campos[$numero_campo])!="30"
		&& trim($campos[$numero_campo])!="31"
		&& trim($campos[$numero_campo])!="32"
		&& trim($campos[$numero_campo])!="33"
		&& trim($campos[$numero_campo])!="34"
		&& trim($campos[$numero_campo])!="35"
		&& trim($campos[$numero_campo])!="36"
		&& trim($campos[$numero_campo])!="37"
		&& trim($campos[$numero_campo])!="38"
		&& trim($campos[$numero_campo])!="39"
		&& trim($campos[$numero_campo])!="40"
		&& trim($campos[$numero_campo])!="41"
		&& trim($campos[$numero_campo])!="42"
		&& trim($campos[$numero_campo])!="43"
		&& trim($campos[$numero_campo])!="44"
		&& trim($campos[$numero_campo])!="45"
		&& trim($campos[$numero_campo])!="46"
		&& trim($campos[$numero_campo])!="47"
		&& trim($campos[$numero_campo])!="48"
		&& trim($campos[$numero_campo])!="49"
		&& trim($campos[$numero_campo])!="50"
		&& trim($campos[$numero_campo])!="51"
		&& trim($campos[$numero_campo])!="52"
		&& trim($campos[$numero_campo])!="53"
		&& trim($campos[$numero_campo])!="54"
		&& trim($campos[$numero_campo])!="55"
		&& trim($campos[$numero_campo])!="56"
		&& trim($campos[$numero_campo])!="57"
		&& trim($campos[$numero_campo])!="58"
		&& trim($campos[$numero_campo])!="59"
		&& trim($campos[$numero_campo])!="60"
		&& trim($campos[$numero_campo])!="61"
		&& trim($campos[$numero_campo])!="62"
		&& trim($campos[$numero_campo])!="63"
		&& trim($campos[$numero_campo])!="64"
		&& trim($campos[$numero_campo])!="65"
		&& trim($campos[$numero_campo])!="66"
		&& trim($campos[$numero_campo])!="67"
		&& trim($campos[$numero_campo])!="68"
		&& trim($campos[$numero_campo])!="69"
		&& trim($campos[$numero_campo])!="70"
		&& trim($campos[$numero_campo])!="71"
		&& trim($campos[$numero_campo])!="72"
		&& trim($campos[$numero_campo])!="73"
		&& trim($campos[$numero_campo])!="74"
		&& trim($campos[$numero_campo])!="75"
		&& trim($campos[$numero_campo])!="76"
		&& trim($campos[$numero_campo])!="77"
		&& trim($campos[$numero_campo])!="78"
		&& trim($campos[$numero_campo])!="79"
		&& trim($campos[$numero_campo])!="80"
		&& trim($campos[$numero_campo])!="81"
		&& trim($campos[$numero_campo])!="82"
		&& trim($campos[$numero_campo])!="83"
		&& trim($campos[$numero_campo])!="84"
		&& trim($campos[$numero_campo])!="85"
		&& trim($campos[$numero_campo])!="86"
		&& trim($campos[$numero_campo])!="87"
		&& trim($campos[$numero_campo])!="88"
		&& trim($campos[$numero_campo])!="89"
		&& trim($campos[$numero_campo])!="90"
		&& trim($campos[$numero_campo])!="91"
		&& trim($campos[$numero_campo])!="92"
		&& trim($campos[$numero_campo])!="93"
		&& trim($campos[$numero_campo])!="94"
		&& trim($campos[$numero_campo])!="95"
		&& trim($campos[$numero_campo])!="96"
		&& trim($campos[$numero_campo])!="97"
		&& trim($campos[$numero_campo])!="98"
	    	);
	    
	    if(
	    	$valores_permitidos_para_condicion_actual
	    	)
	    {
	    	//c4
			$campos[$numero_campo]="98";
	    }
	    
	    		
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 106 numero orden 107 numero campo 66.1 vvalorpermitido
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 107 numero orden 108 numero campo 66.2 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 108 numero orden 109 numero campo 66.3 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 109 numero orden 110 numero campo 66.4 
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 110 numero orden 111 numero campo 66.5 
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 111 numero orden 112 numero campo 66.6 
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 112 numero orden 113 numero campo 66.7 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 113 numero orden 114 numero campo 66.8 
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 114 numero orden 115 numero campo 66.9 
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 115 numero orden 116 numero campo 66.10 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 116 numero orden 117 numero campo 66.11 
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
		 
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 117 numero orden 118 numero campo 66.12 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 118 numero orden 119 numero campo 66.13 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 119 numero orden 120 numero campo 66.14 
	$numero_campo=119;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 120 numero orden 121 numero campo 66.15 
	$numero_campo=120;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 121 numero orden 122 numero campo 66.16 
	$numero_campo=121;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 122 numero orden 123 numero campo 66.17 
	$numero_campo=122;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campos
	
	
	//numero_orden_desde_cero 123 numero orden 124 numero campo 66.18 
	$numero_campo=123;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 124 numero orden 125 numero campo 66.19 
	$numero_campo=124;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 125 numero orden 126 numero campo 66.20 
	$numero_campo=125;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 126 numero orden 127 numero campo 66.21 
	$numero_campo=126;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 127 numero orden 128 numero campo 66.22 
	$numero_campo=127;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 128 numero orden 129 numero campo 66.23 
	$numero_campo=128;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 129 numero orden 130 numero campo 66.24 
	$numero_campo=129;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 130 numero orden 131 numero campo 66.25 
	$numero_campo=130;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 131 numero orden 132 numero campo 66.26 
	$numero_campo=131;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 132 numero orden 133 numero campo 66.27 
	$numero_campo=132;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 133 numero orden 134 numero campo 66.28 
	$numero_campo=133;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 134 numero orden 135 numero campo 66.29 
	$numero_campo=134;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 135 numero orden 136 numero campo 66.30 
	$numero_campo=135;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 136 numero orden 137 numero campo 66.31 
	$numero_campo=136;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 137 numero orden 138 numero campo 66.32 
	$numero_campo=137;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 138 numero orden 139 numero campo 67 vvalorpermitido
	$numero_campo=138;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])==2 &&  $campos[$numero_campo]!=97 &&  $campos[$numero_campo]!=98)
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    
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
	    	    
	    
	    if((count($resultado)==0 || !is_array($resultado))
	       && (trim($campos[$numero_campo])!="97" && trim($campos[$numero_campo])!="98")
	       )
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 139 numero orden 140 numero campo 68 vvalorpermitido
	$numero_campo=139;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])==2 &&  $campos[$numero_campo]!=97 &&  $campos[$numero_campo]!=98)
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    
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
	    	    
	    
	    if((count($resultado)==0 || !is_array($resultado))
	       && (trim($campos[$numero_campo])!="97" && trim($campos[$numero_campo])!="98")
	       )
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 140 numero orden 141 numero campo 69 vvalorpermitido
	$numero_campo=140;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])==2 &&  $campos[$numero_campo]!=97 &&  $campos[$numero_campo]!=98)
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	    //valor permitido
	    $campo_ant_45=$campos[44];
	    
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
	    	    
	    
	    if((count($resultado)==0 || !is_array($resultado))
	       && (trim($campos[$numero_campo])!="97" && trim($campos[$numero_campo])!="98")
	       )
	    {
		$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 141 numero orden 142 numero campo 70 vvalorpermitido
	$numero_campo=141;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 142 numero orden 143 numero campo 71 vvalorpermitdo
	$numero_campo=142;
	if(isset($campos[$numero_campo]))
	{
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $campo_ant_45=$campos[44];
	    
	    
	    $campo_ant_70=trim($campos[141]);
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_n61=trim($campos[100]);
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
		   && $campos[$numero_campo]!="1845-01-01"
		   && $campos[$numero_campo]!="1800-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 143 numero orden 144 numero campo 72 vvalorpermitido
	$numero_campo=143;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 144 numero orden 145 numero campo 73 
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="8"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo

	//numero_orden_desde_cero 145 numero orden 146 numero campo 74 vvalorpermitido
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])!=1)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
	       )
	    {
		    $campos[$numero_campo]="2";
	    }
	}//if si existe campo
	
	//numero_orden_desde_cero 149 numero orden 150 numero campo 78  vvalorpermitido
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" )
	    {
			$campos[$numero_campo]="98";
	    }//fin if
	    
	    //valor permitido
	    
	    $bool_normalizo=false;
	    //CUPS NORMALIZA
	    $consulta1="";
	    $consulta1.="SELECT * FROM gioss_cups WHERE trim(codigo_procedimiento)='".trim($campos[$numero_campo])."' ; ";
	    $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
	    if( count($resultado1)>0 && is_array($resultado1) && $campos[$numero_campo]!="98" )
	    {
			//si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
			$campos[$numero_campo]=$resultado1[0]["codigo_procedimiento"];
			$bool_normalizo=true;
	    }//fin if
	    //FIN NORMALIZA
	    
	    //CORRIGE HOMOLOGO CUPS
	    $consulta2="";
	    $consulta2.="SELECT * FROM gioss_homologo_cups_cfvl WHERE trim(codigo_cfvl)='".trim($campos[$numero_campo])."' ; ";
	    $resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
	    if( count($resultado2)>0 && is_array($resultado2) )
	    {
			$campos[$numero_campo]=trim($resultado2[0]["codigo_cups_homologo"]);
	    }//fin if
	    //CORRIGE HOMOLOGO CUPS
	    
	    $campo_ant_74=$campos[145];

	    $consulta="";
		$consulta.="SELECT * FROM gioss_cups WHERE codigo_procedimiento='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);

	    if( (count($resultado)==0 || !is_array($resultado) ) && $campos[$numero_campo]!="98"    )
	    {
		//si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
			$campos[$numero_campo]="98";
	    }//fin if

	    
		
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 146 numero orden 147 numero campo 75 vvalorpermitido
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    $campo_n74=trim($campos[145]);

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
			    && trim($campos[$numero_campo])!="2"
			    && trim($campos[$numero_campo])!="3"
			    && trim($campos[$numero_campo])!="4"
			    && trim($campos[$numero_campo])!="5"
			    && trim($campos[$numero_campo])!="6"
			    && trim($campos[$numero_campo])!="7"
			    && trim($campos[$numero_campo])!="8"
			    && trim($campos[$numero_campo])!="9"
			    && trim($campos[$numero_campo])!="10"
			    && trim($campos[$numero_campo])!="11"
			    && trim($campos[$numero_campo])!="12"
			    && trim($campos[$numero_campo])!="13"
			    && trim($campos[$numero_campo])!="14"
			    && trim($campos[$numero_campo])!="15"
			    && trim($campos[$numero_campo])!="16"
			    && trim($campos[$numero_campo])!="17"
			    && trim($campos[$numero_campo])!="18"
			    && trim($campos[$numero_campo])!="19"
			    && trim($campos[$numero_campo])!="20"
			    && trim($campos[$numero_campo])!="21"
			    && trim($campos[$numero_campo])!="22"
			    && trim($campos[$numero_campo])!="23"
			    && trim($campos[$numero_campo])!="24"
			    && trim($campos[$numero_campo])!="25"
			    && trim($campos[$numero_campo])!="26"
			    && trim($campos[$numero_campo])!="27"
			    && trim($campos[$numero_campo])!="28"
			    && trim($campos[$numero_campo])!="29"
			    && trim($campos[$numero_campo])!="30"
			    && trim($campos[$numero_campo])!="31"
			    && trim($campos[$numero_campo])!="32"
			    && trim($campos[$numero_campo])!="33"
			    && trim($campos[$numero_campo])!="34"
			    && trim($campos[$numero_campo])!="35"
			    && trim($campos[$numero_campo])!="36"
			    && trim($campos[$numero_campo])!="37"
			    && trim($campos[$numero_campo])!="38"
			    && trim($campos[$numero_campo])!="39"
			    && trim($campos[$numero_campo])!="40"
			    && trim($campos[$numero_campo])!="41"
			    && trim($campos[$numero_campo])!="42"
			    && trim($campos[$numero_campo])!="43"
			    && trim($campos[$numero_campo])!="44"
			    && trim($campos[$numero_campo])!="45"
			    && trim($campos[$numero_campo])!="46"
			    && trim($campos[$numero_campo])!="47"
			    && trim($campos[$numero_campo])!="48"
			    && trim($campos[$numero_campo])!="49"
			    && trim($campos[$numero_campo])!="50"
			    && trim($campos[$numero_campo])!="51"
			    && trim($campos[$numero_campo])!="52"
			    && trim($campos[$numero_campo])!="53"
			    && trim($campos[$numero_campo])!="54"
			    && trim($campos[$numero_campo])!="55"
			    && trim($campos[$numero_campo])!="56"
			    && trim($campos[$numero_campo])!="57"
			    && trim($campos[$numero_campo])!="58"
			    && trim($campos[$numero_campo])!="59"
			    && trim($campos[$numero_campo])!="60"
			    && trim($campos[$numero_campo])!="61"
			    && trim($campos[$numero_campo])!="62"
			    && trim($campos[$numero_campo])!="63"
			    && trim($campos[$numero_campo])!="64"
			    && trim($campos[$numero_campo])!="65"
			    && trim($campos[$numero_campo])!="66"
			    && trim($campos[$numero_campo])!="67"
			    && trim($campos[$numero_campo])!="68"
			    && trim($campos[$numero_campo])!="69"
			    && trim($campos[$numero_campo])!="70"
			    && trim($campos[$numero_campo])!="71"
			    && trim($campos[$numero_campo])!="72"
			    && trim($campos[$numero_campo])!="73"
			    && trim($campos[$numero_campo])!="74"
			    && trim($campos[$numero_campo])!="75"
			    && trim($campos[$numero_campo])!="76"
			    && trim($campos[$numero_campo])!="77"
			    && trim($campos[$numero_campo])!="78"
			    && trim($campos[$numero_campo])!="79"
			    && trim($campos[$numero_campo])!="80"
			    && trim($campos[$numero_campo])!="81"
			    && trim($campos[$numero_campo])!="82"
			    && trim($campos[$numero_campo])!="83"
			    && trim($campos[$numero_campo])!="84"
			    && trim($campos[$numero_campo])!="85"
			    && trim($campos[$numero_campo])!="86"
			    && trim($campos[$numero_campo])!="87"
			    && trim($campos[$numero_campo])!="88"
			    && trim($campos[$numero_campo])!="89"
			    && trim($campos[$numero_campo])!="90"
			    && trim($campos[$numero_campo])!="91"
			    && trim($campos[$numero_campo])!="92"
			    && trim($campos[$numero_campo])!="93"
			    && trim($campos[$numero_campo])!="94"
			    && trim($campos[$numero_campo])!="95"
			    && trim($campos[$numero_campo])!="96"
			    && trim($campos[$numero_campo])!="97"
			    && trim($campos[$numero_campo])!="98"
	    	);
	    //campo obligatorio
	    if(
	    	$valores_permitidos_para_condicion_actual
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 147 numero orden 148 numero campo 76 vcalorpermitido
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{	
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $campo_ant_74=$campos[145];
		
			
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
		$excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
		   && $campos[$numero_campo]!="1845-01-01"
		  )
	    {
			$campos[$numero_campo]="1845-01-01";		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 148 numero orden 149 numero campo 77 vvalorpermitido
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
		
	    //despues de que se corrige se aplica el criterio siguiente
	    $campo_ant_74=$campos[145];
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_cirugia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    if( (count($resultado)==0 || !is_array($resultado) )
	       && $campos[$numero_campo]!="98"
	       )
	    {
			$campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	
	
	
	
	//numero_orden_desde_cero 150 numero orden 151 numero campo 79 vvalorpermitido
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
				&& trim($campos[$numero_campo])!="2"
				&& trim($campos[$numero_campo])!="3"
				&& trim($campos[$numero_campo])!="4"
				&& trim($campos[$numero_campo])!="98"
				)
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
		
		
	}//if si existe campo
	
	//numero_orden_desde_cero 154 numero orden 155 numero campo 83 vvalorpermitido
	$numero_campo=154;
	if(isset($campos[$numero_campo]))
	{
		 
		
	    //formato
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" )
	    {
		$campos[$numero_campo]="98";
	    }//fin if
	    
	    //valor permitido
	    
	    $bool_normalizo=false;
	    //CUPS NORMALIZA
	    $consulta1="";
	    $consulta1.="SELECT * FROM gioss_cups WHERE trim(codigo_procedimiento)='".trim($campos[$numero_campo])."' ; ";
	    $resultado1=$coneccionBD->consultar2_no_crea_cierra($consulta1);
	    if( count($resultado1)>0 && is_array($resultado1) && $campos[$numero_campo]!="98" )
	    {
			//si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
			$campos[$numero_campo]=$resultado1[0]["codigo_procedimiento"];
			$bool_normalizo=true;
	    }//fin if
	    //FIN NORMALIZA
	    
	    //CORRIGE HOMOLOGO CUPS
	    $consulta2="";
	    $consulta2.="SELECT * FROM gioss_homologo_cups_cfvl WHERE trim(codigo_cfvl)='".trim($campos[$numero_campo])."' ; ";
	    $resultado2=$coneccionBD->consultar2_no_crea_cierra($consulta2);
	    if( count($resultado2)>0 && is_array($resultado2) )
	    {
		$campos[$numero_campo]=trim($resultado2[0]["codigo_cups_homologo"]);
	    }//fin if
	    //CORRIGE HOMOLOGO CUPS

	    $consulta="";
		$consulta.="SELECT * FROM gioss_cups WHERE codigo_procedimiento='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		
		if( (count($resultado)==0 || !is_array($resultado) ) && $campos[$numero_campo]!="98" )
	    {
		//si es asi le asigna al campo el mismo codigo pero tal y como se escribe en la base de datos
			$campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 151 numero orden 152 numero campo 80 vvalorpermitido
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
		 
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
		
		
	    $campo_n83=trim($campos[154]);
	    //campo obligatorio
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1845-01-01"
	    )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 152 numero orden 153 numero campo 81 vvalorpermitido
	$numero_campo=152;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    $campo_n83=trim($campos[154]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
			&& trim($campos[$numero_campo])!="5"
			&& trim($campos[$numero_campo])!="6"
			&& trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 153 numero orden 154 numero campo 82 
	$numero_campo=153;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
		
	    //despues de que se corrige se aplica el criterio siguiente
	    $campo_n83=trim($campos[154]);
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_cirugia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		
		if( count($resultado)==0 
	    	&& (trim($campos[$numero_campo])!="98")	       
	       )
	    {
			$campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	
	
	
	//numero_orden_desde_cero 155 numero orden 156 numero campo 84 vvalorpermitido
	$numero_campo=155;
	if(isset($campos[$numero_campo]))
	{
		
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
			&& trim($campos[$numero_campo])!="4"
			&& trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 156 numero orden 157 numero campo 85 vvalorpermitido
	$numero_campo=156;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
	    } 
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 157 numero orden 158 numero campo 86 vvalorpermitido
	$numero_campo=157;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 158 numero orden 159 numero campo 87 vvalorpermitiddo
	$numero_campo=158;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="0"
			    && trim($campos[$numero_campo])!="1"
			    && trim($campos[$numero_campo])!="2"
			    && trim($campos[$numero_campo])!="3"
			    && trim($campos[$numero_campo])!="4"
			    && trim($campos[$numero_campo])!="5"
			    && trim($campos[$numero_campo])!="6"
			    && trim($campos[$numero_campo])!="7"
			    && trim($campos[$numero_campo])!="8"
			    && trim($campos[$numero_campo])!="9"
			    && trim($campos[$numero_campo])!="10"
			    && trim($campos[$numero_campo])!="11"
			    && trim($campos[$numero_campo])!="12"
			    && trim($campos[$numero_campo])!="13"
			    && trim($campos[$numero_campo])!="14"
			    && trim($campos[$numero_campo])!="15"
			    && trim($campos[$numero_campo])!="16"
			    && trim($campos[$numero_campo])!="17"
			    && trim($campos[$numero_campo])!="18"
			    && trim($campos[$numero_campo])!="19"
			    && trim($campos[$numero_campo])!="20"
			    && trim($campos[$numero_campo])!="21"
			    && trim($campos[$numero_campo])!="22"
			    && trim($campos[$numero_campo])!="23"
			    && trim($campos[$numero_campo])!="24"
			    && trim($campos[$numero_campo])!="25"
			    && trim($campos[$numero_campo])!="26"
			    && trim($campos[$numero_campo])!="27"
			    && trim($campos[$numero_campo])!="28"
			    && trim($campos[$numero_campo])!="29"
			    && trim($campos[$numero_campo])!="30"
			    && trim($campos[$numero_campo])!="31"
			    && trim($campos[$numero_campo])!="32"
			    && trim($campos[$numero_campo])!="33"
			    && trim($campos[$numero_campo])!="34"
			    && trim($campos[$numero_campo])!="35"
			    && trim($campos[$numero_campo])!="36"
			    && trim($campos[$numero_campo])!="37"
			    && trim($campos[$numero_campo])!="38"
			    && trim($campos[$numero_campo])!="39"
			    && trim($campos[$numero_campo])!="40"
			    && trim($campos[$numero_campo])!="41"
			    && trim($campos[$numero_campo])!="42"
			    && trim($campos[$numero_campo])!="43"
			    && trim($campos[$numero_campo])!="44"
			    && trim($campos[$numero_campo])!="45"
			    && trim($campos[$numero_campo])!="46"
			    && trim($campos[$numero_campo])!="47"
			    && trim($campos[$numero_campo])!="48"
			    && trim($campos[$numero_campo])!="49"
			    && trim($campos[$numero_campo])!="50"
			    && trim($campos[$numero_campo])!="51"
			    && trim($campos[$numero_campo])!="52"
			    && trim($campos[$numero_campo])!="53"
			    && trim($campos[$numero_campo])!="54"
			    && trim($campos[$numero_campo])!="55"
			    && trim($campos[$numero_campo])!="56"
			    && trim($campos[$numero_campo])!="57"
			    && trim($campos[$numero_campo])!="58"
			    && trim($campos[$numero_campo])!="59"
			    && trim($campos[$numero_campo])!="60"
			    && trim($campos[$numero_campo])!="61"
			    && trim($campos[$numero_campo])!="62"
			    && trim($campos[$numero_campo])!="63"
			    && trim($campos[$numero_campo])!="64"
			    && trim($campos[$numero_campo])!="65"
			    && trim($campos[$numero_campo])!="66"
			    && trim($campos[$numero_campo])!="67"
			    && trim($campos[$numero_campo])!="68"
			    && trim($campos[$numero_campo])!="69"
			    && trim($campos[$numero_campo])!="70"
			    && trim($campos[$numero_campo])!="71"
			    && trim($campos[$numero_campo])!="72"
			    && trim($campos[$numero_campo])!="73"
			    && trim($campos[$numero_campo])!="74"
			    && trim($campos[$numero_campo])!="75"
			    && trim($campos[$numero_campo])!="76"
			    && trim($campos[$numero_campo])!="77"
			    && trim($campos[$numero_campo])!="78"
			    && trim($campos[$numero_campo])!="79"
			    && trim($campos[$numero_campo])!="80"
			    && trim($campos[$numero_campo])!="81"
			    && trim($campos[$numero_campo])!="82"
			    && trim($campos[$numero_campo])!="83"
			    && trim($campos[$numero_campo])!="84"
			    && trim($campos[$numero_campo])!="85"
			    && trim($campos[$numero_campo])!="86"
			    && trim($campos[$numero_campo])!="87"
			    && trim($campos[$numero_campo])!="88"
			    && trim($campos[$numero_campo])!="89"
			    && trim($campos[$numero_campo])!="90"
			    && trim($campos[$numero_campo])!="91"
			    && trim($campos[$numero_campo])!="92"
			    && trim($campos[$numero_campo])!="93"
			    && trim($campos[$numero_campo])!="94"
			    && trim($campos[$numero_campo])!="95"
			    && trim($campos[$numero_campo])!="96"
			    && trim($campos[$numero_campo])!="97"
			    && trim($campos[$numero_campo])!="98"
	    	);

	    //campo obligatorio
	    if(
	    	$valores_permitidos_para_condicion_actual
		    )	       
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 159 numero orden 160 numero campo 88 vvalorpermitido
	$numero_campo=159;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 160 numero orden 161 numero campo 89 vvalorpermitido
	$numero_campo=160;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    
	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="98"
	    	);
	    //campo obligatorio
		if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 161 numero orden 162 numero campo 90 vvalorpermitido
	$numero_campo=161;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="3"
		    && trim($campos[$numero_campo])!="4"
		    && trim($campos[$numero_campo])!="5"
		    && trim($campos[$numero_campo])!="6"
		    && trim($campos[$numero_campo])!="7"
		    && trim($campos[$numero_campo])!="98"
	    	);
	    //campo obligatorio
	    if( $valores_permitidos_para_condicion_actual )
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 162 numero orden 163 numero campo 91 valor permitido
	$numero_campo=162;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    	    
	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="98"
	    	);
	    //campo obligatorio
	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 163 numero orden 164 numero campo 92 vvalorpermitido
	$numero_campo=163;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_radioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    
	    $campo_ant_86=$campos[157];
	    //campo obligatorio

	    if(count($resultado)==0
	       && intval($campos[$numero_campo])!=98)
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 164 numero orden 165 numero campo 93 vvalorpermitdo
	$numero_campo=164;
	if(isset($campos[$numero_campo]))
	{
		
		
		//maximo  <=
		if(strlen($campos[$numero_campo])<=2 
		   && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
		{
		    $campos[$numero_campo]="98";
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
		
		//despues de que se corrige se aplica el criterio siguiente
		$campo_ant_86=$campos[157];
		
		$consulta="";
		$consulta.="SELECT * FROM gioss_cancer_ips_radioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
		$resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		
		if( count($resultado)==0 && intval($campos[$numero_campo])!=98 )
		{
		    $campos[$numero_campo]="98";
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 165 numero orden 166 numero campo 94 vvalorpermitido
	$numero_campo=165;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    

	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 166 numero orden 167 numero campo 95 vvalorpermitido
	$numero_campo=166;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    
	    $campo_ant_86=$campos[157];
	    $campo_n94=trim($campos[165]);
	    $c94_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n94,"1900-12-31");

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
			&& trim($campos[$numero_campo])!="2"
			&& trim($campos[$numero_campo])!="3"
			&& trim($campos[$numero_campo])!="98"
	    	);

	    //campo obligatorio
	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 167 numero orden 168 numero campo 96 vvalorpermitido
	$numero_campo=167;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    
	    $campo_ant_95=$campos[166];
	    $campo_ant_86=$campos[157];

	    $valores_permitidos_para_condicion_actual=(
	    	trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="98"
	    	);
	    
	    
	    if($valores_permitidos_para_condicion_actual)
	    {
			$campos[$numero_campo]="98";
	    }
	    
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 168 numero orden 169 numero campo 97 vvalorpermitido
	$numero_campo=168;
	if(isset($campos[$numero_campo]))
	{
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    $campo_ant_86=$campos[157];
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
		$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 169 numero orden 170 numero campo 98 vvalorpermitido
	$numero_campo=169;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    
	    $campo_ant_86=$campos[157];
	    $campo_n97=trim($campos[168]);
	    $c97_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n97,"1900-12-31");
	    //campo obligatorio
	    
	    $valores_permitidos_para_condicion_actual=( 
	    	trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="8"
	    && trim($campos[$numero_campo])!="9"
	    && trim($campos[$numero_campo])!="10"
	    && trim($campos[$numero_campo])!="98"
	    	);

	    if($valores_permitidos_para_condicion_actual)
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	    
	}//if si existe campos
	
	
	//numero_orden_desde_cero 170 numero orden 171 numero campo 99 vvalorpermitido
	$numero_campo=170;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	     $campo_ant_86=$campos[157];
	    $campo_n97=trim($campos[168]);
	    $c97_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n97,"1900-12-31");
	    //campo obligatorio
	    
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 171 numero orden 172 numero campo 100 vvalorpermitido
	$numero_campo=171;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2 && is_numeric($campos[$numero_campo]))
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
	    
	    //valor permitido
	    	    
	    if(trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
		&& trim($campos[$numero_campo])!="4"
		&& trim($campos[$numero_campo])!="5"
		&& trim($campos[$numero_campo])!="6"
		&& trim($campos[$numero_campo])!="7"
		&& trim($campos[$numero_campo])!="8"
		&& trim($campos[$numero_campo])!="9"
		&& trim($campos[$numero_campo])!="10"
		&& trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 172 numero orden 173 numero campo 101 vvalorpermitido
	$numero_campo=172;
	if(isset($campos[$numero_campo]))
	{
	    	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_radioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    
	    if(count($resultado)==0
	       && intval($campos[$numero_campo])!=98)
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 173 numero orden 174 numero campo 102 vvalorpermitido
	$numero_campo=173;
	if(isset($campos[$numero_campo]))
	{
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    $campo_ant_86=$campos[157];
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_radioterapia WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if( count($resultado)==0 && intval($campos[$numero_campo])!=98 )
	    {
			$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 174 numero orden 175 numero campo 103 vvalorpermitido
	$numero_campo=174;
	if(isset($campos[$numero_campo]))
	{
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    
	    
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
	       && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 175 numero orden 176 numero campo 104 vvalorpermitido
	$numero_campo=175;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 176 numero orden 177 numero campo 105 vvalorpermitido
	$numero_campo=176;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 177 numero orden 178 numero campo 106 vvalorpermitido
	$numero_campo=177;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 178 numero orden 179 numero campo 107 vvalorpermitido
	$numero_campo=178;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"	    
	    && trim($campos[$numero_campo])!="8"
	    && trim($campos[$numero_campo])!="9"
	    && trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 179 numero orden 180 numero campo 108 
	$numero_campo=179;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="98"
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 180 numero orden 181 numero campo 109 vvalorpermitido
	$numero_campo=180;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    
	    
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
	    &&    trim($campos[$numero_campo])!="1845-01-01"
	    
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 181 numero orden 182 numero campo 110 vvalorpermitido
	$numero_campo=181;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    //despues de que se corrige se aplica el criterio siguiente
	    
	    $campo_106=trim($campos[177]);
		
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_trasplante WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if(count($resultado)==0 && intval($campos[$numero_campo])!=98
	       )
	    {
			$campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 182 numero orden 183 numero campo 111 
	$numero_campo=182;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="98"
	    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 183 numero orden 184 numero campo 112 vvalorpermitido
	$numero_campo=183;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
	    	    
	    	    
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
		&& $campos[$numero_campo]!="1845-01-01")
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 184 numero orden 185 numero campo 113 vvalorpermitido
	$numero_campo=184;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    
	    $campo_ant_111=$campos[182];
	    //campo obligatorio
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_cirugia_reconstructiva WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if( count($resultado)==0 
	    	&& intval($campos[$numero_campo])!=98	       
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 185 numero orden 186 numero campo 114 
	$numero_campo=185;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
		&& trim($campos[$numero_campo])!="2"
		&& trim($campos[$numero_campo])!="3"
	       )
	    {
		    $campos[$numero_campo]="3";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 186 numero orden 187 numero campo 114.1 vvalorpermitido
	$numero_campo=186;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    $campo_ant_114=$campos[185];
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    )
	    {
		    $campos[$numero_campo]="2";
	    }//fin if
		
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 187 numero orden 188 numero campo 114.2 
	$numero_campo=187;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    $campo_ant_114=$campos[185];
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    )
	    {
		    $campos[$numero_campo]="2";
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 188 numero orden 189 numero campo 114.3 
	$numero_campo=188;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    $campo_ant_114=$campos[185];
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    )
	    {
		    $campos[$numero_campo]="2";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 189 numero orden 190 numero campo 114.4 
	$numero_campo=189;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    $campo_ant_114=$campos[185];
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    )
	    {
		    $campos[$numero_campo]="2";
	    }//fin if
		
	}//if si existe campos
	
	
	//numero_orden_desde_cero 190 numero orden 191 numero campo 114.5 
	$numero_campo=190;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    $campo_ant_114=$campos[185];
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    )
	    {
		    $campos[$numero_campo]="2";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 191 numero orden 192 numero campo 114.6 
	$numero_campo=191;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    $campo_ant_114=$campos[185];
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    )
	    {
		    $campos[$numero_campo]="2";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 192 numero orden 193 numero campo 115 vvalorpermitido
	$numero_campo=192;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    $campo_ant_114=$campos[185];
	    
	    //valor permitido
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }
	    else if($es_fecha_calendario>0
		&& $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 193 numero orden 194 numero campo 116 vvalorpermitido
	$numero_campo=193;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    $campo_ant_114=$campos[185];
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_cuidado_paliativo WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if( count($resultado)==0 
	    	&& intval($campos[$numero_campo])!=98
	    )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 194 numero orden 195 numero campo 117 vvalorpermitido
	$numero_campo=194;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="98"
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 195 numero orden 196 numero campo 118 vvalorpermitido
	$numero_campo=195;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    
	    
	    
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_primer_dia_corte;
	    }	    
	    else if($es_fecha_calendario>0
		    && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 196 numero orden 197 numero campo 119 vvalorpermitido
	$numero_campo=196;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    $campo_n117=trim($campos[194]);
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_psiquiatria WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if(count($resultado)==0 && intval($campos[$numero_campo])!=98 )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 197 numero orden 198 numero campo 120 vvalorpermitido
	$numero_campo=197;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    
	    
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="98"
	    )
	    {
		    $campos[$numero_campo]="98";
	    }//fin if
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 198 numero orden 199 numero campo 121 vvalorpermitido
	$numero_campo=198;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    
	    $campo_n120=trim($campos[197]);
	    
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
			$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		    && $campos[$numero_campo]!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 199 numero orden 200 numero campo 122 vvalorpermitido
	$numero_campo=199;
	if(isset($campos[$numero_campo]))
	{
		
		
	    //maximo  <=
	    if(strlen($campos[$numero_campo])<=2 
	       && $campos[$numero_campo]!="98" && $campos[$numero_campo]!="99")
	    {
		$campos[$numero_campo]="98";
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
	    
	    //despues de que se corrige se aplica el criterio siguiente
	    $campo_n120=trim($campos[197]);
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_cancer_ips_nutricion WHERE codigo_habilitacion='".$campos[$numero_campo]."' ; ";
	    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    if(count($resultado)==0 && intval($campos[$numero_campo])!=98 )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 200 numero orden 201 numero campo 123 vvalorpermitido
	$numero_campo=200;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    )
	    {
		    $campos[$numero_campo]="4";
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 201 numero orden 202 numero campo 124 vvalorpermitido
	$numero_campo=201;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="8"
	    && trim($campos[$numero_campo])!="98"
	    )
	    {
		    $campos[$numero_campo]="98";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 202 numero orden 203 numero campo 125 
	$numero_campo=202;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="8"	    
	    && trim($campos[$numero_campo])!="9"
	    && trim($campos[$numero_campo])!="98"
	    )
	    {
		    $campos[$numero_campo]="98";
	    }//fin if
		
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 203 numero orden 204 numero campo 126 
	$numero_campo=203;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"	    
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="97"
	    && trim($campos[$numero_campo])!="98"
	    && trim($campos[$numero_campo])!="99"
	    )
	    {
		    $campos[$numero_campo]="97";
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 204 numero orden 205 numero campo 127 
	$numero_campo=204;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"	    
	    && trim($campos[$numero_campo])!="99"
	    )
	    {
		    $campos[$numero_campo]="99";
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 205 numero orden 206 numero campo 128 
	$numero_campo=205;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if( trim($campos[$numero_campo])!="0"
	    && trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    && trim($campos[$numero_campo])!="8"	    
	    && trim($campos[$numero_campo])!="9"
	    && trim($campos[$numero_campo])!="10"
	    && trim($campos[$numero_campo])!="11"
	    && trim($campos[$numero_campo])!="12"
	    && trim($campos[$numero_campo])!="13"
	    )
	    {
		    $campos[$numero_campo]="0";
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 206 numero orden 207 numero campo 129 
	$numero_campo=206;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="5"
	    && trim($campos[$numero_campo])!="6"
	    && trim($campos[$numero_campo])!="7"
	    )
	    {
		    $campos[$numero_campo]="7";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 207 numero orden 208 numero campo 130
	$numero_campo=207;
	if(isset($campos[$numero_campo]))
	{
		
		
	    if(true)
	    {
		    
		    //correccion formato fecha al formato AAAA-MM-DD year-month-day
		    $campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    
	    }//fin if este campo no esta en blanco
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
		$campos[$numero_campo]=$fecha_de_corte;
	    }	    
	    else if($es_fecha_calendario>0
		 && $campos[$numero_campo]!="1845-01-01"
		 )
	    {
		$campos[$numero_campo]="1845-01-01";
	    }
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 208 numero orden 209 numero campo 131 
	$numero_campo=208;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    if(true)
	    {
		//correccion formato fecha al formato AAAA-MM-DD year-month-day
		$campos[$numero_campo]=corrector_formato_fecha($campos[$numero_campo],$fecha_de_corte,false,-10);
	    }
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $excede_fecha_de_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_de_corte);
		
	    //valor permitido
	    
	    $campo_n127=trim($campos[204]);
	    if($es_fecha_calendario<0
	       && $excede_fecha_de_corte<0
	       )
	    {
		$campos[$numero_campo]=$fecha_de_corte;
	    }
	    else if($es_fecha_calendario>0
		 && $campos[$numero_campo]!="1845-01-01"
		 )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 209 numero orden 210 numero campo 132 
	$numero_campo=209;
	if(isset($campos[$numero_campo]))
	{
	    //formato
	    if(strlen($campos[$numero_campo])>2)
	    {
		    $campos[$numero_campo]=intval($campos[$numero_campo]);
	    }
		
	    //valor permitido
	    if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="4"
	    && trim($campos[$numero_campo])!="98")
	    {
		    $campos[$numero_campo]="98";
	    }
	    
	}//if si existe campo
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  reparar Cancer

function reparador_calidad_CANCER(&$campos,$nlinea,
				  &$consecutivo_errores,$array_tipo_validacion,
				  $array_grupo_validacion,$array_detalle_validacion,
				  $nombre_archivo_registrado,$fecha_de_corte,$cod_prestador,
				  $cod_eapb,&$diccionario_identificacion,
				  &$diccionario_identificacion_lineas,
				  &$coneccionBD, $array_numero_campo_bd)
{
	$hubo_errores=false;
	$errores_campos="";
	
	date_default_timezone_set("America/Bogota");
	$fecha_actual = date('Y-m-d');
	$verificador=0;
	
	//$coneccionBD = new conexion();
	
	$NOMBRE_ENTIDAD_PERSONALIZADA=get_entidad_personalizada();
	
	/*
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='03' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0301' ORDER BY numero_de_orden ";
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
	
			
	//CAMPO 20 CONTRA FECHA DE CORTE
	$campo_n20=$campos[19];
	
	//solo year
			
	$array_fecha_campo_20=explode("-",$campo_n20);
	$verificar_validez_para_comparar_fecha_anterior=true;
	
	
	$year_campo_20=$array_fecha_campo_20[0];
	$fecha_de_corte_array=explode("-",$fecha_de_corte);
	$year_fecha_de_corte=$fecha_de_corte_array[0];
	$corresponde_year_campo20_con_year_fecha_corte=false;
	if(ctype_digit($year_campo_20) && ctype_digit($year_fecha_de_corte))
	{
	    if(trim($year_campo_20)==trim($year_fecha_de_corte))
	    {
		$corresponde_year_campo20_con_year_fecha_corte=true;
	    }
	}
	//fin solo year
	
	$campo20_contra_fecha_corte_global=0;
	$campo20_contra_fecha_corte_global=diferencia_dias_entre_fechas($campo_n20,$fecha_de_corte);
	$campo20_es_fecha_calendario=0;
	$campo20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n20,"1900-12-31");
	//FIN CAMPO 20 CONTRA FECHADE CORTE
	
	//numero_orden_desde_cero 0 numero orden 1 numero campo 1 vcalidad
	$numero_campo=0;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	//numero_orden_desde_cero 1 numero orden 2 numero campo 2 
	$numero_campo=1;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	//numero_orden_desde_cero 2 numero orden 3 numero campo 3 
	$numero_campo=2;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 3 numero orden 4 numero campo 4 
	$numero_campo=3;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 4 numero orden 5 numero campo 5 TI
	$numero_campo=4;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 5 numero orden 6 numero campo 6 Numero ID
	$numero_campo=5;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 6 numero orden 7 numero campo 7 
	$numero_campo=6;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 7 numero orden 8 numero campo 8 
	$numero_campo=7;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 8 numero orden 9 numero campo 9 
	$numero_campo=8;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica			
	}//if si existe campo
	
	
	//numero_orden_desde_cero 9 numero orden 10 numero campo 10 
	$numero_campo=9;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica			
	}//if si existe campo
	
	
	//numero_orden_desde_cero 10 numero orden 11 numero campo 11 
	$numero_campo=10;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 11 numero orden 12 numero campo 12 
	$numero_campo=11;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 12 numero orden 13 numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 13 numero orden 14 numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 14 numero orden 15 numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica			
	}//if si existe campo
	
	
	//numero_orden_desde_cero 15 numero orden 16 numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{		
	    //no aplica	
	}//if si existe campo
	
	
	//numero_orden_desde_cero 16 numero orden 17 numero campo 17 
	$numero_campo=16;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica			
	}//if si existe campo
	
	
	//numero_orden_desde_cero 17 numero orden 18 numero campo 18 
	$numero_campo=17;
	if(isset($campos[$numero_campo]))
	{
	    /*
	    $campo_n20=trim($campos[19]);
	    $campo_n7=trim($campos[6]);
	    
	    $campo7_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n7,"1900-12-31");
	    $edad_calculada_para_18=diferencia_dias_entre_fechas($campo_n7,$campo_n20);
	    
	    if($campo20_es_fecha_calendario<0
	       && $edad_calculada_para_18>0
	       && $campo7_es_fecha_calendario<0
	       )
	    {
		$array_fecha_nacimiento=explode("-",$campo_n7);
		$array_fecha_c20=explode("-",$campo_n20);
		$array_edad=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_c20[2]."-".$array_fecha_c20[1]."-".$array_fecha_c20[0]);
		$edad_en_year_con_bis=intval($array_edad['y']);
		
		$resta_en_years=intval($campos[$numero_campo])-$edad_en_year_con_bis;
			
		if($resta_en_years>2 || $resta_en_years<-2)
		{
		    $campos[$numero_campo]=$edad_en_year_con_bis;
		}
	    }//fin if
	    */
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 18 numero orden 19 numero campo 19 
	$numero_campo=18;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_20=$campos[19];
	    /*
	    if($campos[$numero_campo]=="1800-01-01" && $corresponde_year_campo20_con_year_fecha_corte)
	    {
		$campos[$numero_campo]=$campo_ant_20;
	    }
	    */
	}//if si existe campo
	
	
	//numero_orden_desde_cero 19 numero orden 20 numero campo 20 
	$numero_campo=19;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica
			
	}//if si existe campo
	
	
	
	
	$campo_ant_20=$campos[19];
	$c20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_20,"1900-12-31");
	//$fecha_primer_dia_corte=$year_fecha_de_corte."-01-01";
	$mes_fecha_de_corte=$fecha_de_corte_array[1];
	$fecha_primer_dia_corte=$year_fecha_de_corte."-".$mes_fecha_de_corte."-01";
	$c20_es_mayor_primer_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_20,$fecha_primer_dia_corte);
	$c20_es_menor_ultimo_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_20,$fecha_de_corte);
	
	
	
	//numero_orden_desde_cero 20 numero orden 21 numero campo 21 vcalidad
	$numero_campo=20;
	if(isset($campos[$numero_campo]))
	{	    	
	    $campo_ant_20=$campos[19];
	    $campo_ant_24=$campos[23];
	    
	    if($campos[$numero_campo]=="99"
		    && $corresponde_year_campo20_con_year_fecha_corte)
	    {
			$campos[$numero_campo]="8";
	    }

	    $campo_n128=trim($campos[205]);

	    $fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
		$fecha_c18_mayor_igual_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

		$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");
	    
	    $campo_n22=trim($campos[21]);
	    if(trim($campos[$numero_campo])=="7"
		    && $campo_n22=="98"
		    && $fecha_c18_fecha_calendario<0
			   && $fecha_c18_mayor_igual_2015<=0
		    )
	    {
			$campos[$numero_campo]="8";
	    }
	    else if(trim($campos[$numero_campo])!="7"
		    && $campo_n22!="98" 
		    && $fecha_c18_fecha_calendario<0
			   && $fecha_c18_mayor_igual_2015<=0
		    )
	    {
			$campos[$numero_campo]="7";
	    }
	    else if(trim($campos[$numero_campo])=="99"
		    && $campo_n22!="98"
		    && $fecha_c18_fecha_calendario<0
			   && $fecha_c18_mayor_igual_2015<=0
		    )
	    {
			$campos[$numero_campo]="7";
	    }
	    else if(trim($campos[$numero_campo])!="99"
		    && $campo_n22=="98"
		    && (
		    	($fecha_c18_fecha_calendario<0
			   && $fecha_c18_mayor_igual_2015>0)//es menor
		    	|| $fecha_campo_18_fecha_diagnostico=="1800-01-01"
		    	)
		    )
	    {
			$campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 21 numero orden 22 numero campo 22 vcalidad
	$numero_campo=21;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_21=$campos[20];
	    if($campos[$numero_campo]=="98"
	       && $campo_ant_21=="7"
	       )
	    {
		$campos[$numero_campo]="99";  
		    
	    }//fin campo no esta en blanco
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_21!="7")
		    )
	    {
		$campos[$numero_campo]="98";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 22 numero orden 23 numero campo 23 vcalidad
	$numero_campo=22;
	if(isset($campos[$numero_campo]))
	{
			
	    $campo_ant_21=$campos[20];
	    $campo_ant_20=$campos[19];

	    $fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
		$fecha_c18_menor_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

		$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");
	    
	    if($campos[$numero_campo]!="1845-01-01"
		    && $campo_ant_21=="7"
		    )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }//campo no esta en blanco
	    else if($campos[$numero_campo]=="1845-01-01"
	       && $campo_ant_21!="7"
	       && (
	       	($fecha_c18_fecha_calendario<0 && $fecha_c18_menor_2015>0)
	        	|| $fecha_campo_18_fecha_diagnostico=="1800-01-01"
	        	)
	       )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }//campo no esta en blanco
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 23 numero orden 24 numero campo 24 vcalidad
	$numero_campo=23;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_21=$campos[20];
	    $campo_ant_20=$campos[19];
	    $campo_ant_23=$campos[22];
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $c23_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_23,"1900-12-31");
	    //$fecha_primer_dia_corte=$year_fecha_de_corte."-01-01";
	    $mes_fecha_de_corte=$fecha_de_corte_array[1];
		$fecha_primer_dia_corte=$year_fecha_de_corte."-".$mes_fecha_de_corte."-01";
	    $c23_es_mayor_primer_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_23,$fecha_primer_dia_corte);
	    $c23_es_menor_ultimo_dia_fecha_corte=diferencia_dias_entre_fechas($campo_ant_23,$fecha_de_corte);

	    $c23_es_menor_a_2015=diferencia_dias_entre_fechas($campo_ant_23,"2015-01-01");
	    
	    if($campos[$numero_campo]!="1845-01-01"
	       && $campo_ant_23=="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }//campo no esta en blanco
	    else if($campos[$numero_campo]=="1845-01-01"
		    && ( ($c23_es_fecha_calendario<0
		    && $c23_es_menor_a_2015>0 ) || $campo_ant_23=="1800-01-01" )
		    )
	    {
			$campos[$numero_campo]="1800-01-01";
	    }//campo no esta en blanco
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 24 numero orden 25 numero campo 25 vcalidad
	$numero_campo=24;
	if(isset($campos[$numero_campo]))
	{
	    
	    
	    //campo obligatorio
	    $campo_ant_20=$campos[19];
	    $campo_ant_24=$campos[23];

	    $c24_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_24,"1900-12-31");
	    $c24_es_menor_2015=diferencia_dias_entre_fechas($campo_ant_24,"2015-01-01");
	    
	    if(trim($campos[$numero_campo])!="98"
		    && $campo_ant_24=="1845-01-01"
		    )
	    {
		    $campos[$numero_campo]="98";
	    }//campo no esta en blanco
	    else if(trim($campos[$numero_campo])=="98"
		    && ( 
		    	($c24_es_fecha_calendario<0 && $c24_es_menor_2015>0) 
		    	|| $campo_ant_24=="1800-01-01"
		    	)
		    )
	    {
		    $campos[$numero_campo]="99";
	    }//campo no esta en blanco
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 25 numero orden 26 numero campo 26 vcalidad
	$numero_campo=25;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
		
			
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    /*
	    if($campos[$numero_campo]=="1845-01-01"  && $campo20_contra_fecha_corte_global>0)
	    {
		    $campos[$numero_campo]="1800-01-01";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="1800-01-01"  && $corresponde_year_campo20_con_year_fecha_corte)
	    {
		    $campos[$numero_campo]=$campo_ant_20;
		    
	    }//fin if
	    */
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 26 numero orden 27 numero campo 27 vcalidad
	$numero_campo=26;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_24=$campos[23];
	    
	    //campo obligatorio
	    $campo_n28=trim($campos[27]);
	    
	    if(trim($campos[$numero_campo])!="98" 
		&& $campo_n28=="98" 
		)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if( trim($campos[$numero_campo])=="98"
	       && $campo_n28!="98"
	       )
	    {
		    $campos[$numero_campo]="99";
	    }

	}//if si existe campo
	
	//numero_orden_desde_cero 27 numero orden 28 numero campo 28 vcalidad
	$numero_campo=27;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
		$campo_n17=trim($campos[16]);
		
		$string_numeros_comparar="";
		$cumple_condicion_con_rangos_codigo_campo_17=false;
		$cumple_condicion_b_con_rangos_codigo_campo_17=false;
		$cumple_condicion_2_con_rangos_codigo_campo_17=false;
		$empieza_por_d=false;
		if(strlen($campo_n17)==4)
		{
		    $string_numeros_comparar=$campo_n17;
		    $string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		    $string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		    
		    $string_numeros_comparar_2=$campo_n17;
		    $string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		    $string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		    
		    $tiene_letra_principio=substr($campo_n17,0,1);
		    
		    $tiene_letra_final=substr($campo_n17,-1);
		    
		    if(ctype_digit($string_numeros_comparar)
		       && $tiene_letra_principio=="C"
		       //&& $tiene_letra_final!="X"
		       )
		    {
				$numero_int=intval($string_numeros_comparar);
				if(($numero_int>=810
				   && $numero_int<=880)
				   || ($numero_int>=900
				   && $numero_int<=959)
				   )
				{
				    $cumple_condicion_con_rangos_codigo_campo_17=true;
				}

				if(($numero_int>=0
				   && $numero_int<=798)
				   || ($numero_int>=881
				   && $numero_int<=889)
				   || ($numero_int>=960
				   && $numero_int<=969)
				   )
				{
				    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
				}
			
		    }// es digito y c al principio
		    if(ctype_digit($string_numeros_comparar_2)
		       && $tiene_letra_principio=="C"
		       && $tiene_letra_final=="X"
		       )
		    {
				$numero_int=intval($string_numeros_comparar_2);
				if(($numero_int==80
				   || $numero_int==97)
				   )
				{
				    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
				}
			
		    }// es digito y c al principio
		    else if(ctype_digit($string_numeros_comparar)
		       && $tiene_letra_principio=="D"
		       )
		    {
				$numero_int=intval($string_numeros_comparar);
				if(($numero_int>=0
				    && $numero_int<=99)
				    )
				{
				    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
				}
			
		    }// es digito y d al principio
		    
		    if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		    {
				$empieza_por_d=true;
		    }
		}//fin condicion rangos campo 17 mira longitud 4 caracteres
		//FIN COMPARACION RANGOS CON CAMPO 17
	    
	    //campo obligatorio
		$campo_27=trim($campos[26]);
		$campo_n27=trim($campos[26]);
	    
	    //campo obligatorio
		if(trim($campos[$numero_campo])!="98"
    		&& $cumple_condicion_con_rangos_codigo_campo_17==true
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="98"
	    	&& ($campo_n27=="20" || $campo_n27=="98" )
	       && $cumple_condicion_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	    	 && ($campo_n27!="20" && $campo_n27!="98" )
	    	 && $cumple_condicion_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="99";
	    }//fin calidad
	    

	    

	}//if si existe campo
	
	
	//numero_orden_desde_cero 28 numero orden 29 numero campo 29 vcalidad
	$numero_campo=28;
	if(isset($campos[$numero_campo]))
	{
	    
	    //COMPARACION RANGOS CON CAMPO 17
		$campo_n17=trim($campos[16]);
		
		$string_numeros_comparar="";
		$cumple_condicion_con_rangos_codigo_campo_17=false;
		$cumple_condicion_b_con_rangos_codigo_campo_17=false;
		$cumple_condicion_2_con_rangos_codigo_campo_17=false;
		$cumple_condicion_con_rangos_d_codigo_campo_17=false;
		$empieza_por_d=false;
		if(strlen($campo_n17)==4)
		{
		    $string_numeros_comparar=$campo_n17;
		    $string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		    $string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		    
		    $string_numeros_comparar_2=$campo_n17;
		    $string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		    $string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		    
		    $tiene_letra_principio=substr($campo_n17,0,1);
		    
		    $tiene_letra_final=substr($campo_n17,-1);
		    
		    if(ctype_digit($string_numeros_comparar)
		       && $tiene_letra_principio=="C"
		       //&& $tiene_letra_final!="X"
		       )
		    {
				$numero_int=intval($string_numeros_comparar);
				if(($numero_int>=810
				   && $numero_int<=880)
				   || ($numero_int>=900
				   && $numero_int<=959)
				   )
				{
				    $cumple_condicion_con_rangos_codigo_campo_17=true;
				}

				if(($numero_int>=0
				   && $numero_int<=798)
				   || ($numero_int>=881
				   && $numero_int<=889)
				   || ($numero_int>=960
				   && $numero_int<=969)
				   )
				{
				    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
				}
			
		    }// es digito y c al principio
		    if(ctype_digit($string_numeros_comparar_2)
		       && $tiene_letra_principio=="C"
		       && $tiene_letra_final=="X"
		       )
		    {
				$numero_int=intval($string_numeros_comparar_2);
				if(($numero_int==80
				   || $numero_int==97)
				   )
				{
				    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
				}
			
		    }// es digito y c al principio
		    else if(ctype_digit($string_numeros_comparar)
		       && $tiene_letra_principio=="D"
		       )
		    {
				$numero_int=intval($string_numeros_comparar);
				if(($numero_int>=0
				    && $numero_int<=99)
				    )
				{
				    $cumple_condicion_2_con_rangos_codigo_campo_17=true;
				    $cumple_condicion_con_rangos_d_codigo_campo_17=true;
				}
			
		    }// es digito y d al principio
		    
		    if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		    {
				$empieza_por_d=true;
		    }
		}//fin condicion rangos campo 17 mira longitud 4 caracteres
		//FIN COMPARACION RANGOS CON CAMPO 17
	    
	    $campo_ant_28=$campos[27];
	    $campo_n27=trim($campos[26]);

	    if($campos[$numero_campo]!="98" 
	    	&& ($cumple_condicion_con_rangos_codigo_campo_17==true)
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="98";
		    
	    }//fin if este campo no esta en blanco
	    else if($campos[$numero_campo]!="0"
	    	 && ($cumple_condicion_con_rangos_d_codigo_campo_17==true)	   
		    )
	    {
	    	//c4
		    $campos[$numero_campo]="0";
	    }
	    else if(trim($campos[$numero_campo])!="1"
			     && trim($campos[$numero_campo])!="2"
			     && trim($campos[$numero_campo])!="3"
			     && trim($campos[$numero_campo])!="4"
			     && trim($campos[$numero_campo])!="5"
			     && trim($campos[$numero_campo])!="6"
			     && trim($campos[$numero_campo])!="7"
			     && trim($campos[$numero_campo])!="8"
			     && trim($campos[$numero_campo])!="9"
			     && trim($campos[$numero_campo])!="10"
			     && trim($campos[$numero_campo])!="11"
			     && trim($campos[$numero_campo])!="12"
			     && trim($campos[$numero_campo])!="13"
			     && trim($campos[$numero_campo])!="14"
			     && trim($campos[$numero_campo])!="15"
			     && trim($campos[$numero_campo])!="16"
			     && trim($campos[$numero_campo])!="17"
			     && trim($campos[$numero_campo])!="18"
			     && trim($campos[$numero_campo])!="19"
			     && trim($campos[$numero_campo])!="20"
			     && trim($campos[$numero_campo])!="21"			     
			     && trim($campos[$numero_campo])!="22"
			     && trim($campos[$numero_campo])!="23"
			     && trim($campos[$numero_campo])!="24"			     
			     && trim($campos[$numero_campo])!="25"				
			     && trim($campos[$numero_campo])!="99"
	    	&& ($cumple_condicion_con_rangos_codigo_campo_17==false)
	    	&& ($cumple_condicion_con_rangos_d_codigo_campo_17==false)

	       )
	    {
	    	//c2
		    $campos[$numero_campo]="99";
		    
	    }

	    
		
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 29 numero orden 30 numero campo 30 vcalidad
	$numero_campo=29;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_28=$campos[27];
	    $campo_ant_20=$campos[19];

	    $campo_n29=trim($campos[28]);

	    //campo obligatorio
	    $fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
		$fecha_c18_menor_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

		$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="1845-01-01"
	       && $campo_n29=="98"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1845-01-01"
	       && $campo_n29!="98"
	       &&( ($fecha_c18_fecha_calendario<0 && $fecha_c18_menor_2015>=0)
		   || $fecha_campo_18_fecha_diagnostico=="1800-01-01")
	       )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }

	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 30 numero orden 31 numero campo 31 vcalidad
	$numero_campo=30;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
	    $campo_n17=trim($campos[16]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_2_con_rangos_codigo_campo_17=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n17)==4)
	    {
		$string_numeros_comparar=$campo_n17;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n17;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n17,0,1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=500
		       && $numero_int<=509 && $numero_int!=507)
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int==50
				   || $numero_int==51
				   || $numero_int==57
				   || $numero_int==59
				   )
			)
		    {
			$cumple_condicion_2_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y d al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 17 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 17
	    
	    $campo_n32=trim($campos[31]);
			
	    $c32_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n32,"1900-12-31");
	    //campo obligatorio

	    $campo_n33=trim($campos[32]);

	    if(trim($campos[$numero_campo])!="98"	       
		&& $cumple_condicion_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="97"	       
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==true
	       )//fin condicion if
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if(trim($campos[$numero_campo])!="2"	       
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true)
		&& $campo_n33=="98"
	       )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])!="1"	       
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true)
		&& (
			$campo_n33=="1"
			|| $campo_n33=="2"
			|| $campo_n33=="3"
			)
	     )//fin condicion if
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if(trim($campos[$numero_campo])!="99"	       
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true)
		&& $campo_n33=="99"
	       )
	    {
		    $campos[$numero_campo]="99";
	    }//fin

	    
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 31 numero orden 32 numero campo 32 vcalidad
	$numero_campo=31;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
	    $campo_n17=trim($campos[16]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_2_con_rangos_codigo_campo_17=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n17)==4)
	    {
		$string_numeros_comparar=$campo_n17;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n17;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n17,0,1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=500
		       && $numero_int<=509 && $numero_int!=507)
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int==50
				   || $numero_int==51
				   || $numero_int==57
				   || $numero_int==59
				   )
			)
		    {
			$cumple_condicion_2_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y d al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 17 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 17
	    
	    
	    $campo_ant_31=trim($campos[30]);
	    
	    //campo obligatorio
	    //campo obligatorio
	    $fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
		$fecha_c18_menor_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

		$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");
	    
	    //campo obligatorio

	    $campo_ant_33=trim($campos[32]);
	    if(trim($campos[$numero_campo])!="1845-01-01"	       
		&& $cumple_condicion_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1845-01-01"   
		&& ($campo_ant_33=="98")
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true
			|| $cumple_condicion_2_con_rangos_codigo_campo_17==true
			)
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])!="1840-01-01"   
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==true
			
	      )//fin condicion
	    {
		    $campos[$numero_campo]="1840-01-01";
	    }
	    else if( (trim($campos[$numero_campo])=="1845-01-01"
	    	|| trim($campos[$numero_campo])=="1840-01-01"
	    	)   
		&& ($campo_ant_33!="97" && $campo_ant_33!="99")
		&& $cumple_condicion_con_rangos_codigo_campo_17==true
	       )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }//fin 


	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 32 numero orden 33 numero campo 33 vcalidad
	$numero_campo=32;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
	    $campo_n17=trim($campos[16]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_2_con_rangos_codigo_campo_17=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n17)==4)
	    {
		$string_numeros_comparar=$campo_n17;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n17;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n17,0,1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=500
		       && $numero_int<=509 && $numero_int!=507)
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int==50
				   || $numero_int==51
				   || $numero_int==57
				   || $numero_int==59
				   )
			)
		    {
			$cumple_condicion_2_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y d al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 17 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 17
	    
	    $campo_n31=trim($campos[30]);
			
	    //campo obligatorio
	    $campo_n32=trim($campos[31]);
		$campo_32_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n32,"1900-12-31");

	    if(trim($campos[$numero_campo])!="98"	       
		&& $cumple_condicion_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="98"   
		&& $cumple_condicion_con_rangos_codigo_campo_17==true
		&& ($campo_n32=="1845-01-01")
       	)
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="97"	       
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==true
	       )
	    {
		    $campos[$numero_campo]="97";
	    }
	    else if(trim($campos[$numero_campo])!="1"
	    && trim($campos[$numero_campo])!="2"
	    && trim($campos[$numero_campo])!="3"
	    && trim($campos[$numero_campo])!="99"   
		&& $cumple_condicion_con_rangos_codigo_campo_17==true
		&& ($campo_n32=="1800-01-01" || $campo_32_es_fecha_calendario<0)
       	)
	    {
		    $campos[$numero_campo]="97";
	    }
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 33 numero orden 34 numero campo 34 vcalidad
	$numero_campo=33;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
	    $campo_n17=trim($campos[16]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_b_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_2_con_rangos_codigo_campo_17=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n17)==4)
	    {
		$string_numeros_comparar=$campo_n17;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n17;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n17,0,1);
		
		$tiene_letra_final=substr($campo_n17,-1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   //&& $tiene_letra_final!="X"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=180
		       && $numero_int<=189)
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		if(ctype_digit($string_numeros_comparar_2)
		   && $tiene_letra_principio=="C"
		   && $tiene_letra_final=="X"
		   )
		{
		    $numero_int=intval($string_numeros_comparar_2);
		    if(($numero_int>=19
		       && $numero_int<=20)
		       )
		    {
			$cumple_condicion_b_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=10
			&& $numero_int<=12)
			)
		    {
			$cumple_condicion_2_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y d al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 17 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 17
	    
	    //campo obligatorio
	    $campo_n35=trim($campos[34]);
	    if(trim($campos[$numero_campo])!="98"	       
		&& $cumple_condicion_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_b_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"	       
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true
		    || $cumple_condicion_b_con_rangos_codigo_campo_17==true
		    || $cumple_condicion_2_con_rangos_codigo_campo_17==true
		    )
		&& $campo_n35=="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="99";
	    }
	    else if(trim($campos[$numero_campo])=="98"	       
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true
		    || $cumple_condicion_b_con_rangos_codigo_campo_17==true
		    || $cumple_condicion_2_con_rangos_codigo_campo_17==true
		    )
		&& $campo_n35!="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="1";
	    }


		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 34 numero orden 35 numero campo 35 vcalidad
	$numero_campo=34;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
	    $campo_n17=trim($campos[16]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_b_con_rangos_codigo_campo_17=false;
	    $cumple_condicion_2_con_rangos_codigo_campo_17=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n17)==4)
	    {
		$string_numeros_comparar=$campo_n17;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n17;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n17,0,1);
		
		$tiene_letra_final=substr($campo_n17,-1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   //&& $tiene_letra_final!="X"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=180
		       && $numero_int<=189)
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		if(ctype_digit($string_numeros_comparar_2)
		   && $tiene_letra_principio=="C"
		   && $tiene_letra_final=="X"
		   )
		{
		    $numero_int=intval($string_numeros_comparar_2);
		    if(($numero_int>=19
		       && $numero_int<=20)
		       )
		    {
			$cumple_condicion_b_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(($numero_int>=10
			&& $numero_int<=12)
			)
		    {
			$cumple_condicion_2_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y d al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 17 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 17
	    
	    $campo_n34=$campos[33];
	    
	    $campo_n20=$campos[19];
	    
	    
	    $c20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n20,"1900-12-31");

	    //campo obligatorio
	    $fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
		$fecha_c18_menor_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

		$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="1845-01-01"      
		&& $cumple_condicion_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_b_con_rangos_codigo_campo_17==false
		&& $cumple_condicion_2_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }	    
	    else if(trim($campos[$numero_campo])!="1845-01-01"       
		&& ($cumple_condicion_con_rangos_codigo_campo_17==true
		    || $cumple_condicion_b_con_rangos_codigo_campo_17==true
		    || $cumple_condicion_2_con_rangos_codigo_campo_17==true
		    )
		&& ($campo_n34=="98" || $campo_n34=="99")
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1845-01-01"	   //decia 1845-01-01    
			&& ($cumple_condicion_con_rangos_codigo_campo_17==true
			    || $cumple_condicion_b_con_rangos_codigo_campo_17==true
			    || $cumple_condicion_2_con_rangos_codigo_campo_17==true
			    )
			&& ($campo_n34!="98" && $campo_n34!="99")
	       )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 35 numero orden 36 numero campo 36 vcalidad
	$numero_campo=35;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
	    $campo_n17=trim($campos[16]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_17=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n17)==4)
	    {
		$string_numeros_comparar=$campo_n17;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n17;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n17,0,1);
		
		$tiene_letra_final=substr($campo_n17,-1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   //&& $tiene_letra_final!="X"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if(
		    	(
						$numero_int==810
						|| $numero_int==811
						|| $numero_int==812
						|| $numero_int==813
						|| $numero_int==817
						|| $numero_int==819
						|| $numero_int==820
						|| $numero_int==821
						|| $numero_int==822
						|| $numero_int==827
						|| $numero_int==829
						|| $numero_int==830
						|| $numero_int==831
						|| $numero_int==832
						|| $numero_int==833
						|| $numero_int==834
						|| $numero_int==835
						|| $numero_int==836
						|| $numero_int==837
						|| $numero_int==838
						|| $numero_int==839
						|| $numero_int==840
						|| $numero_int==841
						|| $numero_int==842
						|| $numero_int==843
						|| $numero_int==844
						|| $numero_int==845
						|| $numero_int==850
						|| $numero_int==851
						|| $numero_int==857
						|| $numero_int==859
						|| $numero_int==880
						|| $numero_int==900
						|| $numero_int==901
						|| $numero_int==902
						|| $numero_int==910
						|| $numero_int==911
						|| $numero_int==912
						|| $numero_int==913
						|| $numero_int==914
						|| $numero_int==915
						|| $numero_int==917
						|| $numero_int==918
						|| $numero_int==919
						|| $numero_int==920
						|| $numero_int==921
						|| $numero_int==922
						|| $numero_int==923
						|| $numero_int==924
						|| $numero_int==925
						|| $numero_int==927
						|| $numero_int==929
						|| $numero_int==930
						|| $numero_int==931
						|| $numero_int==932
						|| $numero_int==937
						|| $numero_int==939
						|| $numero_int==940
						|| $numero_int==941
						|| $numero_int==942
						|| $numero_int==943
						|| $numero_int==944
						|| $numero_int==945
						|| $numero_int==947
						|| $numero_int==950
						|| $numero_int==951
						|| $numero_int==952
						|| $numero_int==957
						|| $numero_int==959
						)
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_17=true;
		    }
		    
		}// es digito y c al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 17 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 17
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && $cumple_condicion_con_rangos_codigo_campo_17==false
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && $cumple_condicion_con_rangos_codigo_campo_17==true
	       )
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 36 numero orden 37 numero campo 37 vcalidad
	$numero_campo=36;
	if(isset($campos[$numero_campo]))
	{
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
		&& $campo_n17!="D075"
		&& $campo_n17!="C61X"
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		&& ($campo_n17=="D075"
		|| $campo_n17=="C61X"
		)
	       )
	    {
		    $campos[$numero_campo]="99";
	    }
	}//if si existe campo
	
	$campo_n7=trim($campos[6]);
	$array_fecha_nacimiento=explode("-",$campo_n7);
	$array_fecha_corte_para_nacimiento=explode("-",$fecha_de_corte);
	$array_edad_fc=edad_years_months_days($array_fecha_nacimiento[2]."-".$array_fecha_nacimiento[1]."-".$array_fecha_nacimiento[0],$array_fecha_corte_para_nacimiento[2]."-".$array_fecha_corte_para_nacimiento[1]."-".$array_fecha_corte_para_nacimiento[0]);
	$edad_en_year_calc_fc=intval($array_edad_fc['y']);
	
	//numero_orden_desde_cero 37 numero orden 38 numero campo 38 vcalidad
	$numero_campo=37;
	if(isset($campos[$numero_campo]))
	{
	    //COMPARACION RANGOS CON CAMPO 17
			$campo_n17=trim($campos[16]);
			
			$string_numeros_comparar="";
			$cumple_condicion_con_rangos_codigo_campo_17=false;
			$cumple_condicion_a_con_rangos_codigo_campo_17=false;
			$cumple_condicion_b_con_rangos_codigo_campo_17=false;
			$cumple_condicion_2_con_rangos_codigo_campo_17=false;
			$empieza_por_d=false;
			if(strlen($campo_n17)==4)
			{
			    $string_numeros_comparar=$campo_n17;
			    $string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
			    $string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
			    
			    $string_numeros_comparar_2=$campo_n17;
			    $string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
			    $string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
			    
			    $tiene_letra_principio=substr($campo_n17,0,1);
			    
			    $tiene_letra_final=substr($campo_n17,-1);
			    
			    //echo "<script>alert('c17 $campo_n17 str1 $string_numeros_comparar str2 $string_numeros_comparar_2 li $tiene_letra_principio lf $tiene_letra_final ')</script>";
			    
			    if(ctype_digit($string_numeros_comparar)
			       && $tiene_letra_principio=="C"
			       //&& $tiene_letra_final!="X"
			       )
			    {
					//echo "<script>alert('empezo por C')</script>";
					$numero_int=intval($string_numeros_comparar);
					if(
							($numero_int>=810
						   && $numero_int<=880)
						   ||
						   ($numero_int>=900
						   && $numero_int<=959				   
						   )
					   )
					{
					    $cumple_condicion_con_rangos_codigo_campo_17=true;
					}

					if(
						($numero_int>=0
					   && $numero_int<=798)
					   ||
					   ($numero_int>=881
					   && $numero_int<=889				   
					   )
					   ||
					   ($numero_int>=960
					   && $numero_int<=969				   
					   )
				   )
					{
					    $cumple_condicion_a_con_rangos_codigo_campo_17=true;
					}
				
			    }// es digito y c al principio
			    if(ctype_digit($string_numeros_comparar_2)
			       && $tiene_letra_principio=="C"
			       && $tiene_letra_final=="X"
			       )
			    {
				$numero_int=intval($string_numeros_comparar_2);
				if(
					($numero_int==01
				   || $numero_int==07
				   || $numero_int==12
				   || $numero_int==19
				   || $numero_int==20
				   || $numero_int==23
				   || $numero_int==33
				   || $numero_int==52
				   || $numero_int==55
				   || $numero_int==56
				   || $numero_int==58
				   || $numero_int==61
				   || $numero_int==64
				   || $numero_int==65
				   || $numero_int==66
				   || $numero_int==73
				   || $numero_int==80
				   || $numero_int==97
				   )

				   )
				{
				    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
				}
				
			    }// es digito y c al principio
			    else if(ctype_digit($string_numeros_comparar)
			       && $tiene_letra_principio=="D"
			       )
			    {
				$numero_int=intval($string_numeros_comparar);
				if(($numero_int>=0
				    && $numero_int<=99)
				    )
				{
				    $cumple_condicion_2_con_rangos_codigo_campo_17=true;
				}
				
			    }// es digito y d al principio
			    
			    if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
			    {
				$empieza_por_d=true;
			    }
			}//fin condicion rangos campo 17 mira longitud 4 caracteres
			//FIN COMPARACION RANGOS CON CAMPO 17


			//llamado a calculo edad
			$campo_n7=trim($campos[6]);
			$campo_n18=trim($campos[17]);
			$campo18_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n18,"1900-12-31");
			$year_edad_cal_dep=-1;
			if($campo18_es_fecha_calendario<0)
			{
				$year_edad_cal_dep=edad_calculada_a_partir_de_dos_fechas($campo_n7,$campo_n18,"y");
			}
			else
			{
				$year_edad_cal_dep=edad_calculada_a_partir_de_dos_fechas($campo_n7,$fecha_de_corte,"y");
			}
			//fin llamado a calculo edad
	    

	    $campo_n39=trim($campos[38]);

			$fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
			$fecha_c18_mayor_igual_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

			$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && $cumple_condicion_con_rangos_codigo_campo_17==true
	       && $year_edad_cal_dep>=18
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="99"
	    	&& $campo_n39=="1845-01-01"
	       && ( 
			    	($fecha_c18_fecha_calendario<0
			   	&& $fecha_c18_mayor_igual_2015>0)
			    || $fecha_campo_18_fecha_diagnostico=="1800-01-01"
			    )
	       && (
		    	$cumple_condicion_con_rangos_codigo_campo_17==true
		    	|| (
		    		$year_edad_cal_dep<18
				   && ($cumple_condicion_a_con_rangos_codigo_campo_17==true 
					   || $cumple_condicion_b_con_rangos_codigo_campo_17==true 
					   || $cumple_condicion_2_con_rangos_codigo_campo_17==true
					   	)
				   )
		    	)
	       )
	    {
		    $campos[$numero_campo]="99";
	    }
	    else if(
	    	trim($campos[$numero_campo])!="1"
		    && trim($campos[$numero_campo])!="2"
		    && trim($campos[$numero_campo])!="3"
		    && trim($campos[$numero_campo])!="4"
		    && trim($campos[$numero_campo])!="5"
		    && trim($campos[$numero_campo])!="6"
		    && trim($campos[$numero_campo])!="7"
		    && trim($campos[$numero_campo])!="8"
		    && trim($campos[$numero_campo])!="9"
		    && trim($campos[$numero_campo])!="10"
		    && trim($campos[$numero_campo])!="11"
		    && trim($campos[$numero_campo])!="12"
		    && trim($campos[$numero_campo])!="13"
	    	&& trim($campos[$numero_campo])!="97"
	    	&& $campo_n39!="1845-01-01"
	       && ( 
			    	($fecha_c18_fecha_calendario<0
			   	&& $fecha_c18_mayor_igual_2015<=0)
			    || $fecha_campo_18_fecha_diagnostico=="1800-01-01"
			    )
	       && (
		    	$cumple_condicion_con_rangos_codigo_campo_17==true
		    	|| (
		    		$year_edad_cal_dep<18
				   && ($cumple_condicion_a_con_rangos_codigo_campo_17==true 
					   || $cumple_condicion_b_con_rangos_codigo_campo_17==true 
					   || $cumple_condicion_2_con_rangos_codigo_campo_17==true
					   	)
				   )
		    	)
	       )
	    {
		    $campos[$numero_campo]="97";
	    }

	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 38 numero orden 39 numero campo 39 vcalidad
	$numero_campo=38;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n38=trim($campos[37]);
	    
	    $campo_n20=$campos[19];
	    
	    
	    $c20_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n20,"1900-12-31");
	    

	    //COMPARACION RANGOS CON CAMPO 17
		$campo_n17=trim($campos[16]);
		
		$string_numeros_comparar="";
		$cumple_condicion_con_rangos_codigo_campo_17=false;
		$cumple_condicion_a_con_rangos_codigo_campo_17=false;
		$cumple_condicion_b_con_rangos_codigo_campo_17=false;
		$cumple_condicion_2_con_rangos_codigo_campo_17=false;
		$empieza_por_d=false;
		if(strlen($campo_n17)==4)
		{
		    $string_numeros_comparar=$campo_n17;
		    $string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		    $string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		    
		    $string_numeros_comparar_2=$campo_n17;
		    $string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		    $string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		    
		    $tiene_letra_principio=substr($campo_n17,0,1);
		    
		    $tiene_letra_final=substr($campo_n17,-1);
		    
		    //echo "<script>alert('c17 $campo_n17 str1 $string_numeros_comparar str2 $string_numeros_comparar_2 li $tiene_letra_principio lf $tiene_letra_final ')</script>";
		    
		    if(ctype_digit($string_numeros_comparar)
		       && $tiene_letra_principio=="C"
		       //&& $tiene_letra_final!="X"
		       )
		    {
				//echo "<script>alert('empezo por C')</script>";
				$numero_int=intval($string_numeros_comparar);
				if(
						($numero_int>=810
					   && $numero_int<=880)
					   ||
					   ($numero_int>=900
					   && $numero_int<=959				   
					   )
				   )
				{
				    $cumple_condicion_con_rangos_codigo_campo_17=true;
				}

				if(
					($numero_int>=0
				   && $numero_int<=798)
				   ||
				   ($numero_int>=881
				   && $numero_int<=889				   
				   )
				   ||
				   ($numero_int>=960
				   && $numero_int<=969				   
				   )
			   )
				{
				    $cumple_condicion_a_con_rangos_codigo_campo_17=true;
				}
			
		    }// es digito y c al principio
		    if(ctype_digit($string_numeros_comparar_2)
		       && $tiene_letra_principio=="C"
		       && $tiene_letra_final=="X"
		       )
		    {
			$numero_int=intval($string_numeros_comparar_2);
			if(
				($numero_int==01
			   || $numero_int==07
			   || $numero_int==12
			   || $numero_int==19
			   || $numero_int==20
			   || $numero_int==23
			   || $numero_int==33
			   || $numero_int==52
			   || $numero_int==55
			   || $numero_int==56
			   || $numero_int==58
			   || $numero_int==61
			   || $numero_int==64
			   || $numero_int==65
			   || $numero_int==66
			   || $numero_int==73
			   || $numero_int==80
			   || $numero_int==97
			   )

			   )
			{
			    $cumple_condicion_b_con_rangos_codigo_campo_17=true;
			}
			
		    }// es digito y c al principio
		    else if(ctype_digit($string_numeros_comparar)
		       && $tiene_letra_principio=="D"
		       )
		    {
			$numero_int=intval($string_numeros_comparar);
			if(($numero_int>=0
			    && $numero_int<=99)
			    )
			{
			    $cumple_condicion_2_con_rangos_codigo_campo_17=true;
			}
			
		    }// es digito y d al principio
		    
		    if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		    {
			$empieza_por_d=true;
		    }
		}//fin condicion rangos campo 17 mira longitud 4 caracteres
		//FIN COMPARACION RANGOS CON CAMPO 17


		//llamado a calculo edad
		$campo_n7=trim($campos[6]);
		$campo_n18=trim($campos[17]);
		$campo18_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n18,"1900-12-31");
		$year_edad_cal_dep=-1;
		if($campo18_es_fecha_calendario<0)
		{
			$year_edad_cal_dep=edad_calculada_a_partir_de_dos_fechas($campo_n7,$campo_n18,"y");
		}
		else
		{
			$year_edad_cal_dep=edad_calculada_a_partir_de_dos_fechas($campo_n7,$fecha_de_corte,"y");
		}
		//fin llamado a calculo edad

		$fecha_campo_18_fecha_diagnostico=trim($campos[17]);//numero campo 20 con numero de orden 19
		$fecha_c18_mayor_igual_2015=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"2015-01-01");

		$fecha_c18_fecha_calendario=diferencia_dias_entre_fechas($fecha_campo_18_fecha_diagnostico,"1900-12-31");

		if(trim($campos[$numero_campo])!="1845-01-01"
	       && $cumple_condicion_con_rangos_codigo_campo_17==false
	       && $year_edad_cal_dep>=18
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
	    else if(trim($campos[$numero_campo])=="1845-01-01"
	       && ( 
			    	($fecha_c18_fecha_calendario<0
			   	&& $fecha_c18_mayor_igual_2015>0)
			    || $fecha_campo_18_fecha_diagnostico=="1800-01-01"
			    )
	       && (
		    	$cumple_condicion_con_rangos_codigo_campo_17==true
		    	|| (
		    		$year_edad_cal_dep<18
				   && ($cumple_condicion_a_con_rangos_codigo_campo_17==true 
					   || $cumple_condicion_b_con_rangos_codigo_campo_17==true 
					   || $cumple_condicion_2_con_rangos_codigo_campo_17==true
					   	)
				   )
		    	)
	       )
	    {
		    $campos[$numero_campo]="1800-01-01";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 39 numero orden 40 numero campo 40 vcalidad
	$numero_campo=39;
	if(isset($campos[$numero_campo]))
	{
		$campo_n48=trim($campos[55]);
		//campo obligatorio
		if(trim($campos[$numero_campo])!="1"
			&& (
			$campo_n48=="1"
			|| $campo_n48=="2"
		   	|| $campo_n48=="3"
		   	|| $campo_n48=="5"
		   	|| $campo_n48=="7"
		   	|| $campo_n48=="9"
		   	)
		)
		{
			$campos[$numero_campo]="1";
		}
		else if(trim($campos[$numero_campo])!="2"
			&& ($campo_n48=="4" 
		   	|| $campo_n48=="6"
		   	|| $campo_n48=="8"
		   	|| $campo_n48=="10"
		   	)
		)
		{
			$campos[$numero_campo]="2";
		}
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 40 numero orden 41 numero campo 41 
	$numero_campo=40;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
		$campo_ant_31=trim($campos[30]);
		$campo_ant_20=$campos[19];
		/*
		if($campos[$numero_campo]=="99"
		    && $corresponde_year_campo20_con_year_fecha_corte)
		{
		    $campos[$numero_campo]="6";
		}
		*/
		
	}//if si existe campo
	
	//orden 44, 42,43
	
	//numero_orden_desde_cero 43 numero orden 44 numero campo 44 vcalidad
	$numero_campo=43;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_42=$campos[41];
	    
	    //COMPARACION RANGOS CON CAMPO 44
	    $campo_n44=trim($campos[43]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_44_C=false;
	    $cumple_condicion_con_rangos_codigo_campo_44_D=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n44)==4)
	    {
		$string_numeros_comparar=$campo_n44;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n44;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n44,0,1);
		
		$tiene_letra_final=substr($campo_n44,-1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   )
		{
		    $cumple_condicion_con_rangos_codigo_campo_44_C=true;				
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if($numero_int>=0
		       && $numero_int<=99				   
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_44_D=true;
		    }
		    
		}// es digito y c al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 44 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 44
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_diagnostico_ciex_cancer_tumor_maligno WHERE codigo_diagnostico='".$campos[$numero_campo]."' ; ";
	    $resultado_ciex=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    $campo_n17=trim($campos[16]);
	    $campo_n42=trim($campos[41]);
	    $campo_n43=trim($campos[42]);
			

	    
	    if($campos[$numero_campo]!="99"
	       && (
		    	trim($campos[$numero_campo])==$campo_n17
			   		&& trim($campos[$numero_campo])!="C438"
			   		&& trim($campos[$numero_campo])!="C439"
			   		&& trim($campos[$numero_campo])!="C440"
			   		&& trim($campos[$numero_campo])!="C441"
			   		&& trim($campos[$numero_campo])!="C442"
			   		&& trim($campos[$numero_campo])!="C443"
			   		&& trim($campos[$numero_campo])!="C444"
			   		&& trim($campos[$numero_campo])!="C445"
			   		&& trim($campos[$numero_campo])!="C446"
			   		&& trim($campos[$numero_campo])!="C447"
			   		&& trim($campos[$numero_campo])!="C448"
			   		&& trim($campos[$numero_campo])!="C449"
			   		&& trim($campos[$numero_campo])!="C460"
			   		&& trim($campos[$numero_campo])!="C500"
			   		&& trim($campos[$numero_campo])!="C501"
			   		&& trim($campos[$numero_campo])!="C502"
			   		&& trim($campos[$numero_campo])!="C503"
			   		&& trim($campos[$numero_campo])!="C504"
			   		&& trim($campos[$numero_campo])!="C505"
			   		&& trim($campos[$numero_campo])!="C506"
			   		&& trim($campos[$numero_campo])!="C508"
			   		&& trim($campos[$numero_campo])!="C509"			   		
			   		&& trim($campos[$numero_campo])!="C792"
			   		&& trim($campos[$numero_campo])!="D040"
			   		&& trim($campos[$numero_campo])!="D041"
			   		&& trim($campos[$numero_campo])!="D042"
			   		&& trim($campos[$numero_campo])!="D043"
			   		&& trim($campos[$numero_campo])!="D044"
			   		&& trim($campos[$numero_campo])!="D045"
			   		&& trim($campos[$numero_campo])!="D046"
			   		&& trim($campos[$numero_campo])!="D047"
			   		&& trim($campos[$numero_campo])!="D048"
			   		&& trim($campos[$numero_campo])!="D049"
			   		&& trim($campos[$numero_campo])!="D057"
			   		&& trim($campos[$numero_campo])!="D059")
		    
	       )//fin condicion
	    {
			$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]!="99" && $campo_n42=="2") 
    	{
			$campos[$numero_campo]="99";
	    }
	    else if($campos[$numero_campo]!="99" && $campo_n43=="1845-01-01") 
    	{
			$campos[$numero_campo]="99";
	    }

	    /*
	    
	    if($cumple_condicion_con_rangos_codigo_campo_44_C==false
		&& $cumple_condicion_con_rangos_codigo_campo_44_D==false
	       && trim($campos[$numero_campo])!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if(($cumple_condicion_con_rangos_codigo_campo_44_C==true
			   || $cumple_condicion_con_rangos_codigo_campo_44_D==true)
	       && (count($resultado_ciex)==0
		    || !is_array($resultado_ciex)
		    )
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    else if(($cumple_condicion_con_rangos_codigo_campo_44_C==true
			   || $cumple_condicion_con_rangos_codigo_campo_44_D==true)
	       && (count($resultado_ciex)>0 && is_array($resultado_ciex))
	       && $campos[$numero_campo]==$campo_n17
	       && trim($campos[$numero_campo])!="99"
	       )
	    {
		$campos[$numero_campo]="99";
	    }
	    */
		
		
	}//if si existe campo
	
	//numero_orden_desde_cero 41 numero orden 42 numero campo 42 vcalidad
	$numero_campo=41;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_ant_20=$campos[19];
	    $campo_ant_44=$campos[43];
		
	    //COMPARACION RANGOS CON CAMPO 44
	    $campo_n44=trim($campos[43]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_44_C=false;
	    $cumple_condicion_con_rangos_codigo_campo_44_D=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n44)==4)
	    {
		$string_numeros_comparar=$campo_n44;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n44;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n44,0,1);
		
		$tiene_letra_final=substr($campo_n44,-1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   )
		{
		    $cumple_condicion_con_rangos_codigo_campo_44_C=true;				
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if($numero_int>=0
		       && $numero_int<=99				   
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_44_D=true;
		    }
		    
		}// es digito y c al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 44 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 44
		
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_diagnostico_ciex_cancer_tumor_maligno WHERE codigo_diagnostico='".$campo_ant_44."' ; ";
	    $resultado_ciex=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    $campo_n17=$campos[16];
	    $campo_n20=$campos[19];

	    $campo_n44=trim($campos[43]);
	    
	    $campo_n43=trim($campos[42]);
	    $campo_43=trim($campos[42]);
		$c43_es_fecha_calendario=diferencia_dias_entre_fechas($campo_43,"1900-12-31");
		$c43_frente_2015=diferencia_dias_entre_fechas($campo_43,"2015-01-01");
		
		$campo_n128=trim($campos[205]);

	    if($campos[$numero_campo]!="2"
	       && $campo_ant_44=="99"
	       )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="99"
		    && $campo_ant_44!="99"
		    && ($campo_n43=="1800-01-01"	   
			   || ($c43_frente_2015>0 && $c43_es_fecha_calendario<0)  )
		    )
	    {
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="1"
		    && $campo_ant_44!="99"
		    && $c43_es_fecha_calendario<0	   
		  	&& $c43_frente_2015<=0
		    )
	    {
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	    
		
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 42 numero orden 43 numero campo 43 vcalidad
	$numero_campo=42;
	if(isset($campos[$numero_campo]))
	{		
		
	    $campo_ant_44=$campos[43];
	    $campo_ant_20=$campos[19];
	    
	    //COMPARACION RANGOS CON CAMPO 44
	    $campo_n44=trim($campos[43]);
	    
	    $string_numeros_comparar="";
	    $cumple_condicion_con_rangos_codigo_campo_44_C=false;
	    $cumple_condicion_con_rangos_codigo_campo_44_D=false;
	    $empieza_por_d=false;
	    if(strlen($campo_n44)==4)
	    {
		$string_numeros_comparar=$campo_n44;
		$string_numeros_comparar=str_replace("C","",$string_numeros_comparar);
		$string_numeros_comparar=str_replace("D","",$string_numeros_comparar);
		
		$string_numeros_comparar_2=$campo_n44;
		$string_numeros_comparar_2=str_replace("C","",$string_numeros_comparar_2);
		$string_numeros_comparar_2=str_replace("X","",$string_numeros_comparar_2);
		
		$tiene_letra_principio=substr($campo_n44,0,1);
		
		$tiene_letra_final=substr($campo_n44,-1);
		
		if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="C"
		   )
		{
		    $cumple_condicion_con_rangos_codigo_campo_44_C=true;				
		    
		}// es digito y c al principio
		else if(ctype_digit($string_numeros_comparar)
		   && $tiene_letra_principio=="D"
		   )
		{
		    $numero_int=intval($string_numeros_comparar);
		    if($numero_int>=0
		       && $numero_int<=99				   
		       )
		    {
			$cumple_condicion_con_rangos_codigo_campo_44_D=true;
		    }
		    
		}// es digito y c al principio
		
		if(ctype_digit($string_numeros_comparar) && $tiene_letra_principio=="D")
		{
		    $empieza_por_d=true;
		}
	    }//fin condicion rangos campo 44 mira longitud 4 caracteres
	    //FIN COMPARACION RANGOS CON CAMPO 44
	    
	    $consulta="";
	    $consulta.="SELECT * FROM gioss_diagnostico_ciex_cancer_tumor_maligno WHERE codigo_diagnostico='".$campo_ant_44."' ; ";
	    $resultado_ciex=$coneccionBD->consultar2_no_crea_cierra($consulta);
	    
	    $campo_n17=$campos[16];
	    $campo_n20=$campos[19];

	    $campo_n44=trim($campos[43]);
		$campo_n42=trim($campos[41]);
		$campo_n17=trim($campos[16]);
	    
	    if($campos[$numero_campo]!="1845-01-01"
	       && $campo_ant_44=="99"
	       )
	    {
			$campos[$numero_campo]="1845-01-01";
		    
	    }//fin 
	    else if($campos[$numero_campo]=="1845-01-01"
		&& $campo_ant_44!="99"
		&& $campo_n42=="99"
	       )
	    {
		    $campos[$numero_campo]="1800-01-01";
		    
	    }//fin if
	    
	    
	    
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 44 numero orden 45 numero campo 45 vcalidad
	$numero_campo=44;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n46=trim($campos[45]);
		$campo_n47=trim($campos[54]);
		$campo_n53=trim($campos[60]);
		$campo_n57=trim($campos[96]);
		$campo_n66=trim($campos[105]);
		$campo_n70=trim($campos[141]);
	    
	    $c46_1=trim($campos[46]);
	    $c46_2=trim($campos[47]);
	    $c46_3=trim($campos[48]);
	    $c46_4=trim($campos[49]);
	    $c46_5=trim($campos[50]);
	    $c46_6=trim($campos[51]);
	    $c46_7=trim($campos[52]);
	    $c46_8=trim($campos[53]);
	    
	    $c53_1=trim($campos[61]);
	    $c53_2=trim($campos[62]);
	    $c53_3=trim($campos[63]);
	    $c53_4=trim($campos[64]);
	    $c53_5=trim($campos[65]);
	    $c53_6=trim($campos[66]);
	    $c53_7=trim($campos[67]);
	    $c53_8=trim($campos[68]);
	    $c53_9=trim($campos[69]);
	    $c53_10=trim($campos[70]);
	    $c53_11=trim($campos[71]);
	    $c53_12=trim($campos[72]);
	    $c53_13=trim($campos[73]);
	    $c53_14=trim($campos[74]);
	    $c53_15=trim($campos[75]);
	    $c53_16=trim($campos[76]);
	    $c53_17=trim($campos[77]);
	    $c53_18=trim($campos[78]);
	    $c53_19=trim($campos[79]);
	    $c53_20=trim($campos[80]);
	    $c53_21=trim($campos[81]);
	    $c53_22=trim($campos[82]);
	    $c53_23=trim($campos[83]);
	    $c53_24=trim($campos[84]);
	    $c53_25=trim($campos[85]);
	    $c53_26=trim($campos[86]);
	    $c53_27=trim($campos[87]);
	    $c53_28=trim($campos[88]);
	    $c53_29=trim($campos[89]);
	    $c53_30=trim($campos[90]);
	    $c53_31=trim($campos[91]);
	    $c53_32=trim($campos[92]);
	    
	    $c66_1=trim($campos[106]);
	    $c66_2=trim($campos[107]);
	    $c66_3=trim($campos[108]);
	    $c66_4=trim($campos[109]);
	    $c66_5=trim($campos[110]);
	    $c66_6=trim($campos[111]);
	    $c66_7=trim($campos[112]);
	    $c66_8=trim($campos[113]);
	    $c66_9=trim($campos[114]);
	    $c66_10=trim($campos[115]);
	    $c66_11=trim($campos[116]);
	    $c66_12=trim($campos[117]);
	    $c66_13=trim($campos[118]);
	    $c66_14=trim($campos[119]);
	    $c66_15=trim($campos[120]);
	    $c66_16=trim($campos[121]);
	    $c66_17=trim($campos[122]);
	    $c66_18=trim($campos[123]);
	    $c66_19=trim($campos[124]);
	    $c66_20=trim($campos[125]);
	    $c66_21=trim($campos[126]);
	    $c66_22=trim($campos[127]);
	    $c66_23=trim($campos[128]);
	    $c66_24=trim($campos[129]);
	    $c66_25=trim($campos[130]);
	    $c66_26=trim($campos[131]);
	    $c66_27=trim($campos[132]);
	    $c66_28=trim($campos[133]);
	    $c66_29=trim($campos[134]);
	    $c66_30=trim($campos[135]);
	    $c66_31=trim($campos[136]);
	    $c66_32=trim($campos[137]);
	    //campo obligatorio
	    if(
	    	(trim($campos[$numero_campo])=="2" || trim($campos[$numero_campo])=="98")
	       && ($campo_n46!="98"
				|| $campo_n47!="98"
				|| $campo_n53!="98"
				|| $campo_n57=="1"
				|| $campo_n66!="98"
				|| $campo_n70=="1")
		
	       )//fin criterio condicion
	    {
	    	//c1
		    $campos[$numero_campo]="1";
	    }
	    else if(trim($campos[$numero_campo])=="1"
	       	&& $campo_n46=="98"
			&& $campo_n47=="98"
			&& $campo_n53=="98"
			&& ($campo_n57=="2" || $campo_n57=="98")
			&& $campo_n66=="98"
			&& ($campo_n70=="2" || $campo_n70=="98")
		
	       )//fin criterio condicion
	    {
	    	//c2
		    $campos[$numero_campo]="98";
	    }
	    
	}//if si existe campo
	
	//CONSULTA EN TABLA DE CODIGO HEMOLINFATICO
	$campo_n17=trim($campos[16]);
	$consulta_hemolinfatico="";
	$consulta_hemolinfatico.="SELECT * FROM gioss_ca_hematolinfatico WHERE codigo='".$campo_n17."' ; ";
	$resultado_hemolinfatico=$coneccionBD->consultar2_no_crea_cierra($consulta_hemolinfatico);
	//FIN CONSULTA EN TABLA DE CODIGO HEMOLINFATICO

	//numero_orden_desde_cero 45 numero orden 46 numero campo 46 vcalidad
	$numero_campo=45;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_n17=trim($campos[16]);
	    
	    $campo_ant_45=$campos[44];
	    
	    $c46_1=trim($campos[46]);
	    $c46_2=trim($campos[47]);
	    $c46_3=trim($campos[48]);
	    $c46_4=trim($campos[49]);
	    $c46_5=trim($campos[50]);
	    $c46_6=trim($campos[51]);
	    $c46_7=trim($campos[52]);
	    $c46_8=trim($campos[53]);
	    
	    if($campos[$numero_campo]!="98"
	    	&& $campo_n17!="C910"
		  		&& $campo_n17!="C920"
			   	&& $campo_n17!="C835"
	       
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	    	&& ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")   
		    && ($campo_ant_45=="2" || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")   	 
		    && $campo_ant_45=="1" 
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	    
		
		
	}//if si existe campo
	//fincampo 45 numero orden 46 numero campo 46 vcalidad
	
	
	//numero_orden_desde_cero 46 numero orden 47 numero campo 46.1 vcalidad
	$numero_campo=46;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if

	    
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 47 numero orden 48 numero campo 46.2 vcalidad
	$numero_campo=47;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 48 numero orden 49 numero campo 46.3 vcalidad
	$numero_campo=48;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 49 numero orden 50 numero campo 46.4 vcalidad
	$numero_campo=49;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 50 numero orden 51 numero campo 46.5 vcalidad
	$numero_campo=50;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 51 numero orden 52 numero campo 46.6 vcalidad
	$numero_campo=51;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 52 numero orden 53 numero campo 46.7 vcalidad
	$numero_campo=52;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 53 numero orden 54 numero campo 46.8 vcalidad
	$numero_campo=53;
	if(isset($campos[$numero_campo]))
	{
	    //validacion de calidad
	    
	    $campo_n17=trim($campos[16]);
	    
		
	    $campo_ant_45=$campos[44];
	    $campo_ant_46=$campos[45];

	    if($campos[$numero_campo]!="97"
	    	 && $campo_n17!="C910"
	    	  && $campo_n17!="C920"
			   && $campo_n17!="C835"
	      
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="97"
		    && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		     && ($campo_ant_45=="2"  || $campo_ant_45=="98")
		    )
	    {
	    	//c2
		    $campos[$numero_campo]="97";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="97"
		     && ($campo_n17=="C910" || $campo_n17=="C920" || $campo_n17=="C835")
		    && $campo_ant_45=="1"
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="99";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 54 numero orden 55 numero campo 47 vcalidad
	$numero_campo=54;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    $campo_ant_45=$campos[44];
	
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 55 numero orden 56 numero campo 48 vcalidad
	$numero_campo=55;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if

	}//if si existe campo
	
	
	//numero_orden_desde_cero 56 numero orden 57 numero campo 49 vcalidad
	$numero_campo=56;
	if(isset($campos[$numero_campo]))
	{
		$campo_ant_45=$campos[44];
		
			
		$campo_ant_31=trim($campos[30]);
		$campo_ant_20=$campos[19];
		
		if($campos[$numero_campo]!="1845-01-01"
		   && ($campo_ant_45=="2" || $campo_ant_45=="98")
		   )
		{
			$campos[$numero_campo]="1845-01-01";
			
		}//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 57 numero orden 58 numero campo 50 vcalidad
	$numero_campo=57;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    $campo_ant_49=trim($campos[56]);
	    $c49_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_49,"1900-12-31");
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if	    
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_49=="1845-01-01"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $c49_es_fecha_calendario<0
		    )
	    {
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 58 numero orden 59 numero campo 51 vcalidad
	$numero_campo=58;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_49=$campos[56];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_49=="1845-01-01"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_49!="1845-01-01"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
		    
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 59 numero orden 60 numero campo 52 
	$numero_campo=59;
	if(isset($campos[$numero_campo]))
	{
	    //campo 52 y 65 arreglo especial fundacion
	    $campo_ant_45=$campos[44];
	    $campo_ant_45=$campos[44];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_ant_49=$campos[56];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_49=="1845-01-01"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 60 numero orden 61 numero campo 53 vcalidad
	$numero_campo=60;
	if(isset($campos[$numero_campo]))
	{
	    	    
	    $campo_ant_45=$campos[44];
	    
	    $c53_1=trim($campos[61]);
	    $c53_2=trim($campos[62]);
	    $c53_3=trim($campos[63]);
	    $c53_4=trim($campos[64]);
	    $c53_5=trim($campos[65]);
	    $c53_6=trim($campos[66]);
	    $c53_7=trim($campos[67]);
	    $c53_8=trim($campos[68]);
	    $c53_9=trim($campos[69]);
	    $c53_10=trim($campos[70]);
	    $c53_11=trim($campos[71]);
	    $c53_12=trim($campos[72]);
	    $c53_13=trim($campos[73]);
	    $c53_14=trim($campos[74]);
	    $c53_15=trim($campos[75]);
	    $c53_16=trim($campos[76]);
	    $c53_17=trim($campos[77]);
	    $c53_18=trim($campos[78]);
	    $c53_19=trim($campos[79]);
	    $c53_20=trim($campos[80]);
	    $c53_21=trim($campos[81]);
	    $c53_22=trim($campos[82]);
	    $c53_23=trim($campos[83]);
	    $c53_24=trim($campos[84]);
	    $c53_25=trim($campos[85]);
	    $c53_26=trim($campos[86]);
	    $c53_27=trim($campos[87]);
	    $c53_28=trim($campos[88]);
	    $c53_29=trim($campos[89]);
	    $c53_30=trim($campos[90]);
	    $c53_31=trim($campos[91]);
	    $c53_32=trim($campos[92]);
	    
	    $c54=trim($campos[93]);
	    $c55=trim($campos[94]);
	    $c56=trim($campos[95]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if	    
	    else if( $campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && ( $c53_1!="1"
			 && $c53_2!="1"
			 && $c53_3!="1"
			 && $c53_4!="1"
			 && $c53_5!="1"
			 && $c53_6!="1"
			 && $c53_7!="1"
			 && $c53_8!="1"
			 && $c53_9!="1"
			 && $c53_10!="1"
			 && $c53_11!="1"
			 && $c53_12!="1"
			 && $c53_13!="1"
			 && $c53_14!="1"
			 && $c53_15!="1"
			 && $c53_16!="1"
			 && $c53_17!="1"
			 && $c53_18!="1"
			 && $c53_19!="1"
			 && $c53_20!="1"
			 && $c53_21!="1"
			 && $c53_22!="1"
			 && $c53_23!="1"
			 && $c53_24!="1"
			 && $c53_25!="1"
			 && $c53_26!="1"
			 && $c53_27!="1"
			 && $c53_28!="1"
			 && $c53_29!="1"
			 && $c53_30!="1"
			 && $c53_31!="1"
			 && $c53_32!="1"
			 && ($c54=="97" || $c54=="98")
			 && ($c55=="97" || $c55=="98")
			 && ($c56=="97" || $c56=="98")
			 )//fin 53_x
	    )
	    {
			$campos[$numero_campo]="98";
	    }
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && ( $c53_1=="1"
		    || $c53_2=="1"
		    || $c53_3=="1"
		    || $c53_4=="1"
		    || $c53_5=="1"
		    || $c53_6=="1"
		    || $c53_7=="1"
		    || $c53_8=="1"
		    || $c53_9=="1"
		    || $c53_10=="1"
		    || $c53_11=="1"
		    || $c53_12=="1"
		    || $c53_13=="1"
		    || $c53_14=="1"
		    || $c53_15=="1"
		    || $c53_16=="1"
		    || $c53_17=="1"
		    || $c53_18=="1"
		    || $c53_19=="1"
		    || $c53_20=="1"
		    || $c53_21=="1"
		    || $c53_22=="1"
		    || $c53_23=="1"
		    || $c53_24=="1"
		    || $c53_25=="1"
		    || $c53_26=="1"
		    || $c53_27=="1"
		    || $c53_28=="1"
		    || $c53_29=="1"
		    || $c53_30=="1"
		    || $c53_31=="1"
		    || $c53_32=="1"
		    || ($c54!="97" && $c54!="98")
		    || ($c55!="97" && $c55!="98")
		    || ($c56!="97" && $c56!="98")
		    )//fin 53_x
		)
	    {
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 61 numero orden 62 numero campo 53.1 vcalidad
	$numero_campo=61;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 62 numero orden 63 numero campo 53.2 
	$numero_campo=62;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 63 numero orden 64 numero campo 53.3 
	$numero_campo=63;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 64 numero orden 65 numero campo 53.4 
	$numero_campo=64;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 65 numero orden 66 numero campo 53.5 
	$numero_campo=65;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 66 numero orden 67 numero campo 53.6 
	$numero_campo=66;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 67 numero orden 68 numero campo 53.7 
	$numero_campo=67;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 68 numero orden 69 numero campo 53.8 
	$numero_campo=68;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 69 numero orden 70 numero campo 53.9 
	$numero_campo=69;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 70 numero orden 71 numero campo 53.10 
	$numero_campo=70;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 71 numero orden 72 numero campo 53.11 
	$numero_campo=71;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 72 numero orden 73 numero campo 53.12 
	$numero_campo=72;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 73 numero orden 74 numero campo 53.13 
	$numero_campo=73;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 74 numero orden 75 numero campo 53.14 
	$numero_campo=74;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 75 numero orden 76 numero campo 53.15 
	$numero_campo=75;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 76 numero orden 77 numero campo 53.16 
	$numero_campo=76;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 77 numero orden 78 numero campo 53.17 
	$numero_campo=77;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 78 numero orden 79 numero campo 53.18 
	$numero_campo=78;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 79 numero orden 80 numero campo 53.19
	$numero_campo=79;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 80 numero orden 81 numero campo 53.20 
	$numero_campo=80;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 81 numero orden 82 numero campo 53.21 
	$numero_campo=81;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	//numero_orden_desde_cero 82 numero orden 83 numero campo 53.22 
	$numero_campo=82;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 83 numero orden 84 numero campo 53.23 
	$numero_campo=83;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 84 numero orden 85 numero campo 53.24 
	$numero_campo=84;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 85 numero orden 86 numero campo 53.25 
	$numero_campo=85;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 86 numero orden 87 numero campo 53.26 
	$numero_campo=86;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 87 numero orden 88 numero campo 53.27 
	$numero_campo=87;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 88 numero orden 89 numero campo 53.28
	$numero_campo=88;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 89 numero orden 90 numero campo 53.29 
	$numero_campo=89;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 90 numero orden 91 numero campo 53.30 
	$numero_campo=90;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 91 numero orden 92 numero campo 53.31 
	$numero_campo=91;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 92 numero orden 93 numero campo 53.32 vcalidad
	$numero_campo=92;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_53=$campos[60];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_53=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_53!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 93 numero orden 94 numero campo 54 vcalidad
	$numero_campo=93;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    
	    $campo_ant_53=$campos[60];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="2" && $campo_ant_45!="98")
	       && $campo_ant_53=="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_53!="98"
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 94 numero orden 95 numero campo 55 vcalidad
	$numero_campo=94;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    
	    $campo_ant_53=$campos[60];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="2" && $campo_ant_45!="98")
	       && $campo_ant_53=="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_53!="98"
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 95 numero orden 96 numero campo 56 vcalidad
	$numero_campo=95;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    
	    $campo_ant_53=$campos[60];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="2" && $campo_ant_45!="98")
	       && $campo_ant_53=="98"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_53!="98"
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 96 numero orden 97 numero campo 57 vcalidad
	$numero_campo=96;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
			    
	    if($campos[$numero_campo]!="98"
	        && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 97 numero orden 98 numero campo 58 vcalidad
	$numero_campo=97;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    
	    $campo_ant_57=trim($campos[96]);
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="1845-01-01"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 98 numero orden 99 numero campo 59 vcalidad
	$numero_campo=98;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    
	    $campo_n58=trim($campos[97]);
	    
	    $campo_n57=trim($campos[96]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 99 numero orden 100 numero campo 60 vcalidad
	$numero_campo=99;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_59=$campos[98];
	    
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    if($campos[$numero_campo]!="98"
	       && $campo_ant_59!="2"
	       && ($campo_ant_45!="2" && $campo_ant_45!="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_59=="2"
		    )
	    {
		    $campos[$numero_campo]="8";
		    
	    }//fin if
		
	}//if si existe campo
	
	$campo20_contra_2015=diferencia_dias_entre_fechas($campo_n20,"2015-01-01");	
	//numero_orden_desde_cero 100 numero orden 101 numero campo 61 vcalidad
	$numero_campo=100;
	if(isset($campos[$numero_campo]))
	{
		$campo_n18=trim($campos[17]);
		$campo18_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n18,"1900-12-31");
		$campo18_contra_2015=diferencia_dias_entre_fechas($campo_n18,"2015-01-01");

	    $campo_ant_45=$campos[44];
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2") 
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45=="1")
		    && ( ($campo18_es_fecha_calendario<0
		    && $campo18_contra_2015>0) || $campo_n18=="1800-01-01" )
		    )
	    {
		    $campos[$numero_campo]="99";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="99"
		    && ($campo_ant_45=="1")
		    && ( $campo18_es_fecha_calendario<0
		    && $campo18_contra_2015<=0 )
		    )
	    {
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 101 numero orden 102 numero campo 62 vcalidad
	$numero_campo=101;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_n61=trim($campos[100]);
	    if($campos[$numero_campo]!="1845-01-01"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="1845-01-01"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_n61=="97"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 102 numero orden 103 numero campo 63 vcalidad
	$numero_campo=102;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    $campo_ant_49=$campos[56];
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_ant_62=trim($campos[101]);
	    $c62_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_62,"1900-12-31");	    
	    
	    
	    $campo_ant_61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_ant_61!="97"
	       && $campo_ant_62=="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="98" && $campo_ant_45!="2")
		    && $campo_ant_61!="97"
		    && $c62_es_fecha_calendario<0
		    )
	    {
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 103 numero orden 104 numero campo 64 vcalidad
	$numero_campo=103;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_49=$campos[56];
	    
	    
	    $campo_ant_62=trim($campos[101]);
	    $c62_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_62,"1900-12-31");	    
	    
	    
	    $campo_ant_61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_ant_61!="97"
	       && $campo_ant_62=="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="98" && $campo_ant_45!="2")
		    && $campo_ant_61!="97"
		    && $c62_es_fecha_calendario<0
		    )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
		    
	    }//fin if
	    	
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 104 numero orden 105 numero campo 65 vcalidad
	$numero_campo=104;
	if(isset($campos[$numero_campo]))
	{
	    //campo 52 y 65 arreglo especial fundacion
	    $campo_ant_45=$campos[44];
	    $campo_ant_45=$campos[44];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_ant_62=trim($campos[101]);
	    $c62_es_fecha_calendario=diferencia_dias_entre_fechas($campo_ant_62,"1900-12-31");	    
	    
	    
	    $campo_ant_61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="98" && $campo_ant_45!="2")
	       && $campo_ant_61!="97"
	       && $campo_ant_62=="1845-01-01"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    
	    
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 105 numero orden 106 numero campo 66 vcalidad
	$numero_campo=105;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_n61=trim($campos[100]);
	    
	    $c66_1=trim($campos[106]);
	    $c66_2=trim($campos[107]);
	    $c66_3=trim($campos[108]);
	    $c66_4=trim($campos[109]);
	    $c66_5=trim($campos[110]);
	    $c66_6=trim($campos[111]);
	    $c66_7=trim($campos[112]);
	    $c66_8=trim($campos[113]);
	    $c66_9=trim($campos[114]);
	    $c66_10=trim($campos[115]);
	    $c66_11=trim($campos[116]);
	    $c66_12=trim($campos[117]);
	    $c66_13=trim($campos[118]);
	    $c66_14=trim($campos[119]);
	    $c66_15=trim($campos[120]);
	    $c66_16=trim($campos[121]);
	    $c66_17=trim($campos[122]);
	    $c66_18=trim($campos[123]);
	    $c66_19=trim($campos[124]);
	    $c66_20=trim($campos[125]);
	    $c66_21=trim($campos[126]);
	    $c66_22=trim($campos[127]);
	    $c66_23=trim($campos[128]);
	    $c66_24=trim($campos[129]);
	    $c66_25=trim($campos[130]);
	    $c66_26=trim($campos[131]);
	    $c66_27=trim($campos[132]);
	    $c66_28=trim($campos[133]);
	    $c66_29=trim($campos[134]);
	    $c66_30=trim($campos[135]);
	    $c66_31=trim($campos[136]);
	    $c66_32=trim($campos[137]);
	    $c67=trim($campos[138]);
	    $c68=trim($campos[139]);
	    $c69=trim($campos[140]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
	    	//c1
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_n61=="97"
	       )
	    {
	    	//c2
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n61!="97"
		    && ( $c66_1!="1"
			&& $c66_2!="1"
			&& $c66_3!="1"
			&& $c66_4!="1"
			&& $c66_5!="1"
			&& $c66_6!="1"
			&& $c66_7!="1"
			&& $c66_8!="1"
			&& $c66_9!="1"
			&& $c66_10!="1"
			&& $c66_11!="1"
			&& $c66_12!="1"
			&& $c66_13!="1"
			&& $c66_14!="1"
			&& $c66_15!="1"
			&& $c66_16!="1"
			&& $c66_17!="1"
			&& $c66_18!="1"
			&& $c66_19!="1"
			&& $c66_20!="1"
			&& $c66_21!="1"
			&& $c66_22!="1"
			&& $c66_23!="1"
			&& $c66_24!="1"
			&& $c66_25!="1"
			&& $c66_26!="1"
			&& $c66_27!="1"
			&& $c66_28!="1"
			&& $c66_29!="1"
			&& $c66_30!="1"
			&& $c66_31!="1"
			&& $c66_32!="1"
			&& ($c67=="97" || $c67=="98" )
			&& ($c68=="97" || $c68=="98" )
			&& ($c69=="97" || $c69=="98" )
		       )
		    )
	    {
	    	//c3
		    $campos[$numero_campo]="98";
		    
	    }//fin if	    
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n61!="97"
		    && ( $c66_1=="1"
		    || $c66_2=="1"
		    || $c66_3=="1"
		    || $c66_4=="1"
		    || $c66_5=="1"
		    || $c66_6=="1"
		    || $c66_7=="1"
		    || $c66_8=="1"
		    || $c66_9=="1"
		    || $c66_10=="1"
		    || $c66_11=="1"
		    || $c66_12=="1"
		    || $c66_13=="1"
		    || $c66_14=="1"
		    || $c66_15=="1"
		    || $c66_16=="1"
		    || $c66_17=="1"
		    || $c66_18=="1"
		    || $c66_19=="1"
		    || $c66_20=="1"
		    || $c66_21=="1"
		    || $c66_22=="1"
		    || $c66_23=="1"
		    || $c66_24=="1"
		    || $c66_25=="1"
		    || $c66_26=="1"
		    || $c66_27=="1"
		    || $c66_28=="1"
		    || $c66_29=="1"
		    || $c66_30=="1"
		    || $c66_31=="1"
		    || $c66_32=="1"
		    || ($c67!="97" && $c67!="98" )
		    || ($c68!="97" && $c68!="98" )
		    || ($c69!="97" && $c69!="98" )
		    )//fin 66_x
		    )
	    {
	    	//c4
		    $campos[$numero_campo]="1";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 106 numero orden 107 numero campo 66.1 vcalidad
	$numero_campo=106;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 107 numero orden 108 numero campo 66.2 
	$numero_campo=107;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 108 numero orden 109 numero campo 66.3 
	$numero_campo=108;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 109 numero orden 110 numero campo 66.4 
	$numero_campo=109;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 110 numero orden 111 numero campo 66.5 
	$numero_campo=110;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 111 numero orden 112 numero campo 66.6 
	$numero_campo=111;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 112 numero orden 113 numero campo 66.7 
	$numero_campo=112;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 113 numero orden 114 numero campo 66.8 
	$numero_campo=113;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 114 numero orden 115 numero campo 66.9 
	$numero_campo=114;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 115 numero orden 116 numero campo 66.10 
	$numero_campo=115;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 116 numero orden 117 numero campo 66.11 
	$numero_campo=116;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 117 numero orden 118 numero campo 66.12 
	$numero_campo=117;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 118 numero orden 119 numero campo 66.13 
	$numero_campo=118;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 119 numero orden 120 numero campo 66.14 
	$numero_campo=119;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 120 numero orden 121 numero campo 66.15 
	$numero_campo=120;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 121 numero orden 122 numero campo 66.16 
	$numero_campo=121;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 122 numero orden 123 numero campo 66.17 
	$numero_campo=122;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campos
	
	
	//numero_orden_desde_cero 123 numero orden 124 numero campo 66.18 
	$numero_campo=123;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 124 numero orden 125 numero campo 66.19 
	$numero_campo=124;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 125 numero orden 126 numero campo 66.20 
	$numero_campo=125;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 126 numero orden 127 numero campo 66.21 
	$numero_campo=126;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 127 numero orden 128 numero campo 66.22 
	$numero_campo=127;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 128 numero orden 129 numero campo 66.23 
	$numero_campo=128;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 129 numero orden 130 numero campo 66.24 
	$numero_campo=129;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 130 numero orden 131 numero campo 66.25 
	$numero_campo=130;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 131 numero orden 132 numero campo 66.26 
	$numero_campo=131;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 132 numero orden 133 numero campo 66.27 
	$numero_campo=132;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 133 numero orden 134 numero campo 66.28 
	$numero_campo=133;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 134 numero orden 135 numero campo 66.29 
	$numero_campo=134;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 135 numero orden 136 numero campo 66.30 
	$numero_campo=135;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 136 numero orden 137 numero campo 66.31 
	$numero_campo=136;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 137 numero orden 138 numero campo 66.32 
	$numero_campo=137;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_61=$campos[100];
	    $campo_ant_66=$campos[105];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98" 
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	        && ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_ant_61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66=="98"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_ant_61!="97"
		    && $campo_ant_66!="98"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 138 numero orden 139 numero campo 67 vcalidad
	$numero_campo=138;
	if(isset($campos[$numero_campo]))
	{
		
	    $campo_ant_45=$campos[44];
	    
	    $campo_n66=trim($campos[105]);
	    $campo_n61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")		    
		    && $campo_n61=="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n66=="98"
		    && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		     && $campo_n66!="98"
		     && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if		
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 139 numero orden 140 numero campo 68 vcalidad
	$numero_campo=139;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    $campo_n66=trim($campos[105]);
	    $campo_n61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    
		    && $campo_n61=="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n66=="98"
		    && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		     && $campo_n66!="98"
		     && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if			
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 140 numero orden 141 numero campo 69 vcalidad
	$numero_campo=140;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    $campo_n66=trim($campos[105]);
	    $campo_n61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    
		    && $campo_n61=="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n66=="98"
		    && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		     && $campo_n66!="98"
		     && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="97";
		    
	    }//fin if		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 141 numero orden 142 numero campo 70 vcalidad
	$numero_campo=141;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_n61=trim($campos[100]);
			    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n61=="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if	
	    else if($campos[$numero_campo]=="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if	
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 142 numero orden 143 numero campo 71 
	$numero_campo=142;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
	    
	    $campo_ant_70=trim($campos[141]);
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_n61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="1845-01-01"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="1845-01-01"
		&& ($campo_ant_45!="2" && $campo_ant_45!="98")
		&& $campo_n61=="97"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 143 numero orden 144 numero campo 72 vcalidad
	$numero_campo=143;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    
	    $campo_n71=trim($campos[142]);
	    
	    
	    $campo_n70=trim($campos[141]);
	    
	    $campo_n73=trim($campos[144]);
	    
	    $campo_n61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="2" || $campo_ant_45=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")			   
		    && $campo_n61=="97"
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")	
		    && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="3";
		    
	    }//fin if
	    
	    
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 144 numero orden 145 numero campo 73 vcalidad
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_45=$campos[44];
	    $campo_ant_72=$campos[143];
	        
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    $campo_n61=trim($campos[100]);
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_45=="98" || $campo_ant_45=="2")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && ($campo_ant_45!="2" && $campo_ant_45!="98")
	       && $campo_n61=="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
	       && $campo_ant_72!="2"
	       && ($campo_ant_45!="2" && $campo_ant_45!="98")
	       && $campo_n61!="97"
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_72=="2"
		    && ($campo_ant_45!="2" && $campo_ant_45!="98")
		    && $campo_n61!="97"
		    )
	    {
		    $campos[$numero_campo]="8";
		    
	    }//fin if
		
		
		
	}//if si existe campo

	//numero_orden_desde_cero 145 numero orden 146 numero campo 74 vcalidad 
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n78=trim($campos[149]);

	     $consultac78="";
			$consultac78.="SELECT * FROM gioss_cups WHERE codigo_procedimiento='".$campo_n78."' ; ";
			$resultadoc78=$coneccionBD->consultar2_no_crea_cierra($consultac78);
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="1"
	       && $campo_n78=="98"
	       )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(
	    	trim($campos[$numero_campo])!="1"
	       && $campo_n78!="98"
	       && (count($resultadoc78)>0 && is_array($resultadoc78))
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	}//if si existe campo
	
	//numero_orden_desde_cero 149 numero orden 150 numero campo 78 vcalidad
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_74=$campos[145];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];


		$campo_n83=trim($campos[154]);

	    $consulta83="";
		$consulta83.="SELECT * FROM gioss_cups WHERE codigo_procedimiento='".$campo_n83."' ; ";
		$resultado83=$coneccionBD->consultar2_no_crea_cierra($consulta83);


		if( $campos[$numero_campo]!="98"
				&& (trim($campo_ant_74)=="2" || trim($campo_ant_74)=="3")
			)
		{
			$campos[$numero_campo]="98";
		}//fin if
		else if( trim($campo_n83)!=98 
				&& ( count($resultado83)>0 && is_array($resultado83) ) 
				&& $campos[$numero_campo]=="98"
			)
		{
			$campos[$numero_campo]=$campo_n83;
		}//fin if
	    
		
		
	}//if si existe campo
	
	
	
	
	//numero_orden_desde_cero 146 numero orden 147 numero campo 75 vvalidacioncalidad
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n74=trim($campos[145]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_n74!="1")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n74=="1")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 147 numero orden 148 numero campo 76 vcalidad
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_74=$campos[145];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="1845-01-01" 
	    	&& $campo_ant_74!="1" 
	    	)
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 148 numero orden 149 numero campo 77 vcalidad
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_74=$campos[145];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_74!="1")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
	       && ($campo_ant_74=="1")
	       )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
		    
	    }//fin if	
	    
	    
		
	}//if si existe campo
	
	
	
	
	
	
	//numero_orden_desde_cero 150 numero orden 151 numero campo 79 vcalidad
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_74=$campos[145];
		
			
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="98"
	       && ($campo_ant_74!="1")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if		
	    else if($campos[$numero_campo]=="98" && $campo_ant_74=="1" )
	    {
		    $campos[$numero_campo]="1";
		    
	    }//fin if
	    
		
		
	}//if si existe campo
	
	//numero_orden_desde_cero 154 numero orden 155 numero campo 83 vcalidad
	$numero_campo=154;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_74=$campos[145];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    $campo_ant_80=trim($campos[151]);

	    $campo_n74=trim($campos[145]);

		if( trim($campos[$numero_campo])!="98" 
				&& ($campo_n74=="2" || $campo_n74=="3")
		)
		{
			$campos[$numero_campo]="98";
		}//fin if
	    
		
		
		
	}//if si existe campo
	
	//numero_orden_desde_cero 151 numero orden 152 numero campo 80 
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n83=trim($campos[154]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="1845-01-01"
	       && $campo_n83=="98"
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 152 numero orden 153 numero campo 81 vcalidad
	$numero_campo=152;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n83=trim($campos[154]);

	     $consulta83="";
		$consulta83.="SELECT * FROM gioss_cups WHERE codigo_procedimiento='".$campo_n83."' ; ";
		$resultado83=$coneccionBD->consultar2_no_crea_cierra($consulta83);

	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_n83=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n83!="98" && ( count($resultado83)>0 && is_array($resultado83) ) ) 
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 153 numero orden 154 numero campo 82 
	$numero_campo=153;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n83=trim($campos[154]);

	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_n83=="98")
	       )
	    {
			$campos[$numero_campo]="98";
	    }//fin if
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n83!="98")
	       )
	    {
			if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }//fin if
		
	}//if si existe campo
	
	
	
	
	
	//numero_orden_desde_cero 155 numero orden 156 numero campo 84 
	$numero_campo=155;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n83=trim($campos[154]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_n83=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n83!="98")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 156 numero orden 157 numero campo 85 vcalidad
	$numero_campo=156;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n74=trim($campos[145]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_n74!="1")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n74=="1")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 157 numero orden 158 numero campo 86 vcalidad
	$numero_campo=157;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    
	    $campo_87=trim($campos[158]);
	    $campo_89=trim($campos[160]);
	    $campo_90=trim($campos[161]);
	    $campo_91=trim($campos[162]);
	    $campo_92=trim($campos[163]);
	    $campo_93=trim($campos[164]);
	    $campo_95=trim($campos[166]);
	    $campo_96=trim($campos[167]);
	    $campo_98=trim($campos[169]);
	    $campo_99=trim($campos[170]);
	    $campo_100=trim($campos[171]);
	    $campo_101=trim($campos[172]);
	    $campo_102=trim($campos[173]);
	    $campo_104=trim($campos[175]);
	    $campo_105=trim($campos[176]);		
	    
	    $campo_88=trim($campos[159]);
	    $campo_94=trim($campos[165]);
	    $campo_97=trim($campos[168]);
	    $campo_103=trim($campos[174]);
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="1"
	       && (
		$campo_87=="98"
		&& $campo_89=="98"
		&& $campo_90=="98"
		&& $campo_95=="98"
		&& $campo_96=="98"
		&& $campo_98=="98"
		&& $campo_99=="98"
		&& $campo_104=="98"
		&& $campo_105=="98"
		)
	   && (
		$campo_88=="1845-01-01"
		&& $campo_94=="1845-01-01"
		&& $campo_97=="1845-01-01"
		&& $campo_103=="1845-01-01"
		)
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="1"
		    && (
			$campo_87!="98"
			|| $campo_89!="98"
			|| $campo_90!="98"
			|| $campo_95!="98"
			|| $campo_96!="98"
			|| $campo_98!="98"
			|| $campo_99!="98"
			|| $campo_104!="98"
			|| $campo_105!="98"
			|| $campo_88!="1845-01-01"
			|| $campo_94!="1845-01-01"
			|| $campo_97!="1845-01-01"
			|| $campo_103!="1845-01-01"
			)
		    )
	    {
		    $campos[$numero_campo]="1";
	    }
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 158 numero orden 159 numero campo 87 vcalidad
	$numero_campo=158;
	if(isset($campos[$numero_campo]))
	{
		
	    $campo_ant_86=$campos[157];
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       &&  ($campo_ant_86=="2" || $campo_ant_86=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && $campo_ant_86=="1"
		    )
	    {
		    $campos[$numero_campo]="1";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 159 numero orden 160 numero campo 88 vcalidad
	$numero_campo=159;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="1845-01-01" 
	    	&& ($campo_ant_86=="2" ||  $campo_ant_86=="98")
	    	)
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 160 numero orden 161 numero campo 89 vcalidad
	$numero_campo=160;
	if(isset($campos[$numero_campo]))
	{
		$campo_ant_86=$campos[157];
		if($campos[$numero_campo]!="98" 
			&& ($campo_ant_86=="2" ||  $campo_ant_86=="98")
			)

		{
			$campos[$numero_campo]="98";
			
		}//fin if
		else if($campos[$numero_campo]=="98" 
			&& $campo_ant_86=="1"
			)
		{
			$campos[$numero_campo]="2";
			
		}//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 161 numero orden 162 numero campo 90 vcalidad
	$numero_campo=161;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    //campo obligatorio
	     if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98" && $campo_ant_86=="1")
	    {
		    $campos[$numero_campo]="1";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 162 numero orden 163 numero campo 91 vcalidad
	$numero_campo=162;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98" && $campo_ant_86=="1")
	    {
		    $campos[$numero_campo]="1";
	    }
	    		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 163 numero orden 164 numero campo 92 vcalidad
	$numero_campo=163;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    //campo obligatorio

	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98" && $campo_ant_86=="1")
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 164 numero orden 165 numero campo 93 vcalidad
	$numero_campo=164;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
			$campos[$numero_campo]="98";
	    }
		
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 165 numero orden 166 numero campo 94 vcalidad
	$numero_campo=165;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="1845-01-01"
	     && ($campo_ant_86=="2" ||  $campo_ant_86=="98")
	     )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 166 numero orden 167 numero campo 95 vcalidad
	$numero_campo=166;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    $campo_n94=trim($campos[165]);
	    $c94_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n94,"1900-12-31");
	    
	    //campo obligatorio

	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && $campo_ant_86=="1"
	       )
	    {
		    $campos[$numero_campo]="3";
	    }

	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 167 numero orden 168 numero campo 96 vcalidad
	$numero_campo=167;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_95=$campos[166];
	    $campo_ant_86=$campos[157];
	    
	    
	    if($campos[$numero_campo]!="98"
	       &&  ($campo_ant_86=="2" || $campo_ant_86=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]!="98"
		    && $campo_ant_86=="1"
		    && ($campo_ant_95!="2")
		    )
	    {
		    $campos[$numero_campo]="98";
		    
	    }//fin if
	    else if($campos[$numero_campo]=="98"
		    && $campo_ant_86=="1"
		    && ($campo_ant_95=="2") 
		    )
	    {
		    $campos[$numero_campo]="7";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 168 numero orden 169 numero campo 97 vcalidad
	$numero_campo=168;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="1845-01-01" && ($campo_ant_86=="2" ||  $campo_ant_86=="98"))
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 169 numero orden 170 numero campo 98 vcalidad
	$numero_campo=169;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    $campo_n97=trim($campos[168]);
	    $c97_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n97,"1900-12-31");
	    //campo obligatorio
	    
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && $campo_ant_86=="1"
		    && $c97_es_fecha_calendario<0
		    )
	    {
		    $campos[$numero_campo]="2";
	    }
	     
	}//if si existe campos
	
	
	//numero_orden_desde_cero 170 numero orden 171 numero campo 99 vcalidad
	$numero_campo=170;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    $campo_n97=trim($campos[168]);
	    $c97_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n97,"1900-12-31");
	    //campo obligatorio
	    
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && $campo_ant_86=="1"
		    && $c97_es_fecha_calendario<0
		    )
	    {
		    $campos[$numero_campo]="1";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 171 numero orden 172 numero campo 100 vcalidad
	$numero_campo=171;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    $campo_n97=trim($campos[168]);
	    $c97_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n97,"1900-12-31");
	    //campo obligatorio
	    
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && $campo_ant_86=="1"
		    && $c97_es_fecha_calendario<0
		    )
	    {
		    $campos[$numero_campo]="1";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 172 numero orden 173 numero campo 101 vcalidad 
	$numero_campo=172;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    //campo obligatorio
	    $campo_n97=trim($campos[168]);
	    $c97_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n97,"1900-12-31");
	    
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
		    && $campo_ant_86=="1"
		    && $c97_es_fecha_calendario<0
		    )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 173 numero orden 174 numero campo 102 vcalidad
	$numero_campo=173;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	    if($NOMBRE_ENTIDAD_PERSONALIZADA=="Fundacion_Valle_Del_Lili")
	    {
		$campos[$numero_campo]="98";
	    }
	    
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 174 numero orden 175 numero campo 103 vcalidad
	$numero_campo=174;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    
		    
	    $campo_ant_31=trim($campos[30]);
	    $campo_ant_20=$campos[19];
	    
	    if($campos[$numero_campo]!="1845-01-01" && ($campo_ant_86=="2" ||  $campo_ant_86=="98"))
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 175 numero orden 176 numero campo 104 vcalida
	$numero_campo=175;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    $campo_n103=trim($campos[174]);
	    $c103_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n103,"1900-12-31");
	    
	    $campo_n105=trim($campos[176]);
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
		    && ( $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && $campo_ant_86!="98"
	       && $campo_n105!="98"
	       )
	    {
		    $campos[$numero_campo]="2";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && $campo_ant_86!="98"
	       && $campo_n105=="98"
	       )
	    {
		    $campos[$numero_campo]="3";
	    }

	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 176 numero orden 177 numero campo 105 vcalidad
	$numero_campo=176;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_86=$campos[157];
	    
	    $campo_n104=trim($campos[175]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
		    && ($campo_ant_86=="2" || $campo_ant_86=="98")
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])!="98"
	       && $campo_ant_86=="1"
	       && ($campo_n104!="2")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && $campo_ant_86=="1"
	       && ($campo_n104=="2")
	       )
	    {
		    $campos[$numero_campo]="7";
	    }
		
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 177 numero orden 178 numero campo 106 vcalidad
	$numero_campo=177;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n107=trim($campos[178]);
	    $campo_n108=trim($campos[179]);
	    $campo_n109=trim($campos[180]);
	    $campo_n110=trim($campos[181]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="1"
		&& ($campo_n107!="98"
		    || $campo_n108!="98"
		    || $campo_n109!="1845-01-01"
		    )
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if(trim($campos[$numero_campo])=="1"
		    && ($campo_n107=="98"
			&& $campo_n108=="98"
			&& $campo_n109=="1845-01-01"
			)
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 178 numero orden 179 numero campo 107 vcalidad
	$numero_campo=178;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_106=trim($campos[177]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="98"
	       && ($campo_106=="1")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if(trim($campos[$numero_campo])!="98"
	       && ($campo_106=="2" || $campo_106=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 179 numero orden 180 numero campo 108 
	$numero_campo=179;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_106=trim($campos[177]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="98"
	       && ($campo_106=="1")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if(trim($campos[$numero_campo])!="98"
	       && ($campo_106=="2" || $campo_106=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 180 numero orden 181 numero campo 109 vcalidad
	$numero_campo=180;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_106=$campos[177];
	    
	    
	    if($campos[$numero_campo]!="1845-01-01" && ($campo_ant_106=="2" || $campo_ant_106=="98" ))
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 181 numero orden 182 numero campo 110 vcalidad
	$numero_campo=181;
	if(isset($campos[$numero_campo]))
	{
	    $campo_106=trim($campos[177]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_106=="2" || $campo_106=="98")
	       )
	    {
			$campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_106=="1")
	       )
	    {
			if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 182 numero orden 183 numero campo 111 
	$numero_campo=182;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n112=trim($campos[183]);
	    $c112_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n112,"1900-12-31");
	    //campo obligatorio
	    if($c112_es_fecha_calendario<0  && trim($campos[$numero_campo])!="1")
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if($campo_n112=="1845-01-01"  && trim($campos[$numero_campo])=="1")
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 183 numero orden 184 numero campo 112 vcalidad
	$numero_campo=183;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_111=$campos[182];
	    
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $campo_actual_es_inferior_year_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_primer_dia_corte);
	    if($campos[$numero_campo]!="1845-01-01"
	       && ($campo_ant_111=="2" || $campo_ant_111=="98")
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 184 numero orden 185 numero campo 113 
	$numero_campo=184;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_111=$campos[182];
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_ant_111=="2" || $campo_ant_111=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_ant_111=="1")
	       )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 185 numero orden 186 numero campo 114 vcalidad
	$numero_campo=185;
	if(isset($campos[$numero_campo]))
	{
	    $c114_1=trim($campos[186]);
	    $c114_2=trim($campos[187]);
	    $c114_3=trim($campos[188]);
	    $c114_4=trim($campos[189]);
	    $c114_5=trim($campos[190]);
	    $c114_6=trim($campos[191]);
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])=="1"
	       && (($c114_1=="2" || $c114_1=="")
		    && ($c114_2=="2" || $c114_2=="")
		    && ($c114_3=="2" || $c114_3=="")
		    && ($c114_4=="2" || $c114_4=="")
		    && ($c114_5=="2" || $c114_5=="")
		    && ($c114_6=="2" || $c114_6=="")
		    )
	       )
	    {
		    $campos[$numero_campo]="3";
	    }
	    else if((trim($campos[$numero_campo])=="2" || trim($campos[$numero_campo])=="3")
	       && ($c114_1=="1"
		    || $c114_2=="1"
		    || $c114_3=="1"
		    || $c114_4=="1"
		    || $c114_5=="1"
		    || $c114_6=="1"
		    )
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 186 numero orden 187 numero campo 114.1 vcalidad
	$numero_campo=186;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    if($campos[$numero_campo]=="1" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 187 numero orden 188 numero campo 114.2 
	$numero_campo=187;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    if($campos[$numero_campo]=="1" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 188 numero orden 189 numero campo 114.3 
	$numero_campo=188;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    if($campos[$numero_campo]=="1" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 189 numero orden 190 numero campo 114.4 
	$numero_campo=189;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    if($campos[$numero_campo]=="1" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
		
	}//if si existe campos
	
	
	//numero_orden_desde_cero 190 numero orden 191 numero campo 114.5 
	$numero_campo=190;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    if($campos[$numero_campo]=="1" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 191 numero orden 192 numero campo 114.6 
	$numero_campo=191;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    if($campos[$numero_campo]=="1" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="2";
		    
	    }//fin if
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 192 numero orden 193 numero campo 115 
	$numero_campo=192;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_114=$campos[185];
	    
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $campo_actual_es_inferior_year_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_primer_dia_corte);
	    if($campos[$numero_campo]!="1845-01-01" && ($campo_ant_114=="2" || $campo_ant_114=="3"))
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	    	
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 193 numero orden 194 numero campo 116 vcalidad
	$numero_campo=193;
	if(isset($campos[$numero_campo]))
	{
	    
	    $campo_ant_114=$campos[185];
			$campo_n115=trim($campos[192]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_ant_114=="2" || $campo_ant_114=="3")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_ant_114=="1")
	       )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
	    else if(trim($campos[$numero_campo])!="98"
	       && $campo_n115=="1845-01-01"
	       && ($campo_ant_114!="1")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_ant_114=="1")
           && $campo_n115!="1845-01-01"
	       )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 194 numero orden 195 numero campo 117 
	$numero_campo=194;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n118=trim($campos[195]);
	    $c118_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n118,"1900-12-31");
	    //campo obligatorio
	    if((trim($campos[$numero_campo])=="2" || trim($campos[$numero_campo])=="98")
	       && $c118_es_fecha_calendario<0
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if( (trim($campos[$numero_campo])=="1")
		    && $campo_n118=="1845-01-01"
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 195 numero orden 196 numero campo 118 vcalidad
	$numero_campo=195;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_117=$campos[194];
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $campo_actual_es_inferior_year_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_primer_dia_corte);
	    if($campos[$numero_campo]!="1845-01-01"
		    && ($campo_ant_117!="1")
		    )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
		
	    
	}//if si existe campo
	
	
	//numero_orden_desde_cero 196 numero orden 197 numero campo 119 vcalidad
	$numero_campo=196;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_117=$campos[194];
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_ant_117=="2" || $campo_ant_117=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_ant_117=="1")
	       )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 197 numero orden 198 numero campo 120 vcalidad
	$numero_campo=197;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n121=trim($campos[198]);
	    $c121_es_fecha_calendario=diferencia_dias_entre_fechas($campo_n121,"1900-12-31");
	    //campo obligatorio
	    if((trim($campos[$numero_campo])=="2" || trim($campos[$numero_campo])=="98")
	       && $c121_es_fecha_calendario<0
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if( (trim($campos[$numero_campo])=="1")
		    && $campo_n121=="1845-01-01"
		    )
	    {
		    $campos[$numero_campo]="98";
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 198 numero orden 199 numero campo 121 vcalidad
	$numero_campo=198;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_120=$campos[197];
	    
	    $es_fecha_calendario=diferencia_dias_entre_fechas($campos[$numero_campo],"1900-12-31");
	    $campo_actual_es_inferior_year_corte=diferencia_dias_entre_fechas($campos[$numero_campo],$fecha_primer_dia_corte);
	    if($campos[$numero_campo]!="1845-01-01"
		    && ($campo_ant_120=="2" || $campo_ant_120=="98")
		    )
	    {
		    $campos[$numero_campo]="1845-01-01";
		    
	    }//fin if
	}//if si existe campo
	
	
	//numero_orden_desde_cero 199 numero orden 200 numero campo 122 vcalidad
	$numero_campo=199;
	if(isset($campos[$numero_campo]))
	{
	    $campo_ant_120=$campos[197];
	    
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_ant_120=="2" || $campo_ant_120=="98")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_ant_120=="1")
	       )
	    {
		    if($cod_prestador!="AGRUP_EAPB"){$campos[$numero_campo]=$cod_prestador;}
	    }
		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 200 numero orden 201 numero campo 123 vcalidad
	$numero_campo=200;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 201 numero orden 202 numero campo 124 vcalidad
	$numero_campo=201;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica		
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 202 numero orden 203 numero campo 125 
	$numero_campo=202;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 203 numero orden 204 numero campo 126 
	$numero_campo=203;
	if(isset($campos[$numero_campo]))
	{
	    //no aplica
		
	}//if si existe campo
	
	
	
	//numero_orden_desde_cero 204 numero orden 205 numero campo 127 
	$numero_campo=204;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 205 numero orden 206 numero campo 128 
	$numero_campo=205;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n127=trim($campos[204]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="4"
	    	&& trim($campos[$numero_campo])!="10"
	       && ($campo_n127=="2" || $campo_n127=="3")
	       )
	    {
		    $campos[$numero_campo]="4";
	    }
	    else if(
	    	(trim($campos[$numero_campo])=="4" || trim($campos[$numero_campo])=="10")
	       && ($campo_n127=="1" || $campo_n127=="99")
	       )
	    {
		    $campos[$numero_campo]="0";
	    }
	}//if si existe campo
	
	
	//numero_orden_desde_cero 206 numero orden 207 numero campo 129 
	$numero_campo=206;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n127=trim($campos[204]);
			
	    if(trim($campos[$numero_campo])!="7"
	       && ($campo_n127=="2" || $campo_n127=="3")
	       )
	    {
		    $campos[$numero_campo]="7";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 207 numero orden 208 numero campo 130
	$numero_campo=207;
	if(isset($campos[$numero_campo]))
	{
		//no aplica
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 208 numero orden 209 numero campo 131 
	$numero_campo=208;
	if(isset($campos[$numero_campo]))
	{
	   $campo_n127=trim($campos[204]);
	    //campo NO obligatorio
	    if(trim($campos[$numero_campo])!="1845-01-01"
	       && ($campo_n127=="1" || $campo_n127=="99")
	       )
	    {
		    $campos[$numero_campo]="1845-01-01";
	    }
		
	}//if si existe campo
	
	
	//numero_orden_desde_cero 209 numero orden 210 numero campo 132 
	$numero_campo=209;
	if(isset($campos[$numero_campo]))
	{
	    $campo_n127=trim($campos[204]);
	    //campo obligatorio
	    if(trim($campos[$numero_campo])!="98"
	       && ($campo_n127=="1" || $campo_n127=="99")
	       )
	    {
		    $campos[$numero_campo]="98";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n127=="2")
	       )
	    {
		    $campos[$numero_campo]="1";
	    }
	    else if(trim($campos[$numero_campo])=="98"
	       && ($campo_n127=="3")
	       )
	    {
		    $campos[$numero_campo]="4";
	    }
		
	}//if si existe campo
	
	
	
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  reparar Cancer
?>