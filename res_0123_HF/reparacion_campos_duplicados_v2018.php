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


function posicion_del_maximo_numero_en_array($matriz,$posicion_en_array_dentro_de_matriz)
{
       $posicion_matriz=0;
       $maximo_actual="";
       $maximo_actual=$matriz[0][$posicion_en_array_dentro_de_matriz];
       $posicion_maximo_actual=0;
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_es_mayor_que=floatval($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])>floatval($maximo_actual);
            if($comparacion_es_mayor_que==true)
            {
                  $maximo_actual=$matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz];
                  $posicion_maximo_actual=$contador_matriz;
            }//fin if
            $contador_matriz++;
       }
       $posicion_matriz=$posicion_maximo_actual;
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
            $comparacion_es_mayor_que=floatval($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])>floatval($maximo_actual);
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
            $comparacion_es_menor_que=floatval($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])<floatval($minimo_actual);
            if($comparacion_es_menor_que==true)
            {
                  $minimo_actual=$matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz];                        
            }//fin if
            $contador_matriz++;
       }
       $posicion_matriz=$minimo_actual;
       return $posicion_matriz;
}//fin


function calcula_mayor_valor_de_mayor_frecuencia($matriz,$posicion_en_array_dentro_de_matriz,$array_iguales_a_valores=array())
{
       //pasa a un array
       $array_tmp=array();
       $cont_to_array=0;
       while($cont_to_array<count($matriz))
       {
             $array_tmp[]=$matriz[$cont_to_array][$posicion_en_array_dentro_de_matriz];                         
             $cont_to_array++;                         
       }//fin while
       
       //busca los valores de $array_iguales_a_valores en el array
       $new_array_elementos_iguales=array();
       if(count($array_iguales_a_valores)!=0)
       {
         $cont_igv=0;
         while($cont_igv<count($array_iguales_a_valores))
         {
             $new_array_elementos_iguales[]=$array_tmp[$array_iguales_a_valores[$cont_igv]];
             $cont_igv++;
         }
         
       }//fin if
       else
       {
        $new_array_elementos_iguales=$array_tmp;
       }//fin else
       
       //parte donde calcula las frecuencias para cada valor
       $array_frecuencia=array();
       $contador_matriz=0;
       $valor_campo_mayor_frecuencia="";
       while($contador_matriz<count($new_array_elementos_iguales))
       {
            $valor_campo=$new_array_elementos_iguales[$contador_matriz];
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
       return $valor_campo_mayor_frecuencia;
       
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

function iguales_a_varios_valores($matriz,$posicion_en_array_dentro_de_matriz,$array_valores_a_comparar)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       $contador_varios_valores=0;
       while($contador_matriz<count($matriz))
       {
            $contador_varios_valores=0;
            while($contador_varios_valores<count($array_valores_a_comparar))
            {
                if(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz])==$array_valores_a_comparar[$contador_varios_valores])
                {
                       $posiciones_en_matriz[]=$contador_matriz;
                }
                $contador_varios_valores++;
            }//fin while interno
            $contador_matriz++;
            
       }
       return $posiciones_en_matriz;
}//fin


function entre_el_rango_de_valores_enteros($matriz,$posicion_en_array_dentro_de_matriz,$valor_inicial,$valor_final)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            if(intval(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz]))>=$valor_inicial
               && intval(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz]))<=$valor_final
               )
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
       $posicion_del_comparado=0;
       $posicion_del_comparado=$array_posiciones_array_interno_1[0];
       $contador_matriz=0;
       while($contador_matriz<count($matriz) && $contador_matriz<count($array_posiciones_array_interno_1))
       {
            //echo "el_valor_que_va_comparando: ".floatval($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz])." pos ".$array_posiciones_array_interno_1[$contador_matriz]."\n";
            $comparacion_es_mayor_que=floatval($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz])>floatval($maximo_actual);
            if($comparacion_es_mayor_que==true)
            {
                  $maximo_actual=$matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz];
                  $posicion_del_comparado=$array_posiciones_array_interno_1[$contador_matriz];
                  //echo "maximo_actual: ".$maximo_actual." pos_comparado: ".$posicion_del_comparado."\n";
            }//fin if
            $contador_matriz++;
       }//fin while
       $posicion_matriz=$posicion_del_comparado;//la posicion donde encontro el maximo
       return $posicion_matriz;
       
}//fin

