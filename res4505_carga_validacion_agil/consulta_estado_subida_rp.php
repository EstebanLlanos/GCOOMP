<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');

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
    
    $conexionBD = new conexion();
    $conexionBD->crearConexion();
    
    
    $total_errores_bd="";
    
    $query_select_estado_subida="";
    $query_select_estado_subida.="SELECT * FROM gioss_poblacion_riesgo_progreso_subida_tablas ";
    $query_select_estado_subida.=" WHERE ";
    $query_select_estado_subida.=" fecha_de_corte='$fecha_de_corte' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" fecha_validacion='$fecha_validacion' ";
    $query_select_estado_subida.=" AND ";
    $query_select_estado_subida.=" hora_validacion='$hora_validacion' ";
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
    
    $estado_subida_bd="ESPERE";
    $mensaje_progreso_bd="Se esta procesando la informacion.";
    
    if(is_array($resultado_estado_subida) && count($resultado_estado_subida)>0)
    {
        $estado_subida_bd=$resultado_estado_subida[0]["estado_subida"];
        $mensaje_progreso_bd=$resultado_estado_subida[0]["mensaje_progreso"];        
    }
    
    $html_para_div="";
    
    $html_para_div.="";
    $html_para_div.=$mensaje_progreso_bd." Estado: ".$estado_subida_bd;
    $html_para_div.="<input type='hidden' id='estado_subida_hidden' value='$estado_subida_bd' />";
            
    
    echo "<span style='color:red;text-align:center;'><b>".$html_para_div."</b></span>";
    
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