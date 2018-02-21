<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

function alphanumericAndSpace3( $string )
{
    $cadena = str_replace("ñ","n",$string);//aca debe ser $string o la cadena estara vacia (nota obvia por si acaso :P)
    $cadena = str_replace("Ñ","N",$cadena);
    $cadena = str_replace("á","a",$cadena);
    $cadena = str_replace("é","e",$cadena);
    $cadena = str_replace("í","i",$cadena);
    $cadena = str_replace("ó","o",$cadena);
    $cadena = str_replace("ú","u",$cadena);
    $cadena = str_replace("Á","A",$cadena);
    $cadena = str_replace("É","E",$cadena);
    $cadena = str_replace("Í","I",$cadena);
    $cadena = str_replace("Ó","O",$cadena);
    $cadena = str_replace("Ú","U",$cadena);
    $cadena = str_replace(array("\n\r", "\r\n", "\r", "\n"), '', $cadena);
    $cadena = str_replace(PHP_EOL, null, $cadena);
    $cadena = preg_replace('/[^a-zA-Z0-9\s,\-\.]/', '', trim($cadena));
    //$cadena = substr($cadena,0,10);
    return $cadena;
}

if(isset($_REQUEST["c1"])
   && isset($_REQUEST["c2"])
   && isset($_REQUEST["c3"])
   && isset($_REQUEST["c4"])
   && isset($_REQUEST["c5"])
   && isset($_REQUEST["c6"])
   && isset($_REQUEST["c7"])
   && isset($_REQUEST["c8"])
   && isset($_REQUEST["c9"])
   && isset($_REQUEST["c10"])
   && $_REQUEST["c1"]!=""
   && $_REQUEST["c2"]!=""
   && $_REQUEST["c3"]!=""
   && $_REQUEST["c4"]!=""
   && $_REQUEST["c5"]!=""
   && $_REQUEST["c6"]!=""
   && $_REQUEST["c7"]!=""
   && $_REQUEST["c8"]!=""
   && $_REQUEST["c9"]!=""
   && $_REQUEST["c10"]!=""
   )
{
    
    
    
    $cod_prestador_servicios_salud=$_REQUEST["c1"];
    $codigo_eapb=$_REQUEST["c2"];
    $numero_de_identificacion_de_la_entidad_reportadora=$_REQUEST["c3"];
    $numero_de_secuencia=$_REQUEST["c4"];
    $fecha_de_corte=$_REQUEST["c5"];
    $tipo_de_identificacion_entidad_reportadora=$_REQUEST["c6"];
    $tipo_de_regimen_de_la_informacion_reportada=$_REQUEST["c7"];
    $fecha_validacion=$_REQUEST["c8"];
    $hora_validacion=$_REQUEST["c9"];
    $nombre_archivo=$_REQUEST["c10"];
    
    $tiempo_actual_subida = date('H:i:s');
    
    $conexionBD = new conexion();
    $conexionBD->crearConexion();
    
    
    
    $total_errores_bd="";
    
    //PARTE CONSULTA ESTADO SUBIDA
    $esta_en_ejecucion=false;
    
    $query_select_estado_subida="";
    $query_select_estado_subida.="SELECT estado_subida,mensaje_progreso FROM gioss_poblacion_riesgo_progreso_subida_tablas ";
    $query_select_estado_subida.=" WHERE ";
    $query_select_estado_subida.=" fecha_de_corte='$fecha_de_corte' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" tipo_de_identificacion_entidad_reportadora='$tipo_de_identificacion_entidad_reportadora' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" numero_de_identificacion_de_la_entidad_reportadora='$numero_de_identificacion_de_la_entidad_reportadora' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" cod_prestador_servicios_salud='$cod_prestador_servicios_salud' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" codigo_eapb='$codigo_eapb' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" nombre_archivo='$nombre_archivo' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" numero_de_secuencia='$numero_de_secuencia' ";
    $query_select_estado_subida.=";";
    $query_select_estado_subida.="";
    $error_bd_seq="";
    $resultado_estado_subida=$conexionBD->consultar_no_warning_get_error_no_crea_cierra($query_select_estado_subida, $error_bd_seq);
    if($error_bd_seq!="")
    {
        $total_errores_bd=" error consultar estado\n";
    }
    
    if(is_array($resultado_estado_subida) && count($resultado_estado_subida)>0)
    {
        foreach($resultado_estado_subida as $res_estado_subida)
        {
            $estado_subida_bd=$res_estado_subida["estado_subida"];
            $mensaje_progreso_bd=$res_estado_subida["mensaje_progreso"];
            
            if($estado_subida_bd=="EJECUTANDO")
            {
                $esta_en_ejecucion=true;
                break;
            }
        }//fin foreach
    }
    //FIN PARTE CONSULTA ESTADO SUBIDA 
    
    if($esta_en_ejecucion==false)//solo empieza a subir si no esta en ejecucion actualmente
    {
        //PARTE INSERTA ESTADO SUBIDA 
        $query_insert_estado_subida="";
        $query_insert_estado_subida.=" INSERT INTO gioss_poblacion_riesgo_progreso_subida_tablas ";
        $query_insert_estado_subida.="(";
        $query_insert_estado_subida.="fecha_de_corte,";
        $query_insert_estado_subida.="fecha_validacion,";
        $query_insert_estado_subida.="hora_validacion,";
        $query_insert_estado_subida.="tipo_de_identificacion_entidad_reportadora,";
        $query_insert_estado_subida.="numero_de_identificacion_de_la_entidad_reportadora,";
        $query_insert_estado_subida.="cod_prestador_servicios_salud,";
        $query_insert_estado_subida.="codigo_eapb,";
        $query_insert_estado_subida.="tipo_de_regimen_de_la_informacion_reportada,";
        $query_insert_estado_subida.="nombre_archivo,";
        $query_insert_estado_subida.="numero_de_secuencia,";    
        $query_insert_estado_subida.="estado_subida,";
        $query_insert_estado_subida.="mensaje_progreso";
        $query_insert_estado_subida.=")";
        $query_insert_estado_subida.="VALUES";
        $query_insert_estado_subida.="(";
        $query_insert_estado_subida.="'$fecha_de_corte',";
        $query_insert_estado_subida.="'$fecha_validacion',";
        $query_insert_estado_subida.="'$hora_validacion',";
        $query_insert_estado_subida.="'$tipo_de_identificacion_entidad_reportadora',";
        $query_insert_estado_subida.="'$numero_de_identificacion_de_la_entidad_reportadora',";
        $query_insert_estado_subida.="'$cod_prestador_servicios_salud',";
        $query_insert_estado_subida.="'$codigo_eapb',";
        $query_insert_estado_subida.="'$tipo_de_regimen_de_la_informacion_reportada',";
        $query_insert_estado_subida.="'$nombre_archivo',";
        $query_insert_estado_subida.="'$numero_de_secuencia',";
        $query_insert_estado_subida.="'EJECUTANDO',";
        $query_insert_estado_subida.="'Se inicio la subida a las tablas de riesgo poblacion a las $tiempo_actual_subida .'";
        $query_insert_estado_subida.=")";
        $query_insert_estado_subida.=";";
        $query_insert_estado_subida.="";
        $bool_hubo_error_query=false;
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionBD->insertar_no_warning_get_error_no_crea_cierra($query_insert_estado_subida, $error_bd_seq);
        if($error_bd_seq!="")
        {
            $total_errores_bd=" error insertar estado\n";
        }//fin if
        //FIN PARTE INSERTA ESTADO SUBIDA 
        
        //PARTE UPDATE ESTADO SUBIDA TERMINA
        $query_update_estado_subida="";
        $query_update_estado_subida.="UPDATE gioss_poblacion_riesgo_progreso_subida_tablas ";
        $query_update_estado_subida.=" SET estado_subida='TERMINADO', ";
        $query_update_estado_subida.="  mensaje_progreso='Ha finalizado el proceso de subida alas tablas de reisgo poblacion.' ";
        $query_update_estado_subida.=" WHERE ";
        $query_update_estado_subida.=" fecha_de_corte='$fecha_de_corte' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" fecha_validacion='$fecha_validacion' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" hora_validacion='$hora_validacion' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" tipo_de_identificacion_entidad_reportadora='$tipo_de_identificacion_entidad_reportadora' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" numero_de_identificacion_de_la_entidad_reportadora='$numero_de_identificacion_de_la_entidad_reportadora' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" cod_prestador_servicios_salud='$cod_prestador_servicios_salud' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" codigo_eapb='$codigo_eapb' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" nombre_archivo='$nombre_archivo' ";
        $query_update_estado_subida.=" AND ";
        $query_update_estado_subida.=" numero_de_secuencia='$numero_de_secuencia' ";
        $query_update_estado_subida.=";";
        $query_update_estado_subida.="";
        $error_bd_seq="";
        $bool_hubo_error_query=$conexionBD->insertar_no_warning_get_error_no_crea_cierra($query_update_estado_subida, $error_bd_seq);
        if($error_bd_seq!="")
        {
            $total_errores_bd=" error insertar estado\n";
        }//fin if
        //FIN PARTE UPDATE ESTADO SUBIDA TERMINA
        
    }//solo empieza a subir si no esta en ejecucion actualmente
    else
    {
        echo "Ya se esta subiendo a las tablas de riesgo poblacion.\n";
    }
    
    if($total_errores_bd!="")
    {
        echo $total_errores_bd;
    }
    else
    {
        echo "Se ha terminado el proceso de subida.";
    }
    
    $conexionBD->cerrar_conexion();
}//fin if
else
{
    $error_mensaje="";
    $error_mensaje.="Falta informaci&oacuten<br>";
    
    $cont=1;
    while($cont<=10)
    {
        if(!isset($_REQUEST["c".$cont]))
        {
            $error_mensaje.="c".$cont." no existe<br>";
        }
        else if($_REQUEST["c".$cont]=="")
        {
            $error_mensaje.="c".$cont." esta vacio<br>";
        }
        else
        {
            $error_mensaje.="c".$cont." tiene como valor ".$_REQUEST["c".$cont]."<br>";
        }
        $cont++;
    }//fin while
    
    
    
    echo $error_mensaje;
}


?>