function minimo_numero_orden($matriz,$posicion_en_array_dentro_de_matriz,$array_posiciones_array_interno_1)
{
       $posicion_matriz=0;
       $minimo_actual="";
       $minimo_actual=$matriz[$array_posiciones_array_interno_1[0]][$posicion_en_array_dentro_de_matriz];
       $posicion_del_comparado=0;
       $posicion_del_comparado=$array_posiciones_array_interno_1[0];
       $contador_matriz=0;
       while($contador_matriz<count($matriz) && $contador_matriz<count($array_posiciones_array_interno_1))
       {
            $comparacion_es_menor_que=floatval($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz])<floatval($minimo_actual);
            if($comparacion_es_menor_que==true)
            {
                  $minimo_actual=$matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz];
                  $posicion_del_comparado=$array_posiciones_array_interno_1[$contador_matriz];
            }//fin if
            $contador_matriz++;
       }//fin while
       $posicion_matriz=$posicion_del_comparado;//la posicion dondne encontro el minimo
       return $posicion_matriz;
}//fin


function maximo_fecha_orden($matriz,$posicion_en_array_dentro_de_matriz,$array_posiciones_array_interno_1)
{
      $posicion_matriz=0;
      $maximo_actual="";
      $maximo_actual=$matriz[$array_posiciones_array_interno_1[0]][$posicion_en_array_dentro_de_matriz];
      $posicion_del_comparado=0;
      $posicion_del_comparado=$array_posiciones_array_interno_1[0];
      $contador_matriz=0;
      while($contador_matriz<count($matriz) && $contador_matriz<count($array_posiciones_array_interno_1))
      {
           $comparacion_es_mayor_que=diferencia_dias_entre_fechas_para_dupl($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz],$maximo_actual);
           if($comparacion_es_mayor_que<0)
           {
                 $maximo_actual=$matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz];
                 $posicion_del_comparado=$array_posiciones_array_interno_1[$contador_matriz];
                 //echo "maximo_actual: ".$maximo_actual." pos_comparado: ".$posicion_del_comparado."\n";                    
           }//fin if
           $contador_matriz++;
      }
      $posicion_matriz=$posicion_del_comparado;//la posicion donde encontro el maximo
      return $posicion_matriz;
}//fin

function minimo_fecha_orden($matriz,$posicion_en_array_dentro_de_matriz,$array_posiciones_array_interno_1)
{
       $posicion_matriz=0;
       $minimo_actual="";
       $minimo_actual=$matriz[$array_posiciones_array_interno_1[0]][$posicion_en_array_dentro_de_matriz];
       $posicion_del_comparado=0;
       $posicion_del_comparado=$array_posiciones_array_interno_1[0];
       $contador_matriz=0;
       while($contador_matriz<count($matriz) && $contador_matriz<count($array_posiciones_array_interno_1))
       {
            $comparacion_es_menor_que=diferencia_dias_entre_fechas_para_dupl($matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz],$minimo_actual);
            if($comparacion_es_menor_que>0)
            {
                  $minimo_actual=$matriz[$array_posiciones_array_interno_1[$contador_matriz]][$posicion_en_array_dentro_de_matriz];
                  $posicion_del_comparado=$array_posiciones_array_interno_1[$contador_matriz];
            }//fin if
            $contador_matriz++;
       }
       $posicion_matriz=$posicion_del_comparado;//la posicion donde encontro el minimo
      return $posicion_matriz;
}//fin


function mayor_a_fecha_pos_array($matriz,$posicion_en_array_dentro_de_matriz,$fecha_a_comparar)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_mayor_que=diferencia_dias_entre_fechas_para_dupl(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz]),$fecha_a_comparar);
            if($comparacion_mayor_que<0)
            {
                   $posiciones_en_matriz[]=$contador_matriz;
            }
            $contador_matriz++;
       }
       return $posiciones_en_matriz;
}//fin

function menor_a_fecha_pos_array($matriz,$posicion_en_array_dentro_de_matriz,$fecha_a_comparar)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_menor_que=diferencia_dias_entre_fechas_para_dupl(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz]),$fecha_a_comparar);
            if($comparacion_menor_que>0)
            {
                   $posiciones_en_matriz[]=$contador_matriz;
            }
            $contador_matriz++;
       }
       return $posiciones_en_matriz;
}//fin

//para fechas iguales use la funcion iguales
function menor_igual_a_fecha_pos_array($matriz,$posicion_en_array_dentro_de_matriz,$fecha_a_comparar)
{
       $posiciones_en_matriz=array();
       $contador_matriz=0;
       while($contador_matriz<count($matriz))
       {
            $comparacion_igual_que=diferencia_dias_entre_fechas_para_dupl(trim($matriz[$contador_matriz][$posicion_en_array_dentro_de_matriz]),$fecha_a_comparar);
            if($comparacion_igual_que>=0)
            {
                   $posiciones_en_matriz[]=$contador_matriz;
            }
            $contador_matriz++;
       }
       return $posiciones_en_matriz;
}//fin


