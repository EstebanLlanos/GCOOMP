<?php
set_time_limit(936000);
ini_set('max_execution_time', 936000);
ini_set('memory_limit', '900M');

include_once ('../utiles/clase_coneccion_bd.php');
include_once ('../utiles/funciones_crear_menu.php');


function diferencia_dias_entre_fechas_para_dupl($fecha_1,$fecha_2)
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

function maximo_fecha($matriz,$posicion_en_array_dentro_de_matriz)
{
      $posicion_matriz=0;
      $maximo_actual="";
      $maximo_actual=$matriz[0][$posicion_en_array_dentro_de_matriz];
      $contador_matriz=0;
      while($contador_matriz<count($matriz))
      {
           $comparacion_es_mayor_que=diferencia_dias_entre_fechas_para_dupl($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz],$maximo_actual);
           if($comparacion_es_mayor_que<0)
           {
                 $maximo_actual=$matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz];                        
           }//fin if
           $contador_matriz++;
      }
      $posicion_matriz=$maximo_actual;
      return $posicion_matriz;
}//fin

function minimo_fecha($matriz,$posicion_en_array_dentro_de_matriz)
{
       $posicion_matriz=0;
       $minimo_actual="";
       $minimo_actual=$matriz[0][$posicion_en_array_dentro_de_matriz];
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_es_menor_que=diferencia_dias_entre_fechas_para_dupl($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz],$minimo_actual);
            if($comparacion_es_menor_que>0)
            {
                  $minimo_actual=$matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz];                        
            }//fin if
            $contador_matriz++;
       }
       $posicion_matriz=$minimo_actual;
       return $posicion_matriz;
}//fin

function maximo_numero($matriz,$posicion_en_array_dentro_de_matriz)
{
       $posicion_matriz=0;
       $maximo_actual="";
       $maximo_actual=$matriz[0][$posicion_en_array_dentro_de_matriz];
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_es_mayor_que=floatval($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])>float_val($maximo_actual);
            if($comparacion_es_mayor_que==true)
            {
                  $maximo_actual=$matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz];                        
            }//fin if
            $contador_matriz++;
       }
       $posicion_matriz=$maximo_actual;
       return $posicion_matriz; 
}//fin

function minimo_numero($matriz,$posicion_en_array_dentro_de_matriz)
{
       $posicion_matriz=0;
       $minimo_actual="";
       $minimo_actual=$matriz[0][$posicion_en_array_dentro_de_matriz];
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_es_menor_que=floatval($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])<float_val($minimo_actual);
            if($comparacion_es_menor_que==true)
            {
                  $minimo_actual=$matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz];                        
            }//fin if
            $contador_matriz++;
       }
       $posicion_matriz=$minimo_actual;
       return $posicion_matriz;
}//fin

function calcula_mayor_valor_de_mayor_frecuencia($matriz,$posicion_en_array_dentro_de_matriz,$array_iguales_a_valores)
{
       //pasa a un array
       $array_tmp=array();
       $cont_to_array=0;
       while($cont_to_array<count($matriz))
       {
             $array_tmp[]=$matriz[$cont_to_array][$posicion_en_array_dentro_de_matriz];                         
             $cont_to_array++;                         
       }//fin while
       
       //intersecta con las posiciones de valores iguales
       
       //parte donde calcula las frecuencias para cada valor
       $array_frecuencia=array();
       $contador_matriz=0;
       $valor_campo_mayor_frecuencia="";
       while($contador_matriz<count($matriz))
       {
            $valor_campo=$array_tmp[$contador_matriz];
            $comparacion_frecuencia=isset($array_frecuencia[$valor_campo]);
            if($comparacion_frecuencia==true)
            {
                  $array_frecuencia[$valor_campo]++;                    
            }//fin if
            else
            {
                  $array_frecuencia[$valor_campo]=1;
            }//fin else
            
            //asigna el valor de campo con mayor frecuencia
            if($valor_campo_mayor_frecuencia!="")
            {
                  if($array_frecuencia[$valor_campo]>$array_frecuencia[$valor_campo_mayor_frecuencia])
                  {
                          $valor_campo_mayor_frecuencia=$valor_campo;            
                  }//fin if
            }//fin if
            else
            {
                  $valor_campo_mayor_frecuencia=$valor_campo;
            }//fin else
            $contador_matriz++;
       }//fin while
       
       $cont_frecuencias_por_valor=0;
       $son_iguales_las_frecuencias=true;
       while($cont_frecuencias_por_valor<count($array_frecuencia))
       {
             if($array_frecuencia[$valor_campo]!=$array_frecuencia[$valor_campo_mayor_frecuencia])
             {
                    $son_iguales_las_frecuencias=false;           
             }//fin if
             else if($array_frecuencia[$valor_campo]==$array_frecuencia[$valor_campo_mayor_frecuencia])
             {
                        //compara con los que tuvieron la misma frecuencia que el campo
                        //con mayor frecuencia escogido previamente
                        // y selecciona el valor del campo con mayor valor si tubieron la
                        //misma frecuencia
                        if(floatval($valor_campo)>floatval($valor_campo_mayor_frecuencia))
                        {
                              $valor_campo_mayor_frecuencia=$valor_campo;
                        }
             }//fin else
             $cont_frecuencias_por_valor++;                     
       }//fin while
       
       //busca en que posiciones
       // de la matriz esta
       //el mayor valor de mayor frecuencia encontrado
       //se apoya en la funcion iguales_a la cual retorna un array con las posiciones
       return iguales_a($matriz,$posicion_en_array_dentro_de_matriz,$valor_campo_mayor_frecuencia);
       
}//fin

