<?php
set_time_limit(2592000);
ini_set('max_execution_time', 2592000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');

function reparacion_campos_duplicados($tipo_id_duplicado_actual,$numero_id_duplicado_actual,
                                      $numero_duplicados_de_duplicado,
                                      $nombre_vista_duplicados_del_duplicado_actual,
                                      &$numero_registro_para_procesado,
                                      &$cod_prestador_para_procesado,
                                      &$bool_fueron_procesados_duplicados_en_un_registro,
                                      &$contador_offset_duplicados,
                                      &$mensajes_error_bd,
                                      &$conexionbd)
{
    //$conexionbd = new conexion();
    
    $arreglo_campos_reparados=array();
    $cont_campos_reparados_pre_fix=0;
    while($cont_campos_reparados_pre_fix<119)
    {
        $arreglo_campos_reparados[$cont_campos_reparados_pre_fix]="NO_VALOR_1024488856";
        $cont_campos_reparados_pre_fix++;
    }//fin while dejando listas posiciones delos 119 campos desde 0 a 118
    
    //seleccion de numero secuencia 
    $query_condicion_numero_secuencia="";
    $query_condicion_numero_secuencia.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_condicion_numero_secuencia.=" (numero_registro = (select max(numero_registro) from $nombre_vista_duplicados_del_duplicado_actual)) ";
    $error_bd_seq="";
    $resultados_condicion_numero_secuencia=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_condicion_numero_secuencia, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar el numero de secuencia y registro: ".$error_bd_seq."<br>";
    }
    if(count($resultados_condicion_numero_secuencia)>0 && is_array($resultados_condicion_numero_secuencia))
    {
        $numero_registro_para_procesado=$resultados_condicion_numero_secuencia[0]["numero_registro"];
    }
    //fin seleccion secuencia 
    
    
    
    //CRITERIOS NUEVOS 14 09 2015
    //nombre para campo en bd 
    $prefijo_nombre_campo_en_bd="campo_";
    
    //FRECUENCIA GENERO DEL DUPLICADO VERIFICANDO CAMPO 10
    $string_numero_campo_actual="10";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    $cont_masculino=0;
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT count(*) as contador_genero FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='M' ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar frecuencia genero masculino<br>";
    }
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $cont_masculino=intval($resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["contador_genero"]);
    }//fin if
    $cont_femenino=0;
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT count(*) as contador_genero FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='F' ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar frecuencia genero masculino<br>";
    }
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $cont_femenino=intval($resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["contador_genero"]);
    }//fin if
    
    $genero_definitivo="A";
    if($cont_femenino>$cont_masculino)
    {
        $genero_definitivo="F";
    }
    else if($cont_femenino<$cont_masculino)
    {
        $genero_definitivo="M";
    }
    //FIN FRECUENCIA GENERO DEL DUPLICADO VERIFICANDO CAMPO 10
    
    
    //CAMPO 9
    $string_numero_campo_actual="9";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=")) ";
    if($genero_definitivo!="A")
    {
        $query_criterio_campo_actual.=" AND ";
        $query_criterio_campo_actual.=" (trim(".$prefijo_nombre_campo_en_bd."10)='$genero_definitivo') ";
    }
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        
        //dependientes        
        $arreglo_campos_reparados[5]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."5"];
        $arreglo_campos_reparados[6]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."6"];
        $arreglo_campos_reparados[7]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."7"];
        $arreglo_campos_reparados[8]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."8"];
        $arreglo_campos_reparados[10]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."10"];
        

    }//fin if se cumplio el criterio
    else
    {
        
    }//fin else
    //fin criterios    
    //FIN CAMPO 9
    
    //CAMPO 2
    $string_numero_campo_actual="2";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  busca la fecha mas actual diferente de 999 o 0, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 cuando  es igual a 999 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 2
    
    /*
    
    
    
    //CAMPO 5
    $string_numero_campo_actual="5";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'NONE' ";
    $query_criterio_campo_actual.=" AND trim($nombre_campo_actual)<>'' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='NONE' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" (trim($nombre_campo_actual)='' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 5
    
    //CAMPO 6
    $string_numero_campo_actual="6";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'NONE' ";
    $query_criterio_campo_actual.=" AND trim($nombre_campo_actual)<>'' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='NONE' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" (trim($nombre_campo_actual)='' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 6
    
    //CAMPO 7
    $string_numero_campo_actual="7";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'NONE' ";
    $query_criterio_campo_actual.=" AND trim($nombre_campo_actual)<>'' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='NONE' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" (trim($nombre_campo_actual)='' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 7
    
    //CAMPO 8
    $string_numero_campo_actual="8";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'NONE' ";
    $query_criterio_campo_actual.=" AND trim($nombre_campo_actual)<>'' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='NONE' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" (trim($nombre_campo_actual)='' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 8
    
    //CAMPO 10
    $string_numero_campo_actual="10";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'NONE' ";
    $query_criterio_campo_actual.=" AND trim($nombre_campo_actual)<>'' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='NONE' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" (trim($nombre_campo_actual)='' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 10
    
    
    */
    
    //CAMPO 29
    $string_numero_campo_actual="29";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01')";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 30
        $arreglo_campos_reparados[30]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."30"];

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 30
            $arreglo_campos_reparados[30]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."30"];
    
        }//fin if se cumplio el criterio
        else
        {
           
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 29
    
    //CAMPO 31
    $string_numero_campo_actual="31";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01')";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 32
        $arreglo_campos_reparados[32]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."32"];

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 32
            $arreglo_campos_reparados[32]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."32"];
            
    
        }//fin if se cumplio el criterio
        else
        {
           
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 31
    
    //CAMPO 33
    $string_numero_campo_actual="33";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 33
    
    //CAMPO 49
    $string_numero_campo_actual="49";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 49
    
    //CAMPO 50
    $string_numero_campo_actual="50";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 50
    
    //CAMPO 55
    $string_numero_campo_actual="55";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 55
    
    //CAMPO 58
    $string_numero_campo_actual="58";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 58
    
    //CAMPO 64
    $string_numero_campo_actual="64";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 64
    
    //CAMPO 100
    $string_numero_campo_actual="100";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 101
        $arreglo_campos_reparados[101]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."101"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
            //dependiente 101
            $arreglo_campos_reparados[101]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."101"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1845-01-01 y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
                //dependiente 101
                $arreglo_campos_reparados[101]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."101"];

        
            }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 100
    
    //CAMPO 51
    $string_numero_campo_actual="51";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 51
    
    //CAMPO 52
    $string_numero_campo_actual="52";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 52
    
    //CAMPO 53
    $string_numero_campo_actual="53";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 53
    
    //CAMPO 56
    $string_numero_campo_actual="56";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 56
    
    //CAMPO 62
    $string_numero_campo_actual="62";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 62
    
    //CAMPO 63
    $string_numero_campo_actual="63";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 63
    
    //CAMPO 65
    $string_numero_campo_actual="65";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 65
    
    //CAMPO 66
    $string_numero_campo_actual="66";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 66
    
    //CAMPO 67
    $string_numero_campo_actual="67";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 67
    
    //CAMPO 68
    $string_numero_campo_actual="68";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 68
    
    //CAMPO 69
    $string_numero_campo_actual="69";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 69
    
    //CAMPO 72
    $string_numero_campo_actual="72";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 72
    
    //CAMPO 73
    $string_numero_campo_actual="73";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 73
    
    //CAMPO 75
    $string_numero_campo_actual="75";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 75
    
    //CAMPO 76
    $string_numero_campo_actual="76";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 76
    
    //CAMPO 78
    $string_numero_campo_actual="78";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 79
        $arreglo_campos_reparados[79]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."79"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 79
            $arreglo_campos_reparados[79]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."79"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 79
                $arreglo_campos_reparados[79]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."79"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 79
                    $arreglo_campos_reparados[79]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."79"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 78
    
    //CAMPO 80
    $string_numero_campo_actual="80";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 81
        $arreglo_campos_reparados[81]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."81"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 81
            $arreglo_campos_reparados[81]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."81"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 81
                $arreglo_campos_reparados[81]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."81"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 81
                    $arreglo_campos_reparados[81]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."81"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 80
    
    //CAMPO 82
    $string_numero_campo_actual="82";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 83
        $arreglo_campos_reparados[83]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."83"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 83
            $arreglo_campos_reparados[83]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."83"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 83
                $arreglo_campos_reparados[83]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."83"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 83
                    $arreglo_campos_reparados[83]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."83"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 82
    
    //CAMPO 84
    $string_numero_campo_actual="84";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 85
        $arreglo_campos_reparados[85]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."85"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 85
            $arreglo_campos_reparados[85]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."85"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 85
                $arreglo_campos_reparados[85]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."85"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 85
                    $arreglo_campos_reparados[85]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."85"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 84
    
    //CAMPO 87
    $string_numero_campo_actual="87";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 88
        $arreglo_campos_reparados[88]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."88"];
        //dependiente 89
        $arreglo_campos_reparados[89]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."89"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 88
            $arreglo_campos_reparados[88]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."88"];
            //dependiente 89
            $arreglo_campos_reparados[89]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."89"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 88
                $arreglo_campos_reparados[88]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."88"];
                //dependiente 89
                $arreglo_campos_reparados[89]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."89"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 88
                    $arreglo_campos_reparados[88]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."88"];
                    //dependiente 89
                    $arreglo_campos_reparados[89]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."89"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 87
    
    //CAMPO 91
    $string_numero_campo_actual="91";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 91
    
    //CAMPO 93
    $string_numero_campo_actual="93";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 94
        $arreglo_campos_reparados[94]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."94"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 94
            $arreglo_campos_reparados[94]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."94"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 94
                $arreglo_campos_reparados[94]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."94"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 94
                    $arreglo_campos_reparados[94]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."94"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 93
    
    //CAMPO 96
    $string_numero_campo_actual="96";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 97
        $arreglo_campos_reparados[97]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."97"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 97
            $arreglo_campos_reparados[97]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."97"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 97
                $arreglo_campos_reparados[97]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."97"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 97
                    $arreglo_campos_reparados[97]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."97"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 96
    
    //CAMPO 99
    $string_numero_campo_actual="99";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 99
    
    //CAMPO 103
    $string_numero_campo_actual="103";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 104
        $arreglo_campos_reparados[104]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."104"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 104
            $arreglo_campos_reparados[104]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."104"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 104
                $arreglo_campos_reparados[104]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."104"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 104
                    $arreglo_campos_reparados[104]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."104"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 103
    
    //CAMPO 105
    $string_numero_campo_actual="105";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 105
    
    //CAMPO 106
    $string_numero_campo_actual="106";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 107
        $arreglo_campos_reparados[107]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."107"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 107
            $arreglo_campos_reparados[107]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."107"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 107
                $arreglo_campos_reparados[107]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."107"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 107
                    $arreglo_campos_reparados[107]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."107"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 106
    
    //CAMPO 108
    $string_numero_campo_actual="108";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 109
        $arreglo_campos_reparados[109]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."109"];

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 109
            $arreglo_campos_reparados[109]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."109"];
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 109
                $arreglo_campos_reparados[109]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."109"];
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 109
                    $arreglo_campos_reparados[109]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."109"];
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 108
    
    //CAMPO 110
    $string_numero_campo_actual="110";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 110
    
    //CAMPO 111
    $string_numero_campo_actual="111";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 111
    
    //CAMPO 112
    $string_numero_campo_actual="112";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        //dependiente 113
        $arreglo_campos_reparados[113]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."113"];


    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            //dependiente 113
            $arreglo_campos_reparados[113]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."113"];

    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                //dependiente 113
                $arreglo_campos_reparados[113]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."113"];

        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    //dependiente 113
                    $arreglo_campos_reparados[113]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1][$prefijo_nombre_campo_en_bd."113"];

            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 112
    
    //CAMPO 118
    $string_numero_campo_actual="118";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1 y 2 busca la fecha mas actual diferente de 1800-01-01, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select max(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where ($nombre_campo_actual<>'1800-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1805-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1810-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1825-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1830-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1835-01-01' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'1845-01-01' )";
    $query_criterio_campo_actual.=")) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 y 2: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 3 cuando  es igual a 1800-01-01 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='1800-01-01' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
            //criterio 4 cuando  es igual a 1805-01-01 hasta 1835-01-01 selecciona el primer valor de este rango y trae el de ultimo registro
            $query_criterio_campo_actual="";
            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_criterio_campo_actual.=" (to_date($nombre_campo_actual,'yyyy-mm-dd') = (select min(to_date($nombre_campo_actual,'yyyy-mm-dd')) from $nombre_vista_duplicados_del_duplicado_actual ";
            $query_criterio_campo_actual.=" where ($nombre_campo_actual='1805-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1810-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1825-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1830-01-01' ";
            $query_criterio_campo_actual.=" OR $nombre_campo_actual='1835-01-01')";
            $query_criterio_campo_actual.=")) ";
            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                    $error_bd_seq="";
            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
            if($error_bd_seq!="")
            {
                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 4: ".$error_bd_seq."<br>";
            }
            //si se cumple criterio
            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
            {
                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                
        
            }//fin if se cumplio el criterio
            else
            {
                //criterio 5 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='1845-01-01' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 5: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
            }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 118
    
    //CAMPO 3
    $arreglo_campos_reparados[3]=$tipo_id_duplicado_actual;
    //FIN CAMPO 3
    
    //CAMPO 90
    $string_numero_campo_actual="90";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  busca la fecha mas actual diferente de 999 o 0, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 cuando  es igual a 999 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 90
    
    //CAMPO 92
    $string_numero_campo_actual="92";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  busca la fecha mas actual diferente de 999 o 0, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 cuando  es igual a 999 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 92
    
    //CAMPO 95
    $string_numero_campo_actual="95";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  busca la fecha mas actual diferente de 999 o 0, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 cuando  es igual a 999 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 95
    
    //CAMPO 98
    $string_numero_campo_actual="98";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  busca la fecha mas actual diferente de 999 o 0, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 cuando  es igual a 999 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 98
    
    //CAMPO 102
    $string_numero_campo_actual="102";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  busca la fecha mas actual diferente de 999 o 0, en caso de que sean fecha calendario iguales pone la del ultimo registro
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 cuando  es igual a 999 y trae el de ultimo registro
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 cuando  es igual a 1845-01-01 y trae el de ultimo registro
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 102
    
    
    
    
    
    //CAMPO 57
    $string_numero_campo_actual="57";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual<>'999' AND $nombre_campo_actual<>'0' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 57
    
    //CAMPO 34
    $string_numero_campo_actual="34";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual<>'999' AND $nombre_campo_actual<>'0' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 34
    
    //CAMPO 12
    $string_numero_campo_actual="12";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual<>'9999' AND $nombre_campo_actual<>'9998' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='9999' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='9998' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 12
    
    //CAMPO 0
    $string_numero_campo_actual="0";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //cuando  campo 0 es igual a 2, con trae el de mayor numero de secuencia
    $query_condicion_campo0_criterio="";
    $query_condicion_campo0_criterio.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_condicion_campo0_criterio.=" ($nombre_campo_actual='2')";
    $query_condicion_campo0_criterio.=" ORDER BY numero_registro asc ";
    $error_bd_seq="";
    $resultados_condicion_campo0_criterio=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_condicion_campo0_criterio, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1: ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo0_criterio)>0 && is_array($resultados_condicion_campo0_criterio))
    {
        $arreglo_campos_reparados[0]=$resultados_condicion_campo0_criterio[count($resultados_condicion_campo0_criterio)-1]["$nombre_campo_actual"];

    }//fin if se cumplio el criterio
    else
    {
        $arreglo_campos_reparados[0]="2";
    }
    //FIN CAMPO 0
    
    //CAMPO 1
    $arreglo_campos_reparados[1]=$numero_registro_para_procesado;
    //FIN CAMPO 1
    
    //CAMPO 4    
    $arreglo_campos_reparados[4]=$numero_id_duplicado_actual;
    //FIN CAMPO 4
    
    
    
    
    //CAMPO 35
    $string_numero_campo_actual="35";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='1' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' ) ) ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 35
    
    //CAMPO 36
    $string_numero_campo_actual="36";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='1' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' ) ) ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 36
    
    //CAMPO 43
    $string_numero_campo_actual="43";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='1' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' ) ) ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 43
    
    //CAMPO 44
    $string_numero_campo_actual="44";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='1' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' ) ) ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 44
    
    //CAMPO 40
    $string_numero_campo_actual="40";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 40
    
    //CAMPO 45
    $string_numero_campo_actual="45";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 45
    
    //CAMPO 48
    $string_numero_campo_actual="48";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 48
    
    //CAMPO 37
    $string_numero_campo_actual="37";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3'  )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 37
    
    //CAMPO 41
    $string_numero_campo_actual="41";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3'  )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 41
    
    //CAMPO 42
    $string_numero_campo_actual="42";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3'  )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 42
    
    //CAMPO 46
    $string_numero_campo_actual="46";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3'  )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 46
    
    //CAMPO 86
    $string_numero_campo_actual="86";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select min($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3'  )  ) ) "; 
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 86
    
    //CAMPO 38
    $string_numero_campo_actual="38";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3' OR $nombre_campo_actual='4' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='5' )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 38
    
    //CAMPO 47
    $string_numero_campo_actual="47";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3' OR $nombre_campo_actual='4' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='5' )  ) ) ";  
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 47
    
    //CAMPO 54
    $string_numero_campo_actual="54";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' OR $nombre_campo_actual='2' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='3' OR $nombre_campo_actual='4' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='5' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='6' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='7' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='8' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='9' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='10' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='11' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='12' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='13' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='14' ";
    $query_criterio_campo_actual.=" OR $nombre_campo_actual='15' ";
    $query_criterio_campo_actual.=" )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 54
    
    //CAMPO 14
    $string_numero_campo_actual="14";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 14
    
    //CAMPO 16
    $string_numero_campo_actual="16";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 16
    
    //CAMPO 17
    $string_numero_campo_actual="17";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 17
    
    //CAMPO 19
    $string_numero_campo_actual="19";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 19
    
    //CAMPO 26
    $string_numero_campo_actual="26";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 26
    
    //CAMPO 15
    $string_numero_campo_actual="15";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='3' ) ";        
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 15
    
    //CAMPO 22
    $string_numero_campo_actual="22";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='3' ) ";        
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 22
    
    //CAMPO 59
    $string_numero_campo_actual="59";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::integer = (select max($nombre_campo_actual::integer) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 59
    
    //CAMPO 60
    $string_numero_campo_actual="60";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::integer = (select max($nombre_campo_actual::integer) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 60
    
    //CAMPO 61
    $string_numero_campo_actual="61";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::integer = (select max($nombre_campo_actual::integer) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 61
    
    //CAMPO 70
    $string_numero_campo_actual="70";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::integer = (select max($nombre_campo_actual::integer) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 70
    
    //CAMPO 71
    $string_numero_campo_actual="71";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::integer = (select max($nombre_campo_actual::integer) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 71
    
    
    //CAMPO 77
    $string_numero_campo_actual="77";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
                $query_criterio_campo_actual.=" where  ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";           
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 77
    
    //CAMPO 114
    $string_numero_campo_actual="114";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
                $query_criterio_campo_actual.=" where  ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";           
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 114
    
    //CAMPO 115
    $string_numero_campo_actual="115";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
                $query_criterio_campo_actual.=" where  ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";           
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 115
    
    //CAMPO 116
    $string_numero_campo_actual="116";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
                $query_criterio_campo_actual.=" where  ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";           
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 116
    
    //CAMPO 117
    $string_numero_campo_actual="117";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
                $query_criterio_campo_actual.=" where  ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' ";
                $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' )  ) ) ";           
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                //criterio 5
                                $query_criterio_campo_actual="";
                                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                                $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                        $error_bd_seq="";
                                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                                if($error_bd_seq!="")
                                {
                                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                                }
                                //si se cumple criterio
                                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                                {
                                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                    
                            
                                }//fin if se cumplio el criterio
                        }
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 117
    
    //CAMPO 18
    $string_numero_campo_actual="18";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 18
    
    //CAMPO 23
    $string_numero_campo_actual="23";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 23
    
    //CAMPO 24
    $string_numero_campo_actual="24";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 24
    
    //CAMPO 27
    $string_numero_campo_actual="27";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 27
    
    //CAMPO 28
    $string_numero_campo_actual="28";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 28
    
    //CAMPO 11
    $string_numero_campo_actual="11";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select min($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual<>'6' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='6' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 11
    
    
    //CAMPO 25
    $string_numero_campo_actual="25";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select min($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric>='1' AND $nombre_campo_actual::numeric<='6' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='7' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";        
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 25
    
    //CAMPO 20
    $string_numero_campo_actual="20";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='3' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4 
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                
                        }//fin else
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 20
    
    
    //CAMPO 21
    $string_numero_campo_actual="21";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual='1' ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='2' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='3' ) ";         
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4 
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='21' ) ";         
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                                
                        }//fin else
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 21
    
    //CAMPO 13
    $string_numero_campo_actual="13";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select min($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric>='1' AND $nombre_campo_actual::numeric<='12' )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='13' ) ";        
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 13
    
    //CAMPO 39
    $string_numero_campo_actual="39";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" $nombre_campo_actual='4' ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual='16' OR $nombre_campo_actual='17' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='18' OR $nombre_campo_actual='19' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='20' ) ) ) ";
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3 
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='22' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                        else
                        {
                            //criterio en caso de que no coincida con nada
                            $query_criterio_campo_actual="";
                            $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                            $query_criterio_campo_actual.=" (trim($nombre_campo_actual)<>'' ) ";
                            $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                    $error_bd_seq="";
                            $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                            if($error_bd_seq!="")
                            {
                                    $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. de no coincide con nada: ".$error_bd_seq."<br>";
                            }
                            //si se cumple criterio
                            if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                            {
                                $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                                
                        
                            }//fin if se cumplio el criterio de en caso de que no coincida con nada
                        }//fin else para ultimo criterio
                }
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 39
    
    //CAMPO 74
    $string_numero_campo_actual="74";
    $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$string_numero_campo_actual;
    //criterios
    //criterio 1  
    $query_criterio_campo_actual="";
    $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
    $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
    $query_criterio_campo_actual.=" where  ";
    $query_criterio_campo_actual.=" ( ";
    $query_criterio_campo_actual.=" $nombre_campo_actual<>'0' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'993' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'994' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'995' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'996' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'997' ";
    $query_criterio_campo_actual.=" AND $nombre_campo_actual<>'999' ";
    $query_criterio_campo_actual.=" )  ) ) ";
    $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";   
    $error_bd_seq="";
    $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
    if($error_bd_seq!="")
    {
            $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 1 : ".$error_bd_seq."<br>";
    }
    //si se cumple criterio
    if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
    {
        $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
        

    }//fin if se cumplio el criterio
    else
    {
        //criterio 2 
        $query_criterio_campo_actual="";
        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
        $query_criterio_campo_actual.=" ($nombre_campo_actual::numeric = (select max($nombre_campo_actual::numeric) from $nombre_vista_duplicados_del_duplicado_actual ";
        $query_criterio_campo_actual.=" where  ";
        $query_criterio_campo_actual.=" ( ";
        $query_criterio_campo_actual.=" $nombre_campo_actual='993' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='994' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='995' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='996' ";
        $query_criterio_campo_actual.=" OR $nombre_campo_actual='997' ";
        $query_criterio_campo_actual.=" )  ) ) ";       
        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                $error_bd_seq="";
        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
        if($error_bd_seq!="")
        {
                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 2: ".$error_bd_seq."<br>";
        }
        //si se cumple criterio
        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
        {
            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
            
    
        }//fin if se cumplio el criterio
        else
        {
                //criterio 3
                $query_criterio_campo_actual="";
                $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                $query_criterio_campo_actual.=" ($nombre_campo_actual='999' ) ";
                $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                        $error_bd_seq="";
                $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                if($error_bd_seq!="")
                {
                        $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                }
                //si se cumple criterio
                if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                {
                    $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                    
            
                }//fin if se cumplio el criterio
                else
                {
                        //criterio 4
                        $query_criterio_campo_actual="";
                        $query_criterio_campo_actual.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
                        $query_criterio_campo_actual.=" ($nombre_campo_actual='0' ) ";
                        $query_criterio_campo_actual.=" ORDER BY numero_registro asc ";  
                                $error_bd_seq="";
                        $resultados_condicion_campo_actual=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_criterio_campo_actual, $error_bd_seq);
                        if($error_bd_seq!="")
                        {
                                $mensajes_error_bd.=" ERROR al consultar la condicion $string_numero_campo_actual crit. 3: ".$error_bd_seq."<br>";
                        }
                        //si se cumple criterio
                        if(count($resultados_condicion_campo_actual)>0 && is_array($resultados_condicion_campo_actual))
                        {
                            $arreglo_campos_reparados[intval($string_numero_campo_actual)]=$resultados_condicion_campo_actual[count($resultados_condicion_campo_actual)-1]["$nombre_campo_actual"];
                            
                    
                        }//fin if se cumplio el criterio
                }//fin else
        }//fin else
    }//fin else
    //fin criterios    
    //FIN CAMPO 74
    
    //FIN CRITERIOS NUEVOS 14 09 2015
    
    //ARREGLO CAMPOS FALTANTES
    $cont_para_arreglo_faltantes=0;
    $hubo_campo_faltante=false;
    while($cont_para_arreglo_faltantes<count($arreglo_campos_reparados))
    {
        if((string)$arreglo_campos_reparados[intval($cont_para_arreglo_faltantes)]=="NO_VALOR_1024488856"
           //&& $numero_duplicados_de_duplicado>0
           )
        {
            $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$cont_para_arreglo_faltantes;
            $hubo_campo_faltante=true;
            
            //echo "<script>alert('ANTES el campo faltante es $nombre_campo_actual del afiliado $tipo_id_duplicado_actual $numero_id_duplicado_actual de valor $campo_arreglado ');</script>";
            
            //criterio 1  
            $query_obtener_campo_faltante="";
            $query_obtener_campo_faltante.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual WHERE ";
            $query_obtener_campo_faltante.=" $nombre_campo_actual<>'' ";
            $query_obtener_campo_faltante.=" ORDER BY numero_registro asc ";   
            $error_bd_seq="";
            $resultados_campo_faltante=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_obtener_campo_faltante, $error_bd_seq);
            if($error_bd_seq!="")
            {
                echo "<script>alert('error en consulta $nombre_campo_actual del $tipo_id_duplicado_actual $numero_id_duplicado_actual criterio 1');</script>";
                $mensajes_error_bd.=" ERROR al consultar la condicion $nombre_campo_actual crit. 1 para campo faltante : ".$error_bd_seq."<br>";
            }//fin if hubo error en bd
            //si se cumple criterio
            if(count($resultados_campo_faltante)>0 && is_array($resultados_campo_faltante))
            {
                $arreglo_campos_reparados[intval($cont_para_arreglo_faltantes)]=$resultados_campo_faltante[count($resultados_campo_faltante)-1]["$nombre_campo_actual"];
                      
            }//fin if se cumplio el criterio
            else
            {
                //criterio 2
                $query_obtener_campo_faltante="";
                $query_obtener_campo_faltante.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual  ";
                $query_obtener_campo_faltante.=" ORDER BY numero_registro asc ";   
                $error_bd_seq="";
                $resultados_campo_faltante=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_obtener_campo_faltante, $error_bd_seq);
                if($error_bd_seq!="")
                {
                    echo "<script>alert('error en consulta $nombre_campo_actual del $tipo_id_duplicado_actual $numero_id_duplicado_actual criterio 2');</script>";
                    $mensajes_error_bd.=" ERROR al consultar la condicion $nombre_campo_actual crit. 2 para campo faltante : ".$error_bd_seq."<br>";
                }//fin if hubo error en bd
                //si se cumple criterio
                if(count($resultados_campo_faltante)>0 && is_array($resultados_campo_faltante))
                {
                    $arreglo_campos_reparados[intval($cont_para_arreglo_faltantes)]=$resultados_campo_faltante[count($resultados_campo_faltante)-1]["$nombre_campo_actual"];
                          
                }//fin if se cumplio el criterio
            }//fin else no cumpli con el criterio 1
            
            //echo "<script>alert('DESPUES el campo faltante es $nombre_campo_actual del afiliado $tipo_id_duplicado_actual $numero_id_duplicado_actual de valor $campo_arreglado ');</script>";
            
        }//fin if es campo faltante
        $cont_para_arreglo_faltantes++;
    }//fin if arreglo campos faltantes
    
    if($hubo_campo_faltante)
    {
        //echo "<script>alert('entro a all 1ine $tipo_id_duplicado_actual $numero_id_duplicado_actual');</script>";
        
        $query_obtener_campo_faltante="";
        $query_obtener_campo_faltante.=" SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual  ";
        $query_obtener_campo_faltante.=" ORDER BY numero_registro asc ";   
        $error_bd_seq="";
        $resultados_campo_faltante=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_obtener_campo_faltante, $error_bd_seq);
        if($error_bd_seq!="")
        {
            echo "<script>alert('error en consulta  $tipo_id_duplicado_actual $numero_id_duplicado_actual all 1ine');</script>";
            $mensajes_error_bd.=" ERROR al consultar la condicion all line para campo faltante : ".$error_bd_seq."<br>";
        }//fin if hubo error en bd
        
        //echo "<script>alert(' numero dupl(info parametro) $numero_duplicados_de_duplicado  numero resultados ".count($resultados_campo_faltante)."  $tipo_id_duplicado_actual $numero_id_duplicado_actual all line ');</script>";
        
        //si se cumple criterio
        $iterador_cf=0;
        $string_mostrar_toda_la_linea="";
        if(count($resultados_campo_faltante)>0 && is_array($resultados_campo_faltante))
        {
            while($iterador_cf<119)
            {
                $nombre_campo_actual=$prefijo_nombre_campo_en_bd.$iterador_cf;
                
                $string_mostrar_toda_la_linea.="campo ".$iterador_cf;
                $cont_duplicados_del_afiliado_act=0;
                while($cont_duplicados_del_afiliado_act<count($resultados_campo_faltante))
                {
                    $string_mostrar_toda_la_linea.="|";
                    $string_mostrar_toda_la_linea.=$resultados_campo_faltante[$cont_duplicados_del_afiliado_act]["$nombre_campo_actual"];
                    $cont_duplicados_del_afiliado_act++;
                }
                $iterador_cf++;
            }//fin while
            
            //echo "<script>alert(' more info $tipo_id_duplicado_actual $numero_id_duplicado_actual $string_mostrar_toda_la_linea');</script>";
        }//fin if se cumplio el criterio
    }//muestra linea si hay campo(s) faltante(s)
    //FIN ARREGLO CAMPOS FALTANTES
    
    //ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    $campo_faltante=-1;
    $cont_campos_verificacion=0;
    $string_campos_faltantes="";
    foreach($arreglo_campos_reparados as $campo_arreglado)
    {
        if((string)$campo_arreglado=="NO_VALOR_1024488856")
        {
            $bool_fueron_procesados_duplicados_en_un_registro=false;
            $campo_faltante=$cont_campos_verificacion;
            $string_campos_faltantes.=" ".$campo_faltante;
        }
        $cont_campos_verificacion++;
    }
    if($campo_faltante!=-1
       //&& $numero_duplicados_de_duplicado>0
       )
    {
        echo "<script>alert('el campo faltante es $string_campos_faltantes del afiliado $tipo_id_duplicado_actual $numero_id_duplicado_actual ');</script>";
    }
    //fin ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    
    return $arreglo_campos_reparados;    
}
?>