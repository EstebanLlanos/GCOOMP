<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

function validar_ARTE($campos,
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
		      &$coneccionBD)
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
	
	
	
	//PARTE LLENADO ARRAY PARA EL NUMERO DE CAMPO REAL INDEXADO POR EL NUMERO DE ORDEN(NUMERO CAMPO SISTEMATICO)
	$query_consulta_estructura_numero_campos="";
	$query_consulta_estructura_numero_campos.=" SELECT numero_de_orden,numero_de_campo FROM gioss_estructura_campos_por_norma_a_reportar ";
	$query_consulta_estructura_numero_campos.=" WHERE ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_norma_obligatoria='08' ";
	$query_consulta_estructura_numero_campos.=" AND ";
	$query_consulta_estructura_numero_campos.=" codigo_tipo_archivo='0801' ORDER BY numero_de_orden ";
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
	
	//VALIDACION CARACTERES POR CAMPO
	$campos_ver_characters=$campos;
	$cont_campos=0;
	while($cont_campos<count($campos_ver_characters))
	{
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
		$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104099"])[1];
		$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104099,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$cont_campos];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103028"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103028,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}//fin if si no esta en blanco
				
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}//fin if si el campo no esta en blanco
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(
			(intval($campos[$numero_campo])<1 || intval($campos[$numero_campo])>16)
			&&
			(intval($campos[$numero_campo])<31 || intval($campos[$numero_campo])>39)
			&&
			(intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>61)
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103017"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103017,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101011"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101011,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101011"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101011,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101011"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101011,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101011"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101011,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103027"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103027,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103030"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103030,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       && intval($campos[$numero_campo])!=5
		       && intval($campos[$numero_campo])!=6
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103014"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103014,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 12  numero campo 13 
	$numero_campo=12;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio 
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101011"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101011,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 13  numero campo 14 
	$numero_campo=13;
	if(isset($campos[$numero_campo]))
	{
	    //no es campo obligatorio 
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101011"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101011,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    
		}
		
	}//if si existe campo
	
	
	//numero_orden 14  numero campo 15 
	$numero_campo=14;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101004"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101004,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103031"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103031,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 15  numero campo 16 
	$numero_campo=15;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
						$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
						$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
						$consecutivo_errores++;
						
						$hubo_errores=true;
					}//fin if
				}//fin if
			}//fin if
			
			//valor permitido
			$excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
			$es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
			if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       && trim($campos[$numero_campo])!="1822-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103024"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103024,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<50 || intval($campos[$numero_campo])>250)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103021"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103021,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if(
			    intval($campos[$numero_campo])<3
			    || intval($campos[$numero_campo])>300
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103019"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103019,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<3 || intval($campos[$numero_campo])>50) && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103020"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103020,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101005"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
			if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20000) && intval($campos[$numero_campo])!=22222 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103005"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20) && intval($campos[$numero_campo])!=300 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103004"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103004,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 32  numero campo 33
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 33  numero campo 34
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 34  numero campo 35
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 35  numero campo 36
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 36  numero campo 37 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 37  numero campo 38
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 38  numero campo 39 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    
		}
		
	}//if si existe campo
	
	
	//numero_orden 39  numero campo 40 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 40  numero campo 41 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       && intval($campos[$numero_campo])!=5
		       && intval($campos[$numero_campo])!=6
		       && intval($campos[$numero_campo])!=7
		       && intval($campos[$numero_campo])!=8
		       && intval($campos[$numero_campo])!=9
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103016"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103016,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 41  numero campo 42 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103008"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103008,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if		    
		    
		}
	}//if si existe campo
	
	
	//numero_orden 42  numero campo 43 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 43  numero campo 44
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>3) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 44  numero campo 45.1 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 45  numero campo 45.2 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 46  numero campo 45.03 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 47  numero campo 45.04
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 48  numero campo 45.05
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 49  numero campo 45.06 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 50  numero campo 45.07
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 51  numero campo 45.08
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 52  numero campo 46.01
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 53  numero campo 46.02 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 54  numero campo 46.03 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 55  numero campo 46.04 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 56  numero campo 46.05 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
			
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
			
			
		}
	}//if si existe campo
	
	
	//numero_orden 57  numero campo 46.06 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 58  numero campo 46.07 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 59  numero campo 46.08 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 60  numero campo 46.09 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 61  numero campo 46.10
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 62  numero campo 47.01 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 63  numero campo 47.02
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 64  numero campo 47.03 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 65  numero campo 47.04 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 66  numero campo 47.05
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 67  numero campo 47.06 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 68  numero campo 47.07 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 69  numero campo 47.08
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	
	//numero_orden 70  numero campo 47.09
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 71  numero campo 47.10
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 72  numero campo 47.11
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 73  numero campo 47.12
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 74  numero campo 47.13
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 75  numero campo 47.14
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101005"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 76  numero campo 48
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(
			intval($campos[$numero_campo])<3
			|| intval($campos[$numero_campo])>300
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103019"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103019,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 77  numero campo 49
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 78  numero campo 50
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 79  numero campo 51
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101005"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 80  numero campo 52 
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	//numero_orden 81  numero campo 53
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<3 || intval($campos[$numero_campo])>50) && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103020"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103020,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 82  numero campo 54
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101005"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
			if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20000) && intval($campos[$numero_campo])!=22222 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103005"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 83  numero campo 55
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
			if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>20) && intval($campos[$numero_campo])!=300 )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103004"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103004,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 84  numero campo 56
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>250) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 85  numero campo 57
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
		
	}//if si existe campo
	
	
	//numero_orden 88  numero campo 60
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 89  numero campo 61 
	$numero_campo=89;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	//numero_orden 90  numero campo 62
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 91  numero campo 63
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 92  numero campo 64
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103013"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103013,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 93  numero campo 65
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
		
	//numero_orden 94  numero campo 66
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       && intval($campos[$numero_campo])!=5
		       && intval($campos[$numero_campo])!=6
		       && intval($campos[$numero_campo])!=7
		       && intval($campos[$numero_campo])!=8
		       && intval($campos[$numero_campo])!=9
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103016"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103016,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
	}//if si existe campo
	
	
	
	//numero_orden 95  numero campo 67
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>10) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103008"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103008,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	
	//numero_orden 96  numero campo 68
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=300)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103012"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103012,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 97  numero campo 69
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if((intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>3) && intval($campos[$numero_campo])!=300 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 99  numero campo 71.01
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 100  numero campo 71.02
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 101  numero campo 71.03
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 102  numero campo 71.04
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 103  numero campo 71.05
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 103  numero campo 71.05
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103005"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 104  numero campo 71.06
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 105  numero campo 71.07
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0 && intval($campos[$numero_campo])!=1)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 106  numero campo 72
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 107  numero campo 73.01
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 108  numero campo 73.02
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 108  numero campo 73.02
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 109  numero campo 73.03
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 110  numero campo 73.04
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 111  numero campo 73.05
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 112  numero campo 73.06
	$numero_campo=112;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 113  numero campo 73.07
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 114  numero campo 73.08
	$numero_campo=114;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 115  numero campo 73.09
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 116  numero campo 73.10
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 117  numero campo 74.01
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
			$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0104001"])[1];
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 118  numero campo 74.02
	$numero_campo=118;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 119  numero campo 74.03
	$numero_campo=119;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 120  numero campo 74.04
	$numero_campo=120;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 121  numero campo 74.05
	$numero_campo=121;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 122  numero campo 74.06
	$numero_campo=122;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 123  numero campo 74.07
	$numero_campo=123;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 124  numero campo 74.08
	$numero_campo=124;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 125  numero campo 74.09
	$numero_campo=125;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 126  numero campo 74.10
	$numero_campo=126;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	
	//numero_orden 127  numero campo 74.11
	$numero_campo=127;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 128  numero campo 74.12
	$numero_campo=128;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 129  numero campo 74.13
	$numero_campo=129;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 130  numero campo 74.14
	$numero_campo=130;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103026"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103026,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 131  numero campo 75.01
	$numero_campo=131;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 132  numero campo 75.02
	$numero_campo=132;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 133  numero campo 75.03
	$numero_campo=133;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 134  numero campo 75.04
	$numero_campo=134;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 135  numero campo 75.05
	$numero_campo=135;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 136  numero campo 75.06
	$numero_campo=136;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 137  numero campo 75.07
	$numero_campo=137;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       &&intval($campos[$numero_campo])!=5)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103010"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103010,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	
	//numero_orden 138  numero campo 76
	$numero_campo=138;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>12)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103003,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 139  numero campo 77
	$numero_campo=139;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101009"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101009,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103039"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103039,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 140  numero campo 78
	$numero_campo=140;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101005"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101005,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103031"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103031,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 141  numero campo 79
	$numero_campo=141;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       && trim($campos[$numero_campo])!="1800-01-01"
		       && trim($campos[$numero_campo])!="1811-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103023"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103023,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 142  numero campo 80
	$numero_campo=142;
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
			$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0104,".$array_grupo_validacion["0104"].",0104001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3
		       && intval($campos[$numero_campo])!=4
		       && intval($campos[$numero_campo])!=5
		       && intval($campos[$numero_campo])!=6
		       && intval($campos[$numero_campo])!=7
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103015"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103015,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 143  numero campo 81
	$numero_campo=143;
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])<0 || intval($campos[$numero_campo])>12 )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103011"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103011,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 144  numero campo 82
	$numero_campo=144;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103025"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103025,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 145  numero campo 83
	$numero_campo=145;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101006"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101006,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102001,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    $consulta="";
		    $consulta.="SELECT * FROM gios_entidad_administradora WHERE cod_entidad_administradora='".$campos[$numero_campo]."' ; ";
		    $resultado=$coneccionBD->consultar2_no_crea_cierra($consulta);
		    if( count($resultado)==0  && intval($campos[$numero_campo])!=999999)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103029"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103029,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 146  numero campo 84
	$numero_campo=146;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101007"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101007,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
				    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
				    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
					    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102002"])[1];
					    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102002,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
					    $consecutivo_errores++;
					    
					    $hubo_errores=true;
				    }//fin if
			    }//fin if
		    }//fin if
		    
		    //valor permitido
		    $excede_fecha_corte=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),$fecha_de_corte);
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($excede_fecha_corte<0 || $es_menor_a_2010_12_31>0)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103022"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103022,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    $es_menor_a_2010_12_31=diferencia_dias_entre_fechas(trim($campos[$numero_campo]),"2010-12-31");
		    if($es_menor_a_2010_12_31>0
		       && trim($campos[$numero_campo])!="1799-01-01"
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103025"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0103025,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    
		}
		
	}//if si existe campo
	
	//numero_orden 147  numero campo 85
	$numero_campo=147;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101001"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101001,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(intval($campos[$numero_campo])!=0
		       && intval($campos[$numero_campo])!=1
		       && intval($campos[$numero_campo])!=2
		       && intval($campos[$numero_campo])!=3)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103002"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103002,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 148  numero campo 86
	$numero_campo=148;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>11)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101008"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101008,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(
			intval($campos[$numero_campo])<100
			|| intval($campos[$numero_campo])>99999999999
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103018"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103018,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 149  numero campo 87
	$numero_campo=149;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>11)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101008"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101008,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(
			intval($campos[$numero_campo])<100
			|| intval($campos[$numero_campo])>99999999999
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103018"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103018,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 150  numero campo 88
	$numero_campo=150;
	if(isset($campos[$numero_campo]))
	{
		//no es campo obligatorio
		
		if($campos[$numero_campo]!="")
		{
		    //longitud de campo
		    if(strlen($campos[$numero_campo])>11)
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101008"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101008,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
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
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		    
		    //valor permitido
		    if(
			intval($campos[$numero_campo])<100
			|| intval($campos[$numero_campo])>99999999999
		       )
		    {
			    if($errores_campos!="")
			    {
				    $errores_campos.="|";
			    }		
			    //consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
			    $cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103018"])[1];
			    $errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103018,$cadena_descripcion_inconsistencia ...".$campos[$numero_campo]." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
			    $consecutivo_errores++;
			    
			    $hubo_errores=true;
		    }//fin if
		}
		
	}//if si existe campo
	
	//numero_orden 151  numero campo 89
	$numero_campo=151;
	if(isset($campos[$numero_campo]))
	{
		$campo_fix=preg_replace("/[^A-Za-z0-9:,.\/\_\|\-\s+]/", "", trim($campos[$numero_campo]) );
		
		//no es campo obligatorio
		
		if($campo_fix!="")
		{
			//longitud de campo
			if(strlen($campo_fix)>11)
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0101008"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0101,".$array_grupo_validacion["0101"].",0101008,$cadena_descripcion_inconsistencia ...".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//formato de campo
			if(!ctype_digit($campo_fix))
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0102003"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0102,".$array_grupo_validacion["0102"].",0102003,$cadena_descripcion_inconsistencia ...VR:".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if
			
			//valor permitido
			if(
			    intval($campo_fix)<100
			    || intval($campo_fix)>99999999999
			   )
			{
				if($errores_campos!="")
				{
					$errores_campos.="|";
				}		
				//consecutivo|nombre|codigo_tipo_inconsistencia|desc_tipo_inconsistencia|codigo_grupo_inconsistencia|desc_tipo_inconsistencia|codigo_detalle_inconsistencia|desc_detalle|linea|campo
				$cadena_descripcion_inconsistencia=explode(";;",$array_detalle_validacion["0103018"])[1];
				$errores_campos.=$consecutivo_errores.",".$nombre_archivo_registrado.",01,".$array_tipo_validacion["01"].",0103,".$array_grupo_validacion["0103"].",0103018,$cadena_descripcion_inconsistencia ...".$campo_fix." ,".($nlinea+1).",".$array_numero_campo_bd[$numero_campo];
				$consecutivo_errores++;
				
				$hubo_errores=true;
			}//fin if			
			
			
		}
		
	}//if si existe campo
	
	return array("error"=>$hubo_errores,"mensaje"=>$errores_campos);
}//fin funcion  validarVIH
?>