function iguales_a($matriz,$posicion_en_array_dentro_de_matriz,$valor_a_comparar)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            if(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])==$valor_a_comparar)
            {
                   $posiciones_en_matriz[]=$contador_matriz;
            }
            $contador_matriz++;
       }
       return $posiciones_en_matriz;
}//fin

function diferentes_a($matriz,$posicion_en_array_dentro_de_matriz,$valor_a_comparar)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            if(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])!=$valor_a_comparar)
            {
                   $posiciones_en_matriz[]=$contador_matriz;
            }
            $contador_matriz++;
       }
       return $posiciones_en_matriz;
}//fin

function comparacion_binaria_intersecta_posiciones($array_posiciones_array_interno_1,
                                                  $array_posiciones_array_interno_2
                                                  )
{
       $posiciones_en_matriz=array();
       $posiciones_en_matriz=array_values(array_intersect($array_posiciones_array_interno_1, $array_posiciones_array_interno_2));
       return $posiciones_en_matriz;
}//fin

function comparacion_binaria_une_posiciones($array_posiciones_array_interno_1,
                                                  $array_posiciones_array_interno_2
                                                  )
{
       $posiciones_en_matriz=array();
       $posiciones_en_matriz=array_values(array_unique(array_merge($array_posiciones_array_interno_1, $array_posiciones_array_interno_2)));
       
       return $posiciones_en_matriz;
}//fin

function interseccion_arrays_de_matriz_posiciones($matriz_de_arrays_a_unir)
{
       $posiciones_en_matriz=array();
       $cont_array_actual=0;
       $posiciones_en_matriz=$matriz_de_arrays_a_unir[0];//para que no borre la primera interseccion
       while($cont_array_actual<count($matriz_de_arrays_a_unir))
       {
              $posiciones_en_matriz=array_values(array_intersect($posiciones_en_matriz, $matriz_de_arrays_a_unir[$cont_array_actual]));                        
              $cont_array_actual++;
       }//fin while       
       return $posiciones_en_matriz;
}//fin

function union_arrays_de_matriz_posiciones($matriz_de_arrays_a_unir)
{
       $posiciones_en_matriz=array();
       $cont_array_actual=0;
       while($cont_array_actual<count($matriz_de_arrays_a_unir))
       {
              $posiciones_en_matriz=array_values(array_unique(array_merge($posiciones_en_matriz, $matriz_de_arrays_a_unir[$cont_array_actual])));                        
              $cont_array_actual++;
       }//fin while       
       return $posiciones_en_matriz;
}//fin


function maximo_numero_orden($matriz,$posicion_en_array_dentro_de_matriz,$array_posiciones_array_interno_1)
{
       $posicion_matriz=0;
       $maximo_actual="";
       $maximo_actual=$matriz[$array_posiciones_array_interno_1[0]][$posicion_en_array_dentro_de_matriz];
       $contador_matriz=0;
       while($contador_matriz<count($matriz) && $contador_matriz<count($array_posiciones_array_interno_1))
       {
            $comparacion_es_mayor_que=floatval($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz])>float_val($maximo_actual);
            if($comparacion_es_mayor_que==true)
            {
                  $maximo_actual=$matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz];                        
            }//fin if
            $contador_matriz++;
       }//fin while
       $posicion_matriz=$maximo_actual;
       return $posicion_matriz;
       
}//fin

function minimo_numero_orden($matriz,$posicion_en_array_dentro_de_matriz,$array_posiciones_array_interno_1)
{
       $posicion_matriz=0;
       $minimo_actual="";
       $minimo_actual=$matriz[$array_posiciones_array_interno_1[0]][$posicion_en_array_dentro_de_matriz];
       $contador_matriz=0;
       while($contador_matriz<count($matriz) && $contador_matriz<count($array_posiciones_array_interno_1))
       {
            $comparacion_es_menor_que=floatval($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz])<float_val($minimo_actual);
            if($comparacion_es_menor_que==true)
            {
                  $minimo_actual=$matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz];                        
            }//fin if
            $contador_matriz++;
       }//fin while
       $posicion_matriz=$minimo_actual;
       return $posicion_matriz;
}//fin



