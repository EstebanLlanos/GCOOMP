<?php
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

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

function subir_a_tablas_poblacion_riesgo($array_registro_campos4505,
                                         $nombre_archivo_para_zip,
                                         $cod_eapb,
                                         $fecha_corte_bd,
                                         $fecha_actual,
                                         $tiempo_actual,
                                         $nick_user,
                                         $identificacion,
                                         $tipo_id,
                                         $numero_secuencia,
                                         $regimen,
                                         &$mensaje_proceso,
                                         $conexionbd
                                         )
{
    date_default_timezone_set("America/Bogota");
    
    //datos prestador
    $cod_habilitacion_prestador=$array_registro_campos4505[2];
    
    $nit_prestador="";
    $tipo_id_prestador="";
    
    $query_buscar_nit="";
    $query_buscar_nit.="SELECT cod_tipo_identificacion,num_tipo_identificacion FROM gios_prestador_servicios_salud ";
    $query_buscar_nit.=" WHERE trim(cod_registro_especial_pss)='$cod_habilitacion_prestador' ; ";
    $error_bd_seq="";
    $resultados_buscar_nit=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_buscar_nit, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensaje_proceso.="error al consultar gios_prestador_servicios_salud <br>";
    }
    
    if(is_array($resultados_buscar_nit) && count($resultados_buscar_nit)>0)
    {
        $nit_prestador=$resultados_buscar_nit[0]["num_tipo_identificacion"];
        $tipo_id_prestador=$resultados_buscar_nit[0]["cod_tipo_identificacion"];
    }
    //fin datos prestador
    
    //otros datos preliminares
    $consecutivo_del_archivo="";
    $consecutivo_del_archivo=substr($nombre_archivo_para_zip,-2);
    
    $numero_fila=$array_registro_campos4505[1];
    
    //fin otros datos preliminares
    
    //PARTOS
    $sube_a_tabla=false;
    $campo_49_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[49],"1900-12-31");
    if($campo_49_es_fecha_calendario<0
       || $array_registro_campos4505[49]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_partos_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="campo_49_fecha_atencion_parto,";
        $query_subida_riesgo_poblacion.="campo_50_fecha_atencion_egreso_parto";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[49]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[50]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_partos_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
                
        }
    }//fin if se cumple sube a tabla
    
    //PSICOLOGIA
    $sube_a_tabla=false;
    $campo_68_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[68],"1900-12-31");
    if($campo_68_es_fecha_calendario<0
       || $array_registro_campos4505[68]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_atencion_por_psicologia_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="campo_68_consulta_de_psicologia";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[68]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_atencion_por_psicologia_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
                echo "regimen: ".$regimen." longitud: ".strlen($regimen)." , campo 10: ".$array_registro_campos4505[10]." longitud: ".strlen($array_registro_campos4505[10])." ".$error_bd_seq."<br>";
        }
    }//fin if se cumple sube a tabla
    
    //ADULTO MAYOR
    $sube_a_tabla=false;
    $campo_73_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[73],"1900-12-31");
    if($campo_73_es_fecha_calendario<0
       || $array_registro_campos4505[73]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_adulto_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="campo_73_consulta_de_adulto_primera_vez";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[73]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_adulto_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if se cumple sube a tabla
    
    //ITS
    $sube_a_tabla=false;
    
    //echo "<script>alert('c24:".$array_registro_campos4505[24].", c81:".$array_registro_campos4505[81].", c83:".$array_registro_campos4505[83]."');</script>";
    if($array_registro_campos4505[24]=="1")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    else if($array_registro_campos4505[81]=="2")
    {
        $sube_a_tabla=true;  
    }
    else if($array_registro_campos4505[83]=="2")
    {
        $sube_a_tabla=true;  
    }
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_infeccion_trasmision_sexual_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_24_infecciones_de_trasmision_sexual,";
        $query_subida_riesgo_poblacion.="campo_74_preservativos_entregados_a_pacientes_con_its,";
        $query_subida_riesgo_poblacion.="campo_75_asesoria_pre_test_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_76_asesoria_pos_test_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_80_fecha_serologia_para_sifilis,";
        $query_subida_riesgo_poblacion.="campo_81_resultado_serologia_para_sifilis,";
        $query_subida_riesgo_poblacion.="campo_82_fecha_de_toma_de_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_83_resultado_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_115_tratamiento_para_sifilis_gestacional,";
        $query_subida_riesgo_poblacion.="campo_116_tratamiento_para_sifilis_congenita";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[24]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[74]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[75]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[76]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[80]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[81]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[82]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[83]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[115]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[116]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_infeccion_trasmision_sexual_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if se cumple sube a tabla
    
    
    //ENFERMEDAD MENTAL
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[25]=="1"
       || $array_registro_campos4505[25]=="2"
       || $array_registro_campos4505[25]=="3"
       || $array_registro_campos4505[25]=="4"
       || $array_registro_campos4505[25]=="5"
       || $array_registro_campos4505[25]=="6"
       )
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_victima_enfermedad_mental_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_25_enfermedad_mental,";
        $query_subida_riesgo_poblacion.="campo_77_paciente_con_diagnostico_de_enfermedad_mental";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[25]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[77]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_victima_enfermedad_mental_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if se cumple sube a tabla
    
    //CANCER DE SENO
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[27]=="1"
       )
    {
        $sube_a_tabla=true;        
        
    }//fin if
    else if($array_registro_campos4505[97]=="4"
            || $array_registro_campos4505[97]=="5"
            || $array_registro_campos4505[97]=="6"
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_cancer_seno_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_27_cancer_de_seno,";
        $query_subida_riesgo_poblacion.="campo_96_fecha_mamografia,";
        $query_subida_riesgo_poblacion.="campo_97_resultado_mamografia,";
        $query_subida_riesgo_poblacion.="campo_98_codigo_de_habilitacion_ips_donde_se_toma_mamografia,";
        $query_subida_riesgo_poblacion.="campo_99_fecha_toma_biopsia_seno_por_bacaf,";
        $query_subida_riesgo_poblacion.="campo_100_fecha_resultado_biopsia_seno_por_bacaf,";
        $query_subida_riesgo_poblacion.="campo_101_biopsia_seno_por_bacaf,";
        $query_subida_riesgo_poblacion.="campo_102_codigo_de_habilitacion_ips_donde_se_toma_biopsia_seno_por_bacaf";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[27]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[96]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[97]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[98]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[99]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[100]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[101]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[102]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_cancer_seno_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //LEPRA
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[20]=="1"
       || $array_registro_campos4505[20]=="2"
       )
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_lepra_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_20_lepra,";
        $query_subida_riesgo_poblacion.="campo_117_tratamiento_para_lepra";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[20]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[117]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_lepra_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //RIESGO JOVEN
    $sube_a_tabla=false;
    $campo_72_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[72],"1900-12-31");
    if($campo_72_es_fecha_calendario<0
       || $array_registro_campos4505[72]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_joven_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="campo_72_consulta_de_joven_primera_vez";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[72]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_joven_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if se cumple sube a tabla
    
    //VACUNACION
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[35]=="1"
            || $array_registro_campos4505[36]=="1"
            || $array_registro_campos4505[43]=="1"
            || $array_registro_campos4505[44]=="1"
            || $array_registro_campos4505[37]=="1"
            || $array_registro_campos4505[37]=="2"
            || $array_registro_campos4505[37]=="3"
            || $array_registro_campos4505[41]=="1"
            || $array_registro_campos4505[41]=="2"
            || $array_registro_campos4505[41]=="3"
            || $array_registro_campos4505[42]=="1"
            || $array_registro_campos4505[42]=="2"
            || $array_registro_campos4505[42]=="3"
            || $array_registro_campos4505[46]=="1"
            || $array_registro_campos4505[46]=="2"
            || $array_registro_campos4505[46]=="3"
            || $array_registro_campos4505[40]=="1"
            || $array_registro_campos4505[40]=="2"
            || $array_registro_campos4505[45]=="1"
            || $array_registro_campos4505[45]=="2"
            || $array_registro_campos4505[38]=="1"
            || $array_registro_campos4505[38]=="2"
            || $array_registro_campos4505[38]=="3"
            || $array_registro_campos4505[38]=="4"
            || $array_registro_campos4505[38]=="5"
            || $array_registro_campos4505[47]=="1"
            || $array_registro_campos4505[47]=="2"
            || $array_registro_campos4505[47]=="3"
            || $array_registro_campos4505[47]=="4"
            || $array_registro_campos4505[47]=="5"
            || $array_registro_campos4505[39]=="4"
            || $array_registro_campos4505[39]=="5"
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_vacunacion_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_35_bcg,";
        $query_subida_riesgo_poblacion.="campo_36_hepatitis_b_menores_de_un_ano,";
        $query_subida_riesgo_poblacion.="campo_37_pentavalente,";
        $query_subida_riesgo_poblacion.="campo_38_polio,";
        $query_subida_riesgo_poblacion.="campo_39_dpt_menores_5_anos,";
        $query_subida_riesgo_poblacion.="campo_40_rotavirus,";
        $query_subida_riesgo_poblacion.="campo_41_neumococo,";
        $query_subida_riesgo_poblacion.="campo_42_influenza_ninos,";
        $query_subida_riesgo_poblacion.="campo_43_fiebre_amarilla_ninos_1_ano,";
        $query_subida_riesgo_poblacion.="campo_44_hepatitis_a,";
        $query_subida_riesgo_poblacion.="campo_45_triple_viral_ninos,";
        $query_subida_riesgo_poblacion.="campo_46_virus_papiloma_humano,";
        $query_subida_riesgo_poblacion.="campo_47_td_o_tt_mujeres_en_edad_fertil";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[35]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[36]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[37]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[38]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[39]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[40]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[41]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[42]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[43]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[44]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[45]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[46]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[47]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_vacunacion_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //CANCER DE CERVIX
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[26]=="1"            
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    else if($array_registro_campos4505[88]=="4"
            || $array_registro_campos4505[88]=="5"
            || $array_registro_campos4505[88]=="6"
            || $array_registro_campos4505[88]=="10"
            || $array_registro_campos4505[88]=="11"
            || $array_registro_campos4505[88]=="12"
            || $array_registro_campos4505[88]=="13"
            || $array_registro_campos4505[88]=="14"
            || $array_registro_campos4505[88]=="15"
            || $array_registro_campos4505[88]=="16" 
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_cancer_cervix_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_26_cancer_de_cervix,";
        $query_subida_riesgo_poblacion.="campo_86_tamizaje_cancer_de_cuello_uterino,";
        $query_subida_riesgo_poblacion.="campo_87_citologia_cervico_uterina,";
        $query_subida_riesgo_poblacion.="campo_88_citologia_cervico_uterina_resultados_segun_bethesda,";
        $query_subida_riesgo_poblacion.="campo_89_calidad_en_la_muestra_de_citologia_cervicouterina,";
        $query_subida_riesgo_poblacion.="campo_90_codigo_de_habilitacion_ips_donde_se_toma_citologia_cervicouterina,";
        $query_subida_riesgo_poblacion.="campo_91_fecha_colposcopia,";
        $query_subida_riesgo_poblacion.="campo_92_codigo_de_habilitacion_ips_donde_se_toma_colposcopia,";
        $query_subida_riesgo_poblacion.="campo_93_fecha_biopsia_cervical,";
        $query_subida_riesgo_poblacion.="campo_94_resultado_de_biopsia_cervical,";
        $query_subida_riesgo_poblacion.="campo_95_codigo_de_habilitacion_ips_donde_se_toma_biopsia_cervical";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[26]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[86]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[87]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[88]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[89]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[90]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[91]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[92]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[93]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[94]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[95]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_cancer_cervix_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //OBESIDAD DESNUTRICION
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[21]=="1"
            || $array_registro_campos4505[21]=="2"
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_obesidad_desnutricion_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_21_obesidad_o_desnutricion_proteico_calorica,";
        $query_subida_riesgo_poblacion.="campo_29_fecha_del_peso,";
        $query_subida_riesgo_poblacion.="campo_30_peso_en_kilogramos,";
        $query_subida_riesgo_poblacion.="campo_31_fecha_de_la_talla,";
        $query_subida_riesgo_poblacion.="campo_32_talla_en_centimetros,";
        $query_subida_riesgo_poblacion.="campo_64_fecha_diagnostico_desnutricion_proteico_calorica,";
        $query_subida_riesgo_poblacion.="campo_67_consulta_nutricion";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[21]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[29]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[30]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[31]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[32]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[64]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[67]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_obesidad_desnutricion_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //GESTACION
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[14]=="1"            
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_gestacion_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_14_gestacion,";
        $query_subida_riesgo_poblacion.="campo_15_sifilis_gestacional_o_congenita,";
        $query_subida_riesgo_poblacion.="campo_16_hipertension_inducida_por_la_gestacion,";
        $query_subida_riesgo_poblacion.="campo_33_fecha_probable_de_parto,";
        $query_subida_riesgo_poblacion.="campo_49_fecha_atencion_parto_o_cesarea,";
        $query_subida_riesgo_poblacion.="campo_50_fecha_salida_de_la_atencion_del_parto_o_cesarea,";
        $query_subida_riesgo_poblacion.="campo_51_fecha_de_consejeria_en_lactancia_materna,";
        $query_subida_riesgo_poblacion.="campo_56_control_prenatal_de_primera_vez,";
        $query_subida_riesgo_poblacion.="campo_57_control_prenatal,";
        $query_subida_riesgo_poblacion.="campo_58_ultimo_control_prenatal,";
        $query_subida_riesgo_poblacion.="campo_59_suministro_de_acido_folico_en_el_ultimo_control_prenatal,";
        $query_subida_riesgo_poblacion.="campo_60_suministro_de_sulfato_ferroso_en_el_ultimo_control_prenatal,";
        $query_subida_riesgo_poblacion.="campo_61_suministro_de_carbonato_de_calcio_en_el_ultimo_control_prenatal,";
        $query_subida_riesgo_poblacion.="campo_75_asesoria_pre_test_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_76_asesoria_pos_test_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_78_fecha_antigeno_de_superficie_hepatitis_b_en_gestantes,";        
        $query_subida_riesgo_poblacion.="campo_79_resultado_antigeno_de_superficie_hepatitis_b_en_gestantes,";
        $query_subida_riesgo_poblacion.="campo_82_fecha_de_toma_de_elisa_para_vih,";
        $query_subida_riesgo_poblacion.="campo_83_resultado_elisa_para_vih";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[14]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[15]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[16]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[33]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[49]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[50]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[51]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[56]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[57]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[58]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[59]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[60]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[61]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[75]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[76]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[78]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[79]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[82]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[83]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_gestacion_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //MALTRATO
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[22]=="1"            
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_victima_maltrato_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_22_victima_de_maltrato,";
        $query_subida_riesgo_poblacion.="campo_65_consulta_mujer_o_menor_victima_del_maltrato";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[22]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[65]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_victima_maltrato_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //VIOLENCIA SEXUAL
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[23]=="1"            
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_violencia_sexual_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_23_victima_de_violencia_sexual,";
        $query_subida_riesgo_poblacion.="campo_66_consulta_victimas_de_violencia_sexual";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[23]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[66]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_violencia_sexual_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //RIESGO MENOR DE 10
    $sube_a_tabla=false;
    $campo_69_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[69],"1900-12-31");
    if($campo_69_es_fecha_calendario<0
       || $array_registro_campos4505[69]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_menor_10anos_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_69_consulta_de_crecimiento_y_desarrollo_primera_vez,";
        $query_subida_riesgo_poblacion.="campo_70_suministro_de_sulfato_ferroso_en_la_ultima_consulta_del_menor_de_10_anos,";
        $query_subida_riesgo_poblacion.="campo_71_suministro_de_vitamina_a_en_la_ultima_consulta_del_menor_de_10_anos";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[69]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[70]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[71]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_menor_10anos_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //ODONTOLOGIA
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[28]=="1"            
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_odontologico_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_28_fluorosis_dental,";
        $query_subida_riesgo_poblacion.="campo_48_control_de_placa_bacteriana";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[28]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[48]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_odontologico_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    
    //SINTOMATICO RESPIRATORIO
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[18]=="1"            
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_sintomatico_respiratorio_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_18_sintomatico_respiratorio,";
        $query_subida_riesgo_poblacion.="campo_19_tuberculosis_multidrogoresistente,";
        $query_subida_riesgo_poblacion.="campo_112_fecha_toma_de_baciloscopia_de_diagnostico,";
        $query_subida_riesgo_poblacion.="campo_113_baciloscopia_de_diagnostico";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[18]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[19]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[112]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[113]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_sintomatico_respiratorio_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //EDAD GESTACIONAL
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[34]!="0"
       && $array_registro_campos4505[34]!="999"
            )
    {
        $sube_a_tabla=true;        
        
    }//fin else if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_edad_gestacional_nacer_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_34_edad_gestacional_al_nacer";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[34]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_edad_gestacional_nacer_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    
    //LEISHMANIASIS
    $sube_a_tabla=false;
    
    $campo_118_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[118],"1900-12-31");
    if($campo_118_es_fecha_calendario<0
       || $array_registro_campos4505[118]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_enfermedad_leishmaniasis_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_118_fecha_de_terminacion_tratamiento_para_leishmaniasis";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[118]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_enfermedad_leishmaniasis_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //ENFERMEDAD RENAL
    $sube_a_tabla=false;
    
    $campo_106_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[106],"1900-12-31");
    if($campo_106_es_fecha_calendario<0
       || $array_registro_campos4505[106]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_enfermedad_renal_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_106_fecha_creatinina,";
        $query_subida_riesgo_poblacion.="campo_107_creatinina,";
        $query_subida_riesgo_poblacion.="campo_110_fecha_toma_de_microalbuminuria";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[106]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[107]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[110]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_enfermedad_renal_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //RECIEN NACIDO
    $sube_a_tabla=false;
    
    $campo_52_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[52],"1900-12-31");
    if($campo_52_es_fecha_calendario<0
       || $array_registro_campos4505[52]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_control_recien_nacido_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_52_control_recien_nacido";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[52]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_control_recien_nacido_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //ANEMIA
    $sube_a_tabla=false;
    
    $campo_103_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[103],"1900-12-31");
    if($campo_103_es_fecha_calendario<0
       || $array_registro_campos4505[103]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_enfermedad_anemica_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_103_fecha_toma_de_hemoglobina,";
        $query_subida_riesgo_poblacion.="campo_104_hemoglobina";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[103]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[104]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_enfermedad_anemica_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //PROBLEMAS DE VISION
    $sube_a_tabla=false;
    
    $campo_62_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[62],"1900-12-31");
    if($campo_62_es_fecha_calendario<0
       || $array_registro_campos4505[62]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_problemas_vision_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_62_valoracion_de_la_agudeza_visual,";
        $query_subida_riesgo_poblacion.="campo_63_consulta_por_oftalmologia";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[62]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[63]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_problemas_vision_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    
    //PLANIFICACION FAMILIAR
    $sube_a_tabla=false;
    
    $campo_53_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[53],"1900-12-31");
    if($campo_53_es_fecha_calendario<0
       || $array_registro_campos4505[53]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_planificacion_familiar_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_53_planificacion_familiar_primera_vez,";        
        $query_subida_riesgo_poblacion.="campo_54_suministro_de_metodo_anticonceptivo,";
        $query_subida_riesgo_poblacion.="campo_55_fecha_suministro_de_metodo_anticonceptivo";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[53]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[54]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[55]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_planificacion_familiar_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    //DIABETES    
    $sube_a_tabla=false;
    
    $campo_108_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[108],"1900-12-31");
    if($campo_108_es_fecha_calendario<0
       || $array_registro_campos4505[108]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_enfermedad_diabetica_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_105_fecha_de_la_toma_de_glicemia_basal,";        
        $query_subida_riesgo_poblacion.="campo_108_fecha_hemoglobina_glicosilada,";
        $query_subida_riesgo_poblacion.="campo_109_hemoglobina_glicosilada";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[105]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[108]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[109]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_enfermedad_diabetica_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    
    //HIPOTIROIDISMO CONGENITO
    $sube_a_tabla=false;
    
    if($array_registro_campos4505[17]=="1"
       )
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_hipotiroidismo_congenito_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_17_hipotiroidismo_congenito,";
        $query_subida_riesgo_poblacion.="campo_84_fecha_tsh_neonatal,";        
        $query_subida_riesgo_poblacion.="campo_85_resultado_de_tsh_neonatal,";
        $query_subida_riesgo_poblacion.="campo_114_tratamiento_para_hipotiroidismo_congenito";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[17]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[84]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[85]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[114]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_hipotiroidismo_congenito_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
    
    //COLESTEROL
    $sube_a_tabla=false;
    
    $campo_111_es_fecha_calendario=diferencia_dias_entre_fechas($array_registro_campos4505[111],"1900-12-31");
    if($campo_111_es_fecha_calendario<0
       || $array_registro_campos4505[111]=="1800-01-01")
    {
        $sube_a_tabla=true;        
        
    }//fin if
    
    if($sube_a_tabla==true)
    {
        $query_subida_riesgo_poblacion="";
        $query_subida_riesgo_poblacion.="INSERT INTO gioss_poblacion_riesgo_enfermedad_colesterol_res4505_pyp ";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="fecha_de_corte,";
        $query_subida_riesgo_poblacion.="fecha_validacion,";
        $query_subida_riesgo_poblacion.="hora_validacion,";
        $query_subida_riesgo_poblacion.="tipo_de_identificacion_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_subida_riesgo_poblacion.="cod_prestador_servicios_salud,";
        $query_subida_riesgo_poblacion.="codigo_eapb,";
        $query_subida_riesgo_poblacion.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_subida_riesgo_poblacion.="consecutivo_de_archivo,";
        $query_subida_riesgo_poblacion.="numero_fila,";
        $query_subida_riesgo_poblacion.="nombre_archivo,";
        $query_subida_riesgo_poblacion.="numero_de_secuencia,";
        $query_subida_riesgo_poblacion.="campo_0_tipo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_1_consecutivo_de_registro,";
        $query_subida_riesgo_poblacion.="campo_2_codigo_de_habilitacion_ips_primaria,";
        $query_subida_riesgo_poblacion.="campo_3_tipo_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_4_numero_de_identificacion_afiliado,";
        $query_subida_riesgo_poblacion.="campo_5_primer_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_6_segundo_apellido_afiliado,";
        $query_subida_riesgo_poblacion.="campo_7_primer_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_8_segundo_nombre_afiliado,";
        $query_subida_riesgo_poblacion.="campo_9_fecha_nacimiento,";
        $query_subida_riesgo_poblacion.="campo_10_sexo,";
        $query_subida_riesgo_poblacion.="campo_11_pertenencia_etnica,";
        $query_subida_riesgo_poblacion.="campo_12_codigo_ocupacion,";
        $query_subida_riesgo_poblacion.="campo_13_nivel_educativo,";
        //campos correspondientes al grupo        
        $query_subida_riesgo_poblacion.="campo_111_fecha_toma_de_hdl";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.="VALUES";
        $query_subida_riesgo_poblacion.="(";
        $query_subida_riesgo_poblacion.="'$fecha_corte_bd',";
        $query_subida_riesgo_poblacion.="'$fecha_actual',";
        $query_subida_riesgo_poblacion.="'$tiempo_actual',";
        $query_subida_riesgo_poblacion.="'$tipo_id_prestador',";
        $query_subida_riesgo_poblacion.="'$nit_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_habilitacion_prestador',";
        $query_subida_riesgo_poblacion.="'$cod_eapb',";
        $query_subida_riesgo_poblacion.="'$regimen',";
        $query_subida_riesgo_poblacion.="'$consecutivo_del_archivo',";
        $query_subida_riesgo_poblacion.="'$numero_fila',";
        $query_subida_riesgo_poblacion.="'$nombre_archivo_para_zip',";
        $query_subida_riesgo_poblacion.="'$numero_secuencia',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[0]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[1]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[2]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[3]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[4]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[5]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[6]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[7]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[8]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[9]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[10]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[11]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[12]."',";
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[13]."',";
        //campos correspondientes al grupo
        $query_subida_riesgo_poblacion.="'".$array_registro_campos4505[111]."'";
        $query_subida_riesgo_poblacion.=")";
        $query_subida_riesgo_poblacion.=";";
        $query_subida_riesgo_poblacion.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionbd->insertar_no_warning_get_error_no_crea_cierra($query_subida_riesgo_poblacion, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensaje_proceso.="error al subir gioss_poblacion_riesgo_enfermedad_colesterol_res4505_pyp <br>";
                
                echo $error_bd_seq."<br>";
        }
    }//fin if
    
}//fin funcion subir_a_tablas_poblacion_riesgo

?>