function calcula_mayor_fecha_de_mayor_frecuencia($matriz,$posicion_en_array_dentro_de_matriz,$array_iguales_a_valores=array())
{
       //pasa a un array
       $array_tmp=array();
       $cont_to_array=0;
       while($cont_to_array<count($matriz))
       {
             $array_tmp[]=$matriz[$cont_to_array][$posicion_en_array_dentro_de_matriz];                         
             $cont_to_array++;                         
       }//fin while
       
       //busca los valores de $array_iguales_a_valores en el array
       $new_array_elementos_iguales=array();
       if(count($array_iguales_a_valores)!=0)
       {
         $cont_igv=0;
         while($cont_igv<count($array_iguales_a_valores))
         {
             $new_array_elementos_iguales[]=$array_tmp[$array_iguales_a_valores[$cont_igv]];
             $cont_igv++;
         }
         
       }//fin if
       else
       {
        $new_array_elementos_iguales=$array_tmp;
       }//fin else
       
       //parte donde calcula las frecuencias para cada valor
       $array_frecuencia=array();
       $contador_matriz=0;
       $valor_campo_mayor_frecuencia="";
       while($contador_matriz<count($new_array_elementos_iguales))
       {
            $valor_campo=$new_array_elementos_iguales[$contador_matriz];
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
                        $comparacion_mayor_que=diferencia_dias_entre_fechas_para_dupl($valor_campo,$valor_campo_mayor_frecuencia);
                        if($comparacion_mayor_que<0)
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
       return $valor_campo_mayor_frecuencia;
       
}//fin

//hf
function reparacion_campos_duplicados($tipo_id_duplicado_actual,
                                      $numero_id_duplicado_actual,
                                      $fecha_actual,
                                      $tiempo_actual,
                                      $nick_user,
                                      $identificacion,
                                      $tipo_id,
                                      $numero_duplicados_de_duplicado,
                                      $nombre_vista_duplicados_del_duplicado_actual,
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

    $total_campos_norma=96;
    $cont_campos_reparados_pre_fix=0;
    while($cont_campos_reparados_pre_fix<$total_campos_norma)
    {
        $arreglo_campos_reparados[$cont_campos_reparados_pre_fix]="NO_VALOR_1024488856";
        $cont_campos_reparados_pre_fix++;
    }//fin while dejando listas posiciones


    //USAR LA VISTA PARA CREAR UNA MATRIZ ALMACENADA EN RAM Y AGILIZAR 
    $query_obtener_resultados="SELECT * FROM $nombre_vista_duplicados_del_duplicado_actual ; ";
    $res_duplicados_actuales=$conexionbd->consultar_no_warning_get_error_no_crea_cierra($query_obtener_resultados, $error_bd_seq);

    $matrix_registros_duplicados_afiliado_actual=array();
    $matrix_secuencia=array();
    $matrix_registro=array();

    $cont_registro=0;
    foreach ($res_duplicados_actuales as $key => $fila) 
    {
        $campos_fila_actual=array();
        $cont_campo_fa=0;
        while($cont_campo_fa<$total_campos_norma && isset($fila['campo_hf_de_numero_orden_'.$cont_campo_fa]))
        {
            $campos_fila_actual[$cont_campo_fa]=$fila['campo_hf_de_numero_orden_'.$cont_campo_fa];
            $cont_campo_fa++;
        }//fin while
        $campos_fila_actual[999]=$cont_registro;//consecutivo interno matriz
        $matrix_registros_duplicados_afiliado_actual[$cont_registro]=$campos_fila_actual;
        if($numero_secuencia_para_procesado!="-1")
        {
          $matrix_secuencia[$cont_registro]=$fila['numero_secuencia'];
        }//fin if
        $matrix_registro[$cont_registro]=$fila['numero_registro'];
        $cont_registro++;
    }//fin foreach

    if(isset($matrix_registro[0])==true && isset($matrix_secuencia[0])==true)
    {
        $numero_registro_para_procesado=$matrix_registro[0];
        $numero_secuencia_para_procesado=$matrix_secuencia[0];
    }//fin if

    //teniendo en cuenta el numero de campo propio de la norma y valores correspondiente a no aplica
    //para asi escoger lso valores con mas relevancia campo por campo meintras se consturyen los criterios definitivos
    //campo 1 hasta el 4 son nombres y apellidos
    //campo 5 y 6  son ti y numero id
    //7 fecha nacimiento
    //8 sexo
    //10 regimen N es no asegurado
    //11 cod eapb
    //12pertenencia etnica 6 es ninguna anteriores
    //13 grupo poblacional el 61 es no definido
    //14 codigo municipio residencia
    //15 telefono 0000000
    //16 fecha afiliacion 1995-01-01
    //17 estado gestacion 0 no, 3 no aplica
    //18 planificacion 3 no aplica 4 ninguno
    //10 edad usuario diagnostico years 9998 dato no disponible
     
    
    //tipo id y numero id
    $arreglo_campos_reparados[4]=$tipo_id_duplicado_actual;//campo 5
    $arreglo_campos_reparados[5]=$numero_id_duplicado_actual;//campo 6


    //CAMPOS NOMBRES
    //campo 1 hasta el 4 son nombres y apellidos aka 0 al 3
    $numero_orden_campo_actual=0;
    while($numero_orden_campo_actual<=3)
    {
        
        //CAMPO ACTUAL DIFERENTES DE VACIO, NOAP, NONE
        
        $matriz_comparaciones=array();
        $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
        $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NOAP");
        $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
                                          
        $array_diferentes_valor_no_aplica=array();
        $array_diferentes_valor_no_aplica=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
        
        if(count($array_diferentes_valor_no_aplica)>0 && is_array($array_diferentes_valor_no_aplica))
        {
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_valor_no_aplica);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        //CAMPO ACTUAL DIFERENTES DE VACIO
            
        
        $numero_orden_campo_actual++;
    }//fin foreach
    //fin CAMPOS NOMBRES

    //CAMPO 7 FECHA NACIMIENTO
    //campo 7 fecha de nacimeinto aka campo 6 desde cero
    $numero_orden_campo_actual=6;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        //criterio 1
        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 7 FECHA NACIMIENTO


    //CAMPO 8 SEXO
    //campo 8 sexo aka campo 7
    $numero_orden_campo_actual=7;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    //FIN CAMPO 8 SEXO

    //CAMPO 9 OCUPACION
    //campo 9 ocupacion aka campo 8
    $numero_orden_campo_actual=8;

    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9997");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9996");
                                      
    $array_diferentes_valor_no_aplica=array();
    $array_diferentes_valor_no_aplica=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_valor_no_aplica)>0 && is_array($array_diferentes_valor_no_aplica))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_valor_no_aplica);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {

        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9997");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9996");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_valor=array();
                $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
                if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                {            
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                    $posiciones_iguales_a_valor=array();
                    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                    {            
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                        //criterio para no dejar faltante
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                    }//fin else
                }//fin else
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 9 OCUPACION


    //CAMPO 10 REGIMEN
    //campo 10 regimen aka campo 9
    $numero_orden_campo_actual=9;

    $array_varios_valores=array(
            "C",
            "S",
            "P",
            "E",
            "O"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"N");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 10 REGIMEN

    //CAMPO 11 COD EAPB
    //campo 11 sexo aka campo 10
    $numero_orden_campo_actual=10;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 11 COD EAPB

    //CAMPO 12 pertenencia etnica
    //campo 12 regimen aka campo 11
    $numero_orden_campo_actual=11;

    $array_varios_valores=array(
            "1",
            "2",
            "3",
            "4",
            "5"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"6");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 12 pertenencia etnica

    //CAMPO 13 grupo poblacional
    //campo 13 regimen aka campo 12
    $numero_orden_campo_actual=12;

    $array_varios_valores=array(
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10",
            "11",
            "12",
            "13",
            "14",
            "15",
            "31",
            "32",
            "33",
            "34",
            "35",
            "36",
            "37",
            "38",
            "39",
            "50",
            "51",
            "52",
            "53",
            "54",
            "55",
            "56",
            "57",
            "58",
            "59",
            "60"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"61");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 13 grupo poblacional

    //CAMPO 14 COD Municipio
    //campo 14 COD Municipio aka campo 13
    $numero_orden_campo_actual=13;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"00000");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 14 COD Municipio

    //CAMPO 15 numero telefonico
    //campo 15 numero telefonico aka campo 14
    $numero_orden_campo_actual=14;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 14 COD Municipio

    //CAMPO 16 FECHA AFILIACION
    //campo 16 fecha de afiliacion aka campo 15 desde cero
    $numero_orden_campo_actual=15;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1995-01-01");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        //criterio 1
        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //CAMPO 16 FECHA AFILIACION


    //CAMPO 18 planificacion o consejeria genetica
    //campo 18 regimen aka campo 17
    $numero_orden_campo_actual=17;

    $array_varios_valores=array(
            "0",
            "1",
            "2"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 18 planificacion o consejeria genetica

    //CAMPO 19 edad anios
    //campo 19 edad anios aka campo 18
    $numero_orden_campo_actual=18;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 19 edad anios


    //CAMPO 20 prueba de diagnostico
    //campo 20 regimen aka campo 19
    $numero_orden_campo_actual=19;

    $array_varios_valores=array(
            "0",
            "1",
            "2"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //CAMPO 20 prueba de diagnostico

    //CAMPO 21 FECHA DE DIAGNOSTICA
    //campo 21 fecha de afiliacion aka campo 20 desde cero
    $numero_orden_campo_actual=20;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        //criterio 1
        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_fecha_array=array();
        $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_fecha_array=array();
            $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1811-01-01");
            if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 21 FECHA DE DIAGNOSTICA

    //CAMPO 22 codigo habilitacion
    //campo 22 aka campo 21
    $numero_orden_campo_actual=21;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 22 

    //CAMPO 23 tipo deficiencia diagnosticada
    //campo 23 regimen aka campo 22
    $numero_orden_campo_actual=22;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10",
            "11"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 23 tipo deficiencia diagnosticada


    //CAMPO 24 clasificacion severidad segun factor
    //campo 24 regimen aka campo 23
    $numero_orden_campo_actual=23;

    $array_varios_valores=array(
            "0",
            "1",
            "2"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 24


    //CAMPO 25 clasificacion severidad segun factor
    //campo 25 regimen aka campo 24
    $numero_orden_campo_actual=24;

    $array_varios_valores=array(
            "0",
            "1",
            "2"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 25


    //CAMPO 26 antecedentes familiares asociados a la hemofilia
    //campo 26 regimen aka campo 25
    $numero_orden_campo_actual=25;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10",
            "11",
            "12",
            "13",
            "14",
            "15",
            "16",
            "17",
            "18",
            "19",
            "21",
            "22",
            "23",
            "24"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"20");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 26

    //CAMPO 27 factor recibido
    //campo 27 aka campo 26
    $numero_orden_campo_actual=26;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"5");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_valor=array();
                $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                {            
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                    //criterio para no dejar faltante
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }//fin else
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 27

    //CAMPO 28 esquema
    //campo 28 campo 27
    $numero_orden_campo_actual=27;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"5");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"6");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                $posiciones_iguales_a_valor=array();
                $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                {            
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                    //criterio para no dejar faltante
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }//fin else
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 28


    //CAMPO 29 FECHA DE PRIMER TRATAMIENTO
    //campo 29 aka campo 28 desde cero
    $numero_orden_campo_actual=28;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        //criterio 1
        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_fecha_array=array();
        $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_fecha_array=array();
            $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
            if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 29

    //CAMPO 30 factor recibido
    //campo 30 campo 29
    $numero_orden_campo_actual=29;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"7");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 30

    //CAMPO 31 factor recibido
    //campo 31 campo 30
    $numero_orden_campo_actual=30;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 31

    //CAMPO 32 peso kg
    //campo 32  aka campo 31
    $numero_orden_campo_actual=31;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 32

    //CAMPO 32.1 dosis ui
    //campo 32.1 aka campo 32
    $numero_orden_campo_actual=32;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 32.1

    //CAMPO 32.2 frecuencia por semana
    //campo 32.2 campo 33
    $numero_orden_campo_actual=33;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 32.2

    //CAMPO 32.3 numero unidades totales en el periodo demanda
    //campo 32.3 aka campo 34
    $numero_orden_campo_actual=34;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999999");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999998");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999998");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"999999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 32.3

    //CAMPO 32.4 numero de aplicaciones del factor en el periodo demanda
    //campo 32.4 aka campo 35
    $numero_orden_campo_actual=35;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 32.4

    //CAMPO 33 modalidad de aplicacion del tratamiento
    //campo 33 campo 36
    $numero_orden_campo_actual=36;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 33

    //CAMPO 34 via de administracion
    //campo 34 campo 37
    $numero_orden_campo_actual=37;

    $array_varios_valores=array(
            "0",
            "1"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 34

    //CAMPO 35 codigo cum del factor pos recibido
    //campo 35 aka campo 38
    $numero_orden_campo_actual=38;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 35

    //CAMPO 36 codigo cum del factor pos no recibido
    //campo 36 aka campo 39
    $numero_orden_campo_actual=39;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 36

    //CAMPO 37 codigo cum de otros tratamientos utilizados 1
    //campo 37 aka campo 40
    $numero_orden_campo_actual=40;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 37

    //CAMPO 38 codigo cum de otros tratamientos utilizados 2
    //campo 38 aka campo 41
    $numero_orden_campo_actual=41;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 38

    //CAMPO 39 codigo de habilitacion ips
    //campo 39 aka campo 42
    $numero_orden_campo_actual=42;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 39

    //CAMPO 40 hemartrosis
    //campo 40 campo 43
    $numero_orden_campo_actual=43;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 40

    //CAMPO 40.1 numero hermatrosis espontaneas 
    //campo 40.1 aka campo 44
    $numero_orden_campo_actual=44;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 40.1

    //CAMPO 40.2 numero hermatrosis traumaticas
    //campo 40.2 aka campo 45
    $numero_orden_campo_actual=45;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 40.2

    //CAMPO 41 hemorragia ilio psoas
    //campo 41 campo 46
    $numero_orden_campo_actual=46;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 41

    //CAMPO 42 hemorragia de otros muscular tejidos blandos
    //campo 42 campo 47
    $numero_orden_campo_actual=47;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 42

    //CAMPO 43 hemorragia intercraneal
    //campo 43 campo 48
    $numero_orden_campo_actual=48;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 43

    //CAMPO 44 hemorragia cuello o garganta
    //campo 44 campo 49
    $numero_orden_campo_actual=49;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 44

    //CAMPO 45 hemorragia oral
    //campo 45 campo 50
    $numero_orden_campo_actual=50;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 45

    //CAMPO 46 otras
    //campo 46 campo 51
    $numero_orden_campo_actual=51;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 46

    //CAMPO 47.1 numero de otras hemorragias espontaneas 
    //campo 47.1 aka campo 52
    $numero_orden_campo_actual=52;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 47.1

    //CAMPO 47.2 numero de otras hemorragias traumaticas 
    //campo 47.2 aka campo 53
    $numero_orden_campo_actual=53;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 47.2

    //CAMPO 47.3 numero de otras hemorragias traumaticas 
    //campo 47.3 aka campo 54
    $numero_orden_campo_actual=54;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 47.3


    //CAMPO 48
    //campo 48 aka campo 55
    $numero_orden_campo_actual=55;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $posiciones_iguales_a_valor=array();
                $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"3");
                if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                {            
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                    $posiciones_iguales_a_valor=array();
                    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"4");
                    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                    {            
                        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                    }//fin if
                    else
                    {
                        $posiciones_iguales_a_valor=array();
                        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                        {            
                            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                        }//fin if
                        else
                        {
                            //criterio para no dejar faltante
                            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                        }//fin else
                    }//fin else
                }//fin else
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 48

    //CAMPO 48.1 FECHA determinacion  de titulos del inhibidor
    //campo 48.1 aka campo 56
    $numero_orden_campo_actual=56;
    
    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        //criterio 1
        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_fecha_array=array();
        $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_fecha_array=array();
            $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
            if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 48.1

    //CAMPO 48.2 ha recibido induccion a la tolerancia inmune
    //campo 48.2 aka campo 57
    $numero_orden_campo_actual=57;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 48.2

    //CAMPO 48.3 ha recibido induccion a la tolerancia inmune
    //campo 48.3 aka campo 58
    $numero_orden_campo_actual=58;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"2");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {                
                $posiciones_iguales_a_valor=array();
                $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
                {            
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
                }//fin if
                else
                {
                    //criterio para no dejar faltante
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
                }//fin else
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 48.3

    //CAMPO 48.4 tiempo del paciente que leva en iti
    //campo 48.4 aka campo 59
    $numero_orden_campo_actual=59;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9998");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 48.4


    //CAMPO 49 artopatia hemofilica cronica
    //campo 49 aka campo 60
    $numero_orden_campo_actual=60;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 49

    //CAMPO 49.1 articulaciones comprometidas
    //campo 49.1 aka campo 61
    $numero_orden_campo_actual=61;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 49.1

    //CAMPO 50 infectado por vhc
    //campo 50 aka campo 62
    $numero_orden_campo_actual=62;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 50

    //CAMPO 51 infectado por vhb
    //campo 51 aka campo 63
    $numero_orden_campo_actual=63;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 51

    //CAMPO 52 infectado por vih
    //campo 52 aka campo 64
    $numero_orden_campo_actual=64;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 52

    //CAMPO 53 pseudotumores
    //campo 53 aka campo 65
    $numero_orden_campo_actual=65;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 53

    //CAMPO 54 fracturas
    //campo 54 aka campo 66
    $numero_orden_campo_actual=66;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 54

    //CAMPO 55 anaflaxis
    //campo 55 aka campo 67
    $numero_orden_campo_actual=67;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 55

    //CAMPO 55.1 factor reaccion anaflitica
    //campo 55.1 aka campo 68
    $numero_orden_campo_actual=68;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 55.1

    //CAMPO 56 reemplazos articualres
    //campo 56 aka campo 69
    $numero_orden_campo_actual=69;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 56

    //CAMPO 56.1 factor reaccion anaflitica
    //campo 56.1 aka campo 70
    $numero_orden_campo_actual=70;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 56.1


    //CAMPO 57
    //campo 57 regimen aka campo 71
    $numero_orden_campo_actual=71;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57

    //CAMPO 57.1 
    //campo 57.1 aka campo 72
    $numero_orden_campo_actual=72;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 57.1

    //CAMPO 57.2 
    //campo 57.2 aka campo 73
    $numero_orden_campo_actual=73;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.2

    //CAMPO 57.3 
    //campo 57.3 aka campo 74
    $numero_orden_campo_actual=74;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 57.3


    //CAMPO 57.4 
    //campo 57.4 aka campo 75
    $numero_orden_campo_actual=75;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.4

    //CAMPO 57.5 
    //campo 57.5 aka campo 76
    $numero_orden_campo_actual=76;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.5

    //CAMPO 57.6 
    //campo 57.6 aka campo 77
    $numero_orden_campo_actual=77;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.6

    //CAMPO 57.7 
    //campo 57.7 aka campo 78
    $numero_orden_campo_actual=78;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.7

    //CAMPO 57.8 
    //campo 57.8 aka campo 79
    $numero_orden_campo_actual=79;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.8


    //CAMPO 57.9
    //campo 57.9 aka campo 80
    $numero_orden_campo_actual=80;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 57.9

    //CAMPO 57.10
    //campo 57.10 aka campo 81
    $numero_orden_campo_actual=81;

    $posiciones_iguales_a_valor=array();
    $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1");
    if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
    {            
        $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"0");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_valor=array();
            $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
            if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
            {            
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 57.10


    //CAMPO 57.11 
    //campo 57.11 aka campo 82
    $numero_orden_campo_actual=82;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 57.11

    //CAMPO 57.12 
    //campo 57.12 aka campo 83
    $numero_orden_campo_actual=83;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NONE");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.12

    //CAMPO 57.13 
    //campo 57.13 aka campo 84
    $numero_orden_campo_actual=84;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 57.13

    //CAMPO 57.14 
    //campo 57.14 aka campo 85
    $numero_orden_campo_actual=85;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NOAP");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"NOAP");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 57.14


    //CAMPO 58 
    //campo 58 aka campo 86
    $numero_orden_campo_actual=86;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 58

    //CAMPO 59 
    //campo 59 aka campo 87
    $numero_orden_campo_actual=87;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 59

    //CAMPO 60 
    //campo 60 aka campo 88
    $numero_orden_campo_actual=88;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 60

    //CAMPO 61 
    //campo 61 aka campo 89
    $numero_orden_campo_actual=89;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 61

    //CAMPO 62 
    //campo 62 aka campo 90
    $numero_orden_campo_actual=90;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 62

    //CAMPO 63
    //campo 63 aka campo 91
    $numero_orden_campo_actual=91;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_valor=array();
        $posiciones_iguales_a_valor=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"9999");
        if(count($posiciones_iguales_a_valor)>0 && is_array($posiciones_iguales_a_valor))
        {            
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_valor);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            //criterio para no dejar faltante
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
        }//fin else
    }//fin else
    //FIN CAMPO 63

    //CAMPO 64
    //campo 64  aka campo 92
    $numero_orden_campo_actual=92;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 64

    //CAMPO 64.1
    //campo 64.1  aka campo 93
    $numero_orden_campo_actual=93;

    $array_varios_valores=array(
            "0",
            "1",
            "2",
            "3",
            "4",
            "5",
            "6",
            "7",
            "8",
            "9",
            "10"
            );

    $array_iguales_a_varios_valores=array();
    $array_iguales_a_varios_valores=iguales_a_varios_valores($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_varios_valores);
    
    if(count($array_iguales_a_varios_valores)>0 && is_array($array_iguales_a_varios_valores))
    {
        //criterio 6
        $mayor_valor_mayor_frecuencia="";
        $mayor_valor_mayor_frecuencia=calcula_mayor_valor_de_mayor_frecuencia($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$array_iguales_a_varios_valores);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$mayor_valor_mayor_frecuencia;
        
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 64.1

    //CAMPO 64.2
    //campo 64.2  aka campo 94
    $numero_orden_campo_actual=94;

    $matriz_comparaciones=array();
    $matriz_comparaciones[]=mayor_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1900-12-31");
    $matriz_comparaciones[]=menor_igual_a_fecha_pos_array($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,trim($fecha_de_corte));
    
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=array();
    $posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte)>0 && is_array($posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte))
    {
        //criterio 1
        $orden_mayor_fecha_campo_actual=maximo_fecha_orden($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,$posiciones_array_es_fecha_calendario_y_menor_igual_fecha_corte);
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_mayor_fecha_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        $posiciones_iguales_a_fecha_array=array();
        $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1800-01-01");
        if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
        {
            //criterio 2
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
        }//fin if
        else
        {
            $posiciones_iguales_a_fecha_array=array();
            $posiciones_iguales_a_fecha_array=iguales_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"1845-01-01");
            if(count($posiciones_iguales_a_fecha_array)>0 && is_array($posiciones_iguales_a_fecha_array))
            {
                //criterio 2
                $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$posiciones_iguales_a_fecha_array);
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
        }//fin else
    }//fin else
    //FIN CAMPO 64.2

    //CAMPO 65
    //campo 65 aka campo 95
    $numero_orden_campo_actual=95;
    
    $matriz_comparaciones=array();
    
    $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                      
    $array_diferentes_a_NONE_vacio=array();
    $array_diferentes_a_NONE_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
    
    if(count($array_diferentes_a_NONE_vacio)>0 && is_array($array_diferentes_a_NONE_vacio))
    {
            $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_a_NONE_vacio);
            $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
    }//fin if
    else
    {
        //criterio para no dejar faltante
        $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
    }//fin else
    //FIN CAMPO 65

    //---------------------------------------------------------------------------------------------------------------------------------------------
    //FIX SI AUN QUEDAN CAMPOS VACIOS PARA EL ARREGLO FINAL
    $numero_orden_campo_actual=0;
    while($numero_orden_campo_actual<count($arreglo_campos_reparados) )
    {
        if((string)$arreglo_campos_reparados[$numero_orden_campo_actual]=="NO_VALOR_1024488856")
        {
            //CAMPO ACTUAL DIFERENTES DE VACIO
            
            $matriz_comparaciones=array();
            $matriz_comparaciones[]=diferentes_a($matrix_registros_duplicados_afiliado_actual,$numero_orden_campo_actual,"");
                                              
            $array_diferentes_vacio=array();
            $array_diferentes_vacio=interseccion_arrays_de_matriz_posiciones($matriz_comparaciones);
            
            if(count($array_diferentes_vacio)>0 && is_array($array_diferentes_vacio))
            {
                    $orden_ultimo_registro_campo_actual=maximo_numero_orden($matrix_registros_duplicados_afiliado_actual,999,$array_diferentes_vacio);
                    $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[$orden_ultimo_registro_campo_actual][$numero_orden_campo_actual];
            }//fin if
            else
            {
                //criterio para no dejar faltante
                $arreglo_campos_reparados[$numero_orden_campo_actual]=$matrix_registros_duplicados_afiliado_actual[0][$numero_orden_campo_actual];
            }//fin else
            //CAMPO ACTUAL DIFERENTES DE VACIO
            
        }
        $numero_orden_campo_actual++;
    }//fin foreach
    //FIX SI AUN QUEDAN CAMPOS VACIOS PARA EL ARREGLO FINAL
    
    
    //ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    $campo_hf0123_con_numero_orden_faltante=-1;
    $cont_campos_verificacion=0;
    $string_campos_faltantes="";
    foreach($arreglo_campos_reparados as $campo_hf_de_numero_orden_arreglado)
    {
        if((string)$campo_hf_de_numero_orden_arreglado=="NO_VALOR_1024488856")
        {
            $bool_fueron_procesados_duplicados_en_un_registro=false;
            $campo_hf0123_con_numero_orden_faltante=$cont_campos_verificacion;
            $string_campos_faltantes.=" ".$campo_hf0123_con_numero_orden_faltante;
        }
        $cont_campos_verificacion++;
    }
    if($campo_hf0123_con_numero_orden_faltante!=-1)
    {
        echo "<script>alert('el campo faltante es $string_campos_faltantes ');</script>";
    }
    //fin ciclo para verificar que los datos fueron insertados de forma correcta en el arreglo para insertar el nuevo registro resultante de los duplicados
    
    return $arreglo_campos_reparados;    
}
?>