//cancer
function reparacion_duplicados_por_txt($tipo_id_duplicado_actual,
                                       $numero_id_duplicado_actual,
                                       $codigo_diagnostico_actual,
                                      $fecha_actual,
                                      $tiempo_actual,
                                      $nick_user,
                                      $identificacion,
                                      $tipo_id,
                                      $numero_duplicados_de_duplicado,
                                      $ruta_temporal_duplicados_afiliado_actual,
                                      $ruta_temporal_nsecuencia_duplicados_afiliado_actual,
                                      &$numero_secuencia_para_procesado,
                                      &$numero_registro_para_procesado,
                                      &$cod_prestador_para_procesado,
                                      &$bool_fueron_procesados_duplicados_en_un_registro,
                                      &$contador_offset_duplicados,
                                      &$contador_duplicado_para_excluidos,
                                      &$mensajes_error_bd,
                                      &$conexionbd)
{
    //$conexionbd = new conexion();
    
    $fecha_tiempo_generacion_registro="";
    date_default_timezone_set ("America/Bogota");
    $fecha_actual_registro = date('Y-m-d');
    $tiempo_actual_registro = date('h:i:s');
    $fecha_tiempo_generacion_registro=$fecha_actual_registro."_".$tiempo_actual_registro."_".$contador_duplicado_para_excluidos;
    $contador_duplicado_para_excluidos++;
    
    $arreglo_campos_reparados=array();
    $cont_campos_reparados_pre_fix=0;
    while($cont_campos_reparados_pre_fix<210)
    {
        $arreglo_campos_reparados[$cont_campos_reparados_pre_fix]="NO_VALOR_1024488856";
        $cont_campos_reparados_pre_fix++;
    }//fin while dejando listas posiciones delos 210 campos desde 0 a 209
    
    
    $matrix_registros_duplicados_afiliado_actual=array();
    $matrix_secuencia_prestador=array();
    $cont_registro_dupl_actual=0;
    while($cont_registro_dupl_actual<$numero_duplicados_de_duplicado)
    {
                                      
                                      $linea_act = intval($cont_registro_dupl_actual) ;
                                      
                                      //lee el archivo de texto en la linea especifica
                                      $fileHandler = new SplFileObject($ruta_temporal_duplicados_afiliado_actual);
                                      $fileHandler->seek($linea_act);
                                      $linea_duplicada_del_afiliado=$fileHandler->current();
                                      $array_campos_del_duplicado_del_afiliado=array();//solo aqui se inicializa este array
                                      $array_campos_del_duplicado_del_afiliado=explode("|",$linea_duplicada_del_afiliado);
                                      //fin lee el archivo de texto en la linea especifica
                                      
                                      //Se adicionan los campos del registro a la matriz
                                      $matrix_registros_duplicados_afiliado_actual[]=$array_campos_del_duplicado_del_afiliado;
                                      
                                      //lee el archivo con el numero de secuencia del registro
                                      $fileHandler_2 = new SplFileObject($ruta_temporal_nsecuencia_duplicados_afiliado_actual);		
                                      $fileHandler_2->seek($linea_act);
                                      $linea_posee_secuencia_prestador_desde_txt=$fileHandler_2->current();
                                      $array_posee_secuencia_prestador_desde_txt=array();
                                      $array_posee_secuencia_prestador_desde_txt=explode("|",$linea_posee_secuencia_prestador_desde_txt);
                                      //$numero_secuencia_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[0]);
                                      //$prestador_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[1]);
                                      //$regimen_desde_txt=trim($array_posee_secuencia_prestador_desde_txt[2]);
                                      //fin lee el archivo con el numero de secuencia del registro
                                      
                                      //Se adicionan los campos del registro a la matriz
                                      $matrix_secuencia_prestador[]=$array_posee_secuencia_prestador_desde_txt;
                                      
                                      $cont_registro_dupl_actual++;
    }//fin while
    
    //ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    $campo_cancer0247_con_numero_orden_faltante=-1;
    $cont_campos_verificacion=0;
    $string_campos_faltantes="";
    foreach($arreglo_campos_reparados as $campo_cancer_de_numero_orden_arreglado)
    {
        if((string)$campo_cancer_de_numero_orden_arreglado=="NO_VALOR_1024488856")
        {
            $bool_fueron_procesados_duplicados_en_un_registro=false;
            $campo_cancer0247_con_numero_orden_faltante=$cont_campos_verificacion;
            $string_campos_faltantes.=" ".$campo_cancer0247_con_numero_orden_faltante;
        }
        $cont_campos_verificacion++;
    }
    if($campo_cancer0247_con_numero_orden_faltante!=-1)
    {
        echo "<script>alert('el campo faltante es $string_campos_faltantes ');</script>";
    }
    //fin ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    
    return $arreglo_campos_reparados;    
}